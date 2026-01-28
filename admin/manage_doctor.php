<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$sql = "SELECT doctor_id, fullname, specialty, contact_info, status, nationality, mother_name, union_number, email, yearsofex, uni_name, degree FROM doctors";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        /* Your CSS unchanged */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #003366;
            color: white;
            padding: 30px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            letter-spacing: 1px;
        }
        .logout-button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: #0056b3;
        }
        .container {
            width: 95%;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
            table-layout: auto;
            word-wrap: break-word;
        }
        th, td {
            padding: 16px 12px;
            text-align: left;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        th {
            background-color: #003366;
            color: white;
            font-size: 17px;
            letter-spacing: 0.5px;
        }
        td {
            background-color: #ffffff;
        }
        tr:nth-child(even) td {
            background-color: #f7f9fb;
        }
        tr:hover td {
            background-color: #eef2f7;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            color: #0056b3;
        }
        .edit-link {
            margin-right: 10px;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .edit-link:hover {
            background-color: #218838;
        }
        @media screen and (max-width: 1024px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th {
                text-align: left;
                background-color: #002244;
            }
            td {
                padding-left: 50%;
                position: relative;
            }
            td::before {
                position: absolute;
                left: 12px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Manage Doctors</h1>
    <form action="admin.php"  method="POST">
        <button type="submit" name="logout" class="logout-button">Return to Dashboard</button>
    </form>
</div>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>Doctor ID</th>
                <th>Name</th>
                <th>Specialty</th>
                <th>Contact Info</th>
                <th>Status</th>
                <th>Nationality</th>
                <th>Mother's Name</th>
                <th>Union Number</th>
                <th>Email</th>
                <th>Years of Experience</th>
                <th>University</th>
                <th>Degree</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['doctor_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialty']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['nationality']); ?></td>
                        <td><?php echo htmlspecialchars($row['mother_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['union_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['yearsofex']); ?></td>
                        <td><?php echo htmlspecialchars($row['uni_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['degree']); ?></td>
                        <td>
                            <a href="edit_doctor.php?id=<?php echo urlencode($row['doctor_id']); ?>" class="edit-link">Edit</a>
                        </td>
                    </tr>
                <?php }
            } else {
                echo "<tr><td colspan='13'>No doctors found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
