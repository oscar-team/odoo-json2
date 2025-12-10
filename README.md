# Odoo JSON-2 PHP Client

A modern PHP client library for interacting with Odoo's JSON-2 API (Odoo 19+). This library provides a clean, type-safe interface for performing CRUD operations and calling custom methods on Odoo models.

## Features

- ✅ Modern JSON-2 API support (Odoo 19+)
- ✅ API key-based authentication
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Type-safe with PHP 8.2+
- ✅ Comprehensive error handling
- ✅ Built on Guzzle HTTP client
- ✅ Clean, Laravel-style code structure

## Requirements

- PHP 8.2 or higher
- Composer
- Odoo 19 or higher with JSON-2 API enabled

## Installation

```bash
composer require oscar-team/odoo-json2
```

## Configuration

### Getting Your API Key

1. Log in to your Odoo instance
2. Go to **Settings** → **Users & Companies** → **API Keys**
3. Create a new API key
4. Copy the generated API key

## Usage

### Basic Setup

```php
use OdooJson2\OdooClient;

$client = new OdooClient(
    baseUrl: 'https://your-odoo-instance.com',
    apiKey: 'your-api-key-here',
    database: 'your-database-name'
);
```

### Search Records

```php
// Search with domain
$partners = $client->search('res.partner', [
    ['name', '=', 'John Doe']
]);

// Search with options
$partners = $client->search('res.partner', [
    ['is_company', '=', true]
], [
    'limit' => 10,
    'offset' => 0,
    'order' => 'name asc'
]);
```

### Read Records

```php
// Read specific records
$partners = $client->read('res.partner', [1, 2, 3]);

// Read with specific fields
$partners = $client->read('res.partner', [1, 2, 3], [
    'name',
    'email',
    'phone'
]);
```

### Search and Read

```php
// Search and read in one call
$partners = $client->searchRead('res.partner', [
    ['is_company', '=', true]
], [
    'fields' => ['name', 'email', 'phone'],
    'limit' => 10
]);
```

### Create Records

```php
// Create a single record
$partnerId = $client->create('res.partner', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'is_company' => false
]);

// Create multiple records
$partnerIds = $client->create('res.partner', [
    [
        'name' => 'Company A',
        'is_company' => true,
    ],
    [
        'name' => 'Company B',
        'is_company' => true,
    ],
]);
```

### Update Records

```php
// Update records
$success = $client->write('res.partner', [1, 2], [
    'email' => 'newemail@example.com',
    'phone' => '+9876543210'
]);
```

### Delete Records

```php
// Delete records
$success = $client->unlink('res.partner', [1, 2, 3]);
```

### Count Records

```php
// Count records matching a domain
$count = $client->count('res.partner', [
    ['is_company', '=', true]
]);
```

### Get Model Fields

```php
// Get all fields of a model
$fields = $client->fieldsGet('res.partner');

// Get specific field attributes
$fields = $client->fieldsGet('res.partner', ['type', 'required', 'string']);
```

### Custom Method Calls

```php
// Call any custom method on a model
$result = $client->call('sale.order', 'action_confirm', [
    'ids' => [123],
    'context' => []
]);
```

### Error Handling

```php
use OdooJson2\Exceptions\OdooException;
use OdooJson2\Exceptions\AuthenticationException;
use OdooJson2\Exceptions\ConnectionException;

try {
    $partners = $client->search('res.partner', []);
} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Authentication failed: " . $e->getMessage();
} catch (ConnectionException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
} catch (OdooException $e) {
    // Handle other Odoo errors
    echo "Odoo error: " . $e->getMessage();
}
```

### Advanced Configuration

```php
// Custom HTTP client options
$client = new OdooClient(
    baseUrl: 'https://your-odoo-instance.com',
    apiKey: 'your-api-key-here',
    database: 'your-database-name',
    options: [
        'timeout' => 60,
        'verify' => false, // Only for development
        'proxy' => 'http://proxy.example.com:8080',
    ]
);

// Use custom HTTP client
$customClient = new \GuzzleHttp\Client([
    'timeout' => 120,
]);
$client->setHttpClient($customClient);
```

## Domain Syntax

The domain syntax follows Odoo's standard format:

```php
// Simple condition
[['field', '=', 'value']]

// Multiple conditions (AND)
[
    ['field1', '=', 'value1'],
    ['field2', '!=', 'value2']
]

// OR conditions
['|', ['field1', '=', 'value1'], ['field2', '=', 'value2']]

// Operators: =, !=, <, >, <=, >=, like, ilike, in, not in, child_of, parent_of
```

## Examples

### Working with Sales Orders

