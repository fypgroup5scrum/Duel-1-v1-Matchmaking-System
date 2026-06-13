<?php
session_start();
// FIX: Mesti set timezone yang sama untuk perbandingan waktu NOW()
date_default_timezone_set('Asia/Kuala_Lumpur');

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_code = mysqli_real_escape_string($conn, $_POST['input_code']);
    $email = $_SESSION['reset_email'] ?? '';

    // Ambil data user berdasarkan emel
    $query = "SELECT * FROM players WHERE email = '$email' AND reset_code = '$input_code'";
    $res = mysqli_query($conn, $query);

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $currentTime = date("Y-m-d H:i:s");
        $expiryTime = $row['code_expiry'];

        // Semak jika waktu sekarang belum tamat tempoh
        if ($currentTime <= $expiryTime) {
            $_SESSION['code_verified'] = true;
            header("Location: reset_password.php");
        } else {
            echo "<script>alert('THE CODE HAS EXPIRED! Please request a new code.'); window.location.href='forgot_password.html';</script>";
        }
    } else {
        echo "<script>alert('KOD SALAH!'); history.back();</script>";
    }
}
?>