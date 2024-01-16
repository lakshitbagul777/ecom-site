<?php
    error_reporting(0);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $server = "localhost";
        $db = "e-com";
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $conn = new mysqli($server, "root", "", $db);

        if ($conn->connect_error) {
            die("Database Connection Failed" . $conn->connect_error);
        }

        $exists_query = $conn->query("SELECT * FROM user WHERE email='$email'");
        
        if ($exists_query->num_rows > 0) {
            echo "<h1>This E-Mail is already registered!</h1><br><a href='user_login.php'>Login Here</a>";
        } else {
            $sql = "INSERT INTO user(full_name,email,password) VALUES ('$fullname', '$email', '$password')";
            if (!$conn->query($sql)) {
                die("<p>Error: $conn->error</p>");
            }
            echo '<h1>Registration Successful!</h1><br>';
            echo "<a href='user_login.php'>Login Here</a>";
        }

        $conn->close();
    } else {
        header("location:index.php");
    }
?>
