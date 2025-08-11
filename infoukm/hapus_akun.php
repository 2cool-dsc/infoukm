<?php
session_start();
require 'koneksi.php';

// Pastikan user login dan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Pastikan ID dikirim
if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Hapus data UKM berdasarkan ID
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'ukm'");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard_admin.php");
    exit;
} else {
    echo "Gagal menghapus akun UKM: " . $stmt->error;
}
?>