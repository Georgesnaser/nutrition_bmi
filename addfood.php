<?php
include 'conx.php';
include 'nav.php';

$recipe_id = $_GET['id'] ?? '';
$title = $_GET['title'] ?? '';
$servings = $_GET['servings'] ?? '';

// Get existing meals from database
$existing_meals = [];
if ($conn) {
    $sql = "SELECT * FROM meal_planner ORDER BY date, meal_time";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $existing_meals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add or Replace Meal</title>
</head>
<body>
    <main class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Add or Replace Meal</h2>
            </div>
            <div class="card-body">
                <div class="mb-4 text-center">
                    <a href="search_meal.php" class="btn btn-info">
                        <i class="fas fa-search"></i> Search for Different Meals
                    </a>
                </div>
                <form action="save_to_planner.php" method="POST" id="meal-form">
                    <input type="hidden" name="recipe_id" value="<?php echo htmlspecialchars($recipe_id); ?>">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                    <input type="hidden" name="servings" value="<?php echo htmlspecialchars($servings); ?>">
                    
                    <div class="form-group">
                        <label>New Recipe: <?php echo htmlspecialchars($title); ?></label>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Select Date:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="meal_time">Meal Time:</label>
                        <select class="form-control" id="meal_time" name="meal_time" required>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Replace Meal</button>
                    <a href="meal_plan.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <script>
        const form = document.getElementById('meal-form');
        const dateInput = document.getElementById('date');
        const mealTimeInput = document.getElementById('meal_time');

        // Set today's date as default
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    </script>
</body>
</html>
