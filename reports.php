<?php
include 'db_connection.php';
session_start();

// Assuming the college_username is stored in the session
$college_username = $_SESSION['username'];

// Fetch total number of students in the college
$total_students_query = "
    SELECT COUNT(*) AS total_students 
    FROM students 
    WHERE college_username = '$college_username'";
$total_students_result = $conn->query($total_students_query);
$total_students = $total_students_result->fetch_assoc()['total_students'];

// Fetch placement statistics by company
$company_stats_query = "
    SELECT d.company_name, 
           COUNT(DISTINCT os.student_id) AS total_opted, 
           SUM(os.status = 'Selected') AS total_selected
    FROM drives d
    LEFT JOIN opted_students os ON d.id = os.drive_id
    WHERE d.college_username = '$college_username'
    GROUP BY d.company_name";
$company_stats_result = $conn->query($company_stats_query);

// Fetch the college name for footer
$college_name_query = "
    SELECT college_name 
    FROM colleges 
    WHERE username = '$college_username'";
$college_name_result = $conn->query($college_name_query);
$college_name = $college_name_result->fetch_assoc()['college_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            color: #082c5c;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-back {
            background-color: #efa128;
            color: white;
            font-size: 1rem;
            border-radius: 10px;
            text-decoration: none;
            padding: 10px 20px;
        }
        .btn-back:hover {
            background-color: #d8961e;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            background-color: #ffffff;
            color: #082c5c;
            padding: 15px;
            border-radius: 10px;
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
        <h2 class="text-center mb-4">Placement Reports</h2>
        
        <div class="mb-4">
            <h4>Overall Statistics</h4>
            <p><strong>Total Students:</strong> <?= $total_students ?></p>
        </div>
        
        <h4>Company-Wise Statistics</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Company Name</th>
                    <th>Students Opted</th>
                    <th>Students Selected</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($company_stats_result->num_rows > 0) {
                    while ($row = $company_stats_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['company_name'] . "</td>";
                        echo "<td>" . $row['total_opted'] . "</td>";
                        echo "<td>" . $row['total_selected'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Back Button -->
        <a href="college_dashboard.php" class="btn btn-secondary btn-back">Back</a>
        
        <!-- Footer -->
        <div class="footer container">
            <img src="assets/logo.png" alt="CampusHire Logo">
            <span>CampusHire - <?= $college_name ?></span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
