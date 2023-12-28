<?php
/**
 * Title: Default Header
 * Slug: blocland-fse/header-default
 * Categories: header
 * Block Types: 'core/template-part/header'
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"15px","bottom":"15px","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div id="header" class="wp-block-group" style="padding-top:15px;padding-right:var(--wp--preset--spacing--30);padding-bottom:15px;padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group"><!-- wp:site-title {"textAlign":"left","style":{"typography":{"textDecoration":"none"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"fontSize":"large-2"} /-->

        <!-- wp:navigation {"layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"medium"} /-->

        <!-- wp:social-links {"iconColor":"secondary","iconColorValue":"#362706","iconBackgroundColor":"light","iconBackgroundColorValue":"#E9E5D6","size":"has-normal-icon-size","className":"blocland-hide-small-devices","layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <ul class="wp-block-social-links has-normal-icon-size has-icon-color has-icon-background-color blocland-hide-small-devices"><!-- wp:social-link {"url":"javascript:void(0)","service":"facebook"} /-->

            <!-- wp:social-link {"url":"javascript:void(0)","service":"instagram"} /-->

            <!-- wp:social-link {"url":"javascript:void(0)","service":"twitter"} /--></ul>
        <!-- /wp:social-links --></div>
    <!-- /wp:group --></div>
<!-- /wp:group -->
