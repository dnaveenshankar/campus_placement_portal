<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to apply for the drive!'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the drive_id from the POST request
if (!isset($_POST['drive_id'])) {
    echo "<script>alert('Invalid drive ID'); window.location.href = 's_view_drive.php';</script>";
    exit();
}

$drive_id = $_POST['drive_id'];
$username = $_SESSION['username'];

// Fetch student details and college username
$query = "SELECT s.*, c.username AS college_username FROM students s
          JOIN colleges c ON s.college_username = c.username
          WHERE s.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the college username from the student record
$college_username = $student['college_username'];

// Check if the student has already applied for the drive
$query = "SELECT * FROM opted_students WHERE student_id = ? AND drive_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $student['id'], $drive_id);
$stmt->execute();
$opted_result = $stmt->get_result();
if ($opted_result->num_rows > 0) {
    echo "<script>alert('You have already applied for this drive!'); window.location.href = 's_view_drive.php';</script>";
    exit();
}

// Insert application into the opted_students table
$query = "INSERT INTO opted_students (college_username, drive_id, student_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('sii', $college_username, $drive_id, $student['id']);
if ($stmt->execute()) {
    echo "<script>alert('Successfully applied for the drive!'); window.location.href = 's_view_drive.php';</script>";
} else {
    echo "<script>alert('Failed to apply for the drive. Please try again later.'); window.location.href = 's_view_drive.php';</script>";
}
?>
