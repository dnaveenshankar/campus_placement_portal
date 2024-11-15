<?php
include 'db_connection.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to access the dashboard!'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch student details
$username = $_SESSION['username'];
$sql = "SELECT s.name, s.image, c.college_name 
        FROM students s 
        INNER JOIN colleges c 
        ON s.college_username = c.username 
        WHERE s.username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $student_name = $student['name'];
    $student_image = $student['image'] ? $student['image'] : 'assets/student-default.png'; // Default image if not uploaded
    $college_name = $student['college_name']; // Fetch the college name
} else {
    echo "<script>alert('Invalid user. Please log in again!'); window.location.href = 'login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Handle logout action
    session_destroy();
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - CampusHire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #082c5c;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .navbar {
            background-color: #ffffff;
            padding: 15px;
            color: #082c5c;
            border-radius: 10px;
        }
        .navbar .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .navbar .btn-logout {
            background-color: #e74c3c;
            color: white;
            font-size: 1.5rem;
            border: none;
            border-radius: 50%;
            padding: 10px 20px;
        }
        .navbar .btn-logout:hover {
            background-color: #c0392b;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            max-width: 850px;
            text-align: center;
            align-content: center;
        }
        .dashboard-btns {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
            justify-items: center;
        }
        .btn-option {
            background-color: #efa128;
            border: none;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
            width: 150px;
        }
        .btn-option:hover {
            background-color: #d8961e;
        }
        .footer {
            text-align: center;
            background-color: #ffffff;
            color: #082c5c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 50px;
        }
        .footer img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="<?= htmlspecialchars($student_image) ?>" alt="Student Profile">
                <span><?= htmlspecialchars($student_name) ?></span>
            </a>
            <button class="btn btn-logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-power"></i>
            </button>
        </div>
    </nav>

    <!-- Dashboard Options -->
    <div class="dashboard-btns">
        <button class="btn btn-option" onclick="window.location.href='profile.php'">Profile</button>
        <button class="btn btn-option" onclick="window.location.href='s_drives.php'">Drives</button>
        <button class="btn btn-option" onclick="window.location.href='s_reports.php'">Reports</button>
        <button class="btn btn-option" onclick="window.location.href='college.php'">College</button>
    </div>
    
    <!-- Footer with CampusHire logo -->
    <div class="footer container">
    <img src="assets/logo.png" alt="CampusHire Logo">
    <span>CampusHire - <?= htmlspecialchars($college_name) ?></span>
</div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Are you sure you want to log out?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <form method="POST" action="">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="logout">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
