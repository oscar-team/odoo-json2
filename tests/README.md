# Test Suite

This directory contains the test suite for the Odoo JSON-2 PHP library.

## Running Tests

```bash
# Run all tests
composer test

# Or directly with PHPUnit
vendor/bin/phpunit

# With test coverage (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage
```

## Test Structure

### Unit Tests (`tests/Unit/`)

- **ConfigTest.php**: Tests for `Odoo\Config` class
  - Config creation with required and optional parameters
  - Null database handling

- **ContextTest.php**: Tests for `Odoo\Context` class
  - Context creation and conversion to array
  - Custom context arguments
  - Context cloning and defaults

- **DomainTest.php**: Tests for `Odoo\Request\Arguments\Domain` class
  - Domain creation and where clauses
  - Multiple where conditions
  - OrWhere functionality

- **OptionsTest.php**: Tests for `Odoo\Request\Arguments\Options` class
  - Options creation
  - Context handling
  - Limit, offset, and raw options

- **RequestTest.php**: Tests for request classes
  - Search, Read, SearchRead requests
  - Create, Write, Unlink requests
  - FieldsGet request

- **Json2ClientTest.php**: Tests for `Json2\Client` class
  - Client initialization
  - API call handling with mocked responses
  - Error handling

- **OdooModelTest.php**: Tests for `Odoo\OdooModel` base class
  - Model booting
  - Existence checking
  - Fill method and property validation

- **RequestBuilderTest.php**: Tests for `Odoo\Request\RequestBuilder` class
  - Builder creation and fluent methods
  - Where, limit, offset, orderBy methods

- **OdooTest.php**: Tests for main `Odoo` class
  - Odoo instance creation
  - Connection handling
  - Model and search methods

- **EndpointTest.php**: Tests for endpoint classes
  - Endpoint creation
  - ObjectEndpoint functionality

- **CommonEndpointTest.php**: Tests for `Odoo\Endpoint\CommonEndpoint`
  - Authentication handling
  - Fixed user ID support

- **ExceptionTest.php**: Tests for exception classes
  - OdooException
  - OdooModelException
  - UndefinedPropertyException

- **HasFieldsTest.php**: Tests for `Odoo\Mapping\HasFields` trait
  - Model hydration from arrays
  - Model dehydration to arrays
  - Key field handling

## Test Coverage

The test suite covers:

- ✅ Configuration and context management
- ✅ Domain and options building
- ✅ All request types (Search, Read, Create, Write, Unlink, etc.)
- ✅ JSON-2 client with mocked HTTP responses
- ✅ OdooModel base class functionality
- ✅ Request builder fluent interface
- ✅ Main Odoo class integration
- ✅ Exception handling
- ✅ Model hydration and dehydration

## Notes

- Most tests use mocked HTTP responses to avoid requiring a real Odoo instance
- Some integration tests (marked with `expectException`) will fail without a real Odoo instance, but they verify the methods exist and are callable
- The test suite uses PHPUnit 9.x

