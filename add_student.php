<?php
include 'db_connection.php';

session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

$college_username = $_SESSION['username'];  // Assuming session stores the college username

// Handle student addition via form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['department_code'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['username']); // Password same as username
    $department_code = mysqli_real_escape_string($conn, $_POST['department_code']);

    // Check if the username already exists
    $check_query = "SELECT * FROM students WHERE username = '$username'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "<script>document.addEventListener('DOMContentLoaded', function () { 
            showModal('Error', 'The username already exists!', 'danger'); 
        });</script>";
    } else {
        // Insert new student record with college username
        $insert_query = "INSERT INTO students (username, password, department_code, college_username) 
                        VALUES ('$username', '$password', '$department_code', '$college_username')";

        if ($conn->query($insert_query) === TRUE) {
            echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                showModal('Success', 'The student was added successfully.', 'success'); 
            });</script>";
        } else {
            // Detailed error message
            echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                showModal('Error', 'An error occurred. Please try again.', 'danger'); 
            });</script>";
        }
    }
}

// Handle CSV import
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $csv_file = $_FILES['csv_file'];

    if ($csv_file['type'] != 'text/csv' && $csv_file['type'] != 'application/vnd.ms-excel') {
        echo "<script>document.addEventListener('DOMContentLoaded', function () { 
            showModal('Error', 'Invalid file type. Please upload a CSV file.', 'danger'); 
        });</script>";
    } else {
        $file_path = $csv_file['tmp_name'];

        // Open CSV file and read its contents
        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            // Skip the header row
            fgetcsv($handle);

            // Initialize a flag for success
            $inserted = false;

            // Loop through CSV rows
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Changed delimiter to ","
                // Ensure the data has at least 3 columns
                if (count($data) >= 3) {
                    $username = mysqli_real_escape_string($conn, $data[0]);
                    $password = mysqli_real_escape_string($conn, $data[1]);
                    $department_code = mysqli_real_escape_string($conn, $data[2]);

                    // Check if the username already exists in the database
                    $check_query = "SELECT * FROM students WHERE username = '$username'";
                    $result = $conn->query($check_query);

                    if ($result->num_rows == 0) {
                        // Insert the student if not already exists, with college username
                        $insert_query = "INSERT INTO students (username, password, department_code, college_username) 
                                         VALUES ('$username', '$password', '$department_code', '$college_username')";

                        if ($conn->query($insert_query) === TRUE) {
                            $inserted = true;  // Mark that insertion has occurred
                        } else {
                            echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                                showModal('Error', 'Failed to insert student data.', 'danger'); 
                            });</script>";
                        }
                    } else {
                        echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                            showModal('Error', 'Username already exists in the system.', 'danger'); 
                        });</script>";
                    }
                } else {
                    echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                        showModal('Error', 'Invalid CSV format. Ensure the correct columns are provided.', 'danger'); 
                    });</script>";
                }
            }
            fclose($handle);

            // If any row was inserted, show the success alert after the loop
            if ($inserted) {
                echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                    showModal('Success', 'Students have been imported successfully.', 'success'); 
                });</script>";
            } else {
                echo "<script>document.addEventListener('DOMContentLoaded', function () { 
                    showModal('Error', 'No new students were inserted.', 'danger'); 
                });</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
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
        .footer {
            text-align: center;
            background-color: #ffffff;
            color: 082c5c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Student</h2>

        <!-- Form to Add Student Manually -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="department_code" class="form-label">Department Code</label>
                <select class="form-select" id="department_code" name="department_code" required>
                    <?php
                    // Fetch departments from the database
                    $dept_query = "SELECT * FROM departments";
                    $dept_result = $conn->query($dept_query);
                    while ($dept = $dept_result->fetch_assoc()) {
                        echo "<option value='{$dept['dept_code']}'>{$dept['dept_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username (Roll Number)</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>

        <h2>Import Students from CSV</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="csv_file" class="form-label">Choose CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" required>
            </div>
            <button type="submit" class="btn btn-secondary">Import CSV</button>
        </form>

        <div class="mt-4">
            <a href="assets/student_template.csv" class="btn btn-info" download>Download Student Template</a>
        </div>

        <!-- Common Modal for All Alerts -->
        <div class="modal fade" id="commonModal" tabindex="-1" aria-labelledby="commonModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commonModalLabel">Alert</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalMessage">
                        <!-- Dynamic message content will go here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="mt-4">
    <!-- Back Button (Link version) -->
    <a href="student_list.php" class="btn btn-secondary">Back</a>
</div>

    </div>

    <script>
        // Function to show modal with dynamic content
        function showModal(title, message, type) {
            const modalTitle = document.getElementById('commonModalLabel');
            const modalMessage = document.getElementById('modalMessage');
            modalTitle.textContent = title;
            modalMessage.textContent = message;

            const modal = new bootstrap.Modal(document.getElementById('commonModal'));
            modal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
