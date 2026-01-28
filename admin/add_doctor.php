<?php
include('db_connection.php');
require_once '../email.php'; // Adjust path if needed
session_start();

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $fullname = $_POST['fullname'];
    $specialty = $_POST['specialty'];
    $contact_info = $_POST['contact_info'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nationality = $_POST['nationality'];
    $union_number = $_POST['union_number'];
    $mother_name = $_POST['mother_name'];
    $yearsofex = $_POST['yearsofex'];
    $uni_name = $_POST['uni_name'];
    $degree = $_POST['degree'];


    // Handling image upload
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = basename($image_name);

    // Create the "images" directory if it doesn't exist
    if (!is_dir("images")) {
        mkdir("images", 0777, true);
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    }
    
    elseif (!move_uploaded_file($image_tmp, $image_path)) {
        $error_message = "Failed to upload image.";
    } else {
      
        $email_check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $email_check->bind_param("s", $email);
        $email_check->execute();
        $result = $email_check->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already exists!";
        } else {
          
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            
            $sql = "INSERT INTO doctors 
            (fullname, specialty, contact_info, nationality, union_number, mother_name, image, status, email, password, yearsofex, uni_name, degree) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", 
            $fullname, $specialty, $contact_info, $nationality, $union_number, 
            $mother_name, $image_path, $email, $hashed_password, $yearsofex, $uni_name, $degree);
        

            if ($stmt->execute()) {
                $success_message = "Doctor added successfully!";
    
                // Send an email with login credentials
                $emailSent = sendEmailToDoctor(
                $fullname,"Welcome to novena, Dr. {$fullname}",
                "Welcome to Novena Healthcare<br>
                 <b>Dear Dr. {$fullname},</b><br>
                We are pleased to inform you that your profile has been successfully created on our medical appointment system.<br>
                You can now log in to your profile.<br><br>

                Your Login Details: <br>
                <ul>
                <li>Email: {$email}</li>
                <li>Temporary password: {$password}</li>
                <li><a href='http://localhost/senior/doctor'>localhost/senior/doctor</a></li>
                </ul><br><br>

                Please log in as soon as possible to change your password.<br><br>

                We are excited to have you as part of our medical team.<br><br>

                Best regards,<br>
                Novena"
                ,
                  true
                 );

             if ($emailSent) {
               //echo "Email sent to {$fullname}.";
            } else {
              //echo "Failed to send email.";
           }

            } else {
                $error_message = "Error adding doctor: " . $conn->error;
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
    <title>Add Doctor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        section {
            display: flex;
            width: 100%;
            max-width: 900px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            flex-direction: row;
        }

        .imgBx {
            width: 45%;
        }

        .imgBx img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .form-container {
            width: 55%;
            padding: 30px 25px;
            background-color: #ffffff;
        }

        .formBx h3 {
            font-size: 18px;
            color: #343a40;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-bottom: 1px solid #007bff; /* Blue border */
            padding-bottom: 6px;
            text-align: center;
        }

        .inputBx {
            margin-bottom: 15px;
            width: 100%;
        }

        .inputBx label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 5px;
        }

        .inputBx input {
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            color: #495057;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border 0.2s ease;
        }

        .inputBx input:focus {
            border-color: #007bff; /* Focus color blue */
            outline: none;
        }

        .inputBx input[type="submit"] {
            background-color: #007bff; /* Blue button */
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 14px;
            padding: 10px;
        }

        .inputBx input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            border-radius: 4px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .back-button input[type="submit"] {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            color: #fff;
            background-color: #1f2937;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .back-button input[type="submit"]:hover {
            background-color: #111827;
        }

        @media (max-width: 768px) {
            section {
                flex-direction: column;
                max-width: 95%;
            }

            .imgBx {
                width: 100%;
                height: 200px;
            }

            .form-container {
                width: 100%;
            }

            .formBx h3 {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <section>
        <div class="imgBx">
            <img src="Internal-Medicine.jpg" alt="Doctor Illustration">
        </div>
        <div class="form-container">
            <div class="formBx">
                <h3>Add Doctor</h3>

                <?php if (!empty($error_message)): ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="message success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form action="add_doctor.php" method="POST" enctype="multipart/form-data">
                    <div class="inputBx">
                        <label for="fullname">Full Name</label>
                        <input type="text" name="fullname" id="fullname" required>
                    </div>
                    <div class="inputBx">
                        <label for="specialty">Specialty</label>
                        <input type="text" name="specialty" id="specialty" required>
                    </div>
                    <div class="inputBx">
                        <label for="contact_info">Contact Info</label>
                        <input type="text" name="contact_info" id="contact_info" required>
                    </div>
                    <div class="inputBx">
                        <label for="nationality">Nationality</label>
                        <input type="text" name="nationality" id="nationality" required>
                    </div>
                    <div class="inputBx">
                        <label for="union_number">Union Number</label>
                        <input type="text" name="union_number" id="union_number" required>
                    </div>
                    <div class="inputBx">
                        <label for="mother_name">Mother's Name</label>
                        <input type="text" name="mother_name" id="mother_name" required>
                    </div>
                    <div class="inputBx">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="inputBx">
    <label for="yearsofex">Years of Experience</label>
    <input type="text" name="yearsofex" id="yearsofex" required>
</div>
<div class="inputBx">
    <label for="uni_name">University Name</label>
    <input type="text" name="uni_name" id="uni_name" required>
</div>
<div class="inputBx">
    <label for="degree">Degree</label>
    <input type="text" name="degree" id="degree" required>
</div>

                    <div class="inputBx">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="inputBx">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                    <div class="inputBx">
                        <label for="image">Upload Image</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                    </div>
                    <div class="inputBx">
                        <input type="submit" value="Add Doctor">
                    </div>
                </form>

                <form action="admin.php" method="get" class="back-button">
                    <input type="submit" value="Return to Dashboard">
                </form>
            </div>
        </div>
    </section>
</body>
</html>
