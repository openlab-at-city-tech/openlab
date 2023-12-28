<?php
/**
 * Title: 404 Page
 * Slug: blocland-fse/404
 * Categories: featured
 */
?>

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<main class="wp-block-group">
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","left":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group"
	     style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

		<!-- wp:heading {"textAlign":"center","textColor":"primary"} -->
		<h1 class="has-text-align-center has-primary-color has-text-color">404</h1>
		<!-- /wp:heading -->

		<!-- wp:heading {"textAlign":"center","textColor":"primary"} -->
		<h2 class="has-text-align-center has-primary-color has-text-color"><?php echo esc_html_x( 'Oops! Page Not Found!', 'Error code for 404 page.', 'blocland-fse' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center"><?php echo esc_html_x( 'This might be because you have typed the web address incorrectly.', 'Error code for 404 page.', 'blocland-fse' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center"><?php echo esc_html_x( 'We’re sorry but we can’t seem to find the page you requested.', 'Error code for 404 page.', 'blocland-fse' ); ?></p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->
</main>
<!-- /wp:group -->
