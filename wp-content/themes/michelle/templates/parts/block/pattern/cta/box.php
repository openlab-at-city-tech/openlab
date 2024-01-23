<?php
/**
 * Block pattern setup file.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Add block pattern setup args.
Block_Patterns::add_pattern_args( __FILE__, array(
	'title'    => _x( 'Boxed', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'button', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'cta', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/buttons',
		'core/columns',
		'core/group',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"color":{"background":"#010101","text":"#fefefe"}},"className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center is-style-stacked-on-tablet has-text-color has-background" style="background-color:#010101;color:#fefefe"><!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:heading {"className":"is-style-screen-reader-text"} -->
<h2 class="is-style-screen-reader-text"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet" style="flex-basis:15%"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"25%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:25%"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"style":{"color":{"background":"#fefefe","text":"#010101"}},"fontSize":"normal"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 has-custom-font-size has-normal-font-size"><a class="wp-block-button__link has-text-color has-background" href="#0" style="background-color:#fefefe;color:#010101"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
