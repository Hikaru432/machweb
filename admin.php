<?php
session_start();
include 'config.php'; 

$companyid = $_SESSION['companyid'];
echo "Company ID: " . $companyid; 


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname']; // Get the entered first name
    
    // Query to check if the mechanic with entered first name and same company ID exists
    $query = "SELECT * FROM mechanic WHERE firstname = '$firstname' AND companyid = '$companyid'";
    $result = mysqli_query($conn, $query);
    
    // If a mechanic is found, set the session variables and redirect
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['mechanic_id'] = $row['mechanic_id']; // Set session variable to mechanic_id
        $_SESSION['companyid'] = $row['companyid'];
        header('Location: homemechanic.php');
        exit();
    } else {
        // If no mechanic found or not in the same company, display an alert
        echo "<script>alert('Mechanic not found or does not belong to the same company!');</script>";
    }    
}


// If company id is not set in session, redirect to login page
if (!isset($_SESSION['companyid'])) {
    header('location:clogin.php');
    exit();
} 

// Get company id from session
$companyid = $_SESSION['companyid'];

// Query to get company data
$query = "SELECT * FROM autoshop WHERE companyid = $companyid";
$result = mysqli_query($conn, $query);

// If company data found, fetch it
if($result && mysqli_num_rows($result) > 0) {
    $company_data = mysqli_fetch_assoc($result);
} else {
    // Redirect to some error page if company data is not found
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <style>
    .login-form-container {
        position: absolute;
        top: 10px;
        right: 10px;
        max-width: 300px;
    }
  </style>
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
                    <a class="nav-link active text-white" aria-current="page" href="#">User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="addproduct.php">Products</a>
                </li>
                <li class="nav-item"> 
                    <a class="nav-link text-white active" aria-current="page" href="logoutc.php">Logout</a> 
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Display error message if any -->
<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Mechanic login form -->
<div class="login-form-container" style="margin-top: 80px;">
    <form method="post">
        <div class="mb-3">
            <label for="firstname" class="form-label">Mechanic First Name:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <button type="submit" class="btn btn-primary">Login as Mechanic</button>
    </form>
</div>

<h1>Welcome <?php echo isset($company_data['companyname']) ? $company_data['companyname'] : ''; ?></h1>

    <div style="margin-top: 80px; margin-left: 80px;">
        <a href="homemanager.php?companyid=<?php echo $companyid; ?>" class="btn-primary" style="display: inline-block; width: 150px; height: 40px; background-color: #007bff; color: #fff; text-align: center; text-decoration: none; border-radius: 5px; line-height: 40px; margin-right: 10px;">Service Executive</a>
        <a href="add_staff.php?companyid=<?php echo $companyid; ?>" class="btn-primary" style="display: inline-block; width: 150px; height: 40px; background-color: #007bff; color: #fff; text-align: center; text-decoration: none; border-radius: 5px; line-height: 40px; margin-right: 10px;">Add Staff</a>
        <a href="addproduct.php?companyid=<?php echo $companyid; ?>" class="btn-primary" style="display: inline-block; width: 150px; height: 40px; background-color: #007bff; color: #fff; text-align: center; text-decoration: none; border-radius: 5px; line-height: 40px;">Add Product</a>                    
    </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>
