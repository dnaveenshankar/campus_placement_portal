<?php
include 'db_connection.php';
session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the student ID from the URL
$student_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$student_id) {
    echo "<script>alert('No student ID provided!'); window.location.href = 'student_list.php';</script>";
    exit();
}

// Fetch student data
$student_query = "SELECT * FROM students WHERE id = $student_id";
$student_result = $conn->query($student_query);
$student = $student_result->fetch_assoc();

// Get the college name based on the student's college_username
$college_username = $student['college_username'];
$college_query = "SELECT college_name FROM colleges WHERE username = '$college_username'";
$college_result = $conn->query($college_query);
$college = $college_result->fetch_assoc();
$college_name = $college['college_name'];

// If no student found, redirect
if (!$student) {
    echo "<script>alert('Student not found!'); window.location.href = 'student_list.php';</script>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #082c5c;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            max-width: 1000px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #082c5c;
        }
        .footer.container {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            color: #082c5c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 50px;
            text-align: center;
        }

        .footer.container img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Back Button -->
    <a href="student_list.php" class="btn btn-primary mb-3">Back to Student List</a>

    <h2>Student Details</h2>

    <!-- Display Student Image -->
    <img src="<?= $student['image'] ?: 'assets/student-default.png' ?>" alt="Student Image" class="profile-img">

    <!-- Student Data Table -->
    <table class="table table-bordered">
        <tr>
            <th>Username</th>
            <td><?= $student['username'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?= $student['name'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Department Code</th>
            <td><?= $student['department_code'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Profile Status</th>
            <td><?= $student['profile_status'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Marks (10th)</th>
            <td><?= $student['marks_10'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Marks (12th)</th>
            <td><?= $student['marks_12'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Marks (UG)</th>
            <td><?= $student['marks_ug'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Marks (PG)</th>
            <td><?= $student['marks_pg'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Backlog History</th>
            <td><?= $student['backlog_history'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Current Backlogs</th>
            <td><?= $student['current_backlogs'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?= $student['dob'] ? date('d-m-Y', strtotime($student['dob'])) : 'N/A' ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?= $student['gender'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= $student['email'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= $student['phone'] ?: 'N/A' ?></td>
        </tr>
        <tr>
            <th>Resume</th>
            <td>
                <?php if ($student['resume']): ?>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#resumeModal">View Resume</button>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Resume Modal -->
    <?php if ($student['resume']): ?>
        <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumeModalLabel">Student Resume</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <embed src="<?= $student['resume'] ?>" type="application/pdf" width="100%" height="600px">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<!-- Footer -->
<div class="footer container">
    <img src="assets/logo.png" alt="CampusHire Logo">
    <span>CampusHire - <?= $college_name ?: 'N/A' ?></span>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
