# Prize Images - Implementation Guide

**Date:** March 29, 2026
**Status:** ✅ Implemented

---

## Overview

Prize images are stored in the **filesystem** using Laravel's Storage system, not in the database. This provides better performance, scalability, and CDN integration.

---

## Storage Configuration

### Location

**Development:**
- **Path:** `storage/public/prizes/`
- **Public URL:** `http://your-domain.com/storage/prizes/filename.jpg`
- **Symbolic link:** `public/storage` → `storage/public`

**Production:**
- Can easily switch to S3, DigitalOcean Spaces, or any S3-compatible storage
- Configure in `config/filesystems.php`

### Disk Configuration

```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## Database Structure

### prizes table

```sql
prizes
├── ...
├── image (string, nullable)  -- Stores relative path: "prizes/filename.jpg"
└── ...
```

**Important:** Only the **path** is stored, not the file content.

---

## File Upload (Filament Admin)

### In PrizeResource Form

```php
Forms\Components\FileUpload::make('image')
    ->label('Prize Image')
    ->image()                    // Accept only images
    ->directory('prizes')        // Store in storage/app/public/prizes
    ->disk('public')             // Use public disk
    ->maxSize(2048)              // Max 2MB
    ->nullable()                 // Optional
    ->imageEditor()              // Built-in image editor
    ->columnSpanFull()
    ->helperText('Upload an image for this prize (max 2MB)');
```

### Filament Features

✅ **Drag & drop** upload
✅ **Image preview** before upload
✅ **Built-in image editor** (crop, rotate, resize)
✅ **Automatic validation** (file type, size)
✅ **Auto-deletion** when record deleted (optional)

---

## Displaying Images

### In Filament Table

```php
Tables\Columns\ImageColumn::make('image')
    ->label('Image')
    ->disk('public')
    ->square()
    ->toggleable()  // Users can hide/show this column
    ->defaultImageUrl(asset('images/placeholder-prize.svg'));
```

**Features:**
- Shows prize image if uploaded
- Shows placeholder SVG if no image
- Toggleable column (can be hidden via column selector)
- Square aspect ratio for consistent display

### In API Response

```php
'image_url' => $prize->image
    ? Storage::disk('public')->url($prize->image)
    : null
```

**Returns:**
```json
{
    "image_url": "http://your-domain.com/storage/prizes/beer-voucher-abc123.jpg"
}
```

### In Blade Templates

```blade
@if($prize->image)
    <img src="{{ Storage::disk('public')->url($prize->image) }}"
         alt="{{ $prize->name }}">
@else
    <img src="{{ asset('images/default-prize.png') }}"
         alt="Default prize image">
@endif
```

### In React/Inertia

```jsx
<img
    src={prize.image_url || '/images/default-prize.png'}
    alt={prize.name}
/>
```

---

## Image Management

### Upload via Filament

1. Go to **Admin Panel → Prizes**
2. **Create** or **Edit** a prize
3. Click "**Choose file**" or drag & drop image
4. Use built-in editor to crop/resize if needed
5. **Save**

Image is automatically:
- Stored in `storage/app/public/prizes/`
- Path saved to database
- Accessible via public URL

### Manual Upload (Optional)

```php
use Illuminate\Support\Facades\Storage;

$path = Storage::disk('public')->putFile('prizes', $uploadedFile);
// Returns: "prizes/filename-hash.jpg"

$prize->update(['image' => $path]);
```

### Delete Image

```php
use Illuminate\Support\Facades\Storage;

if ($prize->image) {
    Storage::disk('public')->delete($prize->image);
    $prize->update(['image' => null]);
}
```

Filament handles deletion automatically when you remove image via form.

---

## Migration to Production Storage (S3/Spaces)

### 1. Configure S3 in .env

```env
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_URL=https://your-bucket.s3.amazonaws.com
```

### 2. Change Disk in Code

```php
// Update FileUpload component
->disk('s3')  // Instead of 'public'

