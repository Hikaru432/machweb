<?php
include 'config.php';
session_start();

// Check if the user is logged in and has the role of mechanic
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Retrieve mechanic details from the database based on the session user_id
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM mechanic WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$mechanic = mysqli_fetch_assoc($result);

// Proceed only if the user has the role of mechanic
if (!$mechanic) {
    header('Location: login.php'); // Redirect if user is not a mechanic
    exit();
}

// Proceed to fetch repair table content for assigned mechanics
$query = "SELECT car.*, user.name AS username, validation.status AS validation_status, validation.comment, mechanic.jobrole, CONCAT(mechanic_user.name, ' - ', mechanic.jobrole) AS assigned_mechanic
          FROM car 
          JOIN user ON car.user_id = user.id
          LEFT JOIN validation ON car.user_id = validation.user_id AND car.car_id = validation.car_id
          LEFT JOIN assignments ON car.car_id = assignments.car_id
          LEFT JOIN mechanic ON assignments.mechanic_id = mechanic.mechanic_id
          LEFT JOIN user AS mechanic_user ON mechanic.user_id = mechanic_user.id
          WHERE mechanic.mechanic_id = {$mechanic['mechanic_id']}";

$car_select = mysqli_query($conn, $query);

if (!$car_select) {
    die('Error in car query: ' . mysqli_error($conn));
}

?>

<div class="container">
    <h2>Car user</h2>

    <div class="container">
        <table id="carTable" class="table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Car Name</th>
                    <th>Plate No</th>
                    <th>Assign Mechanic</th>
                    <th>Manager Validation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($car_select)) : ?>
                    <tr data-user-id="<?php echo (int)$row['user_id']; ?>" data-car-id="<?php echo (int)$row['car_id']; ?>">
                        <td class="username-cell"><?php echo isset($row['username']) ? $row['username'] : ''; ?></td>
                        <td><?php echo isset($row['manuname']) ? $row['manuname'] : ''; ?></td>
                        <td><?php echo isset($row['plateno']) ? $row['plateno'] : ''; ?></td>
                        <td><?php echo isset($row['assigned_mechanic']) ? $row['assigned_mechanic'] : 'Not Assigned'; ?></td>
                        <td>
                            <?php
                            if ($row['validation_status'] === 'invalid') {
                                echo '<span class="invalid-label">Invalid</span> - ' . (isset($row['comment']) ? $row['comment'] : '');
                            } elseif ($row['validation_status'] === 'valid') {
                                echo 'Valid';
                            } else {
                                echo 'Not determined yet';
                            }
                            ?>
                        </td>
                        <td><a href="machidentify.php?user_id=<?php echo (int)$row['user_id']; ?>&car_id=<?php echo (int)$row['car_id']; ?>" class="btn btn-primary">View Profile</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <style>
        .invalid-label {
            color: red;
        }
        .highlighted {
            background-color: #ffff66;
        }
    </style>
</div>
