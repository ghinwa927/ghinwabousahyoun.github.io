<?php
include('db_connection.php');

$doctor_id = $_GET['id'] ?? null;

if (!$doctor_id) {
    echo "Doctor ID missing.";
    exit();
}

// Fetch doctor
$sql = "SELECT * FROM doctors WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Doctor not found!";
    exit();
}

$doctor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $specialty = $_POST['specialty'];
    $contact_info = $_POST['contact_info'];
    $status = $_POST['status'];
    $nationality = $_POST['nationality'];
    $email = $_POST['email'];
    $yearsofex = $_POST['yearsofex'];
    $uni_name = $_POST['uni_name'];
    $degree = $_POST['degree'];

    $update_sql = "UPDATE doctors SET fullname = ?, specialty = ?, contact_info = ?, status = ?, nationality = ?, email = ?, yearsofex = ?, uni_name = ?, degree = ? WHERE doctor_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssi", $fullname, $specialty, $contact_info, $status, $nationality, $email, $yearsofex, $uni_name, $degree, $doctor_id);

    if ($update_stmt->execute()) {
        header("Location: manage_doctor.php");
        exit();
    } else {
        echo "Error updating doctor information: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .edit-container {
            width: 100%;
            max-width: 600px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border 0.2s ease-in-out;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group input[disabled] {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }

        .form-actions {
            text-align: right;
        }

        .form-actions button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .form-actions button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="edit-container">
    <h2>Edit Doctor</h2>
    <form method="POST">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($doctor['fullname']); ?>" required>
        </div>

        <div class="form-group">
            <label for="specialty">Specialty</label>
            <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($doctor['specialty']); ?>" required>
        </div>

        <div class="form-group">
            <label for="contact_info">Contact Info</label>
            <input type="text" id="contact_info" name="contact_info" value="<?php echo htmlspecialchars($doctor['contact_info']); ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="active" <?php echo ($doctor['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($doctor['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="form-group">
            <label for="nationality">Nationality</label>
            <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($doctor['nationality']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="union_number">Union Number</label>
            <input type="text" id="union_number" name="union_number" value="<?php echo htmlspecialchars($doctor['union_number']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="yearsofex">Years of Experience</label>
            <input type="number" id="yearsofex" name="yearsofex" value="<?php echo htmlspecialchars($doctor['yearsofex']); ?>" required>
        </div>

        <div class="form-group">
            <label for="uni_name">University Name</label>
            <input type="text" id="uni_name" name="uni_name" value="<?php echo htmlspecialchars($doctor['uni_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="degree">Degree</label>
            <input type="text" id="degree" name="degree" value="<?php echo htmlspecialchars($doctor['degree']); ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit">Update</button>
        </div>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
