<?php
/**
 * Handle Genesis menus.
 *
 * @package Genesis
 * @todo Document the function in the functions/menu.php file.
 */

function genesis_nav($args = array()) {

	if ( isset( $args['context'] ) ) // deprecated argument
	_deprecated_argument(__FUNCTION__, '1.2', __('The argument, "context", has been replaced with "theme_location"'));

	$defaults = array(
		'theme_location' => '',
		'type' => 'pages',
		'sort_column' => 'menu_order, post_title',
		'menu_id' => false,
		'menu_class' => 'nav',
		'echo' => true,
		'link_before' => '',
		'link_after' => ''
	);

	$defaults = apply_filters('genesis_nav_default_args', $defaults);
	$args = wp_parse_args($args, $defaults);

	// Allow child theme to short-circuit this function
	$pre = apply_filters('genesis_pre_nav', false, $args);
	if ( $pre ) return $pre;

	$menu = '';

	$list_args = $args;

	// Show Home in the menu (mostly copied from WP source)
	if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = apply_filters('genesis_nav_home_text', __('Home', 'genesis'), $args);
		else
			$text = $args['show_home'];

		$class = '';

		if ( is_front_page() && !is_paged() )
			$class = 'class="home current_page_item"';
		else
			$class = 'class="home"';

		$home = '<li ' . $class . '><a href="' . trailingslashit( home_url() ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';

		$menu .= genesis_get_seo_option('nofollow_home_link') ? genesis_rel_nofollow($home) : $home;

		// If the front page is a page, add it to the exclude list
		if ( get_option('show_on_front') == 'page' && $args['type'] == 'pages' ) {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';

	if ( $args['type'] == 'pages' )
		$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );
	elseif ( $args['type'] == 'categories' )
		$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_categories($list_args) );

	// Apply filters to the nav items
	$menu = apply_filters('genesis_nav_items', $menu, $args);

	$menu_class = ( $args['menu_class'] ) ? ' class="'.esc_attr($args['menu_class']).'"' : '';
	$menu_id = ( $args['menu_id'] ) ? ' id="'.esc_attr($args['menu_id']).'"' : '';

	if ( $menu )
		$menu = '<ul'. $menu_id . $menu_class . '>' . $menu . '</ul>';

	// Apply filters to the final nav output
	$menu = apply_filters('genesis_nav', $menu, $args);

	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}