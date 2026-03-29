# Prizes API Documentation

## List Available Prizes for a Round

Returns a list of active prizes available in a specific round.

### Endpoint

```
GET /api/prizes/{round}
```

### Authentication

**Not required** - This endpoint is public (can be accessed by anyone)

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `round` | integer | Yes | Round ID to get prizes for |

### Response

**Success Response (200 OK):**

```json
{
    "success": true,
    "data": [
        {
            "id": "a169022b-146e-4117-8fcd-9d860256a00b",
            "name": "Beer Voucher",
            "description": "A voucher for a refreshing beer",
            "amount": 20,
            "cost": 10,
            "content": "<p>Redeem this voucher for a cold beer at participating locations. Valid for 30 days from the date of issue.</p><p>Enjoy responsibly!</p>"
        },
        {
            "id": "a169022b-9d41-4eb1-acee-57f262910e97",
            "name": "Craft Beer Prize",
            "description": "Local craft beer selection",
            "amount": 20,
            "cost": 15,
            "content": "<p>Winner receives their choice of craft beer from our selection of local breweries.</p><ul><li>Valid at partner locations</li><li>Must be 18+ to redeem</li><li>Cannot be combined with other offers</li></ul>"
        }
    ]
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if the request was successful |
| `data` | array | Array of prize objects |
| `data[].id` | string (UUID) | Unique prize identifier |
| `data[].name` | string | Prize name |
| `data[].description` | string | Short description of the prize |
| `data[].amount` | integer | Prize quantity/value (e.g., 20ml) |
| `data[].cost` | integer | How many score points needed to redeem |
| `data[].content` | string | HTML content with full details and terms |
| `data[].image_url` | string\|null | Full URL to prize image, or null if no image |

### Example Usage

**cURL:**
```bash
curl -X GET http://your-domain.com/api/prizes/1
```

**JavaScript (fetch):**
```javascript
const roundId = 1;
fetch(`/api/prizes/${roundId}`)
  .then(response => response.json())
  .then(data => {
    console.log('Available prizes:', data.data);
    data.data.forEach(prize => {
      console.log(`${prize.name} - ${prize.cost} points`);
    });
  });
```

**PHP:**
```php
$roundId = 1;
$response = Http::get(config('app.url') . "/api/prizes/{$roundId}");
$prizes = $response->json('data');
```

### Notes

- Only prizes with `status = 1` (Active) are returned
- Prizes are sorted by cost (ascending) - cheapest first
- The `content` field contains HTML that can be displayed to users
- Prize IDs are UUIDs, not integers

### Response

When requesting prizes for a specific round (`GET /api/prizes/1`):

```json
{
    "success": true,
    "round_id": 1,
    "data": [
        {
            "id": "a169022b-146e-4117-8fcd-9d860256a00b",
            "name": "Beer Voucher",
            "description": "A voucher for a refreshing beer",
            "amount": 20,
            "cost": 5,
            "content": "<p>Special Round 1 discount! Only 5 points!</p>",
            "image_url": "http://your-domain.com/storage/prizes/beer-voucher.jpg"
        }
    ]
}
```

**Note:**
- `cost` may be different (uses `custom_cost` from `prize_round` if set)
- Only prizes that are linked to the round AND active in that round are returned

### Error Responses

**Round not found (404):**
```json
{
    "success": false,
    "message": "Round not found"
}
```

### Status Codes

| Code | Description |
|------|-------------|
| 200 | Success - Returns list of prizes |
| 404 | Round not found (when round_id parameter is invalid) |
| 500 | Server error |

### Future Enhancements

Planned additions to this endpoint:
- User authentication to show personalized data (user's score balance, can_afford flag)
- Prize images
- Pagination for large prize catalogs
- Additional filtering options
