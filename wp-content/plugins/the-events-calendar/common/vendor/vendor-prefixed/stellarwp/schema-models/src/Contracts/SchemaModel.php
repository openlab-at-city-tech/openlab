<?php

/**
 * The schema model contract.
 *
 * @since 0.1.0
 *
 * @package \TEC\Common\StellarWP\SchemaModels\Contracts;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\SchemaModels\Contracts;

use TEC\Common\StellarWP\Schema\Tables\Contracts\Table as Table_Interface;
use TEC\Common\StellarWP\Models\Contracts\Model;
use TEC\Common\StellarWP\Models\Contracts\ModelPersistable;
use TEC\Common\StellarWP\Models\ModelRelationshipCollection;
/**
 * The schema model contract.
 *
 * @since 0.1.0
 *
 * @package \TEC\Common\StellarWP\SchemaModels\Contracts;
 */
interface SchemaModel extends Model, ModelPersistable
{
    /**
     * Gets the primary value of the model.
     *
     * @since 0.1.0
     *
     * @return mixed
     */
    public function getPrimaryValue();
    /**
     * Gets the primary column of the model.
     *
     * @since 0.1.0
     *
     * @return string
     */
    public function getPrimaryColumn(): string;
    /**
     * Gets the relationship collection of the model.
     *
     * @since 0.1.0
     *
     * @return ModelRelationshipCollection
     */
    public function getRelationshipCollection(): ModelRelationshipCollection;
    /**
     * Gets the table interface of the model.
     *
     * @since 0.1.0
     *
     * @return Table_Interface
     */
    public static function getTableInterface(): Table_Interface;
    /**
     * Deletes the relationship data for a given key.
     *
     * @since 0.1.0
     *
     * @param string $key The key of the relationship.
     */
    public function deleteRelationshipData(string $key): void;
    /**
     * Adds an ID to a relationship.
     *
     * @since 0.1.0
     *
     * @param string $key The key of the relationship.
     * @param mixed  $id  The ID to add.
     */
    public function addToRelationship(string $key, $id): void;
    /**
     * Removes an ID from a relationship.
     *
     * @since 0.1.0
     *
     * @param string $key The key of the relationship.
     * @param mixed  $id  The ID to remove.
     */
    public function removeFromRelationship(string $key, $id): void;
}