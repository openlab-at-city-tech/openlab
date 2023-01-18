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
	'title'    => _x( 'Testimonial: With background image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'quote', 'keyword', 'michelle' ),
		esc_html_x( 'testimonial', 'keyword', 'michelle' ),
		esc_html_x( 'photo', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/cover',
		'core/columns',
		'core/quote',
		'core/image',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '16to9' );
$image2 = Starter::get_image_url( '1to1' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image1 ); ?>","id":999991,"hasParallax":true,"dimRatio":80,"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim has-parallax" style="padding-top:150px;padding-bottom:120px;background-image:url(<?php echo esc_url_raw( $image1 ); ?>)"><div class="wp-block-cover__inner-container"><!-- wp:heading {"className":"is-style-screen-reader-text"} -->
<h2 class="is-style-screen-reader-text"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%"><!-- wp:quote {"className":"is-style-large"} -->
<blockquote class="wp-block-quote is-style-large"><p><?php Starter::the_text( 5, '.' ); ?></p></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"20%","className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet" style="flex-basis:20%"></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"80%"} -->
<div class="wp-block-column" style="flex-basis:80%"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%"><!-- wp:media-text {"align":"","mediaId":999992,"mediaType":"image","style":{"color":{"background":"#010101","text":"#fefefe"}},"className":"is-style-media-on-top"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-style-media-on-top has-text-color has-background" style="background-color:#010101;color:#fefefe"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999992 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="is-style-no-margin-vertical"><?php Starter::the_text( 'people/name' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><em><?php Starter::the_text( 's' ); ?></em></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
