<?php
    ob_start();
    include 'conx.php';
    include 'nav.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $message = $conn->real_escape_string($_POST['message']);
        $date = date('Y-m-d H:i:s');

        $sql = "INSERT INTO contact (userEmail, message, date, statusID) VALUES ('$email', '$message', '$date', '3')";

        if ($conn->query($sql) === TRUE) {
            header("Location: contact.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $sql1 = "SELECT c.*, s.sname FROM contact c, status s WHERE c.statusID = s.sid AND c.userEmail = '$email' ORDER BY c.date DESC";
    $result = $conn->query($sql1);
?>

<!-- contact Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background: linear-gradient(135deg, #9fd3c7 0%, #385170 100%);
            background-attachment: fixed;
            font-family: 'Arial', sans-serif;
            position: relative;
            margin: 0;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 100% 50%, transparent 20%, rgba(255,255,255,0.03) 21%, rgba(255,255,255,0.03) 34%, transparent 35%, transparent),
                radial-gradient(circle at 0% 50%, transparent 20%, rgba(255,255,255,0.03) 21%, rgba(255,255,255,0.03) 34%, transparent 35%, transparent) 0 -50px;
            background-size: 75px 100px;
            pointer-events: none;
            z-index: -1;
        }
        .custom-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .contact-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        .contact-title {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            color: #34495e;
            font-weight: bold;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .messages-table th,
        .messages-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .messages-table th {
            background-color: #34495e;
            color: white;
        }
        .messages-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .messages-table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="custom-container">
    <div class="contact-card">
        <h2 class="contact-title">Contact Us</h2>
        <form action="contact.php" method="post">
            <div class="form-group">
                <label class="form-label">Your Email:</label>
                <input type="email" name="email" class="form-input" value="<?=$email;?>" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Your Message:</label>
                <textarea name="message" class="form-input" rows="4" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
    <div>
        <h3 class="contact-title">Your Messages</h3>
        <table class="messages-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Message</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']); ?></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
                        <td><?= htmlspecialchars($row['sname']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>
