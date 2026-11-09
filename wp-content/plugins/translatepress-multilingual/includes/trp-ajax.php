<?php

/**
 * Class TRP_Ajax
 *
 * Custom Ajax to get translation of dynamic elements.
 */
class TRP_Ajax{

    protected $connection;
    protected $table_prefix;

    /*
     * Flags that make json_encode() escape <, >, &, ' and " as \uXXXX, so a stored dictionary original such
     * as <svg onload=...> can never be parsed as live html when this standalone endpoint's response is opened
     * as a top-level document. JSON.parse() restores the exact string, so legitimate consumers are unaffected.
     * See CU-1amndjnkyv6.
     */
    const SAFE_JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

    /**
     * TRP_Ajax constructor.
     *
     * Establishes db connection and triggers function to output translations.
     */
    public function __construct( ){

        if ( !isset( $_POST['action'] ) || $_POST['action'] !== 'trp_get_translations_domchanges' || empty( $_POST['originals'] ) || empty( $_POST['language'] ) || empty( $_POST['original_language'] ) ) {
            die();
        }

        /*
         * This file runs outside WordPress and is directly requestable, so an attacker can open its response
         * as a top-level document. Force an inert content type ( application/json + nosniff ) before any output
         * so the browser never sniffs/renders the json body as html, whatever the dictionary happens to contain.
         * Combined with SAFE_JSON_FLAGS this closes the stored-XSS sink. See CU-1amndjnkyv6.
         */
        if ( ! headers_sent() ) {
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'X-Content-Type-Options: nosniff' );
        }

        // Anchor to this file's directory. A cwd-relative include fails on PHP-FPM pools
        // where the working directory of a directly-requested script is not the script's own directory.
        include dirname( __FILE__ ) . '/external-functions.php';
        if ( ! function_exists( 'trp_is_valid_language_code' ) ) {
            // Include failed: degrade to the admin-ajax fallback instead of a fatal error.
            $this->return_error();
        }
        if ( !trp_is_valid_language_code( $_POST['language'] ) || !trp_is_valid_language_code( $_POST['original_language'] ) ) {//phpcs:ignore
            echo json_encode( 'TranslatePress Error: Invalid language code', self::SAFE_JSON_FLAGS );
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
            // Capture the quote character used ($result[1]) alongside the value ($result[2]) so we can
            // reverse PHP's string-literal escaping below. We parse wp-config.php as plain text (without
            // loading WordPress), so the raw match still contains source-level escapes: the WP installer
            // writes these constants single-quoted through addcslashes( $value, "\\'" ), and a
            // hand-edited password such as "xxx\$xxx" resolves to xxx$xxx at runtime. Left uncorrected,
            // the credentials reach mysqli_connect() corrupted. See CU-7epz71.
            if ( preg_match( "/define\s*\(\s*['\"]" . $constant_name . "['\"]\s*,\s*(['\"])(.*?)\\1\s*\)/", $content, $result ) ) {
                $credentials[ $credential ] = $this->unescape_wp_config_value( $result[2], $result[1] );
            } else {
                return false;
            }
        }


        // Parse DB_HOST to support host:port and UNIX socket configurations (e.g. localhost:/var/run/mysqld/mysqld.sock or :/path/to/socket).
        $db_host_parsed = $this->parse_db_host( $credentials['db_host'] );
        if ( false === $db_host_parsed ) {
            return false;
        }
        list( $db_host, $db_port, $db_socket ) = $db_host_parsed;

        // Since PHP 8.1 mysqli throws exceptions by default; silence them so a failed
        // connection returns false and the admin-ajax fallback takes over instead of a fatal error.
        mysqli_report( MYSQLI_REPORT_OFF );

        $this->connection = @mysqli_connect( $db_host, $credentials['db_user'], $credentials['db_password'], $credentials['db_name'], $db_port, $db_socket );

        // Check connection
        if ( mysqli_connect_errno() || ! $this->connection ) {
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

        // On multisite, $table_prefix from wp-config.php is the network base prefix. Subsites (blog_id > 1)
        // store their tables under base_prefix . blog_id . '_' (see wpdb::get_blog_prefix()). See CU-869fh67ce.
        $blog_id = isset( $_POST['blog_id'] ) ? (int) $_POST['blog_id'] : 1;
        if ( $blog_id > 1 ) {
            $this->table_prefix .= $blog_id . '_';
        }

        return true;
    }

    /**
     * Reverse PHP's string-literal escaping for a value read textually from wp-config.php.
     *
     * connect_to_db() parses wp-config.php as plain text (without loading WordPress), so the
     * captured value still contains source-level escape sequences. This restores the value that
     * PHP would produce at runtime, so credentials such as a password defined as "xxx\$xxx" (which
     * resolves to xxx$xxx) connect correctly. See CU-7epz71.
     *
     * @param string $value Raw value captured from between the quotes.
     * @param string $quote The quote character used in the source ( ' or " ).
     * @return string
     */
    protected function unescape_wp_config_value( $value, $quote ) {
        if ( $quote === "'" ) {
            // Single-quoted PHP strings only treat \' and \\ as escapes.
            return preg_replace( '/\\\\([\\\\\'])/', '$1', $value );
        }

        // Double-quoted PHP strings interpret C-style escapes (\n, \t, \\, ...) plus \" and \$.
        return stripcslashes( $value );
    }

    /**
     * Parse the DB_HOST string into host, port and socket components.
     *
     * Mirrors the parsing logic from WordPress core wpdb so that configurations
     * relying solely on UNIX socket connections (e.g. ":/var/run/mysqld/mysqld.sock"
     * or "localhost:/var/run/mysqld/mysqld.sock") are connected to correctly.
     *
     * @param string $db_host Raw DB_HOST value from wp-config.php.
     * @return array|false    [ host, port|null, socket|null ] or false on parse failure.
     */
    protected function parse_db_host( $db_host ) {
        $host   = $db_host;
        $port   = null;
        $socket = null;

        // First peel off the socket parameter from the right, if it exists.
        $socket_pos = strpos( $host, ':/' );
        if ( false !== $socket_pos ) {
            $socket = substr( $host, $socket_pos + 1 );
            $host   = substr( $host, 0, $socket_pos );
        }

        // IPv6 host: contains more than one colon and may be wrapped in [].
        if ( substr_count( $host, ':' ) > 1 ) {
            $pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
        } else {
            $pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
        }

        $matches = array();
        if ( 1 !== preg_match( $pattern, $host, $matches ) ) {
            return false;
        }

        $host = isset( $matches['host'] ) ? $matches['host'] : '';
        $port = ! empty( $matches['port'] ) ? (int) $matches['port'] : null;

        return array( $host, $port, $socket );
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
        try {
            $result = mysqli_query( $this->connection, $sql );
        }catch(Throwable $e){
            $this->return_error();
        }
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
            echo json_encode( $dictionary_by_original, self::SAFE_JSON_FLAGS );
        }

    }

    /**
     * Return error in case of connection fail and other problems.
     */
    protected function return_error(){
        echo json_encode( 'error', self::SAFE_JSON_FLAGS );
        exit;
    }
}

new TRP_Ajax;

die();

