# Upgrading from 1.x to 2.0

This guide will help you migrate your models from StellarWP Models 1.x to 2.0.

## Table of Contents

* [Breaking Changes](#breaking-changes)
* [New Features](#new-features)

## Breaking Changes

### 1. ModelFactory Removed

**Impact:** The entire `ModelFactory` system has been removed from the library.

**1.x Code:**
```php
use StellarWP\Models\Contracts\ModelHasFactory;

class Product_Model extends Model implements ModelHasFactory {
    public static function factory() {
        return new Product_Factory();
    }
}

// Usage
$product = Product_Model::factory()->create([
    'name' => 'Test Product'
]);
```

**Migration:** Remove factory implementations and use direct model instantiation or create your own factory pattern:

```php
class Product_Model extends Model {
    // No factory method needed
}

// Direct instantiation
$product = new Product_Model([
    'name' => 'Test Product'
]);

// Or implement your own factory if needed
class Product_Factory {
    public function create(array $attributes): Product_Model {
        return new Product_Model($attributes);
    }
}
```

### 2. Properties Must Be Static

**Impact:** The `$properties` array must now be declared as `static`.

**1.x Code:**
```php
class Product_Model extends Model {
    protected $properties = [
        'id' => 'int',
        'name' => 'string',
    ];
}
```

**Migration:**
```php
class Product_Model extends Model {
    protected static $properties = [
        'id' => 'int',
        'name' => 'string',
    ];
}
```

### 3. Relationships Must Be Static

**Impact:** The `$relationships` array must now be declared as `static`.

**1.x Code:**
```php
class Product_Model extends Model {
    protected $relationships = [
        'category' => Relationship::BELONGS_TO,
    ];
}
```

**Migration:**
```php
class Product_Model extends Model {
    protected static $relationships = [
        'category' => Relationship::BELONGS_TO,
    ];
}
```

### 4. Contract Changes

**Impact:** Several contracts have been renamed or removed.

#### ModelCrud → ModelPersistable

**1.x Code:**
```php
use StellarWP\Models\Contracts\ModelCrud;

class Product_Model extends Model implements ModelCrud {
    // ...
}
```

**Migration:**
```php
use StellarWP\Models\Contracts\ModelPersistable;

class Product_Model extends Model implements ModelPersistable {
    // Same methods: find(), create(), save(), delete(), query()
}
```

#### ModelReadOnly Removed

**1.x Code:**
```php
use StellarWP\Models\Contracts\ModelReadOnly;

class Product_Model extends Model implements ModelReadOnly {
    public static function find($id): Model { /* ... */ }
    public static function query(): ModelQueryBuilder { /* ... */ }
    public static function fromQueryBuilderObject($object) { /* ... */ }
}
```

**Migration:** Use `ModelPersistable` and only implement the read methods you need:

```php
use StellarWP\Models\Contracts\ModelPersistable;

class Product_Model extends Model implements ModelPersistable {
    public static function find($id): Model { /* ... */ }
    public static function query(): ModelQueryBuilder { /* ... */ }

    // You still need to implement these, even if they just throw exceptions
    public static function create(array $attributes): Model {
        throw new \RuntimeException('Create not supported');
    }

    public function save(): Model {
        throw new \RuntimeException('Save not supported');
    }

    public function delete(): bool {
        throw new \RuntimeException('Delete not supported');
    }
}
```

#### ModelFromQueryBuilderObject Removed

**Impact:** The `fromQueryBuilderObject()` method is no longer required.

**1.x Code:**
```php
class Product_Model extends Model {
    public static function fromQueryBuilderObject($object) {
        return new static([
            'id' => $object->id,
            'name' => $object->name,
        ]);
    }
}
```

**Migration:** Remove the method. Use the built-in `fromData()` method instead:

```php
class Product_Model extends Model {
    // No fromQueryBuilderObject needed - fromData() is built-in
}

// Usage
$data = $wpdb->get_row("SELECT * FROM products WHERE id = 1");
$product = Product_Model::fromData($data);
```

#### ModelHasFactory Removed

**Impact:** Factory-related contract removed.

**Migration:** Remove the contract implementation and factory method. See [ModelFactory Removed](#1-modelfactory-removed) above.

### 5. Model Method Signatures Changed

Several model methods are now static. If you've overridden these methods, update their signatures:

**Changed Methods:**
```php
// 1.x
public function hasProperty(string $key): bool

// 2.0
public static function hasProperty(string $key): bool
```

```php
// 1.x
public function isPropertyTypeValid(string $key, $value): bool

// 2.0
public static function isPropertyTypeValid(string $key, $value): bool
```

```php
// 1.x (already static, but worth noting)
public static function propertyKeys(): array
```

### 6. Constructor is Now Final

**Impact:** The `Model` constructor is now `final` and cannot be overridden.

**1.x Code:**
```php
class Product_Model extends Model {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        // Custom initialization logic
        if (isset($this->price) && $this->price < 0) {
            $this->price = 0;
        }
    }
}
```

**Migration:** Use the new `afterConstruct()` hook instead:

```php
class Product_Model extends Model {
    protected function afterConstruct() {
        // Custom initialization logic
        if ($this->isSet('price') && $this->price < 0) {
            $this->setAttribute('price', 0);
        }
    }
}
```

### 7. PHP Version Requirement

**Impact:** Minimum PHP version is now 7.4 (up from 7.0).

**Migration:** Ensure your environment runs PHP 7.4 or higher.

## New Features

Take advantage of new 2.0 features:

### 1. Advanced Property Definitions

Define properties with more control using `ModelPropertyDefinition` in the static `properties()` method:

```php
use StellarWP\Models\ModelPropertyDefinition;

class Product_Model extends Model {
    protected static function properties(): array {
        return [
            'id' => (new ModelPropertyDefinition())
                ->type('int')
                ->readonly()
                ->required(),
            'name' => (new ModelPropertyDefinition())
                ->type('string')
                ->default('Untitled')
                ->requiredOnSave(),
            'price' => (new ModelPropertyDefinition())
                ->type('float')
                ->nullable(),
            'created_at' => (new ModelPropertyDefinition())
                ->type('string')
                ->readonly()
                ->default(fn() => current_time('mysql')),
        ];
    }
}
```

Property definition options:
- `type(string ...$types)` - Set property type(s)
- `default($value)` - Set default value (can be a closure)
- `nullable()` - Allow null values
- `required()` - Must be provided during construction
- `requiredOnSave()` - Must be set before saving
- `readonly()` - Can only be set during construction
- `castWith(callable $callback)` - Custom casting function

### 2. Built-in fromData() Method

Convert query results to models easily:

```php
$data = $wpdb->get_row("SELECT * FROM products WHERE id = 1");
$product = Product_Model::fromData($data);

// With build modes
$product = Product_Model::fromData($data, Product_Model::BUILD_MODE_STRICT);
$product = Product_Model::fromData($data, Product_Model::BUILD_MODE_IGNORE_MISSING);
$product = Product_Model::fromData($data, Product_Model::BUILD_MODE_IGNORE_EXTRA);
```

### 3. Enhanced Change Tracking

```php
$product = new Product_Model(['name' => 'Widget']);

$product->setAttribute('name', 'Super Widget');
$product->isDirty('name'); // true
$product->getDirty(); // ['name' => 'Super Widget']

$product->commitChanges(); // Sync as "original"
$product->isDirty('name'); // false

$product->setAttribute('name', 'Mega Widget');
$product->revertChange('name'); // Back to 'Super Widget'
```

### 4. Read-Only Properties

Protect properties from modification after construction:

```php
class Product_Model extends Model {
    protected static function properties(): array {
        return [
            'id' => (new ModelPropertyDefinition())
                ->type('int')
                ->readonly(),
        ];
    }
}

$product = new Product_Model(['id' => 1]);
$product->id = 2; // Throws ReadOnlyPropertyException
```

### 5. Union Type Support

Properties can now accept multiple types:

```php
class Product_Model extends Model {
    protected static function properties(): array {
        return [
            'price' => (new ModelPropertyDefinition())
                ->type('float', 'int'), // Accepts either float or int
            'metadata' => (new ModelPropertyDefinition())
                ->type('array', 'string'), // Can be array or JSON string
        ];
    }
}

$product = new Product_Model(['price' => 10]); // int - valid
$product->setAttribute('price', 10.99); // float - also valid
```

### 6. Required Properties for Saving

Mark properties that must be set before saving:

```php
class Product_Model extends Model {
    protected static function properties(): array {
        return [
            'id' => (new ModelPropertyDefinition())
                ->type('int')
                ->readonly(),
            'name' => (new ModelPropertyDefinition())
                ->type('string')
                ->requiredOnSave(), // Must be set before save()
            'description' => (new ModelPropertyDefinition())
                ->type('string')
                ->nullable(), // Optional
        ];
    }
}

// This will work fine
$product = new Product_Model(['id' => 1]);

// But calling save() without name will fail validation
$product->save(); // Throws exception: "name" is required
```

### 7. Child ModelPropertyDefinitions

Create reusable property definitions by extending `ModelPropertyDefinition`:

```php
use StellarWP\Models\ModelPropertyDefinition;

class CreatedAtProperty extends ModelPropertyDefinition {
    public function __construct() {
        parent::__construct();

        $this->type('string')
            ->readonly()
            ->default(fn() => current_time('mysql'));
    }
}

class Product_Model extends Model {
    protected static function properties(): array {
        return [
            'id' => (new ModelPropertyDefinition())
                ->type('int')
                ->readonly(),
            'name' => (new ModelPropertyDefinition())
                ->type('string'),
            'created_at' => new CreatedAtProperty(),
        ];
    }
}

class Order_Model extends Model {
    protected static function properties(): array {
        return [
            'id' => (new ModelPropertyDefinition())
                ->type('int')
                ->readonly(),
            'total' => (new ModelPropertyDefinition())
                ->type('float'),
            'created_at' => new CreatedAtProperty(), // Reuse the same definition
        ];
    }
}
```

### 8. Improved Generics Support

Better type inference for IDEs and static analysis tools:

```php
class Product_Model extends Model implements ModelPersistable {
    // ...

    /**
     * @return ModelQueryBuilder<Product_Model>
     */
    public static function query(): ModelQueryBuilder {
        return App::get(Product_Repository::class)->prepareQuery();
    }
}

// IDEs now know this returns Product_Model|null
$product = Product_Model::query()->where('id', 1)->get();

// And this returns list<Product_Model>
$products = Product_Model::query()->where('active', 1)->getAll();
```

### 9. Enhanced Relationship System

The relationship system has been completely redesigned to work like the property system with better type safety and flexibility.

#### Relationship Definitions

You can now define relationships using `ModelRelationshipDefinition` for more control:

```php
use StellarWP\Models\ModelRelationshipDefinition;
use StellarWP\Models\ValueObjects\Relationship;

class Product_Model extends Model {
    // Shorthand syntax (still supported)
    protected static $relationships = [
        'category' => Relationship::BELONGS_TO,
        'reviews' => Relationship::HAS_MANY,
    ];

    // Or use the fluent API for more control
    protected static function relationships(): array {
        return [
            'category' => (new ModelRelationshipDefinition('category'))
                ->belongsTo(),
            'reviews' => (new ModelRelationshipDefinition('reviews'))
                ->hasMany()
                ->disableCaching(), // Disable caching for this relationship
            'tags' => (new ModelRelationshipDefinition('tags'))
                ->manyToMany(),
        ];
    }

    // Define relationship loaders
    protected function category() {
        return Category_Model::query()->where('id', $this->category_id);
    }

    protected function reviews() {
        return Review_Model::query()->where('product_id', $this->id);
    }

    protected function tags() {
        return Tag_Model::query()
            ->join('product_tags', 'product_tags.tag_id', 'tags.id')
            ->where('product_tags.product_id', $this->id);
    }
}
```

#### Relationship Type Value Object

The `Relationship` class is now a proper value object with instance caching (flyweight pattern):

```php
use StellarWP\Models\ValueObjects\Relationship;

// Factory methods return cached instances
$hasMany1 = Relationship::HAS_MANY();
$hasMany2 = Relationship::HAS_MANY();
$hasMany1 === $hasMany2; // true - same instance

// Create from string
$relationship = Relationship::from('has-many');

// Type checking methods
$relationship->isHasMany(); // true
$relationship->isSingle(); // false
$relationship->isMultiple(); // true

// Get all relationship types
$all = Relationship::all(); // Returns array of all 5 relationship types
```

Available relationship types:
- `Relationship::HAS_ONE` - Single related model
- `Relationship::HAS_MANY` - Multiple related models
- `Relationship::BELONGS_TO` - Single parent model
- `Relationship::BELONGS_TO_MANY` - Multiple parent models
- `Relationship::MANY_TO_MANY` - Many-to-many relationship

#### Relationship Cache Control

New protected methods for managing relationship caching within your model subclasses:

```php
class Product_Model extends Model {
    protected static $relationships = [
        'category' => Relationship::BELONGS_TO,
        'reviews' => Relationship::HAS_MANY,
    ];

    public function updateCategory(Category_Model $category) {
        // Update the relationship in the database
        // ...

        // Manually update the cached relationship value
        $this->setCachedRelationship('category', $category);
    }

    public function refreshCategory() {
        // Clear the cached relationship so it reloads
        $this->purgeRelationship('category');
        return $this->category; // Reloads from database
    }

    public function refreshAllRelationships() {
        // Clear all cached relationships
        $this->purgeRelationshipCache();
    }

    // Override to customize relationship loading
    protected function fetchRelationship(string $key) {
        if ($key === 'category' && !$this->category_id) {
            return null; // No category to load
        }

        return parent::fetchRelationship($key);
    }
}
```

Available methods:
- `setCachedRelationship(string $key, $value)` - Manually set a cached relationship value
- `purgeRelationship(string $key)` - Clear a specific relationship from cache
- `purgeRelationshipCache()` - Clear all relationship caches
- `fetchRelationship(string $key)` - Override to customize relationship loading logic

## Getting Help

If you encounter issues during migration:

1. Check the [README](README.md) for detailed documentation
2. Review the test files in `tests/wpunit/` for usage examples
3. Open an issue on the GitHub repository
