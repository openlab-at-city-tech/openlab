<?php

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
	 * Add original_id, plural_form column to gettext tables, if it doesn't exist.
	 *
	 * Affects all existing tables, including deactivated languages
	 *
	 * @param null $language_code
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
     * Function that takes care of inserting original strings from gettext to gettext_original_strings table
     */
    public function gettext_original_ids_insert( $language_code, $inferior_limit, $batch_size ){

        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_gettext_original_strings();
        $table_name = sanitize_text_field( $this->get_gettext_table_name( $language_code ) );

        /*
        *  select all string that are in the dictionary table and are not in the original tables and insert them in the original
        */
        $insert_records = $this->db->query( $this->db->prepare( "INSERT INTO `$originals_table` (original, domain) SELECT DISTINCT ( BINARY t1.original ), t1.domain FROM `$table_name` t1 LEFT JOIN `$originals_table` t2 ON ( t2.original = t1.original AND t2.original = BINARY t1.original AND t2.domain = t1.domain ) WHERE t2.original IS NULL AND t2.domain IS NULL AND t1.domain != '' AND t1.original != '' AND t1.id > %d AND t1.id <= %d AND LENGTH(t1.original) < 20000", $inferior_limit, ($inferior_limit + $batch_size) ) );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_insert_gettext_original_strings' => $this->db->last_error));
        }

        return $insert_records;

    }

    /**
     * Function that makes sure we don't have duplicates in gettext_original_strings table
     * It is executed after we have inserted all the strings
     */
    public function gettext_original_ids_cleanup(){
        if( !$this->error_manager ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->error_manager = $trp->get_component( 'error_manager' );
        }

        $originals_table = $this->get_table_name_for_gettext_original_strings();
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        $this->db->query( "DELETE t1 FROM `$originals_table` t1 INNER JOIN `$originals_table` t2 WHERE t1.id > t2.id AND t1.domain = t2.domain AND t1.original COLLATE ".$charset."_bin = t2.original" );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_cleaning_gettext_original_strings' => $this->db->last_error));
        }

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
        $charset_collate = $this->db->get_charset_collate();
        $charset = "utf8mb4";
        if( strpos( 'latin1', $charset_collate ) === 0 )
            $charset = "latin1";

        /*
        *  perform a UPDATE JOIN with the original table https://www.mysqltutorial.org/mysql-update-join/
        */
        $update_records = $this->db->query( $this->db->prepare( "UPDATE $table_name, $originals_table SET $table_name.original_id = $originals_table.id WHERE $originals_table.domain = $table_name.domain AND $table_name.original COLLATE ". $charset ."_bin = $originals_table.original AND $table_name.id > %d AND $table_name.id <= %d", $inferior_limit, ($inferior_limit + $batch_size) ) );

        if (!empty($this->db->last_error)) {
            $this->error_manager->record_error(array('last_error_reindex_gettext_original_ids' => $this->db->last_error));
        }

        return $update_records;
    }


}
