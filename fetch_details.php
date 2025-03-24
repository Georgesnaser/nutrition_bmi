<?php
session_start(); // Start the session
include 'conx.php';

if (isset($_POST['date'])) {
    $date = $_POST['date'];
    $email = $_SESSION['email']; // Retrieve the email from the session

    $queryDetails = "SELECT f.*, i.iname, i.calories 
                     FROM favorite f 
                     INNER JOIN items i ON f.itemID = i.itemID 
                     WHERE f.userEmail = '$email' AND DATE(f.date) = '$date'";
    $detailsResult = $conn->query($queryDetails);

    if ($detailsResult->num_rows > 0) {
        while ($detailRow = $detailsResult->fetch_assoc()) {
            echo "<p>Plate: {$detailRow['iname']}, Quantity: {$detailRow['quantity']}, Total Calories: " . ($detailRow['calories'] * $detailRow['quantity']) . "</p>";
        }
    } else {
        echo "<p>No items found for this date.</p>";
    }
}
?>
