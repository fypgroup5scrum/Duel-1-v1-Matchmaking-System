<?php
session_start();
session_unset(); // Padam semua data session
session_destroy(); // Musnahkan session
header("Location: login_page.html"); // Balik ke login
exit();
?>