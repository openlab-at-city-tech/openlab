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
	'title'    => _x( 'With social links', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'contact', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"5%"} -->
<div class="wp-block-column" style="flex-basis:5%"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:paragraph -->
<p><?php Starter::the_text( 'contact/address' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a><br><a href="mailto:<?php Starter::the_text( 'contact/email' ); ?>"><?php Starter::the_text( 'contact/email' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:list -->
<ul><li><a href="#0">Facebook</a></li><li><a href="#0">Instagram</a></li><li><a href="#0">YouTube</a></li><li><a href="#0">Twitter</a></li></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
