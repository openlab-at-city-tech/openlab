# StellarWP Models
A library for a simple model structure.

## Table of Contents

* [Installation](#installation)
* [Notes on examples](#notes-on-examples)
* [Configuration](#configuration)
* [Creating a model](#creating-a-model)
* [Interacting with a model](#interacting-with-a-model)
* [Attribute validation](#attribute-validation)
* [Data transfer objects](#data-transfer-objects)
* [Classes of note](#classes-of-note)
  * [Model](#model)
  * [ModelQueryBuilder](#modelquerybuilder)
  * [DataTransferObject](#data-transfer-objects)
  * [Repositories\Repository](#repositoriesrepository)
* [Contracts of note](#contracts-of-note)
  * [Contracts\ModelPersistable](#contractsmodelpersistable)
  * [Repositories\Contracts\Deletable](#repositoriescontractsdeletable)
  * [Repositories\Contracts\Insertable](#repositoriescontractsinsertable)
  * [Repositories\Contracts\Updatable](#repositoriescontractsupdatable)

## Installation

It's recommended that you install Schema as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/models
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Notes on examples

Since the recommendation is to use Strauss to prefix this library's namespaces, all examples will be using the `Boomshakalaka` namespace prefix.

## Configuration

This library requires some configuration before its classes can be used. The configuration is done via the `Config` class.

```php
use Boomshakalaka\StellarWP\Models\Config;

add_action( 'plugins_loaded', function() {
	Config::setHookPrefix( 'boom-shakalaka' );
} );
```

## Creating a model

Models are classes that hold data and provide some helper methods for interacting with that data.

### A simple model

This is an example of a model that just holds properties. Properties can be defined in one or both of the following ways:

#### Using the shorthand syntax:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Model;

class Breakfast_Model extends Model {
	/**
	 * @inheritDoc
	 */
	protected static $properties = [
		'id'        => 'int',
		'name'      => ['string', 'Default Name'], // With default value
		'price'     => 'float',
		'num_eggs'  => 'int',
		'has_bacon' => 'bool',
	];
}
```

#### Using property definitions for more control:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Model;
use Boomshakalaka\StellarWP\Models\ModelPropertyDefinition;

class Breakfast_Model extends Model {
	/**
	 * @inheritDoc
	 */
	protected static function properties(): array {
		return [
			'id' => ModelPropertyDefinition::create()
				->type('int')
				->required()
			'name' => ModelPropertyDefinition::create()
				->type('string')
				->default('Default Name')
				->nullable(),
			'price' => ModelPropertyDefinition::create()
				->type('float')
				->requiredOnSave(),
		];
	}
}
```

#### Property definition options:

- `type(string ...$types)` - Set one or more types (int, string, bool, float, array, or class names)
- `default($value)` - Set a default value (can be a closure)
- `nullable()` - Allow null values
- `required()` - Property must be provided during construction
- `requiredOnSave()` - Property must be set before saving
- `readonly()` - Property can only be set during construction, cannot be modified afterward
- `castWith(callable $callback)` - Custom casting function for the property value

### A persistable model

This is a model that includes persistence operations (create, find, save, delete). Ideally, the actual persistence operations should be deferred to and handled by
a repository class, but the model should provide a simple interface for interacting with the repository. We get a persistable
model by implementing the `Contracts\ModelPersistable` contract.

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Contracts;
use Boomshakalaka\StellarWP\Models\Model;
use Boomshakalaka\StellarWP\Models\ModelQueryBuilder;

class Breakfast_Model extends Model implements Contracts\ModelPersistable {
	/**
	 * @inheritDoc
	 */
	protected static $properties = [
		'id'        => 'int',
		'name'      => 'string',
		'price'     => 'float',
		'num_eggs'  => 'int',
		'has_bacon' => 'bool',
	];

	/**
	 * @inheritDoc
	 */
	public static function create( array $attributes ) : Model {
		$obj = new static( $attributes );

		return App::get( Repository::class )->insert( $obj );
	}

	/**
	 * @inheritDoc
	 */
	public static function find( $id ) : Model {
		return App::get( Repository::class )->get_by_id( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function save() : Model {
		return App::get( Repository::class )->update( $this );
	}

	/**
	 * @inheritDoc
	 */
	public function delete() : bool {
		return App::get( Repository::class )->delete( $this );
	}

	/**
	 * @inheritDoc
	 */
	public static function query() : ModelQueryBuilder {
		return App::get( Repository::class )->prepareQuery();
	}
}
```

## Interacting with a model

### Change tracking

Models track changes to their properties and provide methods to manage those changes:

```php
$breakfast = new Breakfast_Model([
	'name' => 'Original Name',
	'price' => 5.99,
]);

// Check if a property is dirty (changed)
$breakfast->setAttribute('name', 'New Name');
if ($breakfast->isDirty('name')) {
	echo 'Name has changed!';
}

// Get all dirty values
$dirtyValues = $breakfast->getDirty(); // ['name' => 'New Name']

// Commit changes (makes current values the "original")
$breakfast->commitChanges();
// or use the alias:
$breakfast->syncOriginal();

// Revert a specific property change
$breakfast->setAttribute('price', 7.99);
$breakfast->revertChange('price'); // price is back to 5.99

// Revert all changes
$breakfast->setAttribute('name', 'Another Name');
$breakfast->setAttribute('price', 8.99);
$breakfast->revertChanges(); // All properties back to original

// Get original value
$originalName = $breakfast->getOriginal('name');
$allOriginal = $breakfast->getOriginal(); // Get all original values
```

### Checking if properties are set

The `isSet()` method checks if a property has been set. This is different from PHP's `isset()` because it considers `null` values and default values as "set":

```php
$breakfast = new Breakfast_Model();

// Properties with defaults are considered set
if ($breakfast->isSet('name')) { // true if 'name' has a default value
    echo 'Name is set';
}

// Properties without defaults are not set until assigned
if (!$breakfast->isSet('price')) { // false - no default and not assigned
    echo 'Price is not set';
}

// Setting a property to null still counts as set
$breakfast->setAttribute('price', null);
if ($breakfast->isSet('price')) { // true - explicitly set to null
    echo 'Price is set (even though it\'s null)';
}

// PHP's isset() behaves differently with null
if (!isset($breakfast->price)) { // false - isset() returns false for null
    echo 'PHP isset() returns false for null values';
}
```

**Key differences from PHP's `isset()`:**
- `isSet()` returns `true` for properties with default values
- `isSet()` returns `true` for properties explicitly set to `null`
- `isSet()` returns `false` only for properties that have no default and haven't been assigned

### Creating models from query data

Models can be created from database query results using the `fromData()` method:

```php
// From an object or array
$data = DB::get_row("SELECT * FROM breakfasts WHERE id = 1");
$breakfast = Breakfast_Model::fromData($data);

// With different build modes
$breakfast = Breakfast_Model::fromData($data, Breakfast_Model::BUILD_MODE_STRICT);
$breakfast = Breakfast_Model::fromData($data, Breakfast_Model::BUILD_MODE_IGNORE_MISSING);
$breakfast = Breakfast_Model::fromData($data, Breakfast_Model::BUILD_MODE_IGNORE_EXTRA);
```

Build modes:
- `BUILD_MODE_STRICT`: Throws exceptions for missing or extra properties
- `BUILD_MODE_IGNORE_MISSING`: Ignores properties missing from the data
- `BUILD_MODE_IGNORE_EXTRA`: Ignores extra properties in the data (default)

### Readonly properties

Properties marked as `readonly()` can only be set during construction and cannot be modified afterward:

```php
use Boomshakalaka\StellarWP\Models\Model;
use Boomshakalaka\StellarWP\Models\ModelPropertyDefinition;

class User_Model extends Model {
	protected static function properties(): array {
		return [
			'id' => ModelPropertyDefinition::create()
				->type('int')
				->readonly(), // Can only be set during construction
			'email' => ModelPropertyDefinition::create()
				->type('string'),
		];
	}
}

// Set readonly property during construction
$user = new User_Model(['id' => 1, 'email' => 'user@example.com']);

// This works fine
$user->setAttribute('email', 'newemail@example.com');

// This throws ReadOnlyPropertyException
$user->setAttribute('id', 2); // Error: Cannot modify readonly property "id"

// This also throws ReadOnlyPropertyException
unset($user->id); // Error: Cannot unset readonly property "id"
```

Readonly properties are useful for:
- Primary keys that shouldn't change after creation
- Timestamps that are set once
- Any immutable identifiers or values

### Extending model construction

Models can perform custom initialization after construction by overriding the `afterConstruct()` method:

```php
class Breakfast_Model extends Model {
	protected function afterConstruct() {
		// Perform custom initialization
		if ($this->has_bacon && $this->num_eggs > 2) {
			$this->setAttribute('name', $this->name . ' (Hearty!)');
		}
	}
}
```

## Model Relationships

Models can define relationships to other models, similar to how properties are defined. Relationships support lazy loading and caching.

### Defining Relationships

Relationships can be defined using either shorthand syntax or the fluent `ModelRelationshipDefinition` API:

#### Using shorthand syntax:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Model;
use Boomshakalaka\StellarWP\Models\ValueObjects\Relationship;

class Product_Model extends Model {
	/**
	 * @inheritDoc
	 */
	protected static $relationships = [
		'category' => Relationship::BELONGS_TO,
		'reviews' => Relationship::HAS_MANY,
		'tags' => Relationship::MANY_TO_MANY,
	];

	/**
	 * Define how to load the category relationship.
	 */
	protected function category() {
		return Category_Model::query()->where('id', $this->category_id);
	}

	/**
	 * Define how to load the reviews relationship.
	 */
	protected function reviews() {
		return Review_Model::query()->where('product_id', $this->id);
	}

	/**
	 * Define how to load the tags relationship.
	 */
	protected function tags() {
		return Tag_Model::query()
			->select('tags.*')
			->join('product_tags', 'product_tags.tag_id', 'tags.id')
			->where('product_tags.product_id', $this->id);
	}
}
```

#### Using relationship definitions for more control:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Model;
use Boomshakalaka\StellarWP\Models\ModelRelationshipDefinition;

class Product_Model extends Model {
	/**
	 * @inheritDoc
	 */
	protected static function relationships(): array {
		return [
			'category' => (new ModelRelationshipDefinition('category'))
				->belongsTo(),
			'reviews' => (new ModelRelationshipDefinition('reviews'))
				->hasMany(),
			'tags' => (new ModelRelationshipDefinition('tags'))
				->manyToMany()
				->disableCaching(), // Don't cache this relationship
		];
	}

	// Define relationship loaders as above...
}
```

### Relationship Types

Five relationship types are available:

- `Relationship::HAS_ONE` - Model has one related model
- `Relationship::HAS_MANY` - Model has many related models
- `Relationship::BELONGS_TO` - Model belongs to another model
- `Relationship::BELONGS_TO_MANY` - Model belongs to many related models
- `Relationship::MANY_TO_MANY` - Many-to-many relationship

### Accessing Relationships

Relationships are loaded lazily when accessed as properties:

```php
$product = Product_Model::find(1);

// First access loads from database and caches result
$category = $product->category;

// Subsequent accesses use cached value (if caching enabled)
$category = $product->category; // No additional query

// Access multiple relationship
$reviews = $product->reviews; // Returns array of Review_Model instances
```

### Relationship Caching

By default, relationships are cached after the first load. You can control caching behavior:

```php
class Product_Model extends Model {
	protected static function relationships(): array {
		return [
			// Cached (default)
			'category' => (new ModelRelationshipDefinition('category'))
				->belongsTo(),

			// Not cached - always loads fresh
			'stock' => (new ModelRelationshipDefinition('stock'))
				->hasOne()
				->disableCaching(),
		];
	}
}
```

### Managing Relationship Cache

Models provide methods to manage relationship caching:

```php
$product = Product_Model::find(1);

// Manually set a cached relationship value
$product->setCachedRelationship('category', $newCategory);

// Clear a specific relationship cache
$product->purgeRelationship('category');
$category = $product->category; // Reloads from database

// Clear all relationship caches
$product->purgeRelationshipCache();
```

### Customizing Relationship Loading

Override the `fetchRelationship()` method to customize how relationships are loaded:

```php
class Product_Model extends Model {
	/**
	 * Custom relationship loading logic.
	 */
	protected function fetchRelationship(string $key) {
		// Add custom logic before loading
		if ($key === 'category' && !$this->category_id) {
			return null;
		}

		// Default loading behavior
		return parent::fetchRelationship($key);
	}
}
```

## Attribute validation

Sometimes it would be helpful to validate attributes that are set in the model. To do that, you can create `validate_*()`
methods that will execute any time an attribute is set.

Here's an example:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Model;

class Breakfast_Model extends Model {
	/**
	 * @inheritDoc
	 */
	protected static $properties = [
		'id'        => 'int',
		'name'      => 'string',
		'price'     => 'float',
		'num_eggs'  => 'int',
		'has_bacon' => 'bool',
	];

	/**
	 * Validate the name.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function validate_name( $value ): bool {
		if ( ! preg_match( '/eggs/i', $value ) ) {
			throw new \Exception( 'Breakfasts must have "eggs" in the name!' );
		}

		return true;
	}
}

```

## Data Transfer Objects

Data Transfer Objects (DTOs) are classes that help with the translation of database query results (or other sources of data)
into models. DTOs are not required for using this library, but they are recommended. Using these objects helps you be more
deliberate with your query usage and allows your models and repositories well with the `ModelQueryBuilder`.

Here's an example of a DTO for breakfasts:

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\Whatever\StellarWP\Models\DataTransferObject;
use Boomshakalaka\Whatever\Breakfast_Model;

class Breakfast_DTO extends DataTransferObject {
	/**
	 * Breakfast ID.
	 *
	 * @var int
	 */
	 public int $id;

	/**
	 * Breakfast name.
	 *
	 * @var string
	 */
	 public string $name;

	/**
	 * Breakfast price.
	 *
	 * @var float
	 */
	 public float $price;

	/**
	 * Number of eggs in the breakfast.
	 *
	 * @var int
	 */
	 public int $num_eggs;

	/**
	 * Whether or not the breakfast has bacon.
	 *
	 * @var bool
	 */
	 public bool $has_bacon;

	/**
	 * Builds a new DTO from an object.
	 *
	 * @since TBD
	 *
	 * @param object $object The object to build the DTO from.
	 *
	 * @return Breakfast_DTO The DTO instance.
	 */
	public static function fromObject( $object ): self {
		$self = new self();

		$self->id        = $object->id;
		$self->name      = $object->name;
		$self->price     = $object->price;
		$self->num_eggs  = $object->num_eggs;
		$self->has_bacon = (bool) $object->has_bacon;

		return $self;
	}

	/**
	 * Builds a model instance from the DTO.
	 *
	 * @since TBD
	 *
	 * @return Breakfast_Model The model instance.
	 */
	public function toModel(): Breakfast_Model {
		$attributes = get_object_vars( $this );

		return new Breakfast_Model( $attributes );
	}
}
```

## Repositories

Repositories are classes that fetch from and interact with the database. Ideally, repositories would be used to
query the database in different ways and return corresponding models. With this library, we provide
`Deletable`, `Insertable`, and `Updatable` contracts that can be used to indicate what operations a repository provides.

You may be wondering why there isn't a `Findable` or `Readable` contract (or similar). That's because the fetching needs
of a repository varies with the usecase. However, in the `Repository` abstract class, there is an abstract `prepareQuery()`
method. This method should return a `ModelQueryBuilder` instance that can be used to fetch data from the database.

```php
namespace Boomshakalaka\Whatever;

use Boomshakalaka\StellarWP\Models\Contracts\Model;
use Boomshakalaka\StellarWP\Models\ModelQueryBuilder;
use Boomshakalaka\StellarWP\Repositories\Repository;
use Boomshakalaka\StellarWP\Repositories\Contracts;
use Boomshakalaka\Whatever\Breakfast_Model;
use Boomshakalaka\Whatever\Breakfast as Table;

class Breakfast_Repository extends Repository implements Contracts\Deletable, Contracts\Insertable, Contracts\Updatable {
	/**
	 * {@inheritDoc}
	 */
	public function delete( Model $model ): bool {
		return (bool) DB::delete( Table::table_name(), [ 'id' => $model->id ], [ '%d' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function insert( Model $model ): Breakfast_Model {
		DB::insert( Table::table_name(), [
			'name' => $model->name,
			'price' => $model->price,
			'num_eggs' => $model->num_eggs,
			'has_bacon' => (int) $model->has_bacon,
		], [
			'%s',
			'%s',
			'%d',
			'%d',
		] );

		$model->id = DB::last_insert_id();

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	function prepareQuery(): ModelQueryBuilder {
		$builder = new ModelQueryBuilder( Breakfast_Model::class );

		return $builder->from( Table::table_name( false ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function update( Model $model ): Model {
		DB::update( Table::table_name(), [
			'name' => $model->name,
			'price' => $model->price,
			'num_eggs' => $model->num_eggs,
			'has_bacon' => (int) $model->has_bacon,
		], [ 'id' => $model->id ], [
			'%s',
			'%s',
			'%d',
			'%d',
		], [ '%d' ] );

		return $model;
	}

	/**
	 * Finds a Breakfast by its ID.
	 *
	 * @since TBD
	 *
	 * @param int $id The ID of the Breakfast to find.
	 *
	 * @return Breakfast_Model|null The Breakfast model instance, or null if not found.
	 */
	public function find_by_id( int $id ): ?Breakfast_Model {
		return $this->prepareQuery()->where( 'id', $id )->get();
	}
}
```

### Interacting with the Repository

### Querying

```php
$breakfast = App::get( Breakfast_Repository::class )->find_by_id( 1 );

// Or, we can fetch via the model, which defers to the repository.
$breakfast = Breakfast_Model::find( 1 );
```

### Inserting

```php
$breakfast = new Breakfast_Model( [
	'name'      => 'Bacon and Eggs',
	'price'     => 5.99,
	'num_eggs'  => 2,
	'has_bacon' => true,
] );

$breakfast->save();
```

### Updating

```php
$breakfast = Breakfast_Model::find( 1 );
$breakfast->setAttribute( 'price', 6.99 );
$breakfast->save();
```

### Deleting

```php
$breakfast = Breakfast_Model::find( 1 );
$breakfast->delete();
```

### Unsetting properties

```php
$breakfast = Breakfast_Model::find( 1 );
unset($breakfast->price); // Unsets the price property
```

## Classes of note

### `Model`

This is an abstract class to extend for your models.

### `ModelQueryBuilder`

This class extends the [`stellarwp/db`](https://github.com/stellarwp/db) `QueryBuilder` class so that it returns
model instances rather than arrays or `stdClass` instances.

### `DataTransferObject`

This is an abstract class to extend for your DTOs.

### `Repositories\Repository`

This is an abstract class to extend for your repositories.

## Contracts of note

### `Contracts\ModelPersistable`

Provides definitions of methods for persistence operations in a model (create, find, save, delete, query).

### `Repositories\Contracts\Deletable`

Provides method signatures for delete methods in a repository.

### `Repositories\Contracts\Insertable`

Provides method signatures for insert methods in a repository.

### `Repositories\Contracts\Updatable`

Provides method signatures for update methods in a repository.
