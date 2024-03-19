<?php
session_start();
include 'config.php'; // Assuming this file contains your database connection code

if(!isset($_SESSION['user_id'])){
   header('location:login.php');
   exit();
} 

$user_id = $_SESSION['user_id']; // Corrected variable name

// Assuming your database connection is established and stored in $conn
$query = "SELECT * FROM user WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-black">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="homemechanic.php">User</a>
                </li>
                <li class="nav-item"> 
                    <a class="nav-link text-white active" aria-current="page" href="repair_table_content.php">Sales</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="addproduct.php">Products</a>
                </li>
                <li class="nav-item"> 
                    <a class="nav-link text-white active" aria-current="page" href="repair_table_content.php">Customer</a> 
                </li>
              
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item text-white" href="#">Sales</a></li>
                        <li><a class="dropdown-item text-white" href="#">Products</a></li>
                        <li><a class="dropdown-item text-white" href="#">Customer</a></li>
                        <li><a class="dropdown-item text-white" href="#">Sales Report</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-white" href="#">Something else here</a></li>
                    </ul>
                </li>
                <li class="nav-item"> 
                    <a class="nav-link text-white active" aria-current="page" href="login.php">Logout</a> 
                </li>
            </ul>
        </div>
    </div>
</nav>

<h1>Welcome <?php echo isset($user_data['name']) ? $user_data['name'] : ''; ?></h1>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-r7Xgs1AeUp2GpS3r+pQ4kpz/zbGOGlZf6Mfk5Bv9os0/7AjLkZgJq6gxciXiBUpZ" crossorigin="anonymous"></script>

</body>
</html>
