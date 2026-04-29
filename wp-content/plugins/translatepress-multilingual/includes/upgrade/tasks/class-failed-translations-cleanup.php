<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class TRP_Failed_Translations_Cleanup {

    const BATCH_SIZE      = 1000;
    const FAILED_STRING   = '__OPENROUTER_FAILED__';

    /**
     * Build the list of tables that need cleaning.
     *
     * Targets:
     *  - All wp_trp_dictionary_* tables
     *  - All wp_trp_gettext_* tables (excluding *_original_strings and *_original_meta)
     *  - wp_trp_slug_translations
     *
     * @return array
     */
    public function build_todo_list() {
        global $wpdb;

        $todo  = array();
        $prefix = $wpdb->prefix . 'trp_';

        // Dictionary tables.
        $dictionary_tables = $wpdb->get_col(
            $wpdb->prepare(
                "SHOW TABLES LIKE %s",
                $wpdb->esc_like( $prefix . 'dictionary_' ) . '%'
            )
        );

        foreach ( $dictionary_tables as $table ) {
            $todo[] = array(
                'table'  => $table,
                'type'   => 'dictionary',
                'status' => 'not_completed',
                'error'  => null,
            );
        }

        // Gettext tables (exclude original_strings and original_meta).
        $gettext_tables = $wpdb->get_col(
            $wpdb->prepare(
                "SHOW TABLES LIKE %s",
                $wpdb->esc_like( $prefix . 'gettext_' ) . '%'
            )
        );

        foreach ( $gettext_tables as $table ) {
            if ( strpos( $table, '_original_strings' ) !== false || strpos( $table, '_original_meta' ) !== false ) {
                continue;
            }
            $todo[] = array(
                'table'  => $table,
                'type'   => 'gettext',
                'status' => 'not_completed',
                'error'  => null,
            );
        }

        // Slug translations table.
        $slug_table = $prefix . 'slug_translations';
        $table_exists = $wpdb->get_var(
            $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $slug_table ) )
        );

        if ( $table_exists ) {
            $todo[] = array(
                'table'  => $slug_table,
                'type'   => 'slug',
                'status' => 'not_completed',
                'error'  => null,
            );
        }

        return $todo;
    }

    /**
     * Validate that a table name matches expected TRP table patterns.
     *
     * @param string $table Table name to validate.
     * @return bool
     */
    protected function is_valid_table_name( $table ) {
        // Only allow alphanumeric characters and underscores — standard MySQL table name characters.
        if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $table ) ) {
            return false;
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'trp_';

        // Must match one of: {prefix}dictionary_*, {prefix}gettext_*, {prefix}slug_translations
        if ( strpos( $table, $prefix . 'dictionary_' ) === 0 ) {
            return true;
        }
        if ( strpos( $table, $prefix . 'gettext_' ) === 0 ) {
            return true;
        }
        if ( $table === $prefix . 'slug_translations' ) {
            return true;
        }

        return false;
    }

    /**
     * Execute a batch delete on a single todo item.
     *
     * @param array $item Todo item with 'table' and 'type' keys.
     * @return array With 'status' (string) and optionally 'error' (string).
     */
    public function execute( $item ) {
        global $wpdb;

        $table  = $item['table'];
        $result = array(
            'status' => 'not_completed',
            'error'  => '',
        );

        if ( ! $this->is_valid_table_name( $table ) ) {
            $result['error'] = 'Invalid table name: ' . $table;
            return $result;
        }

        if ( $item['type'] === 'slug' ) {
            // Slug translations may have suffixes like __openrouter_failed__-2, __openrouter_failed__-3.
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $query_result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM `{$table}` WHERE `translated` LIKE %s LIMIT %d",
                    '%' . $wpdb->esc_like( strtolower( self::FAILED_STRING ) ) . '%',
                    self::BATCH_SIZE
                )
            );
        } else {
            // Dictionary and gettext tables use 'translated' column with exact match.
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $query_result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM `{$table}` WHERE `translated` LIKE %s LIMIT %d",
                    '%' . $wpdb->esc_like( self::FAILED_STRING ) . '%',
                    self::BATCH_SIZE
                )
            );
        }

        if ( $query_result === false || $wpdb->last_error !== '' ) {
            $result['error'] = $wpdb->last_error !== '' ? $wpdb->last_error : 'Query returned false for table: ' . $table;
        } else {
            $result['affected_rows'] = (int) $wpdb->rows_affected;
            if ( $result['affected_rows'] === 0 ) {
                $result['status'] = 'completed';
            }
        }

        return $result;
    }
}
