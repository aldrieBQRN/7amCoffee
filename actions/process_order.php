<?php
include '../db_connect.php';

header('Content-Type: application/json');

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

$items = $data['items'];
$paymentMethod = $data['payment_method'];
$totalAmount = $data['total'];

$formattedItemsArray = [];
foreach ($items as $item) {
    $formattedItemsArray[] = $item['name'] . " (x" . $item['qty'] . ")";
}
$itemsString = implode(", ", $formattedItemsArray);

$conn->begin_transaction();

try {
    // Insert into 'sales' table
    $stmt = $conn->prepare("INSERT INTO sales (items, payment, total) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $itemsString, $paymentMethod, $totalAmount);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save sale: " . $stmt->error);
    }
    $stmt->close();

    // Update Inventory (Subtract Stock)
    $updateStmt = $conn->prepare("UPDATE inventory SET stock = stock - ? WHERE id = ?");
    
    foreach ($items as $item) {
        $qty = $item['qty'];
        $id = $item['id'];
        $updateStmt->bind_param("ii", $qty, $id);
        if (!$updateStmt->execute()) {
             throw new Exception("Failed to update stock for item ID: " . $id);
        }
    }
    $updateStmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Order processed and saved to database']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>