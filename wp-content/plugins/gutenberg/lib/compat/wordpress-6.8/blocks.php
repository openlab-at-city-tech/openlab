<?php
/**
 * Temporary compatibility shims for block APIs present in Gutenberg.
 *
 * @package gutenberg
 */

/**
 * Filters the block type arguments during registration to stabilize experimental block supports.
 *
 * This is a temporary compatibility shim as the approach in core is for this to be handled
 * within the WP_Block_Type class rather than requiring a filter.
 *
 * @param array $args Array of arguments for registering a block type.
 * @return array Array of arguments for registering a block type.
 */
function gutenberg_stabilize_experimental_block_supports( $args ) {
	if ( empty( $args['supports']['typography'] ) ) {
		return $args;
	}

	$experimental_typography_supports_to_stable = array(
		'__experimentalFontFamily'     => 'fontFamily',
		'__experimentalFontStyle'      => 'fontStyle',
		'__experimentalFontWeight'     => 'fontWeight',
		'__experimentalLetterSpacing'  => 'letterSpacing',
		'__experimentalTextDecoration' => 'textDecoration',
		'__experimentalTextTransform'  => 'textTransform',
	);

	$current_typography_supports = $args['supports']['typography'];
	$stable_typography_supports  = array();

	foreach ( $current_typography_supports as $key => $value ) {
		if ( array_key_exists( $key, $experimental_typography_supports_to_stable ) ) {
			$stable_typography_supports[ $experimental_typography_supports_to_stable[ $key ] ] = $value;
		} else {
			$stable_typography_supports[ $key ] = $value;
		}
	}

	$args['supports']['typography'] = $stable_typography_supports;

	return $args;
}

add_filter( 'register_block_type_args', 'gutenberg_stabilize_experimental_block_supports', PHP_INT_MAX, 1 );
