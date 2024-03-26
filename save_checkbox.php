<?php
// Include the database connection configuration file
include 'config.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Check if selected checkboxes are present in the POST request
    if (isset($_POST['selected_checkboxes']) && is_array($_POST['selected_checkboxes'])) {
        // Retrieve user_id and car_id from POST data
        $user_id = $_POST['user_id'];
        $car_id = $_POST['car_id'];
        
        // Call the function to insert data into the database
        saveSelectedCheckboxes($_POST['selected_checkboxes'], $conn, $user_id, $car_id);
        
        // Redirect back to the form page after saving
        header("Location: homemechanic.php");
        exit();
    }
}

// Function to insert selected checkboxes into the database
function saveSelectedCheckboxes($selected_checkboxes, $conn, $user_id, $car_id) {
    // Prepare and bind the INSERT statement
    $stmt = $conn->prepare("INSERT INTO selected_checkboxes (category, checkbox_value, user_id, car_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $category, $checkbox_value, $user_id, $car_id);

    // Loop through each selected checkbox
    foreach ($selected_checkboxes as $category => $checkboxes) {
        // Set the category for all checkboxes in the current category
        foreach ($checkboxes as $checkbox_value) {
            // Set parameter values and execute the statement
            $category = $conn->real_escape_string($category); // Sanitize input
            $checkbox_value = $conn->real_escape_string($checkbox_value); // Sanitize input
            $stmt->execute();
        }
    }

    // Close statement
    $stmt->close();
}
?>