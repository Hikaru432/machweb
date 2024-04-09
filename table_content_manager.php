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
$query = "SELECT user.id as user_id, user.name, car.carmodel, car.plateno, car.car_id, car.manuname, car.color ,approvals.status
          FROM user
          JOIN car ON user.id = car.user_id
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
                            // Fetch available mechanics from the mechanic table with their corresponding names from the user table
                            $mechanic_query = "SELECT mechanic.mechanic_id, mechanic.jobrole, user.name 
                                                FROM mechanic 
                                                JOIN user ON mechanic.user_id = user.id";
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

<!-- Displaying mechanic names and progress percentages -->
<div class="container mt-5">
    <h2>Mechanic Progress</h2>
    <div class="row">
        <?php
        // Query to fetch mechanics who are not present in the progress table and belong to the current companyid
        $mechanic_query = "SELECT mechanic.user_id, user.name 
                           FROM mechanic 
                           JOIN user ON mechanic.user_id = user.id 
                           WHERE mechanic.user_id NOT IN 
                           (SELECT DISTINCT user_id FROM progress)
                           AND mechanic.companyid = '$companyid'";
        $mechanic_result = mysqli_query($conn, $mechanic_query);

        if ($mechanic_result && mysqli_num_rows($mechanic_result) > 0) {
            while ($mechanic_row = mysqli_fetch_assoc($mechanic_result)) {
                // Display mechanic name and default availability
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $mechanic_row['name']; ?></h5>
                            <p class="card-text"><strong>Availability</strong>: Available</p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        // Query to fetch mechanic names and progress percentage from the progress table for the current companyid
        $progress_query = "SELECT mechanic.user_id, ROUND(AVG(progress_percentage), 2) AS avg_progress
                            FROM progress
                            JOIN mechanic ON progress.user_id = mechanic.user_id
                            WHERE mechanic.companyid = '$companyid'
                            GROUP BY mechanic.user_id";
        $progress_result = mysqli_query($conn, $progress_query);

        if ($progress_result && mysqli_num_rows($progress_result) > 0) {
            while ($progress_row = mysqli_fetch_assoc($progress_result)) {
                // Fetch mechanic name
                $mechanic_id = $progress_row['user_id'];
                $mechanic_query = "SELECT name FROM user WHERE id = $mechanic_id";
                $mechanic_result = mysqli_query($conn, $mechanic_query);
                $mechanic_info = mysqli_fetch_assoc($mechanic_result);

                // Display mechanic name and progress percentage in a card
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $mechanic_info['name']; ?></h5>
                            <?php
                            // Check if mechanic has progress data
                            if ($progress_row['avg_progress'] !== null) {
                                echo "<p class='card-text'><strong>Availability</strong>: {$progress_row['avg_progress']}%</p>";
                            } else {
                                echo "<p class='card-text'>Availability: Available</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
