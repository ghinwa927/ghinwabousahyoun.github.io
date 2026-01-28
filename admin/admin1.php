<?php

include 'db_connection.php';

$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$password='adminadmin';
$hashedPassword=password_hash($password,PASSWORD_DEFAULT);


echo "Hashed password:".$hashedPassword;

$sql="INSERT into admin(email,password)values('admin@gmail.com','$hashedPassword')";
$RESULT=mysqli_query($conn  ,$sql);

?>