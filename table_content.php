<?php
include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['companyid']) || empty($_SESSION['companyid'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}
// Retrieve mechanic details from the database based on the session companyid
$company_id = $_SESSION['companyid']; // Fixed variable name
$query = "SELECT * FROM mechanic WHERE companyid = $company_id"; // Fixed SQL query
$result = mysqli_query($conn, $query);
$mechanic = mysqli_fetch_assoc($result);

// Redirect to login page if user is not a mechanic
if (!$mechanic) {
    header('Location: login.php');
    exit();
}

// Retrieve car data assigned to the mechanic
$query = "SELECT car.*, user.name AS username, manufacturer.name AS manufacturer_name, mechanic.jobrole, CONCAT(mechanic.firstname, ' - ', mechanic.jobrole) AS assigned_mechanic
          FROM car 
          JOIN user ON car.user_id = user.id
          LEFT JOIN assignments ON car.car_id = assignments.car_id
          LEFT JOIN mechanic ON assignments.mechanic_id = mechanic.mechanic_id
          LEFT JOIN autoshop ON mechanic.companyid = autoshop.companyid
          LEFT JOIN manufacturer ON car.manufacturer_id = manufacturer.id
          WHERE autoshop.companyid = $company_id";

$car_select = mysqli_query($conn, $query);

if (!$car_select) {
    die('Error in car query: ' . mysqli_error($conn));
}

?>

<div class="container">
    <h2>Car Users</h2>

    <div class="container">
        <table id="carTable" class="table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Car Name</th>
                    <th>Plate No</th>
                    <th>Assign Mechanic</th>
                    <th>Parts</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($car_select)) : ?>
                <?php
                // Fetch additional user details using user_id
                $user_id = $row['user_id'];
                $user_query = "SELECT * FROM user WHERE id = $user_id";
                $user_result = mysqli_query($conn, $user_query);
                $user = mysqli_fetch_assoc($user_result);
                ?>

                <tr data-user-id="<?php echo (int)$user_id; ?>" data-car-id="<?php echo (int)$row['car_id']; ?>">
                    <td class="username-cell"><?php echo isset($user['name']) ? $user['name'] : ''; ?></td>
                    <td><?php echo isset($row['manufacturer_name']) ? $row['manufacturer_name'] : ''; ?></td>
                    <td><?php echo isset($row['plateno']) ? $row['plateno'] : ''; ?></td>
                    <td><?php echo isset($row['assigned_mechanic']) ? $row['assigned_mechanic'] : 'Not Assigned'; ?></td>
                    <td><a href="machvalidate.php?mechanic_id=<?php echo $mechanic['mechanic_id']; ?>&car_id=<?php echo $row['car_id']; ?>&user_id=<?php echo $user_id; ?>">Parts</a></td>
                    <td><a href="machidentify.php?mechanic_id=<?php echo (int)$mechanic['mechanic_id']; ?>&car_id=<?php echo (int)$row['car_id']; ?>&user_id=<?php echo $user_id; ?>" class="btn btn-primary">View Profile</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .invalid-label {
        color: red;
    }
    .highlighted {
        background-color: #ffff66;
    }
</style>
