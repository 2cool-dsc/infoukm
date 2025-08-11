<?php
require 'koneksi.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$today = date('Y-m-d');

$search_sql = '';
$search_params = [];
$search_types = '';

if (!empty($q)) {
    $search_sql = " AND (e.nama_event LIKE ? OR u.nama_ukm LIKE ?)";
    $search_params[] = "%$q%";
    $search_params[] = "%$q%";
    $search_types .= 'ss';
}

$sql = "SELECT e.id, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, e.jam_mulai, e.jam_selesai,
               e.lokasi, e.banner, u.nama_ukm, u.logo
        FROM events e
        JOIN users u ON e.created_by = u.id
        WHERE e.status = 'aktif'
          AND e.tanggal_mulai <= ?
          AND e.tanggal_selesai >= ?
          $search_sql
        ORDER BY e.tanggal_mulai ASC
        LIMIT 10";

$stmt = $conn->prepare($sql);
if (!empty($q)) {
    $stmt->bind_param("ss" . $search_types, $today, $today, ...$search_params);
} else {
    $stmt->bind_param("ss", $today, $today);
}
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Upcoming Events (yang tanggalnya > hari ini dan statusnya 'mendatang')
$upcoming_sql = "SELECT e.id, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, e.jam_mulai, e.jam_selesai,
                        e.lokasi, e.banner, u.nama_ukm, u.logo
                 FROM events e
                 JOIN users u ON e.created_by = u.id
                 WHERE e.status = 'mendatang'
                       $search_sql
                 ORDER BY e.tanggal_mulai ASC
                 LIMIT 10";

