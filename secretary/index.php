<?php

session_start([
    'cookie_lifetime' => 86400,
    'cookie_httponly' => true,
    'use_strict_mode' => true
]);

if (isset($_SESSION['secretary_id'])) {
    header("Location: app.php");
    exit();
}

$host = 'localhost';
$db   = 'senior';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM secretary WHERE email = ?");
    $stmt->execute([$email]);
    $secretary = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($secretary && password_verify($password, $secretary['password'])) {
        session_regenerate_id(true);
        $_SESSION['secretary_id'] = $secretary['id'];
        $_SESSION['secretary_name'] = $secretary['fullname'];
        header("Location: app.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secretary Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .main-container {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .form-section {
            padding: 40px;
            width: 50%;
        }
        .form-section h2 {
            margin-bottom: 20px;
            color: #0056b3;
        }
        .form-section input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .form-section button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        .form-section button:hover {
            background-color: #004494;
        }
        .image-section {
            width: 50%;
            background: #eaf3ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-section img {
            max-width: 100%;
            height: auto;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            .form-section, .image-section {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-section">
            <h2>Secretary Login</h2>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
        <div class="image-section">
            <img src="helping-hand-illustration-medical-secretary-assisting-patient-office-waiting-room-o_983420-154191.avif" alt="Login Illustration">
        </div>
    </div>
</body>
</html>
