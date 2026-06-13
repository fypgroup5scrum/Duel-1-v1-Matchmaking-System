<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RPS Arena</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    h1, .btn-gaming {
      font-family: 'Orbitron', sans-serif;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    .gaming-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 3rem;
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
      width: 100%;
      max-width: 420px;
      text-align: center;
    }
    .btn-gaming {
      width: 100%;
      padding: 15px;
      border-radius: 50px;
      font-weight: bold;
      font-size: 14px;
      transition: all 0.3s ease;
      border: none;
      display: block;
      text-decoration: none;
    }
    .btn-player {
      background: linear-gradient(45deg, #00d2ff, #3a7bd5);
      color: white;
      box-shadow: 0 0 20px rgba(0, 210, 255, 0.4);
    }
    .btn-player:hover {
      transform: translateY(-3px) scale(1.03);
      box-shadow: 0 0 30px rgba(0, 210, 255, 0.6);
      color: white;
    }
    .btn-admin {
      background: transparent;
      border: 2px solid #e94560 !important;
      color: #e94560;
    }
    .btn-admin:hover {
      background: #e94560;
      color: white;
      transform: translateY(-3px) scale(1.03);
    }
  </style>
</head>
<body>

  <div class="gaming-card">
    <h1 class="mb-2" style="color:#00d2ff;font-size:1.8rem;">RPS Arena</h1>
    <p class="text-white-50 small mb-5">Choose how you want to enter</p>

    <div class="d-flex flex-column gap-3">
      <a href="login_page.html" class="btn-gaming btn-player">Player Login</a>
      <a href="admin_login.html" class="btn-gaming btn-admin">Admin Login</a>
    </div>
  </div>

</body>
</html>
