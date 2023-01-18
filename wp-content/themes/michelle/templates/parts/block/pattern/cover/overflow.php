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
	'title'    => _x( 'Overflown heading', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/group',
		'core/heading',
		'core/cover',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '3to4' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}},"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#010101;color:#fefefe;padding-top:0px;padding-bottom:0px"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","id":999999,"dimRatio":40,"customOverlayColor":"#010101","minHeight":62,"minHeightUnit":"vh","contentPosition":"bottom center","align":"wide","className":"is-style-scale-image","style":{"spacing":{"padding":{"top":"150px","right":"0px","bottom":"150px","left":"0px"}}}} -->
<div class="wp-block-cover alignwide has-background-dim-40 has-background-dim has-custom-content-position is-position-bottom-center is-style-scale-image" style="padding-top:150px;padding-right:0px;padding-bottom:150px;padding-left:0px;background-color:#010101;min-height:62vh"><img class="wp-block-cover__image-background wp-image-999999" alt="" src="<?php echo esc_url_raw( $image ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"align":"wide","className":"is-style-text-shadow-dark"} -->
<h1 class="alignwide has-text-align-center is-style-text-shadow-dark"><strong><?php Starter::the_text( 'title/l' ); ?></strong></h1>
<!-- /wp:heading -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:buttons {"contentJustification":"center"} -->
<div class="wp-block-buttons is-content-justification-center"><!-- wp:button {"style":{"color":{"background":"#fefefe","text":"#010101"}},"fontSize":"normal"} -->
<div class="wp-block-button has-custom-font-size has-normal-font-size"><a class="wp-block-button__link has-text-color has-background" href="#0" style="background-color:#fefefe;color:#010101"><?php Starter::the_text( 'button' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->
