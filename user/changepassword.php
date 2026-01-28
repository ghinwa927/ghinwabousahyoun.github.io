<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Fetch the hashed password from the database
    $sql = "SELECT password FROM users WHERE patient_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($current_password, $user['password'])) {
        if (!empty($new_password)) {
            if ($new_password === $confirm_new_password) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password in the database
                $update_sql = "UPDATE users SET password = ? WHERE patient_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $user_id);

                if (mysqli_stmt_execute($update_stmt)) {
                    echo "<script>alert('Password updated successfully!');</script>";
                } else {
                    echo "<script>alert('Error updating password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('New password and confirmation do not match.');</script>";
            }
        } else {
            echo "<script>alert('Please enter a new password.');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="changepassword.css">
</head>
<body>
    <h1 id="eidpass">Change Password</h1>
    
    <form action="" method="POST">
        <div class="form-input">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
        </div>

        <div class="form-input">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
        </div>

        <div class="form-input">
            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Re-enter new password" required>
        </div>

        <button type="submit">Update Password</button>
    </form>
</body>
</html>