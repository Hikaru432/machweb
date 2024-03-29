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
    ),
    'Battery' => array (
        'Battery age' => ['Battery Replacement', 'Battery Load Test', 'Charging System Inspection', 'Battery Tender or Trickle Charger'],
        'Overcharging or Undercharging' => ['Alternator Inspection', 'Voltage Regulator Replacement','Charging System Voltage Test', 'Battery Load Test', 'Electrical System Inspection', 'Battery Replacement'],
        'Corrosion' => ['Terminal Cleaning', 'Terminal Protectors', 'Battery Terminal Replacements', 'Sealant or Dielectric Grease', 'Regular Maintenance', 'Environmental Protection']
    ),
    'Light' => array (
        'Faulty Bulbs' => ['Bulb Socket Cleaning', 'Bulb Replacement', 'LED Conversion Kits', 'Halogen/HID Bulb Replacement'],
        'Electrical Wiring Problems' => ['Grounding Point Inspection', 'Wire Harness Repair Kit', 'Voltage Meter/Test Light', 'Wire Connectors/Terminals'],
        'Switch Malfunction' => ['Steering Column Covers', 'OEM Replacement Switches', 'Aftermarket Switches', 'Steering Column Switch Assembly'],
    ),
    'Oil' => array (
        'Oil Leaks' => ['Gasket Replacement', 'Seal Replacement', 'Oil Pan Replacement', 'Oil Filter Housing Gasket Replacement', 'Oil Cooler Replacement'],
        'Oil Consumption' => ['PCV Valve Replacement', 'Valve Stem Seal Replacement', 'Engine Overhaul/Rebuild', 'Oil Consumption Additives'],
        'Oil Contamination' => ['Oil Change', 'Oil Flush', 'Air Filter Replacement', 'Coolant System Inspection'],
    ),
    'Water' => array (
        'Coolant Leaks' => ['Radiator Hose Replacement', 'Radiator Cap Replacement', 'Radiator Replacement', 'Water Pump Replacement'],
        'Coolant Loss' => ['Coolant Reservoir Cap Replacement', 'Coolant Reservoir Replacement', 'Coolant System Pressure Test', 'Head Gasket Replacement'],
        'Coolant Contamination' => ['Flush and Refill Coolant', 'Thermostat Replacement', 'Cylinder Head Inspection', 'Heater Core Replacement'],
    ),
    'Brake' => array (
        'Brake Fluid Leaks' =>  ['Brake Hose Replacement', 'Brake Line Replacement', 'Brake Caliper Rebuild/Replacement'],
        'Brake Pad Wear' => ['Brake Pad Replacement', 'Brake Rotor Resurfacing/Replacement', 'Brake Caliper Inspection'],
        'Brake Fluid Contamination' => ['Brake Fluid Flush', 'Brake Master Cylinder Replacement', 'Brake Bleeding'],
    ),
    'Air' => array (
        'Air Filter Clogging' => ['Air Filter Replacement', 'Air Intake Hose Replacement', 'Mass Air Flow Sensor (MAF) Cleaning/Replacement'],
        'Vacuum Leaks' => ['Vacuum Hose Replacement', 'Intake Manifold Gasket Replacement', 'Throttle Body Gasket Replacement'],
        'Fuel System Issues' => ['Fuel Filter Replacement', 'Fuel Pump Replacement', 'Fuel Injector Cleaning/Replacement'],
    ),
    'Gas' => array (
        'Fuel System Leaks' => ['Fuel Line Replacement', 'Fuel Tank Replacement', 'Fuel Injector O-Ring Replacement'],
        'Fuel Pump Failure' => ['Fuel Pump Replacement', 'Fuel Pump Relay Replacement', 'Fuel Filter Replacement'],
        'Fuel Evaporation and Vapor Management' => ['Fuel Cap Replacement', 'Evaporative Emissions (EVAP) System Inspection', 'Vapor Canister Purge Solenoid Replacement'],
    ),
    'Tire' => array (
        'Tire Wear' => ['Tire Replacement', 'Wheel Alignment', 'Tire Rotation'],
        'Tire Punctures or Damage' => ['Tire Patch or Plug', 'Tire Repair Kit', 'Spare Tire Installation'],
        'Underinflation or Overinflation' => ['Tire Pressure Monitoring System', '(TPMS) Sensor Replacement', 'Tire Pressure Gaugem', 'Tire Inflation Tools'],
    )
);

function displayProblemParts($problem_parts_mapping, $user_id, $car_id)
{
    echo '<form method="post" action="save_checkbox.php">'; 
    echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
    echo '<input type="hidden" name="car_id" value="' . $car_id . '">';
    foreach ($problem_parts_mapping as $problem => $parts) {
        echo '<div class="for-major-container bg-gray-100 p-4 rounded-md shadow-md mb-4">';
        echo '<h2 class="text-2xl font-bold mb-4">' . $problem . '</h2>';
        echo '<div class="grid grid-cols-3 gap-4">';
        foreach ($parts as $part => $subparts) {
            echo '<div>'; // Start a new grid item
            echo '<strong style="margin-bottom: 10px;">' . $part . '</strong>';
            foreach ($subparts as $subpart) {
                echo '<div>'; // Start a new row for checkboxes
                // Include the category in the name attribute for uniqueness
                echo '<input type="checkbox" id="' . str_replace(' ', '_', $subpart) . '" name="selected_checkboxes[' . $problem . '][]" value="' . $subpart . '">';
                echo '<label for="' . str_replace(' ', '_', $subpart) . '">' . $subpart . '</label>';
                echo '</div>'; // End row
            }
            echo '</div>'; // End grid item
        }
        echo '</div>';
        echo '</div>';
    }
    echo '<div class="mt-4">';
    echo '<button type="submit" name="submit" class="btn btn-primary">Valid</button>';
    echo '<a href="homemechanic.php" class="btn btn-danger">Invalid</a>';
    echo '</div>';
    echo '</form>'; 
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

         <center><hr style="width: 1200px; ; border: 3px solid black;"></center>
         <br>

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

<br>
<br>
<center><hr style="width: 1200px; ; border: 3px solid black;"></center>
<br>
<div>
    <h3 style="padding-left: 20px; font-weight: 700;">Parts</h3>
    <br>
<?php
    // Call the function to display checkboxes
    displayProblemParts($problem_parts_mapping, $user_id, $car_id);

?>
</div>



    
</body>
</html>