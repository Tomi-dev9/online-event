<?php
require __DIR__ . '/../vendor/autoload.php';


use GuzzleHttp\Client;

function sendMessage($channel_url, $message) {
    $api_url = "https://api-{APP_ID}.sendbird.com/v3/group_channels/$channel_url/messages";
    $api_token = "0521c71b51bc07e7c20b2b3adef56e97e03d297f"; // Ganti dengan API Token Anda

    $client = new Client();

    try {
        $response = $client->post($api_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $api_token"
            ],
            'json' => [
                'message_type' => 'MESG',
                'user_id' => '$penyelenggara_id', 
                'message' => $message
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            echo "Pesan berhasil dikirim.";
        } else {
            echo "Gagal mengirim pesan. Status code: " . $response->getStatusCode();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Contoh penggunaan
$channel_url = "group_channel_url"; // URL channel grup dari Sendbird
$message = "Halo! Sertifikat Anda tersedia di: https://example.com/certificate/123.pdf";
sendMessage($channel_url, $message);
?>
