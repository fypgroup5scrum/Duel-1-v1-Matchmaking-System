<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user  = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $plain_pass = $_POST['password'];
    $hashed_pass = password_hash($plain_pass, PASSWORD_BCRYPT);

    $default_mmr = 1000;

    // Check if username or email already exists
    $check = mysqli_query($conn, "SELECT player_id FROM players WHERE username = '$user' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Username or email already taken.'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO players (username, email, password, mmr) 
            VALUES ('$user', '$email', '$hashed_pass', '$default_mmr')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Registration successful!');
                window.location.href = 'login_page.html';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
