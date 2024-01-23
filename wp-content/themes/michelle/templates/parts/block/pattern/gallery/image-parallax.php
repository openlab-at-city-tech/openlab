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
	'title'    => _x( 'Parallax background', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'images', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/cover',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:cover {"url":"<?php echo esc_url_raw( $image ); ?>","hasParallax":true,"dimRatio":0,"minHeight":62,"minHeightUnit":"vh","align":"full","className":"is-style-inner-shadow"} -->
<div class="wp-block-cover alignfull has-parallax is-style-inner-shadow" style="background-image:url(<?php echo esc_url_raw( $image ); ?>);min-height:62vh"><div class="wp-block-cover__inner-container"><!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div></div>
<!-- /wp:cover -->
