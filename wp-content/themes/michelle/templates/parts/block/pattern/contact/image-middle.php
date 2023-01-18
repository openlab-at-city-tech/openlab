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
	'title'    => _x( 'With image in the middle', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'contact', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '2to3' );

?>

<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"is-style-stacked-on-tablet"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center is-style-stacked-on-tablet"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"textAlign":"center","className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="has-text-align-center is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 'contact/address' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><a href="tel:<?php Starter::the_text( 'contact/phone' ); ?>"><?php Starter::the_text( 'contact/phone' ); ?></a><br><a href="mailto:<?php Starter::the_text( 'contact/email' ); ?>"><?php Starter::the_text( 'contact/email' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"id":999999,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php esc_attr_e( 'Image alt text', 'michelle' ); ?>" class="wp-image-999999"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"textAlign":"center","className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="has-text-align-center is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 2, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"is-style-uppercase","fontSize":"large"} -->
<p class="has-text-align-center is-style-uppercase has-large-font-size"><?php Starter::the_text( 'date/weekday' ); ?><br><strong>9:00 - 17:00</strong></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
