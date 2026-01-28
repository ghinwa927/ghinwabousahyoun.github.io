<?php
include('db_connection.php');

$query = "SELECT id, fullname, email, contact_info, nationality, mother_name, yearsofex, univeristy FROM secretary"; 
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Secretaries</title>
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

        .dashboard-button {
            background-color: #007bff; 
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .dashboard-button:hover {
            background-color: #0056b3; 
        }

        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2f4050; 
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            text-decoration: none;
            color: #007bff; 
            font-weight: bold;
        }

        a:hover {
            color: #0056b3; 
        }

        .action-links {
            font-size: 14px;
        }

        .edit-link {
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Manage Secretaries</h1>
        <a href="admin.php" class="dashboard-button">Return to Dashboard</a>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact Info</th>
                    <th>Nationality</th>
                    <th>Mother's Name</th>
                    <th>Years of Experience</th>
                    <th>University</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    // Loop through each secretary and display them
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['contact_info']; ?></td>
                            <td><?php echo $row['nationality']; ?></td>
                            <td><?php echo $row['mother_name']; ?></td>
                            <td><?php echo $row['yearsofex']; ?></td>
                            <td><?php echo $row['univeristy']; ?></td>
                            <td class="action-links">
                                <a href="edit_sec.php?id=<?php echo $row['id']; ?>" class="edit-link">Edit</a>
                            </td>
                        </tr>
                    <?php }
                } else {
                    echo "<tr><td colspan='9' style='text-align:center;'>No secretaries found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