$upcoming_stmt = $conn->prepare($upcoming_sql);
if (!empty($q)) {
    $upcoming_stmt->bind_param($search_types, ...$search_params);
}
$upcoming_stmt->execute();
$upcoming_events = $upcoming_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info UKM - Universitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css?v=<?= time() ?>">
</head>
<body>
    <header class="fade-up fade-delay-1">
        <div class="logo">
            <span class="logo-icon">✦</span>
            <span>InfoUKM</span>
        </div>
        <nav>
            <ul>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="login.php" class="btn-nav">Login</a></li>
            </ul>
        </nav>
    </header>

    <form class="search-form fade-up fade-delay-2" action="index.php" method="get">
        <input type="text" id="search" name="q" placeholder="Cari event atau UKM..." autocomplete="off" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">🔍</button>
    </form>

    <section class="hero fade-up fade-delay-2">
        <div class="hero-content">
            <h1>Temukan Kegiatan Mahasiswa yang Menginspirasi</h1>
            <p>Jelajahi berbagai event dan kegiatan dari Unit Kegiatan Mahasiswa di kampus kami. Tingkatkan skill dan perluas jaringan pertemanan Anda.</p>
        </div>
    </section>

    <section class="events fade-up fade-delay-3">
        <h2 class="section-title">Upcoming Events</h2>
        <div id="upcoming-list" class="events-container upcoming">
            <?php if (empty($upcoming_events)): ?>
                <p style="text-align:center">Belum ada event mendatang.</p>
            <?php else: ?>
                <?php foreach ($upcoming_events as $event): ?>
                    <div class="event-card upcoming">
                        <img src="<?= !empty($event['banner']) && file_exists('uploads/' . $event['banner']) ? 'uploads/' . $event['banner'] : 'https://via.placeholder.com/300x150' ?>" alt="<?= htmlspecialchars($event['nama_event']) ?>" class="event-img">
                        <div class="event-content">
                            <h3 class="event-title"><?= htmlspecialchars($event['nama_event']) ?></h3>
                            <div class="event-meta">
                                <span>🗓️ 
                                <?= date('d M Y', strtotime($event['tanggal_mulai'])) ?>
                                <?php if ($event['tanggal_mulai'] !== $event['tanggal_selesai']): ?>
                                    - <?= date('d M Y', strtotime($event['tanggal_selesai'])) ?>
                                <?php endif; ?>
                                </span>
                                <span>📍 <?= htmlspecialchars($event['lokasi']) ?></span>
                                <span>⏰ <?= htmlspecialchars($event['jam_mulai']) ?> - <?= htmlspecialchars($event['jam_selesai']) ?></span>
                            </div>
                            <div class="event-org">
                                <?php
                                $logo = !empty($event['logo']) && file_exists('uploads/' . $event['logo']) 
                                    ? $event['logo'] 
                                    : 'default.png';
                                $logo_path = "uploads/" . $logo;
                                ?>
                                <img src="<?= htmlspecialchars($logo_path) ?>" alt="<?= htmlspecialchars($event['nama_ukm']) ?>" class="org-logo" />
                                <span><?= htmlspecialchars($event['nama_ukm']) ?></span>
                            </div>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn-detail">
                                Lihat Detail <i>→</i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="events fade-up fade-delay-4">
        <h2 class="section-title">Ongoing Event</h2>
        <div id="ongoing-list" class="events-container ongoing">
            <?php if (empty($events)): ?>
                <p style="text-align:center">Belum ada event yang berlangsung.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <img src="<?= !empty($event['banner']) && file_exists('uploads/' . $event['banner']) ? 'uploads/' . $event['banner'] : 'https://via.placeholder.com/600x300' ?>" alt="<?= htmlspecialchars($event['nama_event']) ?>" class="event-img">
                        <div class="event-content">
                            <h3 class="event-title"><?= htmlspecialchars($event['nama_event']) ?></h3>
                            <div class="event-meta">
                                <span>🗓️ 
                                <?= date('d M Y', strtotime($event['tanggal_mulai'])) ?>
                                <?php if ($event['tanggal_mulai'] !== $event['tanggal_selesai']): ?>
                                    - <?= date('d M Y', strtotime($event['tanggal_selesai'])) ?>
                                <?php endif; ?>
                                </span>
                                <span>📍 <?= htmlspecialchars($event['lokasi']) ?></span>
                                <span>⏰ <?= htmlspecialchars($event['jam_mulai']) ?> - <?= htmlspecialchars($event['jam_selesai']) ?></span>
                            </div>
                            <div class="event-org">
                                <?php
                                $logo = !empty($event['logo']) && file_exists('uploads/' . $event['logo']) 
                                    ? $event['logo'] 
                                    : 'default.png';
                                $logo_path = "uploads/" . $logo;
                                ?>
                                <img src="<?= htmlspecialchars($logo_path) ?>" alt="<?= htmlspecialchars($event['nama_ukm']) ?>" class="org-logo" />
                                <span><?= htmlspecialchars($event['nama_ukm']) ?></span>
                            </div>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn-detail">
                                Lihat Detail <i>→</i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
    document.getElementById('search').addEventListener('input', function() {
        let keyword = this.value;

        fetch('search_event.php?q=' + encodeURIComponent(keyword))
            .then(response => response.json())
            .then(data => {
                document.querySelector('.events-container.upcoming').innerHTML = data.upcoming;
                document.querySelector('.events-container.ongoing').innerHTML = data.ongoing;
            })
            .catch(err => console.error("Error fetch data:", err));
    });
    </script>

    <footer class="fade-up fade-delay-5">
        <div class="footer-content" id="tentang">
            <div class="footer-section">
                <h3>Tentang InfoUKM</h3>
                <p>Portal resmi informasi kegiatan Unit Kegiatan Mahasiswa di lingkungan Universitas.</p>
                <p>Menghubungkan mahasiswa dengan berbagai kegiatan pengembangan diri.</p>
            </div>
            <div class="footer-section">
                <h3>Kontak</h3>
                <a href="mailto:info@infoukm.ac.id">info@infoukm.ac.id</a>
                <a href="tel:+622112345678">(021) 1234 5678</a>
                <p>Gedung R. Said Soekanto Lt. 4</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 InfoUKM. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>