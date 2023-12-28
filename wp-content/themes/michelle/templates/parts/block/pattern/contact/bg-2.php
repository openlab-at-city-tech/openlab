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
	'title'    => _x( 'Columns with background', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'contact', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/group',
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#010101","text":"#fefefe"},"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#010101;color:#fefefe;padding-top:150px;padding-bottom:120px"><!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:separator {"className":"is-style-zigzag-wide"} -->
<hr class="wp-block-separator is-style-zigzag-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 'contact/address' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:separator {"className":"is-style-zigzag-wide"} -->
<hr class="wp-block-separator is-style-zigzag-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 'date/weekday' ); ?> &hellip; 10:00 - 22:00<br><?php Starter::the_text( 'date/weekend' ); ?> &hellip; 12:00 - 02:00</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:separator {"className":"is-style-zigzag-wide"} -->
<hr class="wp-block-separator is-style-zigzag-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"align":"center","className":"is-style-no-margin-vertical"} -->
<p class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'xs' ); ?>:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1"}},"fontSize":"extra-large"} -->
<p class="has-text-align-center has-extra-large-font-size" style="line-height:1"><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
