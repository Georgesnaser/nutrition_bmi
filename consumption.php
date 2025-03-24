<?php
include "conx.php";
include 'nav.php';

$sql = "SELECT DISTINCT i.*, c.cname 
        FROM items i, categories c 
        WHERE i.categoryID = c.cid";
$result = $conn->query($sql);
$userEmail = $_SESSION['email']; 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consumption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        .navbar-custom {
            background-color: #3498db;
            padding: 1rem 0;
        }
        .card {
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .action-buttons {
            background-color: #ecf0f1;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .btn-custom {
            margin: 0 0.5rem;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="action-buttons text-center">
        <a href="favorite.php" class="btn btn-info btn-custom">
            <i class="fas fa-heart"></i> My Favorites
        </a>
        <a href="consumption.php" class="btn btn-primary btn-custom">
            <i class="fas fa-utensils"></i> Consumption
        </a>
        <a href="history.php" class="btn btn-danger btn-custom">
            <i class="fas fa-history"></i> History
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <?php while ($consumption = $result->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($consumption['iname']) ?></h5>
                        <p class="card-text">
                            <span class="badge bg-primary"><?= htmlspecialchars($consumption['cname']) ?></span>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-fire"></i> <?= htmlspecialchars($consumption['calories']) ?> cal
                            </span>
                        </p>
                        <form method="post" action="favorite.php" class="mt-3">
                            <input type="hidden" name="itemID" value="<?= htmlspecialchars($consumption['itemID']) ?>">
                            <button type="submit" name="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Add to Favorite
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
