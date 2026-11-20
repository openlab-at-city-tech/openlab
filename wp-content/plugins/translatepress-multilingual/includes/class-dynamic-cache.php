<?php
/**
 * IndexedDB cache for dynamic translations: one site-wide generation without per-string registration.
 *
 * Serving is on by default and can be turned off with the 'trp_enable_indexeddb_cache' filter.
 * Invalidation does not depend on that filter: it runs whenever the generation option exists,
 * so turning serving off and on again cannot expose translations changed in the meantime.
 *
 * The generation option is only read and written with raw SQL, never get_option()/update_option(),
 * so a persistent object cache cannot serve a stale value and every bump is a single atomic UPDATE.
 *
 * Only dictionary writes and plugin version changes invalidate. Settings and other plugins cannot
 * change a cached answer: trp-ajax.php reads nothing but the request and the dictionary, and
 * admin-ajax must agree with it.
 */
require_once dirname( __FILE__ ) . '/class-dynamic-cache-store.php';

class TRP_Dynamic_Cache {
    const LOCK_TIMEOUT = 5;

    /** Automatic first fills run while a page renders, so they wait less for the lock. */
    const FIRST_FILL_LOCK_TIMEOUT = 1;

    /** Set while the database cannot take named locks; pages stop publishing the cache meanwhile. */
    const LOCK_UNAVAILABLE_TRANSIENT = 'trp_dynamic_cache_lock_unavailable';

    /** Columns whose change can make a cached translation stale. */
    const TRACKED_COLUMNS = array( 'original', 'translated', 'status', 'block_type' );

    private static $installed = null;

    /** Whether pages publish the cache configuration. Does not affect invalidation. */
    public static function enabled() {
        if ( get_transient( self::LOCK_UNAVAILABLE_TRANSIENT ) ) {
            return false;
        }

        return (bool) apply_filters( 'trp_enable_indexeddb_cache', true );
    }

    /** Whether the generation option exists; read once per request. */
    public static function installed() {
        if ( self::$installed === null ) {
            $store = self::store();
            self::$installed = $store && $store->state() !== false;
        }

        return self::$installed;
    }

    public static function scope( $source, $language ) {
        global $wpdb;
        if ( !trp_is_valid_language_code( $source ) || !trp_is_valid_language_code( $language ) ) {
            return false;
        }

        $table = TRP_Dynamic_Cache_Store::table_name( $wpdb->prefix, $source, $language );

        // A filtered table name is not one the lightweight endpoint can rebuild.
        $filtered_table = apply_filters( 'trp_table_name_dictionary', $table, $wpdb->prefix, $language, $source );

        return $filtered_table === $table ? $table : false;
    }

    public static function config( $source, $language ) {
        if ( !self::enabled() || !self::scope( $source, $language ) ) {
            return false;
        }

        $store = self::store();
        if ( !$store ) {
            return false;
        }

        $generation = $store->generation( $source, $language );

        // Created on the first page that publishes the cache, not on every request.
        if ( !$generation && self::install() ) {
            $generation = $store->generation( $source, $language );
        }

        if ( !$generation ) {
            return false;
        }

        // This token is cached with the page HTML and can be older than the server's revision, so
        // browsers only serve stored strings while a live response has confirmed the revision
        // within the last revalidate_after seconds. 0 confirms it once on every page view.
        $revalidate_after = (int) apply_filters( 'trp_dynamic_cache_revalidate_after', 30 * MINUTE_IN_SECONDS );

        return array(
            'protocol'         => 2,
            'site'             => untrailingslashit( get_option( 'home' ) ),
            'source'           => $source,
            'language'         => $language,
            'generation'       => $generation,
            'revalidate_after' => max( 0, $revalidate_after ),
        );
    }

    private static function store() {
        global $wpdb;
        if ( !$wpdb->dbh instanceof mysqli ) {
            return false;
        }

        return new TRP_Dynamic_Cache_Store( $wpdb->dbh, $wpdb->prefix );
    }

