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
	'title'    => _x( 'With 3 images', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/group',
		'core/heading',
		'core/gallery',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '2to3' );
$image2 = Starter::get_image_url( '1to1' );
$image3 = Starter::get_image_url( '3to4' );

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:0px;padding-bottom:0px"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="has-text-align-center"><strong><?php Starter::the_text( 'title/m' ); ?></strong></h1>
<!-- /wp:heading -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:gallery {"ids":[999991,999992,999993],"linkTo":"none","align":"wide"} -->
<figure class="wp-block-gallery alignwide columns-3 is-cropped"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image1 ); ?>" data-link="#0" class="wp-image-999991"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image2 ); ?>" data-link="#0" class="wp-image-999992"/></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image3 ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image3 ); ?>" data-link="#0" class="wp-image-999993"/></figure></li></ul></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->
