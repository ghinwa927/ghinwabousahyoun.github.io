<?php
define("db_SERVER","localhost");
define("db_USER","root");
define("db_PASSWORD","");
define("db_DNAME","senior");

$conn=mysqli_connect(db_SERVER,db_USER,db_PASSWORD,db_DNAME);
if(!$conn){
    echo'<script>alert("error connecting the server".mysqli_connect_error()</script>';
}

?>