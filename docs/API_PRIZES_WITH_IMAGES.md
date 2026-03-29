# Prize API with Images - Complete Implementation

**Date:** March 30, 2026
**Status:** ✅ Working

---

## Endpoint

```
GET /api/prizes/{round}
```

Returns all active prizes for a specific round, including image URLs.

---

## Example Request

```bash
curl http://localhost:88/api/prizes/1
```

---

## Example Response

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
      "cost": 10,
      "content": "<p>Redeem this voucher for a cold beer at participating locations...</p>",
      "image_url": "http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg"
    },
    {
      "id": "a169022b-9d41-4eb1-acee-57f262910e97",
      "name": "Craft Beer Prize",
      "description": "Local craft beer selection",
      "amount": 20,
      "cost": 15,
      "content": "<p>Winner receives their choice of craft beer...</p>",
      "image_url": "http://localhost:88/storage/prizes/01KMXVAZF3KX1KX9HC0YB0MKS2.webp"
    },
    {
      "id": "a169022c-2791-411d-94a2-e94213707851",
      "name": "Beer Challenge Reward",
      "description": "Reward for completing a challenge",
      "amount": 20,
      "cost": 20,
      "content": "<p>Congratulations on completing the challenge...</p>",
      "image_url": "http://localhost:88/storage/prizes/01KMXTFQQR0XAGW9JKQMV2TMM3.jpeg"
    },
    {
      "id": "a169022d-47a1-45b4-8038-68492ac34b1a",
      "name": "Beer Tasting Experience",
      "description": "A guided beer tasting experience",
      "amount": 20,
      "cost": 50,
      "content": "<h2>Beer Tasting Experience</h2><p>Join us for a guided tasting...</p>",
      "image_url": "http://localhost:88/storage/prizes/01KMXTRGBZ2NSS2TVE4E8V10GT.webp"
    }
  ]
}
```

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Request success status |
| `round_id` | integer | The round ID requested |
| `data` | array | Array of prize objects |
| `data[].id` | string (UUID) | Prize unique identifier |
| `data[].name` | string | Prize name |
| `data[].description` | string | Short description |
| `data[].amount` | integer | Prize quantity/value |
| `data[].cost` | integer | Score points needed to redeem |
| `data[].content` | string | HTML content with full details |
| `data[].image_url` | string\|null | **Full URL to prize image** |

---

## Image URL Behavior

### When Prize Has Image
```json
{
  "image_url": "http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg"
}
```

The URL is **ready to use** in:
- `<img>` tags
- React/Vue components
- Mobile apps
- Any HTTP client

### When Prize Has No Image
```json
{
  "image_url": null
}
```

Frontend should display a placeholder image.

---

## Frontend Integration Examples

### React/JavaScript

```jsx
const PrizesList = ({ roundId }) => {
  const [prizes, setPrizes] = useState([]);

  useEffect(() => {
    fetch(`/api/prizes/${roundId}`)
      .then(res => res.json())
      .then(data => setPrizes(data.data));
  }, [roundId]);

  return (
    <div className="prizes-grid">
      {prizes.map(prize => (
        <div key={prize.id} className="prize-card">
          <img
            src={prize.image_url || '/images/placeholder-prize.svg'}
            alt={prize.name}
            className="prize-image"
          />
          <h3>{prize.name}</h3>
          <p>{prize.description}</p>
          <div className="prize-cost">{prize.cost} points</div>
        </div>
      ))}
    </div>
  );
};
```

### Vue.js

```vue
<template>
  <div class="prizes-list">
    <div v-for="prize in prizes" :key="prize.id" class="prize-card">
      <img
        :src="prize.image_url || '/images/placeholder-prize.svg'"
        :alt="prize.name"
      />
      <h3>{{ prize.name }}</h3>
      <p>{{ prize.description }}</p>
      <span class="cost">{{ prize.cost }} points</span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      prizes: []
    };
  },
  async mounted() {
    const response = await fetch(`/api/prizes/${this.roundId}`);
    const data = await response.json();
    this.prizes = data.data;
  }
};
</script>
```

### Mobile (Swift/iOS)

```swift
struct Prize: Codable {
    let id: String
    let name: String
    let description: String
    let cost: Int
    let imageUrl: String?

