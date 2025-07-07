<?php
/**
 * Build Welcome Page with settings.
 *
 * @package Kadence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Build Welcome Page class
 *
 * @category class
 */
class Kadence_Dashboard_Settings {

	/**
	 * Settings of this class
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Static var active plugins
	 *
	 * @var $active_plugins
	 */
	private static $active_plugins;

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		// only load if admin.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			$this->add_category_color();
		}
		add_action( 'init', array( $this, 'load_api_settings' ) );
	}
	/**
	 * Redirect to the settings page on activation.
	 *
	 * @param string $key setting key.
	 */
	public static function get_data_options( $key ) {
		if ( ! isset( self::$settings[ $key ] ) ) {
			self::$settings[ $key ] = get_option( $key, array() );
		}
		return self::$settings[ $key ];
	}
	/**
	 * Add option page menu
	 */
	public function add_menu() {
		$page = add_theme_page( __( 'Kadence - Next Generation Theme', 'kadence' ), __( 'Kadence', 'kadence' ), apply_filters( 'kadence_admin_settings_capability', 'manage_options' ), 'kadence', array( $this, 'config_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
		do_action( 'kadence_theme_admin_menu' );
	}
	/**
	 * Initialize getting the active plugins list.
	 */
	public static function get_active_plugins() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}
	/**
	 * Active Plugin Check
	 *
	 * @param string $plugin_base_name is plugin folder/filename.php.
	 */
	public static function active_plugin_check( $plugin_base_name ) {

		if ( ! self::$active_plugins ) {
			self::get_active_plugins();
		}
		return in_array( $plugin_base_name, self::$active_plugins, true ) || array_key_exists( $plugin_base_name, self::$active_plugins );
	}
	/**
	 * Loads admin style sheets and scripts
	 */
	public function scripts() {
		$installed_plugins = get_plugins();
		$button_label = esc_html__( 'Browse Kadence Starter Templates', 'kadence' );
		$data_action  = '';
		if ( ! defined( 'KADENCE_STARTER_TEMPLATES_VERSION' ) ) {
			if ( ! isset( $installed_plugins['kadence-starter-templates/kadence-starter-templates.php'] ) ) {
				$button_label = esc_html__( 'Install Kadence Starter Templates', 'kadence' );
				$data_action  = 'install';
			} elseif ( ! self::active_plugin_check( 'kadence-starter-templates/kadence-starter-templates.php' ) ) {
				$button_label = esc_html__( 'Activate Kadence Starter Templates', 'kadence' );
				$data_action  = 'activate';
			}
		}
		wp_enqueue_style( 'kadence-dashboard', get_template_directory_uri() . '/inc/dashboard/react/dash-controls.min.css', array( 'wp-components' ), KADENCE_VERSION );
		wp_enqueue_script( 'kadence-dashboard', get_template_directory_uri() . '/assets/js/admin/dashboard.js', array( 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-components', 'wp-api', 'wp-hooks', 'wp-edit-post', 'lodash', 'wp-block-library', 'wp-block-editor', 'wp-editor', 'jquery' ), KADENCE_VERSION, true );
		wp_localize_script(
			'kadence-dashboard',
			'kadenceDashboardParams',
			array(
				'adminURL' => esc_url( admin_url() ),
				'settings' => esc_attr( get_option( 'kadence_theme_config' ) ),
				'changelog' => $this->get_changelog(),
				'proChangelog' => ( class_exists( 'Kadence_Theme_Pro' ) ? $this->get_pro_changelog() : '' ),
				'starterTemplates' => ( defined( 'KADENCE_STARTER_TEMPLATES_VERSION' ) ? true : false ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'kadence-ajax-verification' ),
				'proURL'       => esc_url( \Kadence\kadence()->get_pro_url( 'https://www.kadencewp.com/kadence-theme/premium/', 'https://www.kadencewp.com/kadence-theme/premium/', 'in-app', 'theme-dash' ) ),
				'status'       => $data_action,
				'starterLabel' => $button_label,
				'starterImage' => esc_attr( get_template_directory_uri() . '/assets/images/starter-templates-banner.jpeg' ),
				'starterURL' => $this->get_starter_templates_link(),
				'videoImage' => esc_attr( get_template_directory_uri() . '/assets/images/getting-started-video.jpg' ),
			)
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'kadence-dashboard', 'kadence' );
		}
	}

	/**
	 * Get Starter Templates Link
	 */
	public function get_starter_templates_link() {
		$config = get_option( 'kadence_starter_templates_config', '' );
		$use_site_assist = apply_filters( 'kadence_starter_site_assist_enabled', true );
		if ( ! empty( $config ) ) {
			$config = json_decode( $config, true );
			if ( isset( $config['siteAssist'] ) && 'disable' === $config['siteAssist'] ) {
				$use_site_assist = false;
			}
		}
		if ( $use_site_assist || class_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\Register' ) ) {
			return admin_url( 'admin.php?page=kadence-starter-templates' );
		}
		return admin_url( 'themes.php?page=kadence-starter-templates' );
	}

	/**
	 * Get Changelog ( Largely Borrowed From Neve Theme )
	 */
	public function get_changelog() {
		$changelog      = array();
		$changelog_path = get_template_directory() . '/changelog.txt';
		if ( ! is_file( $changelog_path ) ) {
			return $changelog;
		}
		global $wp_filesystem;
		if ( ! is_object( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$changelog_string = $wp_filesystem->get_contents( $changelog_path );
		if ( is_wp_error( $changelog_string ) ) {
			return $changelog;
		}
		$changelog = explode( PHP_EOL, $changelog_string );
		$releases  = [];
		foreach ( $changelog as $changelog_line ) {
			if ( empty( $changelog_line ) ) {
				continue;
			}
			if ( substr( ltrim( $changelog_line ), 0, 2 ) === '==' ) {
				if ( isset( $release ) ) {
					$releases[] = $release;
				}
				$changelog_line = trim( str_replace( '=', '', $changelog_line ) );
				$release = array(
					'head'    => $changelog_line,
				);
			} else {
				if ( preg_match( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line     = preg_replace( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['fix'][] = $changelog_line;
					continue;
				}

				if ( preg_match( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line        = preg_replace( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['add'][] = $changelog_line;
					continue;
				}
				$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
				$release['update'][] = $changelog_line;
			}
		}
		return $releases;
	}
	/**
	 * Get Changelog ( Largely Borrowed From Neve Theme )
	 */
	public function get_pro_changelog() {
		$changelog      = array();
		if ( ! defined( 'KTP_PATH' ) ) {
			return $changelog;
		}
		$changelog_path = KTP_PATH . '/changelog.txt';
		if ( ! is_file( $changelog_path ) ) {
			return $changelog;
		}
		global $wp_filesystem;
		if ( ! is_object( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$changelog_string = $wp_filesystem->get_contents( $changelog_path );
		if ( is_wp_error( $changelog_string ) ) {
			return $changelog;
		}
		$changelog = explode( PHP_EOL, $changelog_string );
		$releases  = [];
		foreach ( $changelog as $changelog_line ) {
			if ( empty( $changelog_line ) ) {
				continue;
			}
			if ( substr( ltrim( $changelog_line ), 0, 2 ) === '==' ) {
				if ( isset( $release ) ) {
					$releases[] = $release;
				}
				$changelog_line = trim( str_replace( '=', '', $changelog_line ) );
				$release = array(
					'head'    => $changelog_line,
				);
			} else {
				if ( preg_match( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line     = preg_replace( '/[*|-]?\s?(\[fix]|\[Fix]|fix|Fix)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['fix'][] = $changelog_line;
					continue;
				}

				if ( preg_match( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', $changelog_line ) ) {
					//$changelog_line        = preg_replace( '/[*|-]?\s?(\[add]|\[Add]|add|Add)[:]?\s?\b/', '', $changelog_line );
					$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
					$release['add'][] = $changelog_line;
					continue;
				}
				$changelog_line = trim( str_replace( [ '*', '-' ], '', $changelog_line ) );
				$release['update'][] = $changelog_line;
			}
		}
		return $releases;
	}

	/**
	 * Register settings
	 */
	public function load_api_settings() {

		register_setting(
			'kadence_theme_config',
			'kadence_theme_config',
			array(
				'type'              => 'string',
				'description'       => __( 'Config Kadence Modules', 'kadence' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
	}

	/**
	 * Loads config page
	 */
	public function config_page() {
		?>
		<div class="kadence_theme_dash_head">
			<div class="kadence_theme_dash_head_container">
				<div class="kadence_theme_dash_logo">
					<img src="<?php echo esc_attr( apply_filters( 'kadence_theme_dashboard_logo', get_template_directory_uri() . '/assets/images/kadence-logo.png' ) ); ?>">
				</div>
				<div class="kadence_theme_dash_version">
					<span>
						<?php echo esc_html( KADENCE_VERSION ); ?>
					</span>
				</div>
			</div>
		</div>
		<div class="wrap kadence_theme_dash">
			<div class="kadence_theme_dashboard">
				<h2 class="notices" style="display:none;"></h2>
				<?php settings_errors(); ?>
				<div class="page-grid">
					<div class="kadence_theme_dashboard_main">
					</div>
					<div class="side-panel">
						<?php do_action( 'kadence_theme_dash_side_panel' ); ?>
						<div class="community-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Web Creators Community', 'kadence' ); ?></h2>
								<p><?php esc_html_e( 'Join our community of fellow kadence users creating effective websites! Share your site, ask a question and help others.', 'kadence' ); ?></p>
								<a href="https://www.facebook.com/groups/webcreatorcommunity" target="_blank" class="sidebar-link"><?php esc_html_e( 'Join our Facebook Group', 'kadence' ); ?></a>
							</div>
						</div>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Video Tutorials', 'kadence' ); ?></h2>
								<p><?php esc_html_e( 'Want a guide? We have video tutorials to walk you through getting started.', 'kadence' ); ?></p>
								<a href="https://kadence-theme.com/learn-kadence/?utm_source=in-app&utm_medium=theme-dash&utm_campaign=videos" target="_blank" class="sidebar-link"><?php esc_html_e( 'Watch Videos', 'kadence' ); ?></a>
							</div>
						</div>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Documentation', 'kadence' ); ?></h2>
								<p><?php esc_html_e( 'Need help? We have a knowledge base full of articles to get you started.', 'kadence' ); ?></p>
								<a href="<?php echo esc_url( \Kadence\kadence()->get_pro_url( 'https://www.kadencewp.com/help-center/knowledge-base/kadence-theme/', 'https://www.kadencewp.com/help-center/knowledge-base/kadence-theme/', 'in-app', 'theme-dash', 'docs' ) ); ?>" target="_blank" class="sidebar-link"><?php esc_html_e( 'Browse Docs', 'kadence' ); ?></a>
							</div>
						</div>
						<div class="support-section sidebar-section components-panel">
							<div class="components-panel__body is-opened">
								<h2><?php esc_html_e( 'Support', 'kadence' ); ?></h2>
								<p><?php esc_html_e( 'Have a question, we are happy to help! Get in touch with our support team.', 'kadence' ); ?></p>
								<a href="https://www.kadencewp.com/free-support/?utm_source=in-app&utm_medium=theme-dash&utm_campaign=help" target="_blank" class="sidebar-link"><?php esc_html_e( 'Submit a Ticket', 'kadence' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	private function add_category_color() {
		// Enqueue the color picker script and styles
		add_action('admin_enqueue_scripts', function ($hook_suffix) {
			if ($hook_suffix === 'edit-tags.php' || $hook_suffix === 'term.php' || $hook_suffix === 'edit-category') {
				// Add the color picker CSS and JS
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('custom-color-picker', get_stylesheet_directory_uri() . '/assets/js/custom-color-picker.min.js', ['wp-color-picker', 'jquery'], false, true);
		   }
		});

		// Add the color picker to the 'Add New Category' screen
		add_action('category_add_form_fields', function ($taxonomy) {
			?>
			<div class="form-field">
				<label for="archive_category_color"><?php esc_html_e('Archive Color', 'kadence'); ?></label>
				<input type="text" name="archive_category_color" id="archive_category_color" class="color-field" value="" />
				<p class="description"><?php esc_html_e('Color for the archive category label.', 'kadence'); ?></p>
			</div>
			<?php
		});

		// Add the color picker to the 'Edit Category' screen
		add_action('category_edit_form_fields', function ($term) {
			$value = get_term_meta($term->term_id, 'archive_category_color', true); // Get the current color value
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="archive_category_color"><?php esc_html_e('Archive Color', 'kadence'); ?></label>
				</th>
				<td>
					<input type="text" name="archive_category_color" id="archive_category_color" class="color-field" value="<?php echo esc_attr($value); ?>" />
					<p class="description"><?php esc_html_e('Color for the archive category label.', 'kadence'); ?></p>
				</td>
			</tr>
			<?php
		});

		// Save the selected color when creating a new category
		add_action('create_category', function ($term_id) {
			if (isset($_POST['archive_category_color'])) {
				// Validate and update color value
				update_term_meta($term_id, 'archive_category_color', sanitize_hex_color($_POST['archive_category_color']));
			}
			if (isset($_POST['archive_category_hover_color'])) {
				// Validate and update hover color value
				update_term_meta($term_id, 'archive_category_hover_color', sanitize_hex_color($_POST['archive_category_hover_color']));
			}
		});

		// Save the selected color when editing an existing category
		add_action('edited_category', function ($term_id) {
			if (isset($_POST['archive_category_color'])) {
				// Validate and update color value
				update_term_meta($term_id, 'archive_category_color', sanitize_hex_color($_POST['archive_category_color']));
			}
			if (isset($_POST['archive_category_hover_color'])) {
				// Validate and update hover color value
				update_term_meta($term_id, 'archive_category_hover_color', sanitize_hex_color($_POST['archive_category_hover_color']));
			}
		});

		// Add the hover color picker to the 'Add New Category' screen
		add_action('category_add_form_fields', function ($taxonomy) {
			?>
			<div class="form-field">
				<label for="archive_category_hover_color"><?php esc_html_e('Archive Hover Color', 'kadence'); ?></label>
				<input type="text" name="archive_category_hover_color" id="archive_category_hover_color" class="color-field" value="" />
				<p class="description"><?php esc_html_e('Hover color for the archive category label.', 'kadence'); ?></p>
			</div>
			<?php
		});
		// Add the hover color picker to the 'Edit Category' screen
		add_action('category_edit_form_fields', function ($term) {
			$hover_value = get_term_meta($term->term_id, 'archive_category_hover_color', true); // Get the current hover color value
			?>
			<tr class="form-field">
				<th scope="row">
					<label for="archive_category_hover_color"><?php esc_html_e('Archive Hover Color', 'kadence'); ?></label>
				</th>
				<td>
					<input type="text" name="archive_category_hover_color" id="archive_category_hover_color" class="color-field" value="<?php echo esc_attr($hover_value); ?>" />
					<p class="description"><?php esc_html_e('Hover color for the archive category label.', 'kadence'); ?></p>
				</td>
			</tr>
			<?php
		});
	}
}
Kadence_Dashboard_Settings::get_instance();
