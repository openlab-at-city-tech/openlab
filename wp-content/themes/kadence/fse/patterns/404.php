<?php
/**
 * The 404 template's content, matching the classic 404 template part.
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
<section class="wp-block-group error"><!-- wp:group {"className":"page-content entry content-bg","layout":{"type":"default"}} -->
<div class="wp-block-group page-content entry content-bg"><!-- wp:group {"className":"entry-content-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group entry-content-wrap"><!-- wp:group {"tagName":"header","className":"page-header","layout":{"type":"default"}} -->
<header class="wp-block-group page-header"><!-- wp:heading {"level":1,"className":"page-title"} -->
<h1 class="wp-block-heading page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kadence' ); ?></h1>
<!-- /wp:heading --></header>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'kadence' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search <?php echo serialize_block_attributes( $search_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
