<?php
$wahaUrl = "http://localhost:3000";
$token = "SECRET_TOKEN"; 
$chatId = "120363401259930700@g.us";
$session = "default"; // session WAHA kamu

$pesan = "✅ Test kirim pesan WA dari PHP\n\n" .
         "📅 Nama Event: Contoh Event\n" .
         "🗓 Tanggal: 2025-08-20\n" .
         "⏰ Jam: 08:00 - 15:00\n" .
         "📍 Lokasi: Aula Universitas\n" .
         "💰 Biaya: Gratis\n\n" .
         "🔗 Info lengkap: https://infoukm.my.id/event_detail.php?id=1"
         "";

$data = [
    "session" => $session,
    "chatId" => $chatId,
    "text" => $pesan
];

$ch = curl_init("$wahaUrl/api/sendText");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "Error WA: " . curl_error($ch);
} else {
    echo "Response WA: " . $response;
}
curl_close($ch);
