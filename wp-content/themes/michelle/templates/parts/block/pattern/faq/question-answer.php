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
	'title'    => _x( 'Question and answer', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'FAQ - frequently asked questions', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"60px","bottom":"30px"}}},"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-group alignfull is-style-no-margin-vertical" style="padding-top:60px;padding-bottom:30px"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"66.66%","className":"is-style-alignleft"} -->
<div class="wp-block-column is-style-alignleft" style="flex-basis:66.66%"><!-- wp:heading {"fontSize":"heading-4"} -->
<h2><?php Starter::the_text( 'title/l', '?' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"640px","className":"is-style-alignright"} -->
<div class="wp-block-column is-style-alignright" style="flex-basis:640px"><!-- wp:paragraph -->
<p><?php Starter::the_text( 8, '.' ); ?>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
