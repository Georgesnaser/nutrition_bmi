<?php
$sql1 = "SELECT * FROM users, status where isadmin=0 and status = sid";
$result1 = $conn->query($sql1);
$nbusers = $result1->num_rows;

$sql2 = "SELECT * FROM items, categories where categoryID = cid";
$result2 = $conn->query($sql2);
$nbItems = $result2->num_rows;

$sql3 = "SELECT * FROM categories";
$result3 = $conn->query($sql3);
$nbCategories = $result3->num_rows;

$sql4 = "SELECT * FROM plan";
$result4 = $conn->query($sql4);
$nbPlans = $result4->num_rows;

$sql5 = "SELECT * from contact,users,status where email = userEmail and sid = statusID";
$result5 = $conn->query($sql5);
$nbcontacts = $result5->num_rows;

// $msgPending = "SELECT c.message as message, c.date as mdate, u.email as email, u.fname as fname, u.lname as lname FROM contact c, users u WHERE c.userEmail = u.email and status =0";
// $res1 = $conn->query($msgPending);
// $PendingMsgNB = $res1->num_rows;

// $msgAccepted = "SELECT c.message as message, c.date as mdate, u.email as email, u.fname as fname, u.lname as lname FROM contact c, users u WHERE c.userEmail = u.email and status=1";
// $res2 = $conn->query($msgAccepted);
// $AcceptedMsgNB = $res2->num_rows;

// $msgRejected = "SELECT c.message as message, c.date as mdate, u.email as email, u.fname as fname, u.lname as lname FROM contact c, users u WHERE c.userEmail = u.email and status=2";
// $res3 = $conn->query($msgRejected);
// $RejectedMsgNB = $res3->num_rows;
?>