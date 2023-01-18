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
	'title'    => _x( 'Features with background image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'services', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/cover',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"dimRatio":80,"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"120px"}}}} -->
<div class="wp-block-cover alignfull has-background-dim-80 has-background-dim" style="padding-top:150px;padding-bottom:120px"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"align":"wide","className":"is-style-zigzag"} -->
<hr class="wp-block-separator alignwide is-style-zigzag"/>
<!-- /wp:separator -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-uppercase","fontSize":"large"} -->
<h3 class="has-text-align-center is-style-uppercase has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-uppercase","fontSize":"large"} -->
<h3 class="has-text-align-center is-style-uppercase has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-uppercase","fontSize":"large"} -->
<h3 class="has-text-align-center is-style-uppercase has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"textAlign":"center","level":3,"className":"is-style-uppercase","fontSize":"large"} -->
<h3 class="has-text-align-center is-style-uppercase has-large-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
