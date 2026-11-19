<?php
/**
 * The message shown when a search finds nothing, matching the classic error template part.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$search_attributes = [
	'label'          => __( 'Search', 'kadence' ),
	'showLabel'      => false,
	// The placeholder Kadence already uses, so its language packs translate it. The block
	// escapes the value again, so an entity in a translation is decoded first.
	'placeholder'    => html_entity_decode( __( 'Search ...', 'kadence' ), ENT_QUOTES, 'UTF-8' ),
	'buttonText'     => __( 'Search', 'kadence' ),
	'buttonUseIcon'  => true,
	'buttonPosition' => 'button-inside',
	'className'      => 'search-form',
];
?>
<!-- wp:group {"tagName":"section","className":"error","layout":{"type":"default"}} -->
<section class="wp-block-group error"><!-- wp:group {"className":"page-content","layout":{"type":"default"}} -->
<div class="wp-block-group page-content"><!-- wp:paragraph -->
<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'kadence' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search <?php echo serialize_block_attributes( $search_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
