<?php

class MPP_Admin {
	/**
	 * The main Multipage admin loader.
	 *
	 * @since 1.4
	 *
	 */	
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set admin-related globals.
	 *
	 * @since 1.4
	 */
	private function setup_globals() {
		// Main settings page.
		$this->settings_page = 'options-general.php';
	}
	
	/**
	 * Include required files.
	 *
	 * @since 1.4
	 */
	private function includes() {
		require( MPP__PLUGIN_DIR . 'inc/admin/admin-actions.php'				 );
		require( MPP__PLUGIN_DIR . 'inc/admin/admin-functions.php'				 );
		require( MPP__PLUGIN_DIR . 'inc/admin/admin-settings.php'			 	 );
		require( MPP__PLUGIN_DIR . 'inc/admin/admin-advanced-settings.php' 		 );
	}

	/**
	 * Set up the admin hooks, actions, and filters.
	 *
	 * @since 1.4
	 *
	 */
	private function setup_actions() { 
		// Add some page specific output to the <head>.
		add_action( 'admin_head',            			 array( $this, 'admin_head'  ), 999 );

		// Add menu item to settings menu.
		add_action( 'admin_menu',						 array( $this, 'admin_menus' ), 5 ); # Priority 5.
		
		// Add settings.
		add_action( 'mpp_register_admin_settings',		 array( $this, 'register_admin_settings' ) );
		
		// Add styles to the admin.
		add_action( 'admin_enqueue_scripts',			 array( $this, 'enqueue_scripts' ) );
		
		// Add link to settings page.
		add_filter( 'plugin_action_links',               array( $this, 'modify_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );
		
		// Check if TinyMCE is enabled
		if ( get_user_option( 'rich_editing' ) == 'true' && mpp_disable_tinymce_buttons() != true ) {

			// Add TinyMCE Plugin
			add_filter( 'mce_css', array( &$this, 'mpp_mce_css' ) );
			add_filter( 'mce_buttons', array( &$this, 'mpp_mce_button' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'mpp_mce_external_plugin' ) );
			add_filter( 'mce_external_languages', array( &$this, 'mpp_mce_external_language' ) );
		}
		
		// Add HTML Editor button
		add_action( 'admin_print_footer_scripts',		 array( &$this, 'editor_add_quicktags' ) );
		
		// Add action on save post.
		add_action( 'save_post',						 array( $this, 'save_post' ) );
	}

	/**
	 * Register site-admin nav menu elements.
	 *
	 * @since 1.4
	 */
	public function admin_menus() {
		$hooks = array();
	
		// Add the option pages.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'Multipage Options', 'buddypress' ),
			__( 'Multipage', 'buddypress' ),
			'manage_options',
			'mpp-settings',
			'mpp_admin_settings'
		);
		
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'Multipage Advaced Settings', 'buddypress' ),
			__( 'Multipage Advaced Settings', 'buddypress' ),
			'manage_options',
			'mpp-advanced-settings',
			'mpp_admin_advanced'
		);

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'mpp_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register the settings.
	 *
	 * @since 1.6.0
	 *
	 */
	public function register_admin_settings() {
		
		/* Main Settings ******************************************************/

		// Add the main section.
		add_settings_section( 'mpp_main', __( 'Main Settings', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_main_section', 'multipage' );

		// Hide default intro title.
		add_settings_field( 'mpp-hide-intro-title', __( 'Intro', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_hide_intro_title', 'multipage', 'mpp_main', array( 'label_for' => 'hide-intro-title' ) );
		register_setting( 'mpp-settings', 'mpp-hide-intro-title', 'intval' );
		
		// Display comments on pages.
		add_settings_field( 'mpp-comments-on-page', __( 'Display comments on', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_comments_on_page', 'multipage', 'mpp_main', array( 'label_for' => 'comments-on-page' ) );
		register_setting( 'mpp-settings', 'mpp-comments-on-page', '' );

		// Add the pagination section.
		add_settings_section( 'mpp_pagination', __( 'Pagination', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_pagination_section', 'multipage' );

		// Display the previous page link.
		add_settings_field( 'mpp-continue-or-prev-next', __( 'Navigation type', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_continue_or_prev_next', 'multipage', 'mpp_pagination', array( 'label_for' => 'continue-or-prev-next' ) );
		register_setting( 'mpp-advanced-settings', 'mpp-continue-or-prev-next', '' );

		// Disable the standard WordPress pagination.
		add_settings_field( 'mpp-disable-standard-pagination', __( 'Disable the standard pagination', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_disable_standard_pagination', 'multipage', 'mpp_pagination', array( 'label_for' => 'mpp-disable-standard-pagination' ) );
		register_setting( 'mpp-advanced-settings', 'mpp-disable-standard-pagination', 'intval' );

		// Add the table of contents section.
		add_settings_section( 'mpp_toc', __( 'Table of contents', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_section', 'multipage' );

		// Disable the standard WordPress pagination.
		add_settings_field( 'mpp-toc-only-on-the-first-page', __( 'Only on the first page', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_only_on_the_first_page', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-only-on-the-first-page' ) );
		register_setting( 'mpp_toc', 'mpp-toc-only-on-the-first-page', 'intval' );

		// Set the table of contents position.
		add_settings_field( 'mpp-toc-position', __( 'Position', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_position', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-position' ) );
		register_setting( 'mpp_toc', 'mpp-toc-position', '' );

		// Define row labels.
		add_settings_field( 'mpp-toc-row-labels', __( 'Row labels', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_row_labels', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-row-labels' ) );
		register_setting( 'mpp_toc', 'mpp-toc-row-labels', '' );

		// Hide the table of contents header.
		add_settings_field( 'mpp-hide-toc-header', __( 'Hide header', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_hide_toc_header', 'multipage', 'mpp_toc', array( 'label_for' => 'hide-toc-header' ) );
		register_setting( 'mpp_toc', 'mpp-hide-toc-header', 'intval' );

		// Add a link to comments inside the table of contents.
		add_settings_field( 'mpp-comments-toc-link', __( 'Comments link', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_comments_toc_link', 'multipage', 'mpp_toc', array( 'label_for' => 'comments-toc-link' ) );
		register_setting( 'mpp_toc', 'mpp-comments-toc-link', 'intval' );
		
		/* Advanced Settings ******************************************************/

		// Add the main section.
		add_settings_section( 'mpp_advanced', '', 'mpp_admin_advanced_settings_callback_main_section', 'mpp-advanced-settings' );
	
		// Set the title rewrite rule.
		add_settings_field( '_mpp-rewrite-title-priority', __( 'Rewrite Title Priority', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_rewrite_title_priority', 'mpp-advanced-settings', 'mpp_advanced', array( 'label_for' => 'rewrite-title-priority' ) );
		register_setting( 'mpp-advanced-settings', '_mpp-rewrite-title-priority', 'intval' );
		
		// Set the content rewrite rule.
		add_settings_field( '_mpp-rewrite-content-priority', __( 'Rewrite Content Priority', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_rewrite_content_priority', 'mpp-advanced-settings', 'mpp_advanced', array( 'label_for' => 'rewrite-content-priority' ) );
		register_setting( 'mpp-advanced-settings', '_mpp-rewrite-content-priority', 'intval' );

		// Disable TinyMCE Buttons inside the editor to preserve older WordPress versions to work.
		add_settings_field( 'mpp-disable-tinymce-buttons', __( 'Disable TinyMCE Buttons', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_disable_tinymce_buttons', 'mpp-advanced-settings', 'mpp_advanced' );
		register_setting( 'mpp-advanced-settings', 'mpp-disable-tinymce-buttons', 'intval' );

		// Build Multipage postmetas. We only showing this if the update process didn't run the postmetas building.
		if ( ! get_option( '_mpp-postmeta-built' ) ) {
			add_settings_field( '_mpp-postmeta-built', __( 'Build Multipage postmetas', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_build_mpp_postmeta_data', 'mpp-advanced-settings', 'mpp_advanced' );
			register_setting( 'mpp-advanced-settings', '_mpp-postmeta-built', 'intval' );
		}
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 0.93
	 *
	 * @uses wp_enqueue_style()
	 * @return void
	 */
	public static function enqueue_scripts() {
		$handle = 'multipage-admin';

		// LTR or RTL
		$file = is_rtl() ? 'admin/css/'. $handle . '-rtl' : 'admin/css/' . $handle;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		$handle = 'multipage-admin';
		$file = $file . $suffix . '.css';

		// Enqueue the Multipage Plugin styling
		wp_enqueue_style( $handle, trailingslashit( MPP_PLUGIN_URL ) . $file, array(), '', 'screen' );
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since 1.4
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {
		$temp_dir = str_replace( '/trunk', '', MPP__PLUGIN_DIR ); // Check this and the following...
		
		// Return normal links if not Multipage.
		if ( basename( $temp_dir ) . '/sgr-nextpage-titles.php' != $file ) { // ...this one
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge( $links, array(
			'settings' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'mpp-settings' ), mpp_get_admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings', 'sgr-nextpage-titles' ) . '</a>',
		) );
	}
	
	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.4
	 */
	public function admin_head() {
		// Settings pages.
		remove_submenu_page( $this->settings_page, 'mpp-advanced-settings' );
	}

	public function save_post( $post_id ) {
		// If this is just a revision or a (auto)draft, don't change the post meta.
		//if ( wp_is_post_revision( $post_id ) || 'draft' == get_post_status( $post_id ) || 'auto-draft' == get_post_status( $post_id ) )
		//	return;

		$key = '_mpp_data';
		$post = get_post( $post_id );
		$post_content = $post->post_content;
		$_mpp_data = self::multipage_return_array( $post_content );
		
		if ( $_mpp_data == false ) {
			delete_post_meta( $post_id, $key );
			return;
		}
		
		// Add the post meta
		update_post_meta( $post_id, $key, $_mpp_data );
		return;
	}
	
	public static function multipage_return_array( $content ) {
		// Initialize the array
		$_multipage = array();

		$matches = self::parse_nextpage_shortcode( $content );
		foreach ( $matches[0] as $key=>$match ) {
			$atts = shortcode_parse_atts( str_replace( array( '[', ']' ), '', $match ) );
			if ( ! array_key_exists( 'title', $atts ) )
				continue;

			// Check if the intro has a Title
			if ( 0 == count( $_multipage ) && 0 !== strpos( $content, $match ) )
				$_multipage['intro'] = '%%intro%%';

			$seo_link = $seo_link_temp = isset( $atts["seo_link"] ) ? sanitize_title( $atts["seo_link"] ) : sanitize_title( $atts["title"] );
			for ( $i = 1; array_key_exists ( $seo_link, $_multipage ) && $i < 100; $i++ ) {
				$seo_link = $seo_link_temp . '-' . $i;
			}
			$_multipage[ $seo_link ] = $atts["title"];
		}

		if ( isset( $_multipage ) && is_array( $_multipage ) )
			return $_multipage;

		return false;
	}
	
	public static function parse_nextpage_shortcode( $content ) {
		preg_match_all( MPP_PATTERN, $content, $matches );
		return $matches;
	}

	/**
	 * Add HTML Text Editor Subpage button
	 *
	 * @since 1.3
	 */
	public static function editor_add_quicktags() {
		if ( wp_script_is( 'quicktags' ) ) {
	?>
	<script type="text/javascript">
		QTags.addButton( 'eg_subpage', '<?php _e( 'subpage', 'sgr-nextpage-titles' ); ?>', prompt_subtitle, '', '', '<?php _e( 'Start a new Subpage', 'sgr-nextpage-titles' ); ?>', 121 );
		
		function prompt_subtitle(e, c, ed) {
			var subtitle = prompt( '<?php _e( 'Enter the subpage title', 'sgr-nextpage-titles' ); ?>' ),
				shortcode, t = this;

			if (typeof subtitle != 'undefined' && subtitle.length < 2) return;

			t.tagStart = '[nextpage title="' + subtitle + '"]\n\n';
			t.tagEnd = false;
			
			// now we've defined all the tagStart, tagEnd and openTags we process it all to the active window
			QTags.TagButton.prototype.callback.call(t, e, c, ed);
		};
	</script>
	<?php
		}
	}
	
	/**
	 * Add a new TinyMCE css.
	 *
	 * @since 1.3
	 *
	 * @return string
	 */
	public static function mpp_mce_css( $mce_css ) {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( ! empty( $mce_css ) )
			$mce_css .= ',';

		$mce_css .= MPP_PLUGIN_URL . 'inc/admin/tinymce/css/multipage' . $suffix . '.css';
		return $mce_css;
	}

	/**
	 * Add the new subpage TinyMCE button.
	 *
	 * @since 1.3
	 *
	 * @return array $buttons
	 */
	public static function mpp_mce_button( $buttons ) {
		// Insert 'Subpage' button after the 'WP More' button
		$wp_more_key = array_search( 'wp_more', $buttons ) +1;
		$buttons_after = array_splice( $buttons, $wp_more_key);
		
		array_unshift( $buttons_after, 'subpage' );
		
		$buttons = array_merge( $buttons, $buttons_after );
		
		return $buttons;
	}

	/**
	 * Add the new TinyMCE plugin.
	 *
	 * @since 1.3
	 *
	 * @return array $plugin_array
	 */
	public static function mpp_mce_external_plugin( $plugin_array ) {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$plugin_array['multipage'] = MPP_PLUGIN_URL . 'inc/admin/tinymce/js/plugin' . $suffix . '.js';
		return $plugin_array;
	}
	
	/**
	 * Add the new TinyMCE plugin locale.
	 *
	 * @since 1.3
	 *
	 * @return array $locales
	 */
	public static function mpp_mce_external_language( $locales ) {
		$locales['multipage'] = MPP_PLUGIN_DIR . 'inc/admin/tinymce/languages.php';
		return $locales;
	}
}