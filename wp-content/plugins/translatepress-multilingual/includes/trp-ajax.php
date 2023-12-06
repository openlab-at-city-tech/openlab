<?php

/**
 * Class TRP_Ajax
 *
 * Custom Ajax to get translation of dynamic elements.
 */
class TRP_Ajax{

    protected $connection;
    protected $table_prefix;

    /**
     * TRP_Ajax constructor.
     *
     * Establishes db connection and triggers function to output translations.
     */
    public function __construct( ){

        if ( !isset( $_POST['action'] ) || $_POST['action'] !== 'trp_get_translations_regular' || empty( $_POST['originals'] ) || empty( $_POST['language'] ) || empty( $_POST['original_language'] ) ) {
            die();
        }

        include './external-functions.php';
        if ( !trp_is_valid_language_code( $_POST['language'] ) || !trp_is_valid_language_code( $_POST['original_language'] ) ) {//phpcs:ignore
            echo json_encode( 'TranslatePress Error: Invalid language code' );
            exit;
        }

        if ( $this->connect_to_db() ){

            $this->output_translations(
            	$this->sanitize_strings( $_POST['originals'] ),//phpcs:ignore
            	$this->sanitize_strings( $_POST['skip_machine_translation'] ),//phpcs:ignore
	            mysqli_real_escape_string( $this->connection, $_POST['language'] ), /* phpcs:ignore */ /* validated with trp_is_valid_language_code on line 25 */
	            mysqli_real_escape_string( $this->connection, $_POST['original_language'] ) /* phpcs:ignore */ /* validated with trp_is_valid_language_code on line 25 */
            );
            //Successful connection to DB
            mysqli_close($this->connection);
        }else{
            //Error connecting to DB
            $this->return_error();

        }

    }

    /**
     * Sanitize posted strings.
     *
     * @param array $posted_strings     Array of strings.
     * @return array                    Sanitized array of strings.
     */
    protected function sanitize_strings( $posted_strings){
    	$numerals_option = ( isset( $_POST['translate_numerals_opt'] ) && $_POST['translate_numerals_opt'] === 'yes' ) ? 'yes' : 'no';
        $strings = json_decode( $posted_strings );
        if ( is_array( $strings ) ) {
            foreach ($strings as $key => $string) {
	            $strings[$key] = mysqli_real_escape_string( $this->connection, trp_full_trim( $string, array( 'numerals'=> $numerals_option ) ) );
            }
        }
        return $strings;
    }

    /**
     * Finds db credentials in wp-config file and tries to connect to db.
     *
     * @return bool     Whether connection was succesful or not.
     */
    protected function connect_to_db(){

        $file = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-config.php';

        try {
            $content = @file_get_contents($file);
            if ($content == false) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }


        // remove single line and multi-line /* Comments */
        $content = preg_replace('!/\*.*?\*/!s', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n", $content);

        // remove single line double slashes
        $content = preg_replace('#^\s*//.+$#m', "", $content);

        $credentials = array(
            'db_name'       => 'DB_NAME',
            'db_user'       => 'DB_USER',
            'db_password'   => 'DB_PASSWORD',
            'db_host'       => 'DB_HOST',
            'db_charset'    => 'DB_CHARSET'
        );

        foreach ( $credentials as $credential => $constant_name ) {
            if ( preg_match_all( "/define\s*\(\s*['\"]" . $constant_name . "['\"]\s*,\s*['\"](.*?)['\"]\s*\)/", $content, $result ) ) {
                $credentials[ $credential ] = $result[1][0];
            } else {
                return false;
            }
        }


        $this->connection = mysqli_connect( $credentials['db_host'], $credentials['db_user'], $credentials['db_password'], $credentials['db_name'] );

        // Check connection
        if ( mysqli_connect_errno() ) {
            //Failed to connect to MySQL.
            return false;
        }

        mysqli_set_charset ( $this->connection , $credentials['db_charset'] );
        if ( preg_match_all( '/\$table_prefix\s*=\s*[\'"](.*?)[\'"]/', $content, $results ) ) {
            $this->table_prefix = end( $results[1] );
        }else{
            $this->table_prefix = $this->sql_find_table_prefix();
            if ( $this->table_prefix === false ){
                return false;
            }
        }

        return true;
    }

    /**
     * Get WP table prefix.
     *
     * @return string       Table prefix.
     */
    protected function sql_find_table_prefix(){
        $sql = "SELECT DISTINCT SUBSTRING(`TABLE_NAME` FROM 1 FOR ( LENGTH(`TABLE_NAME`)-8 ) ) as prefix FROM information_schema.TABLES WHERE `TABLE_NAME` LIKE '%postmeta'";
        $result = mysqli_query( $this->connection, $sql );
        if ( mysqli_num_rows( $result ) > 0 ) {
            $result_object = mysqli_fetch_assoc($result);
            return $result_object['prefix'];
        } else {
            return false;
        }
    }

    /**
     * Output translation for given strings.
     *
     * @param array $strings            Array of string to translate.
     * @param string $language          Language to translate into.
     * @param string $original_language Language to translate from. Default language.
     */
    protected function output_translations( $strings, $skip_machine_translation, $language, $original_language ){
        $sql = 'SELECT original, translated, status FROM ' . $this->table_prefix . 'trp_dictionary_' . strtolower( $original_language ) . '_' . strtolower( $language ) . ' WHERE original IN (\'' . implode( "','", $strings ) .'\') AND status != 0';
        $result = mysqli_query( $this->connection, $sql );
        if ( $result === false ){
            $this->return_error();
        }else {
            $dictionaries[$language] = array();
            while ($row = mysqli_fetch_object($result)) {
            	// do not retrieve a row that should not be machine translated ( ex. src, href )
            	if ( $row->status == 1 && in_array( $row->original, $skip_machine_translation ) ) {
            		continue;
	            }
                $dictionaries[$language][] = $row;
            }

	        $dictionary_by_original = trp_sort_dictionary_by_original( $dictionaries, 'regular', 'dynamicstrings', null, null );
            echo json_encode($dictionary_by_original);
        }

    }

    /**
     * Return error in case of connection fail and other problems.
     */
    protected function return_error(){
        echo json_encode( 'error' );
        exit;
    }
}

new TRP_Ajax;

die();

