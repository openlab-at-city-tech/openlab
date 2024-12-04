<?php

/**
 * Title: Banner section with background image
 * Slug: hello-agency/banner-with-background-image
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/banner_bg_image.jpg',
);
?>
<!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[0]) ?>","id":689,"dimRatio":70,"minHeight":760,"layout":{"type":"constrained"}} -->
<div class="wp-block-cover" style="min-height:760px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-689" alt="" src="<?php echo esc_url($hello_agency_images[0]) ?>" data-object-fit="cover" />
    <div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"padding":{"left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1180px"}} -->
        <div class="wp-block-group" style="padding-right:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"860px"}} -->
            <div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":5,"style":{"typography":{"textTransform":"uppercase","fontSize":"12px","fontStyle":"normal","fontWeight":"600"}},"textColor":"primary-shade"} -->
                <h5 class="wp-block-heading has-text-align-center has-primary-shade-color has-text-color" style="font-size:12px;font-style:normal;font-weight:600;text-transform:uppercase"><?php echo esc_html('Welcome to Hello Agency', 'hello-agency') ?></h5>
                <!-- /wp:heading -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                <div class="wp-block-group"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700","lineHeight":"1.1","fontSize":"120px","textTransform":"uppercase"},"color":{"text":"#ffffff00"}},"className":"hello-agency-text-stroke"} -->
                    <h2 class="wp-block-heading has-text-align-center hello-agency-text-stroke has-text-color" style="color:#ffffff00;font-size:120px;font-style:normal;font-weight:700;line-height:1.1;text-transform:uppercase"><?php echo esc_html('Meet hello', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->

                    <!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700","lineHeight":"1.1","fontSize":"120px","textTransform":"uppercase"}},"textColor":"heading-color"} -->
                    <h2 class="wp-block-heading has-text-align-center has-heading-color-color has-text-color" style="font-size:120px;font-style:normal;font-weight:700;line-height:1.1;text-transform:uppercase"><?php echo esc_html('agency', 'hello-agency') ?></h2>
                    <!-- /wp:heading -->
                </div>
                <!-- /wp:group -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained","contentSize":"660px"}} -->
                <div class="wp-block-group"><!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1.5"}}} -->
                    <p class="has-text-align-center" style="line-height:1.5"><?php echo esc_html('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:group -->

                <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"35px"}}}} -->
                <div class="wp-block-buttons" style="margin-top:35px"><!-- wp:button {"backgroundColor":"heading-color","textColor":"background-alt","style":{"spacing":{"padding":{"left":"30px","right":"30px","top":"17px","bottom":"17px"}},"border":{"radius":"8px"}},"className":"is-style-button-hover-primary-bgcolor","fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size is-style-button-hover-primary-bgcolor has-normal-font-size"><a class="wp-block-button__link has-background-alt-color has-heading-color-background-color has-text-color has-background wp-element-button" style="border-radius:8px;padding-top:17px;padding-right:30px;padding-bottom:17px;padding-left:30px"><?php echo esc_html('Schedule Call', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
</div>
<!-- /wp:cover -->