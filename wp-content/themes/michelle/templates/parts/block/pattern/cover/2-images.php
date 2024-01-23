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
	'title'    => _x( 'With 2 images and description', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/heading',
		'core/image',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '2to3' );
$image2 = Starter::get_image_url( '3to4' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"bottom":"150px","top":"0px"}},"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#010101;color:#fefefe;padding-top:0px;padding-bottom:150px"><!-- wp:spacer {"className":"has-15vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-15vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"id":999991,"width":480,"height":788,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large is-resized"><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999991" width="480" height="788"/></figure>
<!-- /wp:image -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:media-text {"align":"","mediaId":999992,"mediaLink":"#0","mediaType":"image"} -->
<div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999992 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
