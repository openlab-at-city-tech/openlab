<?php

/**
 * The safe dynamic table prefix utility class.
 *
 * @since 0.0.1
 *
 * @package TEC\Common\StellarWP\Shepherd\Tables\Utility;
 */
namespace TEC\Common\StellarWP\Shepherd\Tables\Utility;

use RuntimeException;
use TEC\Common\StellarWP\Shepherd\Config;
/**
 * Safe dynamic table prefix utility class.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Shepherd\Tables\Utility;
 */
class Safe_Dynamic_Prefix
{
    /**
     * Longest table name.
     *
     * @var string
     */
    private string $longest_table_name = '';
    /**
     * Calculates the longest table name.
     *
     * @since 0.0.1
     *
     * @param array<class-string> $tables The tables to calculate the longest table name for.
     *
     * @return void
     */
    public function calculate_longest_table_name(array $tables): void
    {
        $this->longest_table_name = $this->get_longest_table_name($tables);
    }
    /**
     * Gets the longest table name.
     *
     * @since 0.0.1
     *
     * @param array<class-string> $tables The tables to calculate the longest table name for.
     *
     * @return string The longest table name.
     */
    public function get_longest_table_name(array $tables): string
    {
        $longest_table_name = '';
        foreach ($tables as $table) {
            $raw_base_table_name = $table::raw_base_table_name();
            if (strlen($raw_base_table_name) > strlen($longest_table_name)) {
                $longest_table_name = $raw_base_table_name;
            }
        }
        return $longest_table_name;
    }
    /**
     * Gets the maximum safe hook prefix length.
     *
     * Calculates the maximum length a hook prefix can be while ensuring
     * table names don't exceed MySQL's 64-character limit.
     *
     * @since 0.0.1
     *
     * @param string|null $longest_table_name The longest table name to calculate the maximum length for.
     *
     * @return int The maximum safe hook prefix length.
     */
    public function get_max_length(?string $longest_table_name = null): int
    {
        global $wpdb;
        $wp_prefix_length = strlen($wpdb->prefix);
        $longest_table_name ??= $this->longest_table_name;
        $unprefixed_table_name_length = strlen(sprintf($longest_table_name, ''));
        return Config::get_max_table_name_length() - $unprefixed_table_name_length - $wp_prefix_length;
    }
    /**
     * Gets the safe hook prefix.
     *
     * Returns the hook prefix trimmed to the maximum safe length
     * to ensure table names don't exceed MySQL's limit.
     *
     * @since 0.0.1
     *
     * @param string|null $longest_table_name The longest table name to calculate the maximum length for.
     *
     * @throws RuntimeException If the dynamic table prefix is not set or the max dynamic table prefix length could not be determined.
     *
     * @return string The safe hook prefix.
     */
    public function get(?string $longest_table_name = null): string
    {
        $prefix = Config::get_hook_prefix();
        $max_length = $this->get_max_length($longest_table_name);
        if (!$max_length) {
            throw new RuntimeException('The max dynamic table prefix could not be determined.');
        }
        if (strlen($prefix) > $max_length) {
            return substr($prefix, 0, $max_length);
        }
        return $prefix;
    }
}