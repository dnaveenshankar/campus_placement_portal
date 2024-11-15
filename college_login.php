<?php
include 'db_connection.php';

session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Check if username exists in the database
    $sql = "SELECT * FROM colleges WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Store session variables
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = 'college'; // User type for college
            header("Location: college_dashboard.php"); // Redirect to dashboard
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CampusHire</title>
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
            max-width: 500px;
        }
        .logo-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 2px solid #efa128;
        }
        h2 {
            color: #082c5c;
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            color: #082c5c;
            font-weight: bold;
        }
        .form-control {
            border: 1px solid #efa128;
        }
        .btn-primary {
            background-color: #efa128;
            border: none;
        }
        .btn-primary:hover {
            background-color: #d8961e;
        }
    </style>
</head>
<body>
    <div class="container text-center">
         <!-- Display default CampusHire Logo -->
         <div class="logo-container">
            <img id="logo" src="Assets/logo.png" alt="CampusHire Logo">
        </div>
        <h2>CampusHire - College Login</h2>

        <!-- Error Modal Trigger -->
        <?php if ($error): ?>
            <script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    new bootstrap.Modal(document.getElementById('errorModal')).show();
                });
            </script>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <!-- Error Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $error; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
