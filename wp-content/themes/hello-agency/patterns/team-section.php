<?php

/**
 * Title: Team Section
 * Slug: hello-agency/team-section
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/team_image.jpg',
    $hello_agency_url . 'assets/images/team_more.png',
);
?>
<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50"}}},"className":"hello-agency-animate","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-right:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:cover {"url":"<?php echo esc_url($hello_agency_images[0]) ?>","id":190,"dimRatio":70,"minHeight":590,"contentPosition":"bottom left","style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"60px","right":"60px"}}},"className":"is-style-hello-agency-cover-round-style","layout":{"type":"constrained"}} -->
    <div class="wp-block-cover has-custom-content-position is-position-bottom-left is-style-hello-agency-cover-round-style" style="padding-top:60px;padding-right:60px;padding-bottom:60px;padding-left:60px;min-height:590px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-190" alt="" src="<?php echo esc_url($hello_agency_images[0]) ?>" data-object-fit="cover" />
        <div class="wp-block-cover__inner-container"><!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column {"width":"50%","style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
                <div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph {"align":"left","placeholder":"Write title…","textColor":"foreground","fontSize":"big"} -->
                    <p class="has-text-align-left has-foreground-color has-text-color has-big-font-size"><?php echo esc_html('/ Meet our team', 'hello-agency') ?></p>
                    <!-- /wp:paragraph -->

                    <!-- wp:heading {"style":{"typography":{"fontSize":"56px","fontStyle":"normal","fontWeight":"600"}}} -->
                    <h2 class="wp-block-heading" style="font-size:56px;font-style:normal;font-weight:600"><?php echo esc_html('Not just a team but a big family.', 'hello-agency') ?></h2>
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
    <div class="wp-block-group" style="padding-right:60px;padding-left:60px"><!-- wp:image {"id":201,"height":200,"sizeSlug":"full","linkDestination":"custom","className":"hello-agency-rotator team-more-rotator"} -->
        <figure class="wp-block-image size-full is-resized hello-agency-rotator team-more-rotator"><a href="#"><img src="<?php echo esc_url($hello_agency_images[1]) ?>" alt="" class="wp-image-201" style="height:200px" height="200" /></a></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->