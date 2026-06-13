<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['player_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi tamat']);
    exit;
}

$player_id = $_SESSION['player_id'];
$match_id = $_POST['match_id'] ?? '';
$move = $_POST['move'] ?? '';

$res = mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'");
$match = mysqli_fetch_assoc($res);

if (!$match) {
    echo json_encode(['status' => 'error', 'message' => 'Match did not exist']);
    exit;
}

// 1. Simpan pilihan pergerakan tangan pemain (Rock/Paper/Scissors)
if ($move !== 'check' && !empty($move)) {
    $column = ($player_id == $match['player1_id']) ? 'player1_move' : 'player2_move';
    mysqli_query($conn, "UPDATE matches SET $column = '$move' WHERE match_id = '$match_id'");
}

// 2. Ambil data perlawanan terkini selepas pilihan disimpan
$res_update = mysqli_query($conn, "SELECT * FROM matches WHERE match_id = '$match_id'");
$curr = mysqli_fetch_assoc($res_update);

// 3. BENTENG PENGHALANG BUG DOUBLE SUBMIT / DOUBLE UPDATE
// Jika keputusan perlawanan sudah direkodkan dalam perlawanan ini, terus hantar data lengkap tanpa kira lagi
if ($curr['result'] !== 'pending' && !empty($curr['result'])) {
    echo json_encode([
        'status' => 'complete',
        'p1_move' => $curr['player1_move'],
        'p2_move' => $curr['player2_move'],
        'result' => $curr['result'],
        'is_p1' => ($player_id == $curr['player1_id'])
    ]);
    exit;
}

// 4. Jika kedua-dua pemain sudah memilih dan status masih pending, mulakan proses KIRAAN TUNGGAL
if (!empty($curr['player1_move']) && !empty($curr['player2_move'])) {
    $p1_move = $curr['player1_move'];
    $p2_move = $curr['player2_move'];
    $p1_id = $curr['player1_id'];
    $p2_id = $curr['player2_id'];

    // Ambil mata MMR asal dari database
    $p1_res = mysqli_query($conn, "SELECT mmr FROM players WHERE player_id = '$p1_id'");
    $p1_old_mmr = mysqli_fetch_assoc($p1_res)['mmr'];

    $p2_res = mysqli_query($conn, "SELECT mmr FROM players WHERE player_id = '$p2_id'");
    $p2_old_mmr = mysqli_fetch_assoc($p2_res)['mmr'];

    // MATEMATIK FORMULA ELO RATING
    $r1 = pow(10, $p1_old_mmr / 400);
    $r2 = pow(10, $p2_old_mmr / 400);
    $e1 = $r1 / ($r1 + $r2);
    $e2 = $r2 / ($r1 + $r2);

    // Tentukan Hasil Keputusan Fizikal Permainan
    if ($p1_move === $p2_move) {
        $result_label = 'draw';
        $winner_id = null;
        $s1 = 0.5;
        $s2 = 0.5;
        
        $res_p1 = 'draw';
        $res_p2 = 'draw';
    } elseif (
        ($p1_move === 'rock' && $p2_move === 'scissors') ||
        ($p1_move === 'paper' && $p2_move === 'rock') ||
        ($p1_move === 'scissors' && $p2_move === 'paper')
    ) {
        $result_label = 'player1';
        $winner_id = $p1_id;
        $s1 = 1;
        $s2 = 0;
        
        $res_p1 = 'win';
        $res_p2 = 'loss';
    } else {
        $result_label = 'player2';
        $winner_id = $p2_id;
        $s1 = 0;
        $s2 = 1;
        
        $res_p1 = 'loss';
        $res_p2 = 'win';
    }

    // Kira kadar perubahan mata MMR (K-Factor = 32)
    $k = 32;
    $p1_change = round($k * ($s1 - $e1));
    $p2_change = round($k * ($s2 - $e2));

    $new_p1_mmr = $p1_old_mmr + $p1_change;
    $new_p2_mmr = $p2_old_mmr + $p2_change;

    // Had Sempadan Minimum MMR (Tidak boleh kurang dari 1000)
    if ($new_p1_mmr < 1000) { $new_p1_mmr = 1000; $p1_change = 1000 - $p1_old_mmr; }
    if ($new_p2_mmr < 1000) { $new_p2_mmr = 1000; $p2_change = 1000 - $p2_old_mmr; }

    // KEMASKINI DATABASE (Satu baris arahan yang padat & selamat)
    mysqli_query($conn, "UPDATE players SET mmr = $new_p1_mmr WHERE player_id = '$p1_id'");
    mysqli_query($conn, "UPDATE players SET mmr = $new_p2_mmr WHERE player_id = '$p2_id'");

    // Simpan Log Rekod Sejarah ke match_history
    mysqli_query($conn, "INSERT INTO match_history (match_id, player_id, opponent_id, result, mmr_before, mmr_after, change_amount) 
        VALUES ('$match_id', '$p1_id', '$p2_id', '$res_p1', '$p1_old_mmr', $new_p1_mmr, $p1_change)");
    
    mysqli_query($conn, "INSERT INTO match_history (match_id, player_id, opponent_id, result, mmr_before, mmr_after, change_amount) 
        VALUES ('$match_id', '$p2_id', '$p1_id', '$res_p2', '$p2_old_mmr', $new_p2_mmr, $p2_change)");

    // Kunci status jadual perlawanan utama daripada 'pending' kepada keputusan rasmi
    $win_sql = ($winner_id) ? "'$winner_id'" : "NULL";
    mysqli_query($conn, "UPDATE matches SET result = '$result_label', winner_id = $win_sql WHERE match_id = '$match_id'");

    echo json_encode([
        'status' => 'complete',
        'p1_move' => $p1_move,
        'p2_move' => $p2_move,
        'result' => $result_label,
        'is_p1' => ($player_id == $p1_id)
    ]);
    exit;
}

// 5. Jika salah seorang masih belum bertindak
echo json_encode([
    'status' => 'pending',
    'message' => 'Waiting for other opponent...'
]);
?>