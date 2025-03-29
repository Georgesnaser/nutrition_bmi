<?php
include 'conx.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    foreach ($days as $day) {
        if (isset($data[$day]) && isset($data[$day]['meals'])) {
            foreach ($data[$day]['meals'] as $meal) {
                $title = $meal['title'];
                $readyInMinutes = $meal['readyInMinutes'];
                $servings = $meal['servings'];
                $sourceUrl = $meal['sourceUrl'];
                $imageUrl = "https://spoonacular.com/recipeImages/{$meal['id']}-636x393.jpg";

                $sql = "INSERT INTO meals (day, title, ready_in_minutes, servings, source_url, image_url) VALUES ('$day', '$title', $readyInMinutes, $servings, '$sourceUrl', '$imageUrl')";
                if ($conn->query($sql) === FALSE) {
                    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
                    exit;
                }
            }
        }
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
