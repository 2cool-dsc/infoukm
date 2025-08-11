<?php
// Kasih Comment yang banyak biar yang mau nyoba bisa tau

// Konfigurasi
$wahaUrl = "http://localhost:3000"; // URL nya WAHA
$instance = "default"; // Instance name WAHA
$token = "SECRET_TOKEN"; // Token API WAHA
$groupName = "Nama groupnya"; // Ganti sesuai nama grup yang pengen dicari id grupnya

// Ambil semua chat
$ch = curl_init("$wahaUrl/api/$instance/chats");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die("Error: " . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);

// Cari grup berdasarkan nama
$chatId = null;
if (!empty($data)) {
    foreach ($data as $chat) {
        if (isset($chat['name']) && strtolower($chat['name']) === strtolower($groupName)) {
            $chatId = $chat['id']['_serialized'];
            break;
        }
    }
}

if ($chatId) {
    echo "Chat ID Grup: " . $chatId . "\n";
} else {
    echo "Grup dengan nama '$groupName' tidak ditemukan.\n";
}
