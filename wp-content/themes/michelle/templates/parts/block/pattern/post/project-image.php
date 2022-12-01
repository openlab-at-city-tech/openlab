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
	'title'    => _x( 'Project with image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'posts', 'keyword', 'michelle' ),
		esc_html_x( 'portfolio', 'keyword', 'michelle' ),
		esc_html_x( 'image', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:media-text {"mediaId":999999,"mediaLink":"#0","mediaType":"image","className":"is-style-media-on-top"} -->
<div class="wp-block-media-text alignwide is-stacked-on-mobile is-style-media-on-top"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":""} -->
<div class="wp-block-column"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"extra-large"} -->
<h2 class="is-style-uppercase has-extra-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":""} -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:media-text -->
