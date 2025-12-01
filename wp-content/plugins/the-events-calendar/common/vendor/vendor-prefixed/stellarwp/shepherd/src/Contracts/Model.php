<?php

/**
 * The Shepherd model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Contracts;

use TEC\Common\StellarWP\Schema\Tables\Contracts\Table;
/**
 * The Shepherd model contract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Contracts;
 */
interface Model
{
    /**
     * Gets the model's ID.
     *
     * @since 0.0.1
     *
     * @return int The model's ID.
     */
    public function get_id(): int;
    /**
     * Sets the model's ID.
     *
     * @since 0.0.1
     *
     * @param int $id The model's ID.
     */
    public function set_id(int $id): void;
    /**
     * Saves the model.
     *
     * @since 0.0.1
     *
     * @return int The id of the saved model.
     */
    public function save(): int;
    /**
     * Deletes the model.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function delete(): void;
    /**
     * Gets the table interface for the model.
     *
     * @since 0.0.1
     * @since 0.0.8 Updated to return Table instead.
     *
     * @return Table The table interface.
     */
    public function get_table_interface(): Table;
    /**
     * Converts the model to an array.
     *
     * @since 0.0.1
     *
     * @return array The model as an array.
     */
    public function to_array(): array;
}