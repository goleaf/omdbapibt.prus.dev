# API Documentation

The OMDb API BT Platform provides several API endpoints for programmatic access to movie data and system functions.

## Base URL

```
Production: https://omdbapibt.prus.dev/api
Local: http://localhost:8000/api
```

## Authentication

Most API endpoints require authentication using Laravel Sanctum tokens or basic authentication.

### Basic Authentication

```bash
curl -u username:password https://omdbapibt.prus.dev/api/endpoint
```

## Available Endpoints

### 1. Movie Lookup API

Search for movies by IMDB ID or title.

**Endpoint:** `GET /api/movies/lookup`

**Authentication:** None (public endpoint)

**Rate Limit:** `movie-lookup` throttle (configurable)

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `imdb_id` | string | No | IMDB ID (e.g., tt1234567) |
| `title` | string | No | Movie title to search |
| `year` | integer | No | Release year filter |

**Example Request:**

```bash
# Search by IMDB ID
curl "https://omdbapibt.prus.dev/api/movies/lookup?imdb_id=tt0111161"

# Search by title
curl "https://omdbapibt.prus.dev/api/movies/lookup?title=The%20Shawshank%20Redemption"

# Search by title and year
curl "https://omdbapibt.prus.dev/api/movies/lookup?title=Inception&year=2010"
```

**Example Response:**

```json
{
  "data": {
    "id": 123,
    "tmdb_id": 278,
    "imdb_id": "tt0111161",
    "slug": "the-shawshank-redemption",
    "title": {
      "en": "The Shawshank Redemption"
    },
    "year": 1994,
    "runtime": 142,
    "release_date": "1994-09-23",
    "overview": {
      "en": "Framed in the 1940s for the double murder of his wife..."
    },
    "vote_average": 8.7,
    "vote_count": 25000,
    "poster_path": "/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg",
    "backdrop_path": "/j9XKiZrVeViAixVRzCta7h1VU9W.jpg",
    "genres": [
      {
        "id": 1,
        "name": "Drama"
      }
    ],
    "platforms": [
      {
        "id": 1,
        "name": "Netflix",
        "availability": "available",
        "link": "https://netflix.com/..."
      }
    ]
  }
}
```

**Error Responses:**

```json
// Movie not found
{
  "message": "Movie not found"
}

// Validation error
{
  "message": "The given data was invalid.",
  "errors": {
    "imdb_id": [
      "The IMDB ID must start with 'tt'."
    ]
  }
}
```

---

### 2. Parser Trigger API

Trigger the movie parser to fetch and update movie metadata.

**Endpoint:** `POST /api/parser/trigger`

**Authentication:** Required

**Rate Limit:** `parser-trigger` throttle

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `imdb_ids` | array | No | Array of IMDB IDs to parse |
| `movie_ids` | array | No | Array of internal movie IDs |
| `limit` | integer | No | Number of movies to parse (default: 50) |
| `force` | boolean | No | Force re-parsing (ignore cache) |

**Example Request:**

```bash
curl -X POST https://omdbapibt.prus.dev/api/parser/trigger \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "imdb_ids": ["tt0111161", "tt0068646"],
    "force": true
  }'
```

**Example Response:**

```json
{
  "message": "Parser triggered successfully",
  "data": {
    "queued": 2,
    "movies": [
      {
        "id": 123,
        "imdb_id": "tt0111161",
        "status": "queued"
      },
      {
        "id": 124,
        "imdb_id": "tt0068646",
        "status": "queued"
      }
    ]
  }
}
```

---

### 3. OMDB Keys Import API

Bulk import OMDB API keys for validation.

**Endpoint:** `POST /api/omdb-keys/import`

**Authentication:** Required (Admin only)

**Rate Limit:** Standard API rate limit

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `keys` | array | Yes | Array of API keys to import |
| `validate` | boolean | No | Validate immediately (default: false) |

**Example Request:**

```bash
curl -X POST https://omdbapibt.prus.dev/api/omdb-keys/import \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{
    "keys": [
      "abc12345",
      "def67890",
      "ghi24680"
    ],
    "validate": true
  }'
```

**Example Response:**

```json
{
  "message": "Keys imported successfully",
  "data": {
    "imported": 3,
    "duplicates": 0,
    "validation_queued": 3
  }
}
```

---

## Rate Limiting

The API uses Laravel's built-in rate limiting. Different endpoints have different limits:

### Default Limits

