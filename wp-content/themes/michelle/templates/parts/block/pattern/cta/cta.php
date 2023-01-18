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
	'title'    => _x( 'Fullwidth with 2 buttons', 'Block pattern title.', 'michelle' ),
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

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:120px"><!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center is-style-stacked-on-tablet"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"extra-large"} -->
<h2 class="is-style-uppercase has-extra-large-font-size"><strong><?php Starter::the_text( 'title/m' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"5%","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet" style="flex-basis:5%"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":50,"style":{"color":{"background":"#010101","text":"#fefefe"}},"fontSize":"normal"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-50 has-custom-font-size has-normal-font-size"><a class="wp-block-button__link has-text-color has-background" href="#0" style="background-color:#010101;color:#fefefe"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"width":50,"style":{"color":{"text":"#010101"}},"className":"is-style-outline","fontSize":"normal"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-50 has-custom-font-size is-style-outline has-normal-font-size"><a class="wp-block-button__link has-text-color" href="#0" style="color:#010101"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
