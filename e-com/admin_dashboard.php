<?php
session_start();

if (!$_SESSION['type'] == 'admin') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeProductId'])) {
    $productId = $_POST['removeProductId'];

    $sql = "DELETE FROM products WHERE id = '$productId'";
    if ($conn->query($sql)) {
        echo "Product removed successfully.";
    } else {
        echo "Error removing product: " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productName']) && isset($_POST['productPrice'])) {
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];

    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["productImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["productImage"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
            $sql = "INSERT INTO products (name, price, product_image) VALUES ('$productName', '$productPrice', '$targetFile')";
            if ($conn->query($sql)) {
                echo "Product added successfully.";
            } else {
                echo "Error adding product: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#manageOrders" id="orders">
                                Order Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#manageProducts" id="products">
                                Product Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

                <div id="orderManagement" class="mt-4">
                    <h2 class="mt-4">Order Management</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Order Date</th>
                                <th scope="col">User ID</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Payment Status</th>
                                <th scope="col">Product Details</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTable">
            
                        </tbody>
                    </table>
                </div>

                <div id="productManagement" class="mt-4">
                    <h2 class="mt-4">Product Management</h2>
                    <form id="productForm" enctype="multipart/form-data" method="POST">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" name="productName" id="productName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Product Price</label>
                            <input type="number" name="productPrice" id="productPrice" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="productCategory">Product Category</label>
                            <input type="text" name="productCategory" id="productCategory" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="productImage">Product Image</label>
                            <input type="file" name="productImage" id="productImage" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>

                    <h3>Products:</h3>
                    <?php
                    $products = getProducts($conn);
                    foreach ($products as $product) : ?>
                        <div class="card" style="width: 18rem; display: inline-block; margin: 10px;">
                            <img src="<?= $product['product_image'] ?>" class="card-img-top" alt="<?= $product['product_name'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $product['product_name'] ?></h5>
                                <p class="card-text"><?= $product['price'] ?></p>
                                <form class="remove-product-form">
                                    <input type="hidden" name="removeProductId" value="<?= $product['product_id'] ?>">
                                    <input type="submit" class="btn btn-danger" value="Remove">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </main>
        </div>
    </div>

    <script src="js/admin_dashboard.js"></script>


</body>

</html>