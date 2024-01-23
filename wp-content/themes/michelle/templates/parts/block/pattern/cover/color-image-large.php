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
	'title'    => _x( 'Colored, with large image', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'intro', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		'h1',
	),
	'blockTypes' => array(
		'core/group',
		'core/heading',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( '16to9' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0px","bottom":"0px"}},"color":{"background":"#ffdd44","text":"#010101"}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:0px;padding-bottom:0px"><!-- wp:spacer {"className":"has-20vmax-min-height"} -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer has-20vmax-min-height"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1><?php Starter::the_text( 'title/m' ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-hidden-on-tablet"} -->
<div class="wp-block-column is-style-hidden-on-tablet"></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:image {"align":"wide","id":999999,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image alignwide size-large"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>" class="wp-image-999999"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->
