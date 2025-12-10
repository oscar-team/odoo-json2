<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Exceptions\OdooException;
use OdooJson2\Exceptions\AuthenticationException;

// ============================================================================
// Basic Setup
// ============================================================================

// Initialize Odoo connection
$host = 'https://oscar.k1.funet.at';
$database = 'odoo19_oscar'; // Optional: If not provided, Odoo uses default database from API key
$apiKey = '43f93e56d0f8596c5611a86fdcae549020c695d5';

// Database is optional - if not provided, it will be sent as X-Odoo-Database header
// If omitted, Odoo will use the default database associated with the API key
$odoo = new Odoo(new Config(
    database: $database, // Can be null - database is sent as X-Odoo-Database header if provided
    host: $host,
    apiKey: $apiKey,
    sslVerify: true // Set to false for self-signed certificates
));

// Example without database (uses default from API key):
// $odoo = new Odoo(new Config(
//     database: null, // or omit the parameter
//     host: $host,
//     apiKey: $apiKey
// ));

// Connect to Odoo (this will authenticate using the API key)
$odoo->connect();

// ============================================================================
// Example 1: Using Model Query Builder (Fluent API)
// ============================================================================

echo "=== Example 1: Model Query Builder ===\n\n";

// Search and read partners using fluent API
$partners = $odoo->model('res.partner')
    //->where('is_company', '=', true)
    //->where('active', '=', true)
    ->fields(['display_name'])
    ->limit(10)
    ->orderBy('name', 'asc')
    ->get();

echo "Found " . count($partners) . " companies:\n";
foreach ($partners as $partner) {
    var_dump($partner);
    echo "  - " . ($partner['display_name'] ?? 'N/A') . "\n";
}
echo "\n";

// Get first matching record
$firstPartner = $odoo->model('res.partner')
    ->where('is_company', '=', false)
    ->first();

if ($firstPartner) {
    echo "First individual partner: " . ($firstPartner['name'] ?? 'N/A') . "\n\n";
}

// Count records
$companyCount = $odoo->model('res.partner')
    ->where('is_company', '=', true)
    ->count();

echo "Total companies: $companyCount\n\n";

// Get only IDs
$partnerIds = $odoo->model('res.partner')
    ->where('is_company', '=', true)
    ->ids();

echo "Company IDs: " . implode(', ', array_slice($partnerIds, 0, 5)) . "...\n\n";

// ============================================================================
// Example 2: Using Direct Methods
// ============================================================================

echo "=== Example 2: Direct Methods ===\n\n";

// Search with Domain
$domain = (new Domain())
    ->where('is_company', '=', true)
    ->where('active', '=', true);

$companyIds = $odoo->search('res.partner', $domain, offset: 0, limit: 5);
echo "Found " . count($companyIds) . " company IDs\n\n";

// Read specific records
if (!empty($companyIds)) {
    $companies = $odoo->read('res.partner', $companyIds, ['name', 'email', 'phone']);
    echo "Company details:\n";
    foreach ($companies as $company) {
        echo "  - " . ($company['name'] ?? 'N/A') . " (" . ($company['email'] ?? 'No email') . ")\n";
    }
    echo "\n";
}

// Search and read in one call
$partners = $odoo->searchRead(
    model: 'res.partner',
    domain: (new Domain())->where('is_company', '=', false),
    fields: ['name', 'email'],
    offset: 0,
    limit: 5
);

echo "Individual partners:\n";
foreach ($partners as $partner) {
    echo "  - " . ($partner['name'] ?? 'N/A') . "\n";
}
echo "\n";

// ============================================================================
// Example 3: Create, Update, Delete Operations
// ============================================================================

echo "=== Example 3: Create, Update, Delete ===\n\n";

