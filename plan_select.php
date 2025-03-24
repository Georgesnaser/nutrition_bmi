<?php
    ob_start();
    include 'conx.php';
    include 'nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .plan-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            cursor: pointer;
        }
        .plan-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5">Choose Your Nutrition Goal</h2>
        <div class="row justify-content-center">
            <div class="col-md-5 mb-4">
                <div class="card plan-card" onclick="window.location='plan.php'">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-weight fa-4x mb-3 text-danger"></i>
                        <h3 class="mb-3">Lose Weight</h3>
                        <p>Create a plan to help you achieve your weight loss goals</p>
                        <button class="btn btn-danger mt-3">Start Weight Loss Plan</button>
                    </div>
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="card plan-card" onclick="window.location='plan_gain.php'">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-dumbbell fa-4x mb-3 text-success"></i>
                        <h3 class="mb-3">Gain Weight</h3>
                        <p>Create a plan to help you build healthy weight</p>
                        <button class="btn btn-success mt-3">Start Weight Gain Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://kit.fontawesome.com/your-code.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
