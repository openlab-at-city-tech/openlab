<?php
/**
 * Title: Header With Background Color
 * Slug: blocland-fse/header-with-bg-color
 * Categories: header
 * Block Types: 'core/template-part/header'
 */
?>

<!-- wp:group {"tagName":"header","style":{"spacing":{"padding":{"right":"10px","left":"10px","top":"20px","bottom":"20px"}}},"backgroundColor":"primary","textColor":"background","layout":{"type":"constrained","contentSize":"1200px"}} -->
<header id="header" class="wp-block-group has-background-color has-primary-background-color has-text-color has-background" style="padding-top:20px;padding-right:10px;padding-bottom:20px;padding-left:10px">

    <!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group alignwide">

        <!-- wp:site-title {"textAlign":"left","style":{"typography":{"textDecoration":"none"},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"fontSize":"large-2"} /-->

        <!-- wp:navigation {"layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"medium"} /-->

    </div>
    <!-- /wp:group -->
</header>
<!-- /wp:group -->

