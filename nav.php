<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conx.php';
if(!isset($_SESSION['email'])){
    header('Location: login.php'); // Redirect to login page if not logged in
    exit(); // Stop the script
}

$email = $_SESSION['email'];
$fname = isset($_SESSION['fname']) ? $_SESSION['fname'] : '';
$lname = isset($_SESSION['lname']) ? $_SESSION['lname'] : '';
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';
$weight = isset($_SESSION['weight']) ? $_SESSION['weight'] : '';
$height = isset($_SESSION['height']) ? $_SESSION['height'] : '';
$birthdate = isset($_SESSION['birthdate']) ? $_SESSION['birthdate'] : '';

// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nutrition Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"><!-- for icon -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<header class="bg-success text-white text-center py-4">
    <h1>Nutrition Tracker</h1>
</header>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container d-flex">
        <!-- Welcome Message (Left) -->
        <a class = "navbar-brand" href="#"><?php echo "Welcome, ". $fname." ". $lname."!";?></a>

        <!-- Responsive Toggle Button -->
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links (Right) -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'home.php' ? 'active' : '' ?>" href="home.php"><i class="fas fa-home me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'plan_select.php' ? 'active' : '' ?>" href="plan_select.php"><i class="fas fa-calendar-alt me-1"></i>Plan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'consumption.php' ? 'active' : '' ?>" href="consumption.php"><i class="fas fa-utensils me-1"></i>Consumption</a>
                </li>
                
                
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="contact.php"><i class="fas fa-envelope me-1"></i>Contact us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>" href="about.php"><i class="fas fa-info-circle me-1"></i>About us</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'logout.php' ? 'active' : '' ?>" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- <script>
document.addEventListener('contextmenu', event => event.preventDefault());
document.addEventListener('selectstart', event => event.preventDefault());
</script> -->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
