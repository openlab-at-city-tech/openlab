<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// State of one export or import job, kept in its own option (trp_import_export_job_export / _import).
class TRP_Import_Export_Job {

    const OPTION_NAME = 'trp_import_export_job';
    const DIR_NAME    = 'translatepress-import-export';
    const MAX_AGE     = 86400;
    const TYPES       = array( 'export', 'import' );

    public $id        = '';
    public $type      = 'export';
    public $status    = 'running';
    public $created   = 0;
    public $updated   = 0;
    public $steps     = array();
    public $current   = 0;
    public $cursor    = array();
    public $options   = array();
    public $source    = '';
    public $dir       = '';
    public $file      = '';
    public $file_size = 0;
    public $preview   = array();
    public $summary   = array();

    public function __construct( $data = array() ) {
        foreach ( $data as $key => $value ) {
            if ( property_exists( $this, $key ) ) {
                $this->$key = $value;
            }
        }
    }

    public static function load( $type ) {
        $data = get_option( self::OPTION_NAME . '_' . $type, array() );
        return ( is_array( $data ) && ! empty( $data['id'] ) ) ? new self( $data ) : null;
    }

    // An unfinished job does not survive a reload; a ready export and an import waiting for its review do.
    public static function delete_unfinished() {
        $keep    = array( 'export' => 'ready', 'import' => 'analyzed' );
        $stopped = array();
        foreach ( self::TYPES as $type ) {
            $job = self::load( $type );
            // A step still running from before the reload holds the lock; the next page load deletes that job.
            if ( $job && $job->status !== $keep[ $type ] && self::acquire_lock( $type ) ) {
                if ( $job->status === 'running' ) {
                    $stopped[] = $type;
                }
                $job->delete();
            }
        }
        // The job types that were stopped halfway, so the page can say so.
        return $stopped;
    }

    // Held until the request ends, so two requests never work on the same job.
    public static function acquire_lock( $type ) {
        global $wpdb;
        $name = 'trp_ie_' . md5( DB_NAME . '|' . $wpdb->prefix . '|' . $type );
        return (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK( %s, 0 )', $name ) ) === '1';
    }

    public static function start_new( $type, $source = '' ) {
        $previous = self::load( $type );
        if ( $previous ) {
            $previous->delete();
        }

        $job          = new self();
        // Random suffix: nginx ignores the .htaccess guard, so the path must not be guessable.
        $job->id      = uniqid( 'trpie', false ) . wp_generate_password( 12, false, false );
        $job->type    = $type;
        $job->source  = $source;
        $job->created = time();
        // The id is in the run and download URLs, so the folder gets a secret of its own.
        $job->dir     = self::get_base_dir() . $job->id . '-' . wp_generate_password( 24, false, false ) . '/';

        if ( ! wp_mkdir_p( $job->dir ) ) {
            return new WP_Error( 'trp_ie_mkdir', __( 'The file could not be saved on the server. Check that the uploads folder is writable.', 'translatepress-multilingual' ) );
        }
        $job->protect_dir();
        $job->save();

        return $job;
    }

    public static function get_base_dir() {
        $uploads = wp_upload_dir();
        return trailingslashit( $uploads['basedir'] ) . self::DIR_NAME . '/';
    }

    public function protect_dir() {
        foreach ( array( self::get_base_dir(), $this->dir, $this->dir . 'dictionary/', $this->dir . 'gettext/' ) as $dir ) {
            if ( ! is_dir( $dir ) ) {
                continue;
            }
            if ( ! file_exists( $dir . 'index.php' ) ) {
                file_put_contents( $dir . 'index.php', "<?php\n// Silence is golden.\n" );//phpcs:ignore
            }
            if ( ! file_exists( $dir . '.htaccess' ) ) {
                file_put_contents( $dir . '.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nOrder deny,allow\nDeny from all\n</IfModule>\n" );//phpcs:ignore
            }
        }
    }

    public function save() {
        $this->updated = time();
        update_option( self::OPTION_NAME . '_' . $this->type, get_object_vars( $this ), false );
    }

    public function delete() {
        if ( $this->dir !== '' ) {
            self::delete_dir( $this->dir );
        }
        delete_option( self::OPTION_NAME . '_' . $this->type );
    }

    // There is no scheduled cleanup: jobs and orphan work directories older than MAX_AGE go whenever the page loads.
    public static function cleanup_stale() {
        $keep = array();
        foreach ( self::TYPES as $type ) {
            $job = self::load( $type );
            if ( $job && ( time() - $job->updated ) > self::MAX_AGE ) {
                $job->delete();
            } elseif ( $job ) {
                $keep[] = basename( $job->dir );
            }
        }

        $base    = self::get_base_dir();
        $entries = is_dir( $base ) ? @scandir( $base ) : false;//phpcs:ignore
        if ( ! is_array( $entries ) ) {
            return;
        }
        foreach ( $entries as $entry ) {
            if ( strpos( $entry, 'trpie' ) !== 0 || in_array( $entry, $keep, true ) || ! is_dir( $base . $entry ) ) {
                continue;
            }
            $mtime = @filemtime( $base . $entry );//phpcs:ignore
            if ( $mtime === false || ( time() - $mtime ) > self::MAX_AGE ) {
                self::delete_dir( $base . $entry );
            }
        }
    }

    // Recursive delete, only ever inside the base dir.
    public static function delete_dir( $dir ) {
        $base = self::get_base_dir();
        $dir  = trailingslashit( $dir );
        if ( strpos( $dir, $base ) !== 0 || $dir === $base || ! is_dir( $dir ) ) {
            return;
        }
        $items = @scandir( $dir );//phpcs:ignore
        if ( is_array( $items ) ) {
            foreach ( $items as $item ) {
                if ( $item === '.' || $item === '..' ) {
                    continue;
                }
                $path = $dir . $item;
                if ( is_dir( $path ) && ! is_link( $path ) ) {
                    self::delete_dir( $path );
                } else {
                    @unlink( $path );//phpcs:ignore
                }
            }
        }
        @rmdir( $dir );//phpcs:ignore
    }

    public function get_current_step() {
        return $this->steps[ $this->current ];
    }

    public function update_current_step( $data ) {
        $this->steps[ $this->current ] = array_merge( $this->steps[ $this->current ], $data );
    }

    // False when there are no steps left.
    public function advance_step() {
        $this->cursor = array();
        $this->current++;
        return isset( $this->steps[ $this->current ] );
    }

    // A step without a row total counts as one unit, complete once the step is done.
    public function get_percent() {
        $total     = 0;
        $processed = 0;
        foreach ( $this->steps as $step ) {
            $size       = max( 1, (int) $step['total'] );
            $total     += $size;
            $processed += $step['done'] ? $size : ( $step['total'] > 0 ? min( $size, (int) $step['processed'] ) : 0 );
        }
        return $total > 0 ? (int) min( 100, round( ( $processed / $total ) * 100 ) ) : 100;
    }
}
