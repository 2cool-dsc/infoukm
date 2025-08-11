<?php
session_start();
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_ukm'] = $user['nama_ukm'];

            if ($remember) {
                // Generate token unik cihuy
                $token = bin2hex(random_bytes(32));
                
                // Simpan token ke database
                $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->bind_param("si", $token, $user['id']);
                $stmt->execute();

                // Set cookie 30 hari
                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);
            }

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_ukm.php");
            }
            exit;
        }
    }

    header("Location: login.php?error=Email atau password salah");
    exit;
}
