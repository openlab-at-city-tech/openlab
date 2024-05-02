<?php
/**
 * Block styles
 *
 * @package Sydney
 */

function sydney_block_styles() {
	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/site-title',
		array(
			'name'  		=> 'sydney-no-margin',
			'label' 		=> __( 'No margin', 'sydney' ),
		)
	);	

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/paragraph',
		array(
			'name'  		=> 'sydney-no-margin',
			'label' 		=> __( 'No margin', 'sydney' ),
		)
	);	

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/image',
		array(
			'name'  		=> 'sydney-rounded',
			'label' 		=> __( 'Slightly rounded', 'sydney' ),
			'inline_style' => '.wp-block-image.is-style-sydney-rounded img { border-radius:30px; }',
		)
	);

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/gallery',
		array(
			'name'  		=> 'sydney-rounded',
			'label' 		=> __( 'Slightly rounded', 'sydney' ),
			'inline_style' => '.wp-block-gallery.is-style-sydney-rounded img { border-radius:30px; }',
		)
	);	
}
add_action( 'init', 'sydney_block_styles' );