    /** Clients only request an envelope when the page published a configuration. */
    public static function envelope( $rows, $source, $language, $skip_machine ) {
        if ( !self::scope( $source, $language ) ) {
            return $rows;
        }

        $store = self::store();
        if ( !$store ) {
            return $rows;
        }

        $response = $store->cache_rows( $rows, $source, $language, $skip_machine );

        return $response === false ? $rows : $response;
    }

    /** MySQL lock names are server-wide, so the database name keeps sites on a shared server apart. */
    private static function lock_name( $db, $table ) {
        return 'trp_dc_' . md5( $db->dbname . '.' . $table );
    }

    /**
     * Serialize dictionary writes and read the previous values of the rows being saved.
     *
     * A named lock works on MySQL and MariaDB, inside or outside a caller's transaction.
     * Automatic first fills ($detect_changes = false) only take the lock: an editor save that
     * read a row as untranslated must not be overtaken by a machine translation that a
     * browser caches before the editor's value is written.
     *
     * Without the lock a concurrent write may be missed, so the save invalidates instead. A timeout
     * (GET_LOCK returns 0) is rare: holders only keep the lock for one write. A database that
     * cannot take named locks at all (NULL) would invalidate on every save, so pages stop
     * publishing the cache for a day while that keeps happening.
     */
    public static function before_update( $db, $table, $strings, $columns, $detect_changes = true ) {
        if ( !self::installed() ) {
            return false;
        }

        $context = array(
            'locked'     => false,
            'invalidate' => false,
            'originals'  => array(),
            'lock'       => self::lock_name( $db, $table ),
        );

        $timeout           = $detect_changes ? self::LOCK_TIMEOUT : self::FIRST_FILL_LOCK_TIMEOUT;
        $lock_result       = $db->get_var( $db->prepare( 'SELECT GET_LOCK(%s,%d)', $context['lock'], $timeout ) );
        $context['locked'] = $lock_result === '1';
        if ( !$context['locked'] ) {
            if ( $lock_result === null ) {
                set_transient( self::LOCK_UNAVAILABLE_TRANSIENT, 1, DAY_IN_SECONDS );
            }

            $context['invalidate'] = true;
            return $context;
        }

        $changed_columns = array_intersect( $columns, self::TRACKED_COLUMNS );
        if ( !$detect_changes || !$changed_columns ) {
            return $context;
        }

        $previous = self::previous_rows( $db, $table, $strings, $columns );
        if ( $previous === false ) {
            $context['invalidate'] = true;
            return $context;
        }

        $saves_original = in_array( 'original', $columns, true );
        foreach ( $strings as $string ) {
            $id  = isset( $string['id'] ) ? (int) $string['id'] : 0;
            $old = isset( $previous[ $id ] ) ? $previous[ $id ] : null;

            // New rows and renamed originals are checked for duplicates in after_update().
            if ( $saves_original && self::is_new_original( $old, $string ) ) {
                $context['originals'][] = $string['original'];
            }

            if ( $old && self::is_cached_row( $old ) && self::row_changed( $old, $string, $changed_columns ) ) {
                $context['invalidate'] = true;
            }
        }

        return $context;
    }

    /** Current values of the rows being updated by id, or false when they could not be read. */
    private static function previous_rows( $db, $table, $strings, $columns ) {
        if ( !in_array( 'id', $columns, true ) ) {
            return array();
        }

        $ids = array_filter( array_map( 'intval', array_column( $strings, 'id' ) ) );
        if ( !$ids ) {
            return array();
        }

        $previous = $db->get_results( "SELECT id,original,translated,status,block_type FROM `$table` WHERE id IN (" . implode( ',', $ids ) . ')', OBJECT_K );
        if ( $db->last_error !== '' ) {
            return false;
        }

        return $previous;
    }

    private static function is_new_original( $old, $string ) {
        return !$old || (string) $old->original !== (string) $string['original'];
    }

