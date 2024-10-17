<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

ob_start(); ?>
<div class="kadence-compontent-custom fonts-flush-button wp-clearfix">
	<span class="customize-control-title">
		<?php esc_html_e( 'Flush Local Fonts Cache', 'kadence' ); ?>
	</span>
	<span class="description customize-control-description">
		<?php esc_html_e( 'Click the button to reset the local fonts cache', 'kadence' ); ?>
	</span>
	<input type="button" class="button kadence-flush-local-fonts-button" name="kadence-flush-local-fonts-button" value="<?php esc_attr_e( 'Flush Local Font Files', 'kadence' ); ?>" />
</div>
<?php
$kadence_flush_button = ob_get_clean();

Theme_Customizer::add_settings(
	array(
		'microdata' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'microdata' ),
			'label'        => esc_html__( 'Enable Microdata Schema', 'kadence' ),
		),
		'theme_json_mode' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'theme_json_mode' ),
			'label'        => esc_html__( 'Enable Optimized Group Block', 'kadence' ),
		),
		'enable_scroll_to_id' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'enable_scroll_to_id' ),
			'label'        => esc_html__( 'Enable Scroll To ID', 'kadence' ),
		),
		'lightbox' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'lightbox' ),
			'label'        => esc_html__( 'Enable Lightbox', 'kadence' ),
		),
		'load_fonts_local' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'load_fonts_local' ),
			'label'        => esc_html__( 'Load Google Fonts Locally', 'kadence' ),
		),
		'preload_fonts_local' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'preload_fonts_local' ),
			'label'        => esc_html__( 'Preload Local Fonts', 'kadence' ),
			'context'      => array(
				array(
					'setting'    => 'load_fonts_local',
					'operator'   => '==',
					'value'      => true,
				),
			),
		),
		'load_fonts_local_flush' => array(
			'control_type' => 'kadence_blank_control',
			'section'      => 'general_performance',
			'settings'     => false,
			'description'  => $kadence_flush_button,
			'context'      => array(
				array(
					'setting'    => 'load_fonts_local',
					'operator'   => '==',
					'value'      => true,
				),
			),
		),
		'enable_preload' => array(
			'control_type' => 'kadence_switch_control',
			'sanitize'     => 'kadence_sanitize_toggle',
			'section'      => 'general_performance',
			'default'      => kadence()->default( 'enable_preload' ),
			'label'        => esc_html__( 'Enable CSS Preload', 'kadence' ),
		),
	)
);
