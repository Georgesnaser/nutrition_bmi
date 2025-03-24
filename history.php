<?php
session_start(); // Start the session
ob_start();
include 'conx.php';
include 'nav.php';

$email = $_SESSION['email']; // Retrieve the email from the session

// Fetch past consumption data by date for the logged-in user
$queryHistory = "SELECT f.date, 
                    SUM((SELECT i.calories FROM items i WHERE i.itemID = f.itemID) * f.quantity) AS total_calories
                FROM favorite f 
                WHERE f.userEmail = '$email'
                GROUP BY f.date 
                ORDER BY f.date DESC";
$historyResult = $conn->query($queryHistory);

// Prepare data for the chart
$dates = [];
$totalCalories = [];

while ($row = $historyResult->fetch_assoc()) {
    $dates[] = $row['date'];
    $totalCalories[] = $row['total_calories'];
}

// Reset pointer for the table display
$historyResult->data_seek(0);
?>
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="text-center mb-4" style="color: #2c3e50;">Consumption History</h2>
            
            <!-- Navigation Buttons -->
            <div class="text-center mb-4">
                <a href="favorite.php" class="btn" style="background-color: #17C3E5; color: black; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                    <i class="fas fa-heart" style="color: black;"></i> My Favorites
                </a>
                <a href="consumption.php" class="btn" style="background-color: #1660FF; color: white; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                    <i class="fas fa-utensils"></i> Consumption
                </a>
                <a href="history.php" class="btn" style="background-color: #D62D42; color: white; border-radius: 25px; padding: 10px 20px; font-weight: bold;">
                    <i class="fas fa-history"></i> History
                </a>
            </div>

            <!-- Table Section -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #2C3E50; color: white;">
                                <tr>
                                    <th>Date</th>
                                    <th>Total Calories</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($historyResult->num_rows > 0) {
                                    while ($historyRow = $historyResult->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= $historyRow['date'] ?></td>
                                    <td><?= $historyRow['total_calories'] ?></td>
                                    <td>
                                        <button class="btn btn-sm" style="background-color: #2C3E50; color: white;" 
                                                onclick="fetchDetails('<?= $historyRow['date'] ?>', this)">
                                            <i class="fas fa-info-circle"></i> Details
                                        </button>
                                    </td>
                                </tr>
                                <tr class="details-row" style="display: none;">
                                    <td colspan="3" id="details-<?= $historyRow['date'] ?>" class="bg-light"></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>No history found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="card shadow mt-4">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Calorie Consumption Over Time</h4>
                </div>
                <div class="card-body">
                    <canvas id="caloriesChart" style="max-width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const dates = <?= json_encode($dates); ?>;
    const totalCalories = <?= json_encode($totalCalories); ?>;

    const ctx = document.getElementById('caloriesChart').getContext('2d');
    
    const barColors = [
        'rgba(75, 192, 192, 0.7)', 
        'rgba(255, 99, 132, 0.7)', 
        'rgba(54, 162, 235, 0.7)', 
        'rgba(153, 102, 255, 0.7)', 
        'rgba(255, 159, 64, 0.7)',
    ];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates.reverse(),
            datasets: [{
                label: 'Total Calories',
                data: totalCalories.reverse(),
                backgroundColor: barColors.slice(0, totalCalories.length),
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#333',
                        font: { size: 14 }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Calories',
                        color: '#333',
                        font: { size: 14 }
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    function fetchDetails(date, button) {
        $.ajax({
            url: 'fetch_details.php',
            type: 'POST',
            data: { date: date },
            success: function(response) {
                const detailsRow = $(button).closest('tr').next('.details-row');
                detailsRow.find('td').html(response);
                detailsRow.toggle();
            }
        });
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>