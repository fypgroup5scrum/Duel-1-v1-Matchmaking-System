<?php
session_start();
include 'db.php';

if (!isset($_SESSION['player_id'])) {
    header("Location: login_page.html");
    exit();
}

$player_id = $_SESSION['player_id'];
$query = mysqli_query($conn, "SELECT * FROM players WHERE player_id = '$player_id'");
$user = mysqli_fetch_assoc($query);

// Ambil match_id dari URL jika ada
$match_id_from_url = $_GET['match_id'] ?? null;

// Fetch opponent info if match_id is in URL
$opponent = null;
if ($match_id_from_url) {
    $match_res = mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id_from_url'");
    $match_row = mysqli_fetch_assoc($match_res);
    if ($match_row) {
        $opp_id = ($match_row['player1_id'] == $player_id) ? $match_row['player2_id'] : $match_row['player1_id'];
        $opp_res = mysqli_query($conn, "SELECT * FROM players WHERE player_id = '$opp_id'");
        $opponent = mysqli_fetch_assoc($opp_res);
    }
}

function getRankLabel($mmr) {
    if ($mmr <= 500) return 'Bronze';
    if ($mmr <= 1200) return 'Silver';
    if ($mmr <= 2000) return 'Gold';
    return 'Platinum';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Match - RPS Game</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%);
      color: #e94560;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }
    h1, h3, h5, .navbar-brand {
      font-family: 'Orbitron', sans-serif;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    .navbar {
      background: rgba(22, 33, 62, 0.95) !important;
      border-bottom: 2px solid #0f3460;
    }
    .gaming-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 3rem;
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    }
    .choice-btn {
      width: 150px; height: 150px; border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(255, 255, 255, 0.05);
      cursor: pointer; transition: all 0.3s ease;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      color: white; font-family: 'Orbitron';
    }
    .choice-btn:hover:not(:disabled), .choice-btn.selected {
      border-color: #00d2ff;
      box-shadow: 0 0 30px rgba(0, 210, 255, 0.4);
      color: #00d2ff;
      transform: translateY(-5px);
    }
    .choice-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .result-win { color: #00d2ff; text-shadow: 0 0 20px rgba(0, 210, 255, 0.5); }
    .result-lose { color: #e94560; text-shadow: 0 0 20px rgba(233, 69, 96, 0.5); }
    .btn-gaming { padding: 12px 36px; border-radius: 50px; font-family: 'Orbitron'; font-weight: bold; text-transform: uppercase; border: none; transition: 0.3s; }
    .btn-find { background: linear-gradient(45deg, #00d2ff, #3a7bd5); color: white; }
    .btn-find:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(0, 210, 255, 0.4); }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg px-4">
    <a class="navbar-brand text-info" href="#">RPS ARENA</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link text-white" href="home.php">Home</a></li>
      </ul>
      <span class="navbar-text text-white">
        PLAYER: <span class="text-info"><?php echo strtoupper($user['username']); ?></span>
        <span class="badge bg-info text-dark ms-2">MMR: <?php echo $user['mmr'] ?? $user['skill_level']; ?></span>
      </span>
    </div>
  </nav>

  <div class="container mt-5 text-center">
    <div class="gaming-card mx-auto" style="max-width:700px;">

      <div id="game-section" style="display: <?php echo $match_id_from_url ? 'block' : 'none'; ?>;">
        <h3 class="mb-1" style="color:#00d2ff;">Match Found!</h3>
        <p class="text-white-50 small mb-4">Pick your move before time runs out</p>

        <!-- VS Banner -->
        <?php if ($opponent): ?>
        <div class="d-flex justify-content-center align-items-center gap-4 mb-4">
          <!-- You -->
          <div class="text-center">
            <div style="font-family:'Orbitron';font-size:13px;color:#00d2ff;"><?php echo strtoupper($user['username']); ?></div>
            <div style="font-size:11px;color:rgba(255,255,255,0.4);">
              <?php echo getRankLabel($user['mmr']); ?> · <?php echo $user['mmr']; ?> MMR
            </div>
          </div>
          <!-- VS -->
          <div style="font-family:'Orbitron';font-size:18px;color:#e94560;text-shadow:0 0 10px #e94560;">VS</div>
          <!-- Opponent -->
          <div class="text-center">
            <div style="font-family:'Orbitron';font-size:13px;color:#e94560;"><?php echo strtoupper($opponent['username']); ?></div>
            <div style="font-size:11px;color:rgba(255,255,255,0.4);">
              <?php echo getRankLabel($opponent['mmr']); ?> · <?php echo $opponent['mmr']; ?> MMR
            </div>
          </div>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-4 mb-5">
          <button class="choice-btn" onclick="pick('rock', this)">
            <span style="font-size:50px;">🪨</span> Rock
          </button>
          <button class="choice-btn" onclick="pick('paper', this)">
            <span style="font-size:50px;">📄</span> Paper
          </button>
          <button class="choice-btn" onclick="pick('scissors', this)">
            <span style="font-size:50px;">✂️</span> Scissors
          </button>
        </div>

        <div id="waiting-msg" style="display:none;" class="mb-3">
          <div class="spinner-border text-info spinner-border-sm me-2"></div>
          <span style="color:#00d2ff;" class="small">Waiting for opponent to pick...</span>
        </div>

        <div id="result-box" style="display:none;">
          <p class="text-white-50 small mb-2">
            You: <strong id="player-pick" class="text-white"></strong> vs Opponent: <strong id="opponent-pick" class="text-white"></strong>
          </p>
          <h3 id="result-text" class="mb-4"></h3>
          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn-gaming btn-find" onclick="goQueue()">Find New Match</button>
            <a href="home.php" class="btn-gaming" style="padding:12px 36px;border-radius:50px;font-family:'Orbitron',sans-serif;font-weight:bold;font-size:13px;letter-spacing:1px;text-transform:uppercase;border:2px solid #e94560;color:#e94560;background:transparent;text-decoration:none;transition:all 0.3s ease;"
              onmouseover="this.style.background='#e94560';this.style.color='white';"
              onmouseout="this.style.background='transparent';this.style.color='#e94560';">
              Back to Home
            </a>
          </div>
        </div>
      </div>

      <div id="queue-section" style="display: <?php echo $match_id_from_url ? 'none' : 'block'; ?>;">
        <h3 class="mb-4" style="color:#00d2ff;">Matchmaking</h3>
        <div id="status-badge" class="mb-4" style="padding:10px; border-radius:10px; background:rgba(0,0,0,0.3); color:#aaa; border: 1px solid rgba(255,255,255,0.1);">
          Not in Queue
        </div>

        <div id="timer-box" style="display:none;" class="my-4">
          <p class="text-white-50 small mb-1">Searching for a worthy opponent...</p>
          <div id="timer" style="font-size:4rem; color:#00d2ff; font-family:'Orbitron'; text-shadow: 0 0 15px #00d2ff;">00:00</div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
          <button id="find-btn" class="btn-gaming btn-find" onclick="startQueue()">Find Match</button>
          <button id="cancel-btn" class="btn-gaming" style="display:none; background:transparent; border:2px solid #e94560; color:#e94560;" onclick="cancelQueue()">Cancel</button>
        </div>
      </div>

    </div>
  </div>

  <script>
    const emojis = { rock: '🪨', paper: '📄', scissors: '✂️' };
    const params = new URLSearchParams(window.location.search);
    const matchId = params.get('match_id');
    let pollInterval = null;
    let timerInterval = null;
    let t = 0;
    let hasPicked = false;

    // --- LOGIK QUEUE ---
    function startQueue() {
      t = 0;
      document.getElementById('find-btn').style.display = 'none';
      document.getElementById('cancel-btn').style.display = 'inline-block';
      document.getElementById('timer-box').style.display = 'block';
      document.getElementById('status-badge').textContent = 'Searching...';
      document.getElementById('status-badge').style.color = '#00d2ff';

      timerInterval = setInterval(() => {
        t++;
        document.getElementById('timer').textContent = 
          String(Math.floor(t / 60)).padStart(2, '0') + ':' + String(t % 60).padStart(2, '0');
      }, 1000);

      const fd = new FormData();
      fd.append('action', 'join');
      fetch('matchmaking.php', { method: 'POST', body: fd });

      pollInterval = setInterval(checkQueue, 2000);
    }

    function checkQueue() {
      const fd = new FormData();
      fd.append('action', 'check_status');
      fetch('matchmaking.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(data => {
          if (data.includes('match_found')) {
            const mId = data.split(':')[1];
            clearInterval(pollInterval);
            clearInterval(timerInterval);
            
            document.getElementById('status-badge').textContent = 'MATCH FOUND!';
            document.getElementById('status-badge').style.color = '#00ff88';

            setTimeout(() => {
              window.location.replace('match.php?match_id=' + mId);
            }, 1000);
          }
        });
    }

    function cancelQueue() {
      clearInterval(timerInterval);
      clearInterval(pollInterval);
      const fd = new FormData();
      fd.append('action', 'cancel');
      fetch('matchmaking.php', { method: 'POST', body: fd })
        .then(() => window.location.replace('match.php'));
    }

    // --- LOGIK GAMEPLAY ---
    function pick(playerMove, btn) {
      if (hasPicked || !matchId) return;
      hasPicked = true;
      
      document.querySelectorAll('.choice-btn').forEach(b => {
        b.disabled = true;
        b.classList.remove('selected');
      });
      btn.classList.add('selected');

      const fd = new FormData();
      fd.append('match_id', matchId);
      fd.append('move', playerMove);

      fetch('game_action.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.status === 'complete') {
            showResult(data);
          } else {
            document.getElementById('waiting-msg').style.display = 'block';
            pollInterval = setInterval(checkResult, 2000);
          }
        });
    }

    function checkResult() {
      const fd = new FormData();
      fd.append('match_id', matchId);
      fd.append('move', 'check');

      fetch('game_action.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data.status === 'complete') {
            clearInterval(pollInterval);
            showResult(data);
          }
        });
    }

    function showResult(data) {
      const myMove = data.is_p1 ? data.p1_move : data.p2_move;
      const oppMove = data.is_p1 ? data.p2_move : data.p1_move;

      document.getElementById('waiting-msg').style.display = 'none';
      document.getElementById('player-pick').textContent = emojis[myMove] + ' ' + myMove.toUpperCase();
      document.getElementById('opponent-pick').textContent = emojis[oppMove] + ' ' + oppMove.toUpperCase();
      
      let resText = '', resClass = '';
      if (myMove === oppMove) { 
          resText = "TACTICAL DRAW"; 
          resClass = 'text-secondary'; 
      } else if (
        (myMove === 'rock' && oppMove === 'scissors') ||
        (myMove === 'paper' && oppMove === 'rock') ||
        (myMove === 'scissors' && oppMove === 'paper')
      ) { 
          resText = "VICTORY!"; 
          resClass = 'result-win'; 
      } else { 
          resText = "DEFEAT!"; 
          resClass = 'result-lose'; 
      }

      document.getElementById('result-text').textContent = resText;
      document.getElementById('result-text').className = 'mb-4 ' + resClass;
      document.getElementById('result-box').style.display = 'block';
    }

    function goQueue() {
      window.location.replace('match.php');
    }
  </script>
</body>
</html>