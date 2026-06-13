<?php
session_start();
include 'db.php';

if (!isset($_SESSION['player_id'])) {
    die("error_session");
}

$player_id = $_SESSION['player_id'];
$action = $_POST['action'] ?? '';

// SUSUNAN RANK MMR BARU (Padan dengan image_f22618.png)
function getRankCategory($points) {
    if ($points < 1500) return 'Bronze';     // 1000 hingga 1499
    if ($points < 2000) return 'Silver';     // 1500 hingga 1999
    if ($points < 2500) return 'Gold';       // 2000 hingga 2499
    return 'Platinum';                       // 2500 ke atas
}

// --- 1. JOIN QUEUE ---
if ($action == 'join') {
    // Ambil data mmr tulen dari table players
    $res = mysqli_query($conn, "SELECT mmr FROM players WHERE player_id = '$player_id'");
    $p = mysqli_fetch_assoc($res);
    $my_mmr = $p['mmr'];
    $my_rank = getRankCategory($my_mmr);

    // Simpan ke dalam queue menggunakan column 'mmr' dan 'rank_category'
    $sql = "REPLACE INTO queue (player_id, mmr, rank_category, status) 
            VALUES ('$player_id', '$my_mmr', '$my_rank', 'waiting')";

    echo (mysqli_query($conn, $sql)) ? "searching" : "error";
    exit;
}

// --- 2. CHECK STATUS ---
if ($action == 'check_status') {
    // Semak jika perlawanan aktif (pending) sudah dicipta oleh sistem
    $res_assigned = mysqli_query($conn, "SELECT match_id FROM matches 
                   WHERE (player1_id = '$player_id' OR player2_id = '$player_id') 
                   AND result = 'pending' 
                   AND player1_move IS NULL 
                   AND player2_move IS NULL
                   ORDER BY match_date DESC LIMIT 1");

    if (mysqli_num_rows($res_assigned) > 0) {
        $m = mysqli_fetch_assoc($res_assigned);
        echo "match_found:" . $m['match_id'];
        exit;
    }

    // Ambil info semasa pemain daripada queue
    $my_q = mysqli_query($conn, "SELECT mmr, rank_category, status FROM queue WHERE player_id = '$player_id'");
    if (mysqli_num_rows($my_q) == 0) {
        echo "still_waiting";
        exit;
    }

    $my_info = mysqli_fetch_assoc($my_q);
    if ($my_info['status'] == 'matched') {
        echo "still_waiting";
        exit;
    }

    $my_mmr = $my_info['mmr'];
    $my_rank = $my_info['rank_category'];

    // ===================================================================
    // STRATEGI MATCHMAKING DINAMIK (BERDASARKAN ELORATING)
    // ===================================================================
    
    // LANGKAH 1: Cuba cari lawan dalam rank category yang SAMA (Contoh: Bronze vs Bronze) dan perbezaan MMR <= 500
    $result = mysqli_query($conn, "SELECT player_id FROM queue 
                      WHERE player_id != '$player_id' 
                      AND status = 'waiting' 
                      AND rank_category = '$my_rank' 
                      AND ABS(mmr - $my_mmr) <= 500 
                      ORDER BY joined_at ASC LIMIT 1");

    // LANGKAH 2: FALLBACK SYSTEM (Jika queue terlalu lama / tiada orang dalam rank sama)
    // Cari mana-mana rank terdekat berhampiran (Contoh: Bronze tinggi vs Silver rendah) asalkan beza MMR <= 500 mata
    if (mysqli_num_rows($result) == 0) {
        $result = mysqli_query($conn, "SELECT player_id FROM queue 
                          WHERE player_id != '$player_id' 
                          AND status = 'waiting' 
                          AND ABS(mmr - $my_mmr) <= 500 
                          ORDER BY ABS(mmr - $my_mmr) ASC, joined_at ASC LIMIT 1");
    }

    if (mysqli_num_rows($result) > 0) {
        $opp = mysqli_fetch_assoc($result);
        $opp_id = $opp['player_id'];

        // Kemas kini status lawan secara selamat (Atomic Update) supaya tidak bertembung dengan player lain
        mysqli_query($conn, "UPDATE queue SET status = 'matched' WHERE player_id = '$opp_id' AND status = 'waiting'");
        
        if (mysqli_affected_rows($conn) > 0) {
            // Kemas kini status diri sendiri dalam queue
            mysqli_query($conn, "UPDATE queue SET status = 'matched' WHERE player_id = '$player_id'");
            
            // Cipta perlawanan baharu dalam table matches (winner_id di-set NULL secara default)
            mysqli_query($conn, "INSERT INTO matches (player1_id, player2_id, result, winner_id) VALUES ('$player_id', '$opp_id', 'pending', NULL)");
            
            echo "match_found:" . mysqli_insert_id($conn);
            exit;
        }
    }

    echo "still_waiting";
    exit;
}

// --- 3. CANCEL ---
if ($action == 'cancel') {
    mysqli_query($conn, "DELETE FROM queue WHERE player_id = '$player_id'");
    echo "cancelled";
    exit;
}
?>