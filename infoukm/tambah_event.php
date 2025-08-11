<?php
session_start();
require 'koneksi.php';

function kirimPesanWA($namaEvent, $tanggalMulai, $tanggalSelesai, $jamMulai, $jamSelesai, $lokasi, $biaya, $deskripsi, $linkEvent) {
    $wahaUrl = "http://localhost:3000/api/sendText";
    $token = "SECRET_TOKEN"; // ganti dengan token WAHA kamu
    $chatId = "120363401259930700@g.us"; // ganti dengan chatId WA grup kamu
    $session = "default"; // ganti sesuai session WAHA kamu

    // Format biaya
    $biayaFormatted = ($biaya == 0 || $biaya === "0") 
        ? "Gratis" 
        : "Rp " . number_format($biaya, 0, ',', '.');

    // Format pesan
    $pesan = "📢 *{$namaEvent}*\n\n" .
             "🗓 {$tanggalMulai} - {$tanggalSelesai}\n" .
             "⏰ {$jamMulai} - {$jamSelesai}\n" .
             "📍 {$lokasi}\n" .
             "💰 {$biayaFormatted}\n\n" .
             "📝 {$deskripsi}\n\n" .
             "🔗 Info lengkap: {$linkEvent}";

    // Data body
    $data = [
        "session" => $session,
        "chatId" => $chatId,
        "text" => $pesan
    ];

    $ch = curl_init($wahaUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("Error WA: " . curl_error($ch));
    } else {
        error_log("Response WA: " . $response);
    }
    curl_close($ch);
    return $response;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ukm') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_event = $_POST['nama_event'];
    $tanggal_mulai   = date('Y-m-d', strtotime($_POST['tanggal_mulai']));
    $tanggal_selesai = date('Y-m-d', strtotime($_POST['tanggal_selesai']));
    $jam_mulai       = $_POST['jam_mulai'];
    $jam_selesai     = $_POST['jam_selesai'];
    $lokasi     = $_POST['lokasi'];
    $deskripsi  = $_POST['deskripsi'];
    $biaya      = $_POST['biaya'];
    $link       = $_POST['link_pendaftaran'];
    $created_by = $_SESSION['user_id'];

    $banner = '';
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['banner']['tmp_name'];
        $file_name = $_FILES['banner']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png'];

        if (!in_array($file_ext, $allowed)) {
            echo "Format file tidak didukung. Hanya JPG, JPEG, PNG.";
            exit;
        }

    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($_FILES['banner']['size'] > $maxSize) {
        echo "Ukuran file terlalu besar. Maksimal 5MB.";
        exit;
    }

        $newName = uniqid() . '.' . $file_ext;
        move_uploaded_file($file_tmp, 'uploads/' . $newName);
        $banner = $newName;
    }

    // Default status ditentukan berdasarkan tanggal event
    $today = date('Y-m-d');
    if ($tanggal_mulai <= $today && $tanggal_selesai >= $today) {
        $status = 'aktif';
    } elseif ($tanggal_mulai > $today) {
        $status = 'mendatang';
    } else {
        $status = 'selesai';
    }

    $stmt = $conn->prepare("INSERT INTO events 
        (nama_event, tanggal_mulai, tanggal_selesai, jam_mulai, jam_selesai, lokasi, deskripsi, biaya, link_pendaftaran, status, banner, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("sssssssisssi",
        $nama_event, 
        $tanggal_mulai,
        $tanggal_selesai,
        $jam_mulai,
        $jam_selesai,
        $lokasi,
        $deskripsi,
        $biaya,   // double (d) atau integer (i)
        $link,
        $status,
        $banner,
        $created_by
    );
    
    if ($stmt->execute()) {
        if (!empty($banner)) {
            $bannerPath = "uploads/" . $banner;
            $linkEvent = "https://infoukm.my.id/event_detail.php?id=" . $conn->insert_id;
            
            kirimPesanWA(
                $nama_event,
                $tanggal_mulai,
                $tanggal_selesai,
                $jam_mulai,
                $jam_selesai,
                $lokasi,
                $biaya,
                $deskripsi,
                $linkEvent
            );

        }

        header("Location: dashboard_ukm.php");
        exit;
    } else {
        echo "Gagal menambahkan event: " . $stmt->error;
    }

} else {
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/tambah_event.css?v=<?= time() ?>">
</head>
<body>
    <header>
        <div class="logo">
            <span>Tambah Event</span>
        </div>
        <a href="dashboard_ukm.php" class="btn-back">
            <i>←</i> Kembali ke Dashboard
        </a>
    </header>

    <div class="isi">
    <div class="form-container fade-up fade-delay-1">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="event-name" class="form-label">Nama Event</label>
                    <input type="text" name="nama_event" id="event-name" class="form-control" placeholder="Masukkan nama event" autocomplete="off" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai Event</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai Event</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
                </div>
            </div>


            <div class="form-row">
                <div class="form-group">
                    <label for="event-location" class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" id="event-location" class="form-control" placeholder="Tempat pelaksanaan" autocomplete="off" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="event-description" class="form-label">Deskripsi Event</label>
                <textarea id="event-description" name="deskripsi" class="form-control" placeholder="Deskripsikan event secara detail" autocomplete="off" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="event-registration-link" class="form-label">Link Pendaftaran</label>
                <input type="url" id="event-registration-link" name="link_pendaftaran" class="form-control" placeholder="https://example.com/daftar" autocomplete="off">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="event-fee" class="form-label">Biaya Pendaftaran (Rp)</label>
                    <input type="number" id="event-fee" name="biaya" class="form-control" placeholder="0 jika gratis" min="0" autocomplete="off">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Poster Event</label>
                <div class="file-upload" onclick="document.getElementById('bannerInput').click();">
                    <span>📁</span>
                    <p>Seret & jatuhkan file di sini atau klik untuk memilih</p>
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