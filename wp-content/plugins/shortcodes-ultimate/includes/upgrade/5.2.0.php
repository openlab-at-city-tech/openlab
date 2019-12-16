<?php

/**
 * 1. Add `su_option_generator_access` option.
 */
if ( false === get_option( 'su_option_generator_access' ) ) {
	add_option( 'su_option_generator_access', 'manage_options' );
}

/**
 * 2. Add `su_option_enable_shortcodes_in` option.
 */
if ( false === get_option( 'su_option_enable_shortcodes_in' ) ) {

	add_option(
		'su_option_enable_shortcodes_in',
		array( 'term_description', 'widget_text' )
	);

}
