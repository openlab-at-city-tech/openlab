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
	'title'    => _x( 'Testimonials', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
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

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:120px"><!-- wp:heading {"align":"wide"} -->
<h2 class="alignwide"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><p><?php Starter::the_text( 5, '.' ); ?></p><cite><?php Starter::the_text( 'people/name' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><p><?php Starter::the_text( 5, '.' ); ?></p><cite><?php Starter::the_text( 'people/name' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
