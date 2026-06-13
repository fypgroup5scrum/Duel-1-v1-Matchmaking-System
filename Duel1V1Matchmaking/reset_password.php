<?php
session_start();
include 'db.php';

// Sekuriti: Kalau belum verify, tendang keluar
if (!isset($_SESSION['code_verified']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.history.back();</script>";
        exit();
    }

    if (strlen($new_pass) < 6) {
        echo "<script>alert('Password must be at least 6 characters.'); window.history.back();</script>";
        exit();
    }

    $hashed = password_hash($new_pass, PASSWORD_BCRYPT);

    // UPDATE password dan RESET kolum kod supaya tidak boleh guna lagi
    $update = mysqli_query($conn, "UPDATE players SET password = '$hashed', reset_code = NULL, code_expiry = NULL WHERE email = '$email'");

    if ($update) {
        // Clear semua session reset
        unset($_SESSION['reset_email']);
        unset($_SESSION['code_verified']);
        echo "<script>alert('Password Successfully Updated! Please Log In Again.'); window.location.href='login_page.html';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .reset-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(0,210,255,0.2);
            border-radius: 20px;
            padding: 2.5rem;
            width: 380px;
            position: relative;
        }
        .reset-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #00d2ff, #3a7bd5);
            border-radius: 20px 20px 0 0;
        }
        h4 {
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .form-label {
            color: #00d2ff;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .form-control {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(0,210,255,0.2);
            color: white;
            padding: 12px 15px;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(0,0,0,0.5);
            border-color: #00d2ff;
            color: white;
            box-shadow: 0 0 15px rgba(0,210,255,0.2);
        }
        .btn-update {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 2px;
            width: 100%;
            text-transform: uppercase;
            transition: 0.3s;
            cursor: pointer;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,210,255,0.4);
        }
        .error-msg {
            color: #e94560;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <h4 class="text-info mb-4 text-center">Set New Password</h4>
        <form method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" minlength="6" required/>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required/>
                <div class="error-msg" id="match-error">Passwords do not match!</div>
            </div>
            <button type="submit" class="btn-update">Update Password</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const error = document.getElementById('match-error');

            if (pass !== confirm) {
                error.style.display = 'block';
                return false;
            }
            error.style.display = 'none';
            return true;
        }

        // Live check as user types
        document.getElementById('confirm_password').addEventListener('input', function() {
            const pass = document.getElementById('password').value;
            const error = document.getElementById('match-error');
            error.style.display = (this.value && this.value !== pass) ? 'block' : 'none';
        });
    </script>
</body>
</html>
