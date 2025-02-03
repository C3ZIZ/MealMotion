<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Handle add new exercise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_exercise'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $text = $_POST['text'];
    $video_link = $_POST['video_link'];
    $thumnnail = null;

    if (isset($_FILES['thumnnail']) && $_FILES['thumnnail']['error'] === UPLOAD_ERR_OK) {
        $thumnnail = file_get_contents($_FILES['thumnnail']['tmp_name']);
    }

    $insert_query = "INSERT INTO exercises (name, type, text, video_link, thumnnail) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssss", $name, $type, $text, $video_link, $thumnnail);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_exercises.php");
    exit();
}

// Handle delete exercise
if (isset($_GET['delete_exercise_id'])) {
    $delete_exercise_id = $_GET['delete_exercise_id'];
    $delete_query = "DELETE FROM exercises WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_exercise_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_exercises.php");
    exit();
}

// Fetch all exercises
$exercise_query = "SELECT id, name, type, text, video_link, thumnnail FROM exercises";
$result = $conn->query($exercise_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exercises - Admin Panel</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    <h1>Manage Exercises</h1>
</header>
<nav class="main-nav">
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_Meals.php">Manage Meals</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<section class="main-content" id="manage-page">
    <button id="addFormBtn" class="btn">+ Add New Exercise</button>

    <div id="addForm">
        <form action="manage_exercises.php" method="POST" enctype="multipart/form-data">
            <label for="name">Exercise Name:</label>
            <input type="text" name="name" required>
            
            <label for="type">Exercise Type:</label>
            <select name="type" required>
                <option value="gym_workout	">Gym Workout</option>
                <option value="home">Home Workout</option>

            </select>
            
            <label for="text">Description:</label>
            <textarea name="text" required></textarea>
            
            <label for="video_link">Video Link:</label>
            <input type="url" name="video_link">
            
            <label for="thumnnail">thumnnail:</label>
            <input type="file" name="thumnnail" accept="image/*">
            
            <button type="submit" name="add_exercise" class="btn">Add Exercise</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>thumnnail</th>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Video Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php
                        if (!empty($row['thumnnail'])) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['thumnnail']) . "' alt='Exercise Image' class='item-img'>";
                        } else {
                            echo "<p>No Image</p>";
                        }
                        ?>
                    </td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['text']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($row['video_link']); ?>" target="_blank">Watch</a></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_exercise.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                            <a href="manage_exercises.php?delete_exercise_id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<footer class="footer">
    <p>&copy; 2025 FitTrack. All rights reserved.</p>
</footer>
<script src="addFormViewer.js"></script>
</body>
</html>

<?php
$conn->close();
?>
