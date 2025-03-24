<?php 
ob_start();
include 'nav.php';
include 'conx.php';

$email = $_SESSION['email'];

// Fetch user information
$sql1 = "SELECT fname, lname FROM users WHERE email = '$email'";
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
$sql3 = "SELECT gendervalue FROM gender WHERE gid = (SELECT genderID FROM bmi WHERE userEmail = '$email')";
$result3 = mysqli_query($conn, $sql3);
if (!$result3) {
    die('Error fetching gender data: ' . mysqli_error($conn));
}

// Fetch data from queries
$user_data = mysqli_fetch_assoc($result1);
$bmi_data = mysqli_fetch_assoc($result2);
$gender_data = mysqli_fetch_assoc($result3);

if (!$user_data || !$bmi_data || !$gender_data) {
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

// Age message
$age_message = ($age > 65) ? "BMI may not be the most accurate indicator for individuals over 65. Please consult with a healthcare provider." : "";

// Gender-specific message
if ($gender_data['gendervalue'] == "Female") {
    $bmi_category .= " (Note: Women's body fat distribution may affect health risk)";
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <h2 class="text-primary">Personal Information</h2>
        <div class="card p-3 mb-3">
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Gender:</strong> <?php echo $gender_data['gendervalue']; ?></p>
            <p><strong>Birthdate:</strong> <?php echo $bmi_data['birthDate']; ?></p>
            <p><strong>Age:</strong> <?php echo $age . ' years old!'; ?></p>
        </div>

        <h3 class="text-success">BMI Calculation</h3>
        <div class="card p-3">
            <p><strong>Height:</strong> <?php echo $bmi_data['height']; ?> cm</p>
            <p><strong>Weight:</strong> <?php echo $bmi_data['weight']; ?> kg</p>
            <p class="fw-bold">Your BMI is: <?php echo round($bmi, 2); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($bmi_category); ?></p>
            <?php if (!empty($age_message)): ?>
                <p class="text-warning"><strong>Note:</strong> <?php echo htmlspecialchars($age_message); ?></p>
            <?php endif; ?>
        </div>

        <!-- Edit Information Button -->
        <button class="btn btn-warning mt-3" onclick="openUpdatePopup(
            '<?= $bmi_data['bid'] ?>', 
            '<?= $bmi_data['height'] ?>', 
            '<?= $bmi_data['weight'] ?>', 
            '<?= $email ?>', 
            '<?= $user_data['fname'] ?>', 
            '<?= $user_data['lname'] ?>'
        )">Edit Information</button>
    </div>

    <!-- JavaScript for the popup form -->
    <script>
    function openUpdatePopup(bid, height, weight, email, fname, lname) {
        let formHtml = `
            <div id="editPopup" class="modal fade show" tabindex="-1" style="display: block; background: rgba(0, 0, 0, 0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Information</h5>
                            <button type="button" class="btn-close" onclick="closePopup()"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" action="" method="POST">
                                <input type="hidden" name="bid" value="${bid}">
                                <div class="mb-3">
                                    <label class="form-label">First Name:</label>
                                    <input type="text" name="fname" class="form-control" value="${fname}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Last Name:</label>
                                    <input type="text" name="lname" class="form-control" value="${lname}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Height (cm):</label>
                                    <input type="number" name="height" class="form-control" value="${height}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Weight (kg):</label>
                                    <input type="number" name="weight" class="form-control" value="${weight}">
                                </div>
                                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
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
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
        include 'conx.php';

        $bid = $_POST['bid'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $height = $_POST['height'];
        $weight = $_POST['weight'];

        // Update user information
        $update_user = "UPDATE users SET fname='$fname', lname='$lname' WHERE email='$email'";
        $update_bmi = "UPDATE bmi SET height='$height', weight='$weight' WHERE bid='$bid'";

        if (mysqli_query($conn, $update_user) && mysqli_query($conn, $update_bmi)) {
            //echo "<script>alert('Information updated successfully!');</script>";
            header('Location: home.php');
            
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
    ?>
</body>
</html>
