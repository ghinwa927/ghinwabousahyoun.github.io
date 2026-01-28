<?php
session_start();
require 'config.php'; 

//if (!isset($_SESSION['secretary_logged_in']) || $_SESSION['secretary_logged_in'] !== true) {
  //  header("Location: index.php");
    //exit;
//}

$sec_id = $_SESSION['secretary_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE secretaries SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $sec_id);

        if ($stmt->execute()) {
            $success = "Password changed successfully.";
        } else {
            $error = "Error updating password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Change Password</title>
  <style>
    body {
      font-family: sans-serif;
      background: #f3f4f6;
      padding: 40px;
    }
    .container {
      max-width: 400px;
      background: white;
      padding: 20px;
      margin: auto;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    input[type="password"], button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      background: #1d4ed8;
      color: white;
      border: none;
      font-weight: bold;
    }
    .error { color: red; }
    .success { color: green; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Change Password</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
      <input type="password" name="new_password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Update Password</button>
    </form>
  </div>
</body>
</html>