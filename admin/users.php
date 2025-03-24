<?php
// Include database connection and queries
include '../conx.php';
include 'queries.php';
include 'header.php';
ob_start();

// Handle the "Accept" action
if (isset($_GET['accept']) && isset($_GET['email'])) {
    $email = $_GET['email'];
    
    if ($_GET['accept'] == 0) {
        $sql = "UPDATE users SET status = 2 WHERE email = '$email'";
    } elseif ($_GET['accept'] == 1) {
        $sql = "UPDATE users SET status = 1 WHERE email = '$email'";
    }

    if ($conn->query($sql) === TRUE) {
        header("location: users.php");
    } else {
        echo "<script>alert('Error updating user status: " . $conn->error . "');window.location.href='users.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 5% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 600px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Content Spacing */
        .content {
            width: 100%;
            overflow-x: auto;
            padding: 20px;
            margin-top: 80px;
        }

        h1 {
            margin-bottom: 30px;
            color: #2c3e50;
        }

        /* Copy Emails Button */
        .content button {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #34495e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .content button:hover {
            background-color: #2c3e50;
            transform: translateY(-1px);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            overflow-x: auto;
            display: block;
            max-width: fit-content;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
            min-width: 100px; /* Ensure minimum column width */
        }

        th {
            background-color: #2980b9;
            color: white;
            position: sticky;
            top: 0;
        }

        thead {
            background-color: #2980b9;
        }

        tbody {
            background-color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        tr[data-status="pending"] {
            background-color: #fff8e1;
        }

        tr[data-status="rejected"] {
            background-color: #ffebee;
        }

        tr[data-status="accepted"] {
            background-color: #e8f5e9;
        }

        /* Button Styles */
        .accept-btn button, .reject-btn {
            padding: 6px 12px;
            margin: 0 4px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .accept-btn button {
            background-color: #009879;
            color: white;
        }

        .accept-btn button:hover {
            background-color: #007d63;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .reject-btn:hover {
            background-color: #bb2d3b;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Manage Users</h1>
        <button onclick="openModal()">Copy User's Emails</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($nbusers > 0) {
                    // Fetch item data
                    $i = 0;
                    while ($row = $result1->fetch_assoc()) {
                        $i++;
                        $s = $row['status'];
                        
                        if ($s == 3) {
                            echo '<tr data-status="pending">';
                        } elseif ($s == 2) {
                            echo '<tr data-status="rejected">';
                        } else {
                            echo '<tr data-status="accepted">';
                        }
                        ?>
                        <td><?= $i ?></td>
                        <td><?= $row['fname'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['sname'] ?></td>
                        <td>
                            <?php if ($row['status'] == 3) { ?>
                                <a href="?accept=1&email=<?= $row['email'] ?>" class="accept-btn"><button>Accept</button></a>
                                <a href="?accept=0&email=<?= $row['email'] ?>" class="reject-btn">Reject</a>
                            <?php } elseif ($row['status'] == 2) { ?>
                                <a href="?accept=1&email=<?= $row['email'] ?>" class="accept-btn"><button>Accept</button></a>
                            <?php } else { ?>
                                <a href="?accept=0&email=<?= $row['email'] ?>" class="reject-btn">Reject</a>
                            <?php } ?>
                        </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="emailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>User Emails</h2>
            <textarea id="email-list" rows="10" cols="50" readonly>
                <?php
                // Query to get emails
                $sql = "SELECT email FROM users";
                $result = $conn->query($sql);

                $emails = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $emails[] = $row['email'];
                    }
                }

                // Return emails as a comma-separated string without leading space
                echo implode(", ", $emails);
                ?>
            </textarea>
            <p>Emails have been copied to your clipboard!</p>
        </div>
    </div>

    <script>
        // Function to open the modal and copy emails to clipboard
        function openModal() {
            // Open the modal
            document.getElementById("emailModal").style.display = "block";

            // Copy emails to clipboard
            var emailText = document.getElementById("email-list").value;
            navigator.clipboard.writeText(emailText).then(function() {
                console.log("Emails copied to clipboard!");
            }, function(err) {
                console.error("Failed to copy emails: ", err);
            });
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById("emailModal").style.display = "none";
        }
    </script>
</body>
</html>