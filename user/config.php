<?php
define("db_SERVER", "localhost");
define("db_USER", "root");
define("db_PASSWORD", "");
define("db_DNAME", "senior"); // Corrected database name from 'senior' to 'senior'

$conn = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DNAME);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error()); // Log the error
    exit(); 
}
?>