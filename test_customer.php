<?php

require __DIR__ . '/vendor/autoload.php';

use Sixtytwopay\Client;
use Sixtytwopay\Exceptions\ApiException;
use Sixtytwopay\Services\CustomerService;

$apiKey = 'bf5c68892f1e60e87005e7d7c3cabfb81bcd9f9cd8e04b20fa4f56f12bc626e8';
$environment = 'sandbox';

$client = new Client($apiKey, $environment);
$customerService = new CustomerService($client);

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

    $response = $customerService->create($newCustomer);

    echo "Customer created successfully";

} catch (ApiException $e) {
    echo "API Exception: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "General Exception: " . $e->getMessage() . "\n";
}
