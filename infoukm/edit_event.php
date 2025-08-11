<?php
session_start();
require 'koneksi.php';

// Cek login
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ukm', 'admin'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Jika ada parameter ID di URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Ambil data event untuk ditampilkan
    if ($_SESSION['role'] === 'ukm') {
        // UKM hanya bisa ambil event mereka sendiri
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ii", $event_id, $user_id);
    } else {
        // Admin bisa akses semua event
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if (!$event) {
        echo "Event tidak ditemukan atau bukan milik kamu.";
        exit;
    }
}

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id   = $_POST['event_id'];
    $nama_event = $_POST['nama_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $jam_mulai       = $_POST['jam_mulai'];
    $jam_selesai     = $_POST['jam_selesai'];
    $lokasi     = $_POST['lokasi'];
    $deskripsi  = $_POST['deskripsi'];
    $biaya      = $_POST['biaya'];
    $link       = $_POST['link_pendaftaran'];

    // Optional: Handle file baru
    $banner = $_POST['banner_lama']; // default pakai yang lama
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['banner']['tmp_name'], 'uploads/' . $newName);
        $banner = $newName;
    }

    // Validasi tanggal
    $now = date('Y-m-d H:i:s');
    $start = $tanggal_mulai . ' ' . $jam_mulai;
    $end   = $tanggal_selesai . ' ' . $jam_selesai;

    if ($now < $start) {
        $status = 'Mendatang';
    } elseif ($now >= $start && $now <= $end) {
        $status = 'Aktif';
    } else {
        $status = 'Selesai';
    }

    if ($_SESSION['role'] === 'ukm') {
        // UKM hanya bisa update event mereka sendiri
        $stmt = $conn->prepare("UPDATE events 
            SET nama_event=?, tanggal_mulai=?, tanggal_selesai=?, jam_mulai=?, jam_selesai=?, lokasi=?, deskripsi=?, biaya=?, link_pendaftaran=?, status=?, banner=? 
            WHERE id=? AND created_by=?");
        $stmt->bind_param("sssssssssssii", 
            $nama_event, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $lokasi, $deskripsi, $biaya, $link, $status, $banner, $event_id, $user_id);
    } else {
        // Admin bisa update semua event
        $stmt = $conn->prepare("UPDATE events 
            SET nama_event=?, tanggal_mulai=?, tanggal_selesai=?, jam_mulai=?, jam_selesai=?, lokasi=?, deskripsi=?, biaya=?, link_pendaftaran=?, status=?, banner=? 
            WHERE id=?");
        $stmt->bind_param("sssssssssssi", 
            $nama_event, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $lokasi, $deskripsi, $biaya, $link, $status, $banner, $event_id);
    }

    if ($stmt->execute()) {
        header("Location: dashboard_ukm.php");
        exit;
    } else {
        echo "Gagal update event: " . $stmt->error;
    }


}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/edit_event.css?v=<?= time() ?>">
</head>
<body>
    <header>
        <div class="logo">
            <span>Edit Event</span>
        </div>
        <a href="dashboard_ukm.php" class="btn-back">
            <i>←</i> Kembali ke Dashboard
        </a>
    </header>

    <div class="isi">
    <div class="form-container fade-up fade-delay-1">
        <form action="edit_event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
            <input type="hidden" name="banner_lama" value="<?= $event['banner'] ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="event-name" class="form-label">Nama Event</label>
                    <input type="text" id="event-name" name="nama_event" class="form-control" placeholder="Masukkan nama event" required
                        value="<?= htmlspecialchars($event['nama_event']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required value="<?= $event['tanggal_mulai'] ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required value="<?= $event['tanggal_selesai'] ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" required value="<?= $event['jam_mulai'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" required value="<?= $event['jam_selesai'] ?? '' ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event-location" class="form-label">Lokasi</label>
                    <input type="text" id="event-location" name="lokasi" class="form-control" placeholder="Tempat pelaksanaan" required
                        value="<?= htmlspecialchars($event['lokasi']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="event-description" class="form-label">Deskripsi Event</label>
                <textarea id="event-description" name="deskripsi" class="form-control" placeholder="Deskripsikan event secara detail" required><?= htmlspecialchars($event['deskripsi'] ?? '' ) ?></textarea>
            </div>

            <div class="form-group">
                <label for="event-registration-link" class="form-label">Link Pendaftaran</label>
                <input type="url" id="event-registration-link" name="link_pendaftaran" class="form-control" placeholder="https://example.com/daftar"
                    value="<?= htmlspecialchars($event['link_pendaftaran']) ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event-fee" class="form-label">Biaya Pendaftaran (Rp)</label>
                    <input type="number" id="event-fee" name="biaya" class="form-control" placeholder="0 jika gratis" min="0"
                        value="<?= $event['biaya'] ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Poster Event</label>
                <div class="file-upload" onclick="document.getElementById('bannerInput').click();">
                    <span>📁</span>
                    <p><?= $event['banner'] ? htmlspecialchars($event['banner']) : "Seret & jatuhkan file di sini atau klik untuk memilih" ?></p>
                    <small>Format: JPG, PNG (Maks. 5MB)</small>
                </div>
                <input type="file" name="banner" id="bannerInput" accept=".jpg,.jpeg,.png" style="display: none;">
            </div>

            <script>
            document.getElementById('bannerInput').addEventListener('change', function() {
                const fileName = this.files[0]?.name || "Tidak ada file dipilih";
                document.querySelector('.file-upload p').textContent = fileName;
            });
            </script>

            <div class="form-actions">
                <button type="submit" class="btn">
                    Simpan Event
                </button>
            </div>
        </form>
    </div>
    </div>
</body>
</html>