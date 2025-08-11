<?php
session_start();
require 'koneksi.php';

// Pastikan admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    echo "ID UKM tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data user dari DB
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'ukm'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ukm = $result->fetch_assoc();

if (!$ukm) {
    echo "UKM tidak ditemukan.";
    exit;
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ukm  = $_POST['nama_ukm'];
    $email     = $_POST['email'];
    $pembina   = $_POST['pembina'];
    $instagram = trim($_POST['sosial_media']) ?: null;
    $tiktok    = trim($_POST['tiktok']) ?: null;
    $x         = trim($_POST['x']) ?: null;
    $facebook  = trim($_POST['facebook']) ?: null;
    $deskripsi_ukm = $_POST['deskripsi_ukm'];

    // Ambil logo lama dari DB
    $logoLama = $ukm['logo'];
    $logo = $logoLama;

    // Cek upload logo baru
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "uploads/";
        $logoName = time() . "_" . basename($_FILES['logo']['name']);
        $targetFile = $targetDir . $logoName;

        // Validasi ukuran max 5MB
        if ($_FILES['logo']['size'] > 5 * 1024 * 1024) {
            echo "Ukuran file terlalu besar. Maksimal 5MB.";
            exit;
        }

        // Upload file baru
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            $logo = $logoName;

            // Hapus logo lama jika ada & berbeda
            if (!empty($logoLama) && file_exists($targetDir . $logoLama)) {
                unlink($targetDir . $logoLama);
            }
        } else {
            echo "Gagal upload logo.";
            exit;
        }
    }

    // Jika password baru diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE users 
            SET nama_ukm=?, email=?, password=?, pembina=?, sosial_media=?, tiktok=?, x=?, facebook=?, deskripsi_ukm=?, logo=? 
            WHERE id=?
        ");
        $stmt->bind_param(
            "ssssssssssi",
            $nama_ukm, $email, $password, $pembina, $instagram, $tiktok, $x, $facebook, $deskripsi_ukm, $logo, $id
        );
    } else {
        $stmt = $conn->prepare("
            UPDATE users 
            SET nama_ukm=?, email=?, pembina=?, sosial_media=?, tiktok=?, x=?, facebook=?, deskripsi_ukm=?, logo=? 
            WHERE id=?
        ");
        $stmt->bind_param(
            "sssssssssi",
            $nama_ukm, $email, $pembina, $instagram, $tiktok, $x, $facebook, $deskripsi_ukm, $logo, $id
        );
    }

    if ($stmt->execute()) {
        header("Location: dashboard_admin.php");
        exit;
    } else {
        echo "Gagal update: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit UKM - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/edit_akun.css">
</head>
<body>
    <div class="header">
        <h1 class="page-title">Edit UKM</h1>
        <a href="dashboard_admin.php" class="btn-back">
            <i>←</i> Kembali ke Dashboard
        </a>
    </div>

    <div class="isi">
        <div class="form-container fade-up fade-delay-1">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama UKM</label>
                        <input type="text" name="nama_ukm" class="form-control" value="<?= htmlspecialchars($ukm['nama_ukm']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email UKM</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($ukm['email']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Pembina</label>
                        <input type="text" name="pembina" class="form-control" value="<?= htmlspecialchars($ukm['pembina']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="instagram" class="form-label">Instagram</label>
                        <input type="url" id="instagram" name="sosial_media" class="form-control" placeholder="https://instagram.com/..." 
                            value="<?= htmlspecialchars($ukm['sosial_media'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="tiktok" class="form-label">TikTok</label>
                        <input type="url" id="tiktok" name="tiktok" class="form-control" placeholder="https://tiktok.com/..."
                            value="<?= htmlspecialchars($ukm['tiktok'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="x" class="form-label">X (Twitter)</label>
                        <input type="url" id="x" name="x" class="form-control" placeholder="https://x.com/..."
                            value="<?= htmlspecialchars($ukm['x'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="facebook" class="form-label">Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" placeholder="https://facebook.com/..."
                            value="<?= htmlspecialchars($ukm['facebook'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi UKM</label>
                    <textarea name="deskripsi_ukm" class="form-control" required><?= htmlspecialchars($ukm['deskripsi_ukm']) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Logo UKM Saat Ini</label><br>
                    <?php if (!empty($ukm['logo']) && file_exists("uploads/" . $ukm['logo'])): ?>
                        <img src="uploads/<?= htmlspecialchars($ukm['logo']) ?>" 
                            alt="Logo UKM" 
                            style="max-width: 100px; height: auto; border:1px solid #ccc; border-radius:6px; margin-bottom:10px;">
                    <?php else: ?>
                        <p><i>Tidak ada logo</i></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Ganti Logo UKM (opsional)</label>
                    <div class="file-upload" onclick="document.getElementById('logoInput').click();">
                        <span>📁</span>
                        <p>Klik untuk memilih file</p>
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>