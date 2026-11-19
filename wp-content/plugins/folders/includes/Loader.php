<?php
namespace Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin class loader.
 *
 * Walks the `includes/` directory and instantiates every concrete class it
 * finds, so each class can register its own hooks in its constructor.
 */
class Loader {

    /**
     * Run the loader to include all necessary files.
     *
     * @return void
     */
    public static function run() {
        self::load_classes( FOLDERS_PLUGIN_DIR . 'includes' );
    }

    /**
     * Recursively load classes from a directory.
     *
     * @param string $dir Directory path.
     *
     * @return void
     */
    private static function load_classes( $dir ) {
        $files = scandir( $dir );

        foreach ( $files as $file ) {
            if ( '.' === $file || '..' === $file ) {
                continue;
            }

            $path = $dir . '/' . $file;

            if ( is_dir( $path ) ) {
                self::load_classes( $path );
            } elseif ( 'php' === pathinfo( $path, PATHINFO_EXTENSION ) ) {
                self::instantiate_class( $path );
            }
        }
    }

    /**
     * Instantiate a class if it exists and is instantiable.
     *
     * @param string $path File path.
     *
     * @return void
     */
    private static function instantiate_class( $path ) {
        // Calculate class name from path.
        $relative_path = str_replace( FOLDERS_PLUGIN_DIR . 'includes/', '', $path );
        $relative_path = str_replace( '.php', '', $relative_path );
        $parts         = explode( '/', $relative_path );
        $class_name    = 'Folders\\' . implode( '\\', $parts );

        // Skip self.
        if ( __CLASS__ === $class_name ) {
            return;
        }

        if ( class_exists( $class_name ) ) {
            try {
                $ref = new \ReflectionClass( $class_name );
                if ( $ref->isInstantiable() ) {
                    new $class_name();
                }
            } catch ( \ReflectionException $e ) {
                // Ignore if class cannot be reflected.
            }
        }
    }
}
