<?php

/**
 * The Add-ons menu component.
 *
 * @since        5.0.0
 *
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/admin
 */
final class Shortcodes_Ultimate_Admin_Addons extends Shortcodes_Ultimate_Admin {

	private $api_url;
	private $plugin_addons;
	private $transient_name;
	private $transient_timeout;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  5.0.0
	 * @param string  $plugin_file    The path of the main plugin file
	 * @param string  $plugin_version The current version of the plugin
	 */
	public function __construct( $plugin_file, $plugin_version, $plugin_prefix ) {

		parent::__construct( $plugin_file, $plugin_version, $plugin_prefix );

		$this->api_url           = 'https://getshortcodes.com/api/v1/add-ons/';
		$this->addons            = array();
		$this->transient_name    = 'su_addons';
		$this->transient_timeout = 3 * DAY_IN_SECONDS;

	}


	/**
	 * Add menu page.
	 *
	 * @since   5.0.0
	 */
	public function add_menu_pages() {

		/**
		 * Submenu: Add-ons
		 * admin.php?page=shortcodes-ultimate-addons
		 */
		$this->add_submenu_page(
			rtrim( $this->plugin_prefix, '-_' ),
			__( 'Add-ons', 'shortcodes-ultimate' ),
			__( 'Add-ons', 'shortcodes-ultimate' ),
			$this->get_capability(),
			$this->plugin_prefix . 'addons',
			array( $this, 'the_menu_page' )
		);

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

		$screen->add_help_tab( array(
				'id'      => 'shortcodes-ultimate-addons',
				'title'   => __( 'Add-ons', 'shortcodes-ultimate' ),
				'content' => $this->get_template( 'admin/partials/help/addons' ),
			) );

		$screen->set_help_sidebar( $this->get_template( 'admin/partials/help/sidebar' ) );

	}


	/**
	 * Enqueue JavaScript(s) and Stylesheet(s) for the component.
	 *
	 * @since   5.0.0
	 */
	public function enqueue_scripts() {

		if ( ! $this->is_component_page() ) {
			return;
		}

		wp_enqueue_style( 'shortcodes-ultimate-admin', $this->plugin_url . 'admin/css/admin.css', array(), $this->plugin_version );

	}

	/**
	 * Retrieve the collection of plugin add-ons.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @return  array The plugin add-ons collection.
	 */
	protected function get_addons() {

		if ( empty( $this->addons ) ) {
			$this->addons = $this->load_addons();
		}

		return apply_filters( 'su/admin/addons', $this->addons );

	}

	/**
	 * Load the collection of plugin add-ons from remote API.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @return  array The plugin add-ons collection.
	 */
	private function load_addons() {

		$transient = get_transient( $this->transient_name );

		if ( ! empty( $transient ) ) {
			return $transient;
		}

		$response = wp_remote_get(
			$this->api_url,
			array( 'timeout' => 10, 'sslverify' => false, )
		);
		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $response[0]['id'] ) ) {
			return array();
		}

		$this->addons = array();

		foreach ( $response as $item ) {
			$this->addons[ $item['id'] ] = $item;
		}

		set_transient( $this->transient_name, $this->addons, $this->transient_timeout );

		return $this->addons;

	}

}
