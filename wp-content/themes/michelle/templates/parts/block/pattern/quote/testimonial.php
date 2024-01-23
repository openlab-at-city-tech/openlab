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
	'title'    => _x( 'Testimonial', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'quote', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/quote',
	),
) );

// Block pattern content:

?>

<!-- wp:heading {"align":"wide","className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="alignwide is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:pullquote {"align":"wide","className":"is-style-simple"} -->
<figure class="wp-block-pullquote alignwide is-style-simple"><blockquote><p><?php Starter::the_text( 8, '.' ); ?></p><cite><?php Starter::the_text( 'people/name' ); ?></cite></blockquote></figure>
<!-- /wp:pullquote -->