// Create a new partner
try {
    $newPartnerId = $odoo->create('res.partner', [
        'name' => 'Test Company ' . date('Y-m-d H:i:s'),
        'is_company' => true,
        'email' => 'test@example.com',
        'phone' => '+1234567890',
    ]);
    
    echo "Created partner with ID: $newPartnerId\n";
    
    // Update the partner
    $updated = $odoo->write('res.partner', [$newPartnerId], [
        'email' => 'updated@example.com',
        'phone' => '+9876543210',
    ]);
    
    if ($updated) {
        echo "Partner updated successfully\n";
    }
    
    // Read the updated partner
    $updatedPartner = $odoo->find('res.partner', $newPartnerId, ['name', 'email', 'phone']);
    if ($updatedPartner) {
        echo "Updated partner: " . ($updatedPartner['name'] ?? 'N/A') . 
             " - " . ($updatedPartner['email'] ?? 'N/A') . "\n";
    }
    
    // Delete the partner
    $deleted = $odoo->deleteById('res.partner', $newPartnerId);
    if ($deleted) {
        echo "Partner deleted successfully\n\n";
    }
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 4: Using Model Query Builder for Updates
// ============================================================================

echo "=== Example 4: Model Query Builder Updates ===\n\n";

// Create using model syntax
try {
    $partnerId = $odoo->model('res.partner')
        ->create([
            'name' => 'My Company',
            'is_company' => true,
            'email' => 'mycompany@example.com',
        ]);
    
    echo "Created partner with ID: $partnerId\n";
    
    // Update using model syntax
    $updated = $odoo->model('res.partner')
        ->where('id', '=', $partnerId)
        ->update([
            'name' => 'My Updated Company',
            'phone' => '+1111111111',
        ]);
    
    if ($updated) {
        echo "Partner updated using model syntax\n";
    }
    
    // Delete using model syntax
    $deleted = $odoo->model('res.partner')
        ->where('id', '=', $partnerId)
        ->delete();
    
    if ($deleted) {
        echo "Partner deleted using model syntax\n\n";
    }
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 5: Advanced Domain Queries
// ============================================================================

echo "=== Example 5: Advanced Domain Queries ===\n\n";

// OR conditions
$domain = (new Domain())
    ->where('is_company', '=', true)
    ->orWhere('email', 'ilike', '@example.com');

$results = $odoo->search('res.partner', $domain, limit: 5);
echo "Partners matching OR condition: " . count($results) . "\n\n";

// ============================================================================
// Example 6: Group By and Aggregations
// ============================================================================

echo "=== Example 6: Group By ===\n\n";

try {
    $grouped = $odoo->model('res.partner')
        ->where('active', '=', true)
        ->groupBy(['country_id'])
        ->fields(['id', 'name'])
        ->get();
    
    echo "Grouped by country: " . count($grouped) . " groups\n";
    foreach (array_slice($grouped, 0, 3) as $group) {
        echo "  - Country ID: " . ($group['country_id'] ?? 'N/A') . "\n";
    }
    echo "\n";
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 7: Get Model Fields
// ============================================================================

echo "=== Example 7: Get Model Fields ===\n\n";

try {
    $fields = $odoo->fieldsGet('res.partner', ['name', 'email', 'phone'], ['type', 'required', 'string']);
    
    echo "Partner model fields:\n";
    foreach (['name', 'email', 'phone'] as $fieldName) {
        if (isset($fields->$fieldName)) {
            $field = $fields->$fieldName;
            $fieldArray = (array) $field;
            echo "  - $fieldName: " . ($fieldArray['type'] ?? 'N/A') . 
                 " (required: " . (($fieldArray['required'] ?? false) ? 'yes' : 'no') . ")\n";
        }
    }
    echo "\n";
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 8: Check Access Rights
// ============================================================================

echo "=== Example 8: Check Access Rights ===\n\n";

try {
    $canRead = $odoo->checkAccessRights('res.partner', 'read');
    $canWrite = $odoo->can('res.partner', 'write');
    $canCreate = $odoo->can('res.partner', 'create');
    
    echo "Access rights for res.partner:\n";
    echo "  - Read: " . ($canRead ? 'Yes' : 'No') . "\n";
    echo "  - Write: " . ($canWrite ? 'Yes' : 'No') . "\n";
    echo "  - Create: " . ($canCreate ? 'Yes' : 'No') . "\n\n";
    
    // Using model syntax
    $canDelete = $odoo->model('res.partner')->can('unlink');
    echo "  - Delete (using model): " . ($canDelete ? 'Yes' : 'No') . "\n\n";
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 9: Using Context
// ============================================================================

echo "=== Example 9: Using Context ===\n\n";

// Set context for language, timezone, etc.
$context = new Context(
    lang: 'en_US',
    timezone: 'UTC',
    companyId: 1
);

$odoo->setContext($context);

// Now all operations will use this context
$partners = $odoo->model('res.partner')
    ->where('is_company', '=', true)
    ->limit(1)
    ->get();

echo "Query executed with custom context\n\n";

// ============================================================================
// Example 10: Custom Method Calls
// ============================================================================

echo "=== Example 10: Custom Method Calls ===\n\n";

try {
    // Execute any custom method on a model
    // Example: Confirm a sale order
    // $result = $odoo->executeKw('sale.order', 'action_confirm', [
    //     [123], // ids
    //     []     // context
    // ]);
    
    echo "Custom method calls can be made using executeKw()\n";
    echo "Example: \$odoo->executeKw('sale.order', 'action_confirm', [[123], []])\n\n";
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 11: Error Handling
// ============================================================================

echo "=== Example 11: Error Handling ===\n\n";

try {
    // This will fail if the model doesn't exist
    $odoo->search('non.existent.model', new Domain());
} catch (OdooException $e) {
    echo "Caught OdooException: " . $e->getMessage() . "\n";
}

try {
    // This will fail with authentication error if API key is invalid
    $invalidOdoo = new Odoo(new Config(
        database: 'test',
        host: 'https://invalid-host.com',
        apiKey: 'invalid-key'
    ));
    $invalidOdoo->connect();
} catch (AuthenticationException $e) {
    echo "Caught AuthenticationException: " . $e->getMessage() . "\n";
} catch (OdooException $e) {
    echo "Caught OdooException: " . $e->getMessage() . "\n";
}

echo "\n";

// ============================================================================
// Example 12: Working with Sales Orders
// ============================================================================

echo "=== Example 12: Working with Sales Orders ===\n\n";

try {
    // Search for sales orders (if sale module is installed)
    $orders = $odoo->model('sale.order')
        ->where('state', '=', 'draft')
        ->fields(['name', 'partner_id', 'amount_total', 'date_order'])
        ->limit(5)
        ->get();
    
    echo "Found " . count($orders) . " draft sales orders\n";
    foreach ($orders as $order) {
        echo "  - Order: " . ($order['name'] ?? 'N/A') . 
             " | Total: " . ($order['amount_total'] ?? '0') . "\n";
    }
    echo "\n";
} catch (OdooException $e) {
    echo "Note: sale.order model may not be installed. Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 13: Working with Products
// ============================================================================

echo "=== Example 13: Working with Products ===\n\n";

try {
    // Search for products
    // Note: Using 'standard_price' instead of 'qty_available' as it's more commonly available
    $products = $odoo->model('product.product')
        ->where('sale_ok', '=', true)
        ->where('active', '=', true)
        ->fields(['name', 'list_price', 'standard_price'])
        ->orderBy('list_price', 'desc')
        ->limit(5)
        ->get();
    
    echo "Top 5 products by price:\n";
    foreach ($products as $product) {
        echo "  - " . ($product['name'] ?? 'N/A') . 
             " | Price: " . ($product['list_price'] ?? '0') . 
             " | Cost: " . ($product['standard_price'] ?? '0') . "\n";
    }
    echo "\n";
} catch (OdooException $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "=== Examples Complete ===\n";

