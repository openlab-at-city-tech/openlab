<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Zip creation and extraction, entry-name validation, manifest and JSONL reading.
class TRP_Import_Export_Archive {

    const FORMAT_VERSION        = 1;
    const MANIFEST              = 'manifest.json';
    const SETTINGS              = 'settings.json';
    // For people who open the export; never read on import.
    const README                = 'README.txt';
    const FIXED_ENTRIES         = array( self::MANIFEST, self::SETTINGS, self::README, 'original-meta.jsonl', 'gettext-original-meta.jsonl', 'slugs.jsonl' );
    const JSON_DEPTH            = 16;
    const MAX_LINE_LENGTH       = 1048576;
    const MAX_JSON_SIZE         = 8388608;
    const MAX_BATCH_BYTES       = 4194304;
    const MAX_UNCOMPRESSED_SIZE = 2147483648;
    const MAX_ENTRIES           = 500;
    // Contents may be at most this many times the zip's size, so a zip bomb stops early.
    const MAX_COMPRESSION_RATIO = 100;

    protected static function error( $kind ) {
        $messages = array(
            'not_export'    => __( 'This .zip file isn\'t a TranslatePress export. On the other site, go to Settings → TranslatePress → Advanced → Import & Export and click Generate export file.', 'translatepress-multilingual' ),
            'not_zip'       => __( 'This file can\'t be opened as a .zip file. It may be damaged, or another kind of file renamed to .zip. Choose the .zip file made with Generate export file on the other site.', 'translatepress-multilingual' ),
            'damaged'       => __( 'This export file is damaged. Generate a new one on the other site.', 'translatepress-multilingual' ),
            'incomplete'    => __( 'This export file is incomplete. Generate a new one on the other site.', 'translatepress-multilingual' ),
            'too_large'     => __( 'This file is too large to import.', 'translatepress-multilingual' ),
            'upload'        => __( 'The file could not be uploaded. Please try again.', 'translatepress-multilingual' ),
            'save'          => __( 'The file could not be saved on the server. Check that the uploads folder is writable.', 'translatepress-multilingual' ),
            'unpack'        => __( 'The file could not be unpacked on the server. The server may be out of disk space, or the uploads folder may not be writable.', 'translatepress-multilingual' ),
            'create'        => __( 'The export file could not be created. Check that the uploads folder is writable.', 'translatepress-multilingual' ),
            'newer_version' => __( 'This file was made with a newer version of TranslatePress. Update TranslatePress on this site, then import it again.', 'translatepress-multilingual' ),
        );
        return new WP_Error( 'trp_ie_' . $kind, $messages[ $kind ] );
    }

    // An eighth of max_allowed_packet (escaping can double a row, a batch can go one line over), capped at MAX_BATCH_BYTES.
    public static function get_max_batch_bytes() {
        global $wpdb;
        $packet = (int) $wpdb->get_var( 'SELECT @@max_allowed_packet' );
        return ( $packet > 0 ) ? min( self::MAX_BATCH_BYTES, max( 65536, (int) floor( $packet / 8 ) ) ) : 1048576;
    }

    protected function get_extraction_cap( $zip_path ) {
        $compressed = (int) @filesize( $zip_path );//phpcs:ignore
        return ( $compressed > 0 ) ? (int) min( self::MAX_UNCOMPRESSED_SIZE, $compressed * self::MAX_COMPRESSION_RATIO ) : self::MAX_UNCOMPRESSED_SIZE;
    }

    // Only the entry names the exporter produces.
    public static function is_allowed_entry_name( $name ) {
        $name = (string) $name;
        if ( $name === '' || strlen( $name ) > 255 ) {
            return false;
        }
        if ( strpos( $name, '..' ) !== false || strpos( $name, '\\' ) !== false || strpos( $name, "\0" ) !== false ) {
            return false;
        }
        if ( $name[0] === '/' || preg_match( '#^[A-Za-z]:#', $name ) ) {
            return false;
        }
        if ( in_array( $name, self::FIXED_ENTRIES, true ) ) {
            return true;
        }
        $code = self::get_language_from_entry_name( $name );
        return $code !== '' && trp_is_valid_language_code( $code );
    }

