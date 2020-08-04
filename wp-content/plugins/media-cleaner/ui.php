<?php

class Meow_WPMC_UI {

	private $core = null;
	private $admin = null;
	private $foundTypes = array(
		"CONTENT" => "Found in content.",
		"CONTENT (ID)" => "Found in content (as an ID).",
		"CONTENT (URL)" => "Found in content (as an URL).",
		"THEME" => "Found in theme.",
		"PAGE BUILDER" => "Found in Page Builder.",
		"PAGE BUILDER (ID)" => "Found in Page Builder (as an ID).",
		"PAGE BUILDER (URL)" => "Found in Page Builder (as an URL).",
		"GALLERY" => "Found in gallery.",
		"META" => "Found in meta.",
		"META (ID)" => "Found in meta (as an ID).",
		"META (URL)" => "Found in meta (as an URL).",
		"META ACF (ID)" => "Found in meta (as an URL).",
		"META ACF (URL)" => "Found in meta (as an URL).",
		"WIDGET" => "Found in widget.",
		"ACF WIDGET (ID)" => "Found in ACF Widget (as an ID).",
		"ACF WIDGET (URL)" => "Found in ACF Widget (as an URL).",
		"ATTACHMENT (ID)" => "Found in Attachment (as an ID).",
		"METASLIDER (ID)" => "Found in MetaSlider (as an ID).",
		"MY CALENDAR (URL)" => "Found in My Calendar (as an URL).",
		"UBERMENU (URL)" => "Found in UberMenu (as an URL).",
		"MAX MEGA MENU (URL)" => "Found in Max Mega Menu (as an URL).",
		"SITE ICON" => "Found in Site Icon."
	);

	function __construct( $core, $admin ) {
		$this->core = $core;
		$this->admin = $admin;
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_print_scripts', array( $this, 'admin_inline_js' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_filter( 'media_row_actions', array( $this, 'media_row_actions' ), 10, 2 );
	}

	/**
	 * Renders a view within the views directory.
	 * @param string $view The name of the view to render
	 * @param array $data
	 * An associative array of variables to bind to the view.
	 * Each key turns into a variable name.
	 * @return string Rendered view
	 */
	function render_view( $view, $data = null ) {
		ob_start();
		if ( is_array( $data ) ) extract( $data );
		include( __DIR__ . "/views/$view.php" );
		return ob_get_clean();
	}

	function load_textdomain() {
		load_plugin_textdomain( 'media-cleaner', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function admin_menu() {
		add_media_page( 'Media Cleaner', 'Cleaner', 'manage_options', 'media-cleaner', array( $this, 'wpmc_screen' ) );
	}

	function wpmc_screen() {
		global $wpdb, $wplr;
		echo $this->render_view( 'menu-screen', array(
			'wpdb'  => $wpdb,
			'wplr'  => $wplr,
			'ui'    => $this,
			'core'  => $this->core,
			'admin' => $this->admin
		) );
	}

	function wp_enqueue_scripts() {
		global $wpmc_version;
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'media-cleaner-css', plugins_url( '/scripts/style.css', __FILE__ ) );

		$screen = get_current_screen();
		switch ( $screen->id ) {
		case 'media_page_media-cleaner': // Media > Cleaner
			$handle = 'media-cleaner';
			wp_enqueue_script( $handle, plugins_url( '/scripts/dashboard.js', __FILE__ ), array( 'jquery', 'jquery-ui-dialog' ), $wpmc_version, true );

			$actions = array ( 'wpmc_define' );
			$nonces = array (); // action => nonce
			foreach ( $actions as $item ) $nonces[$item] = wp_create_nonce( $item );
			wp_localize_script( $handle, 'WPMC_NONCES', $nonces );
			wp_localize_script( $handle, 'WPMC_E', Meow_WPMC_API::E ); // Error code enums
			break;
		case 'meow-apps_page_wpmc_settings-menu': // Meow Apps > Media Cleaner (Settings)
			wp_enqueue_script( 'media-cleaner-settings', plugins_url( '/scripts/settings.js', __FILE__ ), array( 'jquery' ),
				$wpmc_version, true );
			break;
		}
	}

	/**
	 *
	 * DASHBOARD
	 *
	 */

	function admin_inline_js() {
		echo "<script type='text/javascript'>\n";
		echo 'var wpmc_cfg = {
			timeout: ' . ( (int) $this->core->get_max_execution_time() ) * 1000 . ',
			delay: ' . get_option( 'wpmc_delay', 100 ) . ',
			postsBuffer:' . get_option( 'wpmc_posts_buffer', 5 ) . ',
			mediasBuffer:' . get_option( 'wpmc_medias_buffer', 100 ) . ',
			analysisBuffer: ' . get_option( 'wpmc_analysis_buffer', 50 ) . ',
			isPro: ' . ( $this->admin->is_registered()  ? '1' : '0') . ',
			scanFiles: ' . ( ( $this->core->current_method == 'files' && $this->admin->is_registered() ) ? '1' : '0' ) . ',
			scanMedia: ' . ( $this->core->current_method == 'media' ? '1' : '0' ) . ' };';
		echo "\n</script>";
	}

	/*******************************************************************************
	 * METABOX FOR USAGE
	 ******************************************************************************/

	function add_metabox() {
		add_meta_box( 'mfrh_media_usage_box', 'Media Cleaner', array( $this, 'display_metabox' ), 'attachment', 'side', 'default' );
	}

	function display_metabox( $post ) {
		$originType = $this->core->reference_exists( null, $post->ID );
		if ( $originType ) {
			if ( array_key_exists( $originType, $this->foundTypes ) )
				echo $this->foundTypes[ $originType ];
			else
				echo "It seems to be used as: " . $originType;
		}
		else {
			echo "Doesn't seem to be used.";
		}
	}

	function media_row_actions( $actions, $post ) {
		global $current_screen;
		if ( 'upload' != $current_screen->id )
		    return $actions;
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$res = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $post->ID ) );
		if ( !empty( $res ) && isset( $actions['delete'] ) )
			$actions['delete'] = "<a href='?page=media-cleaner&view=deleted'>" .
				__( 'Delete with Media Cleaner', 'media-cleaner' ) . "</a>";
		if ( !empty( $res ) && isset( $actions['trash'] ) )
			$actions['trash'] = "<a href='?page=media-cleaner'>" .
				__( 'Trash with Media Cleaner', 'media-cleaner' ) . "</a>";
		if ( !empty( $res ) && isset( $actions['untrash'] ) ) {
			$actions['untrash'] = "<a href='?page=media-cleaner&view=deleted'>" .
				__( 'Restore with Media Cleaner', 'media-cleaner' ) . "</a>";
		}
		return $actions;
	}

}
