<?php

/**
 * Title: Testimonials Section
 * Slug: hello-agency/testimonials-section
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/testimonial_1.png',
    $hello_agency_url . 'assets/images/testimonial_2.png',
    $hello_agency_url . 'assets/images/testimonial_3.png',
    $hello_agency_url . 'assets/images/testimonial_4.png',
    $hello_agency_url . 'assets/images/reviews_star.png',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"bottom":"80px","right":"var:preset|spacing|50","left":"var:preset|spacing|50","top":"80px"},"margin":{"top":"0","bottom":"0"}}},"className":"hello-agency-animate","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="margin-top:0;margin-bottom:0;padding-top:80px;padding-right:var(--wp--preset--spacing--50);padding-bottom:80px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
    <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}},"className":"hello-agency-text-stroke"} -->
            <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Testimonials &', 'hello-agency') ?></h2>
            <!-- /wp:heading -->

            <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
            <h2 class="wp-block-heading" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Reviews', 'hello-agency') ?></h2>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->

        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"className":"is-style-fill hello-agency-buttons","fontSize":"normal"} -->
            <div class="wp-block-button has-custom-font-size is-style-fill hello-agency-buttons has-normal-font-size" style="font-style:normal;font-weight:500;text-transform:uppercase"><a class="wp-block-button__link has-heading-color-color has-text-color has-background wp-element-button" href="#" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php echo esc_html('View All', 'hello-agency') ?></a></div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->
    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"50px"}}}} -->
    <div class="wp-block-columns" style="margin-top:50px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"32px"}},"borderColor":"heading-color","layout":{"type":"constrained"}} -->
            <div class="wp-block-group has-border-color has-heading-color-border-color" style="border-width:1px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:image {"id":353,"sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full"><img src="<?php echo esc_url($hello_agency_images[4]) ?>" alt="" class="wp-image-353" /></figure>
                <!-- /wp:image -->

                <!-- wp:paragraph -->
                <p><?php echo esc_html('"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                <div class="wp-block-group"><!-- wp:image {"id":354,"height":48,"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
                    <figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-354" style="border-radius:50%;height:48px" height="48" /></figure>
                    <!-- /wp:image -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                    <div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"normal"} -->
                        <h5 class="wp-block-heading has-normal-font-size" style="font-style:normal;font-weight:500"><?php echo esc_html('Melinda M', 'hello-agency') ?></h5>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"fontSize":"small"} -->
                        <p class="has-small-font-size"><?php echo esc_html('HR Manager, Melinda Tech', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"32px"}},"borderColor":"heading-color","layout":{"type":"constrained"}} -->
            <div class="wp-block-group has-border-color has-heading-color-border-color" style="border-width:1px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:image {"id":353,"sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full"><img src="<?php echo esc_url($hello_agency_images[4]) ?>" alt="" class="wp-image-353" /></figure>
                <!-- /wp:image -->

                <!-- wp:paragraph -->
                <p><?php echo esc_html('"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                <div class="wp-block-group"><!-- wp:image {"id":363,"height":48,"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
                    <figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo esc_url($hello_agency_images[1]) ?>" alt="" class="wp-image-363" style="border-radius:50%;height:48px" height="48" /></figure>
                    <!-- /wp:image -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                    <div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"normal"} -->
                        <h5 class="wp-block-heading has-normal-font-size" style="font-style:normal;font-weight:500"><?php echo esc_html('Moxley Kole', 'hello-agency') ?></h5>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"fontSize":"small"} -->
                        <p class="has-small-font-size"><?php echo esc_html('HR Manager, Melinda Tech', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns -->
    <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"32px"}},"borderColor":"heading-color","layout":{"type":"constrained"}} -->
            <div class="wp-block-group has-border-color has-heading-color-border-color" style="border-width:1px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:image {"id":353,"sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full"><img src="<?php echo esc_url($hello_agency_images[4]) ?>" alt="" class="wp-image-353" /></figure>
                <!-- /wp:image -->

                <!-- wp:paragraph -->
                <p><?php echo esc_html('"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                <div class="wp-block-group"><!-- wp:image {"id":364,"height":48,"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
                    <figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo esc_url($hello_agency_images[2]) ?>" alt="" class="wp-image-364" style="border-radius:50%;height:48px" height="48" /></figure>
                    <!-- /wp:image -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                    <div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"normal"} -->
                        <h5 class="wp-block-heading has-normal-font-size" style="font-style:normal;font-weight:500"><?php echo esc_html('Alexa Mol', 'hello-agency') ?></h5>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"fontSize":"small"} -->
                        <p class="has-small-font-size"><?php echo esc_html('HR Manager, Melinda Tech', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"},"blockGap":"32px"}},"borderColor":"heading-color","layout":{"type":"constrained"}} -->
            <div class="wp-block-group has-border-color has-heading-color-border-color" style="border-width:1px;padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px"><!-- wp:image {"id":353,"sizeSlug":"full","linkDestination":"none"} -->
                <figure class="wp-block-image size-full"><img src="<?php echo esc_url($hello_agency_images[4]) ?>" alt="" class="wp-image-353" /></figure>
                <!-- /wp:image -->

                <!-- wp:paragraph -->
                <p><?php echo esc_html('"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim in eros elementum tristique. Duis cursus, mi quis viverra ornare."', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                <div class="wp-block-group"><!-- wp:image {"id":365,"height":48,"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
                    <figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo esc_url($hello_agency_images[3]) ?>" alt="" class="wp-image-365" style="border-radius:50%;height:48px" height="48" /></figure>
                    <!-- /wp:image -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                    <div class="wp-block-group"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"normal"} -->
                        <h5 class="wp-block-heading has-normal-font-size" style="font-style:normal;font-weight:500"><?php echo esc_html('Henry John', 'hello-agency') ?></h5>
                        <!-- /wp:heading -->

                        <!-- wp:paragraph {"fontSize":"small"} -->
                        <p class="has-small-font-size"><?php echo esc_html('HR Manager, Melinda Tech', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->