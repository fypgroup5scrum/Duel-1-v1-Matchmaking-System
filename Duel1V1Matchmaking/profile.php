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

// ===================================================================
// PENGIRAAN STATISTIK SEBENAR BERDASARKAN HASIL FIZIKAL REKOD DB
// ===================================================================
$stats_query = mysqli_query($conn, "
    SELECT 
        SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as total_wins,
        SUM(CASE WHEN result = 'loss' THEN 1 ELSE 0 END) as total_losses,
        SUM(CASE WHEN result = 'draw' THEN 1 ELSE 0 END) as total_draws
    FROM match_history 
    WHERE player_id = '$id'
");
$stats = mysqli_fetch_assoc($stats_query);

$wins = $stats['total_wins'] ?? 0;
$losses = $stats['total_losses'] ?? 0;
$draws = $stats['total_draws'] ?? 0;
$total_games = $wins + $losses + $draws;

$win_rate = $total_games > 0 ? round(($wins / $total_games) * 100, 1) . "%" : "0%";

// Fungsi Penentuan Kategori Rank (Sesuai dengan logik Leaderboard/Profil)
function getRankCategory($points) {
    if ($points <= 1500) return 'Bronze';
    if ($points <= 2000) return 'Silver';
    if ($points <= 2500) return 'Gold';
    return 'Platinum';
}
$rank_cat = getRankCategory($user['mmr']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Player Profile - RPS Arena</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;500&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    body {
      background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%);
      color: #ffffff;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }
    h5, h3, .navbar-brand, .player-name, .mmr-text, .stat-val {
      font-family: 'Orbitron', sans-serif;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .navbar {
      background: rgba(22, 33, 62, 0.9) !important;
      backdrop-filter: blur(10px);
      border-bottom: 2px solid #e94560;
    }
    .profile-card {
      background: rgba(22, 33, 62, 0.7);
      border: 1px solid rgba(233, 69, 96, 0.3);
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(233, 69, 96, 0.1);
    }
    .stat-box {
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      padding: 15px;
      text-align: center;
    }
    .rank-Bronze { color: #cd7f32; text-shadow: 0 0 10px rgba(205, 127, 50, 0.5); }
    .rank-Silver { color: #c0c0c0; text-shadow: 0 0 10px rgba(192, 192, 192, 0.5); }
    .rank-Gold { color: #ffd700; text-shadow: 0 0 10px rgba(255, 215, 0, 0.5); }
    .rank-Platinum { color: #00ffff; text-shadow: 0 0 10px rgba(0, 255, 255, 0.5); }
    
    .status-win { color: #2ecc71; font-weight: bold; }
    .status-loss { color: #e74c3c; font-weight: bold; }
    .status-draw { color: #95a5a6; font-weight: bold; }
    
    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .table {
      background: rgba(22, 33, 62, 0.5);
      color: #ffffff;
      margin-bottom: 0;
    }
    .chart-container {
      position: relative;
      width: 200px;
      height: 200px;
      margin: 0 auto;
    }
    .chart-legend {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
      margin-top: 12px;
    }
    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color: rgba(255,255,255,0.7);
    }
    .legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
      <a class="navbar-brand text-danger fw-bold" href="home.php">RPS ARENA</a>
      <div class="d-flex">
        <a href="home.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-3">
    <div class="row g-4">
      
      <!-- Left: Player Info -->
      <div class="col-lg-4">
        <div class="profile-card p-4 text-center">
          <div class="mb-3">
            <div class="display-1 text-danger">🥷</div>
          </div>
          <h3 class="player-name text-white mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
          <p class="text-white-50 small mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
          
          <div class="my-3 py-2 border-top border-bottom border-secondary">
            <span class="text-white-50 small d-block">CURRENT RANK</span>
            <h4 class="fw-bold rank-<?php echo $rank_cat; ?>"><?php echo $rank_cat; ?></h4>
          </div>

          <div class="mt-3">
            <span class="text-white-50 small d-block">MMR</span>
            <h2 class="mmr-text text-warning fw-bold mb-0"><?php echo $user['mmr']; ?></h2>
          </div>
        </div>
      </div>

      <!-- Right: Stats + Chart + History -->
      <div class="col-lg-8">

        <!-- Performance Overview -->
        <div class="profile-card p-4 mb-4">
          <h5 class="text-danger mb-3">Performance Overview</h5>
          <div class="row align-items-center g-3">

            <!-- Stat Boxes -->
            <div class="col-md-6">
              <div class="row g-3">
                <div class="col-6">
                  <div class="stat-box">
                    <span class="text-white-50 small">Matches</span>
                    <div class="stat-val fs-4 text-white"><?php echo $total_games; ?></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat-box border-success">
                    <span class="text-success small">Wins</span>
                    <div class="stat-val fs-4 text-success"><?php echo $wins; ?></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat-box border-danger">
                    <span class="text-danger small">Losses</span>
                    <div class="stat-val fs-4 text-danger"><?php echo $losses; ?></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat-box border-warning">
                    <span class="text-warning small">Win Rate</span>
                    <div class="stat-val fs-4 text-warning"><?php echo $win_rate; ?></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-md-6 text-center">
              <?php if ($total_games > 0): ?>
                <div class="chart-container">
                  <canvas id="wldChart"></canvas>
                </div>
                <div class="chart-legend mt-2">
                  <div class="legend-item"><div class="legend-dot" style="background:#2ecc71;"></div> Wins</div>
                  <div class="legend-item"><div class="legend-dot" style="background:#e74c3c;"></div> Losses</div>
                  <div class="legend-item"><div class="legend-dot" style="background:#95a5a6;"></div> Draws</div>
                </div>
              <?php else: ?>
                <p class="text-white-50 small mt-3">No match data yet.<br>Start playing to see your stats!</p>
              <?php endif; ?>
            </div>

          </div>
        </div>

        <!-- Match History -->
        <div class="profile-card p-4">
          <h5 class="text-danger mb-3">Recent Match History</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr class="table-dark text-danger">
                  <th>Date & Time</th>
                  <th>Opponent</th>
                  <th>Opponent Rank</th>
                  <th>Outcome</th>
                  <th>Points Change</th>
                  <th>New MMR</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $history_sql = "SELECT mh.*, p.username as opponent_name, p.mmr as opponent_mmr 
                                FROM match_history mh 
                                LEFT JOIN players p ON mh.opponent_id = p.player_id 
                                WHERE mh.player_id = '$id' 
                                ORDER BY mh.played_at DESC LIMIT 10";
                $history_res = mysqli_query($conn, $history_sql);

                if (mysqli_num_rows($history_res) > 0) {
                    while($row = mysqli_fetch_assoc($history_res)) {
                        $res_type = strtolower($row['result']);
                        $class = "status-" . $res_type; // Memberikan warna mengikut lencana DRAW/WIN/LOSS
                        $opp_name = $row['opponent_name'] ?? 'Unknown';
                        $opp_rank = getRankCategory($row['opponent_mmr'] ?? 0);
                        
                        // FIX LOGIK WARNA MATA: Pisahkan warna mengikut status positif/negatif mata Elo
                        $change_amount = $row['change_amount'];
                        if ($change_amount > 0) {
                            $change_prefix = "+";
                            $points_class = "status-win";
                        } elseif ($change_amount < 0) {
                            $change_prefix = ""; 
                            $points_class = "status-loss";
                        } else {
                            $change_prefix = "";
                            $points_class = "status-draw";
                        }
                        
                        echo "<tr>
                                <td class='opacity-75'>" . date('d M Y, H:i', strtotime($row['played_at'])) . "</td>
                                <td class='text-dark fw-bold'>" . htmlspecialchars($opp_name) . "</td>
                                <td class='rank-$opp_rank'>" . $opp_rank . "</td>
                                <td><span class='$class'>" . strtoupper($res_type) . "</span></td>
                                <td><span class='$points_class'>" . $change_prefix . $change_amount . "</span></td>
                                <td class='fw-bold text-dark'>" . $row['mmr_after'] . "</td>
                              </tr>";
                    }
                } else {
                    // FIX: Colspan ditukar kepada 6 mengikut jumlah lajur th sebenar
                    echo "<tr><td colspan='6' class='text-center opacity-50 py-4'>No match data recorded. Start queueing to fight!</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php if ($total_games > 0): ?>
  <script>
    const ctx = document.getElementById('wldChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Wins', 'Losses', 'Draws'],
        datasets: [{
          data: [<?php echo $wins; ?>, <?php echo $losses; ?>, <?php echo $draws; ?>],
          backgroundColor: ['#2ecc71', '#e74c3c', '#95a5a6'],
          borderColor: ['#1a1a2e', '#1a1a2e', '#1a1a2e'],
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const pct = ((context.parsed / total) * 100).toFixed(1);
                return ` ${context.label}: ${context.parsed} (${pct}%)`;
              }
            }
          }
        }
      }
    });
  </script>
  <?php endif; ?>

</body>
</html>