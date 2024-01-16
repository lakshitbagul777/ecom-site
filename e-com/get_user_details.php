<?php
header("Content-type: application/json");
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

include_once('config.php');
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}


session_start();

if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'user') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);

$result = $stmt->execute();

if (!$result) {
    echo json_encode(['error' => 'Failed to fetch user details']);
    exit();
}

$userDetails = $stmt->get_result()->fetch_assoc();

if ($userDetails) {
    echo json_encode($userDetails);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>
