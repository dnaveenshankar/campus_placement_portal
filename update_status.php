<?php
// Start session and include database connection
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to update the status!'); window.location.href = 'login.php';</script>";
    exit();
}

// Check if the required parameters are provided
if (isset($_GET['student_id'], $_GET['drive_id'], $_GET['status'])) {
    $student_id = (int) $_GET['student_id'];
    $drive_id = (int) $_GET['drive_id'];
    $status = $_GET['status'];

    // Validate status value
    if ($status !== 'Selected' && $status !== 'Rejected') {
        echo "<script>alert('Invalid status!'); window.location.href = 'drives.php';</script>";
        exit();
    }

    // Update the status in the opted_students table
    $query = "UPDATE opted_students SET status = ? WHERE student_id = ? AND drive_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sii', $status, $student_id, $drive_id);

    if ($stmt->execute()) {
        // Redirect to view_drive.php after successful update
        echo "<script>alert('Application status updated to $status!'); window.location.href = 'drives.php';</script>";
    } else {
        echo "<script>alert('Failed to update the status. Please try again!'); window.location.href = 'drives.php';</script>";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "<script>alert('Missing parameters!'); window.location.href = 'drives.php';</script>";
}

// Close the database connection
$conn->close();
?>
