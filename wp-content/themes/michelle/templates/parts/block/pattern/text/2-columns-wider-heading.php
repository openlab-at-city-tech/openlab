<?php
/**
 * Block pattern setup file.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.10
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Add block pattern setup args.
Block_Patterns::add_pattern_args( __FILE__, array(
	'title'      => _x( 'Text in 2 columns with wider heading', 'Block pattern title.', 'michelle' ),
	'blockTypes' => array(
		'core/columns',
		'core/heading',
		'core/paragraph',
	),
) );

// Block pattern content:

?>

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"75%","className":"is-style-alignleft"} -->
<div class="wp-block-column is-style-alignleft" style="flex-basis:75%"><!-- wp:heading {"fontSize":"extra-large"} -->
<h2 class="has-extra-large-font-size"><?php Starter::the_text( 'title/l' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php Starter::the_text( 5, '.' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
