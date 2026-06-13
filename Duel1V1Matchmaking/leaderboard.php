<?php
session_start();
include 'db.php';

// Semak sama ada pemain telah log masuk
if (!isset($_SESSION['player_id'])) {
    header("Location: login_page.html");
    exit();
}

$current_player_id = $_SESSION['player_id'];

// 1. Ambil data pemain semasa untuk paparan bahagian menu pengepala (header)
$player_query = mysqli_query($conn, "SELECT * FROM players WHERE player_id = '$current_player_id'");
$user = mysqli_fetch_assoc($player_query);

// ===================================================================
// 2. FUNGSI PENGIRAAN TIER TERKINI (Kemas kini berdasarkan sistem baharu)
// ===================================================================
function getRankCategory($points) {
    if ($points < 1500) return 'Bronze';     // 1000 hingga 1499 MMR
    if ($points < 2000) return 'Silver';     // 1500 hingga 1999 MMR
    if ($points < 2500) return 'Gold';       // 2000 hingga 2499 MMR
    return 'Platinum';                       // 2500 MMR ke atas
}

// Menggunakan data mmr baharu untuk lencana profil di pengepala
$lobby_rank = getRankCategory($user['mmr']);

// 3. Ambil data 10 pemain terbaik berdasarkan susunan mata MMR yang tertinggi
$leaderboard_query = mysqli_query($conn, "
    SELECT player_id, username, mmr 
    FROM players 
    ORDER BY mmr DESC 
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Leaderboard - RPS Arena</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;500&display=swap" rel="stylesheet">
  
  <style>
    body {
      background: radial-gradient(circle at center, #1a1a2e 0%, #111424 100%) !important;
      color: #ffffff !important;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    h1, h3, h5, .navbar-brand, .rank-header, .mmr-val {
      font-family: 'Orbitron', sans-serif;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .navbar {
      background: rgba(17, 20, 36, 0.9) !important;
      backdrop-filter: blur(10px);
      border-bottom: 2px solid #e94560;
    }

    .leaderboard-card {
      background: rgba(22, 27, 49, 0.9) !important;
      border: 2px solid rgba(233, 69, 96, 0.4);
      border-radius: 15px;
      box-shadow: 0 0 25px rgba(233, 69, 96, 0.25);
    }

    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .table, 
    .table > :not(caption) > * > *, 
    .table tr, 
    .table td, 
    .table th {
      background-color: transparent !important;
      color: #ffffff !important;
      background: transparent !important;
    }

    .table tbody tr {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .gaming-username {
      color: #ffffff !important;
      font-weight: 700 !important;
      text-shadow: 0 0 6px rgba(255, 255, 255, 0.4);
      font-size: 1.05rem;
    }

    .top-1 td { background-color: rgba(255, 215, 0, 0.15) !important; }
    .top-2 td { background-color: rgba(192, 192, 192, 0.1) !important; }
    .top-3 td { background-color: rgba(205, 127, 50, 0.1) !important; }

    .pos-1 { color: #ffd700 !important; text-shadow: 0 0 8px #ffd700; font-weight: bold; }
    .pos-2 { color: #c0c0c0 !important; text-shadow: 0 0 8px #c0c0c0; font-weight: bold; }
    .pos-3 { color: #cd7f32 !important; text-shadow: 0 0 8px #cd7f32; font-weight: bold; }

    .current-user-row td {
      background-color: rgba(233, 69, 96, 0.15) !important;
    }

    .lobby-badge {
      padding: 4px 10px;
      border-radius: 5px;
      font-weight: bold;
      font-family: 'Orbitron';
      font-size: 0.8rem;
    }
    .badge-Bronze { background: #cd7f32; color: #fff; }
    .badge-Silver { background: #c0c0c0; color: #000; }
    .badge-Gold { background: #ffd700; color: #000; }
    .badge-Platinum { background: #00ffff; color: #000; }
    
    .pulse { animation: neonPulse 1.5s infinite alternate; }
    @keyframes neonPulse { 
      from { text-shadow: 0 0 5px #fff, 0 0 10px #e94560; } 
      to { text-shadow: 0 0 10px #fff, 0 0 20px #0f3460; } 
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
      <a class="navbar-brand text-white fw-bold" href="home.php">RPS <span class="text-danger">ARENA</span></a>
      <div class="d-flex align-items-center">
        <a href="profile.php" class="text-decoration-none me-4 text-white fw-medium">
          👤 <?php echo htmlspecialchars($user['username']); ?> 
          <span class="lobby-badge badge-<?php echo $lobby_rank; ?> ms-1"><?php echo $lobby_rank; ?></span>
        </a>
        <a href="home.php" class="btn btn-outline-light btn-sm me-2">Lobby</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="text-center mb-4">
          <h1 class="display-5 text-white fw-bold mb-1">TOP 10 <span class="text-danger pulse">LEADERBOARD</span></h1>
          <p class="text-white-50">Behold the strongest players dominating the competitive arena ladder</p>
        </div>

        <div class="leaderboard-card p-4">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr class="table-dark text-danger rank-header" style="border-bottom: 2px solid #e94560 !important;">
                  <th style="width: 12%">Rank</th>
                  <th style="width: 43%">Player Name</th>
                  <th style="width: 25%">Tier Category</th>
                  <th style="width: 20%" class="text-end">MMR Points</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (mysqli_num_rows($leaderboard_query) > 0) {
                    $position = 1;
                    while ($row = mysqli_fetch_assoc($leaderboard_query)) {
                        
                        $row_class = "";
                        $pos_class = "text-white-50";
                        $medal = "";

                        if ($position == 1) { $row_class = "top-1"; $pos_class = "pos-1"; $medal = "🥇 "; }
                        elseif ($position == 2) { $row_class = "top-2"; $pos_class = "pos-2"; $medal = "🥈 "; }
                        elseif ($position == 3) { $row_class = "top-3"; $pos_class = "pos-3"; $medal = "🥉 "; }

                        if ($row['player_id'] == $current_player_id) {
                            $row_class .= " current-user-row fw-bold";
                        }

                        // Menghitung Kategori Rank berdasarkan logik MMR dinamik yang baru
                        $player_rank = getRankCategory($row['mmr']);
                        $display_name = htmlspecialchars($row['username']);
                        
                        echo "<tr class='" . $row_class . "'>";
                        echo "<td class='fs-5 " . $pos_class . "'>" . $position . "</td>";
                        echo "<td class='gaming-username'>";
                        echo $medal . $display_name;
                        if ($row['player_id'] == $current_player_id) {
                            echo " <span class='badge bg-danger small ms-1' style='font-size: 10px;'>YOU</span>";
                        }
                        echo "</td>";
                        echo "<td><span class='lobby-badge badge-" . $player_rank . "'>" . $player_rank . "</span></td>";
                        echo "<td class='text-end mmr-val text-warning fw-bold fs-5'>" . $row['mmr'] . "</td>";
                        echo "</tr>";
                        
                        $position++;
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center text-white opacity-50 py-4'>No players registered yet.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>