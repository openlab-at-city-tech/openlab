<?php

add_filter(
	'wp_nav_menu_objects',
	function( $items, $args ) {
		if ( 'primary-menu' !== $args->theme_location ) {
			remove_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );
		}

		return $items;
	},
	0,
	2
);
