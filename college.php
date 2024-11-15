<?php
include 'db_connection.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please log in to access this page!'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch college details
$sql_college = "SELECT * FROM colleges";
$result_college = $conn->query($sql_college);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Details - CampusHire</title>
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
        .college-card {
            margin-bottom: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #ffffff;
        }
        .college-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .card-title {
            font-size: 1.5rem;
            color: #333;
        }
        .dept-list {
            margin-top: 20px;
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
            transition: background-color 0.3s;
            width: 150px;
        }
        .btn-option:hover {
            background-color: #d8961e;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">College and Department Details</h1>
    
    <?php if ($result_college->num_rows > 0): ?>
        <?php while ($college = $result_college->fetch_assoc()): ?>
            <div class="college-card p-4">
                <div class="d-flex align-items-center">
                    <img src="<?= htmlspecialchars($college['logo']) ?>" alt="College Logo">
                    <div class="ms-3">
                        <h2 class="card-title"><?= htmlspecialchars($college['college_name']) ?></h2>
                        <p>Email: <?= htmlspecialchars($college['mail_id']) ?></p>
                    </div>
                </div>

                <!-- Fetch departments for this college -->
                <?php
                $college_id = $college['id'];
                $sql_dept = "SELECT * FROM departments WHERE college_id = $college_id";
                $result_dept = $conn->query($sql_dept);
                ?>

                <?php if ($result_dept->num_rows > 0): ?>
                    <div class="dept-list">
                        <h4>Departments:</h4>
                        <ul class="list-group">
                            <?php while ($dept = $result_dept->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <strong><?= htmlspecialchars($dept['dept_name']) ?></strong> (<?= htmlspecialchars($dept['stream']) ?>)
                                    <br>Dept Code: <?= htmlspecialchars($dept['dept_code']) ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p>No departments available for this college.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No colleges found in the database.</p>
    <?php endif; ?>
    <div class="text-center mb-4">
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
