<?php

/**
 * Hemingway: When there's no nav menu, ensure that Course Profile and Home links appear.
 *
 * This theme uses wp_list_pages() rather than a normal WP function for building
 * the default menu.
 */
function openlab_fix_fallback_menu_for_hemingway( $output ) {
	if ( 'hemingway' !== get_template() ) {
		return $output;
	}

	$dbs    = debug_backtrace();
	$gp_key = null;
	foreach ( $dbs as $key => $db ) {
		if ( 'wp_list_pages' === $db['function'] ) {
			$lp_key = $key;
			break;
		}
	}

	if ( null === $lp_key ) {
		return $output;
	}

	// It really doesn't get any worse than this.
	if ( ! isset( $dbs[ $lp_key + 4 ] ) || 'get_header' !== $dbs[ $lp_key + 4 ]['function'] ) {
		return $output;
	}

	// Fake pages.
	$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );
	if ( ! $group_id ) {
		return $output;
	}

	$home_link = sprintf(
		'<li><a title="Site Home" href="%s">Home</a></li>',
		esc_url( trailingslashit( get_option( 'home' ) ) )
	);

	$group_type_label = openlab_get_group_type_label(
		array(
			'group_id' => $group_id,
			'case'     => 'upper',
		)
	);

	$group_link = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) );

	$profile_link = sprintf(
		'<li id="menu-item-group-profile-link" class="group-profile-link"><a href="%s">%s</a>',
		esc_url( $group_link ),
		sprintf( '%s Profile', $group_type_label )
	);

	$output = $profile_link . "\n" . $home_link . "\n" . $output;

	return $output;
}
add_filter( 'wp_list_pages', 'openlab_fix_fallback_menu_for_hemingway' );

/**
 * Hemingway: Add missing label element to comment form.
 */
function openlab_add_missing_label_element_to_comment_form_for_hemingway( $fields ) {
	if ( 'hemingway' !== get_template() ) {
		return $fields;
	}

	$fields['comment'] .= '<label for="comment" class="sr-only">Comment Text</label>';

	return $fields;
}
add_filter( 'comment_form_fields', 'openlab_add_missing_label_element_to_comment_form_for_hemingway' );

add_action(
	'wp_head',
	function() {
		$print_css_url = content_url( 'mu-plugins/theme-fixes/hemingway/print.css' );
		?>
<link rel="stylesheet" href="<?php echo esc_attr( $print_css_url ); ?>" type="text/css" media="print" />
		<?php
	}
);

/**
 * Filter default Accent Color.
 */
add_action(
	'theme_mod_accent_color',
	function( $color ) {
		if ( ! empty( $color ) ) {
			return $color;
		}

		return '#ad0000';
	}
);

/**
 * Ensure that accent_color overrides are in place.
 */
add_action(
	'wp_head',
	function() {
		$accent_color = get_theme_mod( 'accent_color' );
		echo '<style type="text/css">';
		echo sprintf( 'body .blog-menu li:hover > a { color: %s; }', esc_attr( $accent_color ) );
		echo '</style>';
	},
	100
);
