<?php
include 'db.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$sql = "SELECT name, type, protein, calories, carbs, fat, ingredients, directions, videolink, thumbnail FROM meal";
$result = $conn->query($sql);

$meals = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $thumbnail = $row['thumbnail'] ? base64_encode($row['thumbnail']) : null; // if null pircture
        $meal = array(
            'name' => $row['name'],
            'type' => $row['type'],
            'protein' => $row['protein'],
            'calories' => $row['calories'],
            'carbs' => $row['carbs'],
            'fat' => $row['fat'],
            'ingredients' => $row['ingredients'],
            'directions' => $row['directions'],
            'videolink' => $row['videolink'],
            'thumbnail' => $thumbnail
        );
        $meals[] = $meal;
    }
    echo json_encode($meals);
} else {
    echo json_encode(['message' => 'No meals found']);
}

$conn->close();
?>
