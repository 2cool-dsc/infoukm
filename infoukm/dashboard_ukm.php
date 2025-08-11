<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ukm') {
    header("Location: login.php");
    exit;
}

// Ambil data user yang login
$user_id = $_SESSION['user_id'];
$nama_ukm = $_SESSION['nama_ukm'];

// Ambil event milik user ini
$stmt = $conn->prepare("SELECT * FROM events WHERE created_by = ? ORDER BY tanggal_mulai DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Hitung jumlah event
$total_event = $result->num_rows;
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UKM - Info UKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/dashboard_ukm.css?v=<?= time() ?>">
</head>
<body>
    <div class="header">
        <h1 class="page-title">Dashboard UKM</h1>
        <div class="btn-group">
            <a href="tambah_event.php" class="btn btn-primary"> 
                <span>➕</span>
                <span>Tambah Event</span>
            </a>
            <a href="logout.php" class="btn btn-secondary">
                <span>🚪</span>
                <span>Keluar</span>
            </a>
        </div>
    </div>
    
    <div class="isi">
    <div class="stat-card fade-up fade-delay-1">
        <div class="stat-title">Total Event</div>
        <div class="stat-value"><?= $total_event ?></div>
    </div>
    
    <h2 class="fade-up fade-delay-2" style="margin-bottom: 1rem; color: var(--primary);">Daftar Event Terbaru</h2>
    <div class="events-table table-responsive fade-up fade-delay-3">
        <table>
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_event']) ?></td>
                        <td>    
                            <?php if (!empty($row['tanggal_mulai']) && !empty($row['tanggal_selesai'])): ?>
                                <?php if ($row['tanggal_mulai'] === $row['tanggal_selesai']): ?>
                                    <?= date('l, d M Y', strtotime($row['tanggal_mulai'])) ?>
                                <?php else: ?>
                                    <?= date('l, d M Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                                <?php endif; ?>
                            <?php elseif (!empty($row['tanggal_mulai'])): ?>
                                <?= date('l, d M Y', strtotime($row['tanggal_mulai'])) ?>
                            <?php else: ?>
                                <em>Tanggal belum diisi</em>
                            <?php endif; ?>
                        </td>
                        <td><span class="status <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                        <td>
                            <button class="action-btn" onclick="window.location.href='edit_event.php?id=<?= $row['id'] ?>'">✏️</button>
                            <button class="action-btn" onclick="if(confirm('Yakin hapus event ini?')) window.location.href='hapus_event.php?id=<?= $row['id'] ?>'">🗑️</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>