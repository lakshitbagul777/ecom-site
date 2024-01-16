<?php

session_start();

include_once('config.php');

$razorpay_response = json_decode(file_get_contents('php://input'), true);
file_put_contents('razorpay_logs.txt', json_encode($razorpay_response) . PHP_EOL, FILE_APPEND);

if (isset($razorpay_response['razorpay_payment_id']) && !empty($razorpay_response['razorpay_payment_id'])) {
    $payment_id = $razorpay_response['razorpay_payment_id'];
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    $user_id = $_SESSION['user_id'];
    $amount = $razorpay_response['totalBill'];
    $payment_status = 'completed';

    $products = json_encode($razorpay_response['products']);
    

    $insert_order_query = "INSERT INTO orders (user_id, amount, payment_status, products) 
                            VALUES ('$user_id', '$amount', '$payment_status', '$products')";

    if ($conn->query($insert_order_query) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payment failed or not captured']);
}
?>
