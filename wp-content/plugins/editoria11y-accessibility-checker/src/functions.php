<?php // phpcs:ignore

/**
 * Editoria11y functions settings loader.
 *
 * @package Editoria11y
 */

add_filter( 'plugin_action_links_' . ED11Y_BASE, 'ed11y_add_action_links' );
/**
 * Adds link to setting page on plugin admin screen.
 *
 * @param array $links WP action link array.
 */
function ed11y_add_action_links( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=ed11y' ) . '">Settings</a>',
	);
	return array_merge( $links, $mylinks );
}

/**
 * Return the default plugin settings.
 *
 * @param string $option False for all, or specify one by key.
 */
function ed11y_get_default_options( $option = false ) {

	$incompatible = array(
		'Twenty Seventeen',
		'OnePress',
	);
	// We know some themes are incompatible with vizchecks.
	$theme = array( wp_get_theme()->get( 'Name' ) );
	// Todo check this with an actual child theme.
	if ( false !== wp_get_theme()->parent() ) {
		array_push( $theme, wp_get_theme()->parent()->get( 'Name' ) );
	}
	$check_visibility = count( array_intersect( $incompatible, $theme ) ) === 0;

	$default_options = array(
		// Key not-yet-implemented features:
		// Web components
		// JS unfold theme handler
		// Language.
		'ed11y_theme'               => 'sleekTheme',
		'ed11y_checkRoots'          => false,
		'ed11y_livecheck'           => 'all',

		'ed11y_ignore_elements'     => '#comments *, .wp-block-post-comments *, img.avatar',
		'ed11y_link_ignore_strings' => false,

		'ed11y_videoContent'        => 'youtube.com, vimeo.com, yuja.com, panopto.com',
		'ed11y_audioContent'        => 'soundcloud.com, simplecast.com, podbean.com, buzzsprout.com, blubrry.com, transistor.fm, fusebox.fm, libsyn.com',
		'ed11y_documentContent'     => 'a[href$=".pdf"], a[href*=".pdf?"], a[href$=".doc"], a[href$=".docx"], a[href*=".doc?"], a[href*=".docx?"], a[href$=".ppt"], a[href$=".pptx"], a[href*=".ppt?"], a[href*=".pptx?"], a[href^="https://docs.google"]',
		'ed11y_datavizContent'      => 'datastudio.google.com, tableau',

		'ed11y_checkvisibility'     => $check_visibility,
		'ed11y_no_run'              => false,
		'ed11y_report_restrict'     => false,
		'ed11y_custom_tests'        => 0,
	);

	// Allow dev to filter the default settings.
	$filtered = apply_filters( 'ed11y_default_options', $default_options );

	return $option ? $filtered[ $option ] : $filtered;
}

/**
 * Function for quickly grabbing settings for the plugin without having to call get_option()
 * every time we need a setting.
 *
 * @param  mixed $option Option name, or false for all.
 * @param  bool  $include_defaults Whether to provide a default value if empty.
 */
function ed11y_get_plugin_settings( $option = false, $include_defaults = false ) {
	$settings = get_option( 'ed11y_plugin_settings', array() );
	$defaults = $include_defaults ? ed11y_get_default_options() : false;
	if ( $option ) {
		// Return plugin settings for a single option.
		if ( $include_defaults && array_key_exists( $option, $defaults ) ) {
			// Include fallback (for placeholders and library use).
			return ! array_key_exists( $option, $settings ) || empty( $settings[ $option ] ) ? $defaults[ $option ] : '';
		} else {
			// Return actual stored value (for field value use).
			return array_key_exists( $option, $settings ) ? $settings[ $option ] : '';
		}
	} else {
		// Return full array of values.
		if ( $include_defaults ) {
			foreach ( $defaults as $key => $value ) {
				$settings[ $key ] = ! array_key_exists( $key, $settings ) || empty( $settings[ $key ] ) ? $defaults[ $key ] : $settings[ $key ];
			}
		}
		return $settings;
	}
}


/**
 * Loads the scripts for the plugin.
 */
