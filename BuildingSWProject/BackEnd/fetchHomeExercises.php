<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mealmotion";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name, text, video_link FROM exercises WHERE type = 'home'";
$result = $conn->query($sql);
$exercises = array();


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $exercise = array(
            'name' => $row['name'],
            'text' => $row['text'],
            'video_link' => $row['video_link']
        );
        $exercises[] = $exercise;
    }
    echo json_encode($exercises);
} else {
    echo json_encode(['message' => 'No exercises found']);
}

$conn->close();
?>
