<?php
session_start();
include 'nav.php';
include 'conx.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mealName']) && !isset($_POST['saveMeal'])) {
        $mealName = $_POST['mealName'];
        $apiUrl = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . urlencode($mealName);
        $response = @file_get_contents($apiUrl);
        $meals = json_decode($response, true)['meals'];

        foreach ($meals as &$meal) {
            $nutritionApiUrl = "https://api.calorieninjas.com/v1/nutrition?query=" . urlencode($meal['strMeal']);
            $options = [
                "http" => [
                    "header" => "X-Api-Key: HQsWVt8kE+Ibo6aUQhU8Jw==J7Viq8kTvYrcuD6g"
                ]
            ];
            $context = stream_context_create($options);
            $nutritionResponse = @file_get_contents($nutritionApiUrl, false, $context);
            $nutritionData = json_decode($nutritionResponse, true);

            if (!empty($nutritionData['items'])) {
                $meal['calories'] = $nutritionData['items'][0]['calories'];
                $meal['protein'] = $nutritionData['items'][0]['protein_g'];
            } else {
                $meal['calories'] = 'N/A';
                $meal['protein'] = 'N/A';
            }
        }
    } elseif (isset($_POST['saveMeal'])) {
        $mealName = $_POST['mealName'];
        $date = $_POST['date'] ?? date('Y-m-d');
        $meal_time = $_POST['meal_time'] ?? 'lunch';
        $calories = $_POST['mealCalories'];
        $protein = $_POST['mealProtein'];

        $delete_sql = "DELETE FROM meal_planner WHERE date = ? AND meal_time = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ss", $date, $meal_time);
        $stmt->execute();

        $sql = "INSERT INTO meal_planner (title, servings, date, meal_time, calories, protein) 
                VALUES (?, 1, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdd", $mealName, $date, $meal_time, $calories, $protein);
        
        if ($stmt->execute()) {
            header("Location: meal_plan.php?success=1");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Meal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .meal-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .meal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <main class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-search mr-2"></i>Search for a Meal</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="mealName" placeholder="Enter meal name..." required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($meals) && $meals): ?>
            <div class="row">
                <?php foreach ($meals as $meal): ?>
                    <div class="col-md-4">
                        <div class="card meal-card">
                            <img src="<?= htmlspecialchars($meal['strMealThumb']) ?>" class="card-img-top" alt="<?= htmlspecialchars($meal['strMeal']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($meal['strMeal']) ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-fire-alt mr-2"></i>Calories: <?= htmlspecialchars($meal['calories']) ?><br>
                                        <i class="fas fa-dumbbell mr-2"></i>Protein: <?= htmlspecialchars($meal['protein']) ?>g
                                    </small>
                                </p>
                                <form method="post" action="save_to_planner.php">
                                    <input type="hidden" name="mealName" value="<?= htmlspecialchars($meal['strMeal']) ?>">
                                    <input type="hidden" name="mealCalories" value="<?= htmlspecialchars($meal['calories']) ?>">
                                    <input type="hidden" name="mealProtein" value="<?= htmlspecialchars($meal['protein']) ?>">
                                    
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <input type="date" name="date" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Meal Time:</label>
                                        <select name="meal_time" class="form-control" required>
                                            <option value="breakfast">Breakfast</option>
                                            <option value="lunch">Lunch</option>
                                            <option value="dinner">Dinner</option>
                                            <option value="snack">Snack</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" name="saveMeal" class="btn btn-success btn-block">
                                        <i class="fas fa-plus mr-2"></i>Add to Planner
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($meals)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>No meals found. Try another search term.
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Set default date to today
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.value = new Date().toISOString().split('T')[0];
        });
    </script>
</body>
</html>