    // Empty string when the name is not a per-language file.
    public static function get_language_from_entry_name( $name ) {
        return preg_match( '#^(dictionary|gettext)/([A-Za-z0-9\-_]+)\.jsonl$#', (string) $name, $matches ) ? $matches[2] : '';
    }

    // The entry names of an uploaded zip, checked without extracting it.
    public function validate_zip( $zip_path ) {
        if ( ! file_exists( $zip_path ) ) {
            return self::error( 'upload' );
        }

        // Same cap as the extraction, so an archive that declares its bomb is turned away here.
        $max_size = $this->get_extraction_cap( $zip_path );
        $list     = array();
        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive();
            if ( $zip->open( $zip_path ) !== true ) {
                return self::error( 'not_zip' );
            }
            if ( $zip->numFiles > self::MAX_ENTRIES ) {
                $zip->close();
                return self::error( 'not_export' );
            }
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $stat = $zip->statIndex( $i );
                if ( ! is_array( $stat ) || ! isset( $stat['name'] ) ) {
                    $zip->close();
                    return self::error( 'damaged' );
                }
                $list[] = array( 'name' => $stat['name'], 'size' => isset( $stat['size'] ) ? (int) $stat['size'] : 0, 'folder' => substr( $stat['name'], -1 ) === '/' );
            }
            $zip->close();
        } else {
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
            $archive = new PclZip( $zip_path );
            $content = $archive->listContent();
            if ( ! is_array( $content ) ) {
                return self::error( 'not_zip' );
            }
            if ( count( $content ) > self::MAX_ENTRIES ) {
                return self::error( 'not_export' );
            }
            foreach ( $content as $item ) {
                $list[] = array( 'name' => isset( $item['filename'] ) ? $item['filename'] : '', 'size' => isset( $item['size'] ) ? (int) $item['size'] : 0, 'folder' => ! empty( $item['folder'] ) );
            }
        }

        $entries = array();
        $total   = 0;
        foreach ( $list as $item ) {
            // Directory entries are skipped, their files are checked on their own.
            if ( $item['folder'] ) {
                continue;
            }
            if ( ! self::is_allowed_entry_name( $item['name'] ) ) {
                return self::error( 'not_export' );
            }
            $total += $item['size'];
            if ( $total > $max_size ) {
                return self::error( 'too_large' );
            }
            $entries[] = $item['name'];
        }

        if ( ! in_array( self::MANIFEST, $entries, true ) ) {
            return self::error( 'not_export' );
        }
        return $entries;
    }

    // Extract the entries validate_zip() returned.
    public function extract( $zip_path, $destination, $entries ) {
        $destination = trailingslashit( $destination );
        $max_size    = $this->get_extraction_cap( $zip_path );
        $total       = 0;

        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive();
            if ( $zip->open( $zip_path ) !== true ) {
                return self::error( 'not_zip' );
            }
            foreach ( $entries as $name ) {
                if ( ! self::is_allowed_entry_name( $name ) ) {
                    continue;
                }
                $target = $destination . $name;
                if ( ! wp_mkdir_p( dirname( $target ) ) ) {
                    $zip->close();
                    return self::error( 'save' );
                }
                if ( is_link( $target ) ) {
                    @unlink( $target );//phpcs:ignore
                }
                $stream = $zip->getStream( $name );
                if ( ! is_resource( $stream ) ) {
                    $zip->close();
                    return self::error( 'damaged' );
                }
                $out = fopen( $target, 'wb' );//phpcs:ignore
                if ( ! is_resource( $out ) ) {
                    fclose( $stream );//phpcs:ignore
                    $zip->close();
                    return self::error( 'save' );
                }
                $error = '';
                while ( ! feof( $stream ) ) {
                    $chunk = fread( $stream, 262144 );//phpcs:ignore
                    if ( $chunk === false || $chunk === '' ) {
                        break;
                    }
                    // Only the bytes actually written count, not the declared sizes.
                    $total += strlen( $chunk );
                    if ( $total > $max_size ) {
                        $error = 'too_large';
                        break;
                    }
                    if ( fwrite( $out, $chunk ) === false ) {//phpcs:ignore
                        $error = 'unpack';
                        break;
                    }
                }
                fclose( $out );//phpcs:ignore
                fclose( $stream );//phpcs:ignore
                if ( $error !== '' ) {
                    $zip->close();
                    $this->delete_extracted_files( $destination, $entries );
                    return self::error( $error );
                }
            }
            $zip->close();
            return true;
        }

        // unzip_file() inflates everything in memory, so a bomb can only be stopped on the declared sizes, before it runs.
        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
        $archive = new PclZip( $zip_path );
        $list    = $archive->listContent();
        if ( is_array( $list ) ) {
            $declared = 0;
            foreach ( $list as $item ) {
                if ( empty( $item['folder'] ) ) {
                    $declared += isset( $item['size'] ) ? (int) $item['size'] : 0;
                }
            }
            if ( $declared > $max_size ) {
                return self::error( 'too_large' );
            }
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        if ( is_wp_error( unzip_file( $zip_path, untrailingslashit( $destination ) ) ) ) {
            return self::error( 'unpack' );
        }
        foreach ( $entries as $name ) {
            if ( file_exists( $destination . $name ) ) {
                $total += (int) @filesize( $destination . $name );//phpcs:ignore
            }
        }
        if ( $total > $max_size ) {
            $this->delete_extracted_files( $destination, $entries );
            return self::error( 'too_large' );
        }
        // unzip_file() writes every entry, so drop whatever is not on the allow-list.
        $this->prune_unexpected_files( $destination );
        return true;
    }

    protected function delete_extracted_files( $destination, $entries ) {
        foreach ( $entries as $name ) {
            if ( self::is_allowed_entry_name( $name ) && is_file( $destination . $name ) ) {
                @unlink( $destination . $name );//phpcs:ignore
            }
        }
    }

    protected function prune_unexpected_files( $destination ) {
        foreach ( array( '', 'dictionary/', 'gettext/' ) as $sub ) {
            $items = is_dir( $destination . $sub ) ? @scandir( $destination . $sub ) : false;//phpcs:ignore
            if ( ! is_array( $items ) ) {
                continue;
            }
            foreach ( $items as $item ) {
                $path = $destination . $sub . $item;
                if ( in_array( $item, array( '.', '..', 'index.php', '.htaccess' ), true ) || ( is_dir( $path ) && ! is_link( $path ) ) ) {
                    continue;
                }
                if ( is_link( $path ) || ! self::is_allowed_entry_name( $sub . $item ) ) {
                    @unlink( $path );//phpcs:ignore
                }
            }
        }
    }

    // $files: entry name => absolute path.
    // Creates the zip, or adds to the one that is there.
    public function add_to_zip( $zip_path, $files ) {
        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new ZipArchive();
            if ( $zip->open( $zip_path, ZipArchive::CREATE ) !== true ) {
                return self::error( 'create' );
            }
            foreach ( $files as $entry_name => $path ) {
                $zip->addFile( $path, $entry_name );
                // Level 3 of 9: about a third of the default's time for a file about 13% larger.
                $zip->setCompressionName( $entry_name, ZipArchive::CM_DEFLATE, 3 );
            }
            return $zip->close() ? true : self::error( 'create' );
        }

        require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
        $archive = new PclZip( $zip_path );
        $list    = array();
        foreach ( $files as $entry_name => $path ) {
            $list[] = array( PCLZIP_ATT_FILE_NAME => $path, PCLZIP_ATT_FILE_NEW_FULL_NAME => $entry_name );
        }
        return ( $archive->add( $list ) === 0 ) ? self::error( 'create' ) : true;
    }

    public function read_manifest( $dir ) {
        $path = trailingslashit( $dir ) . self::MANIFEST;
        if ( ! file_exists( $path ) ) {
            return self::error( 'not_export' );
        }
        $manifest = $this->read_json_file( $path );
        if ( is_wp_error( $manifest ) ) {
            return $manifest;
        }
        $version = isset( $manifest['format_version'] ) ? (int) $manifest['format_version'] : 0;
        if ( $version !== self::FORMAT_VERSION ) {
            return self::error( $version > self::FORMAT_VERSION ? 'newer_version' : 'not_export' );
        }
        if ( empty( $manifest['default_language'] ) || ! is_string( $manifest['default_language'] ) || ! trp_is_valid_language_code( $manifest['default_language'] ) ) {
            return self::error( 'not_export' );
        }
        if ( ! isset( $manifest['files'] ) || ! is_array( $manifest['files'] ) ) {
            $manifest['files'] = array();
        }
        return $manifest;
    }

    public function read_json_file( $path ) {
        $size = @filesize( $path );//phpcs:ignore
        if ( $size !== false && $size > self::MAX_JSON_SIZE ) {
            return self::error( 'too_large' );
        }
        $contents = file_get_contents( $path );//phpcs:ignore
        $data     = ( $contents === false ) ? null : json_decode( $contents, true, self::JSON_DEPTH );
        return is_array( $data ) ? $data : self::error( 'damaged' );
    }

    public static function encode_line( $row ) {
        return wp_json_encode( $row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }

    // Non-blank lines of a JSONL file; an over-long line counts once, the reader rejects it.
    public static function count_lines( $path ) {
        $handle = @fopen( $path, 'rb' );//phpcs:ignore
        if ( ! is_resource( $handle ) ) {
            return 0;
        }
        $count = 0;
        while ( ( $line = fgets( $handle, self::MAX_LINE_LENGTH ) ) !== false ) {//phpcs:ignore
            if ( ! self::line_is_complete( $line, $handle ) ) {
                $count++;
                self::skip_rest_of_line( $handle );
            } elseif ( trim( $line ) !== '' ) {
                $count++;
            }
        }
        fclose( $handle );//phpcs:ignore
        return $count;
    }

    // False when fgets() returned only the head of an over-long line.
    protected static function line_is_complete( $line, $handle ) {
        return ( substr( $line, -1 ) === "\n" || feof( $handle ) );
    }

    protected static function skip_rest_of_line( $handle ) {
        while ( ( $chunk = fgets( $handle, self::MAX_LINE_LENGTH ) ) !== false ) {//phpcs:ignore
            if ( self::line_is_complete( $chunk, $handle ) ) {
                return;
            }
        }
    }

    // Up to $max_rows decoded rows from a byte offset; stops after $max_bytes, but always reads at least one line.
    public function read_jsonl( $path, $offset, $max_rows, $max_bytes ) {
        if ( ! file_exists( $path ) ) {
            return self::error( 'incomplete' );
        }
        $handle = fopen( $path, 'rb' );//phpcs:ignore
        if ( ! is_resource( $handle ) || ( $offset > 0 && fseek( $handle, $offset ) !== 0 ) ) {
            if ( is_resource( $handle ) ) {
                fclose( $handle );//phpcs:ignore
            }
            return self::error( 'damaged' );
        }

        $rows    = array();
        $read    = 0;
        $invalid = 0;
        $bytes   = 0;
        while ( $read < $max_rows && $bytes < $max_bytes && ( $line = fgets( $handle, self::MAX_LINE_LENGTH ) ) !== false ) {//phpcs:ignore
            $bytes += strlen( $line );
            if ( ! self::line_is_complete( $line, $handle ) ) {
                $read++;
                $invalid++;
                self::skip_rest_of_line( $handle );
                continue;
            }
            $line = trim( $line );
            if ( $line === '' ) {
                continue;
            }
            $read++;
            $decoded = json_decode( $line, true, self::JSON_DEPTH );
            if ( is_array( $decoded ) ) {
                $rows[] = $decoded;
            } else {
                $invalid++;
            }
        }
        $new_offset = ftell( $handle );
        $eof        = feof( $handle );
        fclose( $handle );//phpcs:ignore

        return array(
            'rows'    => $rows,
            'offset'  => ( $new_offset === false ) ? $offset : (int) $new_offset,
            'eof'     => $eof,
            'read'    => $read,
            'invalid' => $invalid,
        );
    }
}
