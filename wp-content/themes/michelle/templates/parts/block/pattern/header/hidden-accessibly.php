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
	'title'         => _x( 'Heading: Accessibly hidden', 'Block pattern title.', 'michelle' ),
	'viewportWidth' => 400,
	'keywords'      => array(
		esc_html_x( 'title', 'keyword', 'michelle' ),
		esc_html_x( 'heading', 'keyword', 'michelle' ),
		esc_html_x( 'a11y', 'keyword', 'michelle' ),
		esc_html_x( 'barrierless', 'keyword', 'michelle' ),
		esc_html_x( 'screen reader', 'keyword', 'michelle' ),
	),
	'blockTypes' => array(
		'core/heading',
	),
) );

// Block pattern content:

?>

<!-- wp:heading {"className":"is-style-screen-reader-text"} -->
<h2 class="is-style-screen-reader-text"><?php esc_html_e( 'Visually hidden, but kept accessible for screen readers', 'michelle' ); ?></h2>
<!-- /wp:heading -->
