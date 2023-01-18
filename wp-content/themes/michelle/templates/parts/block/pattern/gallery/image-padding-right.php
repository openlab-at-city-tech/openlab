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
	'title'    => _x( 'Fullwidth image with right padding', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'gallery', 'keyword', 'michelle' ),
		esc_html_x( 'images', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '21to9' );

?>

<!-- wp:image {"align":"full","id":999999,"sizeSlug":"large","linkDestination":"none","className":"is-style-padding-right"} -->
<figure class="wp-block-image alignfull size-large is-style-padding-right"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999"/></figure>
<!-- /wp:image -->
