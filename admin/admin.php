<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db_connection.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f4f7;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .header {
            background-color: #003366; 
            padding: 25px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
        }

        .logout-button {
            background-color: #007bff; 
            color: #fff;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .logout-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .logout-button i {
            font-size: 1.2rem;
        }

    
        .panel {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            padding: 80px 15%;
        }

        .card {
            background-color: #ffffff;
            padding: 90px 40px;
            text-align: center;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            font-size: 2rem;
            font-weight: 700;
            color: #003366; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.15);
        }

        .card:active {
            background-color: #007bff; /* Blue background on card click */
            color: #fff; /* White text on click */
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .logout-button {
                padding: 12px 20px;
                font-size: 1rem;
            }

            .panel {
                padding: 60px 5%;
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .card {
                font-size: 1.5rem;
                padding: 70px 30px;
            }
        }

        @media (max-width: 480px) {
            .panel {
                grid-template-columns: 1fr;
                padding: 50px 3%;
            }

            .card {
                font-size: 1.2rem;
                padding: 60px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Admin Panel</h1>
        <a href="logout.php" class="logout-button" title="Logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="panel">
        <a href="manage_doctor.php" class="card">Manage Doctor</a>
        <a href="add_doctor.php" class="card">Add Doctor</a>
        <a href="manage_sec.php" class="card">Manage Secretary</a>
        <a href="add_secretary.php" class="card">Add Secretary</a>
    </div>

</body>
</html>
