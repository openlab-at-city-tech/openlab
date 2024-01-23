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
	'title'    => _x( 'Cards', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'price', 'keyword', 'michelle' ),
		esc_html_x( 'food menu', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:separator {"align":"wide","className":"is-style-zigzag"} -->
<hr class="wp-block-separator alignwide is-style-zigzag"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#010101;color:#fefefe"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#010101;color:#fefefe"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","className":"is-style-zigzag"} -->
<hr class="wp-block-separator alignwide is-style-zigzag"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-group has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?> &hellip; <?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","className":"is-style-zigzag"} -->
<hr class="wp-block-separator alignwide is-style-zigzag"/>
<!-- /wp:separator -->