function ed11y_load_scripts(): void {
	$user               = wp_get_current_user();
	$allowed_roles      = array( 'editor', 'administrator', 'author', 'contributor' );
	$allowed_user_roles = array_intersect( $allowed_roles, $user->roles );

	if ( is_user_logged_in()
		&& ( $allowed_user_roles || current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) )
	) {
		// added last two parameters 10/27/22 need to test.
		wp_enqueue_script( 'editoria11y-js', trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.js', null, Editoria11y::ED11Y_VERSION, false );
		wp_enqueue_script( 'editoria11y-js-shim', trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-wp.js', array( 'wp-api' ), Editoria11y::ED11Y_VERSION, false );
		wp_enqueue_style( 'editoria11y-lib-css', trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.css', null, Editoria11y::ED11Y_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'ed11y_load_scripts' );

/**
 * Enqueue content assets but only in the Editor.
 */
function ed11y_enqueue_editor_content_assets() {

	if ( is_admin() ) {

		// Allowed roles.
		$user               = wp_get_current_user();
		$allowed_roles      = array( 'editor', 'administrator', 'author', 'contributor' );
		$allowed_user_roles = array_intersect( $allowed_roles, $user->roles );
		if ( ( $allowed_user_roles || current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && 'none' !== ed11y_get_plugin_settings( 'ed11y_livecheck', false ) ) {
			wp_enqueue_script(
				'editoria11y-js',
				trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.js',
				null,
				Editoria11y::ED11Y_VERSION,
				false
			);
			wp_enqueue_script(
				'editoria11y-editor',
				trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-editor.js',
				array( 'wp-api' ),
				Editoria11y::ED11Y_VERSION,
				false
			);
			wp_localize_script(
				'editoria11y-editor',
				'ed11yVars',
				array(
					'worker'  => trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-editor-worker.js?ver=' . Editoria11y::ED11Y_VERSION,
					'options' => ed11y_get_params( wp_get_current_user() ),
				)
			);
			wp_enqueue_style(
				'editoria11y-lib-css',
				trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.css',
				null,
				Editoria11y::ED11Y_VERSION
			);
		}
	}
}
add_action( 'enqueue_block_assets', 'ed11y_enqueue_editor_content_assets' );
add_action( 'admin_enqueue_scripts', 'ed11y_enqueue_editor_content_assets' );

/**
 * Returns page-specific config for the Editoria11y library.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @param Object $user WP_User.
 */
function ed11y_get_params( $user ) {

	// Get settings array from cache, if available.
	$ed1vals = get_site_transient( 'editoria11y_settings' );
	if ( false === $ed1vals ) {
		$settings                            = ed11y_get_plugin_settings( false, true );
		$ed1vals                             = array();
		$ed1vals['theme']                    = $settings['ed11y_theme'];
		$ed1vals['checkRoots']               = $settings['ed11y_checkRoots'];
		$ed1vals['ignoreElements']           = '#wpadminbar *,' . $settings['ed11y_ignore_elements'];
		$ed1vals['linkStringsNewWindows']    = $settings['ed11y_link_ignore_strings'];
		$ed1vals['videoContent']             = $settings['ed11y_videoContent'];
		$ed1vals['audioContent']             = $settings['ed11y_audioContent'];
		$ed1vals['documentLinks']            = $settings['ed11y_documentContent'];
		$ed1vals['dataVizContent']           = $settings['ed11y_datavizContent'];
		$ed1vals['checkVisible']             = $settings['ed11y_checkvisibility'];
		$ed1vals['preventCheckingIfPresent'] = $settings['ed11y_no_run'];
		$ed1vals['liveCheck']                = $settings['ed11y_livecheck'];
		$ed1vals['customTests']              = $settings['ed11y_custom_tests'];
		$ed1vals['cssLocation']              = trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.css';
		set_site_transient( 'editoria11y_settings', $ed1vals, 360 );
	}

	$ed1vals['title'] = trim( wp_title( '', false, 'right' ) );

	// Get entity type and post id (if single).
	$ed1vals['post_id']     = get_the_ID();
	$ed1vals['entity_type'] = 'other';
	// Ref https://wordpress.stackexchange.com/questions/83887/return-current-page-type .
	if ( is_page() ) {
		$ed1vals['entity_type'] = is_front_page() ? 'Front' : 'Page';
	} elseif ( is_home() ) {
		$ed1vals['entity_type'] = 'Home';
		$ed1vals['post_id']     = 0;
	} elseif ( is_single() ) {
		$ed1vals['entity_type'] = ( is_attachment() ) ? 'Attachment' : 'Post';
	} elseif ( is_category() ) {
		$ed1vals['entity_type'] = 'Category';
		$ed1vals['post_id']     = 0;
	} elseif ( is_tag() ) {
		$ed1vals['entity_type'] = 'Tag';
		$ed1vals['post_id']     = 0;
	} elseif ( is_tax() ) {
		$ed1vals['entity_type'] = 'Taxonomy';
		$ed1vals['post_id']     = 0;
	} elseif ( is_archive() ) {
		$ed1vals['post_id'] = 0;
		if ( is_author() ) {
			$ed1vals['entity_type'] = 'Author';
		} else {
			$ed1vals['entity_type'] = 'Archive';
		}
	} elseif ( is_search() ) {
		$ed1vals['post_id']     = 0;
		$ed1vals['entity_type'] = 'Search';
	} elseif ( is_404() ) {
		$ed1vals['post_id']     = 0;
		$ed1vals['entity_type'] = '404';
	}

	global $wp;

	// Use permalink as sync URL if available, otherwise use query path.
	if ( $ed1vals['post_id'] > 0 ) {
		$ed1vals['currentPage'] = get_permalink( $ed1vals['post_id'] );
	} else {
		$ed1vals['currentPage'] = home_url( $wp->request );
	}

	// Mode is assertive from 0ms to 10minutes after a post is modified.
	$page_edited          = get_post_modified_time( 'U', true );
	$page_edited          = $page_edited ? abs( 1 + $page_edited - time() ) : false;
	$ed1vals['alertMode'] = $page_edited && $page_edited < 600 ? 'assertive' : 'polite';

	// Lazy-create DB if network activation failed.
	if ( ! Editoria11y::check_tables() ) {
		// No DB available.
		$ed1vals['syncedDismissals'] = false;
		return $ed1vals;
	}

	// Get dismissals for route. Complex joins require manual DB call.
	// OR for permalink during transition to new DB structure.
	// phpcs:disable
	global $wpdb;
	$utable                      = $wpdb->prefix . 'ed11y_urls';
	$dtable                      = $wpdb->prefix . 'ed11y_dismissals';

	$dismissals_on_page = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT
			{$dtable}.result_key,
			{$dtable}.element_id,
			{$dtable}.dismissal_status
			FROM {$dtable}
			INNER JOIN {$utable} ON {$utable}.pid={$dtable}.pid
			WHERE (
			    {$utable}.page_url = %s
			        OR
			    	(
			    	    0 < %d
			    	    AND
			    	    {$utable}.post_id = %d
			    	)
			    )
			AND (
				{$dtable}.dismissal_status = 'ok'
					OR
					(
						{$dtable}.dismissal_status = 'hide'
						AND
						{$dtable}.user = %d
					)
				)
			;",
			array(
				$ed1vals['currentPage'],
				$ed1vals['post_id'],
				$ed1vals['post_id'],
				$user->ID,
			)
		)
	);
	// phpcs:enable

	$ed1vals['syncedDismissals'] = array();
	foreach ( $dismissals_on_page as $key => $value ) {
		$ed1vals['syncedDismissals'][ $value->result_key ][ $value->element_id ] = $value->dismissal_status;
	}

	return( $ed1vals );
}

/**
 * Initialize.
 */
function ed11y_init() {

	// Instantiates Editoria11y on the page for allowed users.
	if ( is_user_logged_in() ) {
		// Allowed roles.
		$user               = wp_get_current_user();
		$allowed_roles      = array( 'editor', 'administrator', 'author', 'contributor' );
		$allowed_user_roles = array_intersect( $allowed_roles, $user->roles );
		if ( $allowed_user_roles || current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {

			// At the moment, PHP escapes HTML breakouts. This would not be safe in other languages.
			echo '
			<script id="editoria11y-init" type="application/json">
				' . wp_json_encode( ed11y_get_params( $user ) ) . '
			</script>
			';
		}
	}
}
add_action( 'wp_footer', 'ed11y_init' );

/**
 * Preserve query Args
 *
 * @param string $link The redirect URL.
 *
 * @return string
 */
function ed11y_old_slug_redirect_url_filter( $link ) {
	if ( isset( $_GET['ed1ref'] ) && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'ed1ref' ) ) { // phpcs:ignore
		$link = add_query_arg( 'ed1ref', intval( $_GET['ed1ref'] ), $link ); // phpcs:ignore
	}
	return $link;
}
add_filter( 'old_slug_redirect_url', 'ed11y_old_slug_redirect_url_filter' );


/**
 * Load live checker when editor is present.
 * THIS IS NOT WORKING FOR NEW EDITOR
 * */
function ed11y_editor_init() {
	if ( 'none' !== ed11y_get_plugin_settings( 'ed11y_livecheck', false ) ) {
		add_action( 'enqueue_block_assets', 'ed11y_enqueue_editor_content_assets' );
		add_action( 'admin_footer', 'ed11y_init' );
	}
}
add_action( 'wp_enqueue_editor', 'ed11y_editor_init' );
