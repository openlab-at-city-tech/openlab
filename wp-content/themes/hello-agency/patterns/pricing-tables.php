<?php

/**
 * Title: Pricing Tables
 * Slug: hello-agency/pricing-tables
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/icon_bolt.png',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"120px","bottom":"120px","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"className":"hello-agency-animate","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div id="hello-agency-animate" class="wp-block-group hello-agency-animate" style="padding-top:120px;padding-right:var(--wp--preset--spacing--50);padding-bottom:120px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
    <div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}},"className":"hello-agency-text-stroke"} -->
        <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('services', 'hello-agency') ?></h2>
        <!-- /wp:heading -->

        <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
        <h2 class="wp-block-heading" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"> <?php echo esc_html('Pricing', 'hello-agency') ?></h2>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"62px"}}}} -->
    <div class="wp-block-columns" style="margin-top:62px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"40px","left":"40px","right":"40px"}},"border":{"radius":"10px"}},"backgroundColor":"background-alt","className":"hello-agency-pricing-table","layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-pricing-table has-background-alt-background-color has-background" style="border-radius:10px;padding-top:60px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:group {"style":{"spacing":{"blockGap":"15px","padding":{"bottom":"var:preset|spacing|20"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('Branding', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.6","fontStyle":"normal","fontWeight":"300"}}} -->
                    <p style="font-style:normal;font-weight:300;line-height:1.6"><?php echo esc_html('Bring your brand to life with our logo, style guide ad more.', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('$124.85', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->

                <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"textColor":"heading-color","fontSize":"normal"} -->
                <p class="has-heading-color-color has-text-color has-normal-font-size" style="font-style:normal;font-weight:400"><?php echo esc_html('Branding includes:', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Branding books', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Visual Identity', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Content Strategy', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Social Media', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->

                <!-- wp:buttons {"align":"full","className":"pricing-table-buttons","layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
                <div class="wp-block-buttons alignfull pricing-table-buttons" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:button {"backgroundColor":"heading-color","textColor":"neutra-color","style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"border":{"radius":"5px","width":"1px"},"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"borderColor":"heading-color","className":"is-style-fill","fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size is-style-fill has-normal-font-size" style="font-style:normal;font-weight:700;text-transform:uppercase"><a class="wp-block-button__link has-neutra-color-color has-heading-color-background-color has-text-color has-background has-border-color has-heading-color-border-color wp-element-button" style="border-width:1px;border-radius:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><?php echo esc_html('Get Started', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"40px","left":"40px","right":"40px"}},"border":{"radius":"10px"}},"backgroundColor":"background-alt","className":"hello-agency-pricing-table","layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-pricing-table has-background-alt-background-color has-background" style="border-radius:10px;padding-top:60px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:heading {"level":5,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"uppercase"}},"className":"pricing-table-badge","fontSize":"small"} -->
                <h5 class="wp-block-heading pricing-table-badge has-small-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('Popular', 'hello-agency') ?></h5>
                <!-- /wp:heading -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"15px","padding":{"bottom":"var:preset|spacing|20"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('Website', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.6","fontStyle":"normal","fontWeight":"300"}}} -->
                    <p style="font-style:normal;font-weight:300;line-height:1.6"><?php echo esc_html('Bring your brand to life with our logo, style guide ad more.', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('$249.99', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->

                <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"textColor":"heading-color","fontSize":"normal"} -->
                <p class="has-heading-color-color has-text-color has-normal-font-size" style="font-style:normal;font-weight:400"><?php echo esc_html('Everything in Branding, Plus:', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Branding books', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Visual Identity', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Content Strategy', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Social Media', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->

                <!-- wp:buttons {"align":"full","className":"pricing-table-buttons","layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
                <div class="wp-block-buttons alignfull pricing-table-buttons" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:button {"backgroundColor":"heading-color","textColor":"neutra-color","style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"border":{"radius":"5px","width":"1px"},"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"borderColor":"heading-color","className":"is-style-fill","fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size is-style-fill has-normal-font-size" style="font-style:normal;font-weight:700;text-transform:uppercase"><a class="wp-block-button__link has-neutra-color-color has-heading-color-background-color has-text-color has-background has-border-color has-heading-color-border-color wp-element-button" style="border-width:1px;border-radius:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><?php echo esc_html('Get Started', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","bottom":"40px","left":"40px","right":"40px"}},"border":{"radius":"10px"}},"backgroundColor":"background-alt","className":"hello-agency-pricing-table","layout":{"type":"constrained"}} -->
            <div class="wp-block-group hello-agency-pricing-table has-background-alt-background-color has-background" style="border-radius:10px;padding-top:60px;padding-right:40px;padding-bottom:40px;padding-left:40px"><!-- wp:group {"style":{"spacing":{"blockGap":"15px","padding":{"bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group" style="padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('ENTERPRISE', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.6","fontStyle":"normal","fontWeight":"300"}}} -->
                    <p style="font-style:normal;font-weight:300;line-height:1.6"><?php echo esc_html('Bring your brand to life with our logo, style guide ad more.', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"}},"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('Custom', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->

                <!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"textColor":"heading-color","fontSize":"normal"} -->
                <p class="has-heading-color-color has-text-color has-normal-font-size" style="font-style:normal;font-weight:400"><?php echo esc_html('Everything in Website, Plus:', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Branding books', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Visual Identity', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Content Strategy', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                    <div class="wp-block-group"><!-- wp:image {"id":323,"height":24,"sizeSlug":"full","linkDestination":"none"} -->
                        <figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-323" style="height:24px" height="24" /></figure>
                        <!-- /wp:image -->

                        <!-- wp:paragraph {"textColor":"heading-color"} -->
                        <p class="has-heading-color-color has-text-color"><?php echo esc_html('Social Media', 'hello-agency') ?></p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                </div>
                <!-- /wp:group -->

                <!-- wp:buttons {"align":"full","className":"pricing-table-buttons","layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}}} -->
                <div class="wp-block-buttons alignfull pricing-table-buttons" style="margin-top:var(--wp--preset--spacing--60)"><!-- wp:button {"backgroundColor":"heading-color","textColor":"neutra-color","style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"border":{"radius":"5px","width":"1px"},"spacing":{"padding":{"left":"var:preset|spacing|40","right":"var:preset|spacing|40","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"borderColor":"heading-color","className":"is-style-fill","fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size is-style-fill has-normal-font-size" style="font-style:normal;font-weight:700;text-transform:uppercase"><a class="wp-block-button__link has-neutra-color-color has-heading-color-background-color has-text-color has-background has-border-color has-heading-color-border-color wp-element-button" style="border-width:1px;border-radius:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><?php echo esc_html('Get Started', 'hello-agency') ?></a></div>
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