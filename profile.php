<?php
include 'db_connection.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to access the profile!'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch student details
$username = $_SESSION['username'];
$sql = "SELECT * FROM students WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Invalid user. Please log in again!'); window.location.href = 'login.php';</script>";
    exit();
}

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = $_POST['name'];
    $marks_10 = $_POST['marks_10'];
    $marks_12 = $_POST['marks_12'];
    $marks_ug = $_POST['marks_ug'];
    $marks_pg = $_POST['marks_pg'];
    $backlog_history = $_POST['backlog_history'];
    $current_backlogs = $_POST['current_backlogs'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Handle image upload
    $image = $student['image']; // Default to current image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Handle resume upload
    $resume = $student['resume']; // Default to current resume
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume = 'uploads/' . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume);
    }

    $sql_update = "UPDATE students SET 
                    name = '$name',
                    marks_10 = '$marks_10',
                    marks_12 = '$marks_12',
                    marks_ug = '$marks_ug',
                    marks_pg = '$marks_pg',
                    backlog_history = '$backlog_history',
                    current_backlogs = '$current_backlogs',
                    dob = '$dob',
                    gender = '$gender',
                    email = '$email',
                    phone = '$phone',
                    image = '$image',
                    resume = '$resume'
                    WHERE username = '$username'";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - CampusHire</title>
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
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            max-width: 850px;
        }
        .profile-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }
        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .profile-header h2 {
            margin: 0;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-update {
            background-color: #28a745;
            color: white;
        }
        .btn-update:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-header">
        <img src="<?= htmlspecialchars($student['image']) ?>" alt="Student Image">
        <h2><?= htmlspecialchars($student['name']) ?>'s Profile</h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <!-- Name -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
        </div>

        <!-- Marks -->
        <div class="form-group">
            <label for="marks_10">10th Marks</label>
            <input type="number" class="form-control" id="marks_10" name="marks_10" value="<?= htmlspecialchars($student['marks_10']) ?>" required>
        </div>
        <div class="form-group">
            <label for="marks_12">12th Marks</label>
            <input type="number" class="form-control" id="marks_12" name="marks_12" value="<?= htmlspecialchars($student['marks_12']) ?>" required>
        </div>
        <div class="form-group">
            <label for="marks_ug">UG Marks</label>
            <input type="number" class="form-control" id="marks_ug" name="marks_ug" value="<?= htmlspecialchars($student['marks_ug']) ?>" required>
        </div>
        <div class="form-group">
            <label for="marks_pg">PG Marks</label>
            <input type="number" class="form-control" id="marks_pg" name="marks_pg" value="<?= htmlspecialchars($student['marks_pg']) ?>" required>
        </div>

        <!-- Backlog History -->
        <div class="form-group">
            <label for="backlog_history">Backlog History</label>
            <textarea class="form-control" id="backlog_history" name="backlog_history" rows="4"><?= htmlspecialchars($student['backlog_history']) ?></textarea>
        </div>

        <!-- Current Backlogs -->
        <div class="form-group">
            <label for="current_backlogs">Current Backlogs</label>
            <textarea class="form-control" id="current_backlogs" name="current_backlogs" rows="4"><?= htmlspecialchars($student['current_backlogs']) ?></textarea>
        </div>

        <!-- Date of Birth -->
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" required>
        </div>

        <!-- Gender -->
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>
        </div>

        <!-- Profile Image -->
        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <!-- Resume -->
        <div class="form-group">
            <label for="resume">Resume</label>
            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.docx,.doc">
            <?php if ($student['resume'] != 'assets/student-default.png'): ?>
                <p>Current Resume: <a href="<?= $student['resume'] ?>" target="_blank">View Resume</a></p>
            <?php endif; ?>
        </div>

        <button type="submit" name="update" class="btn btn-update">Update Profile</button>
    </form>
    <div class="text-center mb-4">
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
