<?php
session_start();
include 'config.php'; // Database config with constants: db_SERVER, db_USER, db_PASSWORD, db_DNAME

$alertMessage = '';

// Connect to the database
$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php"); // redirect to login if not logged in
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (empty($old) || empty($new) || empty($confirm)) {
        $alertMessage = "All fields are required.";
    } elseif ($new !== $confirm) {
        $alertMessage = "New passwords do not match.";
    } else {
        // Fetch current hashed password from DB
        $query = "SELECT password FROM doctors WHERE doctor_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $doctor_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashed_password);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Verify old password
        if (!password_verify($old, $hashed_password)) {
            $alertMessage = "Old password is incorrect.";
        } else {
            // Hash and update new password
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = "UPDATE doctors SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "si", $new_hashed, $doctor_id);
            if (mysqli_stmt_execute($stmt)) {
                $alertMessage = "Password changed successfully!";
            } else {
                $alertMessage = "Error updating password.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Alert user
    if (!empty($alertMessage)) {
        echo "<script>alert('" . addslashes($alertMessage) . "');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        h2 { margin-bottom: 1rem; }
        form { max-width: 400px; }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 0.6rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>Change Password</h2>
<form method="POST">
    <label>Old Password:</label>
    <input type="password" name="old_password" required>

    <label>New Password:</label>
    <input type="password" name="new_password" required>

    <label>Confirm New Password:</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Change Password">
</form>

<a href="doctor_dashboard.php" class="btn">← Back to Dashboard</a>

</body>
</html>
