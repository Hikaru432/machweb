<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

// Retrieve Repair data for the specified user and car
$user_id = $_GET['user_id']; // Make sure to validate and sanitize user inputs
$car_id = $_GET['car_id'];   // Make sure to validate and sanitize user inputs

$repair_data = array();  // Associative array to store diagnoses for different problems

// Function to fetch and store data for a specific problem
function fetchDataForProblem($conn, $user_id, $car_id, $problem)
{
    $query = "SELECT diagnosis FROM repair WHERE user_id = ? AND plateno = ? AND problem = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $car_id, $problem);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['diagnosis'];
    }

    mysqli_stmt_close($stmt);
    return $data;
}

// Function to fetch approval reason from approvals table
function getApprovalReason($conn, $user_id, $car_id) {
    $approval_query = "SELECT reason FROM approvals WHERE user_id = '$user_id' AND car_id = '$car_id'";
    $approval_result = mysqli_query($conn, $approval_query);

    if ($approval_result) {
        $approval_info = mysqli_fetch_assoc($approval_result);
        return $approval_info['reason'];
    } else {
        return 'Error fetching reason: ' . mysqli_error($conn);
    }
}

// For Major
$engine_overhaul_data = fetchDataForProblem($conn, $user_id, $car_id, 'Engine Overhaul');
$engine_low_power_data = fetchDataForProblem($conn, $user_id, $car_id, 'Engine Low Power');
$electrical_problem_data = fetchDataForProblem($conn, $user_id, $car_id, 'Electrical Problem');

// For Maintenance
$battery_data = fetchDataForProblem($conn, $user_id, $car_id, 'Battery');
$light_data = fetchDataForProblem($conn, $user_id, $car_id, 'Light');
$oil_data = fetchDataForProblem($conn, $user_id, $car_id, 'Oil');
$water_data = fetchDataForProblem($conn, $user_id, $car_id, 'Water');
$brake_data = fetchDataForProblem($conn, $user_id, $car_id, 'Brake');
$air_data = fetchDataForProblem($conn, $user_id, $car_id, 'Air');
$gas_data = fetchDataForProblem($conn, $user_id, $car_id, 'Gas');
$tire_data = fetchDataForProblem($conn, $user_id, $car_id, 'Tire');

// Fetch user and car information
$user_car_query = "SELECT user.name, user.image, car.manuname ,car.carmodel, car.plateno FROM user
                    JOIN car ON user.id = car.user_id
                    WHERE user.id = '$user_id' AND car.car_id = '$car_id'";
$user_car_result = mysqli_query($conn, $user_car_query);

if ($user_car_result && mysqli_num_rows($user_car_result) > 0) {
    $user_car_info = mysqli_fetch_assoc($user_car_result);
    $carmodel = $user_car_info['carmodel'];
    $plateno = $user_car_info['plateno'];
    $manuname = $user_car_info['manuname'];
} else {
    die('Error fetching user and car information: ' . mysqli_error($conn));
}

