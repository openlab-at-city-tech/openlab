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
	'title'    => _x( 'Site footer with subscription form', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'site', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/group',
	),
) );

// Block pattern content:

?>

<!-- wp:group {"align":"full","style":{"color":{"background":"#ffdd44","text":"#010101"},"spacing":{"padding":{"top":"150px","bottom":"150px"}}},"className":"is-style-no-margin-vertical"} -->
<div class="wp-block-group alignfull is-style-no-margin-vertical has-text-color has-background" style="background-color:#ffdd44;color:#010101;padding-top:150px;padding-bottom:150px"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center"><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:html -->
<!--

This is only a dummy form placeholder, it does not work!
Use a plugin to add newsletter form.

-->
<form class="has-flex-display">
  <label for="field1" class="screen-reader-text">Your email</label>
  <input id="field1" type="email" placeholder="Your email">
  <button class="is-style-outline" title="This is just a demo placeholder form" type="submit">Submit →</button>
</form>
<!-- /wp:html -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
