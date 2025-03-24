<?php
include "conx.php";
include "nav.php";

$userEmail = $_SESSION['email']; 

// Cleanup old data (older than 24 hours)
$cleanupSql = "DELETE FROM favorite WHERE date < CURDATE()";
$conn->query($cleanupSql);

// Fetch only today's favorite items
$favResult = $conn->query("SELECT itemID, quantity FROM favorite WHERE userEmail = '$userEmail' AND date = CURDATE()");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $itemID = intval($_POST['itemID']);

    // Check if the item exists in today's favorites
    $checkSql = "SELECT quantity FROM favorite WHERE userEmail = '$userEmail' AND itemID = '$itemID' AND date = CURDATE()";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        $newQuantity = $row['quantity'] + 1;
        
        // Update quantity for today's item
        $updateSql = "UPDATE favorite SET quantity = $newQuantity WHERE userEmail = '$userEmail' AND itemID = '$itemID' AND date = CURDATE()";
        $conn->query($updateSql);
    } else {
        // Insert new item for today
        $insertSql = "INSERT INTO favorite (userEmail, itemID, date, quantity) VALUES ('$userEmail', '$itemID', CURDATE(), 1)";
        $conn->query($insertSql);
    }
    header("Location: consumption.php");
    exit();
}

$totalCaloriesSum = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Favorite</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <main class="container mt-5">
        <h2 class="text-center mb-4">My Favorite Plates</h2>
        
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 text-center d-flex justify-content-center">
                <a href="favorite.php" class="btn btn-lg m-2" style="background-color: #17c1e8; color: black; border-radius: 30px; padding: 10px 20px;">
                    <i class="fas fa-heart me-2"></i> My Favorites
                </a>
                <a href="consumption.php" class="btn btn-lg m-2" style="background-color: #007bff; color: white; border-radius: 30px; padding: 10px 20px;">
                    <i class="fas fa-utensils me-2"></i> Consumption
                </a>
                <a href="history.php" class="btn btn-lg m-2" style="background-color: #dc3545; color: white; border-radius: 30px; padding: 10px 20px;">
                    <i class="fas fa-history me-2"></i> History
                </a>
            </div>
        </div>

        <table class="table table-hover table-bordered text-center">
            <thead class="thead-light">
                <tr>
                    <th>Plate</th>
                    <th>Calories</th>
                    <th>Quantity</th>
                    <th>Total Calories</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($favResult->num_rows > 0) {
                    while ($favRow = $favResult->fetch_assoc()) {
                        $itemID = $favRow['itemID'];
                        $quantity = $favRow['quantity'];
                        
                        $itemResult = $conn->query("SELECT iname, calories FROM items WHERE itemID = '$itemID'");
                        if ($itemResult->num_rows > 0) {
                            $itemRow = $itemResult->fetch_assoc();
                            $iname = $itemRow['iname'];
                            $calories = $itemRow['calories'];
                            $totalCalories = $calories * $quantity;
                            $totalCaloriesSum += $totalCalories;

                            echo "<tr>
                                    <td>$iname</td>
                                    <td>$calories</td>
                                    <td>$quantity</td>
                                    <td>$totalCalories</td>
                                  </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='4'>No favorite items found.</td></tr>";
                }
            ?>
            </tbody>
        </table>
        
        <div class="text-center my-4">
            <h3>Total Calories: <?php echo $totalCaloriesSum; ?></h3>
        </div>
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let input = this.value.toLowerCase();
    let cards = document.getElementsByClassName('card');
    
    Array.from(cards).forEach(card => {
        let text = card.textContent.toLowerCase();
        card.parentElement.style.display = text.includes(input) ? '' : 'none';
    });
});
</script>
</body>
</html>
