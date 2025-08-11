<?php
require 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID event tidak ditemukan.";
    exit;
}

$event_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT e.id, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, 
           e.jam_mulai, e.jam_selesai, e.lokasi, e.deskripsi, 
           e.banner, e.biaya, e.link_pendaftaran, 
           u.nama_ukm, u.logo, u.deskripsi_ukm, u.sosial_media,
           u.tiktok, u.x, u.facebook
    FROM events e
    JOIN users u ON e.created_by = u.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    echo "Event tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/event_detail.css?v=<?= time() ?>">
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

    <main class="event-detail">
        <article class="detail-header fade-up fade-delay-1">
            <img src="<?= file_exists('uploads/' . $event['banner']) ? 'uploads/' . $event['banner'] : 'https://via.placeholder.com/800x300' ?>" alt="Banner Event" class="detail-img">
            <div class="detail-content">
                <h1 class="detail-title"><?= htmlspecialchars($event['nama_event']) ?></h1>
                <div class="detail-meta">
                    <div class="meta-item">
                        🗓️ <?= date('l, d M Y', strtotime($event['tanggal_mulai'])) ?>
                        <?php if ($event['tanggal_mulai'] !== $event['tanggal_selesai']): ?>
                            - <?= date('d M Y', strtotime($event['tanggal_selesai'])) ?>
                        <?php endif; ?>
                    </div>
                    <div class="meta-item">
                        📍 <?= htmlspecialchars($event['lokasi']) ?>
                    </div>
                    <div class="meta-item">
                        ⏰ 
                        <?= !empty($event['jam_mulai']) ? htmlspecialchars($event['jam_mulai']) : '-' ?>
                        -
                        <?= !empty($event['jam_selesai']) ? htmlspecialchars($event['jam_selesai']) : '-' ?>
                    </div>
                    <div class="meta-item">
                        💰 Rp <?= number_format($event['biaya'], 0, ',', '.') ?>
                    </div>
                </div>

                <div class="detail-org fade-up fade-delay-2">
                    <img src="<?= file_exists('uploads/' . $event['logo']) ? 'uploads/' . $event['logo'] : 'https://via.placeholder.com/80' ?>" alt="Logo UKM" class="ukm-logo">
                    <div class="org-info">
                        <h4><?= htmlspecialchars($event['nama_ukm']) ?></h4>
                    </div>
                </div>

                <div class="detail-body">
                    <p><?= nl2br(htmlspecialchars($event['deskripsi'])) ?></p>
                </div>

                <div class="action-buttons">
                    <?php if (!empty($event['link_pendaftaran'])): ?>
                    <a href="<?= htmlspecialchars($event['link_pendaftaran']) ?>" class="btn-primary" target="_blank">
                        🎟️ Daftar Sekarang
                    </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn-secondary">
                        <i>←</i> Kembali
                    </a>
                </div>
            </div>
        </article>

        <section class="ukm-section">
            <div class="ukm-header">
                <img src="<?= file_exists('uploads/' . $event['logo']) ? 'uploads/' . $event['logo'] : 'https://via.placeholder.com/80' ?>" alt="Logo UKM" class="ukm-logo">
                <div>
                    <h3><?= htmlspecialchars($event['nama_ukm']) ?></h3>
                    <div class="ukm-social">
                        <?php if (!empty($event['sosial_media'])): ?>
                            <a href="<?= htmlspecialchars($event['sosial_media']) ?>" title="Sosial Media" target="_blank">
                                <img src="uploads/instagram.png" alt="Sosial Media" style="width:24px; height:24px; object-fit:contain;">
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($event['tiktok'])): ?>
                            <a href="<?= htmlspecialchars($event['tiktok']) ?>" target="_blank" title="TikTok">
                                <img src="uploads/tiktok.png" alt="TikTok" style="width:24px; height:24px; object-fit:contain;">
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($event['x'])): ?>
                            <a href="<?= htmlspecialchars($event['x']) ?>" target="_blank" title="X (Twitter)">
                                <img src="uploads/x.png" alt="X" style="width:24px; height:24px; object-fit:contain;">
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($event['facebook'])): ?>
                            <a href="<?= htmlspecialchars($event['facebook']) ?>" target="_blank" title="Facebook">
                                <img src="uploads/facebook.png" alt="Facebook" style="width:24px; height:24px; object-fit:contain;">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <p><?= nl2br(htmlspecialchars($event['deskripsi_ukm'])) ?></p>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>InfoUKM</h3>
                <p>Portal informasi kegiatan Unit Kegiatan Mahasiswa di lingkungan Universitas.</p>
                <p>Menghubungkan mahasiswa dengan berbagai kegiatan pengembangan diri.</p>
            </div>
            <div class="footer-section">
                <h3>Kontak</h3>
                <p>Email: info@infoukm.ac.id</p>
                <p>Telepon: (021) 1234 5678</p>
                <p>Gedung R. Said Soekanto Lt. 4</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 InfoUKM. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>