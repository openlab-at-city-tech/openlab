<?php

/**
 * Title: Call to Action
 * Slug: hello-agency/call-to-action
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/cta_bg.jpg',
);
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"hello-agency-animate ha-cover-section","layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group alignfull hello-agency-animate ha-cover-section" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[0]) ?>","id":382,"dimRatio":50,"minHeight":570,"isDark":false,"layout":{"type":"constrained"}} -->
    <div class="wp-block-cover is-light" style="min-height:570px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><img class="wp-block-cover__image-background wp-image-382" alt="" src="<?php echo esc_url($hello_agency_images[0]) ?>" data-object-fit="cover" />
        <div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"bottom":"62px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
            <div class="wp-block-group" style="margin-bottom:62px"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"100px","letterSpacing":"1.5px"}},"textColor":"foreground-alt"} -->
                <h2 class="wp-block-heading has-foreground-alt-color has-text-color" style="font-size:100px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Transform Digital', 'hello-agency') ?></h2>
                <!-- /wp:heading -->

                <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"100px","letterSpacing":"1.5px"},"color":{"text":"#ffffff00"}},"className":"hello-agency-text-stroke"} -->
                <h2 class="wp-block-heading hello-agency-text-stroke has-text-color" style="color:#ffffff00;font-size:100px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Landscape', 'hello-agency') ?></h2>
                <!-- /wp:heading -->

                <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"},"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"fontSize":"big"} -->
                <div class="wp-block-buttons has-custom-font-size has-big-font-size" style="margin-top:var(--wp--preset--spacing--40);font-style:normal;font-weight:600"><!-- wp:button {"textColor":"foreground-alt","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"className":"is-style-fill hello-agency-buttons","fontSize":"medium"} -->
                    <div class="wp-block-button has-custom-font-size is-style-fill hello-agency-buttons has-medium-font-size" style="font-style:normal;font-weight:600;text-transform:uppercase"><a class="wp-block-button__link has-foreground-alt-color has-text-color has-background wp-element-button" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php echo esc_html('Schedule an Appointment', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
    </div>
    <!-- /wp:cover -->
</div>
<!-- /wp:group -->