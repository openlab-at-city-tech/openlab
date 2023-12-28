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
	'title'    => _x( 'Text with extra hierarchy', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/heading',
		'core/paragraph',
		'core/columns',
		'core/separator',
	),
) );

// Block pattern content:

?>

<!-- wp:heading {"align":"wide","fontSize":"huge"} -->
<h2 class="alignwide has-huge-font-size"><?php Starter::the_text( 'title/l' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:separator {"align":"wide"} -->
<hr class="wp-block-separator alignwide"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column {"width":"61.8%"} -->
<div class="wp-block-column" style="flex-basis:61.8%"><!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 7, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"38.2%"} -->
<div class="wp-block-column" style="flex-basis:38.2%"><!-- wp:paragraph -->
<p><?php Starter::the_text( 6, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
