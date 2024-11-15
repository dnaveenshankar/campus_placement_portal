<?php
include 'db_connection.php';
session_start();

// Ensure the user is logged in and is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch college details
$username = $_SESSION['username'];
$sql = "SELECT id, college_name, logo FROM colleges WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>alert('College not found!'); window.location.href = 'login.php';</script>";
    exit();
}

$college = $result->fetch_assoc();
$college_id = $college['id'];
$college_name = $college['college_name'];
$college_logo = $college['logo'];

// Handle Add Department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $dept_code = $_POST['dept_code'];
    $stream = $_POST['stream'];
    $dept_name = $_POST['dept_name'];

    $check_query = "SELECT * FROM departments WHERE college_id = $college_id AND (dept_code = '$dept_code' OR dept_name = '$dept_name')";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $error_message = "A department with the same code or name already exists!";
    } else {
        $insert_query = "INSERT INTO departments (dept_code, stream, dept_name, college_id) VALUES ('$dept_code', '$stream', '$dept_name', $college_id)";
        if ($conn->query($insert_query) === TRUE) {
            $success_message = "Department added successfully!";
        } else {
            $error_message = "Error adding department: " . $conn->error;
        }
    }
}

// Handle Delete Department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_department'])) {
    $dept_id = $_POST['dept_id'];

    $delete_query = "DELETE FROM departments WHERE id = $dept_id AND college_id = $college_id";
    if ($conn->query($delete_query) === TRUE) {
        $success_message = "Department deleted successfully!";
    } else {
        $error_message = "Error deleting department: " . $conn->error;
    }
}

// Fetch Departments
$query = "SELECT * FROM departments WHERE college_id = $college_id";
$departments_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 30px;
            border-radius: 10px;
            max-width: 950px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #082c5c;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
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
            border-radius: 50%
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar -->
    <nav class="navbar navbar-light">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="<?= $college_logo ?>" alt="Logo">
            <span><?= $college_name ?></span>
        </a>
    </nav>
    <hr>

    <h2>Manage Departments</h2>

    <!-- Back Button -->
    <a href="college_dashboard.php" class="btn btn-secondary mb-4">Back to Dashboard</a>

    <!-- Display Alerts -->
    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php } elseif (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php } ?>

    <!-- Departments Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Department Code</th>
                <th>Stream</th>
                <th>Department Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $departments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['dept_code'] ?></td>
                    <td><?= $row['stream'] ?></td>
                    <td><?= $row['dept_name'] ?></td>
                    <td>
                        <a href="view_department.php?id=<?= $row['id'] ?>" class="btn btn-info">View</a>
                        <button class="btn btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Add Department Form -->
    <h4 class="mt-4">Add New Department</h4>
    <form method="POST">
        <input type="hidden" name="add_department" value="1">
        <div class="mb-3">
            <label for="dept_code" class="form-label">Department Code</label>
            <input type="text" class="form-control" id="dept_code" name="dept_code" required>
        </div>
        <div class="mb-3">
            <label for="stream" class="form-label">Stream</label>
            <select class="form-select" id="stream" name="stream" required>
                <option value="UG">UG</option>
                <option value="PG">PG</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dept_name" class="form-label">Department Name</label>
            <input type="text" class="form-control" id="dept_name" name="dept_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Department</button>
    </form>

    <!-- Footer -->
    <div class="footer container">
    <img src="assets/logo.png" alt="CampusHire Logo">
    <span>CampusHire - <?= $college['college_name'] ?></span>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabel" style="color: black;">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="color: black;">
        Are you sure you want to delete this department?
      </div>
      <div class="modal-footer">
        <form method="POST">
            <input type="hidden" name="delete_department" value="1">
            <input type="hidden" id="deleteDeptId" name="dept_id">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    function confirmDelete(deptId) {
        document.getElementById('deleteDeptId').value = deptId;
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        deleteModal.show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
