<?php

session_start();

include'db_connection.php';

$conn->set_charset("utf8mb4");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Prepare statement to get admin by email
    $stmt = $conn->prepare("SELECT id, email, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        // Password matches, set session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        /* Same styling as before */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-container {
            background: #fff;
            display: flex;
            max-width: 900px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-section {
            flex: 1;
            padding: 50px;
        }
        .form-section h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }
        .form-section input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        .form-section input:focus {
            outline: none;
            border-color: #007bff;
            background-color: #fff;
        }
        .form-section button {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .form-section button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin: 10px 0;
        }
        .image-section {
            flex: 1;
            background: #f0f0f0;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }
        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-section">
            <h2>Admin Login</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
        <div class="image-section">
            <img src="online-consultation-doctor-on-smartphone-600nw-2150820867.webp" alt="Admin Login Image">
        </div>
    </div>
</body>
</html>
