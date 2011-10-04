<?php
/**
 * Register default Genesis layouts.
 *
 * @package Genesis
 */

add_action('genesis_init', 'genesis_create_initial_layouts', 0);
/**
 * Creates the initial layouts when the 'init' action is fired
 *
 * @since 1.4
 */
function genesis_create_initial_layouts() {

	genesis_register_layout( 'content-sidebar', array(
		'label' => __('Content/Sidebar', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/cs.gif',
		'default' => true
	) );

	genesis_register_layout( 'sidebar-content', array(
		'label' => __('Sidebar/Content', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/sc.gif'
	) );

	genesis_register_layout( 'content-sidebar-sidebar', array(
		'label' => __('Content/Sidebar/Sidebar', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/css.gif'
	) );

	genesis_register_layout( 'sidebar-sidebar-content', array(
		'label' => __('Sidebar/Sidebar/Content', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/ssc.gif'
	) );

	genesis_register_layout( 'sidebar-content-sidebar', array(
		'label' => __('Sidebar/Content/Sidebar', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/scs.gif'
	) );

	genesis_register_layout( 'full-width-content', array(
		'label' => __('Full Width Content', 'genesis'),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/c.gif',
	) );

}

/**
 * This function registers new layouts by modifying the global
 * $_genesis_layouts variable.
 *
 * @since 1.4
 */
function genesis_register_layout( $id = '', $args = array() ) {

	global $_genesis_layouts;

	if ( !is_array( $_genesis_layouts ) )
		$_genesis_layouts = array();

	// Don't allow empty $id, or double registrations
	if ( !$id || isset( $_genesis_layouts[$id] ) )
		return false;

	$defaults = array(
		'label' => __( 'No Label Selected', 'genesis' ),
		'img' => GENESIS_ADMIN_IMAGES_URL . '/layouts/none.gif',
	);

	$args = wp_parse_args( $args, $defaults );

	$_genesis_layouts[$id] = $args;

	return $args;

}

/**
 * This function allows a user to identify a layout as being the default
 * layout on a new install, as well as serve as the fallback layout.
 *
 * @since 1.4
 */
function genesis_set_default_layout( $id = '' ) {

	global $_genesis_layouts;

	if ( !is_array( $_genesis_layouts ) )
		$_genesis_layouts = array();

	// Don't allow empty $id, or double registrations
	if ( !$id || !isset( $_genesis_layouts[$id] ) )
		return false;

	// remove default flag for all other layouts
	foreach ( (array)$_genesis_layouts as $key => $value ) {
		if ( isset( $_genesis_layouts[$key]['default'] ) ) {
			unset( $_genesis_layouts[$key]['default'] );
		}
	}

	$_genesis_layouts[$id]['default'] = true;

	return $id;

}

/**
 * This function unregisters layouts by modifying the global
 * $_genesis_layouts variable.
 *
 * @since 1.4
 */
function genesis_unregister_layout( $id = '' ) {

	global $_genesis_layouts;

	if ( !$id || !isset( $_genesis_layouts[$id] ) )
		return false;

	unset( $_genesis_layouts[$id] );

	return true;

}

/**
 * This function returns all registered Genesis Layouts
 *
 * @since 1.4
 */
function genesis_get_layouts() {

	global $_genesis_layouts;

	if ( !is_array( $_genesis_layouts ) )
		$_genesis_layouts = array();

	return $_genesis_layouts;

}

/**
 * This function returns the data from a single layout,
 * specified by the $id passed to it.
 *
 * @since 1.4
 */
function genesis_get_layout( $id ) {

	$layouts = genesis_get_layouts();

	if ( !$id || !isset( $layouts[$id] ) )
		return;

	return $layouts[$id];

}

/**
 * This function returns the layout that is set to default.
 *
 * @since 1.4
 */
function genesis_get_default_layout() {

	global $_genesis_layouts;

	$default = '';

	foreach ( (array)$_genesis_layouts as $key => $value ) {
		if ( isset( $value['default'] ) && $value['default'] ) {
			$default = $key; break;
		}
	}

	// return default layout, if exists
	if ( $default ) {
		return $default;
	}

	return 'nolayout';

}

/**
 * This function checks both the custom field and
 * the theme option to find the user-selected site
 * layout, and returns it.
 *
 * @since 0.2.2
 */
function genesis_site_layout() {

	// If viewing a singular page/post
	if ( is_singular() ) {

		$custom_field = genesis_get_custom_field( '_genesis_layout' );
		$site_layout = $custom_field ? $custom_field : genesis_get_option( 'site_layout' );

	}

	// If viewing a taxonomy archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		global $wp_query;

		$term = $wp_query->get_queried_object();

		$site_layout = $term && isset( $term->meta['layout'] ) && $term->meta['layout'] ? $term->meta['layout'] : genesis_get_option( 'site_layout' );

	}

	// If viewing an author archive
	elseif( is_author() ) {

		$site_layout = get_the_author_meta( 'layout', (int)get_query_var('author') ) ? get_the_author_meta( 'layout', (int)get_query_var('author') ) : genesis_get_option('site_layout');

	}

	// else pull the theme option
	else {

		$site_layout = genesis_get_option( 'site_layout' );

	}

	// Use default layout as a fallback, if necessary
	if ( !genesis_get_layout( $site_layout ) ) {
		$site_layout = genesis_get_default_layout();
	}

	return esc_attr( apply_filters( 'genesis_site_layout', $site_layout ) );

}

/**
* A helper function to do the logic, and potentially echo/return a structural wrap div.
*
* @since 1.6
*/
function genesis_structural_wrap( $context = '', $output = '<div class="wrap">', $echo = true ) {

	$genesis_structural_wraps = get_theme_support( 'genesis-structural-wraps' );

	if ( ! in_array( $context, (array) $genesis_structural_wraps[0] ) )
		return '';

	switch( $output ) {
		case 'open':
			$output = '<div class="wrap">';
			break;
		case 'close':
			$output = '</div><!-- end .wrap -->';
			break;
	}

	if ( $echo )
		echo $output;
	else
		return $output;

}