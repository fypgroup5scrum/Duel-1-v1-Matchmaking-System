<?php
session_start();
include 'db.php';

if (!isset($_SESSION['player_id'])) {
    header("Location: login_page.html");
    exit();
}

$id = $_SESSION['player_id'];
$query = mysqli_query($conn, "SELECT * FROM players WHERE player_id = '$id'");
$user = mysqli_fetch_assoc($query);

// Tambahan fungsi pengiraan rank automatik untuk diletakkan pada lobi utama
function getRankCategory($points) {
    if ($points <= 1500) return 'Bronze';
    if ($points <= 2000) return 'Silver';
    if ($points <= 2500) return 'Gold';
    return 'Platinum';
}
$lobby_rank = getRankCategory($user['skill_level']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RPS Arena - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;500&display=swap" rel="stylesheet">
  <style>
    body { background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%); color: #e94560; font-family: 'Inter', sans-serif; min-height: 100vh; overflow-x: hidden; }
    h1, h3, .navbar-brand, .btn-gaming { font-family: 'Orbitron', sans-serif; text-transform: uppercase; letter-spacing: 2px; }
    .gaming-card { background: rgba(22, 33, 62, 0.8); border: 2px solid #e94560; box-shadow: 0 0 15px rgba(233, 69, 96, 0.2); border-radius: 15px; }
    .btn-gaming { background: #e94560; color: #fff; border: none; padding: 12px 30px; font-weight: 700; transition: all 0.3s ease; box-shadow: 0 0 10px rgba(233, 69, 96, 0.4); }
    .btn-gaming:hover { background: #0f3460; transform: translateY(-2px); box-shadow: 0 0 20px #e94560; color: #fff; }
    .pulse { animation: neonPulse 1.5s infinite alternate; }
    @keyframes neonPulse { from { text-shadow: 0 0 5px #fff, 0 0 10px #e94560; } to { text-shadow: 0 0 10px #fff, 0 0 20px #0f3460; } }
    
    /* Custom style untuk keseragaman badge */
    .lobby-badge { padding: 4px 10px; border-radius: 5px; font-weight: bold; font-family: 'Orbitron'; }
    .badge-Bronze { background: #cd7f32; color: #fff; }
    .badge-Silver { background: #c0c0c0; color: #000; }
    .badge-Gold { background: #ffd700; color: #000; }
    .badge-Platinum { background: #00ffff; color: #000; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-transparent py-3">
    <div class="container">
      <a class="navbar-brand text-white fw-bold fs-3" href="#">RPS <span class="text-danger">ARENA</span></a>
      <div class="d-flex align-items-center">
        <a href="profile.php" class="text-decoration-none me-4 text-white fw-medium">
          🥷 <?php echo htmlspecialchars($user['username']); ?> 
          <span class="lobby-badge badge-<?php echo $lobby_rank; ?> ms-1"><?php echo $lobby_rank; ?></span>
        </a>
        <a href="profile.php" class="btn btn-outline-light btn-sm me-2 fw-bold" style="font-family: 'Orbitron';">PROFILE</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold" style="font-family: 'Orbitron';">LOGOUT</a>
      </div>
    </div>
  </nav>

  <div class="container d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 75vh;">
    <div class="gaming-card p-5 max-width-md w-100">
      <h1 class="display-4 text-white fw-bold mb-2">BATTLE <span class="text-danger pulse">LOBBY</span></h1>
      <p class="text-white-50 mb-4">Your Current MMR: <strong class="text-warning fs-5"><?php echo $user['mmr']; ?> MMR</strong></p>

      <div id="matchmaking-panel">
        <button id="btn-find" class="btn btn-gaming btn-lg w-100 py-3 mb-3" onclick="startMatchmaking()">FIND MATCH</button>
        
        <div id="queue-status" style="display: none;">
          <div class="d-flex justify-content-center align-items-center my-4">
            <div class="spinner-border text-danger me-3" role="status"></div>
            <h3 id="status-badge" class="mb-0 text-white">ENTERING QUEUE...</h3>
          </div>
          <p class="text-white-50">Time Elapsed: <span id="timer" class="text-warning fw-bold">00:00</span></p>
          <button class="btn btn-outline-secondary btn-sm mt-2" onclick="cancelMatchmaking()">CANCEL SEARCH</button>
        </div>
      </div>

      <div id="lobby-footer-actions" class="mt-4 border-top border-secondary pt-3 d-block">
        <a href="leaderboard.php" class="btn btn-outline-warning btn-sm fw-bold px-3">🏆 Global Leaderboard</a>
      </div>
    </div>
  </div>

  <script>
    let timerInterval;
    let checkMatchInterval;
    let seconds = 0;

    function startMatchmaking() {
      // 1. Tukar paparan panel butang lobi
      document.getElementById('btn-find').style.display = 'none';
      document.getElementById('queue-status').style.display = 'block';
      
      // 2. Sorokkan garisan & butang Global Leaderboard serta-merta
      document.getElementById('lobby-footer-actions').className = 'mt-4 border-top border-secondary pt-3 d-none';

      const formData = new FormData();
      formData.append('action', 'join');

      fetch('matchmaking.php', { method: 'POST', body: formData })
      .then(r => r.text())
      .then(data => {
        if (data.trim() === 'searching') {
          const badge = document.getElementById('status-badge');
          badge.textContent = 'SEARCHING...';
          badge.classList.add('pulse', 'text-info');

          // Reset timer ke 0 sebelum bermula
          seconds = 0;
          document.getElementById('timer').textContent = "00:00";

          timerInterval = setInterval(() => {
            seconds++;
            const mins = String(Math.floor(seconds/60)).padStart(2,'0');
            const secs = String(seconds%60).padStart(2,'0');
            document.getElementById('timer').textContent = `${mins}:${secs}`;
          }, 1000);

          checkMatchInterval = setInterval(checkStatus, 3000);
        }
      });
    }

    function checkStatus() {
      const formData = new FormData();
      formData.append('action', 'check_status');

      fetch('matchmaking.php', { method: 'POST', body: formData })
      .then(r => r.text())
      .then(data => {
        if (data.includes("match_found")) {
          const matchId = data.split(":")[1];
          clearInterval(timerInterval);
          clearInterval(checkMatchInterval);

          document.getElementById('status-badge').textContent = 'MATCH FOUND!';
          document.getElementById('status-badge').className = 'text-success fw-bold';
          
          setTimeout(() => {
            window.location.href = "match.php?match_id=" + matchId;
          }, 1500);
        }
      });
    }

    function cancelMatchmaking() {
      const formData = new FormData();
      formData.append('action', 'cancel');
      
      fetch('matchmaking.php', { method: 'POST', body: formData })
      .then(() => {
          // 1. Hentikan semua aktiviti loop timer dan semakan database
          clearInterval(timerInterval);
          clearInterval(checkMatchInterval);

          // 2. Kembalikan UI ke keadaan asal tanpa perlu reload page
          document.getElementById('queue-status').style.display = 'none';
          document.getElementById('btn-find').style.display = 'block';
          
          // 3. Paparkan semula garisan dan butang Global Leaderboard
          document.getElementById('lobby-footer-actions').className = 'mt-4 border-top border-secondary pt-3 d-block';
      });
    }
  </script>

</body>
</html>