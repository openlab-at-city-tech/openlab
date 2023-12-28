<?php
/**
 * Title: Icon Cards Section
 * Slug: blocland-fse/icon-cards-section
 * Categories: featured
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"backgroundColor":"light","layout":{"type":"constrained"}} -->
<div id="icon-cards-section" class="wp-block-group has-light-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">

    <!-- wp:heading {"textAlign":"center","level":1} -->
	<h1 class="has-text-align-center">
		<?php echo esc_html_x("We create block themes","Header button text","blocland-fse") ?>
    </h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","textColor":"foreground","fontSize":"medium"} -->
	<p class="has-text-align-center has-foreground-color has-text-color has-medium-font-size">Duis aute irure dolor in reprehenderit in voluptate velit.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"className":"wow fadeInUp"} -->
	<div class="wp-block-columns wow fadeInUp">
        <!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"background"} -->
		<div class="wp-block-column has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

            <!-- wp:outermost/icon-block {"iconName":"wordpress-image","itemsJustification":"center","iconBackgroundColor":"light","iconBackgroundColorValue":"#f2f2f0","iconColor":"tertiary","iconColorValue":"#ACB992","width":100,"style":{"border":{"radius":"100px"},"spacing":{"padding":{"top":"10px","right":"10px","bottom":"10px","left":"10px"}}}} -->
			<div class="wp-block-outermost-icon-block items-justified-center">
                <div class="icon-container has-icon-color has-icon-background-color has-light-background-color" style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:100px;color:#ACB992;width:100px">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 4.5h14c.3 0 .5.2.5.5v8.4l-3-2.9c-.3-.3-.8-.3-1 0L11.9 14 9 12c-.3-.2-.6-.2-.8 0l-3.6 2.6V5c-.1-.3.1-.5.4-.5zm14 15H5c-.3 0-.5-.2-.5-.5v-2.4l4.1-3 3 1.9c.3.2.7.2.9-.1L16 12l3.5 3.4V19c0 .3-.2.5-.5.5z"></path>
                    </svg>
                </div>
            </div>
			<!-- /wp:outermost/icon-block -->

			<!-- wp:heading {"textAlign":"center","textColor":"foreground"} -->
			<h2 class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html_x("Full","Icon cards 1 title","blocland-fse") ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons"><!-- wp:button {"textColor":"primary","style":{"border":{"radius":"0px"}},"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-primary-color has-text-color wp-element-button" style="border-radius:0px"><?php echo esc_html_x("More...","Card box button text","blocland-fse") ?></a></div>
				<!-- /wp:button -->
            </div>
			<!-- /wp:buttons -->

        </div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"background"} -->
		<div class="wp-block-column has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

            <!-- wp:outermost/icon-block {"iconName":"wordpress-chartBar","itemsJustification":"center","iconBackgroundColor":"light","iconBackgroundColorValue":"#f2f2f0","iconColor":"tertiary","iconColorValue":"#ACB992","width":100,"style":{"border":{"radius":"100px"},"spacing":{"padding":{"top":"10px","right":"10px","bottom":"10px","left":"10px"}}}} -->
			<div class="wp-block-outermost-icon-block items-justified-center">
                <div class="icon-container has-icon-color has-icon-background-color has-light-background-color" style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:100px;color:#ACB992;width:100px">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M11.25 5h1.5v15h-1.5V5zM6 10h1.5v10H6V10zm12 4h-1.5v6H18v-6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
			<!-- /wp:outermost/icon-block -->

			<!-- wp:heading {"textAlign":"center","textColor":"foreground"} -->
			<h2 class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html_x("Site","Icon cards 2 title","blocland-fse") ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
                <!-- wp:button {"textColor":"primary","style":{"border":{"radius":"0px"}},"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-primary-color has-text-color wp-element-button" style="border-radius:0px"><?php echo esc_html_x("More...","Card box button text","blocland-fse") ?></a></div>
				<!-- /wp:button -->
            </div>
			<!-- /wp:buttons -->

        </div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"background"} -->
		<div class="wp-block-column has-background-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

            <!-- wp:outermost/icon-block {"iconName":"wordpress-codepen","itemsJustification":"center","iconBackgroundColor":"light","iconBackgroundColorValue":"#f2f2f0","iconColor":"tertiary","iconColorValue":"#ACB992","width":100,"style":{"border":{"radius":"100px"},"spacing":{"padding":{"top":"10px","right":"10px","bottom":"10px","left":"10px"}}}} -->
			<div class="wp-block-outermost-icon-block items-justified-center">
                <div class="icon-container has-icon-color has-icon-background-color has-light-background-color" style="padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:100px;color:#ACB992;width:100px">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.016,8.84c-0.002-0.013-0.005-0.025-0.007-0.037c-0.005-0.025-0.008-0.048-0.015-0.072 c-0.003-0.015-0.01-0.028-0.013-0.042c-0.008-0.02-0.015-0.04-0.023-0.062c-0.007-0.015-0.013-0.028-0.02-0.042 c-0.008-0.02-0.018-0.037-0.03-0.057c-0.007-0.013-0.017-0.027-0.025-0.038c-0.012-0.018-0.023-0.035-0.035-0.052 c-0.01-0.013-0.02-0.025-0.03-0.037c-0.015-0.017-0.028-0.032-0.043-0.045c-0.01-0.012-0.022-0.023-0.035-0.035 c-0.015-0.015-0.032-0.028-0.048-0.04c-0.012-0.01-0.025-0.02-0.037-0.03c-0.005-0.003-0.01-0.008-0.015-0.012l-9.161-6.096 c-0.289-0.192-0.666-0.192-0.955,0L2.359,8.237C2.354,8.24,2.349,8.245,2.344,8.249L2.306,8.277 c-0.017,0.013-0.033,0.027-0.048,0.04C2.246,8.331,2.234,8.342,2.222,8.352c-0.015,0.015-0.028,0.03-0.042,0.047 c-0.012,0.013-0.022,0.023-0.03,0.037C2.139,8.453,2.125,8.471,2.115,8.488C2.107,8.501,2.099,8.514,2.09,8.526 C2.079,8.548,2.069,8.565,2.06,8.585C2.054,8.6,2.047,8.613,2.04,8.626C2.032,8.648,2.025,8.67,2.019,8.69 c-0.005,0.013-0.01,0.027-0.013,0.042C1.999,8.755,1.995,8.778,1.99,8.803C1.989,8.817,1.985,8.828,1.984,8.84 C1.978,8.879,1.975,8.915,1.975,8.954v6.093c0,0.037,0.003,0.075,0.008,0.112c0.002,0.012,0.005,0.025,0.007,0.038 c0.005,0.023,0.008,0.047,0.015,0.072c0.003,0.015,0.008,0.028,0.013,0.04c0.007,0.022,0.013,0.042,0.022,0.063 c0.007,0.015,0.013,0.028,0.02,0.04c0.008,0.02,0.018,0.038,0.03,0.058c0.007,0.013,0.015,0.027,0.025,0.038 c0.012,0.018,0.023,0.035,0.035,0.052c0.01,0.013,0.02,0.025,0.03,0.037c0.013,0.015,0.028,0.032,0.042,0.045 c0.012,0.012,0.023,0.023,0.035,0.035c0.015,0.013,0.032,0.028,0.048,0.04l0.038,0.03c0.005,0.003,0.01,0.007,0.013,0.01 l9.163,6.095C11.668,21.953,11.833,22,12,22c0.167,0,0.332-0.047,0.478-0.144l9.163-6.095l0.015-0.01 c0.013-0.01,0.027-0.02,0.037-0.03c0.018-0.013,0.035-0.028,0.048-0.04c0.013-0.012,0.025-0.023,0.035-0.035 c0.017-0.015,0.03-0.032,0.043-0.045c0.01-0.013,0.02-0.025,0.03-0.037c0.013-0.018,0.025-0.035,0.035-0.052 c0.008-0.013,0.018-0.027,0.025-0.038c0.012-0.02,0.022-0.038,0.03-0.058c0.007-0.013,0.013-0.027,0.02-0.04 c0.008-0.022,0.015-0.042,0.023-0.063c0.003-0.013,0.01-0.027,0.013-0.04c0.007-0.025,0.01-0.048,0.015-0.072 c0.002-0.013,0.005-0.027,0.007-0.037c0.003-0.042,0.007-0.079,0.007-0.117V8.954C22.025,8.915,22.022,8.879,22.016,8.84z M12.862,4.464l6.751,4.49l-3.016,2.013l-3.735-2.492V4.464z M11.138,4.464v4.009l-3.735,2.494L4.389,8.954L11.138,4.464z M3.699,10.562L5.853,12l-2.155,1.438V10.562z M11.138,19.536l-6.749-4.491l3.015-2.011l3.735,2.492V19.536z M12,14.035L8.953,12 L12,9.966L15.047,12L12,14.035z M12.862,19.536v-4.009l3.735-2.492l3.016,2.011L12.862,19.536z M20.303,13.438L18.147,12 l2.156-1.438L20.303,13.438z"></path>
                    </svg>
                </div>
            </div>
			<!-- /wp:outermost/icon-block -->

			<!-- wp:heading {"textAlign":"center","textColor":"foreground"} -->
			<h2 class="has-text-align-center has-foreground-color has-text-color"><?php echo esc_html_x("Editing","Icon cards 3 title","blocland-fse") ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
                <!-- wp:button {"textColor":"primary","style":{"border":{"radius":"0px"}},"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline">
                    <a class="wp-block-button__link has-primary-color has-text-color wp-element-button" style="border-radius:0px"><?php echo esc_html_x("More...","Card box button text","blocland-fse") ?></a>
                </div>
				<!-- /wp:button -->
            </div>
			<!-- /wp:buttons -->

        </div>
		<!-- /wp:column -->
    </div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
