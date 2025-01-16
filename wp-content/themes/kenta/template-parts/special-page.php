<?php
/**
 * The template part for pages.
 *
 * @package Kenta
 */

use LottaFramework\Utils;

$sidebar         = kenta_get_sidebar_layout( 'page' );
$container_style = kenta_get_container_style( 'page' );

$container_css = kenta_container_css( array(
	'sidebar' => $sidebar,
	'style'   => $container_style,
	'layout'  => kenta_get_container_layout( 'page' ),
) );

/**
 * Hook - kenta_action_before_page_container.
 */
do_action( 'kenta_action_before_page_container', $sidebar );
?>

<main class="<?php Utils::the_clsx( $container_css ) ?>">
    <div id="content" class="kenta-article-content-wrap relative flex-grow max-w-full">
		<?php
		// posts loop
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook - kenta_action_before_page.
			 */
			do_action( 'kenta_action_before_page' );

			/**
			 * Hook - kenta_action_page.
			 */
			do_action( 'kenta_action_page', $sidebar );

			/**
			 * Hook - kenta_action_after_page.
			 */
			do_action( 'kenta_action_after_page' );
		}
		?>
    </div>

	<?php
	/**
	 * Hook - kenta_action_sidebar.
	 */
	do_action( 'kenta_action_sidebar', $sidebar );
	?>
</main>
