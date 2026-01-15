<?php

/**
 * The Shepherd model abstract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts;
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Shepherd\Abstracts;

use TEC\Common\StellarWP\Shepherd\Contracts\Model;
use TEC\Common\StellarWP\DB\DB;
use RuntimeException;
/**
 * The Shepherd model abstract.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Abstracts;
 */
abstract class Model_Abstract implements Model
{
    /**
     * The model's ID.
     *
     * @since 0.0.1
     *
     * @var int
     */
    private int $id = 0;
    /**
     * Gets the model's ID.
     *
     * @since 0.0.1
     *
     * @return int The model's ID.
     */
    public function get_id(): int
    {
        return $this->id;
    }
    /**
     * Sets the model's ID.
     *
     * @since 0.0.1
     *
     * @param int $id The model's ID.
     */
    public function set_id(int $id): void
    {
        $this->id = $id;
    }
    /**
     * Saves the model.
     *
     * @since 0.0.1
     *
     * @return int The id of the saved model.
     *
     * @throws RuntimeException If the model fails to save.
     */
    public function save(): int
    {
        $table_interface = $this->get_table_interface();
        $result = $table_interface::upsert($this->to_array());
        if (!$result) {
            throw new RuntimeException('Failed to save the model.');
        }
        $id = $this->get_id();
        if (!$id) {
            $id = DB::last_insert_id();
            $this->set_id($id);
        }
        return $id;
    }
    /**
     * Deletes the model.
     *
     * @since 0.0.1
     *
     * @return void
     *
     * @throws RuntimeException If the model ID required to delete the model is not set.
     * @throws RuntimeException If the model fails to delete.
     */
    public function delete(): void
    {
        $uid = $this->get_id();
        if (!$uid) {
            throw new RuntimeException('Model ID is required to delete the model.');
        }
        $result = $this->get_table_interface()::delete($uid);
        if (!$result) {
            throw new RuntimeException('Failed to delete the model.');
        }
    }
    /**
     * Converts the model to an array.
     *
     * @since 0.0.1
     *
     * @return array The model as an array.
     */
    public function to_array(): array
    {
        $table_interface = $this->get_table_interface();
        $columns = $table_interface::get_columns()->get_names();
        $model = [];
        foreach ($columns as $column) {
            $method = 'get_' . $column;
            $model[$column] = $this->{$method}();
        }
        $uid_column = $table_interface::uid_column();
        if (empty($model[$uid_column])) {
            unset($model[$uid_column]);
        }
        return $model;
    }
}