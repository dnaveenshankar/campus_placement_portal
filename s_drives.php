<?php
include 'db_connection.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to access the drives!'); window.location.href = 'login.php';</script>";
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

// Fetch all available drives for the college sorted by date
$sql_drives = "SELECT * FROM drives WHERE status = 'open' AND college_username = '" . $student['college_username'] . "' ORDER BY date DESC";
$drives_result = $conn->query($sql_drives);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Drives - CampusHire</title>
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
        .drive-card {
            margin-bottom: 20px;
        }
        .btn-optin {
            background-color: #28a745;
            color: white;
        }
        .btn-optin:hover {
            background-color: #218838;
        }
        .btn-view {
            background-color: #007bff;
            color: white;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
        .btn-not-eligible {
            background-color: #dc3545;
            color: white;
        }
        .btn-not-eligible:hover {
            background-color: #c82333;
        }
        .footer {
            text-align: center;
            background-color: #ffffff;
            color: #082c5c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Available Job Drives</h2>

    <?php
    if ($drives_result->num_rows > 0) {
        while ($drive = $drives_result->fetch_assoc()) {
            // Display the drive details
            ?>
            <div class="card drive-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="<?= htmlspecialchars($drive['logo']) ?>" alt="Company Logo" width="50" height="50" class="me-3">
                        <h5 class="card-title"><?= htmlspecialchars($drive['company_name']) ?> - <?= htmlspecialchars($drive['role']) ?></h5>
                    </div>
                    <p class="card-text"><?= htmlspecialchars($drive['job_description']) ?></p>
                    <div class="d-flex justify-content-between">
                        <a href="s_view_drive.php?drive_id=<?= $drive['id'] ?>" class="btn btn-view">View</a>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No open job drives available.</p>";
    }
    ?>

    <div class="mt-3">
        <a href="student_dashboard.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
