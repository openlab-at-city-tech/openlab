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
	'title'    => _x( 'Variable, with description', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'images', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/gallery',
		'core/image',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '16to9' );
$image2 = Starter::get_image_url( '3to4' );
$image3 = Starter::get_image_url( '2to3' );
$image4 = Starter::get_image_url( '1to1' );

?>

<!-- wp:columns {"verticalAlignment":"bottom","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-bottom"><!-- wp:column {"verticalAlignment":"bottom","width":"61.8%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:61.8%"><!-- wp:gallery {"ids":[2136,2137,1966,1970],"columns":2,"linkTo":"none","className":"is-style-variable"} -->
<figure class="wp-block-gallery columns-2 is-cropped is-style-variable"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image1 ); ?>" data-link="#0" class="wp-image-999991"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image2 ); ?>" data-link="#0" class="wp-image-999992"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image3 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image3 ); ?>" data-link="#0" class="wp-image-999993"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image4 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999994" data-full-url="<?php echo esc_url_raw( $image4 ); ?>" data-link="#0" class="wp-image-999994"/></figure></li></ul></figure>
<!-- /wp:gallery --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"38.2%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:38.2%"><!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
