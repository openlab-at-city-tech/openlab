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
	'title'    => _x( 'A large feature with contact', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'price', 'keyword', 'michelle' ),
		esc_html_x( 'services', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '2to3' );

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:0px;padding-bottom:0px"><!-- wp:media-text {"mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image"} -->
<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"className":"is-style-no-margin-vertical","fontSize":"normal"} -->
<h2 class="is-style-no-margin-vertical has-normal-font-size"><?php Starter::the_text( 'xs' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"className":"is-style-no-margin-vertical","fontSize":"extra-large"} -->
<h3 class="is-style-no-margin-vertical has-extra-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong> &hellip; <strong><?php Starter::the_text( 'price' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"className":"is-style-zigzag-wide"} -->
<hr class="wp-block-separator is-style-zigzag-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-zigzag-wide"} -->
<hr class="wp-block-separator is-style-zigzag-wide"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><?php Starter::the_text( 'xs' ); ?>:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":50,"lineHeight":"1"}}} -->
<p style="font-size:50px;line-height:1"><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><strong><?php Starter::the_text( 'contact/phone' ); ?></strong></a></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:group -->
