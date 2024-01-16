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
include_once('config.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
function getProducts($conn)
{
    $result = $conn->query("SELECT * FROM products");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getOrders($conn)
{
    $result = $conn->query("SELECT * FROM orders");
    return $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productName']) && isset($_POST['productPrice'])) {
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];
    $productCategory = $_POST['productCategory'];

    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["productImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if ($check === false) {
        $response = array("success" => false, "message" => "Image is not a valid image");
        echo json_encode($response);
        exit();
    }

    if (file_exists($targetFile)) {
        $response = array("success" => false, "message" => "Sorry, file already exists.");
        echo json_encode($response);
        exit();
    }

    if ($_FILES["productImage"]["size"] > 500000) {
        $response = array("success" => false, "message" => "Sorry, your file is too large.");
        echo json_encode($response);
        exit();
    }

    $allowedFormats = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedFormats)) {
        $response = array("success" => false, "message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        echo json_encode($response);
        exit();
    }

    if ($uploadOk == 0) {
        $response = array("success" => false, "message" => "Sorry, your file was not uploaded.");
        echo json_encode($response);
        exit();
    } else {
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
            $relativePath = "uploads/" . basename($_FILES["productImage"]["name"]);
            $stmt = $conn->prepare("INSERT INTO products (product_name, price, category, product_image) VALUES (?, ?, ?, ?)");

            if ($stmt === false) {
                $response = array("success" => false, "message" => "Error in preparing the SQL statement: " . $conn->error);
                echo json_encode($response);
                exit();
            }

            $stmt->bind_param("ssss", $productName, $productPrice, $productCategory, $relativePath);

            if ($stmt->execute()) {
                $response = array("success" => true, "message" => "Product added successfully.");
                echo json_encode($response);
            } else {
                $response = array("success" => false, "message" => "Error adding product: " . $stmt->error);
                echo json_encode($response);
            }

            $stmt->close();
        } else {
            $response = array("success" => false, "message" => "Sorry, there was an error uploading your file.");
            echo json_encode($response);
        }
    }
}
?>