    enum CodingKeys: String, CodingKey {
        case id, name, description, cost
        case imageUrl = "image_url"
    }
}

// Load image
if let imageUrl = prize.imageUrl,
   let url = URL(string: imageUrl) {
    // Use URLSession or SDWebImage to load
    URLSession.shared.dataTask(with: url) { data, _, _ in
        if let data = data {
            DispatchQueue.main.async {
                self.imageView.image = UIImage(data: data)
            }
        }
    }.resume()
}
```

### Mobile (Kotlin/Android)

```kotlin
data class Prize(
    val id: String,
    val name: String,
    val description: String,
    val cost: Int,
    @SerializedName("image_url")
    val imageUrl: String?
)

// Load image with Glide
Glide.with(context)
    .load(prize.imageUrl ?: R.drawable.placeholder_prize)
    .into(imageView)
```

---

## Testing

### Test with cURL

```bash
# Get prizes for Round 1
curl http://localhost:88/api/prizes/1 | jq

# Get just image URLs
curl -s http://localhost:88/api/prizes/1 | jq '.data[].image_url'

# Count prizes
curl -s http://localhost:88/api/prizes/1 | jq '.data | length'
```

### Test Image Access

```bash
# Test if image is accessible
curl -I http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg

# Should return: HTTP/1.1 200 OK
```

### Verify in Browser

Open these URLs in your browser:

1. **API Response:**
   ```
   http://localhost:88/api/prizes/1
   ```

2. **Individual Image:**
   ```
   http://localhost:88/storage/prizes/01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg
   ```

---

## Filtering & Sorting

Prizes are returned:
- ✅ **Filtered** by round (only prizes in `prize_round` table)
- ✅ **Filtered** by status (only active prizes, `status = 1`)
- ✅ **Filtered** by round activation (`prize_round.is_active = true`)
- ✅ **Sorted** by cost (cheapest first)

---

## Error Responses

### Round Not Found (404)

```bash
curl http://localhost:88/api/prizes/999
```

```json
{
  "success": false,
  "message": "Round not found"
}
```

### No Prizes Available (200)

```bash
curl http://localhost:88/api/prizes/2
```

```json
{
  "success": true,
  "round_id": 2,
  "data": []
}
```

---

## Performance Considerations

### Image Loading

- Images are served as **static files** by web server
- No PHP processing for images
- Browser caching works automatically
- Consider adding CDN for production

### API Optimization

```json
// Current response size with 4 prizes: ~2KB
// Image URLs are lightweight (just strings)
// No base64 encoding (which would be 33% larger)
```

---

## Storage Structure

```
storage/
└── public/
    └── prizes/
        ├── 01KMXSZE8F1Y0GGWYHNBQ7SEPS.jpeg
        ├── 01KMXVAZF3KX1KX9HC0YB0MKS2.webp
        ├── 01KMXTFQQR0XAGW9JKQMV2TMM3.jpeg
        └── 01KMXTRGBZ2NSS2TVE4E8V10GT.webp

public/
└── storage/ → ../storage/public (symlink)
    └── prizes/
```

---

## Production Checklist

- [ ] Configure CDN (CloudFlare, CloudFront)
- [ ] Enable browser caching headers
- [ ] Optimize images (compress, convert to WebP)
- [ ] Consider S3/Spaces for storage
- [ ] Add image size variants (thumbnail, full)
- [ ] Implement lazy loading on frontend
- [ ] Add image alt text from prize name

---

## Related Documentation

- `docs/API_PRIZES.md` - Full API documentation
- `docs/PRIZE_IMAGES.md` - Image storage implementation
- `docs/PRIZE_ROUND_SYSTEM.md` - Prize-round relationships

---

**Status:** ✅ Fully Implemented and Tested
**Endpoint:** `GET /api/prizes/{round}`
**Returns:** Prize data with working image URLs
