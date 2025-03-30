<?php
include 'conx.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Include all necessary fields
$sql = "SELECT id, name, thumb, instructions, source, calories, protein FROM extrameal WHERE id = $id";
$result = $conn->query($sql);

if ($meal = $result->fetch_assoc()) {
    // Format the response to match expected structure
    $formattedMeal = [
        'id' => $meal['id'],
        'name' => $meal['name'],
        'thumb' => $meal['thumb'],
        'instructions' => $meal['instructions'],
        'source' => $meal['source'],
        'calories' => (float)$meal['calories'],
        'protein' => (float)$meal['protein']
    ];
    echo json_encode($formattedMeal);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Meal not found']);
}

$conn->close();
