<?php
// Start the session
session_start();

// Include database connection
include('db_connection.php');

// Assuming the college_username is stored in the session
$college_username = $_SESSION['username'];

// Fetch the college name from the database using the college_username
$query = "SELECT college_name FROM colleges WHERE username = '$college_username'";
$result = $conn->query($query);

// If the query is successful and a college is found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $college_name = $row['college_name'];
} else {
    // Fallback if no college name is found (just in case)
    $college_name = "Unknown College";
}

// Fetch all department codes from the departments table
$dept_query = "SELECT dept_code FROM departments WHERE college_id = (SELECT id FROM colleges WHERE username = '$college_username')";
$dept_result = $conn->query($dept_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch drive data
    $company_name = $_POST['company_name'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $ctc = $_POST['ctc'];

    // Handle file upload for company logo (optional)
    $logo_path = 'assets/company-default.png'; // Default logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a valid image
        if (in_array($fileType, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_path = $target_file;
            }
        }
    }

    // Handle file upload for job description (PDF)
    $job_description_path = '';
    if (isset($_FILES['job_description']) && $_FILES['job_description']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["job_description"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a valid PDF
        if ($fileType == "pdf") {
            if (move_uploaded_file($_FILES["job_description"]["tmp_name"], $target_file)) {
                $job_description_path = $target_file;
            }
        }
    }

    // Insert into 'drives' table
    $query = "INSERT INTO drives (college_username, company_name, address, role, logo, job_description, description, date, status, ctc) 
              VALUES ('$college_username', '$company_name', '$address', '$role', '$logo_path', '$job_description_path', '$description', '$date', '$status', '$ctc')";

    if (mysqli_query($conn, $query)) {
        $drive_id = mysqli_insert_id($conn);

        // Fetch eligibility data
        $selected_dept_codes = isset($_POST['department_code']) ? implode(',', $_POST['department_code']) : ''; // Handle multiple departments
        $marks_10 = $_POST['marks_10'];
        $marks_12 = $_POST['marks_12'];
        $marks_ug = $_POST['marks_ug'];
        $marks_pg = $_POST['marks_pg'];
        $backlog_history = $_POST['backlog_history'];
        $current_backlogs = $_POST['current_backlogs'];
        $dob = $_POST['dob'];
        $eligible_gender = $_POST['eligible_gender'];

        // Insert eligibility data into 'eligibility' table
        $eligibility_query = "INSERT INTO eligibility (drive_id, department_code, marks_10, marks_12, marks_ug, marks_pg, backlog_history, current_backlogs, dob, eligible_gender) 
                              VALUES ('$drive_id', '$selected_dept_codes', '$marks_10', '$marks_12', '$marks_ug', '$marks_pg', '$backlog_history', '$current_backlogs', '$dob', '$eligible_gender')";

        if (mysqli_query($conn, $eligibility_query)) {
            
        } else {
            echo "Error adding eligibility: " . mysqli_error($conn);
        }
    } else {
        echo "Error adding drive: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drive</title>
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
            max-width: 950px;
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
        <h2 class="text-center mb-4">Add Drive</h2>

        <form method="POST" action="add_drive.php" enctype="multipart/form-data">
            <div class="row mb-3">
                <label for="college_username" class="col-sm-3 col-form-label">College Username</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="college_username" id="college_username" value="<?= $college_username ?>" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <label for="company_name" class="col-sm-3 col-form-label">Company Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="company_name" id="company_name" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="address" class="col-sm-3 col-form-label">Company Address</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="address" id="address" rows="3" required></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="role" class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="role" id="role" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="description" class="col-sm-3 col-form-label">Company Description</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="date" class="col-sm-3 col-form-label">Drive Date</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" name="date" id="date" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="status" class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-9">
                    <select class="form-control" name="status" id="status" required>
                        <option value="open">Open</option>
                        <option value="close">Close</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="ctc" class="col-sm-3 col-form-label">CTC (Cost to Company)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control" name="ctc" id="ctc" required>
                </div>
            </div>

            <!-- File Upload for Logo (Optional) -->
            <div class="row mb-3">
                <label for="logo" class="col-sm-3 col-form-label">Company Logo</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                </div>
            </div>

            <!-- File Upload for Job Description (Optional) -->
            <div class="row mb-3">
                <label for="job_description" class="col-sm-3 col-form-label">Job Description (PDF)</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" name="job_description" id="job_description" accept="application/pdf">
                </div>
            </div>

            <!-- Eligibility Section -->
            <h4 class="mb-3">Eligibility Criteria</h4>

            
            <div class="row mb-3">
    <label for="department_code" class="col-sm-3 col-form-label">Department Code</label>
    <div class="col-sm-9">
        <?php
        // Display department checkboxes dynamically
        while ($dept_row = $dept_result->fetch_assoc()) {
            echo '<div class="form-check">
                    <input class="form-check-input" type="checkbox" name="department_code[]" value="' . $dept_row['dept_code'] . '" id="dept_' . $dept_row['dept_code'] . '">
                    <label class="form-check-label" for="dept_' . $dept_row['dept_code'] . '">' . $dept_row['dept_code'] . '</label>
                  </div>';
        }
        ?>
    </div>
</div>


            <div class="row mb-3">
                <label for="marks_10" class="col-sm-3 col-form-label">10th Marks</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="marks_10" id="marks_10" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="marks_12" class="col-sm-3 col-form-label">12th Marks</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="marks_12" id="marks_12" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="marks_ug" class="col-sm-3 col-form-label">UG Marks</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="marks_ug" id="marks_ug" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="marks_pg" class="col-sm-3 col-form-label">PG Marks (if applicable)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="marks_pg" id="marks_pg">
                </div>
            </div>

            <div class="row mb-3">
                <label for="backlog_history" class="col-sm-3 col-form-label">Backlog History</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="backlog_history" id="backlog_history" rows="3"></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="current_backlogs" class="col-sm-3 col-form-label">Current Backlogs</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="current_backlogs" id="current_backlogs" rows="3"></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="dob" class="col-sm-3 col-form-label">Date of Birth</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" name="dob" id="dob" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="eligible_gender" class="col-sm-3 col-form-label">Eligible Gender</label>
                <div class="col-sm-9">
                    <select class="form-control" name="eligible_gender" id="eligible_gender" required>
                        <option value="Both">Both</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
    <div class="col-sm-9 offset-sm-3">
        <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
    </div>
</div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>



    <div class="footer container">
        <img src="assets/logo.png" alt="CampusHire Logo">
        <span>CampusHire - <?= $college_name ?></span>
    </div>
    </div>
</body>
</html>
