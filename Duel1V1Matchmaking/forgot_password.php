<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Forgot Password - RPS Arena</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 100%); color: white; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
    .card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border: 1px solid rgba(0, 210, 255, 0.3); border-radius: 20px; padding: 2.5rem; width: 400px; border-top: 4px solid #00d2ff; }
    .btn-gaming { background: linear-gradient(45deg, #00d2ff, #3a7bd5); border: none; color: white; font-family: 'Orbitron'; font-weight: bold; padding: 12px; }
    
    /* Style Custom untuk Popup Gaming */
    .modal-content { background: #1a1a2e; border: 2px solid #e94560; border-radius: 15px; box-shadow: 0 0 20px rgba(233, 69, 96, 0.5); }
    .btn-close { filter: invert(1); }
  </style>
</head>
<body>

  <div class="card shadow-lg">
    <h2 class="text-center mb-4" style="font-family:'Orbitron'; color:#00d2ff;">PASSWORD RESET</h2>
    <form action="send_reset_code.php" method="POST">
      <div class="mb-4">
        <label class="form-label small text-info">REGISTERED EMAIL</label>
        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="player@example.com" required>
      </div>
      <button type="submit" class="btn btn-gaming w-100">TRANSMIT CODE</button>
      <div class="text-center mt-3"><a href="login_page.html" class="text-secondary text-decoration-none small">← Back to Login</a></div>
    </form>
  </div>

  <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title text-danger fw-bold" style="font-family:'Orbitron';">⚠️ ACCESS DENIED</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center py-4">
          <p class="mb-0 fs-5">Neural Link Error: <b>Email not found!</b></p>
          <small class="text-white-50">Please ensure that the email entered has been registered.</small>
        </div>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-outline-danger px-4 fw-bold" data-bs-dismiss="modal" style="font-family:'Orbitron';">RETRY</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Guna JavaScript untuk trigger popup jika ada error 'notfound' kat URL
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'notfound') {
            var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
            myModal.show();
        }
    });
  </script>

</body>
</html>