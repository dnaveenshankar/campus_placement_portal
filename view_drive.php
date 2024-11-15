<?php
include 'db_connection.php';
session_start();

// Assuming the college_username is stored in the session
$college_username = $_SESSION['username'];

// Check if the drive_id is passed in the URL
if (isset($_GET['id'])) {
    $drive_id = $_GET['id'];

    // Fetch the drive details from the database
    $query = "SELECT * FROM drives WHERE id = '$drive_id' AND college_username = '$college_username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $drive = $result->fetch_assoc();
    } else {
        echo "Drive not found or you don't have permission to view it.";
        exit();
    }

    // Fetch students who have opted for the drive
    $students_query = "SELECT s.id, s.name, os.status FROM opted_students os
                       JOIN students s ON os.student_id = s.id
                       WHERE os.drive_id = '$drive_id'";
    $students_result = $conn->query($students_query);
} else {
    echo "No drive ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Campus Drive</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
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
            max-width: 950px;
            text-align: center;
        }
        .btn-back {
            background-color: #efa128;
            color: white;
            font-size: 1rem;
            border-radius: 10px;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #d8961e;
        }
        .footer {
            text-align: center;
            background-color: #ffffff;
            color: 082c5c;
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
        <h2>Drive Details</h2>
        <p><strong>Company Name:</strong> <?= $drive['company_name'] ?></p>
        <p><strong>Role:</strong> <?= $drive['role'] ?></p>
        <p><strong>CTC:</strong> <?= $drive['ctc'] ?></p>
        <p><strong>Date:</strong> <?= $drive['date'] ?></p>

        <h3>Opted Students</h3>
        <!-- Display the list of students who opted for this drive -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($students_result->num_rows > 0) {
                    while ($student = $students_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $student['name'] . "</td>";
                        echo "<td>" . $student['status'] . "</td>";
                        echo "<td>
                            <a href='update_status.php?student_id=" . $student['id'] . "&drive_id=$drive_id&status=Selected' class='btn btn-success'>Select</a>
                            <a href='update_status.php?student_id=" . $student['id'] . "&drive_id=$drive_id&status=Rejected' class='btn btn-danger'>Reject</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No students have opted for this drive yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Back Button -->
        <a href="drives.php" class="btn btn-secondary btn-back">Back</a>
    

    <div class="footer container">
        <img src="assets/logo.png" alt="CampusHire Logo">
        <span>CampusHire</span>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