```php
// Create a sales order
$orderId = $client->create('sale.order', [
    'partner_id' => 1,
    'order_line' => [
        [
            'product_id' => 5,
            'product_uom_qty' => 10,
            'price_unit' => 100.00,
        ],
    ],
]);

// Confirm the order
$client->call('sale.order', 'action_confirm', [
    'ids' => [$orderId]
]);

// Search for confirmed orders
$orders = $client->searchRead('sale.order', [
    ['state', '=', 'sale']
], [
    'fields' => ['name', 'partner_id', 'amount_total'],
    'limit' => 20
]);
```

### Working with Products

```php
// Create a product
$productId = $client->create('product.product', [
    'name' => 'New Product',
    'type' => 'product',
    'categ_id' => 1,
    'list_price' => 99.99,
    'standard_price' => 50.00,
]);

// Update product price
$client->write('product.product', [$productId], [
    'list_price' => 89.99
]);

// Search products by category
$products = $client->searchRead('product.product', [
    ['categ_id', '=', 1],
    ['sale_ok', '=', true]
], [
    'fields' => ['name', 'list_price', 'qty_available'],
    'order' => 'list_price desc'
]);
```

## Eloquent-like Models

This library provides an Eloquent-like ORM for working with Odoo models, making it easy to define models with relationships and perform type-safe operations.

### Basic Model Definition

```php
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('res.partner')]
class Partner extends OdooModel
{
    #[Field]
    public string $name;

    #[Field('email')]
    public ?string $email;

    #[Field('phone')]
    public ?string $phone;

    #[Field('is_company')]
    public bool $isCompany = false;
}
```

### Field Attributes

Use the `#[Field]` attribute to map Odoo fields to PHP properties:

```php
#[Model('product.product')]
class Product extends OdooModel
{
    // Field name matches property name
    #[Field]
    public string $name;

    // Custom field name mapping
    #[Field('default_code')]
    public ?string $defaultCode;

    #[Field('list_price')]
    public ?float $listPrice;

    #[Field('standard_price')]
    public ?float $standardPrice;
}
```

### Many-to-One Relationships (BelongsTo)

Use `#[Key]` to extract the ID and `#[BelongsTo]` to define the relationship:

```php
#[Model('product.product')]
class Product extends OdooModel
{
    #[Field]
    public string $name;

    // Extract the ID from the many-to-one field
    #[Field('categ_id'), Key]
    public ?int $categoryId;

    // Extract the name from the many-to-one field
    #[Field('categ_id'), KeyName]
    public ?string $categoryName;

    // Define the relationship
    #[Field('categ_id'), BelongsTo(name: 'categ_id', class: ProductCategory::class)]
    public ?ProductCategory $category;
}
```

### One-to-Many Relationships (HasMany)

```php
#[Model('account.move')]
class AccountMove extends OdooModel
{
    #[Field('name')]
    public string $name;

    #[Field('partner_id'), Key]
    public ?int $partnerId;

    // One-to-many relationship
    #[Field('invoice_line_ids'), HasMany(class: AccountMoveLine::class, name: 'invoice_line_ids')]
    public ?array $invoiceLines;
}
```

### Complete Model Example

```php
use OdooJson2\Attributes\BelongsTo;
use OdooJson2\Attributes\Field;
use OdooJson2\Attributes\HasMany;
use OdooJson2\Attributes\Key;
use OdooJson2\Attributes\KeyName;
use OdooJson2\Attributes\Model;
use OdooJson2\Odoo\OdooModel;

#[Model('res.partner')]
class Partner extends OdooModel
{
    #[Field]
    public string $name;

    #[Field('display_name')]
    public ?string $displayName;

    #[Field('email')]
    public ?string $email;

    #[Field('phone')]
    public ?string $phone;

    #[Field('street')]
    public ?string $street;

    #[Field('city')]
    public ?string $city;

    #[Field('zip')]
    public ?string $zip;

    #[Field('country_id'), Key]
    public ?int $countryId;

    #[Field('country_id'), KeyName]
    public ?string $countryName;

    #[Field('is_company')]
    public bool $isCompany = false;

    #[Field('active')]
    public bool $active = true;

    // Self-referential relationship
    #[Field('parent_id'), Key]
    public ?int $parentId;

    #[Field('parent_id'), BelongsTo(name: 'parent_id', class: Partner::class)]
    public ?Partner $parent;

    #[Field('child_ids'), HasMany(class: Partner::class, name: 'child_ids')]
    public ?array $children;
}
```

### Initializing Models

Before using models, you need to boot the Odoo instance:

```php
use OdooJson2\Odoo;
use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\OdooModel;

// Create Odoo instance
$config = new Config(
    database: 'your-database',
    host: 'https://your-odoo-instance.com',
    apiKey: 'your-api-key'
);
$context = new Context();
$odoo = new Odoo($config, $context);

// Boot models
OdooModel::boot($odoo);
```

