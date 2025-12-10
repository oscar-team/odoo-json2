<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OdooJson2\OdooClient;
use OdooJson2\Exceptions\OdooException;
use OdooJson2\Exceptions\AuthenticationException;
use OdooJson2\Exceptions\ConnectionException;

// Initialize the client
$client = new OdooClient(
    baseUrl: 'https://oscar.k1.funet.at',
    apiKey: '43f93e56d0f8596c5611a86fdcae549020c695d5',
    database: 'odoo19_oscar'
);

try {
    // Example 1: Search for partners
    echo "Searching for partners...\n";
    $partnerIds = $client->search('res.partner', [
        //['is_company', '=', true],
        ['display_name', 'ilike', "a%"],
    ], [
        'fields' => ['name', 'display_name'],
        'limit' => 1
    ]);
    echo "Found " . count($partnerIds) . " partners\n\n";

    // Example 2: Read partner details
    if (!empty($partnerIds)) {
        echo "Reading partner details...\n";
        $partners = $client->read('res.partner', array_slice($partnerIds, 0, 3), [
            'name',
            'email',
            'phone'
        ]);
        print_r($partners);
        echo "\n";
    }

    // Example 3: Search and read in one call
    echo "Search and read partners...\n";
    $partners = $client->searchRead('res.partner', [
        ['is_company', '=', false]
    ], [
        'fields' => ['name', 'email'],
        'limit' => 5
    ]);
    print_r($partners);
    echo "\n";

    // Example 4: Create a new partner
    echo "Creating a new partner...\n";
    $newPartnerId = $client->create('res.partner', [
        'name' => 'Test Partner ' . date('Y-m-d H:i:s'),
        'email' => 'test@example.com',
        'phone' => '+1234567890',
        'is_company' => false
    ]);
    echo "Created partner with ID: $newPartnerId\n\n";

    // Example 5: Update the partner
    if ($newPartnerId) {
        echo "Updating partner...\n";
        $success = $client->write('res.partner', [$newPartnerId], [
            'email' => 'updated@example.com',
            'phone' => '+9876543210'
        ]);
        echo $success ? "Partner updated successfully\n\n" : "Failed to update partner\n\n";
    }

    // Example 6: Count partners
    echo "Counting partners...\n";
    $totalCount = $client->count('res.partner', []);
    echo "Total partners: $totalCount\n\n";

    // Example 7: Get model fields
    echo "Getting partner model fields...\n";
    $fields = $client->fieldsGet('res.partner', ['type', 'required', 'string']);
    echo "Found " . count($fields) . " fields\n";
    echo "Sample fields:\n";
    foreach (array_slice($fields, 0, 5, true) as $fieldName => $fieldInfo) {
        echo "  - $fieldName: " . ($fieldInfo['type'] ?? 'N/A') . "\n";
    }
    echo "\n";

    // Example 8: Custom method call
    echo "Calling custom method...\n";
    // This is just an example - adjust based on your Odoo setup
    // $result = $client->call('sale.order', 'action_confirm', [
    //     'ids' => [123]
    // ]);

} catch (AuthenticationException $e) {
    echo "Authentication Error: " . $e->getMessage() . "\n";
    echo "Please check your API key and database name.\n";
} catch (ConnectionException $e) {
    echo "Connection Error: " . $e->getMessage() . "\n";
    echo "Please check your Odoo instance URL and network connection.\n";
} catch (OdooException $e) {
    echo "Odoo Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}

