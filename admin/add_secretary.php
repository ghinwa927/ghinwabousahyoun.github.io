<?php
include('db_connection.php');
require_once '../email.php'; // Adjust path if needed

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mother_name = $_POST['mother_name'];
    $nationality = $_POST['nationality'];
    $experience = $_POST['experience'];

    // Check if the passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Check if email already exists
        $email_check = $conn->prepare("SELECT * FROM secretary WHERE email = ?");
        $email_check->bind_param("s", $email);
        $email_check->execute();
        $result = $email_check->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already exists!";
        } else {
            // Hash the password before inserting
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL to insert a new secretary
            $sql = "INSERT INTO secretary (fullname, contact_info, email, password, mother_name, nationality, yearsofex) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $fullname, $contact_info, $email, $hashed_password, $mother_name, $nationality, $experience);

            if ($stmt->execute()) {
                $success_message = "Secretary added successfully!";
                
                 // Send an email with login credentials
                $emailSent = sendEmailToSecretary(
                $fullname,"Welcome to Novena-Secretary Account Created",
                "Welcome to Novena Healthcare<br>
                <b>Dear {$fullname},</b><br>
                We are exicted to welcome you to the team at Novena.<br><br>
                Your secretary account has been successfully created in our system.You can now log in to manage doctor appointments.
                <br><br>

                Your Login Details: <br>
                <ul>
                <li>Email: {$email}</li>
                <li>Temporary password: {$password}</li>
                <li><a href='http://localhost/senior/secretary'>localhost/senior/doctor</a></li>
                </ul><br><br>

                Please log in as soon as possible to change your password.<br><br>

                Looking forward to working with you!.<br><br>

                Best regards,<br>
                Novena


                 You have been successfully added to our system.<br>
                 <strong>Email:</strong> {$email}<br>
                 <strong>Password:</strong> {$password}<br>
                 Please log in and change your password for security.",
                  true
                 );

             if ($emailSent) {
               //echo "Email sent to {$fullname}.";
            } else {
              //echo "Failed to send email.";
           }



            } else {
                $error_message = "Error adding secretary: " . $conn->error;
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Secretary</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4; /* Light background */
            margin: 0;
            padding: 0;
            color: #1f2937;
        }

        .container {
            max-width: 960px;
            margin: 50px auto;
            display: flex;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .image-container {
            flex: 1.2;
            overflow: hidden;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
           
        }

        .form-container {
            flex: 0.8;
            padding: 50px 40px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container h2 {
            color: #007bff; /* Blue color for the title */
            font-size: 28px;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 12px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007bff; /* Blue border on focus */
            outline: none;
        }

        input[type="submit"] {
            background-color: #007bff; /* Blue button */
            color: #ffffff;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .message {
            font-size: 14px;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }

        .success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button input[type="submit"] {
            background-color: #1f2937;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .back-button input[type="submit"]:hover {
            background-color: #111827;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .form-container {
                padding: 30px 20px;
            }

            .image-container {
                height: 220px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="image-container">
        <img src="OIP.jpeg" alt="Secretary Image">
    </div>

    <div class="form-container">
        <h2>Add Secretary</h2>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="add_secretary.php">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>

            <div class="form-group">
                <label for="contact_info">Contact Info</label>
                <input type="text" id="contact_info" name="contact_info" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="mother_name">Mother's Name</label>
                <input type="text" id="mother_name" name="mother_name" required>
            </div>

            <div class="form-group">
                <label for="nationality">Nationality</label>
                <input type="text" id="nationality" name="nationality" required>
            </div>

            <div class="form-group">
                <label for="experience">Experience</label>
                <input type="text" id="experience" name="experience" required>
            </div>

            <input type="submit" value="Add Secretary">
        </form>

        <!-- Return to Dashboard Button -->
        <div class="back-button">
            <form action="admin.php" method="get">
                <input type="submit" value="Return to Dashboard">
            </form>
        </div>
    </div>
</div>

</body>
</html>
