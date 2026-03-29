# Project Guidelines for Claude Code

## Migration Rules (CRITICAL)

### ⚠️ REQUIRED: All Migrations Must Be Idempotent
**EVERY migration MUST check if tables/columns exist before creating/modifying them.**
This prevents errors when migrations are run multiple times or out of order.

### Required Checks:
- ✅ **CREATE TABLE**: Always wrap in `if (!Schema::hasTable('table_name'))`
- ✅ **ADD COLUMN**: Always check with `if (Schema::hasTable('table') && !Schema::hasColumn('table', 'column'))`
- ✅ **DROP COLUMN**: Always check with `if (Schema::hasColumn('table', 'column'))`
- ✅ **ALTER COLUMN**: Check current type with `\DB::getSchemaBuilder()->getColumnType('table', 'column')`
- ✅ **DROP FOREIGN KEY**: Wrap in try-catch as constraint names may vary

**This is mandatory for ALL migrations - no exceptions.**

## Database Conventions

### Primary Keys
- **All new tables MUST use UUID as primary key, NOT integer**
- Use `$table->uuid('id')->primary()` in migrations
- Add `use HasUuids;` trait to models

### Foreign Keys
- **DO NOT create foreign key constraints**
- Use UUID columns for relationships without constraints
- Example: `$table->uuid('area_id')` instead of `$table->foreignId('area_id')->constrained()`

### Example Migration
```php
Schema::create('example_table', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('area_id'); // No foreign key constraint
    $table->string('name');
    $table->timestamps();
});
```

### Example Model
```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    use HasFactory, HasUuids;

    // Define relationships as normal
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
```

## Migration Best Practices

### Always Check Before Modifying
- **CREATE TABLE**: Check if table exists with `Schema::hasTable()`
- **ADD COLUMN**: Check if column exists with `Schema::hasColumn()`
- **ALTER COLUMN**: Check column type with `\DB::getSchemaBuilder()->getColumnType()`
- **DROP COLUMN**: Check if column exists before dropping

### Examples
```php
// Creating a table
if (!Schema::hasTable('example_table')) {
    Schema::create('example_table', function (Blueprint $table) {
        // ...
    });
}

// Adding a column
if (Schema::hasTable('example_table') && !Schema::hasColumn('example_table', 'new_column')) {
    Schema::table('example_table', function (Blueprint $table) {
        $table->string('new_column')->nullable();
    });
}

// Changing column type (e.g., to UUID)
if (Schema::hasTable('example_table')) {
    $columnType = \DB::getSchemaBuilder()->getColumnType('example_table', 'id');
    if ($columnType !== 'uuid') {
        // Perform conversion
    }
}
```

## Notes
- Existing tables may still use integer IDs - these are legacy
- New tables created after March 28, 2026 should follow UUID convention
- Prizes and AreaArticles already use UUIDs
- All migrations should be safe to re-run (idempotent)
