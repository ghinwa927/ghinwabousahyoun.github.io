<?php
include('db_connection.php');

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // Fetching existing data including nationality, years of experience, and university
    $query = "SELECT id, fullname, email, nationality, yearsofex, univeristy FROM secretary WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Secretary not found!";
        exit;
    }
} else {
    echo "Invalid or missing ID!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $nationality = htmlspecialchars(trim($_POST['nationality']));
    $yearsofex = htmlspecialchars(trim($_POST['yearsofex']));
    $university = htmlspecialchars(trim($_POST['univeristy']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit;
    }

    // Update query for secretary including the new fields
    $updateQuery = "UPDATE secretary SET fullname = ?, email = ?, nationality = ?, yearsofex = ?, univeristy = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $fullname, $email, $nationality, $yearsofex, $university, $id);

    if ($updateStmt->execute()) {
        header("Location: manage_sec.php");
        exit;
    } else {
        echo "Error updating secretary.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Secretary</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #2f4050;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .container {
            width: 50%;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>Edit Secretary</h1>
    </div>

    <!-- Form Container -->
    <div class="container">
        <form method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($row['fullname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>

            <label for="nationality">Nationality:</label>
            <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($row['nationality']); ?>" required>

            <label for="yearsofex">Years of Experience:</label>
            <input type="text" id="yearsofex" name="yearsofex" value="<?php echo htmlspecialchars($row['yearsofex']); ?>" required>

            <label for="univeristy">University:</label>
            <input type="text" id="univeristy" name="univeristy" value="<?php echo htmlspecialchars($row['univeristy']); ?>" required>

            <button type="submit">Update Secretary</button>
        </form>
    </div>

</body>
</html>

<?php
$conn->close();
?>
