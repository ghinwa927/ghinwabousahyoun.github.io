<?php
session_start();
include "config.php";

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

if (!$conn) {
    echo '<script>alert("Error connecting to the server: " . mysqli_connect_error());</script>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if user exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['patient_id'];
            $_SESSION['username'] = $user['name'];

            header("Location: user-dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Healthcare Appointment System</title>
  <link rel="stylesheet" href="login_register.css">
  <style>
    #error-message {
      display: none;
      margin: 10px auto;
      padding: 10px;
      background-color: #ffcccc;
      color: #d8000c;
      font-weight: bold;
      border: 1px solid #d8000c;
      border-radius: 5px;
      text-align: center;
      width: 50%;
    }
  </style>
</head>
<body>
  <?php
    if (isset($_SESSION['error'])) {
        echo '<div id="error-message" class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
  ?>

  <section>
    <div class="imgBx">
      <img src="login.jpeg">
    </div>
    <div class="form-container">
      <div class="formBx">
        <h3>Login</h3>
        <form action="" method="post">
          <div class="inputBx">
            <span>Email</span>
            <input type="email" name="email" placeholder="Enter your email" required class="box">
          </div>
          <div class="inputBx">
            <span>Password</span>
            <input type="password" name="password" placeholder="Enter your password" required class="password">
          </div>

          <div class="inputBx">
            <input type="submit" name="submit" value="Login" class="btn">
          </div>
          <div class="inputBx">
            <p>Don't have an account? <a href="register.php" id="register_link">Register Here</a></p>
          </div>

          <div class="inputBx">
  <a href="index.php" class="btn">Go to Dashboard</a>
</div>
        </form>
      </div>
    </div>
  </section>

  <!-- Error Message Auto-Hide Script -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let errorMsg = document.getElementById("error-message");

      if (errorMsg) {
        errorMsg.style.display = "block"; // Show message
        setTimeout(function() {
          errorMsg.style.display = "none"; // Hide after 2 seconds
        }, 2000);
      }
    });
  </script>

</body>
</html>