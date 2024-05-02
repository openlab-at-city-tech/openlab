<?php
/**
 * Title: Latest Posts
 * Slug: aeonium/latest-posts
 * Categories: aeonium
 * Viewport Width: 1200
 */

?>
<!-- wp:group {"align":"wide"} -->
<div class="wp-block-group alignwide"><!-- wp:separator {"gradient":"accent-bands-light","className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity has-accent-bands-light-gradient-background has-background is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"style":{"typography":{"textTransform":"uppercase"}}} -->
<h2 style="text-transform:uppercase">Latest From Our Blog</h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:social-links {"iconColor":"accent-2","iconColorValue":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dcolor\u002d\u002daccent-2)","className":"is-style-logos-only"} -->
<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"<?php echo esc_url( get_feed_link() ); ?>","service":"feed"} /--></ul>
<!-- /wp:social-links -->

<!-- wp:paragraph -->
<p>Subscribe today and never miss a thing.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":1,"query":{"offset":0,"perPage":4,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":4},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
<!-- wp:group -->
<div class="wp-block-group"><!-- wp:post-featured-image {"className":"is-style-hover-zoom"} /-->

<!-- wp:post-title {"textAlign":"center","isLink":true,"className":"is-style-links-underline-on-hover","fontSize":"medium"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
