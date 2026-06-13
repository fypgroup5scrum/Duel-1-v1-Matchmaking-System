<?php
session_start();
// 1. Sekat penceroboh jika tiada session admin aktif
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit;
}

// 2. Sambungan ke Database (Pastikan nama fail db kau betul)
include 'db.php'; 

// 3. Tarik data untuk bahagian "Stats Row" (Mengikut jadual kau)
$total_players_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM players");
$total_players = mysqli_fetch_assoc($total_players_query)['total'];

$total_matches_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM matches");
$total_matches = mysqli_fetch_assoc($total_matches_query)['total'];

$active_queue_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM queue");
$active_queue = mysqli_fetch_assoc($active_queue_query)['total'];

// 4. KEMAS KINI FUNGSI: Mengira Kategori Rank mengikut logik MMR baru kau
function getRankCategory($points) {
    if ($points < 1500) return 'Bronze';     // 1000 hingga 1499 MMR
    if ($points < 2000) return 'Silver';     // 1500 hingga 1999 MMR
    if ($points < 2500) return 'Gold';       // 2000 hingga 2499 MMR
    return 'Platinum';                       // 2500 MMR ke atas
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RPS Arena - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;500&display=swap" rel="stylesheet">
  
  <style>
    body {
      background: radial-gradient(circle at center, #1a0a0a 0%, #2e0f0f 100%) !important;
      color: #ffffff !important;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    h1, h3, h5, h6, .navbar-brand, .rank-header {
      font-family: 'Orbitron', sans-serif;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .navbar {
      background: rgba(46, 15, 15, 0.95) !important;
      backdrop-filter: blur(10px);
      border-bottom: 2px solid #e94560;
    }

    .gaming-card {
      background: rgba(255, 255, 255, 0.03) !important;
      backdrop-filter: blur(15px);
      border: 1px solid rgba(233, 69, 96, 0.2);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
    }

    .stat-card {
      background: rgba(233, 69, 96, 0.08) !important;
      border: 1px solid rgba(233, 69, 96, 0.2) !important;
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(233, 69, 96, 0.2);
    }

    .stat-number {
      font-family: 'Orbitron', sans-serif;
      font-size: 2.5rem;
      font-weight: 700;
      color: #e94560;
      text-shadow: 0 0 10px rgba(233, 69, 96, 0.5);
    }

    .stat-label {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.6);
      margin-top: 0.5rem;
    }

    .admin-tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 2rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 10px;
    }

    .admin-tab {
      padding: 8px 24px;
      border-radius: 50px;
      font-family: 'Orbitron', sans-serif;
      font-size: 0.8rem;
      font-weight: 700;
      border: 1px solid rgba(233, 69, 96, 0.3);
      background: transparent;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .admin-tab:hover, .admin-tab.active {
      background: rgba(233, 69, 96, 0.15);
      border-color: #e94560;
      color: #e94560;
      box-shadow: 0 0 10px rgba(233, 69, 96, 0.2);
    }

    .tab-section {
      display: none;
    }

    .tab-section.active {
      display: block;
    }

    .admin-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .admin-table th {
      font-family: 'Orbitron', sans-serif;
      font-size: 0.8rem;
      color: #e94560;
      padding: 12px 16px;
      border-bottom: 2px solid rgba(233, 69, 96, 0.3);
      text-transform: uppercase;
    }

    .admin-table td {
      padding: 14px 16px;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.8);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background: transparent !important;
    }

    .admin-table tbody tr:hover td {
      background: rgba(255, 255, 255, 0.02) !important;
    }

    .rank-badge {
      padding: 4px 12px;
      border-radius: 4px;
      font-family: 'Orbitron', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      display: inline-block;
    }

    .rank-bronze { background: rgba(205, 127, 50, 0.2); color: #cd7f32; border: 1px solid #cd7f32; }
    .rank-silver { background: rgba(192, 192, 192, 0.2); color: #b0c4de; border: 1px solid #b0c4de; }
    .rank-gold { background: rgba(255, 215, 0, 0.2); color: #ffd700; border: 1px solid #ffd700; }
    .rank-platinum { background: rgba(0, 229, 255, 0.2); color: #00e5ff; border: 1px solid #00e5ff; }

    .btn-logout {
      background: transparent;
      border: 2px solid #e94560;
      color: #e94560;
      font-family: 'Orbitron', sans-serif;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 6px 20px;
      border-radius: 50px;
      transition: all 0.3s ease;
      text-transform: uppercase;
    }

    .btn-logout:hover {
      background: #e94560;
      color: #ffffff;
      box-shadow: 0 0 15px rgba(233, 69, 96, 0.4);
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg px-4 py-3">
    <div class="container-fluid">
      <a class="navbar-brand text-white fw-bold" href="#">RPS ARENA <span class="text-danger">ADMIN</span></a>
      <div class="collapse navbar-collapse justify-content-end">
        <span class="navbar-text d-flex align-items-center gap-3 text-white">
          WELCOME, <span class="text-danger fw-bold"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
          <button onclick="confirmLogout()" class="btn btn-logout">Logout</button>
        </span>
      </div>
    </div>
  </nav>

  <div class="container mt-4 pb-5">
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="stat-card">
          <div class="stat-number"><?php echo $total_players; ?></div>
          <div class="stat-label">Total Players Registered</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="stat-card">
          <div class="stat-number"><?php echo $total_matches; ?></div>
          <div class="stat-label">Total Matches Played</div>
        </div>
      </div>

    <div class="gaming-card">
      <div class="admin-tabs">
        <button class="admin-tab active" onclick="switchTab('players')">Registered Players</button>
        <button class="admin-tab" onclick="switchTab('stats')">Match Analytics</button>
        <button class="admin-tab" onclick="switchTab('matches')">Global Match Logs</button>
      </div>

      <div id="tab-players" class="tab-section active">
        <h5 class="text-danger mb-4">Player Accounts</h5>
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Player ID</th>
                <th>Username</th>
                <th>Email Address</th>
                <th>MMR Points</th> <th>Rank Tier</th>
                <th>Account Created</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // FIXED QUERY: Diubah daripada ORDER BY skill_level kepada ORDER BY mmr
              $players_query = mysqli_query($conn, "SELECT * FROM players ORDER BY mmr DESC");
              if (mysqli_num_rows($players_query) > 0) {
                  while($row = mysqli_fetch_assoc($players_query)) {
                      // FIXED VARIABLE: Menggunakan column mmr yang betul
                      $current_rank = getRankCategory($row['mmr']);
                      $badge_class = 'rank-' . strtolower($current_rank);
                      
                      echo "<tr>";
                      echo "<td>#" . $row['player_id'] . "</td>";
                      echo "<td class='fw-bold text-white'>" . htmlspecialchars($row['username']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                      echo "<td class='text-warning fw-bold'>" . $row['mmr'] . "</td>"; // Mengambil data mmr
                      echo "<td><span class='rank-badge " . $badge_class . "'>" . $current_rank . "</span></td>";
                      echo "<td>" . $row['created_at'] . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' class='text-center text-white-50 py-4'>No players registered yet.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="tab-stats" class="tab-section">
        <h5 class="text-danger mb-4">Player Performance Stats</h5>
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Current MMR</th> <th>Tier</th>
                <th>Wins / Losses / Draws</th>
                <th>Win Rate (%)</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // FIXED QUERY: Diubah rujukan p.skill_level kepada p.mmr
              $stats_query = "
                SELECT p.player_id, p.username, p.mmr,
                  (SELECT COUNT(*) FROM match_history WHERE player_id = p.player_id AND result = 'win') as wins,
                  (SELECT COUNT(*) FROM match_history WHERE player_id = p.player_id AND result = 'loss') as losses,
                  (SELECT COUNT(*) FROM match_history WHERE player_id = p.player_id AND result = 'draw') as draws
                FROM players p 
                ORDER BY p.mmr DESC
              ";
              
              $stats_result = mysqli_query($conn, $stats_query);
              if (mysqli_num_rows($stats_result) > 0) {
                  while($row = mysqli_fetch_assoc($stats_result)) {
                      $total_games = $row['wins'] + $row['losses'] + $row['draws'];
                      $win_rate = ($total_games > 0) ? round(($row['wins'] / $total_games) * 100, 1) . '%' : '0%';
                      
                      // FIXED VARIABLE: Menggunakan column mmr yang betul
                      $current_rank = getRankCategory($row['mmr']);
                      $badge_class = 'rank-' . strtolower($current_rank);

                      echo "<tr>";
                      echo "<td class='fw-bold text-white'>" . htmlspecialchars($row['username']) . "</td>";
                      echo "<td class='text-warning fw-bold'>" . $row['mmr'] . "</td>"; // Mengambil data mmr
                      echo "<td><span class='rank-badge " . $badge_class . "'>" . $current_rank . "</span></td>";
                      echo "<td>
                              <span class='text-success'>" . $row['wins'] . "W</span> / 
                              <span class='text-danger'>" . $row['losses'] . "L</span> / 
                              <span class='text-white'>" . $row['draws'] . "D</span>
                            </td>";
                      echo "<td class='fw-bold text-info'>" . $win_rate . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' class='text-center text-white-50 py-4'>No analytical data available.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <div id="tab-matches" class="tab-section">
        <h5 class="text-danger mb-4">Recent Match Logs</h5>
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Match ID</th>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>P1 Move</th>
                <th>P2 Move</th>
                <th>Match Outcome</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $matches_query = "
                SELECT m.*, p1.username as p1_name, p2.username as p2_name 
                FROM matches m
                JOIN players p1 ON m.player1_id = p1.player_id
                JOIN players p2 ON m.player2_id = p2.player_id
                ORDER BY m.match_id DESC 
                LIMIT 30
              ";
              
              $matches_result = mysqli_query($conn, $matches_query);
              if (mysqli_num_rows($matches_result) > 0) {
                  while($row = mysqli_fetch_assoc($matches_result)) {
                      $res = $row['result'];
                      if ($res == 'player1') {
                          $display_result = htmlspecialchars($row['p1_name']) . " Won";
                          $badge_class = "text-success fw-bold";
                      } elseif ($res == 'player2') {
                          $display_result = htmlspecialchars($row['p2_name']) . " Won";
                          $badge_class = "text-success fw-bold";
                      } else {
                          $display_result = "Draw / Tie";
                          $badge_class = "text-white fw-bold";
                      }
                      
                      echo "<tr>";
                      echo "<td>#" . $row['match_id'] . "</td>";
                      echo "<td>" . htmlspecialchars($row['p1_name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['p2_name']) . "</td>";
                      echo "<td>" . ucfirst(htmlspecialchars($row['player1_move'])) . "</td>";
                      echo "<td>" . ucfirst(htmlspecialchars($row['player2_move'])) . "</td>";
                      echo "<td><span class='" . $badge_class . "'>" . $display_result . "</span></td>";
                      echo "<td>" . $row['match_date'] . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='7' class='text-center text-white-50 py-4'>No match history recorded yet.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <script>
    function switchTab(tab) {
      document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
      document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
      event.currentTarget.classList.add('active');
    }

    function confirmLogout() {
      if (confirm('Are you sure you want to logout from Admin Session?')) {
        window.location.href = 'admin_logout.php';
      }
    }
  </script>

  <script>
    // Auto-refresh active queue count every 5 seconds
    setInterval(function() {
      fetch('get_queue_count.php')
        .then(r => r.text())
        .then(count => {
          document.getElementById('active-queue-count').textContent = count.trim();
        });
    }, 5000);
  </script>

</body>
</html>