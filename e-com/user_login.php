<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Login</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="user_login.php">User Login <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="admin_login.php">Admin Login <span class="sr-only">(current)</span></a>
      </li>
        
    </ul>
  </div>
</nav>

<div class="container">
        <form method="POST">
            <h1 class="jumbotron">User Login</h1>
                <div class="row">
                    <div class="form-group">
                        <label for="email">E-Mail</label>
                        <input type="text" name="email" class="form-control" placeholder="E-Mail" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="pass" class="form-control" placeholder="Password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group ">
                            <input type="submit" value="Login" class="btn btn-primary" name="login">                   
                    </div>
                </div>
                <p>New User? <a href="user_register.php">Register</a></p>
        </form>
    </div>
    <?php
        error_reporting(0);
        session_start();
        if(isset($_SESSION['email'])){
                if($_SESSION['type']=='user'){
                header("Location:user_dashboard.php");
                exit();
            }
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $server = "localhost";
            $db = "e-com";
    
            $conn = new mysqli($server, "root", "", $db);
            
            $row = null;
    
            $result = $conn->query("SELECT * FROM user WHERE email='$email'");
    
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
    
                if ($password===$row['password']) {
                    $_SESSION['email']=$email;
                    $_SESSION['type']='user';
                    $_SESSION['user_id']=$row['user_id'];
                    header("Location:user_dashboard.php");
                    exit();
                } else {
                    echo "<script type='text/javascript'>alert('Incorrect password!');</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('Email not found!');</script>";
            }
    
            $conn->close();
        }
    
    ?>

</body>
</html>