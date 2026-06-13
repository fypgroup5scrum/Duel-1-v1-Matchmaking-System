<?php
session_start();
include 'db.php'; // Sambung ke database rps_game kau

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    // Ambil data admin berdasarkan username
    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Semak menggunakan SHA256 (Teks biasa di-hash lalu dibandingkan dengan database)
        if (hash('sha256', $password) === $admin['password']) {
            // Set Session Admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            
            echo "success";
            exit;
        } else {
            echo "wrong_password";
            exit;
        }
    } else {
        echo "user_not_found";
        exit;
    }
} else {
    echo "invalid_request";
    exit;
}
?>