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
	'title'    => _x( 'Full-screen with bottom text', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/cover',
		'core/heading',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","hasParallax":true,"dimRatio":80,"minHeight":100,"minHeightUnit":"vh","contentPosition":"bottom center","align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim has-parallax has-custom-content-position is-position-bottom-center" style="padding-top:0px;padding-bottom:120px;background-image:url(<?php echo esc_url_raw( $image ); ?>);min-height:100vh"><div class="wp-block-cover__inner-container"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
