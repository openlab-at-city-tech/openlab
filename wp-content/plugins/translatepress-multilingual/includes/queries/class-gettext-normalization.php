<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Gettext_Normalization
 *
 * Queries for transitioning to normalized gettext table structure
 *
 * To access this component use:
 * 		$trp = TRP_Translate_Press::get_trp_instance();
 *      $trp_query = $trp->get_component( 'query' );
 *      $gettext_normalization = $trp_query->get_query_component('gettext_normalization');
 *
 */
class TRP_Gettext_Normalization extends TRP_Query {

    public $db;
    protected $settings;
    protected $error_manager;

    /**
     * TRP_Query constructor.
     * @param $settings
     */
    public function __construct( $settings ){
        global $wpdb;
        $this->db = $wpdb;
        $this->settings = $settings;
    }


	/**
	 * Add the gettext normalization columns to gettext tables when missing.
	 *
	 * Affects all existing gettext tables, including deactivated languages, unless
	 * a specific language code is provided.
	 *
	 * @param string|null $language_code Optional language code. When empty, all gettext tables are checked.
	 *
	 * @return void
	 */
	public function check_for_gettext_original_id_column($language_code = null){
		if ( $language_code ){
			// check only this language
			$array_of_table_names = array( $this->get_gettext_table_name( $language_code ) );
		}else {
			// check all languages, including deactivated ones
			$array_of_table_names = $this->get_all_gettext_table_names();
		}

		foreach( $array_of_table_names as $table_name ){
			if ( ! $this->table_column_exists( $table_name, 'original_id' ) ) {
				$this->db->query("ALTER TABLE " . $table_name . " ADD original_id BIGINT(20) DEFAULT NULL" );
			}
			if ( ! $this->table_column_exists( $table_name, 'plural_form' ) ) {
				$this->db->query("ALTER TABLE " . $table_name . " ADD plural_form INT(20) DEFAULT NULL" );
			}
		}
	}

	/**
	 * Add the composite lookup index used to resolve gettext originals by
	 * original, domain and context.
	 *
	 * The deferred runtime lookup starts from the shared gettext originals table.
	 * Indexing the three lookup columns together avoids scanning rows that share
	 * the same original prefix but belong to other domains or contexts.
	 *
	 * @return void
	 */
	public function check_for_gettext_original_lookup_index() {
		$table_name = sanitize_text_field( $this->get_table_name_for_gettext_original_strings() );

		if ( empty( $table_name ) || ! $this->table_exists( $table_name ) ) {
			return;
		}

		if ( ! $this->table_index_exists( $table_name, 'gettext_lookup_original_domain_context' ) ) {
			$prefix_length = $this->get_gettext_original_lookup_index_prefix_length( $table_name );
			$this->db->query( "CREATE INDEX gettext_lookup_original_domain_context ON `" . $table_name . "` (original($prefix_length), domain($prefix_length), context($prefix_length))" );
		}
	}

	/**
	 * Check whether a database table index exists.
	 *
	 * @param string $table_name Database table name.
	 * @param string $index_name Database index name.
	 *
	 * @return bool
	 */
	public function table_index_exists( $table_name, $index_name ) {
		$table_name = sanitize_text_field( $table_name );
		$index_name = sanitize_text_field( $index_name );

		if ( empty( $table_name ) || empty( $index_name ) ) {
			return false;
		}

		$index = $this->db->get_results(
			$this->db->prepare(
				"SHOW INDEX FROM `" . $table_name . "` WHERE Key_name = %s",
				$index_name
			)
		);

		return ! empty( $index );
	}

    /**
     * Function that takes care of inserting original strings from gettext to gettext_original_strings table
     */
    public function gettext_original_ids_insert( $language_code, $inferior_limit, $batch_size ){

        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_gettext_original_strings();
        $table_name = sanitize_text_field( $this->get_gettext_table_name( $language_code ) );
		$insert_columns = 'original, domain, context';
		$select_columns = "DISTINCT ( BINARY t1.original ), t1.domain, 'trp_context'";

		/*
		 * The lookup-hash migration can finish before this legacy updater runs.
		 * Once lookup_hash is NOT NULL and unique, omitting it makes every row use
		 * the same implicit empty value and aborts multi-row inserts. Populate the
		 * canonical identity hash whenever the column is present, including while
		 * the hash migration is still in its nullable intermediate state.
		 */
		if ( $this->table_column_exists( $originals_table, $this->get_gettext_original_lookup_hash_column_name() ) ) {
			$connection_charset      = isset( $this->db->charset ) ? (string) $this->db->charset : '';
			$target_original_charset = $this->get_database_column_character_set( $originals_table, 'original' );
			$target_domain_charset   = $this->get_database_column_character_set( $originals_table, 'domain' );

			if (
				$this->db->last_error !== '' ||
				! preg_match( '/^[a-zA-Z0-9_]+$/', $connection_charset ) ||
				! preg_match( '/^[a-zA-Z0-9_]+$/', $target_original_charset ) ||
				! preg_match( '/^[a-zA-Z0-9_]+$/', $target_domain_charset )
			) {
				if ( $this->db->last_error === '' ) {
					$this->db->last_error = 'Could not determine safe character sets for the gettext original lookup hash.';
				}
				$this->record_error_preserving_last_error( 'last_error_insert_gettext_original_strings' );
				return false;
			}

			$insert_columns .= ', lookup_hash';
			$select_columns .= ", MD5(CONCAT(CONVERT(CONVERT(BINARY t1.original USING $target_original_charset) USING $connection_charset), 0x1F, CONVERT(CONVERT(t1.domain USING $target_domain_charset) USING $connection_charset), 0x1F, 'trp_context'))";
		}

        /*
        *  select all string that are in the dictionary table and are not in the original tables and insert them in the original
        */
        $insert_records = $this->db->query(
            $this->db->prepare(
                "INSERT INTO `$originals_table` ($insert_columns) SELECT $select_columns FROM `$table_name` t1 LEFT JOIN `$originals_table` t2 ON ( t2.original = BINARY t1.original AND t2.domain = BINARY t1.domain AND COALESCE( NULLIF( t2.context, '' ), 'trp_context' ) = 'trp_context' ) WHERE t2.id IS NULL AND t1.domain != '' AND t1.original != '' AND t1.id > %d AND t1.id <= %d AND LENGTH(t1.original) < 20000",
                $inferior_limit,
                ( $inferior_limit + $batch_size )
            )
        );

        $this->record_error_preserving_last_error( 'last_error_insert_gettext_original_strings' );

        return $insert_records;

    }

