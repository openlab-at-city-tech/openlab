<?php

/**
 * The Migration Logs table schema.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Migrations\Tables
 */
namespace TEC\Common\StellarWP\Migrations\Tables;

use TEC\Common\StellarWP\Schema\Columns\Referenced_ID;
use TEC\Common\StellarWP\Shepherd\Abstracts\Table_Abstract;
use TEC\Common\StellarWP\Schema\Collections\Column_Collection;
use TEC\Common\StellarWP\Schema\Columns\ID;
use TEC\Common\StellarWP\Schema\Columns\Created_At;
use TEC\Common\StellarWP\Schema\Columns\String_Column;
use TEC\Common\StellarWP\Schema\Columns\Text_Column;
use TEC\Common\StellarWP\Schema\Tables\Table_Schema;
use TEC\Common\StellarWP\Schema\Columns\PHP_Types;
use TEC\Common\StellarWP\Migrations\Enums\Log_Type;
use DateTimeInterface;
/**
 * Migration Logs table schema.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Tables
 *
 * @method static array{ id: int, migration_execution_id: int, type: Log_Type, message: string, data: array<string, mixed>, created_at: DateTimeInterface }[] get_all_by( string $column, $value, string $operator = '=', int $limit = 50, string $order_by = '' )
 * @method static ?array{ id: int, migration_execution_id: int, type: Log_Type, message: string, data: array<string, mixed>, created_at: DateTimeInterface } get_first_by( string $column, $value )
 * @method static ?array{ id: int, migration_execution_id: int, type: Log_Type, message: string, data: array<string, mixed>, created_at: DateTimeInterface } get_by_id( $id )
 */
class Migration_Logs extends Table_Abstract
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
    protected static $base_table_name = 'stellarwp_%s_migration_logs';
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
    protected static $schema_slug = 'stellarwp-migrations-%s-migration-logs';
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
        $columns = new Column_Collection([new ID('id'), new Referenced_ID('migration_execution_id'), (new String_Column('type'))->set_length(191)->set_is_index(true), new Text_Column('message'), (new Text_Column('data'))->set_nullable(true)->set_php_type(PHP_Types::JSON), new Created_At('created_at')]);
        return new Table_Schema(self::table_name(true), $columns);
    }
    /**
     * Transforms a result array into a Migration_Log array.
     *
     * @since 0.0.1
     *
     * @param array<string, mixed> $result_array The result array.
     *
     * @phpstan-param array{
     *     id: int,
     *     migration_execution_id: int,
     *     type: string,
     *     message: string,
     *     data: array<string, mixed>,
     *     created_at: DateTimeInterface
     * } $result_array
     *
     * @return array<string, mixed> The Migration_Log array.
     *
     * @phpstan-return array{
     *     id: int,
     *     migration_execution_id: int,
     *     type: Log_Type,
     *     message: string,
     *     data: array<string, mixed>,
     *     created_at: DateTimeInterface
     * }
     */
    public static function transform_from_array(array $result_array)
    {
        return ['id' => $result_array['id'], 'migration_execution_id' => $result_array['migration_execution_id'], 'type' => Log_Type::from($result_array['type']), 'message' => $result_array['message'], 'data' => $result_array['data'], 'created_at' => $result_array['created_at']];
    }
}