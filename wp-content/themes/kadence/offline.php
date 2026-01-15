<?php
/**
 * The template for displaying offline pages
 *
 * @link https://github.com/xwp/pwa-wp#offline--500-error-handling
 *
 * @package kadence
 */

namespace Kadence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Prevent showing nav menus.
add_filter( 'has_nav_menu', '__return_false' );

get_header();

kadence()->print_styles( 'kadence-content' );
/**
 * Hook for Hero Section
 */
do_action( 'kadence_hero_header' );

?>
<div id="primary" class="content-area">
	<div class="content-container site-container">
		<div id="main" class="site-main">
			<?php
			/**
			 * Hook for anything before main content
			 */
			do_action( 'kadence_before_main_content' );

			get_template_part( 'template-parts/content/error', 'offline' );
			/**
			 * Hook for anything after main content.
			 */
			do_action( 'kadence_after_main_content' );
			?>
		</div><!-- #main -->
		<?php
		get_sidebar();
		?>
	</div>
</div><!-- #primary -->
<?php
get_footer();
