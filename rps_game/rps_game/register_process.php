<?php
// Sambungkan ke database (Gunakan fail db.php yang kita buat tadi)
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari borang HTML
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    // Nilai default untuk pemain baru (seperti dalam imej database anda)
    $default_mmr = 1000;

    // Perintah SQL untuk masukkan data
    // player_id tidak perlu dimasukkan kerana ia AUTO_INCREMENT
    // created_at akan automatik jika anda set CURRENT_TIMESTAMP di database
    $sql = "INSERT INTO players (username, email, password, mmr) 
            VALUES ('$user', '$email', '$pass', '$default_mmr')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Pendaftaran Berjaya!');
                window.location.href = 'login_page.html';
              </script>";
    } else {
        echo "Ralat: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Tutup sambungan
mysqli_close($conn);
?>