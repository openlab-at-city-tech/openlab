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
	'title'    => _x( 'Large with image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'button', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'cta', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
		'core/image',
		'core/buttons',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '1to1' );

?>

<!-- wp:media-text {"align":"full","mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image","imageFill":true,"style":{"color":{"background":"#010101","text":"#fefefe"}},"className":"has-full-screen-min-height"} -->
<div class="wp-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile is-image-fill has-full-screen-min-height has-text-color has-background" style="background-color:#010101;color:#fefefe"><figure class="wp-block-media-text__media" style="background-image:url(<?php echo esc_url_raw( $image ); ?>);background-position:50% 50%"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"480px"} -->
<div class="wp-block-column" style="flex-basis:480px"><!-- wp:heading {"className":"is-style-default"} -->
<h2 class="is-style-default"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"fontSize":"normal"} -->
<div class="wp-block-button has-custom-font-size has-normal-font-size"><a class="wp-block-button__link" href="#0"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:media-text -->
