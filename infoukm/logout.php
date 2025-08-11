<?php
session_start();
require 'koneksi.php';

// Kalau pakai remember_token, hapus dari DB
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

// Hapus cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/"); // expired
}

// Hapus session
session_unset();
session_destroy();

// Redirect ke login
header("Location: login.php");
exit;
