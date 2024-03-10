<?php
// save_parts.php

// Assuming you have a database connection established
include 'config.php';

// Check if parts and user_id are provided
if(isset($_POST['parts']) && isset($_POST['user_id']) && isset($_POST['car_id'])) {
    $parts = $_POST['parts'];
    $user_id = $_POST['user_id'];
    $car_id = $_POST['car_id'];

    // Prepare and execute a query to save selected parts to the database
    $query = "INSERT INTO repair (user_id, plateno, problem, diagnosis) VALUES ";
    $values = [];
    foreach($parts as $part_id) {
        // Assuming 'problem' and 'diagnosis' need to be replaced with actual values
        $values[] = "('$user_id', '$car_id', 'problem', 'diagnosis')";
    }
    $query .= implode(', ', $values);

    if(mysqli_query($conn, $query)) {
        // If parts are successfully saved, return success response
        echo json_encode(array('success' => 'Selected parts saved successfully'));
        exit;
    } else {
        // If an error occurs during the query execution, return error response
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array('error' => 'Error saving selected parts'));
        exit;
    }
} else {
    // If parts or user_id is not provided, return an error response
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(array('error' => 'Parts and user ID are required'));
    exit;
}
?>