- **Movie Lookup:** 60 requests per minute
- **Parser Trigger:** 10 requests per minute
- **General API:** 60 requests per minute

### Rate Limit Headers

All API responses include rate limit headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60
```

### Rate Limit Exceeded Response

```json
{
  "message": "Too Many Attempts.",
  "retry_after": 60
}
```

## Error Handling

### Standard Error Response Format

```json
{
  "message": "Error description",
  "errors": {
    "field_name": [
      "Specific validation error"
    ]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

## Authentication Methods

### 1. API Tokens (Sanctum)

Generate a token:

```php
$token = $user->createToken('api-token')->plainTextToken;
```

Use in requests:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  https://omdbapibt.prus.dev/api/endpoint
```

### 2. Basic Authentication

```bash
curl -u username:password \
  https://omdbapibt.prus.dev/api/endpoint
```

### 3. Session Authentication

For same-domain requests, use Laravel's session-based authentication.

## Pagination

List endpoints support pagination:

**Query Parameters:**

- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

**Paginated Response:**

```json
{
  "data": [...],
  "links": {
    "first": "https://api.example.com/movies?page=1",
    "last": "https://api.example.com/movies?page=10",
    "prev": null,
    "next": "https://api.example.com/movies?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "https://api.example.com/movies",
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

## Filtering and Sorting

### Common Query Parameters

| Parameter | Description | Example |
|-----------|-------------|---------|
| `sort` | Sort field | `?sort=title` |
| `order` | Sort direction | `?order=desc` |
| `filter[field]` | Filter by field | `?filter[year]=2024` |
| `search` | Full-text search | `?search=inception` |

### Example Requests

```bash
# Sort by rating descending
curl "https://api.example.com/movies?sort=vote_average&order=desc"

# Filter by year and genre
curl "https://api.example.com/movies?filter[year]=2024&filter[genre]=action"

# Search with filters
curl "https://api.example.com/movies?search=batman&filter[year]=2022"
```

## Webhook Endpoints

### Stripe Webhook

**Endpoint:** `POST /webhooks/stripe`

**Authentication:** Webhook signature verification

This endpoint handles Stripe payment events:

- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

**Note:** This endpoint is managed automatically by Laravel Cashier.

## CORS Configuration

The API supports CORS for cross-origin requests:

- **Allowed Origins:** Configurable in `.env`
- **Allowed Methods:** GET, POST, PUT, DELETE, OPTIONS
- **Allowed Headers:** Content-Type, Authorization, X-Requested-With

## API Versioning

Currently, the API is at version 1.0. Future versions will be accessible via:

```
https://omdbapibt.prus.dev/api/v2/endpoint
```

## Interactive Documentation

For interactive API documentation with live testing:

```
https://omdbapibt.prus.dev/docs
```

Generated using [Scribe](https://scribe.knuckles.wtf/).

## Code Examples

### PHP (Laravel HTTP Client)

```php
use Illuminate\Support\Facades\Http;

$response = Http::get('https://omdbapibt.prus.dev/api/movies/lookup', [
    'imdb_id' => 'tt0111161'
]);

if ($response->successful()) {
    $movie = $response->json('data');
}
```

### JavaScript (Fetch API)

```javascript
fetch('https://omdbapibt.prus.dev/api/movies/lookup?imdb_id=tt0111161')
  .then(response => response.json())
  .then(data => console.log(data.data))
  .catch(error => console.error('Error:', error));
```

### Python (Requests)

```python
import requests

response = requests.get(
    'https://omdbapibt.prus.dev/api/movies/lookup',
    params={'imdb_id': 'tt0111161'}
)

if response.ok:
    movie = response.json()['data']
```

### cURL

```bash
curl -X GET \
  "https://omdbapibt.prus.dev/api/movies/lookup?imdb_id=tt0111161" \
  -H "Accept: application/json"
```

## Best Practices

1. **Use HTTPS** for all API requests in production
2. **Cache responses** when appropriate to reduce API calls
3. **Handle rate limits** gracefully with exponential backoff
4. **Validate input** before sending requests
5. **Store tokens securely** never commit to version control
6. **Use environment variables** for API URLs and tokens
7. **Handle errors** with proper try-catch blocks
8. **Log API interactions** for debugging and monitoring

## Support

For API support:

- **Issues:** [GitHub Issues](https://github.com/goleaf/omdbapibt.prus.dev/issues)
- **Email:** See repository for contact information
- **Documentation:** This wiki

---

**Last Updated:** October 17, 2025

