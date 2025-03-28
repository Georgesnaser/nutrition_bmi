<?php
include 'conx.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $recipe_id = $_POST['recipe_id'];
        $title = $_POST['title'];
        $servings = $_POST['servings'];
        $date = $_POST['date'];
        $meal_time = $_POST['meal_time'];

        // Always delete existing meal for this slot first
        $delete_sql = "DELETE FROM meal_planner WHERE date = ? AND meal_time = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ss", $date, $meal_time);
        $stmt->execute();

        // Insert new meal
        $sql = "INSERT INTO meal_planner (recipe_id, title, servings, date, meal_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiss", $recipe_id, $title, $servings, $date, $meal_time);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: meal_plan.php?success=1");
        } else {
            throw new Exception("Error inserting new meal");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: meal_plan.php?error=1");
    }
} else {
    header("Location: meal_plan.php");
}
exit();
