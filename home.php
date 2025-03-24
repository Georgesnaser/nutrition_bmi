<?php 
ob_start();
include 'nav.php';
include 'conx.php';

$email = $_SESSION['email'];

// Fetch user information (including password)
$sql1 = "SELECT fname, lname, password FROM users WHERE email = '$email'";
$result1 = mysqli_query($conn, $sql1);
if (!$result1) {
    die('Error fetching user data: ' . mysqli_error($conn));
}

// Fetch BMI information
$sql2 = "SELECT bid, birthDate, genderID, height, weight FROM bmi WHERE userEmail = '$email'";
$result2 = mysqli_query($conn, $sql2);
if (!$result2) {
    die('Error fetching BMI data: ' . mysqli_error($conn));
}

// Fetch gender information
$sql3 = "SELECT gid, gendervalue FROM gender";
$result3 = mysqli_query($conn, $sql3);
if (!$result3) {
    die('Error fetching gender data: ' . mysqli_error($conn));
}

// Store gender options
$genders = [];
while ($row = mysqli_fetch_assoc($result3)) {
    $genders[] = $row;
}

// Fetch user & BMI data
$user_data = mysqli_fetch_assoc($result1);
$bmi_data = mysqli_fetch_assoc($result2);

if (!$user_data || !$bmi_data) {
    echo "Error: User data not found.";
    exit();
}

// Calculate BMI
$bmi = $bmi_data['weight'] / (($bmi_data['height'] / 100) ** 2); 

// Determine BMI category
if ($bmi < 18.5) {
    $bmi_category = "Underweight 😞";
} elseif ($bmi >= 18.5 && $bmi < 24.9) {
    $bmi_category = "Normal weight 😊";
} elseif ($bmi >= 25 && $bmi < 29.9) {
    $bmi_category = "Overweight 😐";
} else {
    $bmi_category = "Obesity 😡";
}

// Calculate age
$birthdate = new DateTime($bmi_data['birthDate']);
$current_date = new DateTime();
$age = $current_date->diff($birthdate)->y;

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/double-bubble-outline.png');
            background-repeat: repeat;
            background-attachment: fixed;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 40px auto;
        }
        .info-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.2s;
            background-color: rgba(255, 255, 255, 0.95);
        }
        .info-card:hover {
            transform: translateY(-5px);
        }
        .bmi-category {
            font-size: 1.5rem;
            padding: 10px 20px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .edit-btn {
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
        }
        .modal-content {
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.98);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="row g-4">
            <!-- Personal Information Card -->
            <div class="col-md-6">
                <div class="card info-card p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle fs-2 text-primary me-2"></i>
                        <h2 class="mb-0">Personal Info</h2>
                    </div>
                    <div class="ps-4">
                        <p><i class="fas fa-envelope me-2"></i> <?php echo $email; ?></p>
                        <p><i class="fas fa-calendar me-2"></i> <?php echo $bmi_data['birthDate']; ?></p>
                        <p><i class="fas fa-birthday-cake me-2"></i> <?php echo $age . ' years old'; ?></p>
                    </div>
                </div>
            </div>

            <!-- BMI Card -->
            <div class="col-md-6">
                <div class="card info-card p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-weight fs-2 text-success me-2"></i>
                        <h2 class="mb-0">BMI Details</h2>
                    </div>
                    <div class="ps-4">
                        <p><i class="fas fa-ruler-vertical me-2"></i> Height: <?php echo $bmi_data['height']; ?> cm</p>
                        <p><i class="fas fa-weight me-2"></i> Weight: <?php echo $bmi_data['weight']; ?> kg</p>
                        <p><i class="fas fa-calculator me-2"></i> BMI: <strong><?php echo round($bmi, 2); ?></strong></p>
                        <div class="bmi-category bg-light text-center">
                            <?php 
                            $category_color = match($bmi_category) {
                                "Underweight 😞" => "text-warning",
                                "Normal weight 😊" => "text-success",
                                "Overweight 😐" => "text-warning",
                                "Obesity 😡" => "text-danger",
                                default => "text-dark"
                            };
                            ?>
                            <span class="<?php echo $category_color; ?> fw-bold">
                                <?php echo htmlspecialchars($bmi_category); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Button -->
        <div class="text-center mt-4">
            <button class="btn btn-warning edit-btn" onclick="openUpdatePopup(
                '<?= $bmi_data['bid'] ?>', 
                '<?= $bmi_data['height'] ?>', 
                '<?= $bmi_data['weight'] ?>', 
                '<?= $email ?>', 
                '<?= $user_data['fname'] ?>', 
                '<?= $user_data['lname'] ?>', 
                '<?= $bmi_data['birthDate'] ?>', 
                '<?= $user_data['password'] ?>'
            )">
                <i class="fas fa-edit me-2"></i>Edit Information
            </button>
        </div>
    </div>

    <script>
    function openUpdatePopup(bid, height, weight, email, fname, lname, birthdate, password) {
        let formHtml = `
            <div id="editPopup" class="modal fade show" tabindex="-1" style="display: block; background: rgba(0, 0, 0, 0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Information</h5>
                            <button type="button" class="btn-close" onclick="closePopup()"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" action="" method="POST">
                                <input type="hidden" name="bid" value="${bid}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-user me-2"></i>First Name:</label>
                                            <input type="text" name="fname" class="form-control" value="${fname}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-user me-2"></i>Last Name:</label>
                                            <input type="text" name="lname" class="form-control" value="${lname}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-calendar me-2"></i>Birthdate:</label>
                                            <input type="date" name="birthdate" class="form-control" value="${birthdate}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-venus-mars me-2"></i>Gender:</label>
                                            <select name="genderID" class="form-control">
                                                <?php foreach ($genders as $gender): ?>
                                                    <option value="<?= $gender['gid'] ?>"><?= $gender['gendervalue'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-lock me-2"></i>Password:</label>
                                            <input type="password" name="password" class="form-control" value="${password}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-lock me-2"></i>Confirm Password:</label>
                                            <input type="password" name="confirm_password" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-ruler-vertical me-2"></i>Height (cm):</label>
                                            <input type="number" name="height" class="form-control" value="${height}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-weight me-2"></i>Weight (kg):</label>
                                            <input type="number" name="weight" class="form-control" value="${weight}">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" name="update" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', formHtml);
    }

    function closePopup() {
        document.getElementById("editPopup").remove();
    }
    </script>

    <?php
    // Handle Form Submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        include 'conx.php';

        $bid = $_POST['bid'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $birthdate = $_POST['birthdate'];
        $genderID = $_POST['genderID'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $height = $_POST['height'];
        $weight = $_POST['weight'];

        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!'); window.location.href = 'home.php';</script>";
        } else {
            //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $update_user = "UPDATE users SET fname='$fname', lname='$lname', password='$password' WHERE email='$email'";
            $update_bmi = "UPDATE bmi SET birthDate='$birthdate', genderID='$genderID', height='$height', weight='$weight' WHERE bid='$bid'";

            if (mysqli_query($conn, $update_user) && mysqli_query($conn, $update_bmi)) {
                echo "<script>alert('Information updated successfully!'); window.location.href = 'home.php';</script>";
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        }

        mysqli_close($conn);
    }
    ?>


</body>
</html>
