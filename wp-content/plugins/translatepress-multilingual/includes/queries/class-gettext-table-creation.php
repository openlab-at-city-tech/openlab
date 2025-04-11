<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Gettext_Table_Creation
 *
 * Queries for creating gettext tables.
 *
 * To access this component use:
 * 		$trp = TRP_Translate_Press::get_trp_instance();
 *      $trp_query = $trp->get_component( 'query' );
 *      $gettext_table_creation = $trp_query->get_query_component('gettext_table_creation');
 *
 */
class TRP_Gettext_Table_Creation extends TRP_Query{

    public $db;
    protected $settings;

    /**
     * TRP_Gettext_Table_Creation constructor.
     * @param $settings
     */
    public function __construct( $settings ){
        global $wpdb;
        $this->db = $wpdb;
        $this->settings = $settings;
    }


	/**
	 * Check if gettext table for specific language exists.
	 *
	 * If the table does not exists it is created.
	 *
	 * @param string $language_code
	 */
    public function check_gettext_table( $language_code ){
        $table_name = sanitize_text_field( $this->get_gettext_table_name($language_code) );
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `" . $table_name . "`(
                                    id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    original  longtext NOT NULL,
                                    translated  longtext,
                                    domain  longtext,
                                    status int(20),
                                    original_id bigint(20),
                                    plural_form int(20),
                                    UNIQUE KEY id (id) )
                                     $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $this->maybe_record_automatic_translation_error(array( 'details' => 'Error creating gettext strings tables' ) );

            $sql_index = "CREATE INDEX index_name ON `" . $table_name . "` (original(100));";
            $this->db->query( $sql_index );

            // full text index for original
            $sql_index = "CREATE FULLTEXT INDEX original_fulltext ON `" . $table_name . "`(original);";
            $this->db->query( $sql_index );
        }
    }


    /**
     * Check if the gettext original string table exists
     *
     * If the table does not exists it is created.
     *
     */
    public function check_gettext_original_table(){

        $table_name = $this->get_table_name_for_gettext_original_strings();
        if ( $this->db->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            // table not in database. Create new table
            $charset_collate = $this->db->get_charset_collate();

            $sql = "CREATE TABLE `" . $table_name . "`(
                                    id bigint(20) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                    original TEXT NOT NULL,
                                    domain TEXT NOT NULL,
                                    context TEXT DEFAULT NULL, 
                                    original_plural TEXT DEFAULT NULL 
                                    )
                                     $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $sql_index = "CREATE INDEX gettext_index_original ON `" . $table_name . "` (original(100));";
            $this->db->query( $sql_index );
        }
    }


    /**
     * Check if the gettext original meta table exists
     *
     * If the table does not exists it is created
     */
    public function check_gettext_original_meta_table(){

        $table_name = $this->get_table_name_for_gettext_original_meta();
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
            $sql_index = "CREATE INDEX gettext_index_original_id ON `" . $table_name . "` (original_id);";
            $this->db->query( $sql_index );
            $sql_index = "CREATE INDEX gettext_meta_key ON `" . $table_name . "`(meta_key);";
            $this->db->query( $sql_index );
        }
    }
}
