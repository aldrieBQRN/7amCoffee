<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO inventory (item_name, category, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $name, $category, $price, $stock);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding item']);
    }
    
    $stmt->close();
    $conn->close();
}
?>