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
	'title'    => _x( '2 large descriptions', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'staff', 'keyword', 'michelle' ),
		esc_html_x( 'employees', 'keyword', 'michelle' ),
		esc_html_x( 'people', 'keyword', 'michelle' ),
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/group',
		'core/columns',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '1to1' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"150px"}},"color":{"background":"#010101","text":"#fefefe"}},"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-group alignfull is-style-no-margin-vertical has-text-color has-background" style="background-color:#010101;color:#fefefe;padding-top:150px;padding-bottom:150px"><!-- wp:heading {"align":"wide","className":"is-style-uppercase","fontSize":"large"} -->
<h2 class="alignwide is-style-uppercase has-large-font-size"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:media-text {"align":"","mediaId":999999,"mediaLink":"#0","mediaType":"image","imageFill":false,"className":"is-style-media-on-top"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-style-media-on-top"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="is-style-no-margin-vertical"><strong><?php Starter::the_text( 'people/name' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 'people/job' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"dropCap":true} -->
<p class="has-drop-cap"><?php Starter::the_text( 8, '.' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:media-text {"align":"","mediaId":999999,"mediaLink":"#0","mediaType":"image","imageFill":false,"className":"is-style-media-on-top"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-style-media-on-top"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="is-style-no-margin-vertical"><strong><?php Starter::the_text( 'people/name' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 'people/job' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"dropCap":true} -->
<p class="has-drop-cap"><?php Starter::the_text( 8, '.' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
