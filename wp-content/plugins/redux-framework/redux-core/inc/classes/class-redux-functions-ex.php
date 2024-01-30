<?php
/**
 * Redux Framework Private Extended Functions Container Class
 *
 * @class       Redux_Functions_Ex
 * @since       3.0.0
 * @package     Redux_Framework/Classes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Functions_Ex', false ) ) {

	/**
	 * Redux Functions Class
	 * A Class of useful functions that can/should be shared among all Redux files.
	 *
	 * @since       3.0.0
	 */
	class Redux_Functions_Ex {

		/**
		 * What is this for?
		 *
		 * @var array
		 */
		public static $args;

		/**
		 * Enqueue Font Awesome.
		 *
		 * @return void
		 */
		public static function enqueue_font_awesome() {
			wp_enqueue_style(
				'font-awesome',
				Redux_Core::$url . 'assets/font-awesome/css/all' . Redux_Functions::is_min() . '.css',
				array(),
				'6.4.0'
			);

			wp_enqueue_style(
				'font-awesome-4-shims',
				Redux_Core::$url . 'assets/font-awesome/css/v4-shims' . Redux_Functions::is_min() . '.css',
				array(),
				'6.4.0'
			);
		}

		/**
		 * Enqueue Elusive Font.
		 *
		 * @return void
		 */
		public static function enqueue_elusive_font() {
			wp_enqueue_style(
				'redux-elusive-icon',
				Redux_Core::$url . 'assets/css/vendor/elusive-icons' . Redux_Functions::is_min() . '.css',
				array(),
				'2.0.0'
			);
		}

		/**
		 * Shim to load Extendify for backward compatibility.
		 *
		 * @return void
		 */
		public static function load_extendify_css() {
			add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'block_editor_styles' ), 99 );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_init' ), 10 );
		}

		/**
		 * Shim to enqueue Extendify CSS in the block editor.
		 *
		 * @return void
		 */
		public static function block_editor_styles() {
			wp_enqueue_style(
				'redux-editor-styles',
				Redux_Core::$url . 'assets/css/extendify-utilities.css',
				array(),
				Redux_Core::$version
			);
		}

		/**
		 * Scripts to enqueue on the frontend
		 */
		public static function frontend_init() {
			wp_enqueue_style(
				'redux-extendify-styles',
				Redux_Core::$url . 'assets/css/extendify-utilities.css',
				array(),
				Redux_Core::$version
			);
		}

		/**
		 * Output alpha data tag for Iris alpha color picker, if enabled.
		 *
		 * @param array $data Data array.
		 *
		 * @return string
		 */
		public static function output_alpha_data( array $data ): string {
			$index = null;

			extract( $data ); // phpcs:ignore WordPress.PHP.DontExtract

			$value = false;

			if ( isset( $field['color_alpha'] ) && $field['color_alpha'] ) {
				if ( is_array( $field['color_alpha'] ) ) {
					$value = $field['color_alpha'][ $index ] ?? false;
				} else {
					$value = $field['color_alpha'];
				}
			}

			return 'data-alpha-enabled="' . (bool) esc_attr( $value ) . '"';
		}

		/**
		 * Parses the string into variables without the max_input_vars limitation.
		 *
		 * @param string $str String of data.
		 *
		 * @return  array|false $result
		 * @since   3.5.7.11
		 * @author  harunbasic
		 * @access  private
		 */
		public static function parse_str( string $str ) {
			if ( '' === $str ) {
				return false;
			}

			$result = array();
			$pairs  = explode( '&', $str );

			foreach ( $pairs as $pair ) {
				// use the original parse_str() on each element.
				parse_str( $pair, $params );

				$k = key( $params );

				if ( ! isset( $result[ $k ] ) ) {
					$result += $params;
				} elseif ( is_array( $result[ $k ] ) && is_array( $params[ $k ] ) ) {
					$result[ $k ] = self::array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
				}
			}

			return $result;
		}

		/**
		 * Merge arrays without converting values with duplicate keys to arrays as array_merge_recursive does.
		 * As seen here http://php.net/manual/en/function.array-merge-recursive.php#92195
		 *
		 * @param array $array1 array one.
		 * @param array $array2 array two.
		 *
		 * @return  array $merged
		 * @since   3.5.7.11
		 * @author  harunbasic
		 * @access  private
		 */
		public static function array_merge_recursive_distinct( array $array1, array $array2 ): array {
			$merged = $array1;

			foreach ( $array2 as $key => $value ) {

				if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
					$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
				} elseif ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
					$merged[] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			}

			return $merged;
		}

		/**
		 * Records calling function.
		 *
		 * @param string $opt_name Panel opt_name.
		 */
		public static function record_caller( string $opt_name = '' ) {
			global $pagenow;

			// phpcs:ignore WordPress.Security.NonceVerification
			if ( ! ( 'options-general.php' === $pagenow && isset( $_GET['page'] ) && ( 'redux-framework' === $_GET['page'] || 'health-check' === $_GET['page'] ) ) ) {
				return;
			}

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1]['file'];

			if ( ! empty( $caller ) && ! empty( $opt_name ) && class_exists( 'Redux_Core' ) ) {
				if ( ! isset( Redux_Core::$callers[ $opt_name ] ) ) {
					Redux_Core::$callers[ $opt_name ] = array();
				}

				if ( strpos( $caller, 'class-redux-' ) !== false || strpos( $caller, 'redux-core/framework.php' ) ) {
					return;
				}

				if ( ! in_array( $caller, Redux_Core::$callers[ $opt_name ], true ) ) {
					Redux_Core::$callers[ $opt_name ][] = $caller;
				}

				if ( ! empty( self::$args[ $opt_name ]['callers'] ) && ! in_array( $caller, self::$args[ $opt_name ]['callers'], true ) ) {
					self::$args[ $opt_name ]['callers'][] = $caller;
				}
			}
		}

		/**
		 * Normalize path.
		 *
		 * @param string $path Path to normalize.
		 *
		 * @return string|string[]|null
		 */
		public static function wp_normalize_path( string $path = '' ) {
			if ( function_exists( 'wp_normalize_path' ) ) {
				$path = wp_normalize_path( $path );
			} else {
				// Shim for pre WP 3.9.
				$path = str_replace( '\\', '/', $path );
				$path = preg_replace( '|(?<=.)/+|', '/', $path );

				if ( ':' === substr( $path, 1, 1 ) ) {
					$path = ucfirst( $path );
				}
			}

			return $path;
		}

		/**
		 * Action to add generator tag to page HEAD.
		 */
		public static function generator() {
			add_action( 'wp_head', array( 'Redux_Functions_Ex', 'meta_tag' ) );
		}


		/**
		 * Callback for wp_head hook to add meta tag.
		 */
		public static function meta_tag() {
			echo '<meta name="generator" content="Redux ' . esc_html( Redux_Core::$version ) . '" />';
		}

		/**
		 * Run URL through a ssl check.
		 *
		 * @param string $url URL to check.
		 *
		 * @return string
		 */
		public static function verify_url_protocol( string $url ): string {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$protocol = ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] || ( ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) ? 'https://' : 'http://';

			if ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				$new_protocol = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) . '://'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				if ( 'http://' === $protocol && $new_protocol !== $protocol && false === strpos( $url, $new_protocol ) ) {
					$url = str_replace( $protocol, $new_protocol, $url );
				}
			}

			return $url;
		}

		/**
		 * Check s.
		 *
		 * @access public
		 * @return bool
		 * @since  4.0.0
		 */
		public static function s(): bool {
			if ( ! get_option( 'redux_p' . 'ro_lic' . 'ense_key', false ) ) { // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
				$s = get_option( 'redux_p' . 'ro_l' . 'icense_status', false ); // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found

				if ( in_array( $s, array( 'valid', 'site_inactive' ), true ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Is file in theme.
		 *
		 * @param string $file File to check.
		 *
		 * @return bool
		 */
		public static function file_in_theme( string $file ): bool {
			$file_path = self::wp_normalize_path( dirname( $file ) );

			if ( strpos( $file_path, self::wp_normalize_path( get_template_directory() ) ) !== false ) {
				return true;
			} elseif ( strpos( $file_path, self::wp_normalize_path( get_stylesheet_directory() ) ) !== false ) {
				return true;
			}

			return false;
		}

		/**
		 * Is Redux embedded inside a plugin?
		 *
		 * @param string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_plugin( string $file ) {
			$file            = self::wp_normalize_path( $file );
			$plugin_basename = self::wp_normalize_path( plugin_basename( $file ) );

			if ( self::file_in_theme( $file ) ) {
				return false;
			}

			if ( $plugin_basename !== $file ) {
				$slug = explode( '/', $plugin_basename );
				$slug = $slug[0];

				return array(
					'slug'      => $slug,
					'basename'  => $plugin_basename,
					'path'      => $file,
					'url'       => self::verify_url_protocol( plugins_url( $plugin_basename ) ),
					'real_path' => self::wp_normalize_path( dirname( realpath( $file ) ) ),
				);
			}

			return false;
		}

		/**
		 * Is Redux embedded in a theme?
		 *
		 * @param string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function is_inside_theme( string $file = '' ) {

			if ( ! self::file_in_theme( $file ) ) {
				return false;
			}

			$theme_paths = array(
				self::wp_normalize_path( get_template_directory() )   => get_template_directory_uri(),
				// parent.
				self::wp_normalize_path( get_stylesheet_directory() ) => get_stylesheet_directory_uri(),
				// child.
			);

			$theme_paths = array_unique( $theme_paths );
			$file_path   = self::wp_normalize_path( $file );

			$filename = explode( DIRECTORY_SEPARATOR, $file );

			end( $filename );

			$filename = prev( $filename );

			foreach ( $theme_paths as $theme_path => $url ) {
				$real_path = self::wp_normalize_path( realpath( $theme_path ) );

				if ( empty( $real_path ) ) {
					continue;
				}

				if ( strpos( $file_path, $real_path ) !== false ) {
					$slug             = explode( '/', $theme_path );
					$slug             = end( $slug );
					$relative_path    = explode( $slug . '/', dirname( $file_path ) );
					$relative_path    = $relative_path[1];
					$data             = array(
						'slug'      => $slug,
						'path'      => trailingslashit( trailingslashit( $theme_path ) . $relative_path ) . $filename,
						'real_path' => trailingslashit( trailingslashit( $real_path ) . $relative_path ) . $filename,
						'url'       => self::verify_url_protocol( trailingslashit( trailingslashit( $url ) . $relative_path ) . $filename ),
						'basename'  => trailingslashit( $slug ) . trailingslashit( $relative_path ) . $filename,
					);
					$data['realpath'] = $data['real_path'];  // Shim for old extensions.

					if ( count( $theme_paths ) > 1 ) {
						$key = array_search( $theme_path, $theme_paths, true );

						if ( false !== $key ) {
							unset( $theme_paths[ $key ] );
						}

						$theme_paths_end = end( $theme_paths );
						$parent_slug_end = explode( '/', $theme_paths_end );
						$parent_slug_end = end( $parent_slug_end );

						$data['parent_slug'] = $parent_slug_end;
					}

					return $data;
				}
			}

			return false;
		}

		/**
		 * Used to fix 3.x and 4 compatibility for extensions
		 *
		 * @param object $extension      The extension parent object.
		 * @param string $path           - Path of the file.
		 * @param string $ext_class      - Extension class name.
		 * @param string $new_class_name - New dynamic class name.
		 * @param string $name           extension name.
		 *
		 * @return object - Extended field class.
		 */
		public static function extension_compatibility( $extension, string $path, string $ext_class, string $new_class_name, string $name ) {
			if ( empty( $new_class_name ) ) {
				return null;
			}

			$upload_dir = ReduxFramework::$_upload_dir . '/extension_compatibility/';

			if ( ! file_exists( $upload_dir . $ext_class . '.php' ) ) {
				if ( ! is_dir( $upload_dir ) ) {
					$extension->filesystem->mkdir( $upload_dir );
					$extension->filesystem->put_contents( $upload_dir . 'index.php', '<?php // Silence is golden.' );
				}
				if ( ! class_exists( $ext_class ) ) {
					require_once $path;
				}
				if ( ! file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					$class_file = '<?php' . PHP_EOL . PHP_EOL .
								'class {{ext_class}} extends Redux_Extension_Abstract {' . PHP_EOL .
								'    private $c;' . PHP_EOL .
								'    public function __construct( $parent, $path, $ext_class ) {' . PHP_EOL .
								'        $this->c = $parent->extensions[\'' . $name . '\'];' . PHP_EOL .
								'        // Add all the params of the Abstract to this instance.' . PHP_EOL .
								'        foreach( get_object_vars( $this->c ) as $key => $value ) {' . PHP_EOL .
								'            $this->$key = $value;' . PHP_EOL .
								'        }' . PHP_EOL .
								'        parent::__construct( $parent, $path );' . PHP_EOL .
								'    }' . PHP_EOL .
								'    // fake "extends Redux_Extension_Abstract\" using magic function' . PHP_EOL .
								'    public function __call( $method, $args ) {' . PHP_EOL .
								'        return call_user_func_array( array( $this->c, $method ), $args );' . PHP_EOL .
								'    }' . PHP_EOL .
								'}' . PHP_EOL;
					$template   = str_replace( '{{ext_class}}', $new_class_name, $class_file );
					// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
					// $parent->filesystem->put_contents( $upload_dir . $new_class_name . '.php', $template );
				}

				if ( file_exists( $upload_dir . $new_class_name . '.php' ) ) {
					if ( ! class_exists( $new_class_name ) ) {
						require_once $upload_dir . $new_class_name . '.php';
					}
					if ( class_exists( $new_class_name ) ) {
						return new $new_class_name( $extension, $path, $ext_class );
					}
				}
			}

			return null;
		}

		/**
		 * Used to merge two deep arrays.
		 *
		 * @param array $a First array to deeply merge.
		 * @param array $b Second array to deeply merge.
		 *
		 * @return    array - Deep merge of the two arrays.
		 */
		public static function nested_wp_parse_args( array &$a, array $b ): array {
			$result = $b;

			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::nested_wp_parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}

			return $result;
		}

		/**
		 * AJAX callback key
		 */
		public static function hash_key(): string {
			$key  = defined( 'AUTH_KEY' ) ? AUTH_KEY : get_site_url();
			$key .= defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : '';

			return $key;
		}

		/**
		 * Register a class path to be autoloaded.
		 * Registers a namespace to be autoloaded from a given path, using the
		 * WordPress/HM-style filenames (`class-{name}.php`).
		 *
		 * @link https://engineering.hmn.md/standards/style/php/#file-naming
		 *
		 * @param string $prefix Prefix to autoload from.
		 * @param string $path   Path to validate.
		 */
		public static function register_class_path( string $prefix = '', string $path = '' ) {
			if ( ! class_exists( 'Redux_Autoloader' ) ) {
				require_once Redux_Path::get_path( '/inc/classes/class-redux-autoloader.php' );
			}

			$loader = new Redux_Autoloader( $prefix, $path );

			spl_autoload_register( array( $loader, 'load' ) );
		}

		/**
		 * Check if a string starts with a string.
		 *
		 * @param string $haystack Full string.
		 * @param string $needle   String to check if it starts with.
		 *
		 * @return bool
		 */
		public static function string_starts_with( string $haystack, string $needle ): bool {
			$length = strlen( $needle );

			return substr( $haystack, 0, $length ) === $needle;
		}

		/**
		 * Check if a string ends with a string.
		 *
		 * @param string $haystack Full string.
		 * @param string $needle   String to check if it starts with.
		 *
		 * @return bool
		 */
		public static function string_ends_with( string $haystack, string $needle ): bool {
			$length = strlen( $needle );

			if ( ! $length ) {
				return true;
			}

			return substr( $haystack, - $length ) === $needle;
		}

		/**
		 * Determine if Extendify plugin is installed.
		 *
		 * @param string $name Plugin name.
		 *
		 * @return bool
		 */
		public static function is_plugin_installed( string $name ): bool {
			if ( ! function_exists( 'get_plugins' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			foreach ( get_plugins() as $plugin => $data ) {
				if ( $data['TextDomain'] === $name ) {
					return $plugin;
				}
			}

			return false;
		}

		/**
		 * Is plugin active.
		 *
		 * @param string $name Plugin name.
		 *
		 * @return bool
		 */
		public static function is_plugin_active( string $name ): bool {
			if ( in_array( $name . '/' . $name . '.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				return true;
			}

			return false;
		}
	}
}
