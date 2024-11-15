<?php
include 'db_connection.php';

session_start();

// Ensure the user is a college
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'college') {
    echo "<script>
            window.onload = function() {
                showModal('Unauthorized access!', 'error');
            }
          </script>";
    exit();
}

// Fetch college data
$username = $_SESSION['username'];
$sql = "SELECT * FROM colleges WHERE username='$username'";
$result = $conn->query($sql);
$college = $result->fetch_assoc();

// Update profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $college_name = $_POST['college_name'];
    $mail_id = $_POST['mail_id'];
    
    // Initialize error variable
    $error = '';

    // Logo upload logic
    if (!empty($_FILES['logo']['name'])) {
        $logo = $_FILES['logo'];
        $logo_name = basename($logo['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . "_" . $logo_name;

        // Check file type
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];
        
        if (in_array($file_type, $allowed_types)) {
            // Check if the file is uploaded successfully
            if (!move_uploaded_file($logo['tmp_name'], $target_file)) {
                $error = "Error uploading logo.";
            } else {
                $logo_path = $target_file; // Assign the path of the uploaded logo
            }
        } else {
            $error = "Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        // If no new logo uploaded, use the existing one
        $logo_path = $college['logo'];
    }

    // If there is no error, update the profile
    if (empty($error)) {
        // Update query (excluding the username)
        $update_query = "UPDATE colleges SET college_name='$college_name', mail_id='$mail_id', logo='$logo_path' WHERE username='$username'";

        if ($conn->query($update_query) === TRUE) {
            // If update is successful, show the success alert modal
            echo "<script>
                    window.onload = function() {
                        showModal('Changes saved successfully!', 'success');
                    }
                  </script>";
        } else {
            echo "<script>
                    window.onload = function() {
                        showModal('Error updating profile: " . $conn->error . "', 'error');
                    }
                  </script>";
        }
    } else {
        // Show the error message if there's any
        echo "<script>
                window.onload = function() {
                    showModal('$error', 'error');
                }
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Profile - CampusHire</title>
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
        .footer img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 50%
        }
        .navbar {
            background-color: #ffffff;
            padding: 15px;
            color: #082c5c;
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
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar with College Logo and Name -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">
            <img src="<?= $college['logo'] ?>" alt="College Logo">
            <h5 class="text-center">College Profile - <?= $college['college_name'] ?></h5>
        </a>
    </nav>

    <!-- College Profile Form -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="college_name" class="form-label">College Name</label>
            <input type="text" class="form-control" id="college_name" name="college_name" value="<?= $college['college_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="mail_id" class="form-label">Mail ID</label>
            <input type="email" class="form-control" id="mail_id" name="mail_id" value="<?= $college['mail_id'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="logo" name="logo" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(event)">
            <img src="<?= $college['logo'] ?>" alt="Profile Picture Preview" id="logo-preview" class="img-thumbnail mt-3" width="100">
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="college_dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </form>


<!-- Footer -->
<div class="footer container">
    <img src="assets/logo.png" alt="CampusHire Logo">
    <span>CampusHire - <?= $college['college_name'] ?></span>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalLabel">Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modal-message">
        <!-- The alert message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function() {
            const preview = document.getElementById('logo-preview');
            preview.src = reader.result; // Set the preview image source to the uploaded file
        };
        
        if (file) {
            reader.readAsDataURL(file); // Read the file as a data URL
        }
    }

    // Show modal function
    function showModal(message, type) {
        const modalMessage = document.getElementById('modal-message');
        modalMessage.textContent = message;
        
        const modal = new bootstrap.Modal(document.getElementById('alertModal'));
        modal.show();
    }
</script>

</body>
</html>
