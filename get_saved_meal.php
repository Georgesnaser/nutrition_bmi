<?php
include 'conx.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT id, name, source FROM extrameal WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($meal = $result->fetch_assoc()) {
    echo json_encode($meal);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Meal not found']);
}

$stmt->close();
$conn->close();
