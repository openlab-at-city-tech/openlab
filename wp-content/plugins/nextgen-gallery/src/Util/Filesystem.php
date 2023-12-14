<?php

namespace Imagely\NGG\Util;

use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\Display\StaticPopeAssets;

class Filesystem {

	protected static $_instances = [];

	public $_document_root;

	/**
	 * @return Filesystem
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new Filesystem();
		}
		return self::$_instances[ $context ];
	}

	public function __construct() {
		$this->_document_root = $this->set_document_root( ABSPATH );
	}

	/**
	 * Gets the document root for this application
	 *
	 * @param string $type Must be one of plugins, plugins_mu, templates, styles, content, gallery, or root
	 * @return string
	 */
	public function get_document_root( $type = 'root' ) {
		switch ( $type ) {
			case 'plugins':
			case 'plugin':
				$retval = WP_PLUGIN_DIR;
				break;
			case 'plugins_mu':
			case 'plugin_mu':
				$retval = WPMU_PLUGIN_DIR;
				break;
			case 'templates':
			case 'template':
			case 'themes':
			case 'theme':
				$retval = \get_template_directory();
				break;
			case 'styles':
			case 'style':
			case 'stylesheets':
			case 'stylesheet':
				$retval = \get_stylesheet_directory();
				break;
			case 'content':
				$retval = WP_CONTENT_DIR;
				break;
			case 'gallery':
			case 'galleries':
				$root_type = NGG_GALLERY_ROOT_TYPE;
				if ( 'content' == $root_type ) {
					$retval = WP_CONTENT_DIR;
				} else {
					$retval = $this->_document_root;
				}
				break;
			default:
				$retval = $this->_document_root;
		}

		return \wp_normalize_path( $retval );
	}

	public function get_absolute_path( $path ) {
		$parts     = \array_filter( \explode( DIRECTORY_SEPARATOR, $path ), 'strlen' );
		$absolutes = [];
		foreach ( $parts as $part ) {
			if ( '.' == $part ) {
				continue;
			}

			if ( '..' == $part ) {
				\array_pop( $absolutes );
			} else {
				$absolutes[] = $part;
			}
		}

		return \wp_normalize_path( \implode( DIRECTORY_SEPARATOR, $absolutes ) );
	}

	/**
	 * Sets the document root for this application
	 *
	 * @param string $value
	 * @return string
	 */
	public function set_document_root( $value ) {
		// some web servers like home.pl and PhpStorm put the document root in "/" or (even weirder) "//".
		if ( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR == $value ) {
			$value = DIRECTORY_SEPARATOR;
		}

		if ( DIRECTORY_SEPARATOR !== $value ) {
			$value = \rtrim( $value, '/\\' );
		}

		return ( $this->_document_root = $value );
	}

