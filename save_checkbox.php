<?php
// Include the database connection configuration file
include 'config.php';
require 'calculate_estimated_price.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if selected checkboxes are present in the POST request
    if (isset($_POST['selected_checkboxes']) && is_array($_POST['selected_checkboxes'])) {
        // Retrieve user_id and car_id from POST data
        $user_id = $_POST['user_id'];
        $car_id = $_POST['car_id'];
        
        // Call the function to insert data into the database
        saveSelectedCheckboxes($_POST['selected_checkboxes'], $_POST['quantity'], $conn, $user_id, $car_id);
        
        // Redirect back to the form page after saving
        header("Location: homemechanic.php");
        exit();
    }
}

// Function to insert selected checkboxes into the database
function saveSelectedCheckboxes($selected_checkboxes, $quantity, $conn, $user_id, $car_id) {
    // Prepare and bind the INSERT statement
    $stmt = $conn->prepare("INSERT INTO selected_checkboxes (category, checkbox_value, quantity, user_id, car_id, other_product) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $category, $checkbox_value, $quantity_value, $user_id, $car_id, $other_product);

    // Loop through each selected checkbox
    foreach ($selected_checkboxes as $category => $checkboxes) {
        // Set the category for all checkboxes in the current category
        foreach ($checkboxes as $checkbox_value) {
            // Get quantity and price for the current checkbox
            $quantity_value = $quantity[$checkbox_value];
            
            // Set parameter values and execute the statement
            $category = $conn->real_escape_string($category); // Sanitize input
            $checkbox_value = $conn->real_escape_string($checkbox_value); // Sanitize input
            $quantity_value = intval($quantity_value); // Convert to integer
            $stmt->execute();
        }
    }

    // Close statement
    $stmt->close();
}
?>