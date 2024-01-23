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
	'title'    => _x( 'Colored, with image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/group',
		'core/heading',
		'core/media-text',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '3to4' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"150px"}},"color":{"background":"#ffdd44","text":"#010101"}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:150px"><!-- wp:media-text {"mediaPosition":"right","mediaId":999999,"mediaLink":"#0","mediaType":"image"} -->
<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"className":"is-style-no-margin-vertical"} -->
<p class="is-style-no-margin-vertical"><strong><?php Starter::the_text( 'xs' ); ?></strong></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/s' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:media-text --></div>
<!-- /wp:group -->
