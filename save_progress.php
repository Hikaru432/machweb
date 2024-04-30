<?php
session_start();
include 'config.php';

if (isset($_POST['user_id']) && isset($_POST['car_id']) && isset($_POST['progress'])) {
    $userId = $_POST['user_id'];
    $carId = $_POST['car_id'];
    $progress = $_POST['progress'];

    // Check if progress data already exists for the user and car
    $checkQuery = "SELECT * FROM progress WHERE user_id = ? AND car_id = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $carId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Update progress if data already exists
        $updateQuery = "UPDATE progress SET progress_percentage = ? WHERE user_id = ? AND car_id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, 'sii', $progress, $userId, $carId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Progress updated successfully.";
        } else {
            echo "Error updating progress.";
        }
    } else {
        // Insert progress if data doesn't exist
        $insertQuery = "INSERT INTO progress (user_id, car_id, progress_percentage) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, 'iis', $userId, $carId, $progress);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Progress saved successfully.";
        } else {
            echo "Error saving progress.";
        }
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Incomplete data received.";
}
?>