### Querying with Models

```php
// Find a record by ID
$partner = Partner::find(1);

// Get all records
$partners = Partner::all();

// Query with conditions
$partners = Partner::query()
    ->where('is_company', '=', true)
    ->where('active', '=', true)
    ->limit(10)
    ->orderBy('name')
    ->get();

// Get first matching record
$partner = Partner::query()
    ->where('email', '=', 'john@example.com')
    ->first();

// Count records
$count = Partner::query()
    ->where('is_company', '=', true)
    ->count();

// Get only IDs
$ids = Partner::query()
    ->where('active', '=', true)
    ->ids();
```

### Creating Records

```php
// Create a new partner
$partner = new Partner();
$partner->name = 'John Doe';
$partner->email = 'john@example.com';
$partner->phone = '+1234567890';
$partner->isCompany = false;
$partner->save();

// Or use fill method
$partner = new Partner();
$partner->fill([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
]);
$partner->save();
```

### Updating Records

```php
// Update an existing record
$partner = Partner::find(1);
$partner->email = 'newemail@example.com';
$partner->phone = '+9876543210';
$partner->save();

// Or update multiple records via query
Partner::query()
    ->where('is_company', '=', true)
    ->update(['active' => false]);
```

### Deleting Records

```php
// Delete a single record
$partner = Partner::find(1);
// Note: OdooModel doesn't have a delete() method by default
// Use the Odoo client directly:
$odoo->unlink('res.partner', [$partner->id]);

// Or delete via query
Partner::query()
    ->where('active', '=', false)
    ->delete();
```

### Working with Relationships

```php
// Access belongs-to relationship
$product = Product::find(1);
$category = $product->category; // Automatically loaded

// Access has-many relationship (lazy loaded)
$invoice = AccountMove::find(1);
$lines = $invoice->invoiceLines; // Lazy loaded when accessed

// Working with self-referential relationships
$partner = Partner::find(1);
$parent = $partner->parent; // Parent partner
$children = $partner->children; // Child partners
```

### Advanced Queries

```php
// Search with multiple conditions
$partners = Partner::query()
    ->where('is_company', '=', true)
    ->orWhere('email', '!=', null)
    ->limit(50)
    ->offset(10)
    ->orderBy('name', 'desc')
    ->get();

// Read specific records
$partners = Partner::read([1, 2, 3, 4, 5]);

// Get model fields
$fields = Partner::listFields();
```

### Real-World Examples

#### Example 1: Create a Partner with Relationships

```php
$partner = new Partner();
$partner->name = 'Acme Corporation';
$partner->email = 'contact@acme.com';
$partner->phone = '+1234567890';
$partner->isCompany = true;
$partner->street = '123 Main St';
$partner->city = 'New York';
$partner->zip = '10001';
$partner->countryId = 233; // USA
$partner->save();

echo "Created partner with ID: {$partner->id}\n";
```

#### Example 2: Find and Update Products

```php
// Find products by category
$products = Product::query()
    ->where('categ_id', '=', 1)
    ->where('sale_ok', '=', true)
    ->where('active', '=', true)
    ->get();

foreach ($products as $product) {
    echo "Product: {$product->name}, Price: {$product->listPrice}\n";
    
    // Update price
    $product->listPrice = $product->listPrice * 1.1; // 10% increase
    $product->save();
}
```

#### Example 3: Working with Invoices

```php
// Find invoices for a partner
$invoices = AccountMove::query()
    ->where('partner_id', '=', 1)
    ->where('move_type', '=', 'out_invoice')
    ->where('state', '=', 'posted')
    ->get();

foreach ($invoices as $invoice) {
    echo "Invoice: {$invoice->name}, Total: {$invoice->amountTotal}\n";
    
    // Access invoice lines (lazy loaded)
    if ($invoice->invoiceLines) {
        foreach ($invoice->invoiceLines as $line) {
            echo "  - Line: {$line->name}, Amount: {$line->priceTotal}\n";
        }
    }
}
```

#### Example 4: Hierarchical Partners

```php
// Find company partners
$companies = Partner::query()
    ->where('is_company', '=', true)
    ->get();

foreach ($companies as $company) {
    echo "Company: {$company->name}\n";
    
    // Access children (lazy loaded)
    if ($company->children) {
        foreach ($company->children as $child) {
            echo "  - Contact: {$child->name} ({$child->email})\n";
        }
    }
}
```

## Testing

```bash
# Run tests
composer test

# Run with coverage
composer test-coverage
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues, questions, or contributions, please open an issue on GitHub.

## Changelog

### 1.0.0
- Initial release
- Support for Odoo JSON-2 API
- Full CRUD operations
- API key authentication
- Comprehensive error handling

