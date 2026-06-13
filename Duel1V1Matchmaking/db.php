<?php
$host = "sql206.infinityfree.com";
$user = "if0_41849028";
$pass = "Test121006";
$db   = "if0_41849028_rps_game";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>