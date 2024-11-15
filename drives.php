<?php
include 'db_connection.php';
session_start();

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
// Fetch all drives posted by the logged-in college
$query = "SELECT * FROM drives WHERE college_username = '$college_username'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Drives</title>
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
        .navbar {
            background-color: #ffffff;
            padding: 15px;
            color: 082c5c;
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
        <h2>Campus Drives</h2>

        <!-- Display the list of drives -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Role</th>
                    <th>CTC</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['company_name'] . "</td>";
                        echo "<td>" . $row['role'] . "</td>";
                        echo "<td>" . $row['ctc'] . "</td>";
                        echo "<td>" . $row['date'] . "</td>";
                        echo "<td>
                            <a href='view_drive.php?id=" . $row['id'] . "' class='btn btn-primary'>View</a>
                            <button class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row['id'] . "'>Delete</button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No drives found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Add Button -->
        <a href="add_drive.php" class="btn btn-secondary btn">Add</a>
        <!-- Back Button -->
        <a href="college_dashboard.php" class="btn btn-secondary btn-back">Back</a>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Drive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this drive?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="" id="confirmDeleteButton" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    

    <div class="footer container">
        <img src="assets/logo.png" alt="CampusHire Logo">
        <span>CampusHire - <?= $college_name ?></span>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set the delete confirmation link dynamically when the delete button is clicked
        var deleteButtons = document.querySelectorAll('button[data-bs-toggle="modal"]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var driveId = this.getAttribute('data-id');
                var confirmDeleteButton = document.getElementById('confirmDeleteButton');
                confirmDeleteButton.setAttribute('href', 'delete_drive_process.php?drive_id=' + driveId);
            });
        });
    </script>
</body>
</html>
