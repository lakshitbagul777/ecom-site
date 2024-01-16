<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

include_once('config.php');
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

$productQuery = "SELECT product_name, price FROM products WHERE product_id = ?";
$productStmt = $conn->prepare($productQuery);
$productStmt->bind_param("i", $productId);
$productStmt->execute();
$productResult = $productStmt->get_result();
$productDetails = $productResult->fetch_assoc();

$insertQuery = "INSERT INTO cart (user_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, 1)
ON DUPLICATE KEY UPDATE quantity = quantity + 1";

$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iisd", $userId, $productId, $productDetails['product_name'], $productDetails['price']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['error' => 'Failed to add to cart: ' . $stmt->error]);
}

$stmt->close();
$productStmt->close();
$conn->close();
?>
