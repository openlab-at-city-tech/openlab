<?php

/**
 * Title: Banner Section
 * Slug: hello-agency/banner-section
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/banner_new.jpg',
    $hello_agency_url . 'assets/images/team_more.png',
);
?>
<!-- wp:group {"className":"hello-agency-animate","style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"100px","bottom":"60px"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-top:100px;padding-right:var(--wp--preset--spacing--50);padding-bottom:60px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"className":"hello-agency-text-stroke","style":{"typography":{"fontSize":"100px","textTransform":"uppercase","letterSpacing":"3.5px","lineHeight":"1"}}} -->
    <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:100px;letter-spacing:3.5px;line-height:1;text-transform:uppercase"><?php esc_html_e('Transform Your', 'hello-agency') ?> </h2>
    <!-- /wp:heading -->

    <!-- wp:heading {"style":{"typography":{"fontSize":"100px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"3.5px","lineHeight":"1"},"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"right":"0","left":"0"}}},"textColor":"primary"} -->
    <h2 class="wp-block-heading has-primary-color has-text-color" style="margin-right:0;margin-left:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-size:100px;font-style:normal;font-weight:700;letter-spacing:3.5px;line-height:1;text-transform:uppercase"><?php esc_html_e('Digital Landscape', 'hello-agency') ?></h2>
    <!-- /wp:heading -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"40px"}}}} -->
    <div class="wp-block-columns" style="margin-top:40px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500","lineHeight":"1.7"}},"textColor":"heading-color","fontSize":"normal"} -->
            <p class="has-heading-color-color has-text-color has-normal-font-size" style="font-style:normal;font-weight:500;line-height:1.7;text-transform:uppercase"><?php esc_html_e('Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.', 'hello-agency') ?></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"></div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"hello-agency-animate","style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-right:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[0]) ?>","id":694,"dimRatio":50,"overlayColor":"background","isUserOverlayColor":true,"minHeight":580,"contentPosition":"bottom left","className":"is-style-hello-agency-cover-round-style","style":{"spacing":{"padding":{"top":"40px","bottom":"40px","left":"40px","right":"40px"}},"border":{"radius":"0px"}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
    <div class="wp-block-cover has-custom-content-position is-position-bottom-left is-style-hello-agency-cover-round-style" style="border-radius:0px;padding-top:40px;padding-right:40px;padding-bottom:40px;padding-left:40px;min-height:580px"><span aria-hidden="true" class="wp-block-cover__background has-background-background-color has-background-dim"></span><img class="wp-block-cover__image-background wp-image-694" alt="" src="<?php echo esc_url($hello_agency_images[0]) ?>" data-object-fit="cover" />
        <div class="wp-block-cover__inner-container"><!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column {"width":"60%","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
                <div class="wp-block-column" style="flex-basis:60%"><!-- wp:paragraph {"align":"left","placeholder":"Write title…","textColor":"foreground","fontSize":"big"} -->
                    <p class="has-text-align-left has-foreground-color has-text-color has-big-font-size"><?php esc_html_e('/ Who We Are', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:heading {"style":{"typography":{"fontSize":"56px","fontStyle":"normal","fontWeight":"700","textTransform":"uppercase"}}} -->
                    <h2 class="wp-block-heading" style="font-size:56px;font-style:normal;font-weight:700;text-transform:uppercase"><?php esc_html_e('Masters of Digital Craftsmanship', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column -->
                <div class="wp-block-column"></div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
        </div>
    </div>
    <!-- /wp:cover -->

    <!-- wp:group {"style":{"spacing":{"padding":{"right":"60px","left":"60px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
    <div class="wp-block-group" style="padding-right:60px;padding-left:60px"><!-- wp:image {"id":201,"width":"undefinedpx","height":"200px","sizeSlug":"full","linkDestination":"custom","className":"hello-agency-rotator team-more-rotator","style":{"color":{"duotone":"var:preset|duotone|white-primary"}}} -->
        <figure class="wp-block-image size-full is-resized hello-agency-rotator team-more-rotator"><a href="#"><img src="<?php echo esc_url($hello_agency_images[1]) ?>" alt="" class="wp-image-201" style="width:undefinedpx;height:200px" /></a></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->