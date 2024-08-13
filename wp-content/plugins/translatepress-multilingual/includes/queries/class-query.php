<?php

/**
 * Class TRP_Query
 *
 * Queries for translations in custom trp tables.
 *
 */
class TRP_Query{

    protected $table_name;
    public $db;
    protected $settings;
    protected $url_converter;
    protected $translation_render;
    protected $error_manager;
    protected $check_invalid_text;
    protected $tables_exist = array();
    protected $db_sql_version = null;
    protected $gettext_normalized = null;

    /* gettext query components */
    protected $gettext_table_creation;
    protected $gettext_normalization;
    protected $gettext_insert_update;

    const NOT_TRANSLATED = 0;
    const MACHINE_TRANSLATED = 1;
    const HUMAN_REVIEWED = 2;
    const SIMILAR_TRANSLATED = 3;
    const BLOCK_TYPE_REGULAR_STRING = 0;
    const BLOCK_TYPE_ACTIVE = 1;
    const BLOCK_TYPE_DEPRECATED = 2;

    /**
     * TRP_Query constructor.
     * @param $settings
     */
    public function __construct( $settings ){
        global $wpdb;
        $this->db = $wpdb;
        $this->settings = $settings;

        $this->gettext_normalization = new TRP_Gettext_Normalization($settings);
        $this->gettext_table_creation = new TRP_Gettext_Table_Creation($settings);
        $this->gettext_insert_update = new TRP_Gettext_Insert_Update($settings);
    }

    public function get_query_component( $component ){
        return $this->$component;
    }


	/**
	 * Return an array of all the active translation blocks
	 *
	 * @param $language_code
	 *
	 * @return array|null|object
	 */
    public function get_all_translation_blocks( $language_code ){
        if ( apply_filters( 'trp_enable_translation_blocks_querying', true ) ) {
            $cache_key = 'get_all_translation_blocks_' . md5( $language_code );
            $dictionary = wp_cache_get( $cache_key, 'trp' );

            if ( false === $dictionary ) {
                $query      = "SELECT original, id, block_type, status FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE block_type = " . self::BLOCK_TYPE_ACTIVE . " OR block_type = " . self::BLOCK_TYPE_DEPRECATED;
                $dictionary = $this->db->get_results( $query, OBJECT_K );
                wp_cache_set( $cache_key, $dictionary, 'trp' );
            }
        }else{
            $dictionary = array();
        }
	    return $dictionary;
    }

