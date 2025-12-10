<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Exceptions\OdooException;
use OdooJson2\Exceptions\AuthenticationException;

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

echo "=== Practical Odoo JSON-2 API Examples ===\n\n";

// ============================================================================
// Example 1: Working with Partners (res.partner)
// ============================================================================

echo "=== Example 1: Partners (res.partner) ===\n\n";

try {
    // Query: Find all active companies
    echo "1. Searching for active companies...\n";
    $companies = $odoo->model('res.partner')
        ->where('is_company', '=', true)
        ->where('active', '=', true)
        ->fields(['name', 'email', 'phone', 'city', 'country_id'])
        ->limit(5)
        ->orderBy('name', 'asc')
        ->get();
    
    echo "   Found " . count($companies) . " companies:\n";
    foreach ($companies as $company) {
        echo "   - " . ($company['name'] ?? 'N/A');
        if (!empty($company['email'])) {
            echo " (" . $company['email'] . ")";
        }
        echo "\n";
    }
    echo "\n";
    
    // Insert: Create a new partner
    echo "2. Creating a new partner...\n";
    $newPartnerId = $odoo->create('res.partner', [
        'name' => 'Test Company ' . date('YmdHis'),
        'is_company' => true,
        'email' => 'test' . time() . '@example.com',
        'phone' => '+1234567890',
        'street' => '123 Test Street',
        'city' => 'Test City',
        'zip' => '12345',
    ]);
    
    echo "   Created partner with ID: $newPartnerId\n";
    
    // Read: Get the newly created partner
    echo "3. Reading the created partner...\n";
    $newPartner = $odoo->read('res.partner', [$newPartnerId], ['name', 'email', 'phone', 'city']);
    if (!empty($newPartner)) {
        $partner = $newPartner[0];
        echo "   Partner: " . ($partner['name'] ?? 'N/A') . "\n";
        echo "   Email: " . ($partner['email'] ?? 'N/A') . "\n";
        echo "   Phone: " . ($partner['phone'] ?? 'N/A') . "\n";
    }
    echo "\n";
    
    // Update: Modify the partner
    echo "4. Updating the partner...\n";
    $updated = $odoo->write('res.partner', [$newPartnerId], [
        'email' => 'updated' . time() . '@example.com',
        'phone' => '+9876543210',
        'city' => 'Updated City',
    ]);
    
    if ($updated) {
        echo "   Partner updated successfully\n";
        
        // Verify the update
        $updatedPartner = $odoo->read('res.partner', [$newPartnerId], ['email', 'phone', 'city']);
        if (!empty($updatedPartner)) {
            $partner = $updatedPartner[0];
            echo "   Updated Email: " . ($partner['email'] ?? 'N/A') . "\n";
            echo "   Updated Phone: " . ($partner['phone'] ?? 'N/A') . "\n";
        }
    }
    echo "\n";
    
    // Delete: Remove the test partner
    echo "5. Deleting the test partner...\n";
    $deleted = $odoo->deleteById('res.partner', $newPartnerId);
    if ($deleted) {
        echo "   Partner deleted successfully\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 2: Working with Products (product.product)
// ============================================================================

echo "=== Example 2: Products (product.product) ===\n\n";

try {
    // Query: Find sellable products
    echo "1. Searching for sellable products...\n";
    $products = $odoo->model('product.product')
        ->where('sale_ok', '=', true)
        ->where('active', '=', true)
        ->fields(['name', 'list_price', 'standard_price', 'categ_id'])
        ->limit(5)
        ->orderBy('list_price', 'desc')
        ->get();
    
    echo "   Found " . count($products) . " products:\n";
    foreach ($products as $product) {
        echo "   - " . ($product['name'] ?? 'N/A');
        if (isset($product['list_price'])) {
            echo " | Price: " . number_format($product['list_price'], 2);
        }
        echo "\n";
    }
    echo "\n";
    
    // Query: Count products in a category
    echo "2. Counting products...\n";
    $productCount = $odoo->model('product.product')
        ->where('active', '=', true)
        ->count();
    echo "   Total active products: $productCount\n\n";
    
    // Insert: Create a new product (if you have permissions)
    echo "3. Creating a new product...\n";
    try {
        // First, get a product category
        $categories = $odoo->model('product.category')
            ->limit(1)
            ->get();
        
        $categoryId = null;
        if (!empty($categories)) {
            $categoryId = $categories[0]['id'] ?? null;
        }
        
        $newProductId = $odoo->create('product.product', [
            'name' => 'Test Product ' . date('YmdHis'),
            'type' => 'product',
            'sale_ok' => true,
            'purchase_ok' => true,
            'list_price' => 99.99,
            'standard_price' => 50.00,
            'categ_id' => $categoryId,
        ]);
        
        echo "   Created product with ID: $newProductId\n";
        
        // Update the product
        echo "4. Updating the product price...\n";
        $updated = $odoo->write('product.product', [$newProductId], [
            'list_price' => 89.99,
        ]);
        
        if ($updated) {
            echo "   Product price updated\n";
        }
        
        // Clean up: Delete the test product
        echo "5. Deleting the test product...\n";
        $odoo->deleteById('product.product', $newProductId);
        echo "   Product deleted\n\n";
        
    } catch (OdooException $e) {
        echo "   Note: Product creation may require specific permissions. Error: " . $e->getMessage() . "\n\n";
    }
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 3: Working with Users (res.users)
// ============================================================================

echo "=== Example 3: Users (res.users) ===\n\n";

try {
    // Query: Find active users
    echo "1. Searching for active users...\n";
    $users = $odoo->model('res.users')
        ->where('active', '=', true)
        ->fields(['name', 'login', 'email'])
        ->limit(5)
        ->get();
    
    echo "   Found " . count($users) . " active users:\n";
    foreach ($users as $user) {
        echo "   - " . ($user['name'] ?? 'N/A');
        if (!empty($user['login'])) {
            echo " (" . $user['login'] . ")";
        }
        echo "\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 4: Working with Product Categories (product.category)
// ============================================================================

echo "=== Example 4: Product Categories (product.category) ===\n\n";

try {
    // Query: Get all product categories
    echo "1. Listing product categories...\n";
    $categories = $odoo->model('product.category')
        ->fields(['name', 'parent_id'])
        ->limit(10)
        ->orderBy('name', 'asc')
        ->get();
    
    echo "   Found " . count($categories) . " categories:\n";
    foreach ($categories as $category) {
        echo "   - " . ($category['name'] ?? 'N/A') . "\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 5: Advanced Queries with Domain
// ============================================================================

echo "=== Example 5: Advanced Domain Queries ===\n\n";

try {
    // Complex domain with OR condition
    echo "1. Finding partners with OR condition...\n";
    $domain = (new Domain())
        ->where('is_company', '=', true)
        ->orWhere('email', 'ilike', '@example.com');
    
    $partners = $odoo->search('res.partner', $domain, limit: 5);
    echo "   Found " . count($partners) . " partner IDs\n\n";
    
    // Search and read with specific fields
    echo "2. Search and read with specific fields...\n";
    $partners = $odoo->searchRead(
        model: 'res.partner',
        domain: (new Domain())->where('active', '=', true),
        fields: ['name', 'email', 'phone'],
        limit: 3
    );
    
    echo "   Found " . count($partners) . " partners:\n";
    foreach ($partners as $partner) {
        echo "   - " . ($partner['name'] ?? 'N/A');
        if (!empty($partner['email'])) {
            echo " | " . $partner['email'];
        }
        echo "\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 6: Batch Operations
// ============================================================================

echo "=== Example 6: Batch Operations ===\n\n";

try {
    // Create multiple partners at once
    echo "1. Creating multiple partners...\n";
    $partnerData = [
        [
            'name' => 'Batch Company A ' . date('His'),
            'is_company' => true,
            'email' => 'batcha' . time() . '@example.com',
        ],
        [
            'name' => 'Batch Company B ' . date('His'),
            'is_company' => true,
            'email' => 'batchb' . time() . '@example.com',
        ],
    ];
    
    $createdIds = $odoo->create('res.partner', $partnerData);
    
    if (is_array($createdIds)) {
        echo "   Created " . count($createdIds) . " partners with IDs: " . implode(', ', $createdIds) . "\n";
        
        // Update multiple partners at once
        echo "2. Updating multiple partners...\n";
        $updated = $odoo->write('res.partner', $createdIds, [
            'city' => 'Batch City',
        ]);
        
        if ($updated) {
            echo "   Updated " . count($createdIds) . " partners\n";
        }
        
        // Delete multiple partners
        echo "3. Deleting multiple partners...\n";
        $deleted = $odoo->unlink('res.partner', $createdIds);
        if ($deleted) {
            echo "   Deleted " . count($createdIds) . " partners\n";
        }
    } else {
        echo "   Created partner with ID: $createdIds\n";
        $odoo->deleteById('res.partner', $createdIds);
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 7: Using find() method
// ============================================================================

echo "=== Example 7: Using find() Method ===\n\n";

try {
    // Find a specific partner by ID
    echo "1. Finding a specific partner...\n";
    
    // First, get an ID
    $partnerIds = $odoo->search('res.partner', new Domain(), limit: 1);
    
    if (!empty($partnerIds)) {
        $partnerId = $partnerIds[0];
        $partner = $odoo->find('res.partner', $partnerId, ['name', 'email', 'phone']);
        
        if ($partner) {
            echo "   Found partner ID $partnerId:\n";
            echo "   Name: " . ($partner['name'] ?? 'N/A') . "\n";
            echo "   Email: " . ($partner['email'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   No partners found\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 8: Get Model Fields
// ============================================================================

echo "=== Example 8: Getting Model Fields ===\n\n";

try {
    echo "1. Getting fields for res.partner model...\n";
    $fields = $odoo->fieldsGet('res.partner', null, ['type', 'required', 'string']);
    
    $fieldCount = is_object($fields) ? count((array) $fields) : 0;
    echo "   Found $fieldCount fields\n";
    
    // Display some common fields
    $commonFields = ['name', 'email', 'phone', 'city', 'country_id'];
    echo "   Common fields:\n";
    foreach ($commonFields as $fieldName) {
        if (isset($fields->$fieldName)) {
            $field = (array) $fields->$fieldName;
            echo "   - $fieldName: " . ($field['type'] ?? 'N/A');
            if (isset($field['string'])) {
                echo " (" . $field['string'] . ")";
            }
            echo "\n";
        }
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 9: Check Access Rights
// ============================================================================

echo "=== Example 9: Checking Access Rights ===\n\n";

try {
    echo "1. Checking access rights for res.partner...\n";
    
    $canRead = $odoo->can('res.partner', 'read');
    $canWrite = $odoo->can('res.partner', 'write');
    $canCreate = $odoo->can('res.partner', 'create');
    $canDelete = $odoo->can('res.partner', 'unlink');
    
    echo "   Read: " . ($canRead ? 'Yes' : 'No') . "\n";
    echo "   Write: " . ($canWrite ? 'Yes' : 'No') . "\n";
    echo "   Create: " . ($canCreate ? 'Yes' : 'No') . "\n";
    echo "   Delete: " . ($canDelete ? 'Yes' : 'No') . "\n";
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 10: Using Model Query Builder for Updates
// ============================================================================

echo "=== Example 10: Model Query Builder Updates ===\n\n";

try {
    // Create a partner using model syntax
    echo "1. Creating partner using model syntax...\n";
    $partnerId = $odoo->model('res.partner')
        ->create([
            'name' => 'Model Syntax Test ' . date('His'),
            'is_company' => false,
            'email' => 'modelsyntax' . time() . '@example.com',
        ]);
    
    echo "   Created partner with ID: $partnerId\n";
    
    // Update using model syntax
    echo "2. Updating using model syntax...\n";
    $updated = $odoo->model('res.partner')
        ->where('id', '=', $partnerId)
        ->update([
            'phone' => '+1111111111',
            'city' => 'Model City',
        ]);
    
    if ($updated) {
        echo "   Partner updated successfully\n";
    }
    
    // Delete using model syntax
    echo "3. Deleting using model syntax...\n";
    $deleted = $odoo->model('res.partner')
        ->where('id', '=', $partnerId)
        ->delete();
    
    if ($deleted) {
        echo "   Partner deleted successfully\n";
    }
    echo "\n";
    
} catch (OdooException $e) {
    echo "   Error: " . $e->getMessage() . "\n\n";
}

echo "=== All Examples Complete ===\n";

