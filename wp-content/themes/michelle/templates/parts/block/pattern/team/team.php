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
	'title'    => _x( '4 members', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'staff', 'keyword', 'michelle' ),
		esc_html_x( 'employees', 'keyword', 'michelle' ),
		esc_html_x( 'people', 'keyword', 'michelle' ),
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/group',
		'core/columns',
		'core/gallery',
	),
) );

// Block pattern content:

$image1 = Starter::get_image_url( '2to3' );
$image2 = Starter::get_image_url( '1to1' );
$image3 = Starter::get_image_url( '3to4' );
$image4 = Starter::get_image_url( '16to9' );

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"150px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:150px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:gallery {"ids":[999991,999992,999993,999994],"columns":4,"linkTo":"none","align":"wide"} -->
<figure class="wp-block-gallery alignwide columns-4 is-cropped"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image1 ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" data-id="999991" data-full-url="<?php echo esc_url_raw( $image1 ); ?>" data-link="#0" class="wp-image-999991"/><figcaption class="blocks-gallery-item__caption"><?php Starter::the_text( 'people/name' ); ?></figcaption></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image2 ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" data-id="999992" data-full-url="<?php echo esc_url_raw( $image2 ); ?>" data-link="#0" class="wp-image-999992"/><figcaption class="blocks-gallery-item__caption"><?php Starter::the_text( 'people/name' ); ?></figcaption></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image3 ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" data-id="999993" data-full-url="<?php echo esc_url_raw( $image3 ); ?>" data-link="#0" class="wp-image-999993"/><figcaption class="blocks-gallery-item__caption"><?php Starter::the_text( 'people/name' ); ?></figcaption></figure></li><li class="blocks-gallery-item"><figure><img src="<?php echo esc_url_raw( $image4 ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" data-id="999994" data-full-url="<?php echo esc_url_raw( $image4 ); ?>" data-link="#0" class="wp-image-999994"/><figcaption class="blocks-gallery-item__caption"><?php Starter::the_text( 'people/name' ); ?></figcaption></figure></li></ul></figure>
<!-- /wp:gallery -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"style":{"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background" style="background-color:#010101;color:#fefefe"><?php Starter::the_text( 'people/name' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
