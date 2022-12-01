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
	'title'    => _x( 'Project with gallery', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'posts', 'keyword', 'michelle' ),
		esc_html_x( 'portfolio', 'keyword', 'michelle' ),
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'call to action', 'keyword', 'michelle' ),
		esc_html_x( 'button', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/image',
		'core/gallery',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '3to4' );
$image2 = Starter::get_image_url( '2to3' );
$image3 = Starter::get_image_url( '1to1' );

?>

<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide"><div class="wp-block-group__inner-container"><!-- wp:gallery {"ids":[999991,999992,999993],"linkTo":"none","align":"wide","className":"is-style-no-gaps has-no-margin-bottom"} -->
<figure class="wp-block-gallery alignwide columns-3 is-cropped is-style-no-gaps has-no-margin-bottom"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image1 ); ?>" data-link="#0" class="wp-image-999991"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image2 ); ?>" data-link="#0" class="wp-image-999992"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image3 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image3 ); ?>" data-link="#0" class="wp-image-999993"/></figure></li></ul></figure>
<!-- /wp:gallery -->

<!-- wp:columns {"align":"wide","style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-columns alignwide has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"extra-large"} -->
<h2 class="is-style-uppercase has-extra-large-font-size"><strong><a href="#0"><?php Starter::the_text( 'title/s' ); ?></a></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:paragraph -->
<p><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="#0"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:group -->
