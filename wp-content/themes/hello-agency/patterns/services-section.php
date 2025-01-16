<?php

/**
 * Title: Services Section
 * Slug: hello-agency/services-section
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/service_icon_1.png',
    $hello_agency_url . 'assets/images/service_icon_2.png',
    $hello_agency_url . 'assets/images/service_icon_3.png',
);
?>
<!-- wp:group {"metadata":{"categories":["hello-agency"],"patternName":"hello-agency/services-section","name":"Services Section"},"className":"hello-agency-animate","style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-top:120px;padding-right:var(--wp--preset--spacing--50);padding-bottom:120px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
    <div class="wp-block-group"><!-- wp:heading {"className":"hello-agency-text-stroke","style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
        <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e('What we', 'hello-agency') ?></h2>
        <!-- /wp:heading -->

        <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
        <h2 class="wp-block-heading" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php esc_html_e('Offers', 'hello-agency') ?></h2>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"64px"},"blockGap":{"left":"var:preset|spacing|60"}}}} -->
    <div class="wp-block-columns" style="margin-top:64px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"hello-agency-service-box","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-service-box"><!-- wp:image {"id":175,"width":"100px","height":"100px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-white"}}} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-175" style="object-fit:cover;width:100px;height:100px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","fontStyle":"normal","fontWeight":"600","textTransform":"uppercase"},"spacing":{"margin":{"top":"32px"}}}} -->
                <h3 class="wp-block-heading" style="margin-top:32px;font-size:24px;font-style:normal;font-weight:600;text-transform:uppercase"><?php esc_html_e('Branding', 'hello-agency') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
                <p style="text-transform:uppercase"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"32px"}}}} -->
                <div class="wp-block-buttons" style="margin-top:32px"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size has-normal-font-size" style="font-style:normal;font-weight:500;text-transform:uppercase"><a class="wp-block-button__link has-heading-color-color has-text-color has-background wp-element-button" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php esc_html_e('Learn More', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"hello-agency-service-box","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-service-box"><!-- wp:image {"id":175,"width":"100px","height":"100px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-white"}}} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[1]) ?>" alt="" class="wp-image-175" style="object-fit:cover;width:100px;height:100px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","fontStyle":"normal","fontWeight":"600","textTransform":"uppercase"},"spacing":{"margin":{"top":"32px"}}}} -->
                <h3 class="wp-block-heading" style="margin-top:32px;font-size:24px;font-style:normal;font-weight:600;text-transform:uppercase"><?php esc_html_e('Startup Consulting', 'hello-agency') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
                <p style="text-transform:uppercase"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"32px"}}}} -->
                <div class="wp-block-buttons" style="margin-top:32px"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size has-normal-font-size" style="font-style:normal;font-weight:500;text-transform:uppercase"><a class="wp-block-button__link has-heading-color-color has-text-color has-background wp-element-button" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php esc_html_e('Learn More', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"hello-agency-service-box","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-service-box"><!-- wp:image {"id":175,"width":"100px","height":"100px","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"color":{"duotone":"var:preset|duotone|primary-white"}}} -->
                <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[2]) ?>" alt="" class="wp-image-175" style="object-fit:cover;width:100px;height:100px" /></figure>
                <!-- /wp:image -->

                <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"24px","fontStyle":"normal","fontWeight":"600","textTransform":"uppercase"},"spacing":{"margin":{"top":"32px"}}}} -->
                <h3 class="wp-block-heading" style="margin-top:32px;font-size:24px;font-style:normal;font-weight:600;text-transform:uppercase"><?php esc_html_e('Mobile App', 'hello-agency') ?></h3>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase"}}} -->
                <p style="text-transform:uppercase"><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"32px"}}}} -->
                <div class="wp-block-buttons" style="margin-top:32px"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size has-normal-font-size" style="font-style:normal;font-weight:500;text-transform:uppercase"><a class="wp-block-button__link has-heading-color-color has-text-color has-background wp-element-button" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php esc_html_e('Learn More', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->