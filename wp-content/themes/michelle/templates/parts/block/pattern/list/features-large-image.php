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
	'title'    => _x( 'Features with large image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'services', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/cover',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );
$icon  = Starter::get_image_url( 'icon' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"dimRatio":0,"minHeight":100,"minHeightUnit":"vh","contentPosition":"bottom center","align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"0px"}}}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-center" style="padding-top:150px;padding-bottom:0px;min-height:100vh"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:columns {"align":"wide","style":{"color":{"background":"#ffdd44","text":"#010101"}}} -->
<div class="wp-block-columns alignwide has-text-color has-background" style="background-color:#ffdd44;color:#010101"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-no-margin-vertical"} -->
<h3 class="has-text-align-center is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
