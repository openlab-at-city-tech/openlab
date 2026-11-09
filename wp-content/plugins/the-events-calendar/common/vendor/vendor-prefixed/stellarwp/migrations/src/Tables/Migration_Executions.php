<?php

/**
 * The Migration Executions table schema.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Tables
 */
namespace TEC\Common\StellarWP\Migrations\Tables;

use TEC\Common\StellarWP\Schema\Columns\Created_At;
use TEC\Common\StellarWP\Shepherd\Abstracts\Table_Abstract;
use TEC\Common\StellarWP\Schema\Collections\Column_Collection;
use TEC\Common\StellarWP\Schema\Columns\Datetime_Column;
use TEC\Common\StellarWP\Schema\Columns\ID;
use TEC\Common\StellarWP\Schema\Columns\Integer_Column;
use TEC\Common\StellarWP\Schema\Columns\String_Column;
use TEC\Common\StellarWP\Schema\Tables\Table_Schema;
use TEC\Common\StellarWP\Migrations\Models\Execution;
use DateTimeInterface;
/**
 * Migration Executions table schema.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tables
 *
 * @method static array<string, Execution> get_all_by( string $column, $value, string $operator = '=', int $limit = 50, string $order_by = '' )
 * @method static ?Execution get_first_by( string $column, $value )
 * @method static ?Execution get_by_id( $id )
 * @method static list<Execution> paginate( array<string, mixed> $args, int $per_page = 20, int $page = 1, list<string> $columns = [ '*' ], string $join_table = '', string $join_condition = '', list<string> $selectable_joined_columns = [], string $output = 'OBJECT' )
 */
class Migration_Executions extends Table_Abstract
{
    /**
     * The schema version.
     *
     * @since 0.0.1
     *
     * @var string
     */
    const SCHEMA_VERSION = '0.0.1';
    /**
     * The base table name, without the table prefix.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $base_table_name = 'stellarwp_%s_migration_executions';
    /**
     * The table group.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $group = 'stellarwp_migrations';
    /**
     * The slug used to identify the custom table.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static $schema_slug = 'stellarwp-migrations-%s-migration-executions';
    /**
     * Gets the schema history for the table.
     *
     * @since 0.0.1
     *
     * @return array<string, callable> The schema history for the table.
     */
    public static function get_schema_history(): array
    {
        return [self::SCHEMA_VERSION => [self::class, 'get_schema_version_0_0_1']];
    }
    /**
     * Gets the schema for version 0.0.1.
     *
     * @since 0.0.1
     *
     * @return Table_Schema The schema for version 0.0.1.
     */
    public static function get_schema_version_0_0_1(): Table_Schema
    {
        $columns = new Column_Collection([
            new ID('id'),
            (new String_Column('migration_id'))->set_length(191)->set_is_index(true),
            // Start date is set when the execution is started.
            (new Datetime_Column('start_date_gmt'))->set_nullable(true),
            // End date is set when the execution is completed (successfully or not).
            (new Datetime_Column('end_date_gmt'))->set_nullable(true),
            (new String_Column('status'))->set_length(191)->set_is_index(true),
            // Total items to process in the migration.
            new Integer_Column('items_total'),
            // Items processed so far.
            new Integer_Column('items_processed'),
            // Set when this execution is an automatic rollback; links to the original (failed) execution id.
            (new Integer_Column('parent_execution_id'))->set_nullable(true)->set_is_index(true),
            // Created at is set when the execution is created (e.g. scheduled). Useful for tracking the execution lifecycle.
            new Created_At('created_at'),
        ]);
        return new Table_Schema(self::table_name(true), $columns);
    }
    /**
     * Transforms a result array into a Migration_Execution array.
     *
     * @since 0.0.1
     *
     * @param array<string, mixed> $result_array The result array.
     *
     * @phpstan-param array{
     *     id: int,
     *     migration_id: string,
     *     start_date_gmt: DateTimeInterface,
     *     end_date_gmt: DateTimeInterface,
     *     status: string,
     *     items_total: int,
     *     items_processed: int,
     *     parent_execution_id: int|null,
     *     created_at: DateTimeInterface
     * } $result_array
     *
     * @return Execution The Execution model.
     */
    public static function transform_from_array(array $result_array): Execution
    {
        return new Execution($result_array);
    }
}