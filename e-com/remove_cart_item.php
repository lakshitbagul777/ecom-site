<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}
include_once 'config.php';
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$productId = isset($_POST['productId']) ? intval($_POST['productId']) : null;
$userId = intval($_SESSION['user_id']);

if ($productId === null || $userId === 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$updateQuery = "UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?";
$stmtUpdate = $conn->prepare($updateQuery);
$stmtUpdate->bind_param("ii", $userId, $productId);
$stmtUpdate->execute();
$stmtUpdate->close();

$deleteQuery = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND quantity = 0";
$stmtDelete = $conn->prepare($deleteQuery);
$stmtDelete->bind_param("ii", $userId, $productId);

if ($stmtDelete->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['error' => 'Failed to remove from cart: ' . $stmtDelete->error]);
}

$stmtDelete->close();
$conn->close();
?>
