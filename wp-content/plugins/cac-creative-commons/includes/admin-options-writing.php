<?php
/**
 * Admin code hooked to "Settings > Writing" page.
 *
 * @package cac-creative-commons
 */

// Add our settings to the page.
add_settings_section(
	'cac-creative-commons',
	__( 'Creative Commons', 'cac-creative-commons' ),
	'cac_cc_settings_writing_section',
	'writing'
);

/**
 * Output our markup to the "Settings > Writing" admin page.
 *
 * @since 0.1.0
 */
function cac_cc_settings_writing_section() {
	echo '<table class="form-table">';

	// Table row helper function.
	$row = function( $second_col = '', $first_col = '' ) {
		printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', $first_col, $second_col );
	};

	$choose_header = esc_html__( 'Default License', 'cac-creative-commons' );
	$row( cac_cc_get_license_logo() . sprintf( '<p id="cac-cc-link">%1$s</p>%2$s', cac_cc_get_license_link(), cac_cc_get_button_chooser() ), $choose_header );

	$size_header = esc_html__( 'Default Icon Size', 'cac-creative-commons' );
	$size_normal = esc_html__( 'Normal (88px x 31px)', 'cac-creative-commons' );
	$size_compact   = esc_html__( 'Compact (80px x 15px)', 'cac-creative-commons' );

	$size_normal_selected  = selected( cac_cc_get_license_logo_size(), 'normal', false );
	$size_compact_selected = selected( cac_cc_get_license_logo_size(), 'compact', false );
	$size_select = <<<SELECT

<select name="cac-cc-default-size" id="cac-cc-default-size">
	<option value="normal" {$size_normal_selected}>{$size_normal}</option>
	<option value="compact" {$size_compact_selected}>{$size_compact}</option>
</select>

SELECT;

	$widgets_menu_txt = esc_html__( 'Appearance > Widgets', 'cac-creative-commons' );

	/* translators: %s is for the "Appearance > Widgets" admin menu link. */
	$instructions = sprintf( __( 'To display your site\'s license, use the provided "Creative Commons License" widget available at %s.', 'cac-creative-commons' ), sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'widgets.php' ), $widgets_menu_txt ) );
	$instructions = sprintf( '<p class="timezone-info">%s</p>', $instructions );

	$row( $size_select . $instructions, $size_header );

	echo '</table>';
}