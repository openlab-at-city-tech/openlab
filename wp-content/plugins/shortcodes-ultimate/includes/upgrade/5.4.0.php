<?php

/**
 * 1. Add `su_option_hide_deprecated` option.
 */
if ( false === get_option( 'su_option_hide_deprecated' ) ) {
	add_option( 'su_option_hide_deprecated', 'on' );
}
