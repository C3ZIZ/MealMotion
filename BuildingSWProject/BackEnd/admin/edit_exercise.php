<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Fetch the exercise details to edit
if (isset($_GET['id'])) {
    $exercise_id = $_GET['id'];
    $fetch_query = "SELECT id, type, name, text, video_link, thumnnail FROM exercises WHERE id = ?";
    $stmt = $conn->prepare($fetch_query);
    $stmt->bind_param("i", $exercise_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exercise = $result->fetch_assoc();
    $stmt->close();

    if (!$exercise) {
        echo "Exercise not found.";
        exit();
    }
}

// Handle update exercise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_exercise'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $text = $_POST['text'];
    $video_link = $_POST['video_link'];
    $thumbnail = $exercise['thumbnail'];

    // Update thumbnail if a new image is uploaded
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail = file_get_contents($_FILES['thumbnail']['tmp_name']);
    }

    $update_query = "UPDATE exercise SET name = ?, type = ?, text = ?, video_link = ?, thumbnail = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $name, $type, $text, $video_link, $thumbnail, $exercise_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_exercises.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exercise - Admin Panel</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="edit_form.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    <h1>Edit Exercise</h1>
</header>
<nav class="main-nav">
    <ul>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="manage_exercises.php">Manage Exercises</a></li>
    </ul>
</nav>

<section class="main-content">
    <div class="edit-form">
        <h2>Edit <?php echo htmlspecialchars($exercise['name']); ?></h2>
        <form action="edit_exercise.php?id=<?php echo $exercise['id']; ?>" method="POST" enctype="multipart/form-data">
            <label for="name">Exercise Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($exercise['name']); ?>" required>

            <label for="type">Exercise Type:</label>
            <select name="type" required>
                <option value="gym_workout" <?php echo ($exercise['type'] == 'gym_workout') ? 'selected' : ''; ?>>Gym Workout</option>
                <option value="gym_muscle" <?php echo ($exercise['type'] == 'gym_muscle') ? 'selected' : ''; ?>>Gym Muscle</option>
                <option value="home" <?php echo ($exercise['type'] == 'home') ? 'selected' : ''; ?>>Home</option>
            </select>

            <label for="text">Description:</label>
            <textarea name="text" required><?php echo htmlspecialchars($exercise['text']); ?></textarea>

            <label for="video_link">Video Link:</label>
            <input type="url" name="video_link" value="<?php echo htmlspecialchars($exercise['video_link']); ?>" required>

            <label for="thumbnail">Thumbnail:</label>
            <input type="file" name="thumbnail" accept="image/*">

            <button type="submit" name="update_exercise" class="btn">Update Exercise</button>
        </form>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 FitnessApp. All rights reserved.</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>
