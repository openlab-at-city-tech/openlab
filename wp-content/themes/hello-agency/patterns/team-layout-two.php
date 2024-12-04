<?php

/**
 * Title: Our Team Layout 2
 * Slug: hello-agency/team-layout-two
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/team_1.jpg',
    $hello_agency_url . 'assets/images/team_2.jpg',
    $hello_agency_url . 'assets/images/team_3.jpg',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","bottom":"80px","top":"80px"}}},"className":"hello-agency-animate","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-top:80px;padding-right:var(--wp--preset--spacing--50);padding-bottom:80px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns -->
    <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
            <div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}},"className":"hello-agency-text-stroke"} -->
                <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Our', 'hello-agency') ?></h2>
                <!-- /wp:heading -->

                <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
                <h2 class="wp-block-heading" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Team', 'hello-agency') ?></h2>
                <!-- /wp:heading -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
            <div class="wp-block-group"><!-- wp:paragraph -->
                <p><?php echo esc_html('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->

                <!-- wp:buttons -->
                <div class="wp-block-buttons"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"}}},"className":"is-style-fill hello-agency-buttons","fontSize":"normal"} -->
                    <div class="wp-block-button has-custom-font-size is-style-fill hello-agency-buttons has-normal-font-size" style="font-style:normal;font-weight:500;text-transform:uppercase"><a class="wp-block-button__link has-heading-color-color has-text-color has-background wp-element-button" style="background-color:#ffffff00;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><?php echo esc_html('view all Teams', 'hello-agency') ?></a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->

    <!-- wp:columns {"style":{"spacing":{"margin":{"top":"70px"},"blockGap":{"left":"40px"}}}} -->
    <div class="wp-block-columns" style="margin-top:70px"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[0]) ?>","id":590,"dimRatio":80,"minHeight":460,"customGradient":"linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)","contentPosition":"bottom center","style":{"spacing":{"blockGap":"1rem","padding":{"top":"30px","bottom":"30px"}}},"className":"hello-agency-team-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover has-custom-content-position is-position-bottom-center hello-agency-team-box" style="padding-top:30px;padding-bottom:30px;min-height:460px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-80 has-background-dim wp-block-cover__gradient-background has-background-gradient" style="background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)"></span><img class="wp-block-cover__image-background wp-image-590" alt="" src="<?php echo esc_url($hello_agency_images[0]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":4,"textColor":"heading-color","fontSize":"big"} -->
                    <h4 class="wp-block-heading has-text-align-center has-heading-color-color has-text-color has-big-font-size"><?php echo esc_html('Tom Hank', 'hello-agency') ?></h4>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"align":"center","textColor":"foreground"} -->
                    <p class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html('Chief Technical Officer', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[1]) ?>","id":603,"dimRatio":80,"minHeight":460,"customGradient":"linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)","contentPosition":"bottom center","style":{"spacing":{"blockGap":"1rem","padding":{"top":"30px","bottom":"30px"}}},"className":"hello-agency-team-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover has-custom-content-position is-position-bottom-center hello-agency-team-box" style="padding-top:30px;padding-bottom:30px;min-height:460px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-80 has-background-dim wp-block-cover__gradient-background has-background-gradient" style="background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)"></span><img class="wp-block-cover__image-background wp-image-603" alt="" src="<?php echo esc_url($hello_agency_images[1]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":4,"textColor":"heading-color","fontSize":"big"} -->
                    <h4 class="wp-block-heading has-text-align-center has-heading-color-color has-text-color has-big-font-size"><?php echo esc_html('Melinda K', 'hello-agency') ?></h4>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"align":"center","textColor":"foreground"} -->
                    <p class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html('Chief Technical Officer', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[2]) ?>","id":604,"dimRatio":80,"minHeight":460,"customGradient":"linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)","contentPosition":"bottom center","style":{"spacing":{"blockGap":"1rem","padding":{"top":"30px","bottom":"30px"}}},"className":"hello-agency-team-box","layout":{"type":"constrained"}} -->
            <div class="wp-block-cover has-custom-content-position is-position-bottom-center hello-agency-team-box" style="padding-top:30px;padding-bottom:30px;min-height:460px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-80 has-background-dim wp-block-cover__gradient-background has-background-gradient" style="background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,0.74) 100%)"></span><img class="wp-block-cover__image-background wp-image-604" alt="" src="<?php echo esc_url($hello_agency_images[2]) ?>" data-object-fit="cover" />
                <div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":4,"textColor":"heading-color","fontSize":"big"} -->
                    <h4 class="wp-block-heading has-text-align-center has-heading-color-color has-text-color has-big-font-size"><?php echo esc_html('Theory M', 'hello-agency') ?></h4>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"align":"center","textColor":"foreground"} -->
                    <p class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html('Chief Technical Officer', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:cover -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->