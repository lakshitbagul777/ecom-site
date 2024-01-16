<?php
session_start();

if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'user') {
    header("location:index.php");
    exit();
}

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: user_login.php");
    exit();
}

$email = $_SESSION['email'];
include_once('config.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$result = $conn->query("SELECT * FROM user WHERE email='$email'");
$userDetails = $result->fetch_assoc();

function getProductCategories($conn)
{
    $result = $conn->query("SELECT DISTINCT category FROM products");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$categories = getProductCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">User Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="navbar-text"><?php echo $email; ?></span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="openCartModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M10 19.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5zm3.5-1.5c-.828 0-1.5.671-1.5 1.5s.672 1.5 1.5 1.5 1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5zm1.336-5l1.977-7h-16.813l2.938 7h11.898zm4.969-10l-3.432 12h-12.597l.839 2h13.239l3.474-12h1.929l.743-2h-4.195z"/></svg>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div id="orderPlacedMessage" class="alert alert-success d-none" role="alert">
    Order placed successfully!
</div>

<div class="container mt-5">
    <h1>Welcome, <?php echo $userDetails['full_name']; ?>!</h1>

    <div class="mb-4">
        <label for="categoryFilter">Filter by Category:</label>
        <select id="categoryFilter" class="form-control">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
            <?php endforeach; ?>
        </select>
</div>

    <div class="mb-4">
        <label for="minPrice">Min Price:</label>
        <input type="number" id="minPrice" class="form-control" placeholder="Enter Min Price">
    </div>

    <div class="mb-4">
        <label for="maxPrice">Max Price:</label>
        <input type="number" id="maxPrice" class="form-control" placeholder="Enter Max Price">
    </div>

    <button type="button" class="btn btn-primary" id="applyFiltersBtn">Apply Filters</button>

    <div class="row mt-4" id="productContainer">
    </div>
</div>

<div class="modal" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Shopping Cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="cartContainerModal">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="rzp-button1" data-email=<?php $_SESSION['email'] ?>>Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="orderPlacedToast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
    <div class="toast-header">
        <strong class="mr-auto">Order Placed</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
    </div>
</div>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="js/user_dashboard.js"></script>

</body>
</html>
