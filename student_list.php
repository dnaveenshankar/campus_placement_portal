<?php
include 'db_connection.php';
session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch college ID and departments
$username = $_SESSION['username'];
$college_query = "SELECT id, username AS college_username FROM colleges WHERE username = '$username'";
$college_result = $conn->query($college_query);
$college = $college_result->fetch_assoc();
$college_id = $college['id'];
$college_username = $college['college_username']; 

// Fetch students belonging to this college
$students_query = "SELECT * FROM students WHERE college_username = '$college_username' ORDER BY department_code ASC, username ASC";
$students_result = $conn->query($students_query);

// Handle deleting a student
if (isset($_GET['delete_student_id'])) {
    $student_id = $_GET['delete_student_id'];
    $delete_query = "DELETE FROM students WHERE id = $student_id";
    if ($conn->query($delete_query) === TRUE) {
        header("Location: student_list.php"); 
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle approval status change
if (isset($_GET['toggle_status_id'])) {
    $student_id = $_GET['toggle_status_id'];
    $toggle_query = "SELECT profile_status FROM students WHERE id = $student_id";
    $status_result = $conn->query($toggle_query);
    $status = $status_result->fetch_assoc()['profile_status'];
    
    // Toggle status between 'Approved' and 'Not Approved'
    $new_status = ($status == 'Approved') ? 'Not Approved' : 'Approved';
    
    $update_status_query = "UPDATE students SET profile_status = '$new_status' WHERE id = $student_id";
    if ($conn->query($update_status_query) === TRUE) {
        header("Location: student_list.php"); // Refresh the page after status change
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="path/to/your/dashboard.css" rel="stylesheet">
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
<div class="container mt-5">
    <h2>Manage Students</h2>

    <!-- Success/Error Messages -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Back Button -->
    <a href="college_dashboard.php" class="btn btn-secondary mb-3">Back</a>

    <!-- Students Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Department Code</th>
                <th>Name</th>
                <th>Username</th>
                <th>Completion</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $students_result->fetch_assoc()): ?>
                <?php
                // Calculate completion percentage based on fields in the 'students' table
                $fields_filled = 0;
                $total_fields = 12; // Total fields to check (name, username, department, marks_10, marks_12, marks_ug, marks_pg, backlog_history, current_backlogs, dob, gender, email)

                if (!empty($student['name'])) $fields_filled++;
                if (!empty($student['username'])) $fields_filled++;
                if (!empty($student['department_code'])) $fields_filled++;
                if (!empty($student['marks_10'])) $fields_filled++;
                if (!empty($student['marks_12'])) $fields_filled++;
                if (!empty($student['marks_ug'])) $fields_filled++;
                if (!empty($student['marks_pg'])) $fields_filled++;
                if (!empty($student['backlog_history'])) $fields_filled++;
                if (!empty($student['current_backlogs'])) $fields_filled++;
                if (!empty($student['dob'])) $fields_filled++;
                if (!empty($student['gender'])) $fields_filled++;
                if (!empty($student['email'])) $fields_filled++;

                // Calculate the completion percentage
                $completion_percentage = ($fields_filled / $total_fields) * 100;
                ?>
                <tr>
                    <td><?= $student['department_code'] ?></td>
                    <td><?= $student['name'] ?: 'N/A' ?></td>
                    <td><?= $student['username'] ?></td>
                    <td><?= round($completion_percentage) ?>%</td>
                    <td><?= $student['profile_status'] ?></td>
                    <td>
                        <?php if ($student['profile_status'] == 'Not Approved'): ?>
                            <a href="?toggle_status_id=<?= $student['id'] ?>" class="btn btn-warning btn-sm">Approve</a>
                        <?php else: ?>
                            <a href="?toggle_status_id=<?= $student['id'] ?>" class="btn btn-danger btn-sm">Not Approve</a>
                        <?php endif; ?>
                        <a href="view_student.php?id=<?= $student['id'] ?>" class="btn btn-info btn-sm">View</a>
                        <a href="?delete_student_id=<?= $student['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Add Student Button -->
    <a href="add_student.php" class="btn btn-primary mb-3">Add Student</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
