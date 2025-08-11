<?php
session_start();
require 'koneksi.php';

// Pastikan user login dan berperan UKM atau Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['ukm', 'admin'])) {
    header("Location: login.html");
    exit;
}

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $user_id  = $_SESSION['user_id'];
    $role     = $_SESSION['role'];

    if ($role === 'admin') {
        // Admin bisa hapus event apa saja
        $stmt = $conn->prepare("SELECT banner FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
    } else {
        // UKM hanya bisa hapus event yang dia buat
        $stmt = $conn->prepare("SELECT banner FROM events WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ii", $event_id, $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if ($event) {
        // Hapus file banner jika ada
        if (!empty($event['banner']) && file_exists('uploads/' . $event['banner'])) {
            unlink('uploads/' . $event['banner']);
        }

        if ($role === 'admin') {
            $delete = $conn->prepare("DELETE FROM events WHERE id = ?");
            $delete->bind_param("i", $event_id);
        } else {
            $delete = $conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
            $delete->bind_param("ii", $event_id, $user_id);
        }
        $delete->execute();
    }
}

header("Location: dashboard_ukm.php");
exit;
?>
