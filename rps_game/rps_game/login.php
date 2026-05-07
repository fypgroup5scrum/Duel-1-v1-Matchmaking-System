<?php
session_start(); // Tambah ni kat baris 1
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM players WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) {
            // SIMPAN DATA DALAM SESSION
            $_SESSION['player_id'] = $row['player_id']; 
            echo "success";
        } else {
            echo "wrong_password";
        }
    } else {
        echo "user_not_found";
    }
}
?>