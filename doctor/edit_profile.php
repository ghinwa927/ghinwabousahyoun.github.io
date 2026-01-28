<?php
session_start();
include 'config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch doctor data
$query = "SELECT fullname, email, contact_info FROM doctors WHERE doctor_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fullname, $email, $phone);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        form { max-width: 400px; }
        input[type="text"], input[type="email"], input[type="submit"] {
            width: 100%;
            padding: 0.6rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Edit Profile</h2>
<form method="POST" action="update_profile.php">
    <label>Full Name:</label>
    <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

    <label>Phone:</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

    <input type="submit" value="Update Profile">
</form>

<a href="doctor_dashboard.php" class="btn">← Back to Dashboard</a>


</body>
</html>
