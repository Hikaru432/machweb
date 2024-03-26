<?php
// Include your database connection configuration file
include 'config.php';

// Check if complete status and user ID are received
if(isset($_POST['complete']) && isset($_POST['user_id'])) {
    // Escape and sanitize the received values
    $complete = mysqli_real_escape_string($conn, $_POST['complete']);
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

    // Prepare and execute the SQL query to insert the complete status along with user ID into the database
    $query = "INSERT INTO complete (complete, user_id) VALUES ('$complete', '$user_id')";
    mysqli_query($conn, $query);

    // Close database connection
    mysqli_close($conn);

    // Return a success message or any response back to the client-side
    echo "Complete status saved successfully!";
} else {
    // Return an error message or handle the case when required data is missing
    echo "Missing required data!";
}
?>
