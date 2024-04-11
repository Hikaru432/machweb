<?php
session_start();
include 'config.php'; // Assuming this file contains your database connection code

if(!isset($_SESSION['companyid'])){
   header('location:clogin.php');
   exit();
} 

$companyid = $_SESSION['companyid'];
// echo "Company ID: " . $companyid; 

// Assuming your database connection is established and stored in $conn
$query = "SELECT * FROM autoshop WHERE companyid = $companyid";
$result = mysqli_query($conn, $query);

if($result && mysqli_num_rows($result) > 0) {
    $company_data = mysqli_fetch_assoc($result);
} else {
    // Redirect to some error page if company data is not found
}

// Fetch mechanics data for the current company
$mechanics_query = "SELECT u.id, u.firstname, u.lastname, u.email, m.employment, m.jobrole FROM mechanic m
                    INNER JOIN user u ON m.user_id = u.id
                    WHERE m.companyid = $companyid";
$mechanics_result = mysqli_query($conn, $mechanics_query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
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
                <li class="nav-item"> <a class="nav-link text-white active" aria-current="page" href="repair_table_content.php">Customer</a> 
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
                    <a class="nav-link text-white active" aria-current="page" href="clogin.php">Logout</a> 
                </li>
            </ul>
        </div>
    </div>
</nav>

<h1>Welcome <?php echo isset($company_data['companyname']) ? $company_data['companyname'] : ''; ?></h1>
<a href="homemanager.php?companyid=<?php echo $companyid; ?>" class="btn btn-primary">Service executive</a>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>