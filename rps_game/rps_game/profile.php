<?php
session_start();
include 'db.php';

// Perlindungan Session: Kalau tak login, tak boleh tengok profile
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
  <title>Profile - RPS Game</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">Rock, Paper, Scissors</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
      </ul>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm p-4">
          <h5 class="mb-4 text-center">My Profile</h5>
          
          <label class="text-muted small">Username</label>
          <p class="fw-bold border-bottom pb-2"><?php echo $user['username']; ?></p>

          <label class="text-muted small">Email Address</label>
          <p class="fw-bold border-bottom pb-2"><?php echo $user['email']; ?></p>

          <label class="text-muted small">Current MMR</label>
          <!-- MMR 1200 atau 1000 akan keluar sini ikut database -->
          <p class="fw-bold text-primary fs-4 border-bottom pb-2"><?php echo $user['mmr']; ?></p>

          <label class="text-muted small">Joined</label>
          <p class="text-secondary small"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>

          <!-- BUTANG LOGOUT DI SINI -->
          <div class="mt-3">
             <a href="logout.php" class="btn btn-danger w-100">Logout</a>
          </div>
          
        </div>
      </div>
    </div>
  </div>

</body>
</html>