	public function add_trailing_slash( $path ) {
		return \rtrim( $path, '/\\' ) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns a calculated path to a file.
	 *
	 * This is used *once* by Pro's ecommerce module and cannot be removed just yet.
	 * TODO: remove this eventually.
	 *
	 * @param string       $path
	 * @param string|false $module (optional)
	 * @deprecated Use Imagely\NGG\Display\StaticAssets::get_abspath()
	 * @return string
	 */
	public function get_abspath( $path, $module = false ) {
		return StaticPopeAssets::get_abspath( $path, $module );
	}

	/**
	 * Gets the absolute path to a file/directory for a specific Pope product. If the path doesn't exist, then NULL is returned.
	 *
	 * @param string       $path
	 * @param string|false $module (optional)
	 * @param bool         $relpath (optional)
	 * @param array        $search_paths (optional)
	 * @deprecated This is only used by NextGEN Pro's comments module and should not be adopted in new code.
	 * @return string|NULL
	 */
	public function find_abspath( $path, $module = false ) {
		if ( \strpos( $path, '#' ) !== false ) {
			$parts = \explode( '#', $path );
			if ( \count( $parts ) === 2 ) {
				$path   = $parts[1];
				$module = $parts[0];
			} else {
				$path = $parts[0];
			}
		}

		if ( ! $module ) {
			die(
				\sprintf(
					'find_abspath requires a path and module. Received %s and %s',
					$path,
					\strval( $module )
				)
			);
		}

		$module_dir = \C_Component_Registry::get_instance()->get_module_dir( $module );
		$path       = \preg_replace( '#^/{1,2}#', '', $path, 1 );

		$retval = \path_join(
			$module_dir,
			$path
		);

		// Adjust for windows paths.
		return \wp_normalize_path( $retval );
	}

	/**
	 * @param string $abspath
	 * @return bool
	 */
	public function delete( $abspath ) {
		$retval = false;

		if ( \file_exists( $abspath ) ) {
			// Delete single file.
			if ( \is_file( $abspath ) ) {
				@\wp_delete_file( $abspath );
			} else {
				// Delete directory.
				foreach ( \scandir( $abspath ) as $relpath ) {
					if ( \in_array( $relpath, [ '.', '..' ] ) ) {
						continue;
					}
					$sub_abspath = $this->join_paths( $abspath, $relpath );
					$this->delete( $sub_abspath );
				}
			}

			$retval = ! \file_exists( $abspath );
		}

		return $retval;
	}

	/**
	 * Joins multiple path segments together
	 *
	 * @deprecated use path_join() instead when you can
	 * @return string
	 */
	public function join_paths() {
		$segments = [];
		$retval   = [];
		$params   = func_get_args();

		$this->_flatten_array( $params, $segments );

		foreach ( $segments as $segment ) {
			$segment = trim( $segment, '/\\' );
			$pieces  = array_values( \preg_split( '#[/\\\\]#', $segment ) );
			$segment = join( DIRECTORY_SEPARATOR, $pieces );
			if ( ! $retval ) {
				$retval = $segment;
			} elseif ( strpos( $segment, $retval ) !== false ) {
				$retval = $segment;
			} else {
				$retval = $retval . DIRECTORY_SEPARATOR . $segment;
			}
		}

		if ( strpos( $retval, $this->get_document_root() ) !== 0 && ( strtoupper( substr( PHP_OS, 0, 3 ) ) != 'WIN' ) ) {
			$retval = DIRECTORY_SEPARATOR . trim( $retval, '/\\' );
		}

		// Check for and adjust Windows UNC paths (\\server\share\) for network mounted sites.
		if ( ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) && substr( $this->get_document_root(), 0, 2 ) === '\\\\' ) {
			$retval = '\\\\' . $retval;
		}

		return $retval;
	}

	protected function _flatten_array( $obj, &$arr ) {
		if ( \is_array( $obj ) ) {
			foreach ( $obj as $inner_obj ) {
				$this->_flatten_array( $inner_obj, $arr );
			}
		} elseif ( $obj ) {
			$arr[] = $obj;
		}
	}

	/**
	 * Parses the path for a module and filename
	 *
	 * @param string $str
	 * @return array [path => module]
	 */
	public function parse_formatted_path( $str ) {
		$module = false;
		$path   = $str;
		$parts  = explode( '#', $path );
		if ( count( $parts ) > 1 ) {
			$module = array_shift( $parts );
			$path   = array_shift( $parts );
		}

		return [ $path, $module ];
	}

	/**
	 * Empties a directory of all of its content
	 *
	 * @param string $directory Absolute path
	 * @param bool   $recursive Remove files from subdirectories of the cache
	 * @param string $regex (optional) Only remove files matching pattern; '/^.+\.png$/i' will match all .png
	 */
	public function flush_directory( $directory, $recursive = true, $regex = null ) {
		// It is possible that the cache directory has not been created yet.
		if ( ! is_dir( $directory ) ) {
			return;
		}

		if ( $recursive ) {
			$directory = new \DirectoryIterator( $directory );
		} else {
			$directory = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $directory ),
				\RecursiveIteratorIterator::CHILD_FIRST
			);
		}

		if ( ! is_null( $regex ) ) {
			$iterator = new \RegexIterator( $directory, $regex, \RecursiveRegexIterator::GET_MATCH );
		} else {
			$iterator = $directory;
		}

		foreach ( $iterator as $file ) {
			if ( $file->isFile() || $file->isLink() ) {
				@unlink( $file->getPathname() );
			} elseif ( $file->isDir() && ! $file->isDot() && $recursive ) {
				@rmdir( $file->getPathname() );
			}
		}
	}

	/**
	 * Flushes cache from all available galleries
	 *
	 * @param array $galleries When provided only the requested galleries' cache is flushed
	 */
	public function flush_galleries( $galleries = [] ) {
		global $wpdb;

		if ( empty( $galleries ) ) {
			$galleries = GalleryMapper::get_instance()->find_all();
		}

		foreach ( $galleries as $gallery ) {
			StorageManager::get_instance()->flush_cache( $gallery );
		}

		// Remove images still in the DB whose gallery no longer exists.
		$wpdb->query( "DELETE FROM `{$wpdb->nggpictures}` WHERE `galleryid` NOT IN (SELECT `gid` FROM `{$wpdb->nggallery}`)" );
	}
}
