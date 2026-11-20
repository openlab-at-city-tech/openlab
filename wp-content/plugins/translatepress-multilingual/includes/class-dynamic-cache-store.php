<?php
/** Shared database protocol for the lightweight and WordPress dynamic endpoints. */
class TRP_Dynamic_Cache_Store {
    /**
     * Non-autoloaded option holding the cache revision: 0 when created, then a millisecond timestamp
     * that only grows. 0 tells browsers to start over; a restored backup moves ahead on the next save.
     */
    const OPTION = 'trp_dynamic_cache_generation';

    /** Larger requests are answered without cache labels. */
    const MAX_ORIGINALS = 500;

    /**
     * Statuses browsers may cache: machine (1), manual (2) and similar (3, Translation Memory).
     * Every check of what a browser may hold uses this list, so a change to one of these rows
     * always invalidates. Keep CACHEABLE_STATUSES in trp-dynamic-cache.js in sync.
     */
    const CACHEABLE_STATUSES = array( 1, 2, 3 );

    private $connection;
    private $prefix;

    public function __construct( $connection, $prefix ) {
        $this->connection = $connection;
        $this->prefix     = $prefix;
    }

    private function query( $sql ) {
        try {
            return mysqli_query( $this->connection, $sql );
        } catch ( Exception $error ) {
            return false;
        }
    }

    private function quote( $value ) {
        return "'" . mysqli_real_escape_string( $this->connection, $value ) . "'";
    }

    private function row( $sql ) {
        $result = $this->query( $sql );

        return $result ? mysqli_fetch_assoc( $result ) : false;
    }

    /** Dictionary table name, built the same way by both endpoints. Language codes must be validated first. */
    public static function table_name( $prefix, $source, $language ) {
        return $prefix . 'trp_dictionary_' . strtolower( $source ) . '_' . strtolower( $language );
    }

    private function scope( $source, $language ) {
        $valid_code = '/^[a-zA-Z0-9_-]+$/';
        if ( !preg_match( $valid_code, $source ) || !preg_match( $valid_code, $language ) ) {
            return false;
        }

        return self::table_name( $this->prefix, $source, $language );
    }

    /**
     * Read the generation straight from the options table, never through get_option(): the
     * lightweight endpoint has no object cache, so both endpoints must see the same row.
     */
    public function state() {
        $row = $this->row( "SELECT option_value FROM `{$this->prefix}options` WHERE option_name='" . self::OPTION . "' LIMIT 1" );
        if ( !$row || !preg_match( '/^\d{1,15}$/', $row['option_value'] ) ) {
            return false;
        }

        return array(
            'revision' => (int) $row['option_value'],
        );
    }

    public function generation( $source, $language ) {
        $scope = $this->scope( $source, $language );
        $state = $scope ? $this->state() : false;
        if ( !$state ) {
            return false;
        }

        return array(
            'protocol' => 2,
            'revision' => $state['revision'],
            'scope'    => $scope,
        );
    }

    /**
     * Label positive rows with the site generation.
     *
     * Writers save rows before bumping the revision, so reading the revision before the rows
     * can only label newer data with an older revision (which browsers later discard), never
     * older data with a newer one. No transaction is needed on any storage engine.
     *
     * A reply without cacheable rows still carries the generation: nothing in it gets stored, but
     * browsers use it to confirm that what they already hold is current.
     */
    public function cache_rows( $rows, $source, $language, $skip_machine ) {
        $originals = $this->translated_originals( $rows, $language );
        if ( count( $originals ) > self::MAX_ORIGINALS ) {
            return false;
        }

        $snapshot = $this->generation( $source, $language );
        if ( !$snapshot ) {
            return false;
        }

        if ( !$originals ) {
            return array(
                'rows'  => $rows,
                'cache' => $snapshot,
            );
        }

        $fresh = $this->fresh_rows( $snapshot['scope'], $originals, $skip_machine );
        if ( $fresh === false ) {
            return false;
        }

        return array(
            'rows'  => trp_sort_dictionary_by_original( array( $language => $fresh ), 'regular', 'dynamicstrings', null ),
            'cache' => $snapshot,
        );
    }

    /** Originals of the rows that have a usable translation, without duplicates. */
    private function translated_originals( $rows, $language ) {
        $originals = array();
        foreach ( $rows as $row ) {
            $translation = isset( $row['translationsArray'][ $language ] ) ? (array) $row['translationsArray'][ $language ] : array();
            if ( self::is_cacheable_translation( $translation ) ) {
                $originals[ $row['original'] ] = $row['original'];
            }
        }

        return $originals;
    }

    /** Only translations with a cacheable status and text can be served from the browser cache. */
    public static function is_cacheable_translation( $translation ) {
        if ( !isset( $translation['status'], $translation['translated'] ) ) {
            return false;
        }

        if ( !in_array( (int) $translation['status'], self::CACHEABLE_STATUSES, true ) ) {
            return false;
        }

        return trim( (string) $translation['translated'] ) !== '';
    }

    /** Re-read the rows after the generation, so a row is never labelled newer than it is. */
    private function fresh_rows( $table, $originals, $skip_machine ) {
        $quoted = array_map( array( $this, 'quote' ), array_values( $originals ) );
        $result = $this->query( "SELECT original,translated,status FROM `{$table}` WHERE original IN (" . implode( ',', $quoted ) . ') AND status != 0' );
        if ( !$result ) {
            return false;
        }

        $fresh = array();
        while ( $row = mysqli_fetch_assoc( $result ) ) {
            $is_machine_translation = (int) $row['status'] === 1;
            if ( $is_machine_translation && in_array( $row['original'], $skip_machine, true ) ) {
                continue;
            }

            $fresh[] = $row;
        }

        return $fresh;
    }
}
