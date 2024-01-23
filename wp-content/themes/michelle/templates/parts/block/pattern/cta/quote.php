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
	'title'    => _x( 'With quote', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'button', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'cta', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/buttons',
		'core/columns',
		'core/quote',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:quote {"className":"is-style-large"} -->
<blockquote class="wp-block-quote is-style-large"><p><?php Starter::the_text( 3, '.' ); ?></p><cite><?php Starter::the_text( 'people/name' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"10%","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet" style="flex-basis:10%"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/m' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="#0"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
