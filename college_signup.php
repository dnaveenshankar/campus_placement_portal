<?php
include 'db_connection.php';

session_start(); // Start session to store user data

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $college_name = $conn->real_escape_string($_POST['college_name']);
    $mail_id = $conn->real_escape_string($_POST['mail_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $target_file = "assets/college-default.png"; // Default image

    // Check for duplicate username or college name
    $duplicate_check = "SELECT * FROM colleges WHERE username='$username' OR college_name='$college_name'";
    $duplicate_result = $conn->query($duplicate_check);

    if ($duplicate_result->num_rows > 0) {
        $error = "Username or College Name already exists.";
    } else {
        // Handle optional file upload for college logo
        if (!empty($_FILES['logo']['name'])) {
            $logo = $_FILES['logo'];
            $logo_name = basename($logo['name']);
            $target_dir = "uploads/";
            $target_file = $target_dir . uniqid() . "_" . $logo_name;

            // Check file type
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png'];
            if (in_array($file_type, $allowed_types)) {
                if (!move_uploaded_file($logo['tmp_name'], $target_file)) {
                    $error = "Error uploading logo.";
                }
            } else {
                $error = "Only JPG, JPEG, and PNG files are allowed.";
            }
        }

        // Insert into database if no errors
        if (!$error) {
            $sql = "INSERT INTO colleges (college_name, mail_id, username, password, logo) VALUES ('$college_name', '$mail_id', '$username', '$password', '$target_file')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['username'] = $username; // Store username in session
                $_SESSION['user_type'] = 'college'; // Store user type
                header("Location: college_dashboard.php"); // Redirect to dashboard
                exit;
            } else {
                $error = "Error: " . $conn->error;
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
    <title>College Signup - CampusHire</title>
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
    <script>
        function showPreview(event) {
            if (event.target.files.length > 0) {
                let src = URL.createObjectURL(event.target.files[0]);
                let preview = document.getElementById("logoPreview");
                preview.src = src;
                preview.style.display = "block";
            }
        }
    </script>
</head>
<body>
    <div class="container text-center">
            <!-- Display default CampusHire Logo -->
            <div class="logo-container">
                <img id="logo" src="Assets/logo.png" alt="CampusHire Logo">
            </div>
            <h2>CampusHire - College Signup</h2>
        
        <!-- Success and Error Modal Trigger -->
        <?php if ($error || $success): ?>
            <script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    new bootstrap.Modal(document.getElementById('messageModal')).show();
                });
            </script>
        <?php endif; ?>

        <!-- Signup Form -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="logo" class="form-label">College Logo (Profile Pic - Optional)</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/png, image/jpeg, image/jpg" onchange="showPreview(event);">
            </div>
            <div class="logo-container">
            <img id="logoPreview" src="Assets/college.png" alt="College Logo">
        </div>
            <div class="mb-3">
                <label for="college_name" class="form-label">College Name</label>
                <input type="text" class="form-control" id="college_name" name="college_name" required>
            </div>
            <div class="mb-3">
                <label for="mail_id" class="form-label">Mail ID</label>
                <input type="email" class="form-control" id="mail_id" name="mail_id" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>
    </div>

    <!-- Modal for Error and Success Messages -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel"><?= $success ? 'Success' : 'Error'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $error ? $error : $success; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
