<?php
include 'db_connection.php';
session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

// Get department ID from URL
$department_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$department_id) {
    echo "<script>alert('No department ID provided!'); window.location.href = 'departments.php';</script>";
    exit();
}

// Fetch department details
$department_query = "SELECT * FROM departments WHERE id = $department_id";
$department_result = $conn->query($department_query);
$department = $department_result->fetch_assoc();

if (!$department) {
    echo "<script>alert('Department not found!'); window.location.href = 'departments.php';</script>";
    exit();
}

// Fetch the college name associated with this department
$college_query = "SELECT college_name FROM colleges WHERE id = " . $department['college_id'];
$college_result = $conn->query($college_query);
$college = $college_result->fetch_assoc();
$college_name = $college['college_name'];

// Fetch all students for this department
$students_query = "SELECT * FROM students WHERE department_code = '" . $department['dept_code'] . "'";
$students_result = $conn->query($students_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Department - <?= $department['dept_name'] ?></title>
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
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Back Button -->
    <a href="departments.php" class="btn btn-primary mb-3">Back to Department List</a>

    <h2>Department Details: <?= $department['dept_name'] ?></h2>

    <!-- Department Info -->
    <p><strong>Department Code:</strong> <?= $department['dept_code'] ?></p>
    <p><strong>Stream:</strong> <?= $department['stream'] ?></p>

    <h3>Students in this Department</h3>

    <!-- Students Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Profile Status</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students_result->num_rows > 0): ?>
                <?php while ($student = $students_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $student['username'] ?></td>
                        <td><?= $student['name'] ?: 'N/A' ?></td>
                        <td><?= $student['profile_status'] ?: 'N/A' ?></td>
                        <td><?= $student['email'] ?: 'N/A' ?></td>
                        <td><?= $student['phone'] ?: 'N/A' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found in this department.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


<!-- Footer with College Name -->
<div class="footer container">
    <img src="assets/logo.png" alt="CampusHire Logo">
    <span>CampusHire - <?= $college_name ?></span>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
