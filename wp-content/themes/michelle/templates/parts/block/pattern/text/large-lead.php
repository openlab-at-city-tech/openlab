<?php
/**
 * Block pattern setup file.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.10
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Add block pattern setup args.
Block_Patterns::add_pattern_args( __FILE__, array(
	'title'    => _x( 'Text with large subheading', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'columns', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/heading',
		'core/paragraph',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading {"className":"is-style-no-margin-vertical","fontSize":"normal"} -->
<h2 class="is-style-no-margin-vertical has-normal-font-size"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.25"},"color":{"text":"#010101"}},"fontSize":"extra-large"} -->
<p class="has-text-color has-extra-large-font-size" style="color:#010101;line-height:1.25"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:paragraph -->
<p><?php Starter::the_text( 7, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
