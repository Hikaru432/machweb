<?php
include 'config.php';
session_start();

// Fetch companyid from query parameter
$companyid = isset($_GET['companyid']) ? $_GET['companyid'] : null;
$companyid = $_SESSION['companyid'];

// Check if companyid is set
if (!$companyid) {
    die('Company ID is missing.');
}

// Fetch data from the user, car, and approvals tables only if companyid is set
$query = "SELECT user.id as user_id, user.name, car.carmodel, car.plateno, car.car_id, car.color, manufacturer.name AS manuname, approvals.status
          FROM user
          JOIN car ON user.id = car.user_id
          LEFT JOIN manufacturer ON car.manufacturer_id = manufacturer.id
          LEFT JOIN approvals ON user.id = approvals.user_id AND car.car_id = approvals.car_id
          WHERE EXISTS (
              SELECT 1 FROM service
              WHERE service.user_id = user.id AND service.companyid = '$companyid'
          )";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}
?>

<div class="container mt-5">
    <h2>Service Executive</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Manufacturer</th>
                <th>Car Model</th>
                <th>Plato no.</th>
                <th>Color</th>
                <th>Mechanic Approval</th>
                <th>Assign Mechanic</th>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><strong><?php echo $row['name']; ?></strong></td>
                    <td><?php echo $row['manuname']; ?></td>
                    <td><?php echo $row['carmodel']; ?></td>
                    <td><?php echo $row['plateno']; ?></td>
                    <td><?php echo $row['color']; ?></td>
                    <td>
                        <?php
                        if (strtolower($row['status']) === '1') {
                            echo 'Approve';
                        } elseif (strtolower($row['status']) === '0') {
                            echo 'Not Approve';
                            if (!empty($row['reason'])) {
                                echo ' - ' . $row['reason'];
                            }
                        } else {
                            echo 'Not Set';
                        }
                        ?>
                    </td>
                    <td>
                    <select name="mechanic_id" class="mechanic-select">
                        <option value="">Select mechanic</option>
                        <?php
                        // Fetch available mechanics from the mechanic table with their corresponding names from the user table, filtered by company ID
                        $mechanic_query = "SELECT mechanic_id, CONCAT(firstname) AS name, jobrole
                            FROM mechanic 
                            WHERE companyid = '$companyid'";
                        $mechanic_result = mysqli_query($conn, $mechanic_query);
                        if ($mechanic_result && mysqli_num_rows($mechanic_result) > 0) {
                            while ($mechanic_row = mysqli_fetch_assoc($mechanic_result)) {
                                echo "<option value=\"{$mechanic_row['mechanic_id']}\">{$mechanic_row['jobrole']} - {$mechanic_row['name']}</option>";
                            }
                        } else {
                            echo "<option value=\"\">No mechanics available</option>";
                        }
                        ?>
                    </select>

                        <button type="button" class="btn-assign-mechanic" data-user-id="<?php echo $row['user_id']; ?>" data-car-id="<?php echo $row['car_id']; ?>">Assign</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>


    <!-- Script for assigning mechanics -->
    <script>
        $(document).ready(function() {
            function loadRepairTable() {
                $.get('repair_table_content.php', function(data) {
                    $('#repair-table-content').html(data);
                });
            }

        });
    </script>
    <script>
        $(document).off('click', '.btn-assign-mechanic').on('click', '.btn-assign-mechanic', function() {
            var userId = $(this).data('user-id');
            var carId = $(this).data('car-id');
            var mechanicId = $(this).closest('tr').find('.mechanic-select').val();

            $.post('assign_mechanic.php', {
                userId: userId,
                carId: carId,
                mechanicId: mechanicId
            }, function(response) {
                if (response.success) {
                    alert('Mechanic assigned successfully!');
                    // Reload only the relevant part of the table
                    loadRepairTable();
                } else {
                    alert('Error assigning mechanic: ' + response.message);
                }
            }, 'json');
        });
    </script>
</div>

<br>
<br>

<div class="container mt-5">
    <h2 class="text-center mb-4">Mechanic Progress</h2>
    <div class="row">
        <br>
        <?php
        // Query to fetch all mechanics for the current companyid
        $mechanic_query = "SELECT m.mechanic_id, CONCAT(m.firstname) AS name, m.jobrole
                           FROM mechanic AS m
                           WHERE m.companyid = '$companyid'";
        $mechanic_result = mysqli_query($conn, $mechanic_query);

        if ($mechanic_result && mysqli_num_rows($mechanic_result) > 0) {
            while ($mechanic_row = mysqli_fetch_assoc($mechanic_result)) {
                ?>
                <div class="col-md-6 mb-4" style="width: 400px;">
                    <div class="card border-2">
                        <div class="card-body">
                            <h3 class="card-title mb-3"><?php echo $mechanic_row['name']; ?></h3>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $mechanic_row['jobrole']; ?></h6>
                            <hr>
                            <?php
                            // Query to fetch progress data for the mechanic
                            $progress_query = "SELECT u.name AS user_name, c.carmodel, ROUND(AVG(p.progress_percentage), 2) AS avg_progress
                                FROM progress AS p
                                INNER JOIN user AS u ON p.user_id = u.id
                                INNER JOIN car AS c ON p.car_id = c.car_id
                                WHERE p.mechanic_id = '{$mechanic_row['mechanic_id']}'
                                GROUP BY u.name, c.carmodel";
                            $progress_result = mysqli_query($conn, $progress_query);

                            if ($progress_result && mysqli_num_rows($progress_result) > 0) {
                                while ($progress_row = mysqli_fetch_assoc($progress_result)) {
                                    echo "<p class='card-text'><strong>User:</strong> {$progress_row['user_name']}</p>";
                                    echo "<p class='card-text'><strong>Car Model:</strong> {$progress_row['carmodel']}</p>";
                                    $progress_percentage = $progress_row['avg_progress'];
                                    $status = '';

                                    if ($progress_percentage < 80) {
                                        $status = 'Under Repair';
                                        $color = 'text-danger';
                                    } elseif ($progress_percentage >= 90) {
                                        $status = 'Ready for Assignment';
                                        $color = 'text-success';
                                    } else {
                                        $status = 'In Progress';
                                        $color = 'text-warning';
                                    }

                                    echo "<p class='card-text'><strong>Status:</strong> <span class='$color'>$status</span></p>";
                                    echo "<p class='card-text'><strong>Progress Percentage:</strong> {$progress_percentage}%</p>";
                                }
                            } else {
                                echo "<p class='card-text'><strong>Status:</strong> Available</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='col-12'>No mechanics available</p>";
        }
        ?>
    </div>
</div>

