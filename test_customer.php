<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Sixtytwopay\Sixtytwopay;

$apiKey = 'bf5c68892f1e60e87005e7d7c3cabfb81bcd9f9cd8e04b20fa4f56f12bc626e8';
$environment = 'SANDBOX';

$gateway = new Sixtytwopay($apiKey, $environment);

try {
    $newCustomer = [
        'name' => 'Test User',
        'email' => 'testusera@example.com',
        'document_number' => '76.654.470/0001-30',
        'phone' => '22998438864',
        'address' => 'Rua ABC',
        'address_number' => '100',
        'complement' => 'Ap 101',
        'postal_code' => '01234567',
        'province' => 'SP',
        'city' => 'São Paulo',
        'type' => 'LEGAL',
        'legal_name' => 'Test User Ltd',
    ];

    $response = $gateway->customer()->create($newCustomer);

    echo "Customer created successfully";

} catch (Exception $e) {
    echo $e->getMessage();
} catch (GuzzleException $e) {
    echo $e->getMessage();
}
