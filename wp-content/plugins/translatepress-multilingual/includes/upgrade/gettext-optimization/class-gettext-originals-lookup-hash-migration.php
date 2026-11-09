<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Handles the phase-based gettext originals lookup hash migration.
 */
class TRP_Gettext_Originals_Lookup_Hash_Migration {

    protected $db;
    protected $trp_query;

    public function __construct( $trp_query = null ) {
        global $wpdb;

        $this->db = $wpdb;

        if ( ! $trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp_query = $trp->get_component( 'query' );
        }

        $this->trp_query = $trp_query;
    }

    public function run( $language_code, $inferior_limit, $batch_size, $extra_params = array() ) {
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $phase      = isset( $extra_params['phase'] ) ? sanitize_text_field( $extra_params['phase'] ) : 'schema';
        $batch_size = max( 1, (int) $batch_size );

        if ( ! $this->trp_query->table_exists( $this->trp_query->get_table_name_for_gettext_original_strings() ) ) {
            return true;
        }

        if ( $phase === 'schema' ) {
            $this->prepare_gettext_original_lookup_hash_schema();

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array( 'phase' => 'reference_indexes' ),
            );
        }

        if ( $phase === 'reference_indexes' ) {
            $last_table = isset( $extra_params['last_table'] ) ? sanitize_text_field( $extra_params['last_table'] ) : '';
            $result     = $this->prepare_gettext_original_reference_indexes( $last_table );

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array(
                    'phase'      => $result['complete'] ? 'backfill' : 'reference_indexes',
                    'last_table' => $result['last_table'],
                ),
            );
        }

        if ( $phase === 'backfill' ) {
            $last_id = isset( $extra_params['last_id'] ) ? (int) $extra_params['last_id'] : 0;
            $result  = $this->backfill_gettext_original_lookup_hashes( $batch_size, $last_id );

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array(
                    'phase'   => $result['complete'] ? 'build_map' : 'backfill',
                    'last_id' => $result['last_id'],
                ),
            );
        }

        if ( $phase === 'build_map' ) {
            $last_hash = isset( $extra_params['last_hash'] ) ? sanitize_text_field( $extra_params['last_hash'] ) : '';
            $result    = $this->build_gettext_originals_dedup_map( $last_hash, $batch_size );

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array(
                    'phase'     => $result['complete'] ? 'process_map' : 'build_map',
                    'last_hash' => $result['last_hash'],
                ),
            );
        }

        if ( $phase === 'process_map' ) {
            $result = $this->process_gettext_originals_dedup_map( $batch_size );

            return array(
                'finalize_with_language' => false,
                'deleted_originals'      => $result['deleted'],
                'extra_params'           => array( 'phase' => $result['remaining'] ? 'process_map' : 'verify' ),
            );
        }

        if ( $phase === 'verify' ) {
            $verification_result = $this->verify_gettext_original_lookup_hash_migration();

            if ( $verification_result !== true ) {
                return array(
                    'finalize_with_language' => false,
                    'extra_params'           => array(
                        'phase'     => $verification_result,
                        'last_hash' => '',
                    ),
                );
            }

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array( 'phase' => 'unique_index' ),
            );
        }

        if ( $phase === 'unique_index' ) {
            $this->finalize_gettext_original_lookup_hash_schema();

            return array(
                'finalize_with_language' => false,
                'extra_params'           => array( 'phase' => 'cleanup' ),
            );
        }

        if ( $phase === 'cleanup' ) {
            $this->cleanup_gettext_original_lookup_hash_migration();

            return true;
        }

        $this->fail_gettext_original_lookup_hash_migration(
            sprintf(
                __( 'Update aborted! Unknown gettext lookup hash migration phase: %s.', 'translatepress-multilingual' ),
                esc_html( $phase )
            )
        );
    }

    /**
     * Add the nullable lookup_hash column and create the temporary deduplication map.
     *
     * @return void
     */
    protected function prepare_gettext_original_lookup_hash_schema() {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        if ( ! $this->trp_query->table_exists( $originals_table ) ) {
            return;
        }

        if ( ! $this->trp_query->table_column_exists( $originals_table, 'lookup_hash' ) ) {
            $this->db->query( "ALTER TABLE `$originals_table` ADD lookup_hash CHAR(32) NULL" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'adding lookup_hash column' );
        }

        if (
            $this->trp_query->table_column_exists( $originals_table, 'lookup_hash' ) &&
            ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash' ) &&
            ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' )
        ) {
            $this->db->query( "CREATE INDEX gettext_lookup_hash ON `$originals_table` (lookup_hash)" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'creating gettext original lookup_hash index' );
        }

        $map_table       = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );
        $charset_collate = $this->db->get_charset_collate();

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `$map_table` (
                duplicate_id BIGINT(20) NOT NULL PRIMARY KEY,
                survivor_id BIGINT(20) NOT NULL,
                lookup_hash CHAR(32) NOT NULL,
                processed TINYINT(1) NOT NULL DEFAULT 0,
                KEY survivor_id (survivor_id),
                KEY processed (processed)
            ) $charset_collate"
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'creating gettext originals deduplication map' );
    }

    /**
     * Make sure gettext locale tables can repoint duplicate original ids quickly.
     *
     * @param string $last_table Last table processed in a previous request.
     *
     * @return array
     */
    protected function prepare_gettext_original_reference_indexes( $last_table = '' ) {
        $gettext_tables = $this->trp_query->get_all_gettext_table_names();
        sort( $gettext_tables );

        foreach ( $gettext_tables as $table_name ) {
            $table_name = sanitize_text_field( $table_name );

            if ( $last_table !== '' && strcmp( $table_name, $last_table ) <= 0 ) {
                continue;
            }

            if ( ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) || $this->gettext_table_has_original_id_lookup_index( $table_name ) ) {
                continue;
            }

            $index_name = $this->get_available_gettext_original_id_index_name( $table_name );
            $this->db->query( "CREATE INDEX `$index_name` ON `$table_name` (original_id)" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'creating gettext locale original_id index' );

            return array(
                'complete'   => false,
                'last_table' => $table_name,
            );
        }

        return array(
            'complete'   => true,
            'last_table' => '',
        );
    }

    /**
     * Check whether a gettext locale table has an index starting with original_id.
     *
     * @param string $table_name Gettext locale table name.
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
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'checking gettext locale original_id indexes' );

        return ! empty( $indexes );
    }

    /**
     * Pick an index name that is not already used by a gettext locale table.
     *
     * @param string $table_name Gettext locale table name.
     *
     * @return string
     */
    protected function get_available_gettext_original_id_index_name( $table_name ) {
        for ( $index = 1; $index <= 10; $index++ ) {
            $index_name = ( $index === 1 ) ? 'gettext_index_original_id' : 'gettext_index_original_id_' . $index;

            if ( ! $this->trp_query->table_index_exists( $table_name, $index_name ) ) {
                return $index_name;
            }
        }

        $this->fail_gettext_original_lookup_hash_migration(
            sprintf(
                __( 'Update aborted! Could not find an available gettext original_id index name for table %s.', 'translatepress-multilingual' ),
                esc_html( $table_name )
            )
        );
    }

    /**
     * Build canonical lookup hashes in bounded batches.
     *
     * All rows are inspected, including rows populated by an interrupted older
     * migration. This prevents a retry after a plugin update from preserving a
     * partial set of hashes generated with an incompatible algorithm.
     *
     * @param int $batch_size Batch size.
     * @param int $last_id Last inspected original ID.
     *
     * @return array Batch completion state and last inspected ID.
     */
    protected function backfill_gettext_original_lookup_hashes( $batch_size, $last_id = 0 ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        if ( ! $this->trp_query->table_exists( $originals_table ) ) {
            return array(
                'complete' => true,
                'last_id'  => (int) $last_id,
            );
        }

        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT id, original, domain, context, lookup_hash FROM `$originals_table` WHERE id > %d ORDER BY id LIMIT %d",
                $last_id,
                $batch_size
            ),
            ARRAY_A
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting gettext originals for lookup_hash backfill' );

        if ( empty( $rows ) ) {
            return array(
                'complete' => true,
                'last_id'  => (int) $last_id,
            );
        }

        foreach ( $rows as $row ) {
            if ( (int) $row['id'] <= 0 ) {
                $this->fail_gettext_original_lookup_hash_migration(
                    sprintf(
                        __( 'Update aborted! Gettext lookup hash migration cannot process invalid ID %1$d in table %2$s. Repair the table ID schema and retry.', 'translatepress-multilingual' ),
                        (int) $row['id'],
                        esc_html( $originals_table )
                    )
                );
            }

            $lookup_hash = $this->trp_query->get_gettext_original_lookup_hash( $row['original'], $row['domain'], $row['context'] );

            if ( $row['lookup_hash'] !== $lookup_hash ) {
                $this->db->query(
                    $this->db->prepare(
                        "UPDATE `$originals_table` SET lookup_hash = %s WHERE id = %d",
                        $lookup_hash,
                        (int) $row['id']
                    )
                );
                $this->fail_gettext_original_lookup_hash_migration_on_error( 'backfilling gettext original lookup_hash' );
            }

            $last_id = (int) $row['id'];
        }

        return array(
            'complete' => count( $rows ) < $batch_size,
            'last_id'  => $last_id,
        );
    }

    /**
     * Build a durable duplicate-to-survivor map for exact duplicate originals.
     *
     * @param string $last_hash Last processed lookup hash.
     * @param int    $batch_size Number of hash groups to inspect.
     *
     * @return array
     */
    protected function build_gettext_originals_dedup_map( $last_hash, $batch_size ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );
        $map_table       = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );

        if (
            ! $this->trp_query->table_exists( $map_table, true ) &&
            $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' )
        ) {
            return array(
                'complete'  => true,
                'last_hash' => $last_hash,
            );
        }

        $hash_groups = $this->db->get_col(
            $this->db->prepare(
                "SELECT lookup_hash FROM `$originals_table` WHERE lookup_hash IS NOT NULL AND lookup_hash > %s GROUP BY lookup_hash HAVING COUNT(*) > 1 ORDER BY lookup_hash LIMIT %d",
                $last_hash,
                $batch_size
            )
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting duplicate gettext original hash groups' );

        if ( empty( $hash_groups ) ) {
            return array(
                'complete'  => true,
                'last_hash' => $last_hash,
            );
        }

        foreach ( $hash_groups as $lookup_hash ) {
            $this->map_gettext_original_duplicate_group( $lookup_hash );
            $last_hash = $lookup_hash;
        }

        return array(
            'complete'  => count( $hash_groups ) < $batch_size,
            'last_hash' => $last_hash,
        );
    }

    /**
     * Add duplicate rows for one lookup hash to the migration map.
     *
     * @param string $lookup_hash Lookup hash.
     *
     * @return void
     */
    protected function map_gettext_original_duplicate_group( $lookup_hash ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        $identity_count = (int) $this->db->get_var(
            $this->db->prepare(
                "SELECT COUNT(*) FROM (
                    SELECT original, domain, COALESCE(NULLIF(context, ''), 'trp_context') AS normalized_context
                    FROM `$originals_table`
                    WHERE lookup_hash = %s
                    GROUP BY original, domain, normalized_context
                    LIMIT 2
                ) AS identity_groups",
                $lookup_hash
            )
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'checking duplicate gettext original hash identities' );

        if ( $identity_count < 2 ) {
            $this->map_single_identity_gettext_original_duplicate_group( $lookup_hash );
            return;
        }

        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT id, original, domain, context, original_plural, lookup_hash FROM `$originals_table` WHERE lookup_hash = %s ORDER BY id",
                $lookup_hash
            ),
            ARRAY_A
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting duplicate gettext originals for one hash' );

        if ( count( $rows ) < 2 ) {
            return;
        }

        $this->map_gettext_original_duplicate_rows( $lookup_hash, $rows );
    }

    /**
     * Map one duplicate hash group that contains a single normalized identity.
     *
     * @param string $lookup_hash Lookup hash.
     *
     * @return void
     */
    protected function map_single_identity_gettext_original_duplicate_group( $lookup_hash ) {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );
        $map_table       = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );
        $survivor_id     = (int) $this->db->get_var(
            $this->db->prepare(
                "SELECT id
                FROM `$originals_table`
                WHERE lookup_hash = %s
                ORDER BY CASE WHEN original_plural IS NOT NULL AND original_plural <> '' THEN 0 ELSE 1 END, id
                LIMIT 1",
                $lookup_hash
            )
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting gettext original duplicate survivor' );

        if ( $survivor_id <= 0 ) {
            return;
        }

        $this->db->query(
            $this->db->prepare(
                "INSERT IGNORE INTO `$map_table` (duplicate_id, survivor_id, lookup_hash, processed)
                SELECT id, %d, lookup_hash, 0
                FROM `$originals_table`
                WHERE lookup_hash = %s AND id <> %d",
                $survivor_id,
                $lookup_hash,
                $survivor_id
            )
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'building gettext originals deduplication map for one hash identity' );
    }

    /**
     * Add duplicate rows for one lookup hash row set to the migration map.
     *
     * @param string $lookup_hash Lookup hash.
     * @param array  $rows Duplicate candidate rows.
     *
     * @return void
     */
    protected function map_gettext_original_duplicate_rows( $lookup_hash, $rows ) {
        $map_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );

        if ( count( $rows ) < 2 ) {
            return;
        }

        $groups = array();
        foreach ( $rows as $row ) {
            $matched = false;

            foreach ( $groups as $group_key => $group ) {
                $first = $group[0];
                if (
                    $first['original'] === $row['original'] &&
                    $first['domain'] === $row['domain'] &&
                    $this->trp_query->normalize_gettext_original_context( $first['context'] ) === $this->trp_query->normalize_gettext_original_context( $row['context'] )
                ) {
                    $groups[ $group_key ][] = $row;
                    $matched = true;
                    break;
                }
            }

            if ( ! $matched ) {
                $groups[] = array( $row );
            }
        }

        foreach ( $groups as $group ) {
            if ( count( $group ) < 2 ) {
                continue;
            }

            $survivor = $this->choose_gettext_original_survivor( $group );
            $insert_rows = array();

            foreach ( $group as $row ) {
                if ( (int) $row['id'] === (int) $survivor['id'] ) {
                    continue;
                }

                $insert_rows[] = $this->db->prepare(
                    '(%d, %d, %s, 0)',
                    (int) $row['id'],
                    (int) $survivor['id'],
                    $lookup_hash
                );
            }

            if ( ! empty( $insert_rows ) ) {
                $this->db->query( "INSERT IGNORE INTO `$map_table` (duplicate_id, survivor_id, lookup_hash, processed) VALUES " . implode( ',', $insert_rows ) );
                $this->fail_gettext_original_lookup_hash_migration_on_error( 'building gettext originals deduplication map' );
            }
        }
    }

    /**
     * Choose a deterministic survivor for a duplicate originals group.
     *
     * @param array $group Duplicate rows.
     *
     * @return array
     */
    protected function choose_gettext_original_survivor( $group ) {
        usort( $group, function ( $a, $b ) {
            $a_has_plural = ! empty( $a['original_plural'] );
            $b_has_plural = ! empty( $b['original_plural'] );

            if ( $a_has_plural !== $b_has_plural ) {
                return $a_has_plural ? -1 : 1;
            }

            return (int) $a['id'] - (int) $b['id'];
        } );

        return reset( $group );
    }

    /**
     * Count gettext locale table references for original ids.
     *
     * @param array $ids Original ids.
     *
     * @return array
     */
    protected function get_gettext_original_reference_counts( $ids ) {
        $ids = array_filter( array_map( 'intval', $ids ) );

        if ( empty( $ids ) ) {
            return array();
        }

        $reference_counts = array_fill_keys( $ids, 0 );
        $ids_sql          = implode( ',', $ids );
        $gettext_tables   = $this->trp_query->get_all_gettext_table_names();

        foreach ( $gettext_tables as $table_name ) {
            $table_name = sanitize_text_field( $table_name );

            if ( ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ) {
                continue;
            }

            $rows       = $this->db->get_results(
                "SELECT original_id, COUNT(*) AS reference_count FROM `$table_name` WHERE original_id IN ($ids_sql) GROUP BY original_id",
                ARRAY_A
            );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'counting gettext original references' );

            foreach ( $rows as $row ) {
                $reference_counts[ (int) $row['original_id'] ] += (int) $row['reference_count'];
            }
        }

        return $reference_counts;
    }

    /**
     * Repoint references and delete duplicate originals from the dedupe map.
     *
     * @param int $batch_size Batch size.
     *
     * @return array Whether map rows remain and how many originals were deleted.
     */
    protected function process_gettext_originals_dedup_map( $batch_size ) {
        $map_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );

        if ( ! $this->trp_query->table_exists( $map_table, true ) ) {
            return array(
                'remaining' => false,
                'deleted'   => 0,
            );
        }

        $map_rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT duplicate_id, survivor_id FROM `$map_table` WHERE processed = 0 ORDER BY duplicate_id LIMIT %d",
                $batch_size
            ),
            ARRAY_A
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting gettext originals deduplication map rows' );

        if ( empty( $map_rows ) ) {
            return array(
                'remaining' => false,
                'deleted'   => 0,
            );
        }

        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );
        $meta_table      = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_meta() );
        $gettext_tables  = $this->trp_query->get_all_gettext_table_names();
        $duplicate_ids   = array_map( 'intval', wp_list_pluck( $map_rows, 'duplicate_id' ) );
        $duplicate_sql   = implode( ',', $duplicate_ids );

        foreach ( $gettext_tables as $table_name ) {
            $table_name = sanitize_text_field( $table_name );

            if ( ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ) {
                continue;
            }

            $this->prepare_gettext_locale_rows_for_original_deduplication( $table_name, $map_table, $originals_table, $duplicate_sql );

            $this->db->query(
                "UPDATE `$table_name` AS tt
                INNER JOIN `$map_table` AS map ON tt.original_id = map.duplicate_id
                SET tt.original_id = map.survivor_id
                WHERE map.processed = 0 AND map.duplicate_id IN ($duplicate_sql)"
            );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'repointing gettext locale table original ids' );
        }

        if ( $this->trp_query->table_exists( $meta_table ) ) {
            $this->db->query(
                "UPDATE `$meta_table` AS meta
                INNER JOIN `$map_table` AS map ON meta.original_id = map.duplicate_id
                SET meta.original_id = map.survivor_id
                WHERE map.processed = 0 AND map.duplicate_id IN ($duplicate_sql)"
            );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'repointing gettext original meta ids' );
        }

        $this->db->query( "DELETE FROM `$originals_table` WHERE id IN ($duplicate_sql)" );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'deleting duplicate gettext originals' );
        $deleted = (int) $this->db->rows_affected;

        $this->db->query( "UPDATE `$map_table` SET processed = 1 WHERE duplicate_id IN ($duplicate_sql)" );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'marking gettext originals deduplication map rows processed' );

        return array(
            'remaining' => count( $map_rows ) === $batch_size,
            'deleted'   => $deleted,
        );
    }

    /**
     * Repair locale rows that would make duplicate-original repointing unsafe.
     *
     * Locale tables may already contain stale original_id values. If the locale
     * row's stored original/domain no longer matches the duplicate original row,
     * leave it for the locale backfill phase by nulling original_id. For valid
     * references, collapse rows that would map to the same survivor/plural pair
     * before the bulk UPDATE runs so existing unique indexes are not violated.
     *
     * @param string $table_name      Gettext locale table.
     * @param string $map_table       Duplicate-original map table.
     * @param string $originals_table Gettext originals table.
     * @param string $duplicate_sql   Comma-separated duplicate ids for this batch.
     *
     * @return void
     */
    protected function prepare_gettext_locale_rows_for_original_deduplication( $table_name, $map_table, $originals_table, $duplicate_sql ) {
        if ( $this->trp_query->table_column_exists( $table_name, 'original' ) ) {
            $domain_mismatch_sql = '';

            if ( $this->trp_query->table_column_exists( $table_name, 'domain' ) ) {
                $domain_mismatch_sql = " OR BINARY COALESCE(tt.domain, '') <> BINARY COALESCE(dup.domain, '')";
            }

            $this->db->query(
                "UPDATE `$table_name` AS tt
                INNER JOIN `$map_table` AS map ON tt.original_id = map.duplicate_id
                INNER JOIN `$originals_table` AS dup ON dup.id = map.duplicate_id
                SET tt.original_id = NULL
                WHERE map.processed = 0
                    AND map.duplicate_id IN ($duplicate_sql)
                    AND tt.original <> ''
                    AND (BINARY tt.original <> BINARY dup.original$domain_mismatch_sql)"
            );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'clearing stale gettext locale original ids before original deduplication' );
        }

        $rows_to_repoint = $this->db->get_results(
            "SELECT tt.id, tt.translated, tt.status, COALESCE(tt.plural_form, 0) AS plural_form, map.survivor_id
            FROM `$table_name` AS tt
            INNER JOIN `$map_table` AS map ON tt.original_id = map.duplicate_id
            WHERE map.processed = 0 AND map.duplicate_id IN ($duplicate_sql)
            ORDER BY map.survivor_id, plural_form, tt.id",
            ARRAY_A
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting gettext locale rows before original deduplication' );

        if ( empty( $rows_to_repoint ) ) {
            return;
        }

        $survivor_ids = array_values( array_unique( array_map( 'intval', wp_list_pluck( $rows_to_repoint, 'survivor_id' ) ) ) );
        $survivor_sql = implode( ',', $survivor_ids );
        $existing_rows = $this->db->get_results(
            "SELECT tt.id, tt.translated, tt.status, COALESCE(tt.plural_form, 0) AS plural_form, tt.original_id AS survivor_id
            FROM `$table_name` AS tt
            WHERE tt.original_id IN ($survivor_sql)",
            ARRAY_A
        );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'selecting gettext locale survivor rows before original deduplication' );

        $groups = array();

        foreach ( $rows_to_repoint as $row ) {
            $row['is_existing_survivor'] = false;
            $group_key = (int) $row['survivor_id'] . ':' . (int) $row['plural_form'];
            $groups[ $group_key ][] = $row;
        }

        foreach ( $existing_rows as $row ) {
            $row['is_existing_survivor'] = true;
            $group_key = (int) $row['survivor_id'] . ':' . (int) $row['plural_form'];
            $groups[ $group_key ][] = $row;
        }

        foreach ( $groups as $group ) {
            if ( count( $group ) < 2 ) {
                continue;
            }

            $this->merge_gettext_locale_original_deduplication_group( $table_name, $group );
        }
    }

    /**
     * Merge locale rows that would collapse to one survivor original/plural pair.
     *
     * @param string $table_name Gettext locale table.
     * @param array  $group      Locale rows in one survivor/plural group.
     *
     * @return void
     */
    protected function merge_gettext_locale_original_deduplication_group( $table_name, $group ) {
        $best_row      = $this->choose_gettext_locale_row_survivor( $group );
        $existing_rows = array_values(
            array_filter(
                $group,
                function ( $row ) {
                    return ! empty( $row['is_existing_survivor'] );
                }
            )
        );

        if ( ! empty( $existing_rows ) && empty( $best_row['is_existing_survivor'] ) ) {
            $target_row = $this->choose_gettext_locale_row_survivor( $existing_rows );
            $this->db->update(
                $table_name,
                array(
                    'translated' => isset( $best_row['translated'] ) ? $best_row['translated'] : '',
                    'status'     => isset( $best_row['status'] ) ? (int) $best_row['status'] : TRP_Query::NOT_TRANSLATED,
                ),
                array( 'id' => (int) $target_row['id'] ),
                array( '%s', '%d' ),
                array( '%d' )
            );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'copying gettext locale survivor translation before original deduplication' );
        }

        $delete_ids = array();
        foreach ( $group as $row ) {
            if ( ! empty( $row['is_existing_survivor'] ) ) {
                continue;
            }

            if ( empty( $existing_rows ) && (int) $row['id'] === (int) $best_row['id'] ) {
                continue;
            }

            $delete_ids[] = (int) $row['id'];
        }

        if ( empty( $delete_ids ) ) {
            return;
        }

        $delete_sql = implode( ',', array_unique( $delete_ids ) );
        $this->db->query( "DELETE FROM `$table_name` WHERE id IN ($delete_sql)" );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'deleting duplicate gettext locale rows before original deduplication' );
    }

    /**
     * Choose the best locale row to keep when duplicate originals collapse.
     *
     * @param array $rows Locale rows.
     *
     * @return array
     */
    protected function choose_gettext_locale_row_survivor( $rows ) {
        usort( $rows, function ( $a, $b ) {
            $a_score = $this->get_gettext_locale_row_score( $a );
            $b_score = $this->get_gettext_locale_row_score( $b );

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
     * @param array $row Locale row.
     *
     * @return int
     */
    protected function get_gettext_locale_row_score( $row ) {
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
     * Verify that the hash migration can safely add the unique index.
     *
     * @return bool|string True when verified, or the phase to rerun.
     */
    protected function verify_gettext_original_lookup_hash_migration() {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );
        $map_table       = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );

        $null_hashes = (int) $this->db->get_var( "SELECT COUNT(*) FROM `$originals_table` WHERE lookup_hash IS NULL" );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'verifying gettext original null lookup hashes' );

        if ( $null_hashes > 0 ) {
            return 'backfill';
        }

        $duplicate_hashes = (int) $this->db->get_var( "SELECT COUNT(*) FROM (SELECT lookup_hash FROM `$originals_table` GROUP BY lookup_hash HAVING COUNT(*) > 1) AS duplicate_hash_groups" );
        $this->fail_gettext_original_lookup_hash_migration_on_error( 'verifying gettext original duplicate lookup hashes' );

        if ( $duplicate_hashes > 0 ) {
            return 'build_map';
        }

        if ( $this->trp_query->table_exists( $map_table, true ) ) {
            $unprocessed = (int) $this->db->get_var( "SELECT COUNT(*) FROM `$map_table` WHERE processed = 0" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'verifying unprocessed gettext originals deduplication map rows' );

            if ( $unprocessed > 0 ) {
                return 'process_map';
            }

            $this->verify_gettext_original_duplicate_references_removed();
        }

        return true;
    }

    /**
     * Verify no gettext table still references mapped duplicate original ids.
     *
     * @return void
     */
    protected function verify_gettext_original_duplicate_references_removed() {
        $map_table      = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );
        $meta_table     = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_meta() );
        $gettext_tables = $this->trp_query->get_all_gettext_table_names();

        foreach ( $gettext_tables as $table_name ) {
            $table_name  = sanitize_text_field( $table_name );

            if ( ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) ) {
                continue;
            }

            $references  = (int) $this->db->get_var( "SELECT COUNT(*) FROM `$table_name` AS tt INNER JOIN `$map_table` AS map ON tt.original_id = map.duplicate_id" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'verifying gettext locale duplicate references were removed' );

            if ( $references > 0 ) {
                $this->fail_gettext_original_lookup_hash_migration(
                    sprintf(
                        __( 'Update aborted! Table %1$s still references %2$d duplicate gettext original ids.', 'translatepress-multilingual' ),
                        esc_html( $table_name ),
                        $references
                    )
                );
            }
        }

        if ( $this->trp_query->table_exists( $meta_table ) ) {
            $references = (int) $this->db->get_var( "SELECT COUNT(*) FROM `$meta_table` AS meta INNER JOIN `$map_table` AS map ON meta.original_id = map.duplicate_id" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'verifying gettext original meta duplicate references were removed' );

            if ( $references > 0 ) {
                $this->fail_gettext_original_lookup_hash_migration(
                    sprintf(
                        __( 'Update aborted! Gettext original meta still references %d duplicate original ids.', 'translatepress-multilingual' ),
                        $references
                    )
                );
            }
        }
    }

    /**
     * Make lookup_hash strict and add its unique index.
     *
     * @return void
     */
    protected function finalize_gettext_original_lookup_hash_schema() {
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        if ( ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' ) ) {
            $this->db->query( "ALTER TABLE `$originals_table` MODIFY lookup_hash CHAR(32) NOT NULL" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'making gettext original lookup_hash not null' );

            if ( $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash' ) ) {
                $this->db->query( "ALTER TABLE `$originals_table` DROP INDEX gettext_lookup_hash" );

                if ( $this->db->last_error !== '' ) {
                    if ( strpos( $this->db->last_error, "Can't DROP INDEX" ) === false ) {
                        $this->fail_gettext_original_lookup_hash_migration_on_error( 'dropping temporary gettext original lookup_hash index' );
                    }

                    $this->db->last_error = '';
                }
            }

            if ( ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' ) ) {
                $this->db->query( "CREATE UNIQUE INDEX gettext_lookup_hash_unique ON `$originals_table` (lookup_hash)" );
                $this->fail_gettext_original_lookup_hash_migration_on_error( 'creating gettext original lookup_hash unique index' );
            }
        }
    }

    /**
     * Drop temporary migration state after successful verification/indexing.
     *
     * @return void
     */
    protected function cleanup_gettext_original_lookup_hash_migration() {
        $map_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_originals_dedup_map() );

        if ( $this->trp_query->table_exists( $map_table, true ) ) {
            $this->db->query( "DROP TABLE `$map_table`" );
            $this->fail_gettext_original_lookup_hash_migration_on_error( 'dropping gettext originals deduplication map' );
        }
    }

    /**
     * Abort migration immediately if the last DB query failed.
     *
     * @param string $phase Human-readable phase.
     *
     * @return void
     */
    protected function fail_gettext_original_lookup_hash_migration_on_error( $phase ) {
        if ( $this->db->last_error === '' ) {
            return;
        }

        $this->fail_gettext_original_lookup_hash_migration(
            sprintf(
                __( 'Update aborted while %1$s. SQL error: %2$s', 'translatepress-multilingual' ),
                esc_html( $phase ),
                esc_html( $this->db->last_error )
            )
        );
    }

    /**
     * Fail the gettext lookup hash migration without terminating background requests.
     *
     * @param string $error_message Error message.
     *
     * @return void
     * @throws Exception
     */
    protected function fail_gettext_original_lookup_hash_migration( $error_message ) {
        throw new Exception( esc_html( wp_strip_all_tags( $error_message ) ) );
    }

}
