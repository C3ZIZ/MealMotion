<?php
include 'db.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$sql = "SELECT fact_text FROM health_fact ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fact = $row["fact_text"];
} else {
    $fact = "No facts were found.";
}
$conn->close();
echo json_encode(["fact" => $fact]);
?>
