<?php

defined( 'ABSPATH' ) || die;

if ( ! defined( 'GUTENBERG_VERSION_REQUIRED' ) ) {
	define( 'GUTENBERG_VERSION_REQUIRED', '5.7.0' );
}

// Check if Gutenberg is activated
if ( ! function_exists( 'register_block_type' ) ) {
	$gutenbergInstallUrl = wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'install-plugin',
				'plugin' => 'gutenberg'
			),
			admin_url( 'update.php' )
		),
		'install-plugin_gutenberg'
	);

	wp_die(
		esc_html__( 'Gutenberg is not detected! Activate it or', 'advanced-gutenberg' )
		. ': <a href="' . esc_attr( $gutenbergInstallUrl ) . '">' . esc_html__( 'Install Gutenberg Now!',
			'advanced-gutenberg' ) . '</a>'
	);
	exit;
}

if ( defined( 'GUTENBERG_VERSION' ) ) {
	if ( version_compare( GUTENBERG_VERSION, GUTENBERG_VERSION_REQUIRED, 'lt' ) ) {
		wp_die(
			esc_html__( 'We require at least Gutenberg version ', 'advanced-gutenberg' )
			. esc_html( GUTENBERG_VERSION_REQUIRED ) . '. ' .
			esc_html__( 'Please update Gutenberg then comeback later!', 'advanced-gutenberg' )
		);
		exit;
	}
}

// Add default settings for first time install
$saved_settings = get_option( 'advgb_settings' );

if ( $saved_settings === false ) {
	update_option( 'advgb_settings', array(
		'gallery_lightbox'            => 1,
		'gallery_lightbox_caption'    => '1',
		'blocks_icon_color'           => '#655997',
		'disable_wpautop'             => 0,
		'enable_columns_visual_guide' => 1,
		'enable_block_access'         => 1,
		'enable_custom_styles'        => 1,
		'enable_advgb_blocks'         => 1,
		'enable_pp_branding'          => 1,
		'enable_core_blocks_features' => 1,
		'block_controls'              => 1,
		'block_extend'                => 0,
		'reusable_blocks'             => 1
	) );
}

/* Delete deprecated options
 * @todo - Remove in 4.0 */
delete_option( 'advgb_jureview_installation_time' );
delete_option( 'advgb_jufeedback_version' );
delete_option( 'ppb_reviews_installed_on' ); // Added in 2.10.4 and disabled in 2.10.5
delete_option( 'advgb_reviews_installed_on' ); // Added in 2.11.0 and disabled in 2.11.1
