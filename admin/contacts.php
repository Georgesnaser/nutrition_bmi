<?php 
    include '../conx.php';
    include 'queries.php';
    include 'header.php';
    ob_start();
?>
    <div class="content">
        <h1>Contact Page</h1>
        <!-- <div class="buttons">
            <button id="pending-btn">Pending</button>
            <button id="rejected-btn">Rejected</button>
            <button id="accepted-btn">Accepted</button>
        </div> -->

        <!-- Pending Messages Section -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($nbcontacts > 0) {
                // Fetch item data
                while ($row = $result5->fetch_assoc()) {
                    $s=$row['statusID'];
                    if($s == 3){
                        echo '<tr style = "background-color:lightyellow">';
                    }else if($s == 2){
                        echo '<tr style = "background-color:#FFE4E1">';
                    }else{
                        echo '<tr style = "background-color:#F5FFFA">';
                    }
            ?>
            
                    <td><?=$row['nb']?></td>
                    <td><?=$row['fname']." ".$row['lname']?></td>
                    <td><?=$row['userEmail']?></td>
                    <td><?=$row['message']?></td>
                    <td><?=$row['date']?></td>
                    <td><?=$row['sname']?></td>
                    <td style="white-space: nowrap;">
                    <?php if ($row['statusID'] == 3) { ?>
                                <a href="?accept=1&userEmail=<?=$row['userEmail']?>&message=<?=$row['message']?>" style="display: inline-block;"><button>Accept</button></a>
                                <a href="?accept=0&userEmail=<?=$row['userEmail']?>&message=<?=$row['message']?>" style="display: inline-block; margin-left: 5px;"><button style="background-color: #ff4d4d; color: white; border: none;">Reject</button></a>
                            <?php } elseif ($row['statusID'] == 2) { ?>
                                <a href="?accept=1&userEmail=<?=$row['userEmail']?>&message=<?=$row['message']?>"><button>Accept</button></a>
                            <?php } else { ?>
                                <a href="?accept=0&userEmail=<?=$row['userEmail']?>&message=<?=$row['message']?>"><button style="background-color: #ff4d4d; color: white; border: none;">Reject</button></a>
                            <?php } ?>
                    </td>
                    
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
// Handle the "Accept" action
if (isset($_GET['accept']) && isset($_GET['userEmail'],$_GET['message'])) {
    $userEmail = $_GET['userEmail'];
    $message = $_GET['message'];
    
    if($_GET['accept'] == 0)
        $sql = "UPDATE contact SET statusID = 2 WHERE userEmail = '$userEmail' and message = '$message'";
    else  if($_GET['accept'] == 1)
        $sql = "UPDATE contact SET statusID = 1 WHERE userEmail = '$userEmail' and message = '$message'";

    if ($conn->query($sql) === TRUE) {
        //echo "<script>alert('User status updated successfully!');</script>";
        header("location:contacts.php");
    } else {
        echo "<script>alert('Error updating contact status: " . $conn->error . "');window.location.href='contacts.php';</script>";
    }
}
?>
<!-- 
<script>
    const pendingBtn = document.getElementById('pending-btn');
    const rejectedBtn = document.getElementById('rejected-btn');
    const acceptedBtn = document.getElementById('accepted-btn');

    const pendingSection = document.getElementById('pending-section');
    const rejectedSection = document.getElementById('rejected-section');
    const acceptedSection = document.getElementById('accepted-section');

    // Function to show the correct section
    function showSection(sectionToShow) {
        pendingSection.classList.add('hidden');
        rejectedSection.classList.add('hidden');
        acceptedSection.classList.add('hidden');
        sectionToShow.classList.remove('hidden');
    }

    // Event listeners for buttons
    pendingBtn.addEventListener('click', () => showSection(pendingSection));
    rejectedBtn.addEventListener('click', () => showSection(rejectedSection));
    acceptedBtn.addEventListener('click', () => showSection(acceptedSection));
</script> -->
</body>
</html>
