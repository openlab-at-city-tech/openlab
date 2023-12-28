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
	'title'    => _x( 'Food menu', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'price', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":80} -->
<div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group -->
<div class="wp-block-group"><!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><em><?php Starter::the_text( 2, '.' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><strong><?php Starter::the_text( 'price' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
