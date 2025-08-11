<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil semua UKM
$ukm_result = $conn->query("SELECT * FROM users WHERE role = 'ukm' ORDER BY nama_ukm");

// Ambil semua Event
$event_result = $conn->query("SELECT e.*, u.nama_ukm FROM events e JOIN users u ON e.created_by = u.id ORDER BY e.tanggal_mulai DESC");

$total_ukm = $ukm_result->num_rows;
$total_event = $event_result->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboard_admin.css?v=<?= time() ?>">
</head>
<body>
    <div class="header">
        <h1 class="page-title">Dashboard Admin</h1>
        <div class="btn-group">
            <a href="tambah_akun.php" class="btn btn-primary">
                <span>➕</span>
                <span>Tambah UKM</span>
            </a>
            <a href="logout.php" class="btn btn-secondary">
                <span>🚪</span>
                <span>Keluar</span>
            </a>
        </div>
    </div>
    <div class="isi">
        <div class="stats-container fade-up fade-delay-1">
            <div class="stat-card">
                <div class="stat-title">Total UKM Terdaftar</div>
                <div class="stat-value"><?= $total_ukm ?></div>
            </div>
            <div class="stat-card fade-up fade-delay-1">
                <div class="stat-title">Total Event Aktif</div>
                <div class="stat-value"><?= $total_event ?></div>
            </div>
        </div>
        
        <div class="data-section fade-up fade-delay-2">
            <h2 class="section-title">Data UKM</h2>
            <div class="data-table table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama UKM</th>
                            <th>Pembina</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ukm = $ukm_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <?php
                                        $logo = !empty($ukm['logo']) ? $ukm['logo'] : 'default.png';
                                        $logo_path = "uploads/" . $logo;

                                        if (!file_exists($logo_path)) {
                                            $logo_path = "uploads/default.png";
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($logo_path) ?>" alt="UKM Logo" class="ukm-avatar" />
                                        <span><?= htmlspecialchars($ukm['nama_ukm']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($ukm['pembina']) ?></td>
                                <td><span class="status <?= strtolower($ukm['status']) ?>"><?= htmlspecialchars($ukm['status']) ?></span></td>
                                <td>
                                    <button class="action-btn" onclick="window.location.href='edit_akun.php?id=<?= $ukm['id'] ?>'">✏️</button>
                                    <button class="action-btn" onclick="if(confirm('Yakin ingin menghapus akun UKM ini?')) window.location.href='hapus_akun.php?id=<?= $ukm['id'] ?>'">🗑️</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="data-section fade-up fade-delay-3">
            <h2 class="section-title">Event Berlangsung</h2>
            <div class="data-table table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Event</th>
                            <th>UKM</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($event = $event_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['nama_event']) ?></td>
                            <td><?= htmlspecialchars($event['nama_ukm']) ?></td>
                            <td>
                                <?php if (!empty($event['tanggal_mulai']) && !empty($event['tanggal_selesai'])): ?>
                                    <span>
                                        <?= date('l, d M Y', strtotime($event['tanggal_mulai'])) ?> - 
                                        <?= date('l, d M Y', strtotime($event['tanggal_selesai'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span>Tanggal belum tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="status <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span></td>
                            <td>
                                <button class="action-btn" onclick="window.location.href='edit_event.php?id=<?= $event['id'] ?>'">✏️</button>
                                <button class="action-btn" onclick="if(confirm('Yakin hapus?')) window.location.href='hapus_event.php?id=<?= $event['id'] ?>'">🗑️</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>