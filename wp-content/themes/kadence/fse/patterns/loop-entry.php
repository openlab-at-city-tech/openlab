<?php
/**
 * A post in a list, matching the classic loop entry.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;

$read_more_attributes = [
	'content'   => __( 'Read More', 'kadence' ),
	'className' => 'post-more-link',
];
?>
<!-- wp:group {"tagName":"article","className":"entry content-bg loop-entry","layout":{"type":"default"}} -->
<article class="wp-block-group entry content-bg loop-entry"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","sizeSlug":"medium_large"} /-->

<!-- wp:group {"className":"entry-content-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group entry-content-wrap"><!-- wp:group {"tagName":"header","className":"entry-header","layout":{"type":"default"}} -->
<header class="wp-block-group entry-header"><!-- wp:post-terms {"term":"category","separator":" | ","className":"entry-taxonomies"} /-->

<!-- wp:post-title {"isLink":true,"rel":"bookmark","className":"entry-title"} /-->

<!-- wp:pattern {"slug":"kadence/entry-meta"} /--></header>
<!-- /wp:group -->

<!-- wp:post-excerpt {"className":"entry-summary"} /-->

<!-- wp:group {"tagName":"footer","className":"entry-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group entry-footer"><!-- wp:group {"className":"entry-actions","layout":{"type":"default"}} -->
<div class="wp-block-group entry-actions"><!-- wp:group {"className":"more-link-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group more-link-wrap"><!-- wp:read-more <?php echo serialize_block_attributes( $read_more_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped -- core's escaping for block comment attributes. ?> /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></footer>
<!-- /wp:group --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
