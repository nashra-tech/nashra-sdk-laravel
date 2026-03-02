# Nashra PHP SDK

Official PHP SDK for the [Nashra](https://nashra.ai) API.

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

```bash
composer require nashra/sdk
```

The package auto-discovers itself. No manual provider registration needed.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=nashra-config
```

Add your API key to `.env`:

```env
NASHRA_API_KEY=your-api-key
NASHRA_BASE_URL=https://app.nashra.ai/api/v1  # optional
NASHRA_TIMEOUT=30                               # optional, seconds
NASHRA_MAX_RETRIES=3                            # optional
```

## Usage

### Via Facade

```php
use Nashra\Sdk\Facades\Nashra;

// Subscribers
$subscribers = Nashra::subscribers()->list(['search' => 'john']);
$subscriber = Nashra::subscribers()->create(['email' => 'john@example.com', 'first_name' => 'John']);
$subscriber = Nashra::subscribers()->find('uuid');
$subscriber = Nashra::subscribers()->update('uuid', ['first_name' => 'Jane']);
Nashra::subscribers()->delete('uuid');

// Tags
$tags = Nashra::tags()->list();
$tag = Nashra::tags()->create(['name' => 'VIP', 'color' => '#FF0000']);
$tag = Nashra::tags()->update('uuid', ['name' => 'Premium']);
Nashra::tags()->delete('uuid');

// Custom Fields
$fields = Nashra::fields()->list();
$field = Nashra::fields()->create([
    'field_name' => 'Company',
    'data_name' => 'company',
    'type' => 'text',
]);
$field = Nashra::fields()->update('uuid', ['is_required' => true]);
Nashra::fields()->delete('uuid');

// Segments (read-only)
$segments = Nashra::segments()->list();
$segment = Nashra::segments()->find('uuid');
```

### Via Dependency Injection

```php
use Nashra\Sdk\Nashra;

class SubscriberController
{
    public function __construct(private Nashra $nashra) {}

    public function index()
    {
        return $this->nashra->subscribers()->list();
    }
}
```

### Direct Instantiation

Works outside of Laravel too:

```php
$nashra = new \Nashra\Sdk\Nashra('your-api-key');
$nashra->subscribers()->list();
```

## Pagination

Paginated endpoints return a `PaginatedResponse` that implements `IteratorAggregate`:

```php
// Auto-paginate through all pages
foreach (Nashra::subscribers()->list() as $subscriber) {
    echo $subscriber->email;
}

// Work with a single page
$page = Nashra::subscribers()->list(['page' => 2, 'per_page' => 50]);
$page->items();       // Subscriber[]
$page->total();       // int
$page->currentPage(); // int
$page->lastPage();    // int
$page->hasMorePages(); // bool
```

## Error Handling

The SDK throws typed exceptions for different error scenarios:

```php
use Nashra\Sdk\Exceptions\AuthenticationException;
use Nashra\Sdk\Exceptions\AuthorizationException;
use Nashra\Sdk\Exceptions\NotFoundException;
use Nashra\Sdk\Exceptions\ValidationException;
use Nashra\Sdk\Exceptions\RateLimitException;
use Nashra\Sdk\Exceptions\ApiException;

try {
    Nashra::subscribers()->create(['email' => 'invalid']);
} catch (ValidationException $e) {
    $e->errors(); // ['email' => ['The email must be a valid email address.']]
} catch (AuthenticationException $e) {
    // Invalid API key (401)
} catch (AuthorizationException $e) {
    // No newsletter configured (403)
} catch (NotFoundException $e) {
    // Resource not found (404)
} catch (RateLimitException $e) {
    $e->retryAfter(); // seconds until retry
} catch (ApiException $e) {
    // Server error (5xx)
}
```

## Data Objects

All API responses are mapped to readonly data objects:

- `Subscriber` - email, firstName, lastName, status, tags, extraAttributes, notes
- `Tag` - name, color
- `CustomField` - fieldName, dataName, type, isRequired
- `Segment` - name, subscribersCount, storedConditions

## Testing

Since the SDK uses Laravel's HTTP client under the hood, you can use `Http::fake()` to mock API responses in your tests:

```php
use Illuminate\Support\Facades\Http;
use Nashra\Sdk\Facades\Nashra;

Http::fake([
    '*/subscribers' => Http::response([
        'data' => [
            [
                'uuid' => 'test-uuid',
                'email' => 'john@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'status' => 'subscribed',
                'tags' => [],
            ],
        ],
        'meta' => [
            'current_page' => 1,
            'last_page' => 1,
            'total' => 1,
            'per_page' => 15,
        ],
    ]),
]);

$subscribers = Nashra::subscribers()->list();
```

You can also mock specific scenarios like validation errors:

```php
Http::fake([
    '*/subscribers' => Http::response([
        'error' => [
            'message' => 'Validation failed',
            'details' => ['email' => ['The email must be a valid email address.']],
        ],
    ], 422),
]);
```

## License

MIT
