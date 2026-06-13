<?php
session_start();
// FIX: Set waktu Malaysia supaya kod tidak expired serta-merta
date_default_timezone_set('Asia/Kuala_Lumpur');

include 'db.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $res = mysqli_query($conn, "SELECT * FROM players WHERE email = '$email'");

    if (mysqli_num_rows($res) > 0) {
        $code = rand(100000, 999999);
        // Waktu sekarang + 15 minit
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));
        
        // Simpan ke database
        mysqli_query($conn, "UPDATE players SET reset_code = '$code', code_expiry = '$expiry' WHERE email = '$email'");
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ajwadnabil767@gmail.com'; // TUKAR SINI
            $mail->Password   = 'ceqe ybqc hbrg oxsz';    // TUKAR SINI
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('ajwadnabil767@gmail.com', 'RPS Arena Support');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Security Code: ' . $code;
            $mail->Body    = "Kod pengesahan anda ialah: <b style='font-size:24px;'>$code</b><br>Sah sehingga: $expiry";

            $mail->send();
            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
        } catch (Exception $e) { echo "Mailer Error: {$mail->ErrorInfo}"; }
    } else { echo "<script>alert('Email not found!'); history.back();</script>"; }
}
?>