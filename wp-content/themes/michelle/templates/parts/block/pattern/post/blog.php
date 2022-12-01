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
	'title'    => _x( 'Blog', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'articles', 'keyword', 'michelle' ),
		esc_html_x( 'posts', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/latest-posts',
	),
) );

// Block pattern content:

?>

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"><?php Starter::the_text( 3, '.' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:latest-posts {"postsToShow":3,"displayPostContent":true,"excerptLength":20,"displayPostDate":true,"postLayout":"grid","displayFeaturedImage":true,"align":"wide"} /-->
