<?php

/**
 * Many to many relationship with posts.
 *
 * @since 0.1.0
 *
 * @package \TEC\Common\StellarWP\SchemaModels\Relationships;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\SchemaModels\Relationships;

use TEC\Common\StellarWP\Models\ModelRelationshipDefinition;
use TEC\Common\StellarWP\Schema\Tables\Contracts\Table as Table_Interface;
use TEC\Common\StellarWP\SchemaModels\Contracts\Relationships\ManyToManyWithPosts as ManyToManyWithPostsContract;
use TEC\Common\StellarWP\DB\DB;
/**
 * Many to many relationship with posts.
 *
 * @since 0.1.0
 *
 * @package \TEC\Common\StellarWP\SchemaModels\Relationships;
 */
class ManyToManyWithPosts extends ModelRelationshipDefinition implements ManyToManyWithPostsContract
{
    /**
     * The table interface.
     *
     * @since 0.1.0
     *
     * @var class-string<Table_Interface>
     */
    private string $tableInterface;
    /**
     * The this entity column.
     *
     * @since 0.1.0
     *
     * @var string
     */
    private string $thisEntityColumn;
    /**
     * The other entity column.
     *
     * @since 0.1.0
     *
     * @var string
     */
    private string $otherEntityColumn;
    /**
     * Constructor.
     *
     * @since 0.1.0
     *
     * @param string $key The relationship key/name.
     */
    public function __construct(string $key)
    {
        parent::__construct($key);
        $this->manyToMany();
    }
    /**
     * Sets the this entity column.
     *
     * @since 0.1.0
     *
     * @param string $thisEntityColumn The this entity column.
     *
     * @return self
     */
    public function setThisEntityColumn(string $thisEntityColumn): self
    {
        $this->thisEntityColumn = $thisEntityColumn;
        return $this;
    }
    /**
     * Sets the other entity column.
     *
     * @since 0.1.0
     *
     * @param string $otherEntityColumn The other entity column.
     *
     * @return self
     */
    public function setOtherEntityColumn(string $otherEntityColumn): self
    {
        $this->otherEntityColumn = $otherEntityColumn;
        return $this;
    }
    /**
     * Sets the table interface.
     *
     * @since 0.1.0
     *
     * // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint
     *
     * @param class-string<Table_Interface> $tableInterface The table interface.
     *
     * @return self
     */
    public function setTableInterface(string $tableInterface): self
    {
        $this->tableInterface = $tableInterface;
        return $this;
    }
    // phpcs:enable Squiz.Commenting.FunctionComment.IncorrectTypeHint
    /**
     * Gets the this entity column.
     *
     * @since 0.1.0
     *
     * @return string
     */
    public function getThisEntityColumn(): string
    {
        return $this->thisEntityColumn;
    }
    /**
     * Gets the other entity column.
     *
     * @since 0.1.0
     *
     * @return string
     */
    public function getOtherEntityColumn(): string
    {
        return $this->otherEntityColumn;
    }
    /**
     * Gets the table interface.
     *
     * @since 0.1.0
     *
     * @return class-string<Table_Interface>
     */
    public function getTableInterface(): string
    {
        return $this->tableInterface;
    }
    /**
     * Deletes the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id The ID of the relationship.
     */
    public function deleteAllRelationshipData($id): void
    {
        $this->getTableInterface()::delete($id, $this->getThisEntityColumn());
    }
    /**
     * Fetches the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id The ID of the relationship.
     *
     * @return array
     */
    public function fetchRelationshipData($id)
    {
        $table = $this->getTableInterface();
        $sanitizer = $this->getValidateSanitizeRelationshipWith();
        return array_map(fn($id) => $sanitizer($id), wp_list_pluck($table::get_all_by($this->getThisEntityColumn(), $id, '=', 1000), $this->getOtherEntityColumn()));
    }
    /**
     * Inserts the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id The ID of the relationship.
     * @param array      $data    The data to insert.
     */
    public function insertRelationshipData($id, array $data = []): void
    {
        if (empty($data)) {
            return;
        }
        $insert_data = [];
        foreach ($data as $insert_id) {
            $insert_data[] = [$this->getThisEntityColumn() => $id, $this->getOtherEntityColumn() => $insert_id];
        }
        // First delete them to avoid duplicates.
        $this->getTableInterface()::delete_many($data, $this->getOtherEntityColumn(), DB::prepare(' AND %i = %d', $this->getThisEntityColumn(), $id));
        $this->getTableInterface()::insert_many($insert_data);
    }
    /**
     * Deletes the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id   The ID of the relationship.
     * @param array      $data The data to delete.
     */
    public function deleteRelationshipData($id, $data): void
    {
        $this->getTableInterface()::delete_many($data, $this->getOtherEntityColumn(), DB::prepare(' AND %i = %d', $this->getThisEntityColumn(), $id));
    }
}