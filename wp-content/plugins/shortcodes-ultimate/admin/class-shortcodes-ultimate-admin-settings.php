<?php

/**
 * The Settings menu component.
 *
 * @since        5.0.0
 *
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/admin
 */
final class Shortcodes_Ultimate_Admin_Settings extends Shortcodes_Ultimate_Admin {

	/**
	 * The plugin settings data.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      array     $plugin_settings   The plugin settings data.
	 */
	private $plugin_settings;

	/**
	 * Default values for a single setting.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      array     $setting_defaults   Default values for a single setting.
	 */
	private $setting_defaults;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  5.0.0
	 * @param string  $plugin_file    The path of the main plugin file
	 * @param string  $plugin_version The current version of the plugin
	 */
	public function __construct( $plugin_file, $plugin_version, $plugin_prefix ) {

		parent::__construct( $plugin_file, $plugin_version, $plugin_prefix );

		$this->plugin_settings  = array();
		$this->setting_defaults = array(
			'id'          => '',
			'title'       => '',
			'type'        => 'text',
			'description' => '',
			'page'        => $this->plugin_prefix . 'settings',
			'section'     => $this->plugin_prefix . 'general',
			'group'       => rtrim( $this->plugin_prefix, '-_' ),
			'callback'    => array( $this, 'the_settings_field' ),
			'sanitize'    => 'sanitize_text_field',
		);

	}

	/**
	 * Add menu page.
	 *
	 * @since   5.0.0
	 */
	public function add_menu_pages() {

		/**
		 * Submenu: Settings
		 * admin.php?page=shortcodes-ultimate-settings
		 */
		$this->add_submenu_page(
			rtrim( $this->plugin_prefix, '-_' ),
			__( 'Settings', 'shortcodes-ultimate' ),
			__( 'Settings', 'shortcodes-ultimate' ),
			$this->get_capability(),
			$this->plugin_prefix . 'settings',
			array( $this, 'the_menu_page' )
		);

	}

	/**
	 * Register plugin settings.
	 *
	 * @since  5.0.0
	 */
	public function add_settings() {

		/**
		 * Add default settings section.
		 */
		add_settings_section(
			$this->plugin_prefix . 'general',
			__( 'General settings', 'shortcodes-ultimate' ),
			array( $this, 'the_settings_section' ),
			$this->plugin_prefix . 'settings'
		);

		/**
		 * Register plugin settings.
		 */
		foreach ( $this->get_plugin_settings() as $setting ) {

			$setting = wp_parse_args( $setting, $this->setting_defaults );

			add_settings_field(
				$setting['id'],
				$setting['title'],
				$setting['callback'],
				$setting['page'],
				$setting['section'],
				$setting
			);

			register_setting(
				$setting['group'],
				$setting['id'],
				$setting['sanitize']
			);

		}

	}

	/**
	 * Add help tabs and set help sidebar at Add-ons page.
	 *
	 * @since  5.0.0
	 * @param WP_Screen $screen WP_Screen instance.
	 */
	public function add_help_tabs( $screen ) {

		if ( ! $this->is_component_page() ) {
			return;
		}

		$screen->add_help_tab(
			array(
				'id'      => 'shortcodes-ultimate-general',
				'title'   => __( 'General settings', 'shortcodes-ultimate' ),
				'content' => $this->get_template( 'admin/partials/help/settings' ),
			)
		);

		$screen->set_help_sidebar( $this->get_template( 'admin/partials/help/sidebar' ) );

	}

	/**
	 * Filter to add action links at plugins screen.
	 *
	 * @since 5.0.8
	 * @param array $links Default links.
	 */
	public function add_action_links( $links ) {

		$plugin_links = array(
			sprintf(
				'<a href="%s">%s</a>',
				esc_attr( $this->get_component_url() ),
				esc_html( __( 'Settings', 'shortcodes-ultimate' ) )
			),
		);

		return array_merge( $plugin_links, $links );

	}

	/**
	 * Retrieve the plugin settings data.
	 *
	 * @since    5.0.0
	 * @access   protected
	 * @return  array The plugin settings data.
	 */
	protected function get_plugin_settings() {

		if ( empty( $this->plugin_settings ) ) {

			$this->plugin_settings[] = array(
				'id'          => 'su_option_custom-css',
				'type'        => 'css',
				'sanitize'    => 'wp_strip_all_tags',
				'title'       => __( 'Custom CSS code', 'shortcodes-ultimate' ),
				'description' => __( 'In this field you can write your custom CSS code for shortcodes. These styles will have higher priority compared to original styles of shortcodes. You can use variables in your CSS code. These variables will be replaced by respective values.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_prefix',
				'sanitize'    => array( $this, 'sanitize_prefix' ),
				'title'       => __( 'Shortcodes prefix', 'shortcodes-ultimate' ),
				'description' => __( 'This prefix will be used in shortcode names. For example: set <code>MY_</code> prefix and shortcodes will look like <code>[MY_button]</code>. Please note that this setting does not change shortcodes that have been inserted earlier. Change this setting very carefully.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_custom-formatting',
				'type'        => 'checkbox',
				'sanitize'    => array( $this, 'sanitize_checkbox' ),
				'title'       => __( 'Custom formatting', 'shortcodes-ultimate' ),
				'description' => __( 'Enable this option if you face any problems with formatting of nested shortcodes.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_skip',
				'type'        => 'checkbox',
				'sanitize'    => array( $this, 'sanitize_checkbox' ),
				'title'       => __( 'Skip default settings', 'shortcodes-ultimate' ),
				'description' => __( 'Enable this option if you don\'t want the inserted shortcode to contain any settings that were not changed by you. As a result, inserted shortcodes will be much shorter.', 'shortcodes-ultimate' ),
			);

			/**
			 * @since 5.1.0
			 */
			$this->plugin_settings[] = array(
				'id'          => 'su_option_supported_blocks',
				'type'        => 'checkbox-group',
				'sanitize'    => array( $this, 'sanitize_checkbox_group' ),
				'title'       => __( 'Supported blocks', 'shortcodes-ultimate' ),
				'description' => __( 'Enable the "Insert Shortcode" button in selected blocks', 'shortcodes-ultimate' ),
				'options'     => su_get_config( 'supported-blocks' ),
			);

			/**
			 * @since 5.2.0
			 */
			$this->plugin_settings[] = array(
				'id'          => 'su_option_generator_access',
				'title'       => __( 'Required user capability', 'shortcodes-ultimate' ),
				'description' => __( 'A user must have this capability to be able to use the "Insert Shortcode" button. Do not change this value if you do not understand its meaning as this may lower the plugin security.', 'shortcodes-ultimate' ),
			);

			/**
			 * @since 5.2.0
			 */
			$this->plugin_settings[] = array(
				'id'          => 'su_option_enable_shortcodes_in',
				'type'        => 'checkbox-group',
				'sanitize'    => array( $this, 'sanitize_checkbox_group' ),
				'title'       => __( 'Enable shortcodes in', 'shortcodes-ultimate' ),
				'description' => __( 'This option allows you to enable shortcodes in places where they are disabled by default', 'shortcodes-ultimate' ),
				'options'     => array(
					'category_description' => __( 'Category descriptions', 'shortcodes-ultimate' ),
					'widget_text'          => __( 'Text widgets', 'shortcodes-ultimate' ),
				),
			);

		}

		return apply_filters( 'su/admin/settings', $this->plugin_settings );

	}

}
