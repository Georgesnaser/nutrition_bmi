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
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --background-color: #ecf0f1;
            --text-color: #2c3e50;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }

        .navbar {
            background: var(--primary-color);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }

        main {
            margin: 3rem auto;
            max-width: 900px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            padding: 2.5rem;
        }

        .form-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .form-section {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        h2 {
            color: var(--primary-color);
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--secondary-color);
        }

        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 200px;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        /* Error message styling */
        .error-message {
            background: #fff3f3;
            color: var(--accent-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid var(--accent-color);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <span class="navbar-brand">Nutrition Tracker</span>
        </div>
    </nav>

    <main class="container">
        <form id="register-form" method="post" action="">
            <h2>Create Your Account</h2>
            <div class="form-section">
                <div>
                    <h3 class="section-title">Personal Information</h3>
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
                </div>
                <div>
                    <h3 class="section-title">BMI Information</h3>
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
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </main>
 
</body>

</html>
