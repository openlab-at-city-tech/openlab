<?php

/**
 * 1. Add `su_option_supported_blocks` option.
 */
if ( false === get_option( 'su_option_supported_blocks' ) ) {

	add_option(
		'su_option_supported_blocks',
		array_keys( su_get_config( 'supported-blocks' ) )
	);

}
