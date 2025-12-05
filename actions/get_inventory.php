<?php
include '../db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT id, item_name as name, category, price, stock FROM inventory ORDER BY id ASC";
$result = $conn->query($sql);

$items = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['price'] = (float)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $items[] = $row;
    }
}

echo json_encode($items);

$conn->close();
?>