<?php
 /**
  * Title: Search
  * Slug: fse-freelancer-portfolio/search
  */
?>
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%"><!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"className":"alignfull","layout":{"contentSize":null,"type":"constrained"}} -->
<div class="wp-block-query alignfull"><!-- wp:post-template {"className":"alignfull","layout":{"type":"default"}} -->
<!-- wp:group {"className":"archieve-post","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|80","right":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|primary","width":"6px"},"top":[],"right":[],"left":[]}},"backgroundColor":"tertiary-bg-color","layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group archieve-post has-tertiary-bg-color-background-color has-background" style="border-bottom-color:var(--wp--preset--color--primary);border-bottom-width:6px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--80)"><!-- wp:group {"className":"blog-date-box","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group blog-date-box" style="margin-top:0;margin-bottom:0"><!-- wp:post-date {"textAlign":"center","format":" j","isLink":true,"style":{"spacing":{"padding":{"right":"0","bottom":"0","left":"0","top":"var:preset|spacing|60"}},"typography":{"fontSize":"1.88rem","fontStyle":"normal","fontWeight":"600"}},"backgroundColor":"primary"} /-->

<!-- wp:post-date {"textAlign":"center","format":"M","className":"blog-date-mon","style":{"typography":{"fontSize":"0.88rem","fontStyle":"normal","fontWeight":"600","letterSpacing":"7px"},"spacing":{"padding":{"right":"0","left":"0","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"wp-block-post-meta alignwide","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","allowOrientation":false}} -->
<div class="wp-block-group wp-block-post-meta alignwide" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:post-author {"showAvatar":false,"isLink":true} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"wp-block-post-container"} -->
<div class="wp-block-group alignwide wp-block-post-container"><!-- wp:post-title {"isLink":true} /-->

<!-- wp:post-featured-image {"isLink":true} /-->

<!-- wp:post-excerpt {"moreText":"Continue Reading"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous {"fontSize":"small"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"fontSize":"small"} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center","placeholder":"Add text or blocks that will display when a query returns no results.","style":{"elements":{"link":{"color":{"text":"var:preset|color|heading"}}}},"textColor":"heading"} -->
<p class="has-text-align-center has-heading-color has-text-color has-link-color"><?php esc_html_e( 'Sorry Nothing Found. Try searching..!!', 'fse-freelancer-portfolio' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:spacer {"height":"45px"} -->
<div style="height:45px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:template-part {"slug":"sidebar","theme":"fse-freelancer-portfolio","tagName":"div"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->