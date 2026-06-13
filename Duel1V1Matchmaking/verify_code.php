<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify - RPS Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a2e; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { background: rgba(255,255,255,0.05); border: 1px solid #00d2ff; border-radius: 15px; width: 380px; }
        .code-input { letter-spacing: 12px; font-size: 2.2rem; text-align: center; background: #000; color: #00d2ff; border: 1px solid #00d2ff; }
    </style>
</head>
<body>
    <div class="card p-4 text-center">
        <h3 class="text-info">SECURITY CHECK</h3>
        <p class="small text-white-50">Enter the 6-digit code sent to your email.</p>
        <form action="process_verify.php" method="POST">
            <input type="text" name="input_code" class="form-control code-input mb-4" maxlength="6" pattern="\d{6}" required>
            <button type="submit" class="btn btn-info w-100 fw-bold">VERIFY IDENTITY</button>
        </form>
    </div>
</body>
</html>