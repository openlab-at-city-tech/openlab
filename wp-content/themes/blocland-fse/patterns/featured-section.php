<?php
/**
 * Title: Featured Section
 * Slug: blocland-fse/featured-section
 * Categories: featured
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"gradient":"default-gradient","layout":{"type":"constrained"}} -->
<div id="featured-section" class="wp-block-group has-default-gradient-gradient-background has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">

    <!-- wp:columns {"className":"wow fadeInUp"} -->
	<div class="wp-block-columns wow fadeInUp">

        <!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
            <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"background","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

                <!-- wp:heading {"textAlign":"center","level":1,"fontSize":"x-large-3"} -->
				<h1 class="has-text-align-center has-x-large-3-font-size"><?php echo esc_html_x("Future of Web","Featured section title","blocland-fse") ?></h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"align":"center","textColor":"foreground"} -->
				<p class="has-text-align-center has-foreground-color has-text-color">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
				<div class="wp-block-buttons">
                    <!-- wp:button {"textColor":"background","style":{"border":{"radius":"0px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button is-style-fill">
                        <a class="wp-block-button__link has-background-color has-text-color wp-element-button" style="border-radius:0px"><?php echo esc_html_x("See More...","See more text","blocland-fse") ?></a>
                    </div>
					<!-- /wp:button -->
                </div>
				<!-- /wp:buttons -->

            </div>
			<!-- /wp:group -->
        </div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"bottom"} -->
		<div class="wp-block-column is-vertically-aligned-bottom">

            <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large">
                <img src="<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/featured-image.png'); ?>" alt=""/>
            </figure>
			<!-- /wp:image -->

        </div>
		<!-- /wp:column -->

    </div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
