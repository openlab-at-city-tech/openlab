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
	'title'    => _x( 'Text with parallax background', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'image', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/cover',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"hasParallax":true,"dimRatio":0,"overlayColor":"palette-2","contentPosition":"center center","align":"full","className":"is-style-no-margin-vertical","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}}}} -->
<div class="wp-block-cover alignfull has-palette-2-background-color has-parallax is-style-no-margin-vertical" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;background-image:url(<?php echo esc_url_raw( $image ); ?>)"><div class="wp-block-cover__inner-container"><!-- wp:columns {"align":"wide","className":"is-style-no-gaps"} -->
<div class="wp-block-columns alignwide is-style-no-gaps"><!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:cover {"customOverlayColor":"#010101","minHeight":100,"minHeightUnit":"vh","contentPosition":"center center"} -->
<div class="wp-block-cover has-background-dim" style="background-color:#010101;min-height:100vh"><div class="wp-block-cover__inner-container"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"fontSize":"large"} -->
<ul class="has-large-font-size"><li><?php Starter::the_text( 'xs' ); ?></li><li><?php Starter::the_text( 's' ); ?></li><li><?php Starter::the_text( 'xs' ); ?></li><li><?php Starter::the_text( 's' ); ?></li></ul>
<!-- /wp:list --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
