<?php
/**
 * Title: Call To Action Section
 * Slug: blocland-fse/call-to-action-section
 * Categories: featured
 */
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div id="call-to-action-section" class="wp-block-group"><!-- wp:cover {"url":"<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/contact_image.jpg'); ?>","dimRatio":10,"minHeight":283,"minHeightUnit":"px","contentPosition":"center center","isDark":false,"align":"full","style":{"color":{"duotone":["#464E2E","#ACB992"]}}} -->
    <div class="wp-block-cover alignfull is-light" style="min-height:283px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-10 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/contact_image.jpg'); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"textColor":"background"} -->
            <h1 class="has-text-align-center has-background-color has-text-color"><?php echo esc_html_x( ' Call to Action', 'Call to action section title', 'blocland-fse' ); ?></h1>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center","textColor":"background","fontSize":"medium"} -->
            <p class="has-text-align-center has-background-color has-text-color has-medium-font-size">Duis aute irure dolor in reprehenderit in voluptate velit.</p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
            <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"light","textColor":"foreground","fontSize":"medium"} -->
                <div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-foreground-color has-light-background-color has-text-color has-background wp-element-button"><?php echo esc_html_x( ' Contact Now', 'Call to action section button text', 'blocland-fse' ); ?></a></div>
                <!-- /wp:button --></div>
            <!-- /wp:buttons --></div></div>
    <!-- /wp:cover --></div>
<!-- /wp:group -->
