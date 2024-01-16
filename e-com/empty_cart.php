<?php
session_start();
include_once 'config.php';

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    $user_id = $_SESSION['user_id'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $products = file_get_contents("php://input");
    error_log("\$products=" . print_r($products, true));
    $products = json_decode($products, true);

    if ($products === null) {
        echo json_encode(["error" => "Failed to decode JSON data"]);
        exit();
    }

    $product_ids = [];

    foreach ($products as $product) {
        $product_ids[] = $product['product_id'];
    }

    $product_ids_string = implode(',', $product_ids);

    $query = "DELETE FROM cart WHERE user_id = $user_id AND product_id IN ($product_ids_string)";

    $response = [];

    if ($conn->query($query) === TRUE) {
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
        $response['message'] = $conn->error;
    }

    $conn->close();

    echo json_encode($response);
} else {
    echo json_encode(["error" => "Direct access not allowed"]);
}
?>
