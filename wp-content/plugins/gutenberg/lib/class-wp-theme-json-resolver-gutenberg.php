<?php
/**
 * Process the different data sources for site-level
 * config and offers and API to work with them.
 *
 * @package gutenberg
 */

/**
 * Class that abstracts the processing
 * of the different data sources.
 */
class WP_Theme_JSON_Resolver_Gutenberg {

	/**
	 * Container for data coming from core.
	 *
	 * @var WP_Theme_JSON_Gutenberg
	 */
	private static $core = null;

	/**
	 * Container for data coming from the theme.
	 *
	 * @var WP_Theme_JSON_Gutenberg
	 */
	private static $theme = null;

	/**
	 * Whether or not the theme supports theme.json.
	 *
	 * @var boolean
	 */
	private static $theme_has_support = null;

	/**
	 * Container for data coming from the user.
	 *
	 * @var WP_Theme_JSON_Gutenberg
	 */
	private static $user = null;

	/**
	 * Stores the ID of the custom post type
	 * that holds the user data.
	 *
	 * @var integer
	 */
	private static $user_custom_post_type_id = null;

	/**
	 * Container to keep loaded i18n schema for `theme.json`.
	 *
	 * @var Array
	 */
	private static $i18n_schema = null;

	/**
	 * Processes a file that adheres to the theme.json
	 * schema and returns an array with its contents,
	 * or a void array if none found.
	 *
	 * @param string $file_path Path to file. Empty if no file.
	 *
	 * @return array Contents that adhere to the theme.json schema.
	 */
	private static function read_json_file( $file_path ) {
		$config = array();
		if ( $file_path ) {
			$decoded_file = gutenberg_json_file_decode( $file_path, array( 'associative' => true ) );
			if ( is_array( $decoded_file ) ) {
				$config = $decoded_file;
			}
		}
		return $config;
	}

	/**
	 * Returns a data structure used in theme.json translation.
	 *
	 * @return array An array of theme.json fields that are translatable and the keys that are translatable
	 */
	public static function get_fields_to_translate() {
		_deprecated_function( __METHOD__, '5.9.0' );
		return array();
	}

	/**
	 * Given a theme.json structure modifies it in place
	 * to update certain values by its translated strings
	 * according to the language set by the user.
	 *
	 * @param array  $theme_json The theme.json to translate.
	 * @param string $domain    Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                          Default 'default'.
	 *
	 * @return array Returns the modified $theme_json_structure.
	 */
	private static function translate( $theme_json, $domain = 'default' ) {
		if ( null === self::$i18n_schema ) {
			$i18n_schema       = gutenberg_json_file_decode( __DIR__ . '/theme-i18n.json' );
			self::$i18n_schema = null === $i18n_schema ? array() : $i18n_schema;
		}

		return gutenberg_translate_settings_using_i18n_schema( self::$i18n_schema, $theme_json, $domain );
	}

	/**
	 * Return core's origin config.
	 *
	 * @return WP_Theme_JSON_Gutenberg Entity that holds core data.
	 */
	public static function get_core_data() {
		if ( null !== self::$core ) {
			return self::$core;
		}

		$config     = self::read_json_file( __DIR__ . '/theme.json' );
		$config     = self::translate( $config );
		self::$core = new WP_Theme_JSON_Gutenberg( $config, 'core' );

		return self::$core;
	}

