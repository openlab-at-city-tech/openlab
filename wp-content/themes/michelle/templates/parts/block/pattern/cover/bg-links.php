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
	'title'    => _x( 'With page links and background', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/cover',
		'core/heading',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"dimRatio":80,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim" style="padding-top:0px;padding-bottom:120px;min-height:100vh"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:spacer {"className":"has-15vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-15vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide is-style-stacked-on-tablet"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"20%","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet" style="flex-basis:20%"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:paragraph {"style":{"color":{"text":"#fefefe"}},"fontSize":"extra-large"} -->
<p class="has-text-color has-extra-large-font-size" style="color:#fefefe"><a href="#0"><?php Starter::the_text( 'xs' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#fefefe"}},"fontSize":"extra-large"} -->
<p class="has-text-color has-extra-large-font-size" style="color:#fefefe"><a href="#0"><?php Starter::the_text( 's' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#fefefe"}},"fontSize":"extra-large"} -->
<p class="has-text-color has-extra-large-font-size" style="color:#fefefe"><a href="#0"><?php Starter::the_text( 'xs' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#fefefe"}},"fontSize":"extra-large"} -->
<p class="has-text-color has-extra-large-font-size" style="color:#fefefe"><a href="#0"><?php Starter::the_text( 's' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
