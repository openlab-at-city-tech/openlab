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
	'title'    => _x( 'Features with drop shadow', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'services', 'keyword', 'michelle' ),
		esc_html_x( 'columns', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/image',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( 'icon' );

?>

<!-- wp:columns {"align":"full","className":"is-style-no-gaps has-hidden-overflow"} -->
<div class="wp-block-columns alignfull is-style-no-gaps has-hidden-overflow"><!-- wp:column {"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"background":"#ffdd44","text":"#010101"}},"className":"is-style-drop-shadow"} -->
<div class="wp-block-column is-style-drop-shadow has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em"><!-- wp:image {"linkDestination":"none"} -->
<figure class="wp-block-image"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"className":"is-style-no-margin-vertical"} -->
<h2 class="is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"background":"#ffdd44","text":"#010101"}},"className":"is-style-drop-shadow"} -->
<div class="wp-block-column is-style-drop-shadow has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em"><!-- wp:image {"linkDestination":"none"} -->
<figure class="wp-block-image"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"className":"is-style-no-margin-vertical"} -->
<h2 class="is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2em","right":"2em","bottom":"2em","left":"2em"}},"color":{"background":"#ffdd44","text":"#010101"}},"className":"is-style-drop-shadow"} -->
<div class="wp-block-column is-style-drop-shadow has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:2em;padding-right:2em;padding-bottom:2em;padding-left:2em"><!-- wp:image {"linkDestination":"none"} -->
<figure class="wp-block-image"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":50} -->
<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"className":"is-style-no-margin-vertical"} -->
<h2 class="is-style-no-margin-vertical"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
