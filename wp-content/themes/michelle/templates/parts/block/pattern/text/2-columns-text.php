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
	'title'    => _x( 'Centered main heading with 2 columns text', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/heading',
		'core/paragraph',
	),
) );

// Block pattern content:

?>

<!-- wp:paragraph {"align":"center","className":"is-style-uppercase"} -->
<p class="has-text-align-center is-style-uppercase"><?php Starter::the_text( 'title/s' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="has-text-align-center"><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
