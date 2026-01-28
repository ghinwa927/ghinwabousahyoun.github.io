<?php
session_start();
include 'config.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

if (empty($fullname) || empty($email) || empty($phone)) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Update doctor profile
$sql = "UPDATE doctors SET fullname = ?, email = ?, contact_info = ? WHERE doctor_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $phone, $doctor_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully.'); window.location.href='edit_profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile.'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Database error.'); window.history.back();</script>";
}

mysqli_close($conn);
?>
