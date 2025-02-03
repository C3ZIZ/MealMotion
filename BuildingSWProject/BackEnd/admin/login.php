<?php
include '../db.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $input_username);  // 's' stands for string parameter

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password (assuming the password is stored in plain text in the database, but it's recommended to use hashed passwords in real systems)
        if ($input_password === $row['password']) {
            // Set session variable for login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $input_username;
            $_SESSION['admin_name'] = $row['name'];
            header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
            exit();
        } else {
            $error_message = "Invalid username or password!";
        }
    } else {
        $error_message = "Invalid username or password!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealMotion Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <div class="login-container">
        <h2>MealMotion Admin</h2>
        <?php
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>

</body>
<footer class="footer">
    <p>&copy; 2025 MealMotion. All rights reserved.</p>
  </footer>
</html>