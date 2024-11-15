<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to access the drives!'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the drive_id from the URL
if (!isset($_GET['drive_id'])) {
    echo "<script>alert('Invalid drive ID'); window.location.href = 's_drives.php';</script>";
    exit();
}

$drive_id = $_GET['drive_id'];  // Get the drive ID from the URL
$username = $_SESSION['username'];

// Fetch drive details from the database
$query = "SELECT * FROM drives WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $drive_id);
$stmt->execute();
$drive_result = $stmt->get_result();
if ($drive_result->num_rows == 0) {
    echo "<script>alert('No drive details found'); window.location.href = 's_drives.php';</script>";
    exit();
}
$drive = $drive_result->fetch_assoc(); // Fetch drive details

// Fetch eligibility criteria for the drive
$query = "SELECT * FROM eligibility WHERE drive_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $drive_id);
$stmt->execute();
$eligibility_result = $stmt->get_result();
$eligibility = $eligibility_result->fetch_assoc(); // Fetch eligibility criteria

// Fetch student's details
$query = "SELECT * FROM students WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found'); window.location.href = 'login.php';</script>";
    exit();
}

// Check if the student is already opted for the drive
$query = "SELECT * FROM opted_students WHERE student_id = ? AND drive_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $student['id'], $drive_id);
$stmt->execute();
$opted_result = $stmt->get_result();
$is_opted = $opted_result->num_rows > 0;

// Eligibility check
$eligible = true;
$eligible_criteria_count = 0;
$total_criteria = 7;  // There are 7 eligibility criteria (Department, Marks 10th, Marks 12th, Marks UG, Marks PG, Backlog History, Gender)

// Check if the student meets each eligibility criterion
$eligible_department = in_array($student['department_code'], explode(",", $eligibility['department_code']));
$eligible_backlog = $student['backlog_history'] <= $eligibility['backlog_history'];

if ($eligible_department) {
    $eligible_criteria_count++;
}
if ($eligible_backlog) {
    $eligible_criteria_count++;
}

$eligible_marks_10 = $student['marks_10'] >= $eligibility['marks_10'];
if ($eligible_marks_10) {
    $eligible_criteria_count++;
}

$eligible_marks_12 = $student['marks_12'] >= $eligibility['marks_12'];
if ($eligible_marks_12) {
    $eligible_criteria_count++;
}

$eligible_marks_ug = $student['marks_ug'] >= $eligibility['marks_ug'];
if ($eligible_marks_ug) {
    $eligible_criteria_count++;
}

$eligible_marks_pg = $student['marks_pg'] >= $eligibility['marks_pg'];
if ($eligible_marks_pg) {
    $eligible_criteria_count++;
}

$eligible_gender = ($eligibility['eligible_gender'] == 'Both' || $student['gender'] == $eligibility['eligible_gender']);
if ($eligible_gender) {
    $eligible_criteria_count++;
}

// Calculate match percentage
$match_percentage = ($eligible_criteria_count / $total_criteria) * 100;

// Check if the student is eligible to apply based on match percentage
$eligible = $match_percentage == 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Drive</title>
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
        <h2 class="text-center"><?php echo $drive['company_name']; ?> - Drive Details</h2>
        <div class="text-center">
            <img src="<?php echo $drive['logo']; ?>" alt="Company Logo" width="100">
        </div>
        <h4>Role: <?php echo $drive['role']; ?></h4>
        <p><strong>Description:</strong> <?php echo $drive['description']; ?></p>
        <p><strong>CTC:</strong> ₹<?php echo number_format($drive['ctc'], 2); ?></p>
        <p><strong>Date:</strong> <?php echo $drive['date']; ?></p>

        <h3 class="mt-5">Eligibility Criteria</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Criteria</th>
                    <th>Your Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Department</td>
                    <td><?php echo $eligibility['department_code']; ?></td>
                    <td><?php echo $student['department_code']; ?></td>
                    <td>
                        <?php echo $eligible_department ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Marks 10th</td>
                    <td><?php echo $eligibility['marks_10']; ?>%</td>
                    <td><?php echo $student['marks_10']; ?>%</td>
                    <td>
                        <?php echo ($eligible_marks_10) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Marks 12th</td>
                    <td><?php echo $eligibility['marks_12']; ?>%</td>
                    <td><?php echo $student['marks_12']; ?>%</td>
                    <td>
                        <?php echo ($eligible_marks_12) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Marks UG</td>
                    <td><?php echo $eligibility['marks_ug']; ?>%</td>
                    <td><?php echo $student['marks_ug']; ?>%</td>
                    <td>
                        <?php echo ($eligible_marks_ug) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Marks PG</td>
                    <td><?php echo $eligibility['marks_pg']; ?>%</td>
                    <td><?php echo $student['marks_pg']; ?>%</td>
                    <td>
                        <?php echo ($eligible_marks_pg) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Backlog History</td>
                    <td><?php echo $eligibility['backlog_history']; ?></td>
                    <td><?php echo $student['backlog_history']; ?></td>
                    <td>
                        <?php echo ($eligible_backlog) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td><?php echo $eligibility['eligible_gender']; ?></td>
                    <td><?php echo $student['gender']; ?></td>
                    <td>
                        <?php echo ($eligible_gender) ? '<span class="status-symbol">✓</span>' : '<span class="status-symbol x">x</span>'; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if ($eligible): ?>
            <?php if (!$is_opted): ?>
                <form action="apply_drive.php" method="POST">
                    <input type="hidden" name="drive_id" value="<?php echo $drive_id; ?>">
                    <button type="submit" class="btn btn-option">Apply for Drive</button>
                </form>
            <?php else: ?>
                <p class="text-success">You have already applied for this drive.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-danger">You are not eligible to apply for this drive based on the eligibility criteria.</p>
        <?php endif; ?>

        <a href="s_drives.php" class="back-btn">Back to Drives List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