	/**
	 * Returns the theme's data.
	 *
	 * Data from theme.json will be backfilled from existing
	 * theme supports, if any. Note that if the same data
	 * is present in theme.json and in theme supports,
	 * the theme.json takes precendence.
	 *
	 * @return WP_Theme_JSON_Gutenberg Entity that holds theme data.
	 */
	public static function get_theme_data() {
		if ( null === self::$theme ) {
			$theme_json_data = self::read_json_file( self::get_file_path_from_theme( 'theme.json' ) );
			$theme_json_data = self::translate( $theme_json_data, wp_get_theme()->get( 'TextDomain' ) );
			self::$theme     = new WP_Theme_JSON_Gutenberg( $theme_json_data );

			if ( wp_get_theme()->parent() ) {
				// Get parent theme.json.
				$parent_theme_json_data = self::read_json_file( self::get_file_path_from_theme( 'theme.json', true ) );
				$parent_theme_json_data = self::translate( $parent_theme_json_data, wp_get_theme()->parent()->get( 'TextDomain' ) );
				$parent_theme           = new WP_Theme_JSON_Gutenberg( $parent_theme_json_data );

				// Merge the child theme.json into the parent theme.json.
				// The child theme takes precedence over the parent.
				$parent_theme->merge( self::$theme );
				self::$theme = $parent_theme;
			}
		}

		/*
		* We want the presets and settings declared in theme.json
		* to override the ones declared via theme supports.
		* So we take theme supports, transform it to theme.json shape
		* and merge the self::$theme upon that.
		*/
		$theme_support_data  = WP_Theme_JSON_Gutenberg::get_from_editor_settings( gutenberg_get_default_block_editor_settings() );
		$with_theme_supports = new WP_Theme_JSON_Gutenberg( $theme_support_data );
		$with_theme_supports->merge( self::$theme );

		return $with_theme_supports;
	}

