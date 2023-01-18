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
	'title'    => _x( 'Site footer with logo, links, description and social links', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'site', 'keyword', 'michelle' ),
		esc_html_x( 'links', 'keyword', 'michelle' ),
		esc_html_x( 'social links', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/group',
		'core/social-links',
	),
) );

// Block pattern content:

$image = Starter::get_image_url( 'icon' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"50px"}},"color":{"background":"#ffdd44","text":"#010101"}},"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-group alignfull is-style-no-margin-vertical has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:50px"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%"><!-- wp:image {"linkDestination":"none"} -->
<figure class="wp-block-image"><img src="<?php echo esc_url_raw( $image ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"5%"} -->
<div class="wp-block-column" style="flex-basis:5%"></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li><?php Starter::the_text( 'xs' ); ?></li><li><?php Starter::the_text( 's' ); ?></li><li><?php Starter::the_text( 'xs' ); ?></li><li><?php Starter::the_text( 's' ); ?></li><li><?php Starter::the_text( 's' ); ?></li></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"color":{"background":"#010101","text":"#fefefe"}}} -->
<div class="wp-block-group alignfull has-text-color has-background" style="background-color:#010101;color:#fefefe"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"33.33%","className":"is-style-no-margin-vertical"} -->
<div class="wp-block-column is-vertically-aligned-center is-style-no-margin-vertical" style="flex-basis:33.33%"><!-- wp:heading {"className":"is-style-uppercase","fontSize":"normal"} -->
<h2 class="is-style-uppercase has-normal-font-size"><strong><?php Starter::the_text( 'title/s' ); ?></strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","className":"is-style-no-margin-vertical"} -->
<div class="wp-block-column is-vertically-aligned-center is-style-no-margin-vertical"><!-- wp:social-links {"customIconColor":"#fefefe","iconColorValue":"#fefefe","size":"has-large-icon-size","align":"right","className":"has-no-margin-bottom is-style-logos-only"} -->
<ul class="wp-block-social-links alignright has-large-icon-size has-icon-color has-no-margin-bottom is-style-logos-only"><!-- wp:social-link {"url":"#0","service":"instagram"} /-->

<!-- wp:social-link {"url":"#0","service":"youtube"} /-->

<!-- wp:social-link {"url":"#0","service":"facebook"} /-->

<!-- wp:social-link {"url":"#0","service":"twitter"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
