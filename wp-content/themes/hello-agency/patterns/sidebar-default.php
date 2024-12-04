<?php

/**
 * Title: Sidebar Default
 * Slug: hello-agency/sidebar-default
 * Categories: hello-agency
 */
$hello_agency_url = trailingslashit(get_template_directory_uri());
$hello_agency_images = array(
    $hello_agency_url . 'assets/images/sidebar_author.jpg',
);
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|70"},"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"width":"1px"}},"borderColor":"neutral-color","layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color has-neutral-color-border-color" style="border-width:1px;margin-bottom:var(--wp--preset--spacing--70);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
        <div class="wp-block-group"><!-- wp:image {"align":"left","id":1038,"width":100,"height":100,"scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%"}}} -->
            <figure class="wp-block-image alignleft size-full is-resized has-custom-border"><img src="<?php echo esc_url($hello_agency_images[0]) ?>" alt="" class="wp-image-1038" style="border-radius:50%;object-fit:cover;width:100px;height:100px" width="100" height="100" /></figure>
            <!-- /wp:image -->

            <!-- wp:heading {"textAlign":"center","level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}},"typography":{"fontStyle":"normal","fontWeight":"500"}},"fontSize":"large"} -->
            <h3 class="wp-block-heading has-text-align-center has-large-font-size" style="margin-top:var(--wp--preset--spacing--30);font-style:normal;font-weight:500"><?php echo esc_html('Alexa Liv', 'hello-agency') ?></h3>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center","textColor":"primary","fontSize":"x-small"} -->
            <p class="has-text-align-center has-primary-color has-text-color has-x-small-font-size"><?php echo esc_html('1.5M Followers', 'hello-agency') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"20px"}}}} -->
            <p class="has-text-align-center" style="margin-top:20px"><?php echo esc_html('Check out our new&nbsp;font generatorand level up your social bios. Need more? Head over to Glyphy for all the&nbsp;fancy fonts&nbsp;and&nbsp;cool symbols&nbsp;you could ever imagine.', 'hello-agency') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"20px"}}}} -->
            <div class="wp-block-buttons" style="margin-top:20px"><!-- wp:button {"textColor":"heading-color","style":{"color":{"background":"#ffffff00"},"border":{"radius":"0px","width":"1px"},"spacing":{"padding":{"left":"30px","right":"30px","top":"10px","bottom":"10px"}}},"borderColor":"heading-color","className":"is-style-button-hover-primary-bgcolor"} -->
                <div class="wp-block-button is-style-button-hover-primary-bgcolor"><a class="wp-block-button__link has-heading-color-color has-text-color has-background has-border-color has-heading-color-border-color wp-element-button" style="border-width:1px;border-radius:0px;background-color:#ffffff00;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:30px"><?php echo esc_html('Follow', 'hello-agency') ?></a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}},"border":{"width":"1px"}},"borderColor":"neutral-color","layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color has-neutral-color-border-color" style="border-width:1px;margin-bottom:var(--wp--preset--spacing--60);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search...","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"style":{"border":{"radius":"0px","width":"0px","style":"none"}},"backgroundColor":"heading-color","textColor":"background-alt","fontSize":"small"} /--></div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}},"border":{"width":"1px"}},"borderColor":"neutral-color","layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color has-neutral-color-border-color" style="border-width:1px;margin-bottom:var(--wp--preset--spacing--60);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"style":{"border":{"bottom":{"color":"var:preset|color|background-alt","width":"2px"}},"spacing":{"padding":{"bottom":"var:preset|spacing|30"},"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--background-alt);border-bottom-width:2px;margin-bottom:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":4} -->
            <h4 class="wp-block-heading"><?php echo esc_html('Latest Posts', 'hello-agency') ?></h4>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->

        <!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"width":"0px","style":"none"}},"layout":{"type":"constrained","contentSize":"1180px","justifyContent":"center"}} -->
        <div class="wp-block-group" style="border-style:none;border-width:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:query {"queryId":29,"query":{"perPage":"5","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false}} -->
            <div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default","columnCount":3}} -->
                <!-- wp:group {"style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"blockGap":"var:preset|spacing|20"},"border":{"radius":"10px"}},"layout":{"inherit":false}} -->
                <div class="wp-block-group" style="border-radius:10px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:columns -->
                    <div class="wp-block-columns"><!-- wp:column {"width":"90px"} -->
                        <div class="wp-block-column" style="flex-basis:90px"><!-- wp:post-featured-image {"height":"90px","style":{"border":{"radius":"5px"}}} /--></div>
                        <!-- /wp:column -->

                        <!-- wp:column {"verticalAlignment":"center","width":"","style":{"spacing":{"blockGap":"0"}}} -->
                        <div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical"}} -->
                            <div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"},"fontSize":"x-small"} -->
                                <div class="wp-block-group has-x-small-font-size" style="padding-bottom:0"><!-- wp:post-date {"fontSize":"xx-small"} /-->

                                    <!-- wp:paragraph -->
                                    <p>.</p>
                                    <!-- /wp:paragraph -->

                                    <!-- wp:post-author-name {"fontSize":"xx-small"} /-->
                                </div>
                                <!-- /wp:group -->

                                <!-- wp:post-title {"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground"},":hover":{"color":{"text":"var:preset|color|primary"}}}},"spacing":{"margin":{"bottom":"0","top":"var:preset|spacing|30"}},"typography":{"lineHeight":"1.2","fontStyle":"normal","fontWeight":"400","fontSize":"18px"}},"textColor":"heading-color","className":"is-style-title-hover-secondary-color"} /-->
                            </div>
                            <!-- /wp:group -->
                        </div>
                        <!-- /wp:column -->
                    </div>
                    <!-- /wp:columns -->
                </div>
                <!-- /wp:group -->
                <!-- /wp:post-template -->
            </div>
            <!-- /wp:query -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}},"border":{"width":"1px","color":"#454545","radius":"0px"}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color" style="border-color:#454545;border-width:1px;border-radius:0px;margin-bottom:var(--wp--preset--spacing--60);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"style":{"border":{"bottom":{"color":"var:preset|color|background-alt","width":"2px"}},"spacing":{"padding":{"bottom":"var:preset|spacing|30"},"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--background-alt);border-bottom-width:2px;margin-bottom:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":4} -->
            <h4 class="wp-block-heading"><?php echo esc_html('Categories', 'hello-agency') ?></h4>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->

        <!-- wp:categories {"showHierarchy":true,"showPostCounts":true,"className":"is-style-fotawp-categories-bullet-hide-style is-style-hello-agency-categories-bullet-hide-style","style":{"typography":{"lineHeight":"2"}}} /-->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}},"border":{"width":"1px","color":"#454545","radius":"0px"}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color" style="border-color:#454545;border-width:1px;border-radius:0px;margin-bottom:var(--wp--preset--spacing--60);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"style":{"border":{"bottom":{"color":"var:preset|color|background-alt","width":"2px"}},"spacing":{"padding":{"bottom":"var:preset|spacing|30"},"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--background-alt);border-bottom-width:2px;margin-bottom:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":4} -->
            <h4 class="wp-block-heading"><?php echo esc_html('Pages', 'hello-agency') ?></h4>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->

        <!-- wp:page-list {"className":"is-style-fotawp-categories-bullet-hide-style is-style-hello-agency-page-list-bullet-hide-style","style":{"typography":{"lineHeight":"2"}}} /-->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"30px","bottom":"30px","left":"30px","right":"30px"}},"border":{"width":"1px","color":"#454545","radius":"0px"}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group has-border-color" style="border-color:#454545;border-width:1px;border-radius:0px;margin-bottom:var(--wp--preset--spacing--60);padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:group {"style":{"border":{"bottom":{"color":"var:preset|color|background-alt","width":"2px"}},"spacing":{"padding":{"bottom":"var:preset|spacing|30"},"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--background-alt);border-bottom-width:2px;margin-bottom:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:heading {"level":4} -->
            <h4 class="wp-block-heading"><?php echo esc_html('Tags', 'hello-agency') ?></h4>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->

        <!-- wp:tag-cloud {"className":"is-style-fotawp-categories-bullet-hide-style","style":{"typography":{"lineHeight":"2"}}} /-->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->