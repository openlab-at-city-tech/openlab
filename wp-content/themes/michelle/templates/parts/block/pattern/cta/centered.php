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
	'title'    => _x( 'Centered', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'button', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'cta', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/buttons',
		'core/group',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"150px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:150px"><!-- wp:heading {"textAlign":"center","className":"is-style-default"} -->
<h2 class="has-text-align-center is-style-default"><?php Starter::the_text( 'title/m' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"style":{"color":{"background":"#010101","text":"#fefefe"}},"fontSize":"normal"} -->
<div class="wp-block-button has-custom-font-size has-normal-font-size"><a class="wp-block-button__link has-text-color has-background" href="#0" style="background-color:#010101;color:#fefefe"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
