<?php
// phpcs:disable WordPress.WP.AlternativeFunctions
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface for the filesystem.
 *
 * @since 1.0.0
 */
class Filesystem {
	/**
	 * Holds the WP filesystem instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WP_Filesystem_Base
	 */
	public $fs = null;

	/**
	 * Core class instance.
	 *
	 * @since 1.1.0
	 *
	 * @var Core
	 */
	public $core = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Core  $core The Core class.
	 * @param array $args Optional arguments needed to construct the class with.
	 */
	public function __construct( $core, $args = [] ) {
		$this->core = $core;
		$this->init( $args );
	}

	/**
	 * Initialize the filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args List of arguments for the WP_Filesystem class.
	 * @return void
	 */
	public function init( $args = [] ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem( $args );

		global $wp_filesystem;
		if ( is_object( $wp_filesystem ) ) {
			$this->fs = $wp_filesystem;
		}
	}

	/**
	 * Wrapper method to check if a file exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $filename The filename to check if it exists.
	 * @return bool             Returns true if the file or dir specified exists; false otherwise.
	 */
	public function exists( $filename ) {
		if ( ! $this->isWpfsValid() ) {
			return @file_exists( $filename );
		}

		return $this->fs->exists( $filename );
	}

	/**
	 * Retrieve the contents of a file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $filename The filename to get the contents for.
	 * @return string|bool           The function returns the read data or false on failure.
	 */
	public function getContents( $filename ) {
		if ( ! $this->exists( $filename ) ) {
			return false;
		}

		if ( ! $this->isWpfsValid() ) {
			return @file_get_contents( $filename );
		}

		return $this->fs->get_contents( $filename );
	}

	/**
	 * Reads entire file into an array.
	 *
	 * @since 1.0.0
	 *
	 * @param  string     $file Path to the file.
	 * @return array|bool       File contents in an array on success, false on failure.
	 */
	public function getContentsArray( $file ) {
		if ( ! $this->exists( $file ) ) {
			return false;
		}

		if ( ! $this->isWpfsValid() ) {
			return @file( $file );
		}

		return $this->fs->get_contents_array( $file );
	}

	/**
	 * Sets the access and modification times of a file.
	 * Note: If $file doesn't exist, it will be created.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file  Path to file.
	 * @param  int    $time  Optional. Modified time to set for file. Default 0.
	 * @param  int    $atime Optional. Access time to set for file. Default 0.
	 * @return bool          True on success, false on failure.
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		if ( 0 === $time ) {
			$time = time();
		}

		if ( 0 === $atime ) {
			$atime = time();
		}

		if ( ! $this->isWpfsValid() ) {
			return @touch( $file, $time, $atime );
		}

		return $this->fs->touch( $file, $time, $atime );
	}

	/**
	 * Writes a string to a file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string    $file     Remote path to the file where to write the data.
	 * @param  string    $contents The data to write.
	 * @param  int|false $mode     Optional. The file permissions as octal number, usually 0644. Default false.
	 * @return int|bool            True on success, false on failure.
	 */
	public function putContents( $file, $contents, $mode = false ) {
		if ( ! $this->isWpfsValid() ) {
			return @file_put_contents( $file, $contents );
		}

		return $this->fs->put_contents( $file, $contents, $mode );
	}

	/**
	 * Checks if a file or dir is writable.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file Path to file or dir.
	 * @return bool         Whether $file is writable.
	 */
	public function isWritable( $file ) {
		if ( ! $this->isWpfsValid() ) {
			return @is_writable( $file );
		}

		return $this->fs->is_writable( $file );
	}

	/**
	 * Checks if a file is readable.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file Path to file.
	 * @return bool         Whether $file is readable.
	 */
	public function isReadable( $file ) {
		if ( ! $this->isWpfsValid() ) {
			return @is_readable( $file );
		}

		return $this->fs->is_readable( $file );
	}

	/**
	 * Gets the file size (in bytes).
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $file Path to file.
	 * @return int|bool       Size of the file in bytes on success, false on failure.
	 */
	public function size( $file ) {
		if ( ! $this->isWpfsValid() ) {
			return @filesize( $file );
		}

		return $this->fs->size( $file );
	}

	/**
	 * Checks if resource is a file.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file File path.
	 * @return bool         Whether $file is a file.
	 */
	public function isFile( $file ) {
		if ( ! $this->isWpfsValid() ) {
			return @is_file( $file );
		}

		return $this->fs->is_file( $file );
	}

	/**
	 * Checks if resource is a directory.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $path Directory path.
	 * @return bool         Whether $path is a directory.
	 */
	public function isDir( $path ) {
		if ( ! $this->isWpfsValid() ) {
			return @is_dir( $path );
		}

		return $this->fs->is_dir( $path );
	}

	/**
	 * A simple check to ensure that the WP_Filesystem is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if valid, false if not.
	 */
	public function isWpfsValid() {
		if (
			! is_a( $this->fs, 'WP_Filesystem_Base' ) ||
			(
				// Errors is a WP_Error object.
				! empty( $this->fs->errors ) &&
				// We check if the errors array is empty for compatibility with WP < 5.1.
				! empty( $this->fs->errors->errors )
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * In order to prevent conflicts, we need to return a clone.
	 *
	 * @since 1.0.0
	 *
	 * @return Filesystem The cloned Filesystem object.
	 */
	public function noConflict() {
		return clone $this;
	}
}