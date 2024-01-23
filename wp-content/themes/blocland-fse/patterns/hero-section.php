<?php
/**
 * Title: Hero Section
 * Slug: blocland-fse/hero-section
 * Categories: blocland
 */
?>

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"className":"is-style-default","layout":{"type":"default"}} -->
<div id="hero-section" class="wp-block-group is-style-default">
    <!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
    <div class="wp-block-group alignfull">
        <!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"0","left":"0"},"padding":{"top":"var:preset|spacing|50","right":"0","bottom":"var:preset|spacing|50","left":"0"},"margin":{"top":"0","bottom":"0"}}}} -->
        <div class="wp-block-columns" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:var(--wp--preset--spacing--50);padding-left:0">
            <!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"right":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-column is-vertically-aligned-center" style="padding-right:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

                <!-- wp:heading {"level":1,"textColor":"primary"} -->
                <h1 class="wp-block-heading has-primary-color has-text-color"><?php echo esc_html_x("Ready to transform your business?","Hero headline","blocland-fse") ?></h1>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"textColor":"secondary"} -->
                <p class="has-secondary-color has-text-color">​Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button -->
                    <div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x("Get Started","Hero button text","blocland-fse") ?></a>
                    </div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:column -->

            <!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
            <div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                <!-- wp:image {"align":"center","id":2878,"width":"510px","height":"auto","sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image aligncenter size-full is-resized">
                    <img src="<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/blocland_hero_img_2.webp'); ?>" alt="" class="wp-image-2878" style="width:510px;height:auto"/>
                </figure>
                <!-- /wp:image -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
