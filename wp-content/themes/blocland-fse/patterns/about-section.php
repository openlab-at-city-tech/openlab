<?php
/**
 * Title: About Section
 * Slug: blocland-fse/about-section
 * Categories: featured
 */
?>

<!-- wp:group {"gradient":"default-gradient","layout":{"type":"constrained"}} -->
<div id="about-section" class="wp-block-group has-default-gradient-gradient-background has-background">
    <!-- wp:columns {"className":"wow fadeInUp","style":{"spacing":{"blockGap":{"top":"0","left":"0"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} -->
    <div class="wp-block-columns wow fadeInUp" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
        <!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
        <div class="wp-block-column is-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);flex-basis:50%">

            <!-- wp:heading {"textAlign":"left","level":1,"textColor":"background","fontFamily":"raleway-regular"} -->
            <h1 class="has-text-align-left has-background-color has-text-color has-raleway-regular-font-family">Blocland FSE</h1>
            <!-- /wp:heading -->

            <!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group">

                <!-- wp:paragraph {"align":"left","textColor":"background","fontFamily":"raleway-regular"} -->
                <p class="has-text-align-left has-background-color has-text-color has-raleway-regular-font-family">Excepteur sint occaecat cupidatat non proident.</p>
                <!-- /wp:paragraph -->

                <!-- wp:paragraph {"align":"left","textColor":"background","fontFamily":"raleway-regular"} -->
                <p class="has-text-align-left has-background-color has-text-color has-raleway-regular-font-family"> esse cillum dolore eu fugiat nulla pariatur. </p>
                <!-- /wp:paragraph -->

                <!-- wp:paragraph {"textColor":"background","fontFamily":"raleway-regular"} -->
                <p class="has-background-color has-text-color has-raleway-regular-font-family">Duis aute irure dolor in reprehenderit in voluptate velit.</p>
                <!-- /wp:paragraph -->

            </div>
            <!-- /wp:group -->

            <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
            <div class="wp-block-buttons">

                <!-- wp:button {"style":{"border":{"radius":"0px"}},"fontSize":"medium"} -->
                <div class="wp-block-button has-custom-font-size has-medium-font-size">
                    <a class="wp-block-button__link wp-element-button" style="border-radius:0px"><?php echo esc_html_x("Read More","Header button text","blocland-fse") ?></a>
                </div>
                <!-- /wp:button -->

            </div>
            <!-- /wp:buttons -->

        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
        <div class="wp-block-column is-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);flex-basis:50%">

            <!-- wp:image {"align":"center","sizeSlug":"large","linkDestination":"none","style":{"border":{"top":{"color":"var:preset|color|light","width":"10px"},"left":{"color":"var:preset|color|light","width":"10px"},"radius":"27px"}}} -->
            <figure class="wp-block-image aligncenter size-large has-custom-border">
                <img src="<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/hero_image.jpg'); ?>" alt="" style="border-radius:27px;border-top-color:var(--wp--preset--color--light);border-top-width:10px;border-left-color:var(--wp--preset--color--light);border-left-width:10px"/>
            </figure>
            <!-- /wp:image -->

        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->
