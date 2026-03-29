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

## API Response Conventions

### ⚠️ REQUIRED: Always Use API Resources for JSON Responses
**ALL API endpoints MUST use Laravel API Resources for formatting responses.**
Never return raw arrays or manually constructed JSON.

### Required Pattern:
- ✅ **Single Resource**: Use `ResourceName::make($data)->response()`
- ✅ **Collection**: Use `ResourceName::collection($data)`
- ✅ **Wrap in response()->json()**: Only when resource doesn't provide `->response()` method

### Example Resource
```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExampleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
        ];
    }
}
```

### Example Controller
```php
// Single resource
public function show(Example $example)
{
    return ExampleResource::make($example)->response();
}

// Collection
public function index()
{
    $examples = Example::all();
    return response()->json([
        'success' => true,
        'data' => ExampleResource::collection($examples),
    ]);
}
```

### Benefits
- ✅ Consistent response format across all endpoints
- ✅ Reusable transformation logic
- ✅ Easy to test independently
- ✅ Single source of truth for API structure
- ✅ Clean separation of concerns

**This is mandatory for ALL API endpoints - no exceptions.**

## Notes
- Existing tables may still use integer IDs - these are legacy
- New tables created after March 28, 2026 should follow UUID convention
- Prizes and AreaArticles already use UUIDs
- All migrations should be safe to re-run (idempotent)
- All API endpoints created after March 30, 2026 should use API Resources
