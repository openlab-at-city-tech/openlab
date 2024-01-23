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
	'title'    => _x( 'Center aligned features with icon', 'Block pattern title.', 'michelle' ),
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

$icon = Starter::get_image_url( 'icon' );

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"150px","bottom":"120px"}},"color":{"background":"#010101","text":"#fefefe"}},"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-group alignfull is-style-no-margin-vertical has-text-color has-background" style="background-color:#010101;color:#fefefe;padding-top:150px;padding-bottom:120px"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"align":"center","linkDestination":"none"} -->
<div class="wp-block-image"><figure class="aligncenter"><img src="<?php echo esc_url_raw( $icon ); ?>" alt="<?php Starter::the_text( 'alt' ); ?>"/></figure></div>
<!-- /wp:image -->

<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…"} -->
<p class="has-text-align-center"><?php Starter::the_text( 4, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
