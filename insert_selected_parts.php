<?php
include 'config.php';

// Function to insert selected parts into the database
function insertSelectedParts($conn, $user_id, $car_id, $selectedCategories) {
    foreach ($selectedCategories as $category => $parts) {
        foreach ($parts as $part) {
            $sql = "INSERT INTO selected_parts (user_id, car_id, category, part) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiss", $user_id, $car_id, $category, $part);
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
            }
        }
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $user_id = $_POST['user_id']; 
    $car_id = $_POST['car_id']; 
    $selectedCategories = json_decode($_POST['selected_parts'], true); 

    // Insert selected parts into the database
    insertSelectedParts($conn, $user_id, $car_id, $selectedCategories);
}
?>

