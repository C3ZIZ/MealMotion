<?php
include '../db.php';
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - MealMotion</title>
  <link rel="stylesheet" href="admin_dashboard.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Header Section -->
  <header class="header">
    <div class="overlay">
      <h1>MealMotion Admin Panel</h1>
      <p>Manage meals and exercises for your users.</p>
      <p>Welcome back <?php echo $_SESSION['admin_name'].'!' ?></p>
    </div>
  </header>

  <nav class="main-nav">
    <ul>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <!-- Main Content Section -->
  <section class="main-content">
    <div class="main-card exercise-card">
      <h3>Exercise Management</h3>
      <p>Manage workout routines for users.</p>
      <a href="manage_exercises.php" class="btn">Manage Exercises</a>
    </div>
    <div class="main-card meals-card">
      <h3>Meal Management</h3>
      <p>Manage meals for users.</p>
      <a href="manage_meals.php" class="btn">Manage Meals</a>
    </div>
  </section>

  <!-- Footer Section -->
  <footer class="footer">
    <p>&copy; 2025 MealMotion. All rights reserved.</p>
  </footer>
</body>
</html>


<?php
$conn->close();
?>