// Update API response
Storage::disk('s3')->url($prize->image)
```

### 3. Migrate Existing Files

```bash
php artisan tinker

# Copy all prize images to S3
$prizes = Prize::whereNotNull('image')->get();
foreach ($prizes as $prize) {
    $localPath = Storage::disk('public')->path($prize->image);
    if (file_exists($localPath)) {
        Storage::disk('s3')->put($prize->image, file_get_contents($localPath));
    }
}
```

---

## Image Optimization (Recommended)

### Install Intervention Image

```bash
composer require intervention/image
```

### Optimize on Upload

```php
use Intervention\Image\Facades\Image;

public function store(Request $request)
{
    $path = $request->file('image')->store('prizes', 'public');

    // Optimize
    $imagePath = Storage::disk('public')->path($path);
    Image::make($imagePath)
        ->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })
        ->save($imagePath, 80); // 80% quality

    Prize::create(['image' => $path]);
}
```

---

## Best Practices

### ✅ DO

- Store images in filesystem (not database)
- Use Laravel Storage for abstraction
- Compress images before upload (max 2MB)
- Use descriptive filenames (automatic with Filament)
- Set up CDN for production (CloudFlare, CloudFront)
- Use `.webp` format for better compression (optional)
- Create different sizes (thumbnail, medium, large) if needed

### ❌ DON'T

- Store base64 in database (bloats DB)
- Store full URLs in database (breaks on domain change)
- Allow unlimited file sizes
- Trust user-provided filenames (security risk)
- Forget to delete old images when updating

---

## Troubleshooting

### Images not showing

**Check symbolic link:**
```bash
php artisan storage:link
```

**Verify permissions:**
```bash
chmod -R 775 storage/app/public
```

### Upload fails

**Check max upload size:**
```php
// php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

**Check storage disk:**
```bash
php artisan storage:link
php artisan config:clear
```

### Images not deleted

Enable auto-delete in model:

```php
protected static function booted()
{
    static::deleting(function ($prize) {
        if ($prize->image) {
            Storage::disk('public')->delete($prize->image);
        }
    });
}
```

---

## API Response Example

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
            "content": "<p>Redeem this voucher...</p>",
            "image_url": "http://your-domain.com/storage/prizes/beer-voucher-abc123.jpg"
        },
        {
            "id": "a169022b-9d41-4eb1-acee-57f262910e97",
            "name": "Craft Beer Prize",
            "description": "Local craft beer selection",
            "amount": 20,
            "cost": 15,
            "content": "<p>Winner receives...</p>",
            "image_url": null
        }
    ]
}
```

---

## File Structure

```
project/
├── storage/
│   └── public/              ← Actual storage location
│       └── prizes/
│           ├── beer-voucher-abc123.jpg
│           ├── craft-beer-xyz789.jpg
│           └── ...
├── public/
│   └── storage/             ← Symbolic link to storage/public
│       └── prizes/
└── database/
    └── migrations/
        └── 2026_03_29_215007_add_image_to_prizes_table.php
```

---

## Summary

✅ **Migration created** - Added `image` column to prizes table
✅ **Model updated** - Added `image` to fillable
✅ **Filament form** - FileUpload component with image editor
✅ **Filament table** - ImageColumn for preview
✅ **API endpoint** - Returns `image_url` with full URL
✅ **Storage link** - Created symbolic link
✅ **Documentation** - Complete guide

**Images are stored at:** `storage/public/prizes/`
**Accessible via:** `http://your-domain.com/storage/prizes/filename.jpg`

---

## Next Steps (Optional)

- Add default prize image (create `public/images/default-prize.png`)
- Set up CDN for production
- Implement image optimization on upload
- Create thumbnail variants (small, medium, large)
- Add image validation (dimensions, aspect ratio)
- Set up automatic cleanup for orphaned images
