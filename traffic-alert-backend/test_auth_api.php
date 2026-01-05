<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);

try {
    echo "Testing Registration...\n";
    $response = $client->post('auth/register', [
        'json' => [
            'ten_dang_nhap' => 'testuser_' . time(),
            'email' => 'test_' . time() . '@example.com',
            'mat_khau' => 'password123',
            'so_dien_thoai' => '0123456789'
        ]
    ]);
    echo "Registration Response: " . $response->getBody() . "\n\n";

    $data = json_decode($response->getBody(), true);
    $token = $data['data']['access_token'];

    echo "Testing Login...\n";
    $response = $client->post('auth/login', [
        'json' => [
            'login' => $data['data']['user']['email'],
            'mat_khau' => 'password123'
        ]
    ]);
    echo "Login Response: " . $response->getBody() . "\n\n";

    echo "Testing Me (Authenticated)...\n";
    $response = $client->get('auth/me', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ]
    ]);
    echo "Me Response: " . $response->getBody() . "\n\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
