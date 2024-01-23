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
	'title'    => _x( 'A large feature', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'services', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '1to1' );

?>

<!-- wp:media-text {"align":"full","mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image","imageFill":true,"style":{"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-media-text alignfull has-media-on-the-right is-stacked-on-mobile is-image-fill has-text-color has-background" style="background-color:#010101;color:#fefefe"><figure class="wp-block-media-text__media" style="background-image:url(<?php echo esc_url_raw( $image ); ?>);background-position:50% 50%"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/m' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"className":"has-40vmin-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-40vmin-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->
