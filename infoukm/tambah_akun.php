<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ukm    = $_POST['nama_ukm'];
    $email       = $_POST['email'];
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pembina     = $_POST['pembina'];
    $instagram = isset($_POST['sosial_media']) ? trim($_POST['sosial_media']) : null;
    $tiktok    = isset($_POST['tiktok']) ? trim($_POST['tiktok']) : null;
    $x         = isset($_POST['x']) ? trim($_POST['x']) : null;
    $facebook  = isset($_POST['facebook']) ? trim($_POST['facebook']) : null;
    $deskripsi_ukm   = $_POST['deskripsi_ukm'];

    // Upload logo
    $logo = 'default.png';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/uploads/' . $logo);
    }

    $stmt = $conn->prepare("
        INSERT INTO users 
        (nama_ukm, email, password, pembina, sosial_media, tiktok, x, facebook, deskripsi_ukm, logo, role, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ukm', 'aktif')
    ");
    $stmt->bind_param("ssssssssss", $nama_ukm, $email, $hashed_password, $pembina, $instagram, $tiktok, $x, $facebook, $deskripsi_ukm, $logo);


    if ($stmt->execute()) {
        header("Location: dashboard_admin.php");
        exit;
    } else {
        echo "Gagal menambahkan akun UKM: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah UKM - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/tambah_akun.css?v=<?= time() ?>">
</head>
<body>
    <div class="header">
        <h1 class="page-title">Tambah UKM</h1>
        <a href="dashboard_admin.php" class="btn-back">
            <i>←</i> Kembali ke Dashboard
        </a>
    </div>

    <div class="isi">
        <div class="form-container fade-up fade-delay-1">
            <form action="tambah_akun.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="ukm-name" class="form-label">Nama UKM</label>
                        <input type="text" id="ukm-name" name="nama_ukm" class="form-control" placeholder="Masukkan nama UKM" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="ukm-email" class="form-label">Email UKM</label>
                        <input type="email" id="ukm-email" name="email" class="form-control" placeholder="ukm@example.ac.id" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ukm-password" class="form-label">Password</label>
                        <input type="password" id="ukm-password" name="password" class="form-control" placeholder="Buat password" required>
                    </div>
                    <div class="form-group">
                        <label for="ukm-mentor" class="form-label">Nama Pembina</label>
                        <input type="text" id="ukm-mentor" name="pembina" class="form-control" placeholder="Nama dosen pembina" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="instagram" class="form-label">Instagram</label>
                        <input type="url" id="instagram" name="sosial_media" class="form-control" placeholder="https://instagram.com/..." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="tiktok" class="form-label">TikTok</label>
                        <input type="url" id="tiktok" name="tiktok" class="form-control" placeholder="https://tiktok.com/@..." autocomplete="off">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="x" class="form-label">X (Twitter)</label>
                        <input type="url" id="x" name="x" class="form-control" placeholder="https://x.com/..." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="facebook" class="form-label">Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" placeholder="https://facebook.com/..." autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ukm-description" class="form-label">Deskripsi UKM</label>
                    <textarea id="ukm-description" name="deskripsi_ukm" class="form-control" placeholder="Deskripsikan UKM secara detail" autocomplete="off" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Logo UKM</label>
                    <div class="file-upload" onclick="document.getElementById('logoInput').click();">
                        <span>📁</span>
                        <p>Seret & jatuhkan file di sini atau klik untuk memilih</p>
                        <small>Format: JPG, PNG (Maks. 5MB)</small>
                    </div>
                    <input type="file" name="logo" id="logoInput" accept=".jpg,.jpeg,.png" style="display: none;">
                </div>

                <script>
                document.getElementById('logoInput').addEventListener('change', function() {
                    const fileName = this.files[0]?.name || "Tidak ada file dipilih";
                    document.querySelector('.file-upload p').textContent = fileName;
                });
                </script>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        Simpan UKM
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>