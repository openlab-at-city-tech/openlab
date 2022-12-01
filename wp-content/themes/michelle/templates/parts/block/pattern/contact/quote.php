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
		esc_html_x( 'contact', 'keyword', 'michelle' ),
		esc_html_x( 'quote', 'keyword', 'michelle' ),
		esc_html_x( 'testimonial', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/quote',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"38%"} -->
<div class="wp-block-column" style="flex-basis:38%"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 'contact/address' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a><br><a href="mailto:<?php Starter::the_text( 'contact/email' ); ?>"><?php Starter::the_text( 'contact/email' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"62%"} -->
<div class="wp-block-column" style="flex-basis:62%"><!-- wp:quote {"className":"is-style-large"} -->
<blockquote class="wp-block-quote is-style-large"><p><?php Starter::the_text( 3, '.' ); ?></p><cite><?php Starter::the_text( 'people/name' ); ?></cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
