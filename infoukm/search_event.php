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

// ================= UPCOMING EVENTS =================
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

$html_upcoming = '';
if (empty($upcoming_events)) {
    $html_upcoming = '<p style="text-align:center">Belum ada event mendatang.</p>';
} else {
    foreach ($upcoming_events as $event) {
        $banner = (!empty($event['banner']) && file_exists('uploads/' . $event['banner']))
            ? 'uploads/' . $event['banner']
            : 'https://via.placeholder.com/300x150';
        $logo_path = (!empty($event['logo']) && file_exists('uploads/' . $event['logo']))
            ? 'uploads/' . $event['logo']
            : 'uploads/default.png';

        $html_upcoming .= '
        <div class="event-card upcoming">
            <img src="'.htmlspecialchars($banner).'" alt="'.htmlspecialchars($event['nama_event']).'" class="event-img">
            <div class="event-content">
                <h3 class="event-title">'.htmlspecialchars($event['nama_event']).'</h3>
                <div class="event-meta">
                    <span>🗓️ '.date('d M Y', strtotime($event['tanggal_mulai']));
        if ($event['tanggal_mulai'] !== $event['tanggal_selesai']) {
            $html_upcoming .= ' - '.date('d M Y', strtotime($event['tanggal_selesai']));
        }
        $html_upcoming .= '</span>
                    <span>📍 '.htmlspecialchars($event['lokasi']).'</span>
                    <span>⏰ '.htmlspecialchars($event['jam_mulai']).' - '.htmlspecialchars($event['jam_selesai']).'</span>
                </div>
                <div class="event-org">
                    <img src="'.htmlspecialchars($logo_path).'" alt="'.htmlspecialchars($event['nama_ukm']).'" class="org-logo" />
                    <span>'.htmlspecialchars($event['nama_ukm']).'</span>
                </div>
                <a href="event_detail.php?id='.$event['id'].'" class="btn-detail">
                    Lihat Detail <i>→</i>
                </a>
            </div>
        </div>';
    }
}

// =============== ONGOING EVENTS ===============
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
    $stmt->bind_param("ss".$search_types, $today, $today, ...$search_params);
} else {
    $stmt->bind_param("ss", $today, $today);
}
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Bentuk HTML Ongoing
$html_ongoing = '';
if (empty($events)) {
    $html_ongoing = '<p style="text-align:center">Belum ada event yang berlangsung.</p>';
} else {
    foreach ($events as $event) {
        $banner = !empty($event['banner']) && file_exists('uploads/' . $event['banner'])
            ? 'uploads/' . $event['banner']
            : 'https://via.placeholder.com/600x300';
        $logo_path = (!empty($event['logo']) && file_exists('uploads/' . $event['logo']))
            ? 'uploads/' . $event['logo']
            : 'uploads/default.png';

        $html_ongoing .= '
        <div class="event-card">
            <img src="'.htmlspecialchars($banner).'" alt="'.htmlspecialchars($event['nama_event']).'" class="event-img">
            <div class="event-content">
                <h3 class="event-title">'.htmlspecialchars($event['nama_event']).'</h3>
                <div class="event-meta">
                    <span>🗓️ '.date('d M Y', strtotime($event['tanggal_mulai']));
        if ($event['tanggal_mulai'] !== $event['tanggal_selesai']) {
            $html_ongoing .= ' - '.date('d M Y', strtotime($event['tanggal_selesai']));
        }
        $html_ongoing .= '</span>
                    <span>📍 '.htmlspecialchars($event['lokasi']).'</span>
                    <span>⏰ '.htmlspecialchars($event['jam_mulai']).' - '.htmlspecialchars($event['jam_selesai']).'</span>
                </div>
                <div class="event-org">
                    <img src="'.htmlspecialchars($logo_path).'" alt="'.htmlspecialchars($event['nama_ukm']).'" class="org-logo" />
                    <span>'.htmlspecialchars($event['nama_ukm']).'</span>
                </div>
                <a href="event_detail.php?id='.$event['id'].'" class="btn-detail">
                    Lihat Detail <i>→</i>
                </a>
            </div>
        </div>';
    }
}

// Output JSON untuk diambil JS
header('Content-Type: application/json');
echo json_encode([
    'upcoming' => $html_upcoming,
    'ongoing'  => $html_ongoing
]);