    /**
     * Returns the translations for the provided strings.
     *
     * Only returns results where there actually is a translation ( != NOT_TRANSLATED )
     *
     * @param array $strings_array      Array of original strings to search for.
     * @param string $language_code     Language code to query for.
     * @return object                   Associative Array of objects with translations where key is original string.
     */
    public function get_existing_translations( $strings_array, $language_code, $block_type = null ){
        if ( !is_array( $strings_array ) || count ( $strings_array ) == 0 || !in_array( $language_code, $this->settings['translation-languages'] ) || $language_code === $this->settings['default-language'] ){
            return array();
        }
        if ( $block_type == null ){
	        $and_block_type = "";
        }else {
	        $and_block_type = " AND block_type = " . $block_type;
        }
        $query = "SELECT original,translated, status FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE status != " . self::NOT_TRANSLATED . $and_block_type . " AND translated <>'' AND original IN ";

        $placeholders = array();
        $values = array();
        foreach( $strings_array as $string ){
            $placeholders[] = '%s';
            $values[] = $string;
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
	    $prepared_query = $this->db->prepare( $query, $values );
        $dictionary = $this->db->get_results( $prepared_query, OBJECT_K  );



        if( !$this->check_invalid_text ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->check_invalid_text = $trp->get_component( 'check_invalid_text' );
        }
        $dictionary = $this->check_invalid_text->get_existing_translations_without_invalid_text($dictionary, $prepared_query, $strings_array, $language_code, $block_type );

        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_existing_translations()' ) );

        if ($this->db->last_error !== '' && !$this->check_invalid_text->is_invalid_data_error())
            $dictionary = false;

        $dictionary = apply_filters( 'trp_get_existing_translations', $dictionary, $prepared_query, $strings_array, $language_code, $block_type );
        if ( is_array( $dictionary ) && count( $dictionary ) === 0 && !$this->table_exists($this->get_table_name( $language_code )) && !$this->check_invalid_text->is_invalid_data_error()){
            // if table is missing then last_error is empty for the select query
            $this->maybe_record_automatic_translation_error(array( 'details' => 'Missing table ' . $this->get_table_name( $language_code ) . ' . To regenerate tables, try going to Settings->TranslatePress->General tab and Save Settings.'), true );
        }

        return $dictionary;
    }

    /**
     * Return constant used for entries without translations.
     *
     * @return int
     */
    public function get_constant_not_translated(){
        return self::NOT_TRANSLATED;
    }

    /**
     * Return constant used for entries with machine translation.
     *
     * @return int
     */
    public function get_constant_machine_translated(){
        return self::MACHINE_TRANSLATED;
    }

    /**
     * Return constant used for entries edited by humans.
     *
     * @return int
     */
    public function get_constant_human_reviewed(){
        return self::HUMAN_REVIEWED;
    }

    /**
     * Return constant used for entries automatically filled by the original being a string similar to another string that has a translation.
     *
     * @return int
     */
    public function get_constant_similar_translated(){
        return self::SIMILAR_TRANSLATED;
    }

	/**
	 * Return constant used for individual strings, not part of a translation block
	 *
	 * @return int
	 */
	public function get_constant_block_type_regular_string(){
		return self::BLOCK_TYPE_REGULAR_STRING;
	}

	/**
	 * Return constant used for a translation block
	 *
	 * @return int
	 */
	public function get_constant_block_type_active(){
		return self::BLOCK_TYPE_ACTIVE;
	}

	/**
	 * Return constant used for a translation block, no longer in use (i.e. after being split )
	 *
	 * @return int
	 */
	public function get_constant_block_type_deprecated(){
		return self::BLOCK_TYPE_DEPRECATED;
	}

	/**
     * Check if table for specific language exists.
     *
     * If the table does not exists it is created.
     *
     * @param string $language_code
     */
    public function check_table( $default_language, $language_code ){
        $table_name = sanitize_text_field( $this->get_table_name( $language_code, $default_language ) );
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `" . $table_name . "`(
                                    id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    original  longtext NOT NULL,
                                    translated  longtext,
                                    status int(20) DEFAULT " . $this::NOT_TRANSLATED .",
                                    block_type int(20) DEFAULT " . $this::BLOCK_TYPE_REGULAR_STRING .",
                                    original_id bigint(20) DEFAULT NULL,
                                    UNIQUE KEY id (id) )
                                     $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $sql_index = "CREATE INDEX index_name ON `" . $table_name . "` (original(100));";
            $this->db->query( $sql_index );

            // added index on block_type for performance improvement when creating a new dictionary table
            // for existing tables a function was added in class-upgrade on version 2.7.4
            $sql_index_block_type = "CREATE INDEX block_type ON `" . $table_name . "` (block_type);";
            $this->db->query( $sql_index_block_type );

            $this->maybe_record_automatic_translation_error(array( 'details' => 'Error creating regular tables' ) );

            if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
                // table still doesn't exist after creation
                $this->maybe_record_automatic_translation_error(array( 'details' => 'Error creating regular strings tables' ), true );
            }else {
                // full text index for original
                $sql_index = "CREATE FULLTEXT INDEX original_fulltext ON `" . $table_name . "`(original);";
                $this->db->query( $sql_index );

                //syncronize all translation blocks.
                $this->copy_all_translation_blocks_into_table($default_language, $language_code);
            }
        }else{
	        $this->check_for_block_type_column( $language_code, $default_language );
	        $this->check_for_original_id_column( $language_code, $default_language );
        }
    }

    /**
     * Check if table for machine translation logs exists.
     *
     * If the table does not exists it is created.
     *
     * @param string $language_code
     */
    public function check_machine_translation_log_table(){
        $table_name = $this->db->prefix . 'trp_machine_translation_log';
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )
        {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `{$table_name}`(
                                    id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    url text,
                                    timestamp datetime DEFAULT '0000-00-00 00:00:00',
                                    strings longtext,
                                    characters text,
                                    response longtext,
                                    lang_source text,
                                    lang_target text,
                                    UNIQUE KEY id (id) )
                                     {$charset_collate};";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $this->maybe_record_automatic_translation_error(array( 'details' => 'Error creating machine translation log tables' ) );

            if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )
            {
                $this->maybe_record_automatic_translation_error(array( 'details' => 'Error creating machine translation log tables' ), true );
                // something failed. Table still doesn't exist.
                return false;
            }
            // table exists
            return true;
        }
        //table exists
        return true;
    }

    public function copy_all_translation_blocks_into_table( $default_language, $language_code ){
    	$all_table_names = $this->get_all_table_names( $default_language, array( $language_code ) );
    	if ( count( $all_table_names ) > 0 ){
		    $source_table_name = $all_table_names[0];

		    // copy translation blocks from table name of this language
		    $source_language = apply_filters( 'trp_source_language_translation_blocks', '', $default_language, $language_code );
		    if ( $source_language != '' ){
			    $source_table_name = $this->get_table_name( $source_language, $default_language );
		    }

		    $destination_table_name = $this->get_table_name( $language_code, $default_language );

		    // get all tb from $source_table_name and copy to $destination_table_name
		    $sql = 'INSERT INTO `' . $destination_table_name . '` (id, original, translated, status, block_type) SELECT NULL, original, "", ' . $this::NOT_TRANSLATED . ', block_type FROM `' . $source_table_name . '` WHERE block_type = ' . self::BLOCK_TYPE_ACTIVE . ' OR block_type = ' . self::BLOCK_TYPE_DEPRECATED;
		    $this->db->query( $sql );
	    }
    }

    /**
     * Check if the original string table exists
     *
     * If the table does not exists it is created.
     *
     * @since   1.6.6
     */
    public function check_original_table(){

        $table_name = $this->get_table_name_for_original_strings();
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `" . $table_name . "`(
                                    id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    original TEXT NOT NULL )
                                     $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $sql_index = "CREATE INDEX index_original ON `" . $table_name . "` (original(100));";
            $this->db->query( $sql_index );
        }
    }

    /**
     * Function that takes care of inserting  original strings from dictionary to original_strings table when updating to version 1.6.6
     */
    public function original_ids_insert( $language_code, $inferior_limit, $batch_size ){

        //don't do anything for default language
        if ( $this->settings['default-language'] === $language_code )
            return 0;

        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_original_strings();
        $table_name = sanitize_text_field( $this->get_table_name( $language_code, $this->settings['default-language'] ) );

        /*
        *  select all string that are in the dictionary table and are not in the original tables and insert them in the original
        */
        $insert_records = $this->db->query( $this->db->prepare( "INSERT INTO `$originals_table` (original) SELECT DISTINCT ( BINARY t1.original ) FROM `$table_name` t1 LEFT JOIN `$originals_table` t2 ON ( t2.original = t1.original AND t2.original = BINARY t1.original ) WHERE t2.original IS NULL AND t1.id > %d AND t1.id <= %d AND LENGTH(t1.original) < 20000", $inferior_limit, ($inferior_limit + $batch_size) ) );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_insert_original_strings' => $this->db->last_error));
        }

        return $insert_records;

    }

    /**
     * Function that makes sure we don't have duplicates in original_strings table when updating to version 1.6.6
     * It is executed after we inserted all the strings
     */
    public function original_ids_cleanup(){
        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_original_strings();
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        $this->db->query( "DELETE t1 FROM `$originals_table` t1 INNER JOIN `$originals_table` t2 WHERE t1.id > t2.id AND t1.original COLLATE ".$charset."_bin = t2.original" );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_cleaning_original_strings' => $this->db->last_error));
        }

    }

    /**
     * Function that takes care of synchronizing the dictionaries with the original table by inserting the original ids in the original_id column
     */
    public function original_ids_reindex( $language_code, $inferior_limit, $batch_size ){

        //don't do anything for default language
        if ( $this->settings['default-language'] === $language_code )
            return 0;

        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_original_strings();
        $table_name = sanitize_text_field( $this->get_table_name( $language_code, $this->settings['default-language'] ) );
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        /*
        *  perform a UPDATE JOIN with the original table https://www.mysqltutorial.org/mysql-update-join/
        */
        $update_records = $this->db->query( $this->db->prepare( "UPDATE $table_name, $originals_table SET $table_name.original_id = $originals_table.id WHERE $table_name.original COLLATE ". $charset ."_bin = $originals_table.original AND $table_name.id > %d AND $table_name.id <= %d", $inferior_limit, ($inferior_limit + $batch_size) ) );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_reindex_original_ids' => $this->db->last_error));
        }

        return $update_records;
    }

    /**
     * Function that makes sure that when new strings are inserted in dictionaries they are also inserted in original_strings table if they don't exist
     * @param $language_code
     * @param $new_strings
     * @return array|object|null
     */
    public function original_strings_sync( $language_code, $new_strings ){
        if ( count($new_strings ) === 0 ){
            return array();
        }
        if ( $this->settings['default-language'] != $language_code ) {

            $originals_table = $this->get_table_name_for_original_strings();

            $possible_new_strings = array();
            foreach ( $new_strings as $string ) {
                $possible_new_strings[] = $this->db->prepare( "%s",  $string );
            }

            $existing_strings = $this->db->get_results( "SELECT original FROM `$originals_table` WHERE BINARY $originals_table.original IN (".implode( ',', $possible_new_strings ).")", OBJECT_K );

            if( !empty( $existing_strings ) ){
                $existing_strings = array_keys($existing_strings);
                $insert_strings = array_diff( $new_strings, $existing_strings );
            }
            else{
                $insert_strings = $new_strings;
            }

            foreach ( $insert_strings as $k => $string ) {
                $insert_strings[$k] = $this->db->prepare( "(%s)",  $string );
            }

            if( !empty( $insert_strings ) ) {
                //insert the strings that are missing
                $this->db->query("INSERT INTO `$originals_table` (original) VALUES " . implode(',', $insert_strings));
            }

            //get the ids for all the new strings (new in dictionary)
            $new_strings_in_dictionary_with_original_id = $this->db->get_results( "SELECT original,id FROM `$originals_table` WHERE BINARY $originals_table.original IN (".implode( ',', $possible_new_strings ).")", OBJECT_K );

            if( count( $new_strings_in_dictionary_with_original_id ) === count( $new_strings ) ){
                return $new_strings_in_dictionary_with_original_id;
            }
        }

        return array();

    }

    /**
     * Function that adds post_parent_id meta to  original_meta table
     * @param $original_string_ids
     * @param $post_ids
     */
    public function set_original_string_meta_post_id( $original_string_ids, $post_ids ){

        //group the strings in a new array by post_id
        $strings_grouped = array();
        if( !empty( $post_ids ) ){
            foreach( $post_ids as $i => $post_id ){
                $strings_grouped[ $post_id ][] = $original_string_ids[$i];
            }
        }

        if( !empty($strings_grouped) ){
            foreach ( $strings_grouped as $post_id => $original_ids ){

                //remove all empty values from original_ids just in case
                $original_ids = array_filter($original_ids);
                if( !empty( $original_ids ) ) {

                    /*
                     * - select all id's that are in the meta already
                     * - in php compare our $original_ids with the result and leave just the ones that are not in the db
                     * - insert all the remaining ones
                     */

                    $existing_entries = $this->db->get_results($this->db->prepare(
                        "SELECT original_id FROM " . $this->get_table_name_for_original_meta() . " WHERE meta_key = '" . $this->get_meta_key_for_post_parent_id() . "' AND meta_value = '%1d' AND original_id IN ( %2s )",
                        $post_id, implode(', ', $original_ids)
                    ), OBJECT_K);

                    $existing_entries = array_keys($existing_entries);
                    $insert_this = array_unique(array_diff($original_ids, $existing_entries));

                    if (!empty($insert_this)) {
                        $insert_values = array();
                        foreach ($insert_this as $missing_entry) {
                            $insert_values[] = $this->db->prepare("( %d, %s, %d )", $missing_entry, $this->get_meta_key_for_post_parent_id(), $post_id);
                        }

                        $this->db->query("INSERT INTO " . $this->get_table_name_for_original_meta() . " ( original_id, meta_key, meta_value ) VALUES " . implode(', ', $insert_values));
                    }

                }

            }

        }

    }

    /**
     * Check if the original meta table exists
     *
     * If the table does not exists it is created.
     *
     * @since   1.6.6
     */
    public function check_original_meta_table(){

        $table_name = $this->get_table_name_for_original_meta();
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `" . $table_name . "`(
                                    meta_id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    original_id bigint(20) NOT NULL,
                                    meta_key varchar(255),
                                    meta_value longtext,
                                    UNIQUE KEY meta_id (meta_id) )
                                     $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            //create indexes
            $sql_index = "CREATE INDEX index_original_id ON `" . $table_name . "` (original_id);";
            $this->db->query( $sql_index );
            $sql_index = "CREATE INDEX meta_key ON `" . $table_name . "`(meta_key);";
            $this->db->query( $sql_index );
        }
    }


	/**
	 * Add block_type column to dictionary tables, if it doesn't exist.
	 *
	 * Affects all existing tables, including deactivated languages
	 *
	 * @param null $language_code
	 * @param null $default_language
	 */
    public function check_for_block_type_column( $language_code = null, $default_language = null ){
    	if ( $default_language == null ){
		    $default_language = $this->settings['default-language'];
	    }

    	if ( $language_code ){
    		// check only this language
    		$array_of_table_names = array( $this->get_table_name( $language_code, $default_language ) );
	    }else {
		    // check all languages, including deactivated ones
		    $array_of_table_names = $this->get_all_table_names( $default_language, array() );
	    }

	    foreach( $array_of_table_names as $table_name ){
		    if ( ! $this->table_column_exists( $table_name, 'block_type' ) ) {
			    $this->db->query("ALTER TABLE " . $table_name . " ADD block_type INT(20) DEFAULT " . $this::BLOCK_TYPE_REGULAR_STRING );
		    }
	    }
    }


    /**
     * Add original_id column to dictionary tables, if it doesn't exist.
     *
     * Affects all existing tables, including deactivated languages
     *
     * @param null $language_code
     * @param null $default_language
     */
    public function check_for_original_id_column($language_code = null, $default_language = null ){
        if ( $default_language == null ){
            $default_language = $this->settings['default-language'];
        }

        if ( $language_code ){
            // check only this language
            $array_of_table_names = array( $this->get_table_name( $language_code, $default_language ) );
        }else {
            // check all languages, including deactivated ones
            $array_of_table_names = $this->get_all_table_names( $default_language, array() );
        }

        foreach( $array_of_table_names as $table_name ){
            if ( ! $this->table_column_exists( $table_name, 'original_id' ) ) {
                $this->db->query("ALTER TABLE " . $table_name . " ADD original_id BIGINT(20) DEFAULT NULL" );
            }
        }
    }

	/**
	 * Returns true if a database table column exists. Otherwise returns false.
	 *
	 * @link http://stackoverflow.com/a/5943905/2489248
	 * @global wpdb $wpdb
	 *
	 * @param string $table_name Name of table we will check for column existence.
	 * @param string $column_name Name of column we are checking for.
	 *
	 * @return boolean True if column exists. Else returns false.
	 */
	public function table_column_exists( $table_name, $column_name ) {
		$column = $this->db->get_results( $this->db->prepare(
			"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
			DB_NAME, $table_name, $column_name
		) );
		if ( ! empty( $column ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Update regular (non-gettext) strings in DB
	 *
	 * @param array $update_strings                 Array of strings to update
	 * @param string $language_code                 Language code
	 * @param array $columns_to_update              Array with the name of columns to update id, original, translated, status, block_type, original_id
     */

	public function update_strings( $update_strings, $language_code, $columns_to_update = array('id','original', 'translated', 'status', 'block_type', 'original_id') ) {
		if ( count( $update_strings ) == 0 ) {
			return;
		}

		$placeholder_array_mapping = array( 'id'=>'%d', 'original'=>'%s', 'translated' => '%s', 'status' => '%d', 'block_type'=>'%d', 'original_id'=>'%d' );
		$columns_query_part = '';
		foreach ( $columns_to_update as $column ) {
			$columns_query_part .= $column . ',';
			$placeholders[] = $placeholder_array_mapping[$column];
		}
		$columns_query_part = rtrim( $columns_query_part, ',' );

		$query = "INSERT INTO `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` ( " . $columns_query_part . " ) VALUES ";

		$values = array();
		$place_holders = array();

		$placeholders_query_part = '(';
		foreach ( $placeholders as $placeholder ) {
			$placeholders_query_part .= "'" . $placeholder . "',";
		}
		$placeholders_query_part = rtrim( $placeholders_query_part, ',' );
		$placeholders_query_part .= ')';

		foreach ( $update_strings as $string ) {
			foreach( $columns_to_update as $column ) {
				array_push( $values, $string[$column] );
			}
			$place_holders[] = $placeholders_query_part;
		}

		$on_duplicate = ' ON DUPLICATE KEY UPDATE ';
		$key_term_values = $this->is_values_accepted() ? 'VALUES' : 'VALUE';
		foreach ( $columns_to_update as $column ) {
			if ( $column == 'id' ){
				continue;
			}
			$on_duplicate .= $column . '=' . $key_term_values . '(' . $column . '),';
		}
		$query .= implode( ', ', $place_holders );

		$on_duplicate = rtrim( $on_duplicate, ',' );
		$query .= $on_duplicate;

		// you cannot insert multiple rows at once using insert() method.
		// but by using prepare you cannot insert NULL values.

		$prepared_query = $this->db->prepare($query . ' ', $values);
		$this->db->query( $prepared_query );
        if( !$this->check_invalid_text ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->check_invalid_text = $trp->get_component( 'check_invalid_text' );
        }
        $this->check_invalid_text->update_translations_without_invalid_text( $update_strings, $language_code, $columns_to_update );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running update_strings()' ) );
	}

	/**
	 * Insert new regular strings in DB.
	 *
	 * @param array $new_strings Array of strings for which we do not have a translation. Only inserts original.
	 * @param string $language_code Language code of table where it should be inserted.
	 * @param int $block_type
	 */
	public function insert_strings( $new_strings, $language_code, $block_type = self::BLOCK_TYPE_REGULAR_STRING ) {

        if ( $block_type == null ) {
			$block_type = self::BLOCK_TYPE_REGULAR_STRING;
		}
		if ( count( $new_strings ) == 0 ) {
			return;
		}
		$query = "INSERT INTO `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` ( original, translated, status, block_type, original_id ) VALUES ";

        $values = array();
        $place_holders = array();
        $new_strings = array_unique( $new_strings );

        //make sure we have the same strings in the original table as well
        $original_inserts = $this->original_strings_sync( $language_code, $new_strings );

        foreach ( $new_strings as $string ) {
            array_push( $values, $string, NULL, self::NOT_TRANSLATED, $block_type, $original_inserts[$string]->id );
            $place_holders[] = "('%s','%s','%d','%d', %d)";
        }
		$query .= implode( ', ', $place_holders );

        // you cannot insert multiple rows at once using insert() method.
        // but by using prepare you cannot insert NULL values.
        $this->db->query( $this->db->prepare($query . ' ', $values) );
        if( !$this->check_invalid_text ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->check_invalid_text = $trp->get_component( 'check_invalid_text' );
        }
        $this->check_invalid_text->insert_translations_without_invalid_text($new_strings, $language_code, $block_type);
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running insert_strings()' ) );
    }

    /**
     * Returns the DB ids of the provided original strings
     *
     * @param array $original_strings       Array of original strings to search for.
     * @param string $language_code         Language code to query for.
     * @return object                       Associative Array of objects with translations where key is original string.
     */
    public function get_string_ids( $original_strings, $language_code, $output = OBJECT_K ){
        if ( !is_array( $original_strings ) || count ( $original_strings ) == 0 ){
            return array();
        }
        $query = "SELECT original,id FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE original IN ";

        $placeholders = array();
        $values = array();
        foreach( $original_strings as $string ){
            $placeholders[] = '%s';
            $values[] = $string;
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $dictionary = $this->db->get_results( $this->db->prepare( $query, $values ), $output  );

        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_string_ids()' ) );
        return $dictionary;
    }

    /**
     * Returns the DB ids of the provided original strings
     *
     * @param array $original_strings       Array of original strings to search for.
     * @return array                       Associative Array of objects with translations where key is original string.
     */
    public function get_original_string_ids( $original_strings ){
        if ( !is_array( $original_strings ) || count ( $original_strings ) == 0 ){
            return array();
        }
        $query = "SELECT original,id FROM `" . $this->get_table_name_for_original_strings() . "` WHERE BINARY original IN ";

        $placeholders = array();
        $values = array();
        foreach( $original_strings as $string ){
            $placeholders[] = '%s';
            $values[] = $string;
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $results = $this->db->get_results( $this->db->prepare( $query, $values ), OBJECT_K );

        $results_ids = array();
        if( !empty( $results ) && !empty( $original_strings ) ){
            foreach( $original_strings as $string ){
                if( !empty( $results[$string] ) && !empty($results[$string]->id) )
                    $results_ids[] = $results[$string]->id;
                else
                    $results_ids[] = null; //this should not happen but if it does we need to keep the same number of result ids as original_strings to have a correlation
            }
        }

        return $results_ids;
    }

    /**
     * Returns the entries for the provided strings.
     *
     * Only returns results where there is no translation ( == NOT_TRANSLATED )
     *
     * @param array $strings_array      Array of original strings to search for.
     * @param string $language_code     Language code to query for.
     * @return object                   Associative Array of objects with translations where key is original string.
     */
    public function get_untranslated_strings( $strings_array, $language_code ){
        if ( !is_array( $strings_array ) || count ( $strings_array ) == 0 ){
            return array();
        }
        $query = "SELECT original,id FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE status = " . self::NOT_TRANSLATED . " AND original IN ";

        $placeholders = array();
        $values = array();
        foreach( $strings_array as $string ){
            $placeholders[] = '%s';
            $values[] = $string;
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $dictionary = $this->db->get_results( $this->db->prepare( $query, $values ), OBJECT_K );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_untranslated_strings()' ) );
        return $dictionary;
    }

    /**
     * Return custom table name for given language code.
     *
     * @param string $language_code         Language code.
     * @param string $default_language      Default language. Defaults to the one from settings.
     * @return string                       Table name.
     */
    public function get_table_name( $language_code, $default_language = null, $only_prefix = false ){
        if ( $default_language == null ) {
            $default_language = $this->settings['default-language'];
        }
        if ( (!trp_is_valid_language_code($language_code) && $only_prefix === false) || !trp_is_valid_language_code($default_language) ){
            /* there's are other checks that display an admin notice for this kind of errors */
            return 'trp_language_code_is_invalid_error';
        }

        return apply_filters( 'trp_table_name_dictionary', $this->db->prefix . 'trp_dictionary_' . strtolower( $default_language ) . '_'. strtolower( $language_code ), $this->db->prefix, $language_code, $default_language );
    }

    public function get_language_code_from_table_name( $table_name, $default_language = null ){
	    if ( $default_language == null ) {
		    $default_language = $this->settings['default-language'];
	    }
	    $language_code = str_replace($this->db->prefix . 'trp_dictionary_' . strtolower( $default_language ) . '_', '', $table_name );
	    return $language_code;
    }

    /**
     * Return table name for original strings table
     *
     * @return string                       Table name.
     */
    public function get_table_name_for_original_strings(){
        return apply_filters( 'trp_table_name_original_strings', sanitize_text_field( $this->db->prefix . 'trp_original_strings' ), $this->db->prefix );
    }

    /**
     * Return table name for original meta table
     *
     * @return string                       Table name.
     */
    public function get_table_name_for_original_meta(){
        return apply_filters( 'trp_table_name_original_meta', sanitize_text_field( $this->db->prefix . 'trp_original_meta' ), $this->db->prefix );
    }

    /**
     * Return table name for gettext original strings table
     *
     * @return string                       Table name.
     */
    public function get_table_name_for_gettext_original_strings(){
        return sanitize_text_field( $this->db->prefix . 'trp_gettext_original_strings' );
    }

    /**
     * Return table name for gettext original meta table
     *
     * @return string                       Table name.
     */
    public function get_table_name_for_gettext_original_meta(){
        return sanitize_text_field( $this->db->prefix . 'trp_gettext_original_meta' );
    }

    /**
     * Return meta_key for post parent id from meta table
     *
     * @return string                       key name.
     */
    public function get_meta_key_for_post_parent_id(){
        return 'post_parent_id';
    }

    public function get_all_gettext_strings(  $language_code, $inferior_limit = null, $batch_size = null ){
        if ($inferior_limit == null && $batch_size ==null) {
            $dictionary = $this->db->get_results("SELECT tt.id, CASE WHEN ot.original is NULL THEN tt.original ELSE NULL END as tt_original, tt.translated, tt.domain AS tt_domain, tt.plural_form, tt.original_id AS tt_original_id, ot.original, ot.domain, ot.context FROM `" . sanitize_text_field($this->get_gettext_table_name($language_code)) . "` AS tt LEFT JOIN `" . sanitize_text_field($this->get_table_name_for_gettext_original_strings()) . "` AS ot ON tt.original_id = ot.id", ARRAY_A);
        }else{
            $dictionary = $this->db->get_results("SELECT tt.id, CASE WHEN ot.original is NULL THEN tt.original ELSE NULL END as tt_original, tt.translated, tt.domain AS tt_domain, tt.plural_form, tt.original_id AS tt_original_id, ot.original, ot.domain, ot.context FROM `" . sanitize_text_field($this->get_gettext_table_name($language_code)) . "` AS tt LEFT JOIN `" . sanitize_text_field($this->get_table_name_for_gettext_original_strings()) . "` AS ot ON tt.original_id = ot.id LIMIT " . $inferior_limit . ", " . ($inferior_limit + $batch_size), ARRAY_A);
        }
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_all_gettext_strings()' ) );
        if ( is_array( $dictionary ) && count( $dictionary ) === 0 && !$this->table_exists($this->get_gettext_table_name( $language_code )) ){
            // if table is missing then last_error is empty
            $this->maybe_record_automatic_translation_error(array( 'details' => 'Missing table ' . $this->get_gettext_table_name( $language_code ). ' . To regenerate tables, try going to Settings->TranslatePress->General tab and Save Settings.'), true );
        }
        return $dictionary;
    }

    public function get_all_gettext_translated_strings(  $language_code ){
        $dictionary = $this->db->get_results("SELECT id, original, translated, domain FROM `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) ) . "` WHERE translated <>'' AND status != " . self::NOT_TRANSLATED, ARRAY_A );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_all_gettext_translated_strings()' ) );
        return $dictionary;
    }

    public function get_gettext_table_name( $language_code ){
        if ( !trp_is_valid_language_code($language_code) ){
            /* there's are other checks that display an admin notice for this kind of errors */
            return 'trp_language_code_is_invalid_error';
        }
        return apply_filters( 'trp_table_name_gettext', $this->db->prefix . 'trp_gettext_' . strtolower( $language_code ), $this->db->prefix, $language_code );
    }

    /**
     * Return entire rows for given ids or original strings.
     *
     * @param array $id_array Int array of db ids.
     * @param array $original_array String array of originals.
     * @param string $language_code Language code of table.
     * @param string $output Return format
     * @param bool $is_original_id_array Whether the id array refers to the id or original_id column
     * @return object                   Associative Array of objects with translations where key is id.
     */
    public function get_string_rows( $id_array, $original_array, $language_code, $output = OBJECT_K, $is_original_id_array = false ){
        $original_id = ($is_original_id_array) ? ' original_id, ' : '';
        $select_query = "SELECT " . $original_id . "id, original, translated, status, block_type FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE ";

        $prepared_query1 = '';
        if ( is_array( $original_array ) && count ( $original_array ) > 0 ) {
            $placeholders = array();
            $values = array();
            foreach ($original_array as $string) {
                $placeholders[] = '%s';
                $values[] = $string;
            }

            $query1 = "original IN ( " . implode(", ", $placeholders) . " )";
            $prepared_query1 = $this->db->prepare($query1, $values);
        }

        $prepared_query2 = '';
        if ( is_array( $id_array ) && count ( $id_array ) > 0 ) {
            $placeholders = array();
            $values = array();
            foreach ($id_array as $id) {
                $placeholders[] = '%d';
                $values[] = intval($id);
            }
            $original_or_not = ( $is_original_id_array ) ? 'original_' : '';
            $query2 = $original_or_not . "id IN ( " . implode(", ", $placeholders) . " )";
            $prepared_query2 = $this->db->prepare($query2, $values);
        }


        $query = '';
        if ( empty ( $prepared_query1 ) && empty ( $prepared_query2 ) ){
            return array();
        }
        if ( empty( $prepared_query1 ) ){
            $query = $select_query . $prepared_query2;
        }
        if ( empty( $prepared_query2 ) ){
            $query = $select_query . $prepared_query1;
        }
        if ( !empty ( $prepared_query1 ) && !empty ( $prepared_query2 ) ){
            $query = $select_query . $prepared_query1 . " OR " . $prepared_query2;
        }


        $dictionary = $this->db->get_results( $query, $output );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_string_rows()' ) );
        return $dictionary;
    }

    public function get_gettext_string_rows_by_ids( $id_array, $language_code ){
        if ( !is_array( $id_array ) || count ( $id_array ) == 0 ){
            return array();
        }

        $query = "SELECT ot.id as ot_id, tt.id, ot.original, tt.original as tt_original, tt.translated, tt.domain AS tt_domain, tt.plural_form, ot.original, ot.domain, ot.context, ot.original_plural FROM `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) )  . "` AS tt LEFT JOIN `" . sanitize_text_field( $this->get_table_name_for_gettext_original_strings() ) . "` AS ot ON tt.original_id = ot.id WHERE tt.id IN ";

        $placeholders = array();
        $values = array();
        foreach( $id_array as $id ){
            $placeholders[] = '%d';
            $values[] = intval( $id );
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $dictionary = $this->db->get_results( $this->db->prepare( $query, $values ), ARRAY_A );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_gettext_string_rows_by_ids()' ) );
        return $dictionary;
    }

    public function get_gettext_string_rows_by_original_id( $original_id_array, $language_code ){
        if ( !is_array( $original_id_array ) || count ( $original_id_array ) == 0 ){
            return array();
        }
        $query = "SELECT tt.id, tt.original AS tt_original, tt.translated, tt.status, tt.domain AS tt_domain, tt.plural_form, ot.id AS ot_id, ot.original, ot.domain, ot.context, ot.original_plural FROM `" . sanitize_text_field( $this->get_table_name_for_gettext_original_strings() ) . "` AS ot LEFT JOIN `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) ) . "` AS tt ON tt.original_id = ot.id WHERE ot.id IN ";

        $placeholders = array();
        $values = array();
        foreach( $original_id_array as $id ){
            $placeholders[] = '%d';
            $values[] = intval( $id );
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $dictionary = $this->db->get_results( $this->db->prepare( $query, $values ), ARRAY_A );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_gettext_string_rows_by_original_id()' ) );
        return $dictionary;
    }

    public function get_gettext_string_rows_by_original( $original_array, $language_code ){
        if ( !is_array( $original_array ) || count ( $original_array ) == 0 ){
            return array();
        }
        $query = "SELECT tt.id, tt.original as tt_original, tt.translated, tt.domain AS tt_domain, tt.plural_form, ot.original, ot.domain, ot.context FROM `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) )  . "` AS tt LEFT JOIN `" . sanitize_text_field( $this->get_table_name_for_gettext_original_strings() ) . "` AS ot ON tt.original_id = ot.id WHERE CASE WHEN ot.original is NULL THEN tt.original ELSE NULL END IN ";

        $placeholders = array();
        $values = array();
        foreach( $original_array as $string ){
            $placeholders[] = '%s';
            $values[] = $string;
        }

        $query .= "( " . implode ( ", ", $placeholders ) . " )";
        $dictionary = $this->db->get_results( $this->db->prepare( $query, $values ), ARRAY_A );
        $this->maybe_record_automatic_translation_error(array( 'details' => 'Error running get_gettext_string_rows_by_original()' ) );
        return $dictionary;
    }

    public function get_all_table_names ( $original_language, $exception_translation_languages = array() ){
	    foreach ( $exception_translation_languages as $key => $language ){
		    $exception_translation_languages[$key] = $this->get_table_name( $language, $original_language );
	    }
	    $return_tables = array();
	    $table_name = $this->get_table_name( '', null, true  );
	    $table_names = $this->db->get_results( "SHOW TABLES LIKE '$table_name%'", ARRAY_N );
	    foreach ( $table_names as $table_name ){
	    	if ( isset( $table_name[0]) && ! in_array( $table_name[0], $exception_translation_languages ) ) {
			    $return_tables[] = $table_name[0];
		    }
	    }
	    return $return_tables;
    }

    public function get_all_gettext_table_names(){
        global $wpdb;
        $table_name = $wpdb->get_blog_prefix() . 'trp_gettext_';
        $return_tables = array();

        $table_names = $this->db->get_results( "SHOW TABLES LIKE '$table_name%'", ARRAY_N );
        foreach ( $table_names as $table_name ){
            if ( isset( $table_name[0]) &&
                strpos($table_name[0], 'trp_gettext_original_meta') === false &&
                strpos($table_name[0], 'trp_gettext_original_strings') === false ) {
                $return_tables[] = $table_name[0];
            }
        }
        return $return_tables;
    }

	public function update_translation_blocks_by_original( $table_names, $original_array, $block_type ) {

		$values = array();
		foreach( $table_names as $table_name ){
			$placeholders = array();
			foreach( $original_array as $string ){
				$placeholders[] = '%s';
				$values[] = trp_full_trim( $string );
			}
		}

		$placeholders = "( " . implode ( ", ", $placeholders ) . " )";
		$query = 'UPDATE `' . implode( '`, `', $table_names ) . '` SET `' . implode( '`.block_type=' . $block_type . ', `', $table_names ) . '`.block_type=' . $block_type . ' WHERE `' . implode( '`.original IN ' . $placeholders . ' AND `', $table_names ) . '`.original IN ' . $placeholders ;

		return $this->db->query( $this->db->prepare( $query, $values ) );
	}

	/**
	 * Removes duplicate rows of regular strings table
	 *
	 * (original, translated, status, block_type) have to be identical.
	 * Only the row with the lowest ID remains
	 *
	 * https://stackoverflow.com/a/25206828
	 *
	 * @param $table
	 */
	public function remove_duplicate_rows_in_dictionary_table( $language_code, $inferior_limit, $batch_size ) {
		$table_name = $this->get_table_name( $language_code );
        if ($this->table_exists($table_name)) {
            $last_id = $this->get_last_id( $table_name );
            $query = $this->get_remove_identical_duplicates_query( $table_name, $inferior_limit, 'regular' );
            $this->db->query( $query );
            if ( $inferior_limit > $last_id ) {
                return true;
            }
            return false;
        }else{
            return true;
        }
    }

    /**
     * Removes duplicate rows of gettext strings table
     *
     * (original, translated, domain, status) have to be identical.
     * Only the row with the lowest ID remains
     *
     * @param $table
     */
    /**
     * @param $language_code
     * @param $inferior_limit 1000, 2000
     * @param $batch_size
     * @return bool|int
     */
	public function remove_duplicate_rows_in_gettext_table( $language_code, $inferior_limit, $batch_size ){
        $table_name = $this->get_gettext_table_name( $language_code );
        if ($this->table_exists($table_name)) {
            $last_id = $this->get_last_id( $table_name );
            $query = $this->get_remove_identical_duplicates_query( $table_name, $inferior_limit, 'gettext' );
            $this->db->query( $query );
            if ( $inferior_limit > $last_id ) {
                return true;
            }
            return false;
        }else{
            return true;
        }
    }

    /**
     * Function that builds the query string for removing identical entries
     * @param $table_name
     * @param $batch
     * @param $type string possible values are 'regular' or 'gettext'
     * @return string
     */
    private function get_remove_identical_duplicates_query( $table_name, $inferior_limit, $type ){
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        $query = '	DELETE `b`
					FROM
					    ' . $table_name . ' AS `a`,
					    ' . $table_name . ' AS `b`
					WHERE
					    -- IMPORTANT: Ensures one version remains
					    `a`.ID < ' . $inferior_limit . '
					    AND `b`.ID < ' . $inferior_limit . '
					    AND `a`.`ID` < `b`.`ID`

					    -- Check for all duplicates. Binary ensure case sensitive comparison
					    AND (`a`.`original` COLLATE '.$charset.'_bin = `b`.`original` OR `a`.`original` IS NULL AND `b`.`original` IS NULL)
					    AND (`a`.`translated` COLLATE '.$charset.'_bin = `b`.`translated` OR `a`.`translated` IS NULL AND `b`.`translated` IS NULL)
					    AND (`a`.`status` = `b`.`status` OR `a`.`status` IS NULL AND `b`.`status` IS NULL)';
        if($type === 'gettext')
            $query .= 'AND (`a`.`domain` = `b`.`domain` OR `a`.`domain` IS NULL AND `b`.`domain` IS NULL)';
        else if($type === 'regular')
            $query .= 'AND (`a`.`block_type` = `b`.`block_type` OR `a`.`block_type` IS NULL AND `b`.`block_type` IS NULL)';
        $query .=	    ';';
        return $query;
    }

	/**
	 * Removes a row if translation status 0, if the original exists translated
	 *
	 * Only the original with translation remains
	 */
	public function remove_untranslated_strings_if_translation_available( $language_code, $inferior_limit, $batch_size ){
		$table_name = $this->get_table_name( $language_code );

        if ($this->table_exists($table_name)) {
            $query = $this->get_remove_untranslated_duplicates_query( $table_name, 'regular' );
            $this->db->query( $query );
        }
        return true;
	}

    /**
     * Removes a row if translation status 0, if the original exists translated in gettext tables
     *
     * Only the original with translation remains
     */
    public function remove_untranslated_strings_if_gettext_translation_available( $language_code, $inferior_limit, $batch_size ){
        $table_name = $this->get_gettext_table_name( $language_code );

        if ($this->table_exists($table_name)) {
            $query = $this->get_remove_untranslated_duplicates_query( $table_name, 'gettext' );
            $this->db->query( $query );
        }
        return true;
    }

    /**
     * Function that builds the query string for removing identical entries
     * @param $table_name
     * @param $batch
     * @param $type string possible values are 'regular' or 'gettext'
     * @return string
     */
    private function get_remove_untranslated_duplicates_query( $table_name, $type ){
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        $query = '	DELETE `a`
						FROM
						    ' . $table_name . ' AS `a`,
						    ' . $table_name . ' AS `b`
						WHERE
						    (`a`.`original` COLLATE '.$charset.'_bin = `b`.`original` OR `a`.`original` IS NULL AND `b`.`original` IS NULL)
						    AND (`a`.`status` = 0 )
						    AND (`b`.`status` != 0 )';
        if($type === 'gettext')
            $query .= 'AND (`a`.`domain` = `b`.`domain` OR `a`.`domain` IS NULL AND `b`.`domain` IS NULL)';
        else if($type === 'regular')
            $query .= 'AND (`a`.`block_type` = `b`.`block_type` OR `a`.`block_type` IS NULL AND `b`.`block_type` IS NULL)';
        $query .=	    ';';
        return $query;
    }


    /**
     * Removes CDATA from original and dictionary tables.
     * @param $language_code
     * @param $inferior_limit
     * @param $batch_size
     * @return bool
     */
    public function remove_cdata_in_original_and_dictionary_tables($language_code, $inferior_limit, $batch_size){

        if ($language_code == $this->settings['default-language']){
            $table_name = $this->get_table_name_for_original_strings();
            $query = $this->get_remove_cdata_query($table_name, $batch_size);

            $rows_affected = $this->db->query( $query );
            if ( $rows_affected > 0 ) {
                return false;
            }else{
                return true;
            }
        }
        $table_name = $this->get_table_name( $language_code );
        if ($this->table_exists($table_name)) {
            $query = $this->get_remove_cdata_query($table_name, $batch_size);
            $rows_affected = $this->db->query( $query );
            if ( $rows_affected > 0 ) {
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }

    /**
     * @param $table_name
     * @param $batch_size
     * @return string
     */
    private function get_remove_cdata_query( $table_name, $batch_size ){

        $query = "DELETE FROM " . $table_name . " WHERE original LIKE '<![CDATA[%' LIMIT " . $batch_size;

        return $query;
    }

    /**
     * Removes untranslated links from the dictionary table
     * @param $language_code
     * @param $inferior_limit
     * @param $batch_size
     * @return bool
     */
    public function remove_untranslated_links_in_dictionary_table($language_code, $inferior_limit, $batch_size){
        $table_name = $this->get_table_name( $language_code );
        if ($this->table_exists($table_name)) {
            $query = $this->get_remove_untranslated_links_query($table_name, $batch_size);
            $rows_affected = $this->db->query( $query );
            if ( $rows_affected > 0 ) {
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }

    /**
     * @param $table_name
     * @return $query
     */
    private function get_remove_untranslated_links_query($table_name, $batch_size){

        $query = "DELETE FROM " . $table_name . " WHERE original LIKE 'http%' AND (translated = '' OR translated IS NULL) LIMIT " . $batch_size;

        return $query;
    }

    /*
     * Get last inserted ID for this table
     *
     * Useful for optimizing database by removing duplicate rows
     */
	public function get_last_id( $table_name ){
		$last_id = $this->db->get_var("SELECT MAX(id) FROM " . $table_name );
		return $last_id;
	}

	/**
	 * Returns a selection of rows from a specific location.
	 * Only id and original are selected.
	 *
	 * Ex. if $inferior_limit = 400 and $batch_size = 10
	 * You will get rows 401 to 411
	 *
	 * @param $language_code
	 * @param $inferior_limit
	 * @param $batch_size
	 *
	 * @return array|null|object
	 */
	public function get_rows_from_location( $language_code, $inferior_limit, $batch_size, $columns_to_retrieve ) {
		$columns_query_part = '';
		foreach ( $columns_to_retrieve as $column ) {
			$columns_query_part .= $column . ',';
		}
		$columns_query_part = rtrim( $columns_query_part, ',' );
		$query = "SELECT " .  $columns_query_part . " FROM `" . sanitize_text_field( $this->get_table_name( $language_code ) ) . "` WHERE status != " . self::NOT_TRANSLATED . " ORDER BY id LIMIT " . $inferior_limit . ", " . $batch_size;
		$dictionary = $this->db->get_results( $query, ARRAY_A );
		return $dictionary;
	}

	/**
	 * Used for updating database
	 *
	 * @param string $language_code     Language code of the table
	 * @param string $limit             How many strings to affect at most
	 *
	 * @return bool|int
	 */
	public function delete_empty_gettext_strings( $language_code, $limit ){
		$limit = (int) $limit;
		$sql = "DELETE FROM `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) ). "` WHERE (original IS NULL OR original = '') LIMIT " . $limit;
		return $this->db->query( $sql );
	}

	public function maybe_record_automatic_translation_error($error_details = array(), $ignore_last_error = false ){
        if( !$this->check_invalid_text ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->check_invalid_text = $trp->get_component( 'check_invalid_text' );
        }

        if ( ( !empty( $this->db->last_error) && !$this->check_invalid_text->is_invalid_data_error() ) || $ignore_last_error ){
            $trp = TRP_Translate_Press::get_trp_instance();
            if( !$this->error_manager ){
                $this->error_manager = $trp->get_component( 'error_manager' );
            }
            if( !$this->url_converter ) {
                $this->url_converter = $trp->get_component( 'url_converter' );
            }

            $default_error_details = array(
                'last_error'  => $this->db->last_error,
                'disable_automatic_translations' => true,
                'url' => $this->url_converter->cur_page_url(),
            );
            $error_details = array_merge( $default_error_details, $error_details );
            $this->error_manager->record_error( $error_details );
        }
    }

    /**
     * Return true if table exists in db, return false otherwise
     *
     * @param $table_name
     * @param $ignore_cache
     * @return bool
     */
    public function table_exists($table_name, $ignore_cache = false ){
        if( !$ignore_cache && in_array( $table_name, $this->tables_exist ) ){
            return true;
        }

	    $table_name = sanitize_text_field($table_name);
        $table_found = strtolower( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) ) == strtolower( $table_name );
        if ( $table_found ) {
            $this->tables_exist[] = $table_name;
        }
        return $table_found;
    }

    /**
     * Removes any other strings from DB that have the same original with the provided array
     *
     * Keeps only the rows with the ids specified. Any other rows with the same original (and domain for gettext) are deleted from DB
     *
     * @param $update_string_array
     * @param $language
     * @param $string_type
     * @return bool|int|void
     */
    public function remove_possible_duplicates( $update_string_array, $language, $string_type ){
        if ( !is_array( $update_string_array ) || count ($update_string_array) < 1 ) {
            return;
        }

        $charset_collate = $this->db->get_charset_collate();
        $charset = (strpos( 'latin1', $charset_collate ) === 0 ) ? "latin1" : "utf8mb4";

        $table_name = ( $string_type === 'gettext' ) ? $this->get_gettext_table_name( $language ) : $this->get_table_name( $language );

        $values = array();
        $place_holders = array();
        foreach( $update_string_array as $string ){
            if ( $string_type === 'gettext' ) {
                array_push( $values, $string['original'], $string['domain'], (int)$string['plural_form'], $string['id'] );
            }else{
                array_push( $values, $string['original'], $string['id'] );
            }
            $domain = ( $string_type === 'gettext') ? "AND domain COLLATE " . $charset . "_bin = '%s' AND plural_form = '%d' " : "";
            $place_holders[] = "(original COLLATE " . $charset . "_bin = '%s' " . $domain . "AND id != '%d'  )";
        }

        $sql = "DELETE FROM `" . sanitize_text_field( $table_name ). "` WHERE " . implode( " OR ", $place_holders );
        $query = $this->db->prepare( $sql, $values );
        return $this->db->query( $query );
    }

    public function rename_originals_table(){
        $new_table_name = sanitize_text_field( $this->get_table_name_for_original_strings() . time() );
        $this->db->query( "ALTER TABLE " . $this->get_table_name_for_original_strings() . " RENAME TO " . $new_table_name );

        $table_to_use_for_recovery = get_option('trp_original_strings_table_for_recovery', '');
        if ( $table_to_use_for_recovery == '' ) {
            // if a previous run of removing original strings duplicates failed, use the old table, not the one created during that failed time
            update_option( 'trp_original_strings_table_for_recovery', $new_table_name );
        }
    }

    public function regenerate_original_meta_table($inferior_limit, $batch_size){

        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_original_strings();
        $recovery_originals_table = sanitize_text_field( get_option( 'trp_original_strings_table_for_recovery' ) );
        $originals_meta_table = $this->get_table_name_for_original_meta();
        if ( empty( $recovery_originals_table ) ){
            $this->error_manager->record_error(array('regenerate_original_meta_table' => 'Empty option trp_original_strings_table_for_recovery'));
            return;
        }

        $this->db->query( $this->db->prepare("UPDATE `$originals_meta_table` trp_meta INNER JOIN `$recovery_originals_table` trp_old ON trp_meta.original_id = trp_old.id LEFT JOIN `$originals_table` trp_new ON trp_new.original = trp_old.original set trp_meta.original_id = IF(trp_new.id IS NULL, 0, trp_new.id) WHERE trp_meta.meta_id > %d AND trp_meta.meta_id <= %d AND trp_new.id != trp_old.id", $inferior_limit, ($inferior_limit + $batch_size) ) );

        /* UPDATE `wp_trp_original_meta` trp_meta INNER JOIN `wp_trp_original_strings1608214654` as trp_old ON trp_meta.original_id = trp_old.id
         * LEFT JOIN `wp_trp_original_strings` as trp_new on trp_new.original = trp_old.original  set trp_meta.original_id = IF(trp_new.id IS NULL, 0, trp_new.id)
         * WHERE trp_meta.meta_id > 10 AND trp_meta.meta_id <= 33 AND trp_new.id != trp_old.id*/

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_regenerate_original_meta_table' => $this->db->last_error));
        }
    }

    public function clean_original_meta( $limit ){
        $limit = (int) $limit;
        $sql = "DELETE FROM `" . sanitize_text_field( $this->get_table_name_for_original_meta() ). "` WHERE original_id = 0 LIMIT " . $limit;
        return $this->db->query( $sql );
    }

    public function drop_table($table_name){
        $sql = "DROP TABLE `" . sanitize_text_field( $table_name ). "`";
        return $this->db->query( $sql );
    }

    /**
     * Return db sql version
     *
     * Using 'select version()' instead of wpdb->db_server_info because
     * db_server_info returns format 5.5.5-10.3.3-mariadb instead of 10.3.3-mariadb on some setups
     * https://www.php.net/manual/en/mysqli.get-server-info.php#118822
     *
     * @return string|null
     */
    public function get_db_sql_version(){
        if ( $this->db_sql_version === null ){
            $this->db_sql_version = $this->db->get_var( 'select version()' );
            $this->db_sql_version = ( $this->db_sql_version === null ) ? '0' : $this->db_sql_version;
        }
        return $this->db_sql_version;
    }

    /**
     * Whether it is safe to use VALUES() instead of VALUE()
     *
     * Starting with 10.3.3 MariaDB recommends using VALUE() instead of VALUES()
     * Even though they say they still accept the term values for 'on duplicate key update' syntax,
     * some users still report syntax error.
     * https://mariadb.com/kb/en/values-value/
     *
     * MySQL servers marked the use of VALUES deprecated starting with 8.0.20 but have not removed support for it.
     * We can't use the MariaDB approach, their alternative is different and not supported by earlier versions.
     * For now, there is no need to further complicate this SQL query based on DB version and make.
     *
     *
     * @return bool
     */
    public function is_values_accepted(){
        $return = true;
        $db_sql_version = strtolower( $this->get_db_sql_version() );
        if ( strpos( $db_sql_version, 'mariadb' ) !== false ){
            $db_server_array = explode('-', $db_sql_version);
            if( isset( $db_server_array[1] ) && $db_server_array[1] == 'mariadb' && version_compare($db_server_array[0], '10.3.3', '>=') ){
                $return = false;
            }
        }
        return apply_filters('trp_is_sql_values_accepted', $return );
    }

    /**
     * Return true if the dictionary table of $language has at least $minimum_rows with $status
     *
     * @param $language
     * @param $minimum_rows
     * @param $status
     *
     * @return bool
     */
    public function minimum_rows_with_status( $language, $minimum_rows, $status ) {
        $minimum_rows = (int) $minimum_rows;
        $status = (int) $status;

        $sql = "SELECT (COUNT(*) > " . $minimum_rows . ") FROM `" . sanitize_text_field( $this->get_table_name( $language ) ). "` WHERE status = " . $status;
        return $this->db->get_var( $sql );
    }

    public function is_gettext_normalized(){
        if( $this->gettext_normalized === null ){
            $this->gettext_normalized = !( get_option( 'trp_gettext_normalized', '' ) == 'no' );
        }
        return $this->gettext_normalized;
    }
}
