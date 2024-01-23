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
	'title'    => _x( 'Logos', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'images', 'keyword', 'michelle' ),
		esc_html_x( 'clients', 'keyword', 'michelle' ),
		esc_html_x( 'partners', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/gallery',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( 'icon' );

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#efefef","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#efefef;color:#010101;padding-top:150px;padding-bottom:120px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:gallery {"ids":[999991,999992,999993,999994,999995],"columns":5,"imageCrop":false,"linkTo":"none","align":"wide"} -->
<figure class="wp-block-gallery alignwide columns-5"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image ); ?>" data-link="#0" class="wp-image-999991"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image ); ?>" data-link="#0" class="wp-image-999992"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image ); ?>" data-link="#0" class="wp-image-999993"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999994" data-full-url="<?php echo esc_url_raw( $image ); ?>" data-link="#0" class="wp-image-999994"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999995" data-full-url="<?php echo esc_url_raw( $image ); ?>" data-link="#0" class="wp-image-999995"/></figure></li></ul></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->
