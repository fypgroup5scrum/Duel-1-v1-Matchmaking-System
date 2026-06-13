<?php
session_start();
$_SESSION = array(); // Kosongkan array session
session_destroy();   // Hancurkan fungsi session aktif

header("Location: admin_login.html"); // Hantar balik ke skrin log masuk admin
exit;
?>