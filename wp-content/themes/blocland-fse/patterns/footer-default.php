<?php
/**
 * Title: Default Footer With Columns
 * Slug: blocland-fse/footer-default
 * Categories: footer
 * Block Types: 'core/template-part/footer'
 */
?>
<!-- wp:group {"tagName":"footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30"}}},"backgroundColor":"foreground","layout":{"type":"constrained","contentSize":"1140px"}} -->
<footer id="footer-default" class="wp-block-group has-foreground-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"className":"border-top","layout":{"type":"default"}} -->
    <div id="upper-footer" class="wp-block-group border-top" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
        <div class="wp-block-columns"><!-- wp:column {"width":"30%","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} -->
            <div class="wp-block-column" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;flex-basis:30%"><!-- wp:site-title {"level":6,"textAlign":"left","style":{"spacing":{"margin":{"right":"0px","bottom":"0px","left":"0px"}},"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}}}} /-->

                <!-- wp:paragraph {"align":"left","textColor":"background"} -->
                <p class="has-text-align-left has-background-color has-text-color">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                <!-- /wp:paragraph --></div>
            <!-- /wp:column -->

            <!-- wp:column {"width":"30%"} -->
            <div class="wp-block-column" style="flex-basis:30%"><!-- wp:heading {"level":6,"textColor":"background"} -->
                <h6 class="has-background-color has-text-color"><?php echo esc_html_x("Contact Us","Footer text 1","blocland-fse") ?></h6>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"textColor":"background"} -->
                <p class="has-background-color has-text-color">​605 Rippin Mountains,<br>Kuvalisberg 55696</p>
                <!-- /wp:paragraph -->

                <!-- wp:paragraph {"textColor":"background"} -->
                <p class="has-background-color has-text-color">Tel:&nbsp;<strong>(111) 360 360 360</strong></p>
                <!-- /wp:paragraph --></div>
            <!-- /wp:column -->

            <!-- wp:column {"width":"15%"} -->
            <div class="wp-block-column" style="flex-basis:15%"><!-- wp:heading {"level":6,"textColor":"background"} -->
                <h6 class="has-background-color has-text-color"><?php echo esc_html_x("Menu","Footer text 2","blocland-fse") ?></h6>
                <!-- /wp:heading -->
                <!-- wp:navigation {"textColor":"background","overlayMenu":"never","overlayTextColor":"foreground","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"},"style":{"typography":{"lineHeight":"2","fontSize":"14px","fontStyle":"normal","fontWeight":"700"},"spacing":{"blockGap":"0"}}} /-->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"width":"15%"} -->
            <div class="wp-block-column" style="flex-basis:15%"><!-- wp:heading {"level":6,"textColor":"background"} -->
                <h6 class="has-background-color has-text-color"><?php echo esc_html_x("Social","Footer text 3","blocland-fse") ?></h6>
                <!-- /wp:heading -->

                <!-- wp:social-links {"iconColor":"foreground","iconColorValue":"#000000","iconBackgroundColor":"light","iconBackgroundColorValue":"#E9E5D6"} -->
                <ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

                    <!-- wp:social-link {"url":"#","service":"instagram"} /-->

                    <!-- wp:social-link {"url":"#","service":"twitter"} /--></ul>
                <!-- /wp:social-links --></div>
            <!-- /wp:column --></div>
        <!-- /wp:columns --></div>
    <!-- /wp:group -->

    <!-- wp:group {"align":"wide","style":{"border":{"top":{"color":"var:preset|color|secondary","width":"1px"}}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignwide" id="lower-footer" style="border-top-color:var(--wp--preset--color--secondary);border-top-width:1px"><!-- wp:paragraph {"align":"center","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}}},"textColor":"background","className":"copyright","fontSize":"small"} -->
        <p class="has-text-align-center copyright has-background-color has-text-color has-link-color has-small-font-size" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">Copyright © 2022 <a rel="noreferrer noopener" href="https://xideathemes.com" target="_blank">Xidea Themes</a></p>
        <!-- /wp:paragraph --></div>
    <!-- /wp:group --></footer>
<!-- /wp:group -->