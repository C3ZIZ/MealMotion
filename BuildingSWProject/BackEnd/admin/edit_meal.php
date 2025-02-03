<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Fetch the meal details to edit
if (isset($_GET['id'])) {
    $meal_id = $_GET['id'];
    $fetch_query = "SELECT meal_id, name, type, calories, protein, carbs, fat, thumbnail, videoLink FROM meal WHERE meal_id = ?";
    $stmt = $conn->prepare($fetch_query);
    $stmt->bind_param("i", $meal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meal = $result->fetch_assoc();
    $stmt->close();

    if (!$meal) {
        echo "Meal not found.";
        exit();
    }
}

// Handle update meal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_meal'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];
    $fat = $_POST['fat'];
    $videoLink = $_POST['videoLink'];
    $thumbnail = $meal['thumbnail'];

    // Update thumbnail if a new image is uploaded
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail = file_get_contents($_FILES['thumbnail']['tmp_name']);
    }

    $update_query = "UPDATE meal SET name = ?, type = ?, calories = ?, protein = ?, carbs = ?, fat = ?, thumbnail = ?, videoLink = ? WHERE meal_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssiiisssi", $name, $type, $calories, $protein, $carbs, $fat, $thumbnail, $videoLink, $meal_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_meals.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal - Admin Panel</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="edit_form.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    <h1>Edit Meal</h1>
</header>
<nav class="main-nav">
    <ul>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="manage_meals.php">Manage Meals</a></li>
    </ul>
</nav>

<section class="main-content">
    <div class="edit-form">
    <h2>Edit <?php echo $meal['name'] ?></h2>
    <form action="edit_meal.php?id=<?php echo $meal['meal_id']; ?>" method="POST" enctype="multipart/form-data">
        <label for="name">Meal Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($meal['name']); ?>" required>

        <label for="type">Meal Type:</label>
        <select name="type" required>
            <option value="breakfast" <?php echo ($meal['type'] == 'breakfast') ? 'selected' : ''; ?>>Breakfast</option>
            <option value="lunch" <?php echo ($meal['type'] == 'lunch') ? 'selected' : ''; ?>>Lunch</option>
            <option value="dinner" <?php echo ($meal['type'] == 'dinner') ? 'selected' : ''; ?>>Dinner</option>
            <option value="snack" <?php echo ($meal['type'] == 'snack') ? 'selected' : ''; ?>>Snack</option>
        </select>

        <label for="calories">Calories:</label>
        <input type="number" name="calories" value="<?php echo htmlspecialchars($meal['calories']); ?>" required min="0">

        <label for="protein">Protein:</label>
        <input type="number" name="protein" value="<?php echo htmlspecialchars($meal['protein']); ?>" required min="0">

        <label for="carbs">Carbs:</label>
        <input type="number" name="carbs" value="<?php echo htmlspecialchars($meal['carbs']); ?>" required min="0">

        <label for="fat">Fat:</label>
        <input type="number" name="fat" value="<?php echo htmlspecialchars($meal['fat']); ?>" required min="0">

        <label for="thumbnail">Image:</label>
        <input type="file" name="thumbnail" accept="image/*">

        <label for="videoLink">Video Link:</label>
        <input type="url" name="videoLink" value="<?php echo htmlspecialchars($meal['videoLink']); ?>" required>

        <button type="submit" name="update_meal" class="btn">Update Meal</button>
    </form>
    </div>
</section>

<footer class="footer">
    <p>&copy; 2025 MealMotion. All rights reserved.</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>
