<?php
include('conx.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if($email == 'admin@nutrition.app' && $password == '123'){
        $_SESSION['admin'] = 'Admin';
        header("location: admin/dashboard.php");
        exit();
    }

    // Fetch user details from database
    $sql = "SELECT email, password, isadmin, status FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $stored_hashed_password = $user['password'];

        // Verify the password using password_verify()
        if (password_verify($password, $stored_hashed_password)) {
            if ($user['status'] === '1') {
                $_SESSION['email'] = $user['email'];
                header("Location:home.php");
                exit();
            } else {
                $error_message = "Your account status is not accepted.";
            }
        } else {
            $error_message = "Invalid credentials. Please try again.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <!-- Removed reCAPTCHA script -->
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1490818387583-1baba5e638af?auto=format&fit=crop&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
        }
        .login-container {
            max-width: 450px;
            margin: 50px auto;
        }
        .login-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            height: auto;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="login-form">
            <div class="logo">
                <h1 class="text-primary">Nutrition Tracker</h1>
                <p class="text-muted">Welcome back! Please login to your account.</p>
            </div>
            
            <form id="login-form" method="post">
                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <!-- Removed reCAPTCHA div -->

                <button type="submit" class="btn btn-primary">Sign In</button>
                
                <div class="text-center mt-4">
                    <p class="mb-0">Don't have an account? <a href="register.php" class="text-primary">Register here</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>