<?php
/**
 * Starter Content Compatibility.
 *
 * @since 4.0.0
 * @package Astra
 */

/**
 * Class Astre_Starter_Content
 */
class Astra_Starter_Content {
	public const HOME_SLUG     = 'home';
	public const ABOUT_SLUG    = '#about';
	public const SERVICES_SLUG = '#services';
	public const REVIEWS_SLUG  = '#reviews';
	public const WHY_US_SLUG   = '#whyus';
	public const CONTACT_SLUG  = '#contact';

	/**
	 * Constructor
	 */
	public function __construct() {
		$is_fresh_site = get_option( 'fresh_site' );

		if ( ! $is_fresh_site ) {
			return;
		}

		// Adding post meta and inserting post.
		add_action(
			'wp_insert_post',
			array(
				$this,
				'register_listener',
			),
			3,
			99
		);

		// Save astra settings into database.
		add_action(
			'customize_save_after',
			array(
				$this,
				'save_astra_settings',
			),
			10,
			3
		);

		if ( ! is_customize_preview() ) {
			return;
		}

		// preview customizer values.
		add_filter( 'default_post_metadata', array( $this, 'starter_meta' ), 99, 3 );

		add_filter( 'astra_theme_defaults', array( $this, 'theme_defaults' ) );

		add_filter( 'astra_global_color_palette', array( $this, 'theme_color_palettes_defaults' ) );
	}

	/**
	 * Load default starter meta.
	 *
	 * @since 4.0.2
	 * @param mixed  $value Value.
	 * @param int    $post_id Post id.
	 * @param string $meta_key Meta key.
	 *
	 * @return string Meta value.
	 */
	public function starter_meta( $value, $post_id, $meta_key ) {
		if ( get_post_type( $post_id ) !== 'page' ) {
			return $value;
		}
		if ( 'site-content-layout' === $meta_key ) {
			return 'plain-container';
		}
		if ( 'theme-transparent-header-meta' === $meta_key ) {
			return 'enabled';
		}
		if ( 'site-sidebar-layout' === $meta_key ) {
			return 'no-sidebar';
		}
		if ( 'site-post-title' === $meta_key ) {
			return 'disabled';
		}
		return $value;
	}

	/**
	 * Register listener to insert post.
	 *
	 * @since 4.0.0
	 * @param int      $post_ID Post Id.
	 * @param \WP_Post $post Post object.
	 * @param bool     $update Is update.
	 */
	public function register_listener( $post_ID, $post, $update ) {

		if ( $update ) {
			return;
		}

		$custom_draft_post_name = get_post_meta( $post_ID, '_customize_draft_post_name', true );

		$is_from_starter_content = ! empty( $custom_draft_post_name );

		if ( ! $is_from_starter_content ) {
			return;
		}

		if ( 'page' === $post->post_type ) {
			update_post_meta( $post_ID, 'site-content-layout', 'plain-container' );
			update_post_meta( $post_ID, 'theme-transparent-header-meta', 'enabled' );
			update_post_meta( $post_ID, 'site-sidebar-layout', 'no-sidebar' );
			update_post_meta( $post_ID, 'site-post-title', 'disabled' );
		}
	}

	/**
	 *  Get customizer json
	 *
	 * @since 4.0.0
	 *  @return mixed value.
	 */
	public function get_customizer_json() {
		// The bundled file never changes during a request; decode it once and reuse.
		static $decoded = null;

		if ( null !== $decoded ) {
			return $decoded;
		}

		$file = ASTRA_THEME_DIR . 'inc/compatibility/starter-content/astra-settings-export.json';

		if ( ! is_readable( $file ) ) {
			return false; // Bail early.
		}

		$contents = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown -- Reading a static JSON file bundled inside the theme, not a remote resource.

		if ( false === $contents ) {
			return false;
		}

		$decoded = json_decode( $contents, true );

		return $decoded;
	}

