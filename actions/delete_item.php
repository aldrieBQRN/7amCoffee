<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "DELETE FROM inventory WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting item']);
    }

    $stmt->close();
    $conn->close();
}
?>