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
	'title'    => _x( 'Text with image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'image', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '3to4' );

?>

<!-- wp:media-text {"mediaId":999999,"mediaLink":"#0","mediaType":"image","imageFill":false,"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-media-text alignwide is-stacked-on-mobile is-style-no-margin-vertical"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"dropCap":true} -->
<p class="has-drop-cap"><?php Starter::the_text( 7, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:media-text -->
