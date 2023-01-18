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
	'title'    => _x( 'Pricing columns with with icons', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'price', 'keyword', 'michelle' ),
		esc_html_x( 'services', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/buttons',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( 'icon' );

?>

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-column has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","className":"is-style-no-margin-vertical"} -->
<h2 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><em><?php Starter::the_text( 's' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1"}},"fontSize":"huge"} -->
<p class="has-text-align-center has-huge-font-size" style="line-height:1"><?php Starter::the_text( 'price' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link" href="#0"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"color":{"background":"#ffdd44","text":"#010101"}}} -->
<div class="wp-block-column has-text-color has-background" style="background-color:#ffdd44;color:#010101"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","className":"is-style-no-margin-vertical"} -->
<h2 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><em><?php Starter::the_text( 's' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1"}},"fontSize":"huge"} -->
<p class="has-text-align-center has-huge-font-size" style="line-height:1"><?php Starter::the_text( 'price' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"style":{"color":{"background":"#010101","text":"#fefefe"}},"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-text-color has-background" href="#0" style="background-color:#010101;color:#fefefe"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"color":{"background":"#efefef","text":"#010101"}}} -->
<div class="wp-block-column has-text-color has-background" style="background-color:#efefef;color:#010101"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","className":"is-style-no-margin-vertical"} -->
<h2 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><em><?php Starter::the_text( 's' ); ?></em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1"}},"fontSize":"huge"} -->
<p class="has-text-align-center has-huge-font-size" style="line-height:1"><?php Starter::the_text( 'price' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link" href="#0"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
