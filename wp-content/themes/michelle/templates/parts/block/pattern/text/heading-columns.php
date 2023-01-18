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
	'title'    => _x( 'Text with heading in columns', 'Block pattern title.', 'michelle' ),
	'keywords' => array(
		esc_html_x( 'columns', 'keyword', 'michelle' ),
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/columns',
		'core/heading',
		'core/paragraph',
	),
) );

// Block pattern content:

?>

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2><?php Starter::the_text( 'title/s' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 7, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
