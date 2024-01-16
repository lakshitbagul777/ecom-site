<?php
session_start();

if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header("location:index.php");
    exit();
}

if (!isset($_SESSION['email'])) {
    header("Location: admin_login.php");
    exit();
}

$server = "localhost";
$conn = new mysqli($server, "root", "", "e-com");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeProductId'])) {
    $productId = $_POST['removeProductId'];

    $sql = "DELETE FROM products WHERE product_id = '$productId'";
    if ($conn->query($sql)) {
        echo json_encode(['message' => 'Product removed successfully.']);
    } else {
        echo json_encode(['message' => 'Error removing product: ' . $conn->error]);
    }
}
?>
