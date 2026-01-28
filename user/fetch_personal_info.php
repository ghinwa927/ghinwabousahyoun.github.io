<?php
session_start();

require 'config.php';

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

if (!$conn) {
    echo '<script>alert("Error connecting to the server: ' . mysqli_connect_error() . '");</script>';
    exit;
}

// Assuming user is logged in and user_id is stored in session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT name, email, password, phone_number, date_of_birth FROM users WHERE patient_id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $phone_number = $row['phone_number'] ?? ''; 
        $dob = $row['date_of_birth'] ?? ''; 
    } else {
        echo '<script>alert("No user found.");</script>';
    }
} else {
    echo '<script>alert("User not logged in.");</script>';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="personalinfo.css">
</head>
<body>
    <h1 id='edit'>Edit Profile</h1>
    
    <!-- Form to update user info -->
    <form action="update_profile.php" method="POST">
        <div class="form-input">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-input">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-input">
            <label for="phone_number">Phone Number:</label>
            <div class="password-container">
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
            </div>
        </div>
        <div class="form-input">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
        </div>
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>

