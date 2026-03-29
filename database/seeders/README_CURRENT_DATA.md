# Current Database Seeders

This directory contains seeders with the **current production data** exported from your database.

## Created Seeders

### 1. CurrentAreasSeeder.php
**Contains:** 10 areas from your production database

Areas included:
- Kotorska Sever
- Pod mostom
- Kerestur ulice
- Grbavica iza autoputa
- Liman parkiralista
- Telep parkiralista
- Liman 2 Parking
- Limanski Park Novo
- Bulevarski park
- Novi Sad Centar

Each area includes:
- Name and description
- Point coordinates (center point)
- Polygon coordinates (area boundaries)
- Active status
- Type (if any)

### 2. CurrentChallengesSeeder.php
**Contains:** 5 challenges from your production database

Challenges included:
- Liman 3 (ten_each type)
- Limanski park (ten_each type)
- Becarusa (ten_each type)
- Ruski Kerestur (zigzag type)
- Srem (zigzag type)

Each challenge includes:
- Round ID (all linked to round 1)
- Name and description
- Challenge type
- Active status
- Center point coordinates

## How to Use

### Seed Individual Classes

```bash
# Seed only areas
php artisan db:seed --class=CurrentAreasSeeder

# Seed only challenges
php artisan db:seed --class=CurrentChallengesSeeder
```

### Seed All Current Data

Edit `database/seeders/DatabaseSeeder.php` and uncomment:
```php
$this->call([
    CurrentAreasSeeder::class,
    CurrentChallengesSeeder::class,
]);
```

Then run:
```bash
php artisan db:seed
```

### Fresh Database with Current Data

```bash
# Drop all tables, run migrations, then seed
php artisan migrate:fresh --seed

# Or manually:
php artisan migrate:fresh
php artisan db:seed --class=CurrentAreasSeeder
php artisan db:seed --class=CurrentChallengesSeeder
```

## Features

✅ **Duplicate Prevention**: Seeders check if data already exists before inserting
✅ **Spatial Data**: Properly handles Point and Polygon geometries
✅ **Informative Output**: Shows which records were created vs skipped
✅ **Safe to Re-run**: Won't create duplicates

## Note

The original `AreaSeeder.php` and `ChallengeSeeder.php` files contain different test/sample data. The `Current*Seeder.php` files contain your actual production data as of March 28, 2026.

## Generated On
March 28, 2026

## AreaArticleSeeder

**Purpose:** Creates 3 dummy articles for each area in the database.

### What It Creates

For each area, 3 articles are generated with:

**Article 1: "Discover the Beauty of {AreaName}"**
- Status: Active ✅
- Published: Yes (random date within last 30 days)
- Content: Welcome guide with local tips

**Article 2: "Top Things to Do in {AreaName}"**
- Status: Active ✅
- Published: Yes (random date within last 30 days)
- Content: Activities and seasonal highlights

**Article 3: "History and Culture of {AreaName}"**
- Status: Inactive ❌
- Published: No (Draft)
- Content: Historical context and community story

### Features

✅ **Rich HTML Content**: Each article includes formatted HTML with headings, paragraphs, lists, and blockquotes
✅ **Duplicate Prevention**: Won't create duplicate articles if run multiple times
✅ **Realistic Data**: Generated excerpts and full content for each article
✅ **Varied Status**: Mix of active/published and draft articles
✅ **Safe to Re-run**: Skips existing articles

### Usage

```bash
# Seed articles for all areas
php artisan db:seed --class=AreaArticleSeeder

# Or as part of full database seed
php artisan db:seed
```

### Statistics

- **3 articles** per area
- **2 active, 1 draft** per area
- **~200+ words** per article
- **38 areas** × 3 = **114 total articles**
- **76 active articles**, **38 draft articles**