    /** Only rows a browser may have cached can become stale. */
    private static function is_cached_row( $row ) {
        return TRP_Dynamic_Cache_Store::is_cacheable_translation( (array) $row );
    }

    private static function row_changed( $old, $string, $changed_columns ) {
        foreach ( $changed_columns as $column ) {
            if ( $old->$column === null ) {
                return true;
            }

            $numeric   = in_array( $column, array( 'status', 'block_type' ), true );
            $new_value = $numeric ? (int) $string[ $column ] : (string) $string[ $column ];
            $old_value = $numeric ? (int) $old->$column : (string) $old->$column;
            if ( $old_value !== $new_value ) {
                return true;
            }
        }

        return false;
    }

    /** Finish change detection while still serialized, then release the lock. */
    public static function after_update( $db, $table, $context, $result ) {
        if ( !$context ) {
            return false;
        }

        $error = $db->last_error;

        // A new row for an already-used original is not necessarily a new cache key.
        if ( $context['locked'] && $result > 0 && !$context['invalidate'] && $context['originals'] ) {
            $context['invalidate'] = self::has_cached_duplicate( $db, $table, array_unique( $context['originals'] ) );
        }

        if ( $context['locked'] ) {
            $db->query( $db->prepare( 'SELECT RELEASE_LOCK(%s)', $context['lock'] ) );
        }

        // Invalid-text split retries rely on the error from the original save.
        $db->last_error = $error;

        return $context['invalidate'];
    }

    /** Whether one of the originals now has several rows and at least one of them is cached. */
    private static function has_cached_duplicate( $db, $table, $originals ) {
        $placeholders = implode( ',', array_fill( 0, count( $originals ), '%s' ) );
        $statuses     = implode( ',', TRP_Dynamic_Cache_Store::CACHEABLE_STATUSES );
        $duplicate    = $db->get_var( $db->prepare(
            "SELECT original FROM `$table`
            WHERE original IN ($placeholders)
            GROUP BY original
            HAVING COUNT(*) > 1 AND SUM(status IN ($statuses) AND TRIM(COALESCE(translated,'')) <> '') > 0
            LIMIT 1",
            $originals
        ) );

        return $duplicate !== null || $db->last_error !== '';
    }

    /** Bump the revision after a successful dictionary change, once its lock is released. */
    public static function invalidate() {
        if ( self::bump() ) {
            return true;
        }

        // Retry a transient failure once at shutdown without changing the save result.
        // Registering the same callback again is a no-op, so several failures retry once.
        add_action( 'shutdown', array( __CLASS__, 'retry_invalidation' ), PHP_INT_MAX );

        return false;
    }

    public static function retry_invalidation() {
        self::bump();
    }

    private static function bump() {
        global $wpdb;
        // One statement, so concurrent saves cannot lose a bump. The +1 keeps the revision growing when
        // saves share a millisecond or server clocks differ; the timestamp moves a restored backup ahead.
        $result = $wpdb->query( $wpdb->prepare(
            "UPDATE `{$wpdb->options}` SET option_value=GREATEST(CAST(option_value AS UNSIGNED)+1,%d) WHERE option_name=%s",
            (int) floor( microtime( true ) * 1000 ),
            TRP_Dynamic_Cache_Store::OPTION
        ) );

        return $result !== false;
    }

    /**
     * Add the generation option at revision 0, the only time 0 is written, so browsers start over.
     * An existing valid revision is kept, which keeps concurrent first requests from racing;
     * an unreadable value is replaced.
     */
    public static function install() {
        global $wpdb;
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO `{$wpdb->options}` (option_name,option_value,autoload) VALUES (%s,'0','off')
            ON DUPLICATE KEY UPDATE option_value=IF(option_value REGEXP '^[0-9]{1,15}$',option_value,'0')",
            TRP_Dynamic_Cache_Store::OPTION
        ) );
        self::$installed = null;

        return self::installed();
    }
}
