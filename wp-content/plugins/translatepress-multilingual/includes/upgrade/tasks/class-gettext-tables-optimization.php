<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class TRP_Gettext_Tables_Optimization {

    const ORIGINAL_HASH_BATCH_SIZE = 500;
    const LOCALE_GROUP_BATCH_SIZE  = 250;
    const LOCALE_ROW_BATCH_SIZE    = 500;
    const RUNTIME_BATCH_SIZE       = 500;

    protected $db;
    protected $settings;
    protected $trp_query;
    protected $upgrade;
    protected $text_column_definitions = array();

    public function __construct() {
        global $wpdb;

        $this->db = $wpdb;

        $trp            = TRP_Translate_Press::get_trp_instance();
        $settings       = $trp->get_component( 'settings' );
        $this->settings = $settings->get_settings();
        $this->trp_query = $trp->get_component( 'query' );
        $this->upgrade   = $trp->get_component( 'upgrade' );
    }

    /**
     * Validate unresolved locale values before the background runner is started.
     *
     * @return string Empty when the task can start, otherwise an actionable error.
     */
    public function validate_before_start() {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        if ( ! $this->trp_query->table_exists( $originals_table ) ) {
            return '';
        }

        $target_columns = $this->get_text_column_definitions( $originals_table );
        if ( empty( $target_columns['original'] ) || empty( $target_columns['domain'] ) ) {
            return sprintf( 'Could not determine gettext originals column capacity and character set for %s.', $originals_table );
        }

        foreach ( $this->trp_query->get_all_gettext_table_names() as $table_name ) {
            $table_name = sanitize_text_field( $table_name );

            if ( ! $this->is_valid_gettext_locale_table( $table_name ) || ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ) {
                continue;
            }

            $source_columns = $this->get_text_column_definitions( $table_name );
            if ( empty( $source_columns['original'] ) || empty( $source_columns['domain'] ) ) {
                return sprintf( 'Could not determine gettext locale column capacity and character set for %s.', $table_name );
            }

            $incompatibility = $this->find_incompatible_locale_original( $table_name, $originals_table, $source_columns, $target_columns );
            if ( $this->db->last_error !== '' ) {
                return sprintf( 'Could not verify gettext originals character-set compatibility for %1$s: %2$s', $table_name, $this->db->last_error );
            }

            if ( ! empty( $incompatibility ) ) {
                return $this->format_incompatible_locale_original_error( $incompatibility, $table_name, $originals_table );
            }
        }

        return '';
    }

    /**
     * Build the single resumable gettext optimization item.
     *
     * @return array
     */
    public function build_todo_list() {
        return array(
            array(
                'status'                 => 'not_completed',
                'phase'                  => 'originals_hash',
                'originals_extra_params' => array(),
                'locale_table_index'     => 0,
                'locale_phase'           => 'prepare',
                'locale_last_original_id'=> 0,
                'locale_last_plural_form'=> -1,
                'runtime_language_index' => 0,
                'runtime_batch'          => 0,
                'id_schema_validated'    => false,
                'stats'                  => array(
                    'duplicate_originals' => 0,
                    'duplicate_gettext'   => 0,
                ),
                'error'                  => null,
            ),
        );
    }

    /**
     * Execute one batch step.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    public function execute( $item ) {
        $item = $this->normalize_item( $item );

        if ( ! $item['id_schema_validated'] ) {
            $schema_error = $this->validate_gettext_id_schema();

            if ( $schema_error !== '' ) {
                return $this->error_result( $item, $schema_error );
            }

            $item['id_schema_validated'] = true;
        }

        if ( $item['phase'] === 'originals_hash' ) {
            return $this->execute_originals_hash_phase( $item );
        }

        if ( $item['phase'] === 'locale_tables' ) {
            return $this->execute_locale_tables_phase( $item );
        }

        if ( $item['phase'] === 'originals_lookup_index' ) {
            return $this->execute_originals_lookup_index_phase( $item );
        }

        if ( $item['phase'] === 'runtime_status' ) {
            return $this->execute_runtime_status_phase( $item );
        }

        if ( $item['phase'] === 'complete' ) {
            update_option( 'trp_updated_database_gettext_tables_optimization', 'yes' );
            return array(
                'status'        => 'completed',
                'affected_rows' => $this->get_total_affected_rows( $item ),
                'item'          => $item,
                'progress'      => array(
                    'message' => __( 'Gettext database optimization completed.', 'translatepress-multilingual' ),
                ),
            );
        }

        return $this->error_result( $item, 'Unknown gettext optimization phase: ' . $item['phase'] );
    }

    /**
     * Run the existing originals hash migration callback in background batches.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    protected function execute_originals_hash_phase( $item ) {
        try {
            $result = $this->upgrade->trp_updated_database_gettext_original_lookup_hash( '', 0, self::ORIGINAL_HASH_BATCH_SIZE, $item['originals_extra_params'] );
        } catch ( Exception $e ) {
            return $this->error_result( $item, $e->getMessage() );
        }

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, 'Originals hash migration failed: ' . $this->db->last_error );
        }

        $removed = is_array( $result ) && isset( $result['deleted_originals'] ) ? (int) $result['deleted_originals'] : 0;
        $item['stats']['duplicate_originals'] += $removed;

        if ( $result === true ) {
            $item['phase']              = 'locale_tables';
            $item['locale_phase']       = 'prepare';
            $item['locale_table_index'] = 0;
            update_option( 'trp_updated_database_gettext_original_lookup_hash', 'yes' );

            return $this->continue_result( $item, __( 'Deduplicating gettext translation tables...', 'translatepress-multilingual' ), $removed );
        }

        $item['originals_extra_params'] = isset( $result['extra_params'] ) ? $result['extra_params'] : array();

        return $this->continue_result( $item, __( 'Migrating gettext original lookup hashes...', 'translatepress-multilingual' ), $removed );
    }

    /**
     * Deduplicate and index each gettext locale table.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    protected function execute_locale_tables_phase( $item ) {
        $tables = $this->get_gettext_locale_tables();

        if ( empty( $tables ) || ! isset( $tables[ $item['locale_table_index'] ] ) ) {
            $this->drop_locale_map_table();
            $item['phase'] = 'originals_lookup_index';
            return $this->continue_result( $item, __( 'Preparing the gettext originals lookup index...', 'translatepress-multilingual' ) );
        }

        $table_name = $tables[ $item['locale_table_index'] ];

        if ( ! $this->is_valid_gettext_locale_table( $table_name ) ) {
            return $this->error_result( $item, 'Invalid gettext table name: ' . $table_name );
        }

        if ( $item['locale_phase'] === 'prepare' ) {
            return $this->prepare_locale_table( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'normalize_plural' ) {
            return $this->normalize_locale_plural_form( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'backfill_original_ids' ) {
            return $this->backfill_locale_original_ids( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'build_map' ) {
            return $this->build_locale_dedup_map( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'process_map' ) {
            return $this->process_locale_dedup_map( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'verify' ) {
            return $this->verify_locale_table( $item, $table_name );
        }

        if ( $item['locale_phase'] === 'unique_index' ) {
            return $this->add_locale_unique_index( $item, $table_name );
        }

        return $this->error_result( $item, 'Unknown gettext locale optimization phase: ' . $item['locale_phase'] );
    }

    /**
     * Add the originals lookup index from the resumable background task.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    protected function execute_originals_lookup_index_phase( $item ) {
        $gettext_normalization = $this->trp_query->get_query_component( 'gettext_normalization' );
        $gettext_normalization->check_for_gettext_original_lookup_index();

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, 'Could not add the gettext originals lookup index: ' . $this->db->last_error );
        }

        $item['phase'] = 'runtime_status';

        return $this->continue_result( $item, __( 'Updating gettext runtime statuses...', 'translatepress-multilingual' ) );
    }

    /**
     * Run the runtime status migration for every discovered gettext locale table.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    protected function execute_runtime_status_phase( $item ) {
        $tables = $this->get_gettext_locale_tables();

        if ( empty( $tables ) || ! isset( $tables[ $item['runtime_language_index'] ] ) ) {
            update_option( 'trp_updated_database_gettext_runtime_status_update', 'yes' );
            $item['phase'] = 'complete';
            return $this->continue_result( $item, __( 'Finishing gettext database optimization...', 'translatepress-multilingual' ) );
        }

        $table_name     = $tables[ $item['runtime_language_index'] ];
        $language_code  = $this->get_language_code_from_gettext_table( $table_name );
        $inferior_limit = self::RUNTIME_BATCH_SIZE * (int) $item['runtime_batch'];
        $complete       = $this->upgrade->trp_updated_database_gettext_runtime_status_update( $language_code, $inferior_limit, self::RUNTIME_BATCH_SIZE, $table_name );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Runtime status migration failed for %1$s: %2$s', $language_code, $this->db->last_error ) );
        }

        if ( $complete ) {
            $item['runtime_language_index']++;
            $item['runtime_batch'] = 0;
        } else {
            $item['runtime_batch']++;
        }

        return $this->continue_result( $item, sprintf( __( 'Updating gettext runtime statuses for language %s...', 'translatepress-multilingual' ), $language_code ) );
    }

    /**
     * Prepare a locale table for normalized dedupe.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function prepare_locale_table( $item, $table_name ) {
        if (
            ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ||
            ! $this->trp_query->table_column_exists( $table_name, 'plural_form' )
        ) {
            $language_code        = $this->get_language_code_from_gettext_table( $table_name );
            $gettext_normalization = $this->trp_query->get_query_component( 'gettext_normalization' );
            $gettext_normalization->check_for_gettext_original_id_column( $language_code );

            if ( $this->db->last_error !== '' ) {
                return $this->error_result( $item, sprintf( 'Could not add gettext normalization columns to %1$s: %2$s', $table_name, $this->db->last_error ) );
            }

            if (
                ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ||
                ! $this->trp_query->table_column_exists( $table_name, 'plural_form' )
            ) {
                return $this->error_result( $item, sprintf( 'Could not add gettext normalization columns to %s.', $table_name ) );
            }
        }

        if ( ! $this->gettext_table_has_original_id_lookup_index( $table_name ) ) {
            $index_name = $this->get_available_original_id_index_name( $table_name );
            $this->db->query( "CREATE INDEX `$index_name` ON `$table_name` (original_id)" );
            if ( $this->db->last_error !== '' ) {
                return $this->error_result( $item, sprintf( 'Could not add original_id index to %1$s: %2$s', $table_name, $this->db->last_error ) );
            }
        }

        $unresolved = $this->count_unresolved_locale_original_ids( $table_name );
        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not verify original_id values in %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( $unresolved > 0 ) {
            if ( $this->trp_query->table_index_exists( $table_name, 'gettext_original_plural_unique' ) ) {
                $this->db->query( "ALTER TABLE `$table_name` DROP INDEX gettext_original_plural_unique" );

                if ( $this->db->last_error !== '' ) {
                    return $this->error_result( $item, sprintf( 'Could not drop gettext unique index before repairing %1$s: %2$s', $table_name, $this->db->last_error ) );
                }
            }

            $item['locale_phase'] = 'backfill_original_ids';
            return $this->continue_result( $item, sprintf( __( 'Resolving missing gettext original ids in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $this->ensure_locale_map_table();

        $item['locale_phase'] = 'normalize_plural';
        return $this->continue_result( $item, sprintf( __( 'Preparing gettext table %s...', 'translatepress-multilingual' ), $table_name ) );
    }

    /**
     * Resolve missing original_id values in one gettext table.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function backfill_locale_original_ids( $item, $table_name ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        $invalid_empty_ids = $this->db->get_col(
            $this->db->prepare(
                "SELECT tt.id
                FROM `$table_name` AS tt
                LEFT JOIN `$originals_table` AS ot ON tt.original_id = ot.id
                WHERE ( tt.original_id IS NULL OR tt.original_id = 0 OR ot.id IS NULL ) AND tt.original = ''
                ORDER BY tt.id
                LIMIT %d",
                self::LOCALE_ROW_BATCH_SIZE
            )
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not select invalid gettext rows without original from %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( ! empty( $invalid_empty_ids ) ) {
            $invalid_empty_sql = implode( ',', array_map( 'intval', $invalid_empty_ids ) );
            $this->db->query( "DELETE FROM `$table_name` WHERE id IN ($invalid_empty_sql)" );

            if ( $this->db->last_error !== '' ) {
                return $this->error_result( $item, sprintf( 'Could not delete invalid gettext rows without original from %1$s: %2$s', $table_name, $this->db->last_error ) );
            }

            return $this->continue_result( $item, sprintf( __( 'Removing invalid gettext rows from %s...', 'translatepress-multilingual' ), $table_name ), (int) $this->db->rows_affected );
        }

        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT tt.id, tt.original, tt.domain
                FROM `$table_name` AS tt
                LEFT JOIN `$originals_table` AS ot ON tt.original_id = ot.id
                WHERE ( tt.original_id IS NULL OR tt.original_id = 0 OR ot.id IS NULL ) AND tt.original <> ''
                ORDER BY tt.id
                LIMIT %d",
                self::LOCALE_ROW_BATCH_SIZE
            ),
            ARRAY_A
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not select gettext rows without original_id from %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( empty( $rows ) ) {
            $unresolved = $this->count_unresolved_locale_original_ids( $table_name );

            if ( $unresolved > 0 ) {
                return $this->error_result( $item, sprintf( 'Table %1$s still has %2$d gettext rows without original_id after backfill.', $table_name, $unresolved ) );
            }

            $item['locale_phase'] = 'prepare';
            return $this->continue_result( $item, sprintf( __( 'Prepared gettext original ids in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $resolved_ids = $this->find_existing_locale_original_ids( $table_name, $originals_table, $rows );
        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not resolve existing gettext originals for %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( ! empty( $resolved_ids ) ) {
            $update_error = $this->update_locale_original_ids( $table_name, $resolved_ids );
            if ( $update_error !== '' ) {
                return $this->error_result( $item, $update_error );
            }

            $rows = array_filter(
                $rows,
                function( $row ) use ( $resolved_ids ) {
                    return ! isset( $resolved_ids[ (int) $row['id'] ] );
                }
            );
        }

        if ( empty( $rows ) ) {
            return $this->continue_result( $item, sprintf( __( 'Resolving missing gettext original ids in %s...', 'translatepress-multilingual' ), $table_name ), count( $resolved_ids ) );
        }

        $incompatible_error = $this->get_incompatible_gettext_originals_error( $table_name, $originals_table, $rows );
        if ( $incompatible_error !== '' ) {
            return $this->error_result( $item, $incompatible_error );
        }

        $sync_rows = array();
        foreach ( $rows as $index => $row ) {
            $sync_rows[ $index ] = array(
                'original'        => $row['original'],
                'domain'          => $row['domain'],
                'context'         => 'trp_context',
                'original_plural' => '',
            );
        }

        $gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );
        $original_ids          = $gettext_insert_update->gettext_original_strings_sync( $sync_rows, false );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not sync gettext originals for %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        $case_rows = array();
        $ids       = array();

        foreach ( $rows as $index => $row ) {
            if ( empty( $original_ids[ $index ] ) ) {
                continue;
            }

            $ids[]       = (int) $row['id'];
            $case_rows[] = $this->db->prepare( 'WHEN %d THEN %d', (int) $row['id'], (int) $original_ids[ $index ] );
        }

        if ( empty( $case_rows ) ) {
            return $this->error_result(
                $item,
                sprintf(
                    'Gettext original_id backfill made no progress in %1$s for rows %2$s.',
                    $table_name,
                    implode( ', ', array_map( 'intval', wp_list_pluck( $rows, 'id' ) ) )
                )
            );
        }

        $ids_sql = implode( ',', $ids );
        $this->db->query( "UPDATE `$table_name` SET original_id = CASE id " . implode( ' ', $case_rows ) . " END WHERE id IN ($ids_sql)" );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not backfill original_id in %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        return $this->continue_result( $item, sprintf( __( 'Resolving missing gettext original ids in %s...', 'translatepress-multilingual' ), $table_name ), count( $resolved_ids ) + count( $case_rows ) );
    }

    /**
     * Stop before MySQL can silently truncate or lossily transcode a locale
     * identity while inserting it into the gettext originals table.
     *
     * @param string $table_name Locale gettext table.
     * @param string $originals_table Gettext originals table.
     * @param array  $rows Locale rows selected for backfill.
     *
     * @return string Empty when all rows are compatible, otherwise an actionable error.
     */
    protected function get_incompatible_gettext_originals_error( $table_name, $originals_table, $rows ) {
        $source_columns = $this->get_text_column_definitions( $table_name );
        $target_columns = $this->get_text_column_definitions( $originals_table );

        if (
            empty( $source_columns['original'] ) ||
            empty( $source_columns['domain'] ) ||
            empty( $target_columns['original'] ) ||
            empty( $target_columns['domain'] )
        ) {
            return sprintf( 'Could not determine gettext column capacity and character sets for %1$s and %2$s.', $table_name, $originals_table );
        }

        $ids             = array_map( 'intval', wp_list_pluck( $rows, 'id' ) );
        $incompatibility = $this->find_incompatible_locale_original( $table_name, $originals_table, $source_columns, $target_columns, $ids );

        if ( $this->db->last_error !== '' ) {
            return sprintf( 'Could not verify gettext originals character-set compatibility for %1$s: %2$s', $originals_table, $this->db->last_error );
        }

        if ( ! empty( $incompatibility ) ) {
            return $this->format_incompatible_locale_original_error( $incompatibility, $table_name, $originals_table );
        }

        return '';
    }

    /**
     * Return text-column capacity and character-set metadata.
     *
     * @param string $table_name Database table name.
     *
     * @return array
     */
    protected function get_text_column_definitions( $table_name ) {
        if ( isset( $this->text_column_definitions[ $table_name ] ) ) {
            return $this->text_column_definitions[ $table_name ];
        }

        $column_rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT COLUMN_NAME, CHARACTER_OCTET_LENGTH, CHARACTER_SET_NAME
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = %s
                    AND COLUMN_NAME IN ('original', 'domain')",
                $table_name
            ),
            ARRAY_A
        );

        $definitions = array();
        foreach ( $column_rows as $column_row ) {
            $column_name = isset( $column_row['COLUMN_NAME'] ) ? (string) $column_row['COLUMN_NAME'] : '';
            $capacity    = isset( $column_row['CHARACTER_OCTET_LENGTH'] ) ? (int) $column_row['CHARACTER_OCTET_LENGTH'] : 0;
            $charset     = isset( $column_row['CHARACTER_SET_NAME'] ) ? (string) $column_row['CHARACTER_SET_NAME'] : '';

            if (
                in_array( $column_name, array( 'original', 'domain' ), true ) &&
                $capacity > 0 &&
                preg_match( '/^[a-zA-Z0-9_]+$/', $charset )
            ) {
                $definitions[ $column_name ] = array(
                    'capacity' => $capacity,
                    'charset'  => $charset,
                );
            }
        }

        if ( $this->db->last_error === '' ) {
            $this->text_column_definitions[ $table_name ] = $definitions;
        }

        return $definitions;
    }

    /**
     * Find one unresolved value that would be changed or truncated by the target columns.
     *
     * @param string $table_name Locale gettext table.
     * @param string $originals_table Gettext originals table.
     * @param array  $source_columns Locale column definitions.
     * @param array  $target_columns Originals column definitions.
     * @param array  $ids Optional locale row IDs to inspect.
     *
     * @return array Incompatibility details, or an empty array.
     */
    protected function find_incompatible_locale_original( $table_name, $originals_table, $source_columns, $target_columns, $ids = array() ) {
        $source_original_charset = $source_columns['original']['charset'];
        $source_domain_charset   = $source_columns['domain']['charset'];
        $target_original_charset = $target_columns['original']['charset'];
        $target_domain_charset   = $target_columns['domain']['charset'];
        $target_original_size    = (int) $target_columns['original']['capacity'];
        $target_domain_size      = (int) $target_columns['domain']['capacity'];
        $ids_condition           = '';

        if ( ! empty( $ids ) ) {
            $ids_condition = 'AND tt.id IN (' . implode( ',', array_map( 'intval', $ids ) ) . ')';
        }

        $original_size = "OCTET_LENGTH(CONVERT(tt.original USING $target_original_charset))";
        $domain_size   = "OCTET_LENGTH(CONVERT(COALESCE(tt.domain, '') USING $target_domain_charset))";

        // Keep the successful preflight path to one scan. Only calculate
        // diagnostic details when an incompatible row was actually found.
        $incompatible_id = (int) $this->db->get_var(
            "SELECT tt.id
            FROM `$table_name` AS tt
            LEFT JOIN `$originals_table` AS current_original ON tt.original_id = current_original.id
            WHERE (tt.original_id IS NULL OR tt.original_id = 0 OR current_original.id IS NULL)
                AND tt.original <> ''
                $ids_condition
                AND (
                    $original_size > $target_original_size
                    OR $domain_size > $target_domain_size
                    OR BINARY tt.original <> BINARY CONVERT(CONVERT(tt.original USING $target_original_charset) USING $source_original_charset)
                    OR BINARY COALESCE(tt.domain, '') <> BINARY CONVERT(CONVERT(COALESCE(tt.domain, '') USING $target_domain_charset) USING $source_domain_charset)
                )
            ORDER BY tt.id
            LIMIT 1"
        );

        if ( $incompatible_id <= 0 || $this->db->last_error !== '' ) {
            return array();
        }

        $incompatibility = $this->db->get_row(
            $this->db->prepare(
                "SELECT tt.id,
                    CASE
                        WHEN $original_size > $target_original_size THEN 'original_capacity'
                        WHEN $domain_size > $target_domain_size THEN 'domain_capacity'
                        WHEN BINARY tt.original <> BINARY CONVERT(CONVERT(tt.original USING $target_original_charset) USING $source_original_charset) THEN 'original_charset'
                        ELSE 'domain_charset'
                    END AS reason,
                    CASE
                        WHEN $original_size > $target_original_size THEN $original_size
                        WHEN $domain_size > $target_domain_size THEN $domain_size
                        ELSE 0
                    END AS value_size,
                    CASE
                        WHEN $original_size > $target_original_size THEN $target_original_size
                        WHEN $domain_size > $target_domain_size THEN $target_domain_size
                        ELSE 0
                    END AS target_capacity
                FROM `$table_name` AS tt
                WHERE tt.id = %d",
                $incompatible_id
            ),
            ARRAY_A
        );

        return is_array( $incompatibility ) ? $incompatibility : array();
    }

    /**
     * Format an actionable error for a locale value that cannot be copied safely.
     *
     * @param array  $incompatibility Incompatibility details.
     * @param string $table_name Locale gettext table.
     * @param string $originals_table Gettext originals table.
     *
     * @return string
     */
    protected function format_incompatible_locale_original_error( $incompatibility, $table_name, $originals_table ) {
        $row_id = isset( $incompatibility['id'] ) ? (int) $incompatibility['id'] : 0;
        $reason = isset( $incompatibility['reason'] ) ? (string) $incompatibility['reason'] : '';

        if ( $reason === 'original_capacity' || $reason === 'domain_capacity' ) {
            $column          = $reason === 'original_capacity' ? 'original' : 'domain';
            $value_size      = isset( $incompatibility['value_size'] ) ? (int) $incompatibility['value_size'] : 0;
            $target_capacity = isset( $incompatibility['target_capacity'] ) ? (int) $incompatibility['target_capacity'] : 0;

            return sprintf(
                'Gettext optimization cannot continue because the %1$s value in row %2$d from %3$s is %4$d bytes, but %5$s.%1$s accepts at most %6$d bytes. Increase that column capacity or remove or shorten the value, then retry.',
                $column,
                $row_id,
                $table_name,
                $value_size,
                $originals_table,
                $target_capacity
            );
        }

        return sprintf(
            'Gettext optimization cannot continue because row %1$d from %2$s cannot be represented by the original/domain character sets in %3$s. Convert the gettext originals table to a compatible character set, then retry.',
            $row_id,
            $table_name,
            $originals_table
        );
    }

    /**
     * Resolve selected locale rows against existing originals inside MySQL.
     *
     * Select one indexed comparison strategy for the whole table. Raw-byte
     * equality is safe only when source and target use the same character sets.
     * Mixed-character-set tables convert the locale values while leaving the
     * indexed originals columns unwrapped.
     *
     * @param string $table_name Locale gettext table.
     * @param string $originals_table Gettext originals table.
     * @param array  $rows Selected locale rows.
     *
     * @return array Locale row ID to original row ID map.
     */
    protected function find_existing_locale_original_ids( $table_name, $originals_table, $rows ) {
        $ids = array_map( 'intval', wp_list_pluck( $rows, 'id' ) );
        if ( empty( $ids ) ) {
            return array();
        }

        $source_columns = $this->get_text_column_definitions( $table_name );

        if ( $this->db->last_error !== '' ) {
            return array();
        }

        $target_columns = $this->get_text_column_definitions( $originals_table );

        if (
            empty( $source_columns['original']['charset'] ) ||
            empty( $source_columns['domain']['charset'] ) ||
            empty( $target_columns['original']['charset'] ) ||
            empty( $target_columns['domain']['charset'] )
        ) {
            $this->db->last_error = sprintf( 'Could not determine gettext column character sets for %1$s and %2$s.', $table_name, $originals_table );
            return array();
        }

        $character_sets_differ =
            strcasecmp( $source_columns['original']['charset'], $target_columns['original']['charset'] ) !== 0 ||
            strcasecmp( $source_columns['domain']['charset'], $target_columns['domain']['charset'] ) !== 0;

        return $this->query_existing_locale_original_ids(
            $table_name,
            $originals_table,
            $ids,
            $character_sets_differ,
            $source_columns,
            $target_columns
        );
    }

    /**
     * Query existing gettext original IDs for selected locale rows.
     *
     * @param string $table_name Locale gettext table.
     * @param string $originals_table Gettext originals table.
     * @param array  $ids Locale row IDs.
     * @param bool   $convert_character_sets Convert locale values to the originals column character sets.
     * @param array  $source_columns Locale text-column definitions.
     * @param array  $target_columns Originals text-column definitions.
     *
     * @return array Locale row ID to original row ID map.
     */
    protected function query_existing_locale_original_ids( $table_name, $originals_table, $ids, $convert_character_sets, $source_columns = array(), $target_columns = array() ) {
        if ( empty( $ids ) ) {
            return array();
        }

        if ( $convert_character_sets ) {
            $source_original_charset = $source_columns['original']['charset'];
            $source_domain_charset   = $source_columns['domain']['charset'];
            $target_original_charset = $target_columns['original']['charset'];
            $target_domain_charset   = $target_columns['domain']['charset'];

            // Convert only locale values so the originals index remains usable. The
            // BINARY is exact; source -> target -> source rejects lossy conversions like € -> ?.
            $identity_condition = "originals.original = BINARY CONVERT(tt.original USING $target_original_charset)
                AND originals.domain = BINARY CONVERT(COALESCE(tt.domain, '') USING $target_domain_charset)
                AND BINARY tt.original = BINARY CONVERT(CONVERT(tt.original USING $target_original_charset) USING $source_original_charset)
                AND BINARY COALESCE(tt.domain, '') = BINARY CONVERT(CONVERT(COALESCE(tt.domain, '') USING $target_domain_charset) USING $source_domain_charset)";
        } else {
            $identity_condition = "originals.original = BINARY tt.original
                AND originals.domain = BINARY COALESCE(tt.domain, '')";
        }

        $matches = $this->db->get_results(
            "SELECT tt.id AS locale_id, MIN(originals.id) AS original_id
            FROM `$table_name` AS tt
            INNER JOIN `$originals_table` AS originals
                ON $identity_condition
            WHERE tt.id IN (" . implode( ',', $ids ) . ")
            GROUP BY tt.id",
            ARRAY_A
        );

        $resolved_ids = array();
        foreach ( $matches as $match ) {
            $resolved_ids[ (int) $match['locale_id'] ] = (int) $match['original_id'];
        }

        return $resolved_ids;
    }

    /**
     * Apply an original ID map to one locale table.
     *
     * @param string $table_name Locale gettext table.
     * @param array  $resolved_ids Locale row ID to original row ID map.
     *
     * @return string Empty on success, otherwise an error message.
     */
    protected function update_locale_original_ids( $table_name, $resolved_ids ) {
        if ( empty( $resolved_ids ) ) {
            return '';
        }

        $case_rows = array();
        foreach ( $resolved_ids as $locale_id => $original_id ) {
            $case_rows[] = $this->db->prepare( 'WHEN %d THEN %d', (int) $locale_id, (int) $original_id );
        }

        $ids_sql = implode( ',', array_map( 'intval', array_keys( $resolved_ids ) ) );
        $this->db->query( "UPDATE `$table_name` SET original_id = CASE id " . implode( ' ', $case_rows ) . " END WHERE id IN ($ids_sql)" );

        if ( $this->db->last_error !== '' ) {
            return sprintf( 'Could not backfill original_id in %1$s: %2$s', $table_name, $this->db->last_error );
        }

        return '';
    }

    /**
     * Normalize null plural forms in batches.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function normalize_locale_plural_form( $item, $table_name ) {
        $this->db->query(
            $this->db->prepare(
                "UPDATE `$table_name` SET plural_form = 0 WHERE plural_form IS NULL LIMIT %d",
                self::LOCALE_ROW_BATCH_SIZE
            )
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not normalize plural_form in %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( (int) $this->db->rows_affected === 0 ) {
            $item['locale_phase']            = 'build_map';
            $item['locale_last_original_id'] = 0;
            $item['locale_last_plural_form'] = -1;
        }

        return $this->continue_result( $item, sprintf( __( 'Normalizing plural forms in %s...', 'translatepress-multilingual' ), $table_name ), (int) $this->db->rows_affected );
    }

    /**
     * Build duplicate map rows for one locale table.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function build_locale_dedup_map( $item, $table_name ) {
        $last_original_id = (int) $item['locale_last_original_id'];
        $last_plural_form = (int) $item['locale_last_plural_form'];

        $groups = $this->db->get_results(
            $this->db->prepare(
                "SELECT original_id, COALESCE(plural_form, 0) AS normalized_plural_form, COUNT(*) AS duplicate_count
                FROM `$table_name`
                WHERE original_id IS NOT NULL AND original_id <> 0
                    AND ( original_id > %d OR ( original_id = %d AND COALESCE(plural_form, 0) > %d ) )
                GROUP BY original_id, normalized_plural_form
                HAVING duplicate_count > 1
                ORDER BY original_id, normalized_plural_form
                LIMIT %d",
                $last_original_id,
                $last_original_id,
                $last_plural_form,
                self::LOCALE_GROUP_BATCH_SIZE
            ),
            ARRAY_A
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not select duplicate gettext rows in %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( empty( $groups ) ) {
            $item['locale_phase'] = 'process_map';
            return $this->continue_result( $item, sprintf( __( 'Processing duplicate gettext rows in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $mapped_rows = 0;
        foreach ( $groups as $group ) {
            $mapped_rows += $this->map_locale_duplicate_group( $table_name, (int) $group['original_id'], (int) $group['normalized_plural_form'] );
            $item['locale_last_original_id'] = (int) $group['original_id'];
            $item['locale_last_plural_form'] = (int) $group['normalized_plural_form'];
        }

        $item['stats']['duplicate_gettext'] += $mapped_rows;

        if ( count( $groups ) < self::LOCALE_GROUP_BATCH_SIZE ) {
            $item['locale_phase'] = 'process_map';
        }

        return $this->continue_result( $item, sprintf( __( 'Building duplicate gettext map for %s...', 'translatepress-multilingual' ), $table_name ), $mapped_rows );
    }

    /**
     * Map duplicate rows for one original/plural group.
     *
     * @param string $table_name Gettext locale table.
     * @param int    $original_id Original id.
     * @param int    $plural_form Plural form.
     *
     * @return int Number of duplicate rows mapped.
     */
    protected function map_locale_duplicate_group( $table_name, $original_id, $plural_form ) {
        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT id, translated, status FROM `$table_name` WHERE original_id = %d AND COALESCE(plural_form, 0) = %d ORDER BY id",
                $original_id,
                $plural_form
            ),
            ARRAY_A
        );

        if ( empty( $rows ) || count( $rows ) < 2 ) {
            return 0;
        }

        $survivor    = $this->choose_locale_survivor( $rows );
        $insert_rows = array();

        foreach ( $rows as $row ) {
            if ( (int) $row['id'] === (int) $survivor['id'] ) {
                continue;
            }

            $insert_rows[] = $this->db->prepare(
                '(%s, %d, %d, 0)',
                $table_name,
                (int) $row['id'],
                (int) $survivor['id']
            );
        }

        if ( empty( $insert_rows ) ) {
            return 0;
        }

        $map_table = $this->get_locale_map_table_name();
        $this->db->query( "INSERT IGNORE INTO `$map_table` (table_name, duplicate_id, survivor_id, processed) VALUES " . implode( ',', $insert_rows ) );

        return $this->db->last_error === '' ? count( $insert_rows ) : 0;
    }

    /**
     * Choose which duplicate gettext row survives.
     *
     * @param array $rows Duplicate rows.
     *
     * @return array
     */
    protected function choose_locale_survivor( $rows ) {
        usort( $rows, function ( $a, $b ) {
            $a_score = $this->get_locale_row_score( $a );
            $b_score = $this->get_locale_row_score( $b );

            if ( $a_score !== $b_score ) {
                return ( $a_score > $b_score ) ? -1 : 1;
            }

            return (int) $a['id'] <=> (int) $b['id'];
        } );

        return $rows[0];
    }

    /**
     * Score gettext rows for duplicate survivor selection.
     *
     * @param array $row Gettext row.
     *
     * @return int
     */
    protected function get_locale_row_score( $row ) {
        $status = isset( $row['status'] ) ? (int) $row['status'] : TRP_Query::NOT_TRANSLATED;
        $score  = empty( $row['translated'] ) ? 0 : 10;

        if ( $status === TRP_Query::HUMAN_REVIEWED ) {
            $score += 5;
        } elseif ( $status === TRP_Query::MACHINE_TRANSLATED ) {
            $score += 4;
        } elseif ( $status === TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE ) {
            $score += 3;
        }

        return $score;
    }

    /**
     * Delete mapped duplicate rows from one locale table.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function process_locale_dedup_map( $item, $table_name ) {
        $map_table = $this->get_locale_map_table_name();
        $map_rows  = $this->db->get_results(
            $this->db->prepare(
                "SELECT duplicate_id FROM `$map_table` WHERE table_name = %s AND processed = 0 ORDER BY duplicate_id LIMIT %d",
                $table_name,
                self::LOCALE_ROW_BATCH_SIZE
            ),
            ARRAY_A
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not select gettext duplicate map rows for %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        if ( empty( $map_rows ) ) {
            $item['locale_phase'] = 'verify';
            return $this->continue_result( $item, sprintf( __( 'Verifying gettext table %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $duplicate_ids = array_map( 'intval', wp_list_pluck( $map_rows, 'duplicate_id' ) );
        $duplicate_sql = implode( ',', $duplicate_ids );

        $this->db->query( "DELETE FROM `$table_name` WHERE id IN ($duplicate_sql)" );
        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not delete duplicate gettext rows from %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        $deleted = (int) $this->db->rows_affected;

        $this->db->query(
            $this->db->prepare(
                "UPDATE `$map_table` SET processed = 1 WHERE table_name = %s AND duplicate_id IN ($duplicate_sql)",
                $table_name
            )
        );

        if ( $this->db->last_error !== '' ) {
            return $this->error_result( $item, sprintf( 'Could not mark gettext duplicate map rows processed for %1$s: %2$s', $table_name, $this->db->last_error ) );
        }

        return $this->continue_result( $item, sprintf( __( 'Deleting duplicate gettext rows from %s...', 'translatepress-multilingual' ), $table_name ), $deleted );
    }

    /**
     * Verify a locale table can receive the unique index.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function verify_locale_table( $item, $table_name ) {
        $unresolved = $this->count_unresolved_locale_original_ids( $table_name );
        if ( $unresolved > 0 ) {
            if ( $this->trp_query->table_index_exists( $table_name, 'gettext_original_plural_unique' ) ) {
                $this->db->query( "ALTER TABLE `$table_name` DROP INDEX gettext_original_plural_unique" );

                if ( $this->db->last_error !== '' ) {
                    return $this->error_result( $item, sprintf( 'Could not drop gettext unique index before repairing %1$s: %2$s', $table_name, $this->db->last_error ) );
                }
            }

            $item['locale_phase'] = 'backfill_original_ids';
            return $this->continue_result( $item, sprintf( __( 'Resolving missing gettext original ids in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $null_plural = (int) $this->db->get_var( "SELECT COUNT(*) FROM `$table_name` WHERE plural_form IS NULL" );
        if ( $null_plural > 0 ) {
            $item['locale_phase'] = 'normalize_plural';
            return $this->continue_result( $item, sprintf( __( 'Normalizing plural forms in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $duplicate_groups = (int) $this->db->get_var( "SELECT COUNT(*) FROM (SELECT original_id, plural_form FROM `$table_name` GROUP BY original_id, plural_form HAVING COUNT(*) > 1) AS duplicate_groups" );
        if ( $duplicate_groups > 0 ) {
            $item['locale_phase']            = 'build_map';
            $item['locale_last_original_id'] = 0;
            $item['locale_last_plural_form'] = -1;
            return $this->continue_result( $item, sprintf( __( 'Rechecking duplicate gettext rows in %s...', 'translatepress-multilingual' ), $table_name ) );
        }

        $item['locale_phase'] = 'unique_index';
        return $this->continue_result( $item, sprintf( __( 'Adding unique gettext index for %s...', 'translatepress-multilingual' ), $table_name ) );
    }

    /**
     * Add the locale unique index after verification.
     *
     * @param array  $item Todo item.
     * @param string $table_name Gettext locale table.
     *
     * @return array
     */
    protected function add_locale_unique_index( $item, $table_name ) {
        if ( ! $this->trp_query->table_index_exists( $table_name, 'gettext_original_plural_unique' ) ) {
            $this->db->query( "CREATE UNIQUE INDEX gettext_original_plural_unique ON `$table_name` (original_id, plural_form)" );

            if ( $this->db->last_error !== '' ) {
                if ( stripos( $this->db->last_error, 'Duplicate entry' ) !== false ) {
                    $this->db->last_error                 = '';
                    $item['locale_phase']                 = 'build_map';
                    $item['locale_last_original_id']      = 0;
                    $item['locale_last_plural_form']      = -1;
                    $this->ensure_locale_map_table();

                    return $this->continue_result( $item, sprintf( __( 'Rechecking duplicate gettext rows in %s...', 'translatepress-multilingual' ), $table_name ) );
                }

                return $this->error_result( $item, sprintf( 'Could not add unique gettext index to %1$s: %2$s', $table_name, $this->db->last_error ) );
            }
        }

        $item['locale_table_index']++;
        $item['locale_phase']            = 'prepare';
        $item['locale_last_original_id'] = 0;
        $item['locale_last_plural_form'] = -1;

        return $this->continue_result( $item, sprintf( __( 'Finished gettext table %s.', 'translatepress-multilingual' ), $table_name ) );
    }

    /**
     * Create the shared locale dedupe map.
     */
    protected function ensure_locale_map_table() {
        $map_table       = $this->get_locale_map_table_name();
        $charset_collate = $this->db->get_charset_collate();

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `$map_table` (
                table_name VARCHAR(191) NOT NULL,
                duplicate_id BIGINT(20) NOT NULL,
                survivor_id BIGINT(20) NOT NULL,
                processed TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (table_name, duplicate_id),
                KEY processed (processed),
                KEY survivor_id (survivor_id)
            ) $charset_collate"
        );
    }

    /**
     * Drop the shared locale dedupe map.
     */
    protected function drop_locale_map_table() {
        $map_table = $this->get_locale_map_table_name();

        if ( $this->trp_query->table_exists( $map_table, true ) ) {
            $this->db->query( "DROP TABLE `$map_table`" );
        }
    }

    /**
     * Return the locale dedupe map table name.
     *
     * @return string
     */
    protected function get_locale_map_table_name() {
        return sanitize_text_field( $this->db->prefix . 'trp_gettext_locale_dedup_map' );
    }

    /**
     * Return gettext locale tables.
     *
     * @return array
     */
    protected function get_gettext_locale_tables() {
        $tables = $this->trp_query->get_all_gettext_table_names();
        $locale_map_table = $this->get_locale_map_table_name();

        $tables = array_values( array_filter( $tables, function ( $table_name ) use ( $locale_map_table ) {
            return $table_name !== $locale_map_table;
        } ) );

        sort( $tables );

        return $tables;
    }

    /**
     * Verify that gettext tables can safely use ID-based batch cursors.
     *
     * @return string Empty when valid, otherwise an actionable error.
     */
    protected function validate_gettext_id_schema() {
        $tables = array_merge(
            array( $this->trp_query->get_table_name_for_gettext_original_strings() ),
            $this->get_gettext_locale_tables()
        );

        foreach ( array_unique( $tables ) as $table_name ) {
            if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $table_name ) ) {
                return sprintf( 'Could not validate gettext table ID schema for invalid table name %s.', $table_name );
            }

            $column = $this->db->get_row(
                $this->db->prepare(
                    "SELECT IS_NULLABLE, EXTRA
                    FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s AND COLUMN_NAME = 'id'",
                    $table_name
                ),
                ARRAY_A
            );

            if ( $this->db->last_error !== '' ) {
                return sprintf( 'Could not validate the ID column in %1$s: %2$s', $table_name, $this->db->last_error );
            }

            if ( empty( $column ) ) {
                return sprintf( 'Gettext optimization cannot start because table %s is missing its id column.', $table_name );
            }

            $primary_columns = $this->db->get_col(
                $this->db->prepare(
                    "SELECT COLUMN_NAME
                    FROM information_schema.STATISTICS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s AND INDEX_NAME = 'PRIMARY'
                    ORDER BY SEQ_IN_INDEX",
                    $table_name
                )
            );

            if ( $this->db->last_error !== '' ) {
                return sprintf( 'Could not validate the primary key in %1$s: %2$s', $table_name, $this->db->last_error );
            }

            $problems = array();

            if ( $column['IS_NULLABLE'] !== 'NO' ) {
                $problems[] = 'id must be NOT NULL';
            }

            if ( stripos( $column['EXTRA'], 'auto_increment' ) === false ) {
                $problems[] = 'AUTO_INCREMENT is missing from id';
            }

            if ( $primary_columns !== array( 'id' ) ) {
                $problems[] = 'PRIMARY KEY (id) is missing';
            }

            if ( ! empty( $problems ) ) {
                return sprintf(
                    'Gettext optimization cannot start because table %1$s has an invalid ID schema: %2$s. Repair the table schema and retry.',
                    $table_name,
                    implode( '; ', $problems )
                );
            }

            $invalid_id = $this->db->get_var( "SELECT id FROM `$table_name` WHERE id <= 0 LIMIT 1" );

            if ( $this->db->last_error !== '' ) {
                return sprintf( 'Could not validate gettext IDs in %1$s: %2$s', $table_name, $this->db->last_error );
            }

            if ( $invalid_id !== null ) {
                return sprintf( 'Gettext optimization cannot start because table %s contains an id of zero or less. Repair the affected rows and retry.', $table_name );
            }
        }

        return '';
    }

    /**
     * Resolve the locale code represented by a gettext table.
     *
     * @param string $table_name Gettext locale table.
     *
     * @return string
     */
    protected function get_language_code_from_gettext_table( $table_name ) {
        $trp              = TRP_Translate_Press::get_trp_instance();
        $languages        = $trp->get_component( 'languages' );
        $language_codes   = $languages ? $languages->get_all_language_codes() : array();
        $configured_codes = isset( $this->settings['translation-languages'] ) ? $this->settings['translation-languages'] : array();

        foreach ( array_unique( array_merge( $configured_codes, $language_codes ) ) as $language_code ) {
            if ( $this->trp_query->get_gettext_table_name( $language_code ) === $table_name ) {
                return $language_code;
            }
        }

        $table_prefix = $this->db->get_blog_prefix() . 'trp_gettext_';

        return substr( $table_name, strlen( $table_prefix ) );
    }

    /**
     * Check whether a gettext table name is safe to interpolate.
     *
     * @param string $table_name Table name.
     *
     * @return bool
     */
    protected function is_valid_gettext_locale_table( $table_name ) {
        if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $table_name ) ) {
            return false;
        }

        if ( strpos( $table_name, $this->db->prefix . 'trp_gettext_' ) !== 0 ) {
            return false;
        }

        return strpos( $table_name, 'trp_gettext_original_' ) === false;
    }

    /**
     * Check whether a gettext table has a lookup index starting with original_id.
     *
     * @param string $table_name Table name.
     *
     * @return bool
     */
    protected function gettext_table_has_original_id_lookup_index( $table_name ) {
        $indexes = $this->db->get_results(
            $this->db->prepare(
                "SHOW INDEX FROM `$table_name` WHERE Seq_in_index = 1 AND Column_name = %s",
                'original_id'
            )
        );

        return ! empty( $indexes );
    }

    /**
     * Count gettext rows whose original_id is missing or points to no original row.
     *
     * @param string $table_name Gettext locale table.
     *
     * @return int
     */
    protected function count_unresolved_locale_original_ids( $table_name ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        return (int) $this->db->get_var(
            "SELECT COUNT(*)
            FROM `$table_name` AS tt
            LEFT JOIN `$originals_table` AS ot ON tt.original_id = ot.id
            WHERE tt.original_id IS NULL OR tt.original_id = 0 OR ot.id IS NULL"
        );
    }

    /**
     * Return an available original_id index name.
     *
     * @param string $table_name Table name.
     *
     * @return string
     */
    protected function get_available_original_id_index_name( $table_name ) {
        for ( $index = 1; $index <= 10; $index++ ) {
            $index_name = ( $index === 1 ) ? 'gettext_index_original_id' : 'gettext_index_original_id_' . $index;

            if ( ! $this->trp_query->table_index_exists( $table_name, $index_name ) ) {
                return $index_name;
            }
        }

        return 'gettext_index_original_id_' . wp_rand( 1000, 9999 );
    }

    /**
     * Normalize item defaults.
     *
     * @param array $item Todo item.
     *
     * @return array
     */
    protected function normalize_item( $item ) {
        $defaults = $this->build_todo_list();

        return array_merge( $defaults[0], is_array( $item ) ? $item : array() );
    }

    /**
     * Return an in-progress execution result.
     *
     * @param array  $item Todo item.
     * @param string $message Progress message.
     * @param int    $affected_rows Affected row count.
     *
     * @return array
     */
    protected function continue_result( $item, $message, $affected_rows = 0 ) {
        return array(
            'status'        => 'not_completed',
            'affected_rows' => $affected_rows,
            'item'          => $item,
            'progress'      => array(
                'message' => $message,
            ),
        );
    }

    /**
     * Return a failed execution result.
     *
     * @param array  $item Todo item.
     * @param string $message Error message.
     *
     * @return array
     */
    protected function error_result( $item, $message ) {
        update_option( 'trp_updated_database_gettext_tables_optimization', 'no', false );

        return array(
            'status'   => 'failed',
            'error'    => $message,
            'item'     => $item,
            'progress' => array(
                'message' => $message,
            ),
        );
    }

    /**
     * Return total affected rows tracked by the task.
     *
     * @param array $item Todo item.
     *
     * @return int
     */
    protected function get_total_affected_rows( $item ) {
        $stats = isset( $item['stats'] ) && is_array( $item['stats'] ) ? $item['stats'] : array();

        return (int) ( isset( $stats['duplicate_originals'] ) ? $stats['duplicate_originals'] : 0 ) + (int) ( isset( $stats['duplicate_gettext'] ) ? $stats['duplicate_gettext'] : 0 );
    }
}
