<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Handle add new meal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_meal'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $carbs = $_POST['carbs'];
    $fat = $_POST['fat'];
    $videoLink = $_POST['videoLink'] ?? null; // Get video link (optional)
    $thumbnail = null;

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbnail = file_get_contents($_FILES['thumbnail']['tmp_name']);
    }

    $insert_query = "INSERT INTO meal (name, type, calories, protein, carbs, fat, thumbnail, videoLink) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssiiisss", $name, $type, $calories, $protein, $carbs, $fat, $thumbnail, $videoLink);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_meals.php");
    exit();
}

// Handle delete meal
if (isset($_GET['delete_meal_id'])) {
    $delete_meal_id = $_GET['delete_meal_id'];
    $delete_query = "DELETE FROM meal WHERE meal_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_meal_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_meals.php");
    exit();
}

// Fetch all meals
$meal_query = "SELECT meal_id, name, type, calories, protein, carbs, fat, thumbnail, videoLink FROM meal";
$result = $conn->query($meal_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meals - Admin Panel</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    <h1>Manage Meals</h1>
</header>
<nav class="main-nav">
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_exercises.php">Manage Exercises</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<section class="main-content" id="manage-page">
    <!-- Add New Meal -->
    <button id="addFormBtn" class="btn">+ Add New Meal</button>

    <div id="addForm">
        <form action="manage_meals.php" method="POST" enctype="multipart/form-data">
            <label for="name">Meal Name:</label>
            <input type="text" name="name" required>
            
            <label for="type">Meal Type:</label>
            <select name="type" required>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
            </select>

            <label for="calories">Calories:</label>
            <input type="number" name="calories" required min="0">

            <label for="protein">Protein:</label>
            <input type="number" name="protein" required min="0">

            <label for="carbs">Carbs:</label>
            <input type="number" name="carbs" required min="0">

            <label for="fat">Fat:</label>
            <input type="number" name="fat" required min="0">

            <label for="videoLink">Video Link:</label>
            <input type="text" name="videoLink" placeholder="Enter video link (optional)">

            <label for="thumbnail">Image:</label>
            <input type="file" name="thumbnail" accept="image/*">

            <button type="submit" name="add_meal" class="btn">Add Meal</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Calories</th>
                <th>Protein</th>
                <th>Carbs</th>
                <th>Fat</th>
                <th>Video Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php
                        if (!empty($row['thumbnail'])) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($row['thumbnail']) . "' alt='Meal Image' class='item-img'>";
                        } else {
                            echo "<p>No Image</p>";
                        }
                        ?>
                    </td>
                    <td><?php echo $row['meal_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['calories']); ?></td>
                    <td><?php echo htmlspecialchars($row['protein']); ?></td>
                    <td><?php echo htmlspecialchars($row['carbs']); ?></td>
                    <td><?php echo htmlspecialchars($row['fat']); ?></td>
                    <td>
                        <?php
                        echo htmlspecialchars($row['videoLink']) ? "<a href='" . htmlspecialchars($row['videoLink']) . "' target='_blank'>View Video</a>" : 'No Video Link';
                        ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_meal.php?id=<?php echo $row['meal_id']; ?>" class="btn">Edit</a>
                            <a href="manage_meals.php?delete_meal_id=<?php echo $row['meal_id']; ?>" class="btn" onclick="return confirm('Are you sure?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>

<footer class="footer">
    <p>&copy; 2025 MealMotion. All rights reserved.</p>
</footer>

<script src="addFormViewer.js"></script>
</body>
</html>

<?php
$conn->close();
?>
