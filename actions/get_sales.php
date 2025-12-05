<?php
include '../db_connect.php';

header('Content-Type: application/json');

// Select all sales, ordered by newest first
$sql = "SELECT id, date_time, items, payment, total FROM sales ORDER BY date_time DESC";
$result = $conn->query($sql);

$sales = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['total'] = (float)$row['total'];
        $row['id'] = (int)$row['id'];
        $sales[] = $row;
    }
}

echo json_encode($sales);

$conn->close();
?>