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
	'title'    => _x( 'Full-width with no gaps', 'Block pattern title.', 'michelle' ),
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

$image1 = Starter::get_image_url( '3to4' );
$image2 = Starter::get_image_url( '2to3' );
$image3 = Starter::get_image_url( '1to1' );

?>

<!-- wp:gallery {"ids":[999991,999992,999993],"linkTo":"none","align":"full","className":"is-style-no-gaps"} -->
<figure class="wp-block-gallery alignfull columns-3 is-cropped is-style-no-gaps"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image1 ); ?>" data-link="#0" class="wp-image-999991"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image2 ); ?>" data-link="#0" class="wp-image-999992"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image3 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image3 ); ?>" data-link="#0" class="wp-image-999993"/></figure></li></ul></figure>
<!-- /wp:gallery -->
