<?php
function kirimPesanGroupWA($pesan) {
    $wahaUrl = "http://localhost:3000";
    $token = "SECRET_TOKEN";

    $data = [
        "session" => "default", // nama session WAHA kamu
        "chatId" => "120363401259930700@g.us", // ID grup
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
    }
    curl_close($ch);

    return $response;
}

// Tes kirim
echo kirimPesanGroupWA("halo maniezz");
