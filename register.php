<?php
include 'conx.php';
session_start(); 
//reg
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $gender_id = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    $check_email_sql = "SELECT * FROM users WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_sql);

    if ($check_email_result->num_rows > 0) {
        echo "<p style='color:red;'>The email address is already registered. Please use a different email.</p>";
    } else {
        $status_query = "SELECT sid FROM status WHERE sname='Pending'";
        $status_result = $conn->query($status_query);
        while ($status_row = $status_result->fetch_assoc()) {
            $status_id = $status_row['sid'];
        }

        $insert_user_sql = "INSERT INTO users (email, fname, lname, password, status, isadmin) 
                            VALUES ('$email', '$first_name', '$last_name', '$password', '$status_id', 0)";
        $conn->query($insert_user_sql);

        $gender_check_sql = "SELECT * FROM gender WHERE gid='$gender_id'";
        $gender_check_result = $conn->query($gender_check_sql);
        if ($gender_check_result->num_rows > 0) {
            $gender_row = $gender_check_result->fetch_assoc();
            $gender_id = $gender_row['gid'];
        } else {
            echo "Error: Gender ID not found.";
            exit;
        }

        $insert_bmi_sql = "INSERT INTO bmi (userEmail, birthDate, genderID, height, weight) 
                           VALUES ('$email', '$birthdate', '$gender_id', '$height', '$weight')";
        $conn->query($insert_bmi_sql);
        header("Location: login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="display-4">Nutrition Tracker - Register</h1>
        </div>
    </header>
    <main class="container mt-4">
        <form id="register-form" method="post" action="">
            <h2 class="mb-3">Personal Information</h2>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <h2 class="mb-3">BMI Information</h2>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control">
                    <?php
                    $gender_query = "SELECT * FROM gender";
                    $gender_result = $conn->query($gender_query);
                    while ($gender_row = $gender_result->fetch_assoc()) {
                        echo "<option value='{$gender_row['gid']}'>{$gender_row['gendervalue']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="birthdate">Birthdate:</label>
                <input type="date" id="birthdate" name="birthdate" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="height">Height (cm):</label>
                <input type="number" id="height" name="height" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </main>
</body>

</html>
