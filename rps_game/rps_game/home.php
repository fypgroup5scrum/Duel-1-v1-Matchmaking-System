<?php
session_start();
include 'db.php';

// Pastikan user sudah log masuk, jika tidak hantar ke login page
if (!isset($_SESSION['player_id'])) {
    header("Location: login_page.html");
    exit();
}

$id = $_SESSION['player_id'];
$query = mysqli_query($conn, "SELECT * FROM players WHERE player_id = '$id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home - RPS Game</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">Rock, Paper, Scissors</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" href="home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
      </ul>
    </div>
    <span class="navbar-text text-white">
      Selamat kembali, <strong><?php echo $user['username']; ?></strong>!
    </span>
  </nav>

  <div class="container mt-5 text-center">
    <h1>Selamat Datang ke RPS Game</h1>
    <p class="lead">Sedia untuk meningkatkan MMR anda hari ini?</p>
    <hr>
    <div class="mt-4">
        <button class="btn btn-primary btn-lg">Mula Bermain</button>
    </div>
  </div>

</body>
</html>