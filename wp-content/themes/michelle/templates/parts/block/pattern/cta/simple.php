<?php
/**
 * Block pattern setup file.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.10
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Add block pattern setup args.
Block_Patterns::add_pattern_args( __FILE__, array(
	'title'    => _x( 'Simple', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'button', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'cta', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/buttons',
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull"><!-- wp:separator {"align":"wide"} -->
<hr class="wp-block-separator alignwide"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"38.2%"} -->
<div class="wp-block-column" style="flex-basis:38.2%"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"61.8%"} -->
<div class="wp-block-column" style="flex-basis:61.8%"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {} -->
<div class="wp-block-buttons"><!-- wp:button {"fontSize":"normal"} -->
<div class="wp-block-button has-custom-font-size has-normal-font-size"><a href="#0" class="wp-block-button__link"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