    /**
     * Function that makes sure we don't have duplicates in gettext_original_strings table
     * It is executed after we have inserted all the strings
     *
     * @return int|false Number of deleted rows, or false on error.
     */
    public function gettext_original_ids_cleanup(){
        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_gettext_original_strings();
        // Plain equality allows index lookups in either join order; binary casts keep matching byte-exact.
        $result = $this->db->query(
            "DELETE t1 FROM `$originals_table` t1 INNER JOIN `$originals_table` t2 WHERE t1.id > t2.id AND t1.original = t2.original AND CAST(t1.original AS BINARY) = CAST(t2.original AS BINARY) AND t1.domain = BINARY t2.domain AND COALESCE( NULLIF( t1.context, '' ), 'trp_context' ) = BINARY COALESCE( NULLIF( t2.context, '' ), 'trp_context' )"
        );

        $this->record_error_preserving_last_error( 'last_error_cleaning_gettext_original_strings' );

        return $result;
    }

    /**
     * Function that takes care of synchronizing the gettext with the gettext original table by inserting the original
     * ids in the original_id column
     */
    public function gettext_original_ids_reindex( $language_code, $inferior_limit, $batch_size ){
        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_gettext_original_strings();
        $table_name = sanitize_text_field( $this->get_gettext_table_name( $language_code ) );
        $source_original_charset = $this->get_database_column_character_set( $table_name, 'original' );

        if ( $this->db->last_error !== '' ) {
            $this->record_error_preserving_last_error( 'last_error_reindex_gettext_original_ids' );
            return false;
        }

        $source_domain_charset   = $this->get_database_column_character_set( $table_name, 'domain' );

        if ( $this->db->last_error !== '' ) {
            $this->record_error_preserving_last_error( 'last_error_reindex_gettext_original_ids' );
            return false;
        }

        $target_original_charset = $this->get_database_column_character_set( $originals_table, 'original' );

        if ( $this->db->last_error !== '' ) {
            $this->record_error_preserving_last_error( 'last_error_reindex_gettext_original_ids' );
            return false;
        }

        $target_domain_charset   = $this->get_database_column_character_set( $originals_table, 'domain' );

        if ( $this->db->last_error !== '' ) {
            $this->record_error_preserving_last_error( 'last_error_reindex_gettext_original_ids' );
            return false;
        }
        $character_sets_differ =
            ( $source_original_charset !== '' && $target_original_charset !== '' && strcasecmp( $source_original_charset, $target_original_charset ) !== 0 ) ||
            ( $source_domain_charset !== '' && $target_domain_charset !== '' && strcasecmp( $source_domain_charset, $target_domain_charset ) !== 0 );

        if ( $character_sets_differ ) {
            // Convert only locale values so the originals index remains usable. Converting
            // back plus BINARY rejects lossy matches, e.g. utf8mb4 "€" -> latin1 "?".
            $identity_condition = "originals.original = BINARY CONVERT(locale_strings.original USING $target_original_charset)
                AND originals.domain = BINARY CONVERT(locale_strings.domain USING $target_domain_charset)
                AND BINARY locale_strings.original = BINARY CONVERT(CONVERT(locale_strings.original USING $target_original_charset) USING $source_original_charset)
                AND BINARY locale_strings.domain = BINARY CONVERT(CONVERT(locale_strings.domain USING $target_domain_charset) USING $source_domain_charset)";
            // Validate an existing reference against the converted original and domain.
            $current_original_condition = "current_original.original != BINARY CONVERT(locale_strings.original USING $target_original_charset)
                OR current_original.domain != BINARY CONVERT(locale_strings.domain USING $target_domain_charset)";
        } else {
            $identity_condition = "originals.original = BINARY locale_strings.original
                AND originals.domain = BINARY locale_strings.domain";
            $current_original_condition = "current_original.original != BINARY locale_strings.original
                OR current_original.domain != BINARY locale_strings.domain";
        }

        /*
        *  perform a UPDATE JOIN with the original table https://www.mysqltutorial.org/mysql-update-join/
        */
        $update_records = $this->db->query(
            $this->db->prepare(
                "UPDATE `$table_name` AS locale_strings INNER JOIN `$originals_table` AS originals ON $identity_condition AND COALESCE( NULLIF( originals.context, '' ), 'trp_context' ) = 'trp_context' LEFT JOIN `$originals_table` AS current_original ON current_original.id = locale_strings.original_id SET locale_strings.original_id = originals.id WHERE ( locale_strings.original_id IS NULL OR locale_strings.original_id = 0 OR current_original.id IS NULL OR $current_original_condition ) AND locale_strings.id > %d AND locale_strings.id <= %d",
                $inferior_limit,
                ( $inferior_limit + $batch_size )
            )
        );

        if ( $this->db->last_error !== '' ) {
            $this->record_error_preserving_last_error( 'last_error_reindex_gettext_original_ids' );
            return false;
        }

        return $update_records;
    }


}
