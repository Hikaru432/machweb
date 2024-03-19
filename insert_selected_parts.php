<?php
// Assuming you have established a database connection and identified the currently logged-in user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $selected_parts = json_decode($_POST['selected_parts'], true);
    // Ensure that the user_id matches the currently logged-in user or handle authentication as needed

    $mechanical_issues = $_POST['mechanical_issues'] ?? null;
    $fuel_and_air_intake_system = $_POST['fuel_and_air_intake_system'] ?? null;
    $cooling_and_lubrication = $_POST['cooling_and_lubrication'] ?? null;
    $battery = $_POST['battery'] ?? null;
    $light = $_POST['light'] ?? null;
    $oil = $_POST['oil'] ?? null;
    $water = $_POST['water'] ?? null;
    $brake = $_POST['brake'] ?? null;
    $air = $_POST['air'] ?? null;
    $gas = $_POST['gas'] ?? null;
    $tire = $_POST['tire'] ?? null;

    // Prepare and execute the SQL statement to insert data into the userpart table
    $stmt = $conn->prepare("INSERT INTO userpart (user_id, mechanical_issues, fuel_and_air_intake_system, cooling_and_lubrication, battery, light, oil, water, brake, air, gas, tire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssss", $user_id, $mechanical_issues, $fuel_and_air_intake_system, $cooling_and_lubrication, $battery, $light, $oil, $water, $brake, $air, $gas, $tire);
    $stmt->execute();

    foreach ($selected_parts as $category => $parts) {
        foreach ($parts as $part) {
            $stmt->bind_param("iss", $user_id, $category, $part);
            if (!$stmt->execute()) {
                die("Error: " . $stmt->error); // Check for SQL errors
            }
        }
    }

    if ($stmt->affected_rows > 0) {
        echo "Selected parts inserted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
