<?php
/**
 * Title: Blog Section
 * Slug: blocland-fse/blog-section
 * Categories: featured
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div id="blog-section" class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">

    <!-- wp:heading {"textAlign":"center","level":1} -->
    <h1 class="has-text-align-center">Blog</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","textColor":"foreground"} -->
    <p class="has-text-align-center has-foreground-color has-text-color">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    <!-- /wp:paragraph -->

    <!-- wp:group {"className":"wow fadeInUp","style":{"spacing":{"padding":{"top":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
    <div class="wp-block-group wow fadeInUp" style="padding-top:var(--wp--preset--spacing--40)">
        <!-- wp:query {"queryId":16,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"flex","columns":3}} -->
        <div class="wp-block-query">

            <!-- wp:post-template -->

            <!-- wp:post-featured-image {"isLink":true,"align":"wide"} /-->

            <!-- wp:post-title {"level":5,"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} /-->

            <!-- wp:post-date /-->

            <!-- /wp:post-template -->
        </div>
        <!-- /wp:query -->
    </div>
    <!-- /wp:group -->

</div>
<!-- /wp:group -->