<?php
header('Content-Type: application/json');
include 'conx.php';

try {
    // Get POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['weekData'])) {
        throw new Exception('Invalid data received');
    }

    $weekData = $data['weekData'];
    $success = true;
    $savedMeals = 0;

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Process each day's meals
        foreach ($weekData as $day => $dayData) {
            if (isset($dayData['meals']) && is_array($dayData['meals'])) {
                foreach ($dayData['meals'] as $meal) {
                    $query = "INSERT INTO meals (recipe_id, title, ready_in_minutes, servings, source_url, day_of_week, calories, protein, fat, carbs) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = mysqli_prepare($conn, $query);
                    
                    $calories = $dayData['nutrients']['calories'] ?? 0;
                    $protein = $dayData['nutrients']['protein'] ?? 0;
                    $fat = $dayData['nutrients']['fat'] ?? 0;
                    $carbs = $dayData['nutrients']['carbohydrates'] ?? 0;

                    mysqli_stmt_bind_param($stmt, 'isiisspppd', 
                        $meal['id'],
                        $meal['title'],
                        $meal['readyInMinutes'],
                        $meal['servings'],
                        $meal['sourceUrl'],
                        $day,
                        $calories,
                        $protein,
                        $fat,
                        $carbs
                    );

                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception('Failed to save meal: ' . mysqli_error($conn));
                    }
                    $savedMeals++;
                }
            }
        }

        // If we got here, commit the transaction
        mysqli_commit($conn);
        echo json_encode([
            'success' => true, 
            'message' => "Successfully saved $savedMeals meals",
            'savedMeals' => $savedMeals
        ]);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
