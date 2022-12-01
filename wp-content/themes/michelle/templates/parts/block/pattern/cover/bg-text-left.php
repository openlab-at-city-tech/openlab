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
	'title'    => _x( 'With background and text on the left', 'Block pattern title.', 'michelle' ),
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

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"dimRatio":80,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim" style="padding-top:0px;padding-bottom:120px;min-height:100vh"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/s' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
