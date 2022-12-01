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
	'title'    => _x( 'With image and background image', 'Block pattern title.', 'michelle' ),
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

$image1 = Starter::get_image_url( '16to9' );
$image2 = Starter::get_image_url( '1to1' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image1 ); ?>","id":999999,"dimRatio":80,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim" style="padding-top:0px;padding-bottom:0px;min-height:100vh"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image1 ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:media-text {"align":"full","mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image","verticalAlignment":"top"} -->
<div class="wp-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile is-vertically-aligned-top"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading --></div></div>
<!-- /wp:media-text --></div></div>
<!-- /wp:cover -->
