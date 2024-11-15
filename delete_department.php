<?php
include 'db_connection.php';

session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>alert('Unauthorized access!'); window.location.href = 'login.php';</script>";
    exit();
}

$department_id = $_GET['id'] ?? null;

// If no department ID is provided, redirect to the department list
if (!$department_id) {
    echo "<script>alert('Invalid department ID!'); window.location.href = 'departments.php';</script>";
    exit();
}

// Fetch the department details to ensure it's valid
$sql = "SELECT * FROM departments WHERE id = $department_id";
$result = $conn->query($sql);

// If the department doesn't exist, redirect with an error
if ($result->num_rows == 0) {
    echo "<script>alert('Department not found!'); window.location.href = 'departments.php';</script>";
    exit();
}

// Begin a transaction to delete everything related to this department
$conn->begin_transaction();

try {
    // 1. Delete students from the department
    $delete_students_query = "DELETE FROM students WHERE department_code = (SELECT dept_code FROM departments WHERE id = $department_id)";
    if ($conn->query($delete_students_query) === FALSE) {
        throw new Exception('Error deleting students');
    }

    // 2. Delete department from the departments table
    $delete_department_query = "DELETE FROM departments WHERE id = $department_id";
    if ($conn->query($delete_department_query) === FALSE) {
        throw new Exception('Error deleting department');
    }

    // Commit the transaction
    $conn->commit();

    // Redirect and trigger success modal
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const myModal = new bootstrap.Modal(document.getElementById('successModal'), {});
                myModal.show();
                
                setTimeout(function() {
                    window.location.href = 'departments.php'; // Redirect after modal closes
                }, 2000); // Redirect after 2 seconds
            });
          </script>";
    exit();

} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'departments.php';</script>";
    exit();
}

?>

<!-- Modal HTML structure -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Department and all related students deleted successfully!
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and CSS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
