<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
        }

        header {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    <script>
        function togglePopup(id) {
            document.getElementById(id).style.display = 'block';
            document.querySelector('.popup-overlay').style.display = 'block';
        }

        function closePopup() {
            document.querySelectorAll('.popup').forEach(popup => popup.style.display = 'none');
            document.querySelector('.popup-overlay').style.display = 'none';
        }
    </script>
</head>
<body>
<header>Admin Panel</header>
<div class="container">
    <?php
        include 'nav.php';
    ?>
