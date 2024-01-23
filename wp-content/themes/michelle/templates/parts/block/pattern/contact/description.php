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
	'title'    => _x( 'With description', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'contact', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:heading {"level":3,"className":"has-no-margin is-style-uppercase","fontSize":"normal"} -->
<h3 class="has-no-margin is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'xs' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><a href="mailto:<?php Starter::the_text( 'contact/email' ); ?>"><?php Starter::the_text( 'contact/email' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"has-no-margin is-style-uppercase","fontSize":"normal"} -->
<h3 class="has-no-margin is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'xs' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
