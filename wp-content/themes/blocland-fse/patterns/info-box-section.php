<?php
/**
 * Title: Info Box Section
 * Slug: blocland-fse/info-box-section
 * Categories: featured
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div id="info-box-section" class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">

    <!-- wp:columns {"className":"wow fadeInUp"} -->
	<div class="wp-block-columns wow fadeInUp">

        <!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">

            <!-- wp:outermost/icon-block {"iconName":"wordpress-pin","itemsJustification":"left","iconBackgroundColor":"light","iconBackgroundColorValue":"#f2f2f0","iconColor":"tertiary","iconColorValue":"#ACB992","width":80,"align":"center","style":{"border":{"radius":"100px"},"spacing":{"padding":{"top":"10px","right":"10px","bottom":"10px","left":"10px"}}}} -->
			<div class="wp-block-outermost-icon-block aligncenter items-justified-left">
                <div class="icon-container has-icon-color has-icon-background-color has-light-background-color" style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:100px;color:#ACB992;width:80px">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="m21.5 9.1-6.6-6.6-4.2 5.6c-1.2-.1-2.4.1-3.6.7-.1 0-.1.1-.2.1-.5.3-.9.6-1.2.9l3.7 3.7-5.7 5.7v1.1h1.1l5.7-5.7 3.7 3.7c.4-.4.7-.8.9-1.2.1-.1.1-.2.2-.3.6-1.1.8-2.4.6-3.6l5.6-4.1zm-7.3 3.5.1.9c.1.9 0 1.8-.4 2.6l-6-6c.8-.4 1.7-.5 2.6-.4l.9.1L15 4.9 19.1 9l-4.9 3.6z"></path>
                    </svg>
                </div>
            </div>
			<!-- /wp:outermost/icon-block -->

			<!-- wp:heading {"textAlign":"left","level":3,"textColor":"foreground","fontFamily":"montserrat"} -->
			<h3 class="has-text-align-left has-foreground-color has-text-color has-montserrat-font-family"><?php echo esc_html_x("Is Gutenberg Great?","Info box section title","blocland-fse") ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"left","textColor":"foreground","fontSize":"small"} -->
			<p class="has-text-align-left has-foreground-color has-text-color has-small-font-size">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"align":"left","textColor":"foreground","fontSize":"small"} -->
			<p class="has-text-align-left has-foreground-color has-text-color has-small-font-size"><strong>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</strong></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
			<div class="wp-block-buttons">

                <!-- wp:button {"textColor":"primary","style":{"border":{"radius":"0px"}},"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline">
                    <a class="wp-block-button__link has-primary-color has-text-color wp-element-button" style="border-radius:0px"><?php echo esc_html_x("See More...","See more text","blocland-fse") ?></a>
                </div>
				<!-- /wp:button -->

            </div>
			<!-- /wp:buttons -->

        </div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">

            <!-- wp:image {"id":2564,"sizeSlug":"large","linkDestination":"none","style":{"color":{"duotone":"unset"}}} -->
			<figure class="wp-block-image size-large">
                <img src="<?php echo esc_url(BLOCLAND_FSE_URI.'/assets/img/info-box-image.jpg'); ?>" alt="" class="wp-image-2564"/>
            </figure>
			<!-- /wp:image -->

        </div>
		<!-- /wp:column -->

    </div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
