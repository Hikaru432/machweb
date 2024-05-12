<?php
// Start the session
session_start();

// Include database connection or any necessary files
include 'config.php';

// Check if car ID is provided via POST
if(isset($_POST['car_id'])) {
    $car_id = $_POST['car_id'];
    
    // Fetch checkboxes associated with the selected car ID and user ID
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Prepare and execute query to fetch checkboxes
    $query = "SELECT * FROM selected_checkboxes WHERE car_id = $car_id AND user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if($result) {
        // If checkboxes are found, display them
        if(mysqli_num_rows($result) > 0) {
            echo '<ul>';
            while($row = mysqli_fetch_assoc($result)) {
                // Display checkbox information as list items
                echo '<li>' . $row['checkbox_value'] . ' - Quantity: ' . $row['quantity'] . '</li>';
            }
            echo '</ul>';
        } else {
            // If no checkboxes are found, display a message
            echo '<p>No checkboxes found for the selected car.</p>';
        }
    } else {
        // If there's an error with the query, display an error message
        echo 'Error: ' . mysqli_error($conn);
    }
} else {
    // If car ID is not provided, display an error message
    echo 'Error: Car ID not provided.';
}
?>
