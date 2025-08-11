<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_ukm.php");
        }
        exit;
    }
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_ukm.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Info UKM</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles/login.css" />
</head>

<body>
  <header>
    <div class="logo">
      <span class="logo-icon">✦</span>
      <span>InfoUKM</span>
    </div>
    <a href="index.php" class="btn-back">
      <i>←</i> Kembali ke Beranda
    </a>
  </header>

  <div class="login-wrapper fade-up fade-delay-1">
    <div class="login-container">
      <div class="login-header">
        <div class="logo-login">InfoUKM</div>
        <h1>Masuk ke Akun UKM</h1>
        <p>Silakan login menggunakan Akun UKM Anda</p>
      </div>

      <!-- ALERT ERROR -->
      <?php if (isset($_GET['error'])): ?>
          <div style="color: red;"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>


      <form action="login_proses.php" method="POST" class="login-form">
        <div class="form-group">
          <label for="email">Email UKM</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="nama@ukm.ac.id" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div style="position: relative;">
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required />
            <span id="togglePassword" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; font-size: 1rem; color: #999;">👁️</span>
          </div>
        </div>

        <div class="remember-me">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn-login">Masuk</button>
      </form>
    </div>
  </div>

  <div class="alert fade-up fade-delay-2">
    <p>Belum punya akun? Akun UKM dibuat langsung oleh admin kampus.</p>
    <p>Hubungi admin untuk mendapatkan akses Login.</p>
  </div>
</body>

<script>
  const toggle = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  toggle.addEventListener("click", function () {
    // Toggle type attribute
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    
    // Toggle icon
    this.textContent = type === "password" ? "👁️" : "🙈";
  });
</script>

</html>
