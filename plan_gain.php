<?php
    ob_start();
    include 'conx.php';
    include 'nav.php';

    if (isset($_GET['closed']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $closed = $_GET['closed'];
    
        $statusID = ($closed == 1) ? 5 : 4;
        $sql1 = "UPDATE plan SET statusID = $statusID WHERE id = '$id'";
        
        if ($conn->query($sql1)) {
            header("Location: plan.php");
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $target = $_POST['targetWeight'];
        $date = $_POST['dueDate'];
        $start = $_POST['startdate'];
        $des = $_POST['description'];

        if (empty($target) || empty($date) || empty($des) || empty($start)) {
            echo "<div class='alert alert-danger'>All fields must be filled!</div>";
            exit();
        }

        $sql1 = "SELECT email FROM users WHERE email='$email'";
        $res1 = $conn->query($sql1);
        if ($res1->num_rows > 0) {
            $row1 = $res1->fetch_assoc();
            $email = $row1['email'];
        }

        $sql2 = "SELECT weight FROM bmi WHERE userEmail='$email'";
        $res2 = $conn->query($sql2);
        if ($row2 = $res2->fetch_assoc()) {
            $weight = $row2['weight'];
        }

        $sql = "INSERT INTO plangain (InitialWeight, targetWeight, startDate, dueDate, userEmail, statusID, description) 
                VALUES ('$weight', '$target', '$start', '$date', '$email', 4, '$des')";
        $conn->query($sql);

        $_SESSION['initialWeight'] = $weight;
        $_SESSION['targetWeight'] = $target;

        header("Location: plan.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .custom-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .custom-form label {
            font-weight: 600;
            color: #2c3e50;
        }
        .custom-form input, .custom-form textarea {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .progress-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #2c3e50; font-weight: 700;">Your Weight Gain Journey</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card custom-card p-4">
                <h3 class="text-center mb-4" style="color: #2c3e50;">Set Your Gain Goals</h3>
                <form action="" method="post" class="custom-form">
                    <div class="mb-3">
                        <label for="targetWeight" class="form-label">Target Weight to Gain (kg)</label>
                        <input type="number" id="targetWeight" name="targetWeight" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="startdate" class="form-label">Start Date</label>
                        <input type="date" id="startdate" name="startdate" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="dueDate" class="form-label">Target Date</label>
                        <input type="date" id="dueDate" name="dueDate" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Your Plan Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-custom text-white w-100">Create Your Plan</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="progress-card">
                <h3 class="text-center mb-4" style="color: #2c3e50;">Your Gain Progress</h3>
                <div class="text-center">
                    <div class="h4 mb-3">Weight to Gain:</div>
                    <div class="display-4 text-success mb-4">
                        <?php 
                        if (isset($_SESSION['initialWeight']) && isset($_SESSION['targetWeight'])) {
                            echo ($_SESSION['targetWeight'] + $_SESSION['initialWeight']) . " kg"; 
                        } else {
                            echo "Set a goal";
                        }
                        ?>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="export.php" class="btn btn-custom text-white">Download Progress Report</a>
                        <a href="bulkplan.php" class="btn btn-custom text-white">Get Meal Plan</a>
                    
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <h3 class="text-center mb-4" style="color: #2c3e50;">Your Weight Gain Plans</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Initial Weight</th>
                    <th>Target Weight</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM plan WHERE userEmail='$email'";
                $res = $conn->query($sql);
                $i = 0;
                while ($row = $res->fetch_assoc()) {
                    $i++;
                ?>
                    <tr>
                    <td><?= $i; ?></td>
                    <td><?= $row['InitialWeight']; ?></td>
                    <td><?= $row['targetWeight']; ?></td>
                    <td><?= $row['startDate']; ?></td>
                    <td><?= $row['dueDate']; ?></td>
                    <td><?= $row['description']; ?></td>
                    <td>
                        <?php
                        $statusId = $row['statusID'];
                        $sql3 = "SELECT sname FROM status WHERE sid='$statusId'";
                        $res3 = $conn->query($sql3);
                        if ($row3 = $res3->fetch_assoc()) {
                            echo $row3['sname'];
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row3['sname'] == "Opened") { ?>
                            <a href="?closed=1&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Close</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php 
                } 
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>