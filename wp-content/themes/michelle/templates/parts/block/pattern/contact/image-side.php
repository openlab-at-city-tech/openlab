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
	'title'    => _x( 'With image on side', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'contact', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/media-text',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '2to3' );

?>

<!-- wp:media-text {"mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image","mediaWidth":38} -->
<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile" style="grid-template-columns:auto 38%"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 'contact/address' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 'date/weekday' ); ?> &hellip; 09:00 - 16:00<br><?php Starter::the_text( 'date/weekend' ); ?> &hellip; &times;</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="mailto:<?php Starter::the_text( 'contact/email' ); ?>"><?php Starter::the_text( 'contact/email' ); ?></a><br><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text -->
