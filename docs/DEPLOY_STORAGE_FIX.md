# Production Storage Fix Deployment

**Date:** March 28, 2026
**Issue:** Images not accessible at https://janikoke.com/storage/prizes/[filename]

---

## Changes Made

### 1. Dockerfile.prod
- Added symlink creation: `ln -sf /var/www/html/storage/public /var/www/html/public/storage`
- Added storage/public/prizes directory creation
- Set correct permissions for www-data

### 2. docker-compose.prod.yml
- Added volume mount: `storage-data:/var/www/html/storage/public`
- Defined storage-data volume in volumes section

---

## Deployment Steps

### On Production Server

```bash
# 1. Pull latest changes
git pull origin main

# 2. Stop containers
docker compose -f docker-compose.prod.yml down

# 3. Rebuild app container (includes symlink fix)
docker compose -f docker-compose.prod.yml build app

# 4. Start containers with new volume
docker compose -f docker-compose.prod.yml up -d

# 5. Verify symlink exists inside container
docker compose -f docker-compose.prod.yml exec app ls -la /var/www/html/public/storage

# 6. Check storage directory permissions
docker compose -f docker-compose.prod.yml exec app ls -la /var/www/html/storage/public/prizes

# 7. Test image accessibility
curl -I https://janikoke.com/storage/prizes/01KMXXQGV04XWNENVBH2ZQ5446.jpeg
```

---

## What This Fixes

### Before
- ❌ No symlink: `public/storage` → `storage/public`
- ❌ No persistent volume for uploaded images
- ❌ Images lost on container restart
- ❌ Images not web-accessible

### After
- ✅ Symlink created during build
- ✅ Persistent volume for storage/public
- ✅ Images survive container restarts
- ✅ Images accessible via /storage/prizes/[filename]

---

## Expected Result

```bash
curl -I https://janikoke.com/storage/prizes/01KMXXQGV04XWNENVBH2ZQ5446.jpeg
```

**Should return:**
```
HTTP/2 200
content-type: image/jpeg
content-length: [size]
```

---

## Troubleshooting

### Images still not accessible after deployment

1. **Check symlink:**
   ```bash
   docker compose -f docker-compose.prod.yml exec app ls -la /var/www/html/public/storage
   ```
   Should show: `storage -> /var/www/html/storage/public`

2. **Check if images exist:**
   ```bash
   docker compose -f docker-compose.prod.yml exec app ls -la /var/www/html/storage/public/prizes
   ```

3. **Check Nginx config:**
   ```bash
   docker compose -f docker-compose.prod.yml exec app cat /etc/nginx/http.d/default.conf
   ```
   Verify it serves static files from `/var/www/html/public`

4. **Check permissions:**
   ```bash
   docker compose -f docker-compose.prod.yml exec app stat /var/www/html/storage/public/prizes
   ```
   Should be owned by `www-data:www-data`

### Need to re-upload images

If images were uploaded before the volume mount:

```bash
# Upload via Filament admin panel at https://janikoke.com/admin/prizes
# Images will now persist in the storage-data volume
```

---

## Volume Persistence

The `storage-data` volume persists uploaded images across:
- Container restarts
- Container rebuilds
- Docker Compose down/up cycles

**To backup images:**
```bash
docker run --rm \
  -v janikoke_lara_storage-data:/source \
  -v $(pwd)/backups:/backup \
  alpine tar czf /backup/storage-$(date +%Y%m%d).tar.gz -C /source .
```

**To restore images:**
```bash
docker run --rm \
  -v janikoke_lara_storage-data:/target \
  -v $(pwd)/backups:/backup \
  alpine tar xzf /backup/storage-YYYYMMDD.tar.gz -C /target
```