	/**
	 * Returns the CPT that contains the user's origin config
	 * for the current theme or a void array if none found.
	 *
	 * It can also create and return a new draft CPT.
	 *
	 * @param bool  $should_create_cpt Whether a new CPT should be created if no one was found.
	 *                                 False by default.
	 * @param array $post_status_filter Filter CPT by post status.
	 *                                  ['publish'] by default, so it only fetches published posts.
	 *
	 * @return array Custom Post Type for the user's origin config.
	 */
	private static function get_user_data_from_custom_post_type( $should_create_cpt = false, $post_status_filter = array( 'publish' ) ) {
		$user_cpt         = array();
		$post_type_filter = 'wp_global_styles';
		$recent_posts     = wp_get_recent_posts(
			array(
				'numberposts' => 1,
				'orderby'     => 'date',
				'order'       => 'desc',
				'post_type'   => $post_type_filter,
				'post_status' => $post_status_filter,
				'tax_query'   => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'name',
						'terms'    => wp_get_theme()->get_stylesheet(),
					),
				),
			)
		);

		if ( is_array( $recent_posts ) && ( count( $recent_posts ) === 1 ) ) {
			$user_cpt = $recent_posts[0];
		} elseif ( $should_create_cpt ) {
			$cpt_post_id = wp_insert_post(
				array(
					'post_content' => '{"version": ' . WP_Theme_JSON_Gutenberg::LATEST_SCHEMA . ', "isGlobalStylesUserThemeJSON": true }',
					'post_status'  => 'publish',
					'post_title'   => __( 'Custom Styles', 'default' ),
					'post_type'    => $post_type_filter,
					'post_name'    => 'wp-global-styles-' . urlencode( wp_get_theme()->get_stylesheet() ),
					'tax_input'    => array(
						'wp_theme' => array( wp_get_theme()->get_stylesheet() ),
					),
				),
				true
			);
			$user_cpt    = get_post( $cpt_post_id, ARRAY_A );
		}

		return $user_cpt;
	}

	/**
	 * Returns the user's origin config.
	 *
	 * @return WP_Theme_JSON_Gutenberg Entity that holds user data.
	 */
	public static function get_user_data() {
		if ( null !== self::$user ) {
			return self::$user;
		}

		$config   = array();
		$user_cpt = self::get_user_data_from_custom_post_type();
		if ( array_key_exists( 'post_content', $user_cpt ) ) {
			$decoded_data = json_decode( $user_cpt['post_content'], true );

			$json_decoding_error = json_last_error();
			if ( JSON_ERROR_NONE !== $json_decoding_error ) {
				trigger_error( 'Error when decoding a theme.json schema for user data. ' . json_last_error_msg() );
				return new WP_Theme_JSON_Gutenberg( $config, 'user' );
			}

			// Very important to verify if the flag isGlobalStylesUserThemeJSON is true.
			// If is not true the content was not escaped and is not safe.
			if (
				is_array( $decoded_data ) &&
				isset( $decoded_data['isGlobalStylesUserThemeJSON'] ) &&
				$decoded_data['isGlobalStylesUserThemeJSON']
			) {
				unset( $decoded_data['isGlobalStylesUserThemeJSON'] );
				$config = $decoded_data;
			}
		}
		self::$user = new WP_Theme_JSON_Gutenberg( $config, 'user' );

		return self::$user;
	}

	/**
	 * There are three sources of data (origins) for a site:
	 * core, theme, and user. The user's has higher priority
	 * than the theme's, and the theme's higher than core's.
	 *
	 * Unlike the getters {@link get_core_data},
	 * {@link get_theme_data}, and {@link get_user_data},
	 * this method returns data after it has been merged
	 * with the previous origins. This means that if the same piece of data
	 * is declared in different origins (user, theme, and core),
	 * the last origin overrides the previous.
	 *
	 * For example, if the user has set a background color
	 * for the paragraph block, and the theme has done it as well,
	 * the user preference wins.
	 *
	 * @param string $origin To what level should we merge data.
	 *                       Valid values are 'theme' or 'user'.
	 *                       Default is 'user'.
	 *
	 * @return WP_Theme_JSON_Gutenberg
	 */
	public static function get_merged_data( $origin = 'user' ) {
		$result = new WP_Theme_JSON_Gutenberg();
		$result->merge( self::get_core_data() );
		$result->merge( self::get_theme_data() );

		if ( 'user' === $origin ) {
			$result->merge( self::get_user_data() );
		}

		return $result;
	}

	/**
	 * Registers a Custom Post Type to store the user's origin config.
	 */
	public static function register_user_custom_post_type() {
		$args = array(
			'label'        => __( 'Global Styles', 'gutenberg' ),
			'description'  => 'CPT to store user design tokens',
			'public'       => false,
			'show_ui'      => false,
			'show_in_rest' => false,
			'capabilities' => array(
				'read'                   => 'edit_theme_options',
				'create_posts'           => 'edit_theme_options',
				'edit_posts'             => 'edit_theme_options',
				'edit_published_posts'   => 'edit_theme_options',
				'delete_published_posts' => 'edit_theme_options',
				'edit_others_posts'      => 'edit_theme_options',
				'delete_others_posts'    => 'edit_theme_options',
			),
			'map_meta_cap' => true,
			'supports'     => array(
				'title',
				'editor',
				'revisions',
			),
		);
		register_post_type( 'wp_global_styles', $args );
	}

	/**
	 * Returns the ID of the custom post type
	 * that stores user data.
	 *
	 * @return integer
	 */
	public static function get_user_custom_post_type_id() {
		if ( null !== self::$user_custom_post_type_id ) {
			return self::$user_custom_post_type_id;
		}

		$user_cpt = self::get_user_data_from_custom_post_type( true );
		if ( array_key_exists( 'ID', $user_cpt ) ) {
			self::$user_custom_post_type_id = $user_cpt['ID'];
		}

		return self::$user_custom_post_type_id;
	}

	/**
	 * Whether the current theme has a theme.json file.
	 *
	 * @return boolean
	 */
	public static function theme_has_support() {
		if ( ! isset( self::$theme_has_support ) ) {
			self::$theme_has_support = is_readable( get_theme_file_path( 'theme.json' ) );
		}

		return self::$theme_has_support;
	}

	/**
	 * Builds the path to the given file
	 * and checks that it is readable.
	 *
	 * If it isn't, returns an empty string,
	 * otherwise returns the whole file path.
	 *
	 * @param string $file_name Name of the file.
	 * @param bool   $template  Use template theme directroy. Default: false.
	 * @return string The whole file path or empty if the file doesn't exist.
	 */
	private static function get_file_path_from_theme( $file_name, $template = false ) {
		$path      = $template ? get_template_directory() : get_stylesheet_directory();
		$candidate = $path . '/' . $file_name;

		return is_readable( $candidate ) ? $candidate : '';
	}

	/**
	 * Cleans the cached data so it can be recalculated.
	 */
	public static function clean_cached_data() {
		self::$core                     = null;
		self::$theme                    = null;
		self::$user                     = null;
		self::$user_custom_post_type_id = null;
		self::$theme_has_support        = null;
		self::$i18n_schema              = null;
	}

}

add_action( 'switch_theme', array( 'WP_Theme_JSON_Resolver_Gutenberg', 'clean_cached_data' ) );
add_action( 'start_previewing_theme', array( 'WP_Theme_JSON_Resolver_Gutenberg', 'clean_cached_data' ) );
