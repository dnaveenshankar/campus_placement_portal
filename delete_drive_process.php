<?php
include 'db_connection.php';
session_start();

// Check if the drive_id is passed via GET
if (isset($_GET['drive_id'])) {
    $drive_id = $_GET['drive_id'];

    // Query to delete the drive
    $query = "DELETE FROM drives WHERE id = '$drive_id'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // If successful, redirect to drives.php
        header("Location: drives.php?message=Drive deleted successfully.");
        exit();
    } else {
        // If an error occurs, display an error message
        echo "Error deleting drive: " . mysqli_error($conn);
    }
} else {
    // If no drive_id is provided, redirect to drives.php with an error message
    header("Location: drives.php?message=Invalid drive ID.");
    exit();
}
?>
