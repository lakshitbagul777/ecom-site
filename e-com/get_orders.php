<?php
    session_start();
    include_once 'config.php';
    
    if(!isset($_SESSION['admin_id']) || $_SESSION['type']!=="admin" || empty($_SESSION['admin_id'] || $_SERVER['REQUEST_METHOD']!=="POST")){
        echo json_encode(["error"=>"Access Prohibited!"]);
        exit();
    }
    error_log("admin_id : ".$_SESSION['admin_id']);
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if($conn->connect_error){
        echo json_encode(["error"=>"Couldn't connect to the database"]);
        exit();
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        $result = $conn->query("SELECT * FROM orders");
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        if($orders)
        {
            file_put_contents('get_orders_logs.txt', json_encode($orders) . PHP_EOL, FILE_APPEND);
            echo json_encode($orders);
        }
    } else {    
        echo json_encode(["error" => "Direct access not allowed"]);
    }
?>