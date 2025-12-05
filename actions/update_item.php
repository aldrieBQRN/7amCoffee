<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE inventory SET item_name=?, category=?, price=?, stock=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $name, $category, $price, $stock, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating item']);
    }

    $stmt->close();
    $conn->close();
}
?>