<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Meal Plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .response-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .success-message {
            color: #28a745;
            padding: 10px;
            border-radius: 4px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            color: #dc3545;
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="response-container">
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
                            echo '<div class="error-message">Error: ' . $conn->error . '</div>';
                            exit;
                        }
                    }
                }
            }

            echo '<div class="success-message">Meal plan saved successfully!</div>';
        } else {
            echo '<div class="error-message">Invalid data received</div>';
        }
        ?>
    </div>
</body>
</html>
