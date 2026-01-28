<?php
session_start();
include 'config.php';

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare the query to fetch the doctor with the entered email
    $stmt = $conn->prepare("SELECT doctor_id, password, fullname, email FROM doctors WHERE email = ? LIMIT 1");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch a single row
    if ($row = $result->fetch_assoc()) {
        // Check password using password_verify (don't rehash input)
        if (password_verify($password, $row['password'])) {
            $_SESSION['doctor_id'] = $row['doctor_id'];
            $_SESSION['doctor_name'] = $row['fullname'];
            header("Location: doctor_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No doctor found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Login</title>
    <style>
        body {
      background-image: url("b.jpg");
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
      padding: 20px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    h1 {
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type="text"],
    input[type="password"] {
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 4px;
      width: 100%;
    }

    input[type="submit"] {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome to the Healthcare Appointment System</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
<!--
ADD THIS WHEN I ADD DOCTOR WITH HASHED PASSWORD
if (password_verify($password, $row['password'])) {
    $_SESSION['doctor_id'] = $row['doctor_id'];
    $_SESSION['doctor_name'] = $row['full_name'];
    header("Location: doctor_dashboard.php");
    exit();
} else {
    $error = "Incorrect password.";
}


    -->