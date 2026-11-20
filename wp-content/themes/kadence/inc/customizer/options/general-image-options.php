<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;

Theme_Customizer::add_settings(
	[
		'image_border_radius' => [
			'control_type' => 'kadence_range_control',
			'section'      => 'general_image',
			'label'        => esc_html__( 'Border Radius', 'kadence' ),
			'live_method'  => [
				[
					'type'     => 'css',
					'selector' => '.entry-content :where(.wp-block-image) img, .entry-content :where(.wp-block-kadence-image) img',
					'property' => 'border-radius',
					'pattern'  => '$',
					'key'      => 'size',
				],
			],
			'default'      => kadence()->default( 'image_border_radius' ),
			'input_attrs'  => [
				'min'        => [
					'px'  => 0,
					'em'  => 0,
					'rem' => 0,
					'%'   => 0,
				],
				'max'        => [
					'px'  => 100,
					'em'  => 12,
					'rem' => 12,
					'%'   => 100,
				],
				'step'       => [
					'px'  => 1,
					'em'  => 0.01,
					'rem' => 0.01,
					'%'   => 1,
				],
				'units'      => [ 'px', 'em', 'rem', '%' ],
				'responsive' => true,
			],
		],
	]
);
