<?php

/**
 * Title: Newsletter Section
 * Slug: hello-agency/newsletter-section
 * Categories: hello-agency
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"right":"var:preset|spacing|50","left":"var:preset|spacing|50","bottom":"120px","top":"60px"}}},"className":"hello-agency-animate","layout":{"type":"constrained","contentSize":"1180px"}} -->
<div class="wp-block-group hello-agency-animate" style="padding-top:60px;padding-right:var(--wp--preset--spacing--50);padding-bottom:120px;padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center"} -->
    <div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"bottom":"40px"}}}} -->
        <div class="wp-block-column is-vertically-aligned-center" style="padding-bottom:40px;flex-basis:50%"><!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}},"className":"hello-agency-text-stroke"} -->
            <h2 class="wp-block-heading hello-agency-text-stroke" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Signup', 'hello-agency') ?></h2>
            <!-- /wp:heading -->

            <!-- wp:heading {"style":{"typography":{"textTransform":"uppercase","fontSize":"56px","letterSpacing":"1.5px"}}} -->
            <h2 class="wp-block-heading" style="font-size:56px;letter-spacing:1.5px;text-transform:uppercase"><?php echo esc_html('Newsletter', 'hello-agency') ?></h2>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"padding":{"top":"50px","bottom":"50px","left":"50px","right":"50px"}},"border":{"width":"1px"}},"borderColor":"foreground-alt"} -->
        <div class="wp-block-column is-vertically-aligned-center has-border-color has-foreground-alt-border-color" style="border-width:1px;padding-top:50px;padding-right:50px;padding-bottom:50px;padding-left:50px;flex-basis:50%"><!-- wp:paragraph {"align":"left","style":{"spacing":{"margin":{"top":"0px","bottom":"32px"}},"typography":{"lineHeight":"1.5"}}} -->
            <p class="has-text-align-left" style="margin-top:0px;margin-bottom:32px;line-height:1.5"><?php echo esc_html('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.', 'hello-agency') ?></p>
            <!-- /wp:paragraph -->

            <!-- wp:contact-form-7/contact-form-selector {"id":503,"hash":"436f465","title":"Newsletter Form","className":"hello-agency-newsletter"} -->
            <div class="wp-block-contact-form-7-contact-form-selector hello-agency-newsletter">[contact-form-7 id="436f465" title="Newsletter Form"]</div>
            <!-- /wp:contact-form-7/contact-form-selector -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->