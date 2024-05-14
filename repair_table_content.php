<?php
include 'config.php';

// Check if the mechanic_id is provided through the URL
if (!isset($_GET['mechanic_id'])) {
    header('Location: login.php');
    exit();
}

$mechanic_id = $_GET['mechanic_id'];

// Proceed to fetch mechanic details based on the provided mechanic_id
$query = "SELECT * FROM mechanic WHERE mechanic_id = $mechanic_id";
$result = mysqli_query($conn, $query);
$mechanic = mysqli_fetch_assoc($result);

// Check if mechanic exists
if (!$mechanic) {
    header('Location: login.php');
    exit();
}

// Proceed to fetch repair table content for the specified mechanic
$query = "SELECT DISTINCT user.id AS user_id, user.name, car.carmodel, manufacturer.name AS manuname, car.car_id, mechanic.jobrole, mechanic.firstname, mechanic.middlename, mechanic.lastname
          FROM user
          JOIN car ON user.id = car.user_id
          JOIN assignments ON car.car_id = assignments.car_id
          JOIN mechanic ON assignments.mechanic_id = mechanic.mechanic_id
          JOIN user AS mechanic_user ON mechanic.companyid = mechanic_user.companyid
          JOIN manufacturer ON car.manufacturer_id = manufacturer.id
          WHERE mechanic.mechanic_id = $mechanic_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-black">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="homemechanic.php">Mechanic</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="homemechanic.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" aria-current="page" href="#">Notifications<span id="notification-badge" class="badge bg-danger"></span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<h3 style="margin: 20px;">Job Available</h3>

<!-- The table -->
<div class="container">
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $row['name']; ?></h3>
                        <h6 class="card-title"><?php echo $row['manuname']; ?></h6>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo $row['carmodel']; ?></h6>
                        <p class="card-text">
                            <strong>Assigned Mechanic:</strong>
                            <span style="margin-left: 5px;">
                                <?php 
                                    if ($row['jobrole'] && $row['firstname']) {
                                        echo $row['firstname'] . ' - ' . $row['jobrole'];
                                    } else {
                                        echo 'Not Assigned';
                                    }
                                ?>
                            </span>
                        </p>
                        <a href="machrepair.php?mechanic_id=<?php echo $mechanic_id; ?>&car_id=<?php echo $row['car_id']; ?>&user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary">Repair</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
