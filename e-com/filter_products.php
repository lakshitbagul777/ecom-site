<?php
header('Content-Type: application/json');
error_log('Received data: ' . print_r($_POST, true));

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Forbidden');
}

include_once('config.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
$minPrice = isset($_POST['minPrice']) ? (float)$_POST['minPrice'] : 0;
$maxPrice = isset($_POST['maxPrice']) ? (float)$_POST['maxPrice'] : PHP_INT_MAX;

$query = "";

if (!empty($category)) {
    $query .= "SELECT * FROM products WHERE price BETWEEN $minPrice AND $maxPrice AND category = '$category'";
} else {
    $query .= "SELECT * FROM products WHERE price BETWEEN $minPrice AND $maxPrice";
}

error_log("SQL query: " . $query);

$result = $conn->query($query);
if ($result == false) {
    error_log('Error executing query: ' . $conn->error);
    echo json_encode(['error' => 'Error executing query']);
    exit();
}

$products = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);

$conn->close();
?>
