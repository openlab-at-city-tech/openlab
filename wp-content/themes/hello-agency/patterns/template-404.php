<?php

/**
 * Title: 404 Template
 * Slug: hello-agency/template-404
 * Categories: hello-agency
 */
?>
<!-- wp:group {"tagName":"main","layout":{"type":"constrained","contentSize":"100%"}} -->
<main class="wp-block-group"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"nutral","layout":{"type":"constrained","contentSize":"1200px"}} -->
    <div class="wp-block-group has-nutral-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"150px","fontStyle":"normal","fontWeight":"600","lineHeight":0.7}},"textColor":"primary"} -->
        <h1 class="wp-block-heading has-text-align-center has-primary-color has-text-color" style="font-size:150px;font-style:normal;font-weight:600;line-height:0.7"><?php echo esc_html('404', 'hello-agency') ?></h1>
        <!-- /wp:heading -->

        <!-- wp:heading {"textAlign":"center","level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"300","textTransform":"uppercase","letterSpacing":"4px"}},"fontSize":"medium"} -->
        <h4 class="wp-block-heading has-text-align-center has-medium-font-size" style="font-style:normal;font-weight:300;letter-spacing:4px;text-transform:uppercase"><?php echo esc_html('OOPS! Page Not Found!', 'hello-agency') ?></h4>
        <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|80"}}},"backgroundColor":"background","layout":{"type":"constrained","contentSize":"1200px"}} -->
    <div class="wp-block-group has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:heading -->
                <h2 class="wp-block-heading"><?php echo esc_html('Helpful Link', 'hello-agency') ?></h2>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.5"}}} -->
                <p style="line-height:1.5"><?php echo esc_html('Something went wrong! We couldn’t find the page you were looking for. But don’t worry, we’ve got some other Links that might be helpful:', 'hello-agency') ?></p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->

        <!-- wp:columns {"style":{"spacing":{"margin":{"top":"60px"}}}} -->
        <div class="wp-block-columns" style="margin-top:60px"><!-- wp:column -->
            <div class="wp-block-column"><!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
                <h4 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--50);font-style:normal;font-weight:600"><?php echo esc_html('Pages', 'hello-agency') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:page-list {"className":"is-style-hello-agency-page-list-bullet-hide-style is-style-hello-agency-page-list-bullet-hide-style","style":{"typography":{"lineHeight":"2"}}} /-->
            </div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
                <h4 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--50);font-style:normal;font-weight:600"><?php echo esc_html('Categories', 'hello-agency') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:categories {"className":"is-style-hello-agency-categories-bullet-hide-style is-style-hello-agency-categories-bullet-hide-style","style":{"typography":{"lineHeight":"2"}}} /-->
            </div>
            <!-- /wp:column -->

            <!-- wp:column -->
            <div class="wp-block-column"><!-- wp:heading {"level":4,"style":{"typography":{"fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} -->
                <h4 class="wp-block-heading" style="margin-bottom:var(--wp--preset--spacing--50);font-style:normal;font-weight:600"><?php echo esc_html('Posts', 'hello-agency') ?></h4>
                <!-- /wp:heading -->

                <!-- wp:query {"queryId":22,"query":{"perPage":"20","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
                <div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default"}} -->
                    <!-- wp:post-title {"level":5,"isLink":true,"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"0","right":"0","bottom":"0","left":"0"}}}} /-->
                    <!-- /wp:post-template -->
                </div>
                <!-- /wp:query -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
    </div>
    <!-- /wp:group -->
</main>
<!-- /wp:group -->