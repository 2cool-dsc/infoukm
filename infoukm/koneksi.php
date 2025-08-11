<?php
$host = 'localhost'; // Ganti sama host databasenya yaa
$user = 'root';    // Ganti sama username databasenya yaa
$pass = '';        // Ganti sama password databasenya yaa
$dbname = 'infoukm';  // Ganti sama nama database yang dipake

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
