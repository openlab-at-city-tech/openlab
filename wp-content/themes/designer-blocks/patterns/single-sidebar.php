<?php
/**
 * Title: single-sidebar
 * Slug: designer-blocks/single-sidebar
 * Categories: hidden
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","tagName":"header","theme":"designer-blocks"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"top","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-top"><!-- wp:column {"verticalAlignment":"top","width":"800px","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-column is-vertically-aligned-top" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20);flex-basis:800px"><!-- wp:post-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"100"}},"fontSize":"large","fontFamily":"system-font"} /-->

<!-- wp:post-content {"style":{"typography":{"fontStyle":"normal","fontWeight":"100"}},"layout":{"type":"default"},"fontSize":"small","fontFamily":"system-font"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"top","width":"300px","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained","contentSize":"300px","justifyContent":"center"}} -->
<div class="wp-block-column is-vertically-aligned-top" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;flex-basis:300px"><!-- wp:search {"label":"Search","width":100,"widthUnit":"%","buttonText":"Search","backgroundColor":"contrast","fontSize":"small"} /-->


<!-- wp:latest-posts {"displayPostDate":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"100"}},"fontSize":"medium","fontFamily":"system-font"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:template-part {"slug":"comments","tagName":"section","theme":"designer-blocks"} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer","theme":"designer-blocks"} /-->