	/**
	 *  Save Astra customizer settings into database.
	 *
	 * @since 4.0.0
	 */
	public function save_astra_settings() {

		$settings = self::get_customizer_json();

		if ( empty( $settings['customizer-settings'] ) || ! is_array( $settings['customizer-settings'] ) ) {
			return;
		}

		// Clear the existing astra-settings only when the payload will replace it,
		// otherwise a payload without this key would wipe settings with nothing to restore.
		if ( isset( $settings['customizer-settings']['astra-settings'] ) ) {
			delete_option( 'astra-settings' );
		}

		/**
		 * Only the options the bundled starter-content file is expected to provide.
		 * Guards against an unbounded option write if the JSON payload is ever tampered with.
		 * Keep this list in sync with the top-level keys under `customizer-settings` in
		 * inc/compatibility/starter-content/astra-settings-export.json.
		 */
		$allowed_options = array( 'astra-settings', 'astra-color-palettes', 'astra-typography-presets' );

		foreach ( $settings['customizer-settings'] as $option => $value ) {
			if ( ! in_array( $option, $allowed_options, true ) ) {
				continue;
			}
			update_option( $option, $value );
		}
	}

	/**
	 * Load default astra settings.
	 *
	 * @since 4.0.0
	 * @param mixed $defaults defaults.
	 * @return mixed value.
	 */
	public function theme_defaults( $defaults ) {
		$json     = '';
		$settings = self::get_customizer_json();

		if ( ! empty( $settings['customizer-settings'] ) ) {
			$json = $settings['customizer-settings']['astra-settings'];
		}

		return $json ? $json : $defaults;
	}

	/**
	 * Load default color palettes.
	 *
	 * @since 4.0.0
	 * @param mixed $defaults defaults.
	 * @return mixed value.
	 */
	public function theme_color_palettes_defaults( $defaults ) {
		$json     = '';
		$settings = self::get_customizer_json();

		if ( ! empty( $settings['customizer-settings'] ) ) {
			$json = $settings['customizer-settings']['astra-color-palettes'];
		}

		return $json ? $json : $defaults;
	}

	/**
	 * Return starter content definition.
	 *
	 * @return mixed|void
	 * @since 4.0.0
	 */
	public function get() {

		$nav_items_header = array(
			'home'     => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{' . self::HOME_SLUG . '}}',
			),
			'about'    => array(
				'title' => __( 'Services', 'astra' ),
				'type'  => 'custom',
				'url'   => '{{' . self::SERVICES_SLUG . '}}',
			),
			'services' => array(
				'title' => __( 'About', 'astra' ),
				'type'  => 'custom',
				'url'   => '{{' . self::ABOUT_SLUG . '}}',
			),
			'reviews'  => array(
				'title' => __( 'Reviews', 'astra' ),
				'type'  => 'custom',
				'url'   => '{{' . self::REVIEWS_SLUG . '}}',
			),
			'faq'      => array(
				'title' => __( 'Why Us', 'astra' ),
				'type'  => 'custom',
				'url'   => '{{' . self::WHY_US_SLUG . '}}',
			),
			'contact'  => array(
				'title' => __( 'Contact', 'astra' ),
				'type'  => 'custom',
				'url'   => '{{' . self::CONTACT_SLUG . '}}',
			),
		);

		$content = array(
			'attachments' => array(
				'logo' => array(
					'post_title' => _x( 'Logo', 'Theme starter content', 'astra' ),
					'file'       => 'inc/assets/images/starter-content/logo.png',
				),
			),
			'theme_mods'  => array(
				'custom_logo' => '{{logo}}',
			),

			'nav_menus'   => array(
				'primary'     => array(
					'name'  => esc_html__( 'Primary', 'astra' ),
					'items' => $nav_items_header,
				),
				'mobile_menu' => array(
					'name'  => esc_html__( 'Primary', 'astra' ),
					'items' => $nav_items_header,
				),
			),
			'options'     => array(
				'page_on_front' => '{{' . self::HOME_SLUG . '}}',
				'show_on_front' => 'page',
			),
			'posts'       => array(
				self::HOME_SLUG => require ASTRA_THEME_DIR . 'inc/compatibility/starter-content/home.php', // PHPCS:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			),
		);

		return apply_filters( 'astra_starter_content', $content );
	}
}
