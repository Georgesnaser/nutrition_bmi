<?php
session_start(); // Start the session
include 'nav.php';
include 'conx.php'; // Include database connection

$userEmail = $_SESSION['email']; // Get the user's email from the session

$sql = "SELECT id, name, thumb, instructions, source, calories, protein FROM extrameal WHERE email='$userEmail'";
$result = $conn->query($sql);

$totalCalories = 0;
$totalProtein = 0;

$day = isset($_GET['day']) ? $_GET['day'] : '';
$mealId = isset($_GET['mealId']) ? $_GET['mealId'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Meals</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #43C6AC 0%, #191654 100%);
            min-height: 100vh;
            position: relative;
        }
        body::before {
            display: none;
        }
        .meal-card {
            margin-bottom: 25px;
            transition: transform 0.3s ease;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .meal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        .card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            background: linear-gradient(45deg, #ffffff 0%, #fcfcfc 100%);
            height: 500px;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            filter: brightness(1.05);
        }
        .card-body {
            padding: 1.5rem;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
            display: flex;
            flex-direction: column;
        }
        .card-title {
            color: #4a154b;
            font-weight: 600;
        }
        .card-text {
            max-height: 150px;
            overflow-y: auto;
            scrollbar-width: thin;
            padding-right: 5px;
            margin-bottom: 10px;
        }
        .card-text::-webkit-scrollbar {
            width: 6px;
        }
        .card-text::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .card-text::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .card-text::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .nutrition-info {
            margin-top: 30px;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            text-align: center;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        .gradient-header {
            background: linear-gradient(135deg, #9796f0 0%, #fbc7d4 100%);
            padding: 15px;
            border-radius: 15px 15px 0 0;
        }
        .gradient-header h3 {
            color: white;
            margin: 0;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        .nutrition-content {
            padding: 25px;
        }
        .nutrition-content h4 {
            color: #4a154b;
            font-weight: 600;
            margin: 10px 0;
        }
        .btn-primary {
            background: linear-gradient(45deg, #ff6b6b 0%, #ff8e8e 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #ff8e8e 0%, #ff6b6b 100%);
            transform: translateY(-2px);
        }
        .btn-warning {
            background: linear-gradient(45deg, #f6d365 0%, #fda085 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-warning:hover {
            background: linear-gradient(45deg, #fda085 0%, #f6d365 100%);
            transform: translateY(-2px);
        }
        h2 {
            color: #4a154b;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .search-button {
            background: linear-gradient(45deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-left: 15px;
        }
        .search-button:hover {
            background: linear-gradient(45deg, #45a049 0%, #4CAF50 100%);
            transform: translateY(-2px);
        }
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <main class="container mt-5">
        <div class="header-container">
            <h2 class="text-center mb-0">Saved Meals</h2>
            <a href="search_meal.php" class="btn search-button">Search New Meals</a>
        </div>
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card meal-card">
                            <img src="<?= htmlspecialchars($row['thumb']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($row['instructions']) ?></p>
                                <p class="card-text"><strong>Calories:</strong> <?= htmlspecialchars($row['calories']) ?></p>
                                <p class="card-text"><strong>Protein:</strong> <?= htmlspecialchars($row['protein']) ?>g</p>
                                <div class="d-flex justify-content-between">
                                    <a href="<?= htmlspecialchars($row['source']) ?>" class="btn btn-primary" target="_blank">View Recipe</a>
                                    <button onclick="replaceMeal('<?= htmlspecialchars($row['name']) ?>', '<?= $day ?>', <?= $mealId ?>, <?= $row['id'] ?>)" class="btn btn-warning">Replace</button> <!-- to replace the meal  --> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $totalCalories += (int)$row['calories']; 
                    $totalProtein += (float)$row['protein'];
                    ?>
                <?php endwhile; ?>
            </div>
            <div class="nutrition-info">
                <div class="gradient-header">
                    <h3>Nutrition Summary</h3>
                </div>
                <div class="nutrition-content">
                    <h4>Total Calories: <?= $totalCalories ?></h4>
                    <h4>Total Protein: <?= $totalProtein ?>g</h4>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                No saved meals found.
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        &copy; 2025 Smart Coding Center. All rights reserved.
    </footer>
   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function replaceMeal(mealName, day, originalMealId, newFoodId) {
        if (confirm(`Do you want to replace the original meal with ${mealName}?`)) {
            // Navigate back to meal_plan.php with replacement parameters
            window.location.href = `meal_plan.php?replace=true&day=${day}&mealId=${originalMealId}&newFoodId=${newFoodId}`;
        }
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>