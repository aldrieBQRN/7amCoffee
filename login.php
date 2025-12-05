<?php
include 'db_connect.php';

header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter both username and password.']);
    exit;
}

$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'id' => $user['id'], // Include the user ID
        'role' => $user['role']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}

$stmt->close();
$conn->close();
?>