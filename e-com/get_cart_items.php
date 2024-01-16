<?php

session_start();

if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'){
    header("HTTP/1.0 403 Forbidden");
    echo "Access Forbidden";
}
include_once('config.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM cart WHERE user_id = '$userId'");

$cartItems = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = [
            'product_id'=>$row['product_id'],
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }
} else {
    echo json_encode(['error' => 'Failed to fetch cart items']);
    exit();
}

echo json_encode($cartItems);
?>
