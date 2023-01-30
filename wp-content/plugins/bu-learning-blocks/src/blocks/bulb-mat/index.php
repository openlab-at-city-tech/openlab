<?php
/**
 * Matching question block
 *
 * Register dynamic block functions
 *
 * @since   0.0.7
 * @package BU Learning Blocks
 */

namespace BU\Plugins\LearningBlocks;

/**
 * Render the dynamic block
 *
 * @param object $attributes The block's attributes.
 * @param string $content The block's content.
 * @return string The html markup for the block
 */
function bulb_render_block_mat( $attributes, $content ) {
	// Get the question block instance id.
	$id               = $attributes['id'];
	$background_color = $attributes['backgroundColorControl'];
	$text_color       = $attributes['textColorControl'];
	$font_size        = $attributes['fontSize'];

	// Parse any shortcodes in answer array.
	foreach ( $attributes['answers'] as &$answer ) {
		$answer['feedback'] = do_shortcode( $answer['feedback'] );
		$answer['answer']   = do_shortcode( $answer['answer'] );
	}

	// Parse any shorcodes in feedback.
	$attributes['feedback']['correct']   = do_shortcode( $attributes['feedback']['correct'] );
	$attributes['feedback']['incorrect'] = do_shortcode( $attributes['feedback']['incorrect'] );

	// Transform gutenberg attributes into the proposed data structure.
	$data = [
		'type'     => $attributes['type'],
		'header'   => do_shortcode( $attributes['header'] ),
		'body'     => do_shortcode( $attributes['body'] ),
		'answers'  => $attributes['answers'],
		'feedback' => $attributes['feedback'],
	];

	// Save the block data as a JS variable.
	// Use the instance id as the variable name.
	wp_localize_script( 'bulb-frontend-js', $id, $data );

	// Print a question block wrapper with the same instance id.
	// The JS code will then be able to connect the question wrapper with its data.
	return '<div id="' . $id . '" class="bulb-question" style="background-color:' . $background_color . ';color:' . $text_color . ';font-size:' . $font_size . 'px;"></div>';
}

/**
 * Register the dynamic block
 *
 * @return void
 */
function bulb_register_question_mat() {
	register_block_type(
		'bulb/question-mat', [
			'attributes'      => [
				'id'                     => [
					'default' => '',
					'type'    => 'string',
				],
				'type'                   => [
					'default' => 'matching',
					'type'    => 'string',
				],
				'header'                 => [
					'default' => '',
					'type'    => 'string',
				],
				'body'                   => [
					'default' => '',
					'type'    => 'string',
				],
				'answers'                => [
					'default' => [
						[
							'answer'   => '',
							'feedback' => '',
							'correct'  => 'B',
						],
						[
							'answer'   => '',
							'feedback' => '',
							'correct'  => 'A',
						],
					],
					'type'    => 'array',
					'items'   => [
						'type' => 'object',
					],
				],
				'feedback'               => [
					'default' => [
						'correct'   => '',
						'incorrect' => '',
					],
					'type'    => 'object',
				],
				'textAlignment'          => [
					'default' => 'left',
					'type'    => 'string',
				],
				'backgroundColorControl' => [
					'default' => 'left',
					'type'    => 'string',
				],
				'textColorControl'       => [
					'default' => '',
					'type'    => 'string',
				],
				'fontSize'               => [
					'default' => '',
					'type'    => 'string',
				],
			],
			'render_callback' => __NAMESPACE__ . '\bulb_render_block_mat',
		]
	);
}
add_action( 'init', __NAMESPACE__ . '\bulb_register_question_mat' );
