<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\OdooModel;
use OdooJson2\Examples\Models\Partner;
use OdooJson2\Examples\Models\Product;
use OdooJson2\Examples\Models\ProductCategory;
use OdooJson2\Examples\Models\AccountMove;
use OdooJson2\Examples\Models\AccountMoveLine;
use OdooJson2\Exceptions\OdooException;

// ============================================================================
// Configuration
// ============================================================================

$host = 'https://oscar.k1.funet.at';
$database = 'odoo19_oscar';
$apiKey = '43f93e56d0f8596c5611a86fdcae549020c695d5';

$odoo = new Odoo(new Config(
    database: $database,
    host: $host,
    apiKey: $apiKey,
    sslVerify: true
));

$odoo->connect();

// Boot OdooModel with the Odoo instance
OdooModel::boot($odoo);

echo "=== Eloquent-like Odoo Model Examples ===\n\n";

// ============================================================================
// Example 1: Using Partner Model
// ============================================================================

echo "=== Example 1: Partner Model ===\n\n";

try {
    // Query: Find all partners using model
    echo "1. Finding all partners...\n";
    $partners = Partner::all();
    echo "   Found " . count($partners) . " partners\n\n";

    // Query: Find specific partner
    echo "2. Finding partner by ID...\n";
    $partner = Partner::find(1);
    if ($partner) {
        echo "   Partner: " . ($partner->name ?? 'N/A') . "\n";
        echo "   Email: " . ($partner->email ?? 'N/A') . "\n";
        echo "   City: " . ($partner->city ?? 'N/A') . "\n";
    }
    echo "\n";

    // Query: Using query builder
    echo "3. Using query builder...\n";
    $companies = Partner::query()
        ->where('is_company', '=', true)
        ->where('active', '=', true)
        ->limit(5)
        ->orderBy('name', 'asc')
        ->get();

    echo "   Found " . count($companies) . " companies:\n";
    foreach ($companies as $company) {
        echo "   - " . ($company->name ?? 'N/A') . "\n";
    }
    echo "\n";

    // Create: Create new partner using model
    echo "4. Creating new partner using model...\n";
    $newPartner = new Partner();
    $newPartner->fill([
        'name' => 'Model Test Company ' . date('YmdHis'),
        'isCompany' => true,
        'email' => 'modeltest' . time() . '@example.com',
        'phone' => '+1234567890',
        'city' => 'Model City',
    ]);
    $newPartner->save();

    echo "   Created partner with ID: " . $newPartner->id . "\n";
    echo "   Partner name: " . $newPartner->name . "\n\n";

    // Update: Update partner
    echo "5. Updating partner...\n";
    $newPartner->email = 'updated' . time() . '@example.com';
    $newPartner->phone = '+9876543210';
    $newPartner->save();

    echo "   Partner updated successfully\n";
    echo "   Updated email: " . $newPartner->email . "\n\n";

    // Delete: Delete partner
    echo "6. Deleting partner...\n";
    $partnerId = $newPartner->id;
    $partnerToDelete = Partner::find($partnerId);
    if ($partnerToDelete) {
        // Note: OdooModel doesn't have a delete method, use Odoo directly
        $odoo->deleteById('res.partner', $partnerId);
        echo "   Partner deleted successfully\n";
    }
    echo "\n";

} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 2: Using Product Model
// ============================================================================

echo "=== Example 2: Product Model ===\n\n";

try {
    // Query: Find products
    echo "1. Finding products...\n";
    $products = Product::query()
        ->where('sale_ok', '=', true)
        ->where('active', '=', true)
        ->limit(5)
        ->get();

    echo "   Found " . count($products) . " products:\n";
    foreach ($products as $product) {
        echo "   - " . ($product->name ?? 'N/A');
        if (isset($product->listPrice)) {
            echo " | Price: " . number_format($product->listPrice, 2);
        }
        echo "\n";
    }
    echo "\n";

    // Create: Create new product
    echo "2. Creating new product...\n";
    try {
        $newProduct = new Product();
        $newProduct->fill([
            'name' => 'Model Test Product ' . date('YmdHis'),
            'type' => 'product',
            'saleOk' => true,
            'purchaseOk' => true,
            'listPrice' => 99.99,
            'standardPrice' => 50.00,
        ]);
        $newProduct->save();

        echo "   Created product with ID: " . $newProduct->id . "\n";
        echo "   Product name: " . $newProduct->name . "\n\n";

        // Update product
        echo "3. Updating product...\n";
        $newProduct->listPrice = 89.99;
        $newProduct->save();

        echo "   Product updated successfully\n\n";

        // Clean up
        $odoo->deleteById('product.product', $newProduct->id);
        echo "   Product deleted\n\n";
    } catch (OdooException $e) {
        echo "   Note: Product creation may require permissions. Error: " . $e->getMessage() . "\n\n";
    }

} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 3: Using Query Builder Methods
// ============================================================================

echo "=== Example 3: Query Builder Methods ===\n\n";

try {
    // Count
    echo "1. Counting partners...\n";
    $count = Partner::query()
        ->where('active', '=', true)
        ->count();
    echo "   Active partners: $count\n\n";

    // First
    echo "2. Getting first partner...\n";
    $firstPartner = Partner::query()
        ->where('is_company', '=', false)
        ->first();

    if ($firstPartner) {
        echo "   First partner: " . ($firstPartner->name ?? 'N/A') . "\n";
    }
    echo "\n";

    // Complex query
    echo "3. Complex query with OR condition...\n";
    $partners = Partner::query()
        ->where('is_company', '=', true)
        ->orWhere('email', 'ilike', '@example.com')
        ->limit(3)
        ->get();

    echo "   Found " . count($partners) . " partners\n\n";

} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 4: Relations (BelongsTo, HasMany)
// ============================================================================

echo "=== Example 4: Model Relations ===\n\n";

try {
    // Find partner with relations
    echo "1. Finding partner with country relation...\n";
    $partner = Partner::find(1);
    if ($partner) {
        echo "   Partner: " . ($partner->name ?? 'N/A') . "\n";
        if ($partner->countryId) {
            echo "   Country ID: " . $partner->countryId . "\n";
            if ($partner->countryName) {
                echo "   Country Name: " . $partner->countryName . "\n";
            }
        }
    }
    echo "\n";

} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 5: Batch Operations
// ============================================================================

echo "=== Example 5: Batch Operations ===\n\n";

try {
    // Read multiple partners
    echo "1. Reading multiple partners...\n";
    $partnerIds = $odoo->search('res.partner', new \OdooJson2\Odoo\Request\Arguments\Domain(), limit: 3);
    
    if (!empty($partnerIds)) {
        $partners = Partner::read($partnerIds);
        echo "   Read " . count($partners) . " partners:\n";
        foreach ($partners as $partner) {
            echo "   - " . ($partner->name ?? 'N/A') . "\n";
        }
    }
    echo "\n";

} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

echo "=== Model Examples Complete ===\n";

