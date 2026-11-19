<?php
/**
 * A post's meta line, matching the classic entry meta.
 *
 * The Date block is written with the binding the editor adds to it since WordPress 6.9,
 * so opening a template that uses this pattern doesn't show unsaved changes.
 *
 * @package kadence
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"entry-meta entry-meta-divider-dot","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group entry-meta entry-meta-divider-dot"><!-- wp:group {"className":"posted-by","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group posted-by"><!-- wp:paragraph {"className":"meta-label"} -->
<p class="meta-label"><?php esc_html_e( 'By', 'kadence' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-author-name {"isLink":true,"className":"author vcard"} /--></div>
<!-- /wp:group -->

<!-- wp:post-date {"className":"posted-on","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}}} /--></div>
<!-- /wp:group -->
