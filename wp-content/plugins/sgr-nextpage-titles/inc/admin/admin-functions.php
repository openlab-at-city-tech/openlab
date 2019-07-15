<?php

/**
 * This tells WP to highlight the Settings > BuddyPress menu item,
 * regardless of which actual BuddyPress admin screen we are on.
 *
 * The conditional prevents the behaviour when the user is viewing the
 * backpat "Help" page, the Activity page, or any third-party plugins.
 *
 * @global string $plugin_page
 * @global array $submenu
 *
 * @since 1.4
 */
function mpp_modify_admin_menu_highlight() {
	global $plugin_page, $submenu_file;

	// This tweaks the Settings subnav menu to show only one BuddyPress menu item.
	if ( in_array( $plugin_page, array( 'mpp-advanced-settings', 'mpp-templates' ) ) ) {
		$submenu_file = 'mpp-settings';
	}
}

/**
 * Output the correct admin URL based on WordPress configuration.
 *
 * @since 1.4
 *
 *
 * @param string $path   See {@link bp_get_admin_url()}.
 * @param string $scheme See {@link bp_get_admin_url()}.
 */
function mpp_admin_url( $path = '', $scheme = 'admin' ) {
	echo esc_url( mpp_get_admin_url( $path, $scheme ) );
}
	/**
	 * Return the correct admin URL based on WordPress configuration.
	 *
	 * @since 1.4
	 *
	 *
	 * @param string $path   Optional. The sub-path under /wp-admin to be
	 *                       appended to the admin URL.
	 * @param string $scheme The scheme to use. Default is 'admin', which
	 *                       obeys {@link force_ssl_admin()} and {@link is_ssl()}. 'http'
	 *                       or 'https' can be passed to force those schemes.
	 * @return string Admin url link with optional path appended.
	 */
	function mpp_get_admin_url( $path = '', $scheme = 'admin' ) {
		$url = admin_url( $path, $scheme );

		return $url;
	}

/**
 * Output the tabs in the admin area.
 *
 * @since 1.4
 *
 * @param string $active_tab Name of the tab that is active. Optional.
 */
function mpp_admin_tabs( $active_tab = '' ) {
	$tabs_html    = '';
	$idle_class   = 'nav-tab';
	$active_class = 'nav-tab nav-tab-active';
	
	/**
	 * Filters the admin tabs to be displayed.
	 *
	 * @since 1.4
	 *
	 * @param array $value Array of tabs to output to the admin area.
	 */
	$tabs         = apply_filters( 'mpp_admin_tabs', mpp_get_admin_tabs( $active_tab ) );

	// Loop through tabs and build navigation.
	foreach ( array_values( $tabs ) as $tab_data ) {
		$is_current = (bool) ( $tab_data['name'] == $active_tab );
		$tab_class  = $is_current ? $active_class : $idle_class;
		$tabs_html .= '<a href="' . esc_url( $tab_data['href'] ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data['name'] ) . '</a>';
	}

	echo $tabs_html;

	/**
	 * Fires after the output of tabs for the admin area.
	 *
	 * @since 1.4
	 */
	do_action( 'mpp_admin_tabs' );
}

/**
 * Get the data for the tabs in the admin area.
 *
 * @since 1.4
 *
 * @param string $active_tab Name of the tab that is active. Optional.
 * @return string
 */
function mpp_get_admin_tabs( $active_tab = '' ) {
	$tabs = array(
		'0' => array(
			'href' => mpp_get_admin_url( add_query_arg( array( 'page' => 'mpp-settings' ), 'options-general.php' ) ),
			'name' => __( 'Options', 'sgr-nextpage-titles' )
		),
		'1' => array(
			'href' => mpp_get_admin_url( add_query_arg( array( 'page' => 'mpp-advanced-settings' ), 'options-general.php' ) ),
			'name' => __( 'Advanced', 'sgr-nextpage-titles' )
		),
	);

	/**
	 * Filters the tab data used in our wp-admin screens.
	 *
	 * @since 1.4
	 *
	 * @param array $tabs Tab data.
	 */
	return apply_filters( 'mpp_get_admin_tabs', $tabs );
}

?>