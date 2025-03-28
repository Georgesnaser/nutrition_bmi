<?php
include 'conx.php';

$data = json_decode(file_get_contents('php://input'), true);
$weekData = $data['weekData'];
$validUntil = date('Y-m-d H:i:s', strtotime('+7 days'));

$query = "INSERT INTO meal_plans (week_data, valid_until) VALUES ('$weekData', '$validUntil')";

if (mysql_query($query, $conn)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysql_error()]);
}

mysql_close($conn);
?>
