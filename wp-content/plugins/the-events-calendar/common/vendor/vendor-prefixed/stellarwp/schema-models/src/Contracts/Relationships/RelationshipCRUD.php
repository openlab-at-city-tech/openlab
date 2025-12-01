<?php

/**
 * The relationship CRUD contract.
 *
 * @since 0.1.0
 *
 * @package \TEC\Common\StellarWP\SchemaModels\Contracts\Relationships;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\SchemaModels\Contracts\Relationships;

use TEC\Common\StellarWP\Models\Model as ModelContract;
interface RelationshipCRUD
{
    /**
     * Deletes the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id The ID of the relationship.
     */
    public function deleteAllRelationshipData($id): void;
    /**
     * Fetches the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id The ID of the relationship.
     *
     * @return ModelContract|ModelContract[]|null
     */
    public function fetchRelationshipData($id);
    /**
     * Inserts the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id   The ID of the relationship.
     * @param array      $data The data to insert.
     */
    public function insertRelationshipData($id, array $data): void;
    /**
     * Deletes the relationship data.
     *
     * @since 0.1.0
     *
     * @param string|int $id   The ID of the relationship.
     * @param array      $data The data to delete.
     */
    public function deleteRelationshipData($id, $data): void;
}