// Define the problem parts mapping array
$problem_parts_mapping = array(
    'Mechanical Issues' => array(
        'Piston and Piston Rings' => ['Cylinder Walls/Liners', 'Piston Pins/Piston Wrist Pins', 'Piston Skirts', 'Piston Crowns', 'Piston Ring Grooves', 'Valve Train Components', 'Engine Block', 'Lubrication System', 'Compression and Leak Down Test', 'Cooling System'],
        'Valve Train' => ['Camshaft(s)', 'Timing Chain/Belt', 'Timing Chain Tensioner/Guides', 'Camshaft Bearings', 'Valve Lifters/Tappets', 'Pushrods', 'Rocker Arms', 'Valve Springs', 'Valve Retainers and Keepers', 'Valves', 'Valve Seats', 'Valve Guides', 'Valve Seals', 'Valve Springs Seats', 'Valve Cover and Gaskets', 'Timing Components (sprockets, guides, tensioners)'],
        'Timing Chain or Belt' => ['Timing Chain/Belt', 'Timing Chain Tensioner', 'Timing Belt Tensioner', 'Timing Chain Guides', 'Timing Belt Idler Pulleys', 'Timing Chain/Belt Cover', 'Timing Gears/Sprockets', 'Timing Chain/Belt Dampener', 'Timing Chain/Belt Seal', 'Crankshaft Position Sensor (if applicable)'],
    ),
    'Fuel and Air Intake System' => array(
        'Fuel Injection System' => ['Fuel Injectors', 'Fuel Pump', 'Fuel Pressure Regulator', 'Fuel Filter', 'Fuel Rail', 'Throttle Body', 'Mass Airflow Sensor (MAF)', 'Manifold Absolute Pressure Sensor (MAP)', 'Engine Control Unit (ECU)', 'Oxygen Sensor (O2 Sensor)', 'Idle Air Control Valve (IACV)', 'Fuel Pressure Sensor', 'Fuel Pump Relay', 'Fuel Tank and Fuel Lines', 'Fuel Injector O-rings', 'Fuel Injector Wiring Harness'],
        'Air Filter' => ['Air Filter Element', 'Air Filter Housing/Box', 'Air Intake Duct/Tube', 'Air Filter Clips/Clamps', 'Air Filter Gasket/Seal', 'Air Filter Cover/Cap', 'Air Filter Mounting Bracket', 'Air Filter Restriction Gauge', 'Air Filter Service Indicator'],
        'Throttle Body' => ['Throttle Body Housing', 'Throttle Plate', 'Throttle Shaft', 'Throttle Position Sensor (TPS)', 'Idle Air Control Valve (IACV)', 'Throttle Body Gasket', 'Throttle Body Motor/Actuator', 'Throttle Body Mounting Bolts', 'Throttle Cable', 'Throttle Return Spring', 'Throttle Body Heater', 'Throttle Body Cleaning Kit'],
    ),
    'Cooling and Lubrication' => array(
        'Coolant Leaks' => ['Radiator', 'Radiator Cap', 'Coolant Hoses', 'Heater Core', 'Water Pump', 'Coolant Reservoir/Tank', 'Thermostat Housing', 'Freeze Plugs (Expansion Plugs)', 'Cylinder Head Gasket', 'Intake Manifold Gasket', 'Coolant Temperature Sensor', 'Coolant Drain Plug', 'Coolant Hose Clamps', 'Engine Block'],
        'Oil Leaks' => ['Valve Cover Gasket', 'Oil Pan Gasket', 'Oil Filter', 'Oil Drain Plug', 'Rear Main Seal', 'Front Crankshaft Seal', 'Oil Cooler Lines', 'Timing Cover Gasket', 'Oil Filler Cap', 'Oil Pressure Switch/Sensor', 'Oil Pan', 'Turbocharger/ Supercharger Oil Seals', 'Camshaft Seals', 'Cylinder Head Gasket'],
        'Water Pump' => ['Water Pump Housing', 'Water Pump Impeller', 'Water Pump Seal', 'Water Pump Bearing', 'Water Pump Gasket', 'Water Pump Pulley', 'Water Pump Belt Tensioner', 'Water Pump Belt or Chain', 'Water Pump Bypass Valve', 'Water Pump Housing Bolts', 'Coolant Inlet/Outlet Ports', 'Coolant Temperature Sensor', 'Heater Core Hose Fittings'],
    )
);

function displayProblemParts($problem_parts_mapping)
{
    foreach ($problem_parts_mapping as $problem => $parts) {
        echo '<div class="for-major-container bg-gray-100 p-4 rounded-md shadow-md mb-4">';
        echo '<h2 class="text-2xl font-bold mb-4">' . $problem . '</h2>';
        echo '<div class="grid grid-cols-2 gap-4">';
        foreach ($parts as $part => $subparts) {
            echo '<div style="padding: 10px; border: 2px solid black; border-radius: 8px; background-color: #f0f0f0; display: flex; flex-direction: column;">';
            echo '<strong style="margin-bottom: 10px;">' . $part . '</strong>';
            echo '<select class="select2-multiple" multiple="multiple" style="margin-bottom: 10px;">';
            foreach ($subparts as $subpart) {
                echo '<option>' . $subpart . '</option>';
            }
            echo '</select>';
            echo '<button style="background-color: #b30036; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer;">Approve</button>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation</title>
    <link rel="stylesheet" href="css/machvalidate.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>

<body>

<nav class="navbar navbar-expand-lg bg-black">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Mechanic</a>
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
                        <li><a class="dropdown-item text-white" href="#">Action</a></li>
                        <li><a class="dropdown-item text-white" href="#">Another action</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-white" href="#">Something else here</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- For profile -->
    <ul class="flex justify-normal items-center mt-8 " id="container">
        <li>
          <?php
            if($user_car_info['image'] == ''){
                  echo '<img src="images/default-avatar.png" class="w-20 h-20 rounded-full">';
                }else{
                    echo '<img src="uploaded_img/' . $user_car_info['image'] . '" class="w-20 h-20 rounded-full">';
                }
            ?>
        </li>
        <li class="px-4"><p class="mb-2 font-medium"><strong>User Name</strong>: <?php echo '<span class="font-normal">'.$user_car_info['name'] . '</span>'; ?></p></li>
        <li class="px-4"><p class="mb-2 font-medium"><strong>Manufacturer</strong>: <?php echo '<span class="font-normal">'. $manuname . '</span>'; ?></p></li>
        <li class="px-4"><p class="mb-2 font-medium"><strong>Car Model</strong>: <?php echo '<span class="font-normal">'. $carmodel . '</span>'; ?></p></li>
        <li class="px-4"><p class="mb-2 font-medium"><strong>Plate #</strong>:  <?php echo '<span class="font-normal">'. $plateno .'</span>'; ?></p></li>
    </ul>


    <div class="for-major-container bg-white p-4 rounded-md shadow-md mb-4">
    <h2 class="text-2xl font-bold mb-4">Primary Engine System</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php if (!empty($engine_overhaul_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Mechanical Issues:</strong>
                <?php echo implode(', ', $engine_overhaul_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Mechanical Issues:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($engine_low_power_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Fuel and Air intake System:</strong>
                <?php echo implode(', ', $engine_low_power_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Fuel and Air intake System:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($electrical_problem_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Cooling and Lubrication:</strong>
                <?php echo implode(', ', $electrical_problem_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Cooling and Lubrication:</strong>
                <p>Good</p>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Parts -->
<section>
      <!-- Display the problem parts with improved design -->
      <div class="for-major-container bg-gray-100 p-4 rounded-md shadow-md">
        <h2 class=" font-bold mb-4">Primary Engine System</h2>
        <?php displayProblemParts($problem_parts_mapping); ?>
    </div>

    <!-- Display selected parts in a separate container -->
    <div class="selected-parts-container bg-white p-4 rounded-md shadow-md mt-4">
        <h2 class="text-2xl font-bold mb-4">Selected Parts</h2>
        <h2 class="text-xl font-bold mb-4">Primary Engine System</h2>
        <div id="selectedParts" style="display: flex; flex-wrap: wrap;"></div>
    </div>
 </section>

<!-- Maintenance part -->
<div class="for-maintenance-container bg-white p-4 mt-4 rounded-md shadow-md">
    <h2 class="text-2xl font-bold mb-4">Maintenance</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (!empty($battery_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Battery:</strong>
                <?php echo implode(', ', $battery_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Battery:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($light_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Light:</strong>
                <?php echo implode(', ', $light_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Light:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($oil_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Oil:</strong>
                <?php echo implode(', ', $oil_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Oil:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($water_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Water:</strong>
                <?php echo implode(', ', $water_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Water:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($brake_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Brake:</strong>
                <?php echo implode(', ', $brake_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Brake:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($air_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Air:</strong>
                <?php echo implode(', ', $air_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Air:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($gas_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Gas:</strong>
                <?php echo implode(', ', $gas_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Gas:</strong>
                <p>Good</p>
            </div>
        <?php } ?>

        <?php if (!empty($tire_data)) { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Tire:</strong>
                <?php echo implode(', ', $tire_data); ?>
            </div>
        <?php } else { ?>
            <div class="bg-gray-100 p-4 rounded-md shadow-md">
                <strong>Tire:</strong>
                <p>Good</p>
            </div>
        <?php } ?>
    </div>
</div>


  

    <!-- Approval Reason -->
    <!-- <div class="bg-white p-4 rounded-md shadow-md">
        <p><span class="font-bold">Reason:</span> <?php echo getApprovalReason($conn, $user_id, $car_id); ?></p>
    </div> -->

    <button id="validBtn" class="mt-4 bg-green-500 text-white p-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring focus:border-green-300">
        Valid
    </button>
    <button id="invalidBtn" class="mt-4 bg-red-500 text-white p-2 rounded-md hover:bg-red-600 focus:outline-none focus:ring focus:border-red-300">
        Invalid
    </button>


    <!-- Parts script -->
    <script>
    $(document).ready(function() {
        // Initialize Select2 for better select styling
        $('.select2-multiple').select2({
            theme: 'bootstrap4',
            placeholder: 'Select parts...',
        });

        var selectedCategories = {}; // Track selected categories

        // Event listener for select change
        $('.select2-multiple').on('change', function() {
            // Clear previous selections
            $('#selectedParts').empty();
            selectedCategories = {};

            // Get selected options and display them
            $('.select2-multiple').each(function() {
                var category = $(this).closest('.for-major-container').find('h2').text();
                var selectedParts = $(this).val();
                if (selectedParts && selectedParts.length > 0) {
                    if (!selectedCategories.hasOwnProperty(category)) {
                        selectedCategories[category] = selectedParts;
                    } else {
                        selectedCategories[category] = selectedCategories[category].concat(selectedParts);
                    }
                }
            });

            // Display selected categories and parts
            for (var category in selectedCategories) {
                if (selectedCategories.hasOwnProperty(category)) {
                    var parts = selectedCategories[category];
                    var html = '<div class="bg-gray-100 p-4 rounded-md shadow-md">';
                    html += '<strong>' + category + ':</strong> ';
                    html += parts.join(', ');
                    html += '</div>';
                    $('#selectedParts').append(html);
                }
            }

            if (Object.keys(selectedCategories).length === 0) {
                $('#selectedParts').html('<div class="bg-gray-100 p-4 rounded-md shadow-md"><strong>No parts selected</strong><p>Good</p></div>');
            }
        });

        // Event listener for approve button click
        $(document).on('click', '.approve-btn', function() {
    var selectedParts = $(this).siblings('select').val();
    var category = $(this).closest('.for-major-container').find('h2').text();
    if (selectedParts && selectedParts.length > 0) {
        var partsString = selectedParts.join(', ');
        var user_id = <?php echo $user_id; ?>; // Assuming $user_id is defined earlier
        var car_id = <?php echo $car_id; ?>; // Assuming $car_id is defined earlier
        var data = {
            user_id: user_id, // Include user_id in the data
            car_id: car_id, // Include car_id in the data
            category: category, // Include category in the data
            parts: selectedParts // Include selected parts in the data
        };

        // Send an AJAX request to insert the selected parts into the database
        $.ajax({
            type: "POST",
            url: "insert_selected_parts.php",
            data: data,
            success: function(response) {
                // Handle success
                console.log("Selected parts inserted successfully.");
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error("Error inserting selected parts:", error);
            }
        });

        // Append selected parts to the display
        var html = '<div class="bg-gray-100 p-4 rounded-md shadow-md">';
        html += '<strong>' + category + ':</strong> ';
        html += partsString;
        html += '</div>';
        $('#selectedParts').append(html);
    }
});

});
</script>

    <script>
        document.getElementById('validBtn').addEventListener('click', function () {
            updateValidation('valid');
        });

        document.getElementById('invalidBtn').addEventListener('click', function () {
            const comment = prompt('Enter your comment for invalidation:');
            if (comment !== null) {
                updateValidation('invalid', comment);
            }
        });

        function updateValidation(status, comment = null) {
    const user_id = <?php echo $user_id; ?>;
    const car_id = <?php echo $car_id; ?>;
    const selectedParts = getSelectedParts(); // Function to retrieve selected parts

    // Use AJAX to update validation status
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_validation.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // If update_validation.php is successful, insert selected parts
            insertSelectedParts(user_id, selectedParts, function() {
                // After inserting selected parts, redirect to homemechanic.php
                window.location.href = 'homemechanic.php';
            });
        }
    };

    const data = 'user_id=' + user_id + '&car_id=' + car_id + '&status=' + status + '&comment=' + comment;
    xhr.send(data);
}

function insertSelectedParts(user_id, selectedParts, callback) {
    // Use AJAX to insert selected parts
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'insert_selected_parts.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // If insert_selected_parts.php is successful, execute the callback function
            callback();
        }
    };

    const data = 'user_id=' + user_id + '&selected_parts=' + JSON.stringify(selectedParts);
    xhr.send(data);
}

function getSelectedParts() {
    const selectedParts = {};
    $('.select2-multiple').each(function() {
        const category = $(this).closest('.for-major-container').find('h2').text();
        const parts = $(this).val();
        if (parts && parts.length > 0) {
            selectedParts[category] = parts;
        }
    });
    return selectedParts;
}

</script>
</body>
</html>