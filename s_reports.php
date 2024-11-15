<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to view the report!'); window.location.href = 'login.php';</script>";
    exit();
}

$username = $_SESSION['username'];

// Query to get the student's report, including the company opted and status
$query = "SELECT d.company_name, d.role, os.status AS application_status 
          FROM opted_students os 
          JOIN drives d ON os.drive_id = d.id 
          JOIN students s ON os.student_id = s.id 
          WHERE s.username = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report</title>
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
        .btn-option {
            background-color: #efa128;
            border: none;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 150px;
            margin-top: 20px;
        }
        .btn-option:hover {
            background-color: #d8961e;
        }
        .status-symbol {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: green;
        }
        .status-symbol.x {
            background-color: red;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Report</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td>
                                <?php 
                                    $status = htmlspecialchars($row['application_status']);
                                    echo ($status == 'Selected') 
                                        ? "<span class='status-symbol'></span> $status"
                                        : "<span class='status-symbol x'></span> $status";
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found for this student.</p>
        <?php endif; ?>
        
        <a href="student_dashboard.php" class="back-btn">Back</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
