<?php
session_start(); // Start the session
include 'nav.php';
include 'conx.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mealName']) && !isset($_POST['saveMeal'])) {
        $mealName = $_POST['mealName'];
        $apiUrl = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . urlencode($mealName);
        $response = @file_get_contents($apiUrl);
        $meals = json_decode($response, true)['meals'];

        // Fetch exact values for calories and protein from the new API
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
        // Save meal to database
        $mealName = $_POST['mealName'];
        $mealThumb = $_POST['mealThumb'];
        $mealInstructions = $_POST['mealInstructions'];
        $mealSource = $_POST['mealSource'];
        $mealCalories = $_POST['mealCalories'];
        $mealProtein = $_POST['mealProtein'];
        $userEmail = $_SESSION['email']; // Get the user's email from the session

        $sql = "INSERT INTO extrameal (name, thumb, instructions, source, calories, protein, email) VALUES ('$mealName', '$mealThumb', '$mealInstructions', '$mealSource', '$mealCalories', '$mealProtein', '$userEmail')";
        if ($conn->query($sql) === TRUE) {
            $savedMessage = "Meal saved successfully!";
        } else {
            $savedMessage = "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
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
    <style>
        body {
            background-color: #f0f2f5;
            background-image: linear-gradient(30deg, #f8f9fa 12%, transparent 12.5%, transparent 87%, #f8f9fa 87.5%, #f8f9fa),
            linear-gradient(150deg, #f8f9fa 12%, transparent 12.5%, transparent 87%, #f8f9fa 87.5%, #f8f9fa),
            linear-gradient(30deg, #f8f9fa 12%, transparent 12.5%, transparent 87%, #f8f9fa 87.5%, #f8f9fa),
            linear-gradient(150deg, #f8f9fa 12%, transparent 12.5%, transparent 87%, #f8f9fa 87.5%, #f8f9fa),
            linear-gradient(60deg, #e3e7ea 25%, transparent 25.5%, transparent 75%, #e3e7ea 75%, #e3e7ea),
            linear-gradient(60deg, #e3e7ea 25%, transparent 25.5%, transparent 75%, #e3e7ea 75%, #e3e7ea);
            background-size: 80px 140px;
            background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;
            font-family: 'Arial', sans-serif;
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 30px;
        }

        h2 {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meal-card {
            margin-bottom: 30px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .meal-card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .card-text {
            color: #666;
            font-size: 0.9rem;
            max-height: 100px;
            overflow-y: auto;
        }

        .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            margin-right: 10px;
        }

        .btn-success {
            background-color: #2ecc71;
            border: none;
            width: 100%;
            margin-top: 10px;
        }

        .form-control {
            border-radius: 25px;
            padding: 10px 20px;
            border: 2px solid #ddd;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #3498db;
        }

        footer {
            background-color: #2c3e50 !important;
            padding: 20px 0;
            margin-top: 50px;
        }

        .alert {
            border-radius: 15px;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <main class="container mt-5">
        <h2 class="text-center mb-4">Search for a Meal</h2>
        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="mealName">Meal Name:</label>
                <input type="text" id="mealName" name="mealName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (isset($meals) && $meals): ?>
            <div class="row">
                <?php foreach ($meals as $meal): ?>
                    <div class="col-md-4">
                        <div class="card meal-card">
                            <img src="<?= htmlspecialchars($meal['strMealThumb']) ?>" class="card-img-top" alt="<?= htmlspecialchars($meal['strMeal']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($meal['strMeal']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($meal['strInstructions']) ?></p>
                                <p class="card-text"><strong>Calories:</strong> <?= htmlspecialchars($meal['calories']) ?></p>
                                <p class="card-text"><strong>Protein:</strong> <?= htmlspecialchars($meal['protein']) ?>g</p>
                                <a href="<?= htmlspecialchars($meal['strSource']) ?>" class="btn btn-primary" target="_blank">View Recipe</a>
                                <form method="post" class="mt-2">
                                    <input type="hidden" name="mealName" value="<?= htmlspecialchars($meal['strMeal']) ?>">
                                    <input type="hidden" name="mealThumb" value="<?= htmlspecialchars($meal['strMealThumb']) ?>">
                                    <input type="hidden" name="mealInstructions" value="<?= htmlspecialchars($meal['strInstructions']) ?>">
                                    <input type="hidden" name="mealSource" value="<?= htmlspecialchars($meal['strSource']) ?>">
                                    <input type="hidden" name="mealCalories" value="<?= htmlspecialchars($meal['calories']) ?>">
                                    <input type="hidden" name="mealProtein" value="<?= htmlspecialchars($meal['protein']) ?>">
                                    <input type="hidden" name="saveMeal" value="1">
                                    <button type="submit" class="btn btn-success">Save Meal</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($meals)): ?>
            <div class="alert alert-danger" role="alert">
                No meals found.
            </div>
        <?php endif; ?>

        <?php if (isset($savedMessage)): ?>
            <div class="alert alert-success" role="alert">
                <?= $savedMessage ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        &copy; 2025 Smart Coding Center. All rights reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>