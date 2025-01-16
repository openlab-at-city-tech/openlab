<?php
/**
 * The template part for single post.
 *
 * @package Kenta
 */

use LottaFramework\Utils;

$sidebar         = kenta_get_sidebar_layout( 'post' );
$container_style = kenta_get_container_style( 'post' );

$container_css = kenta_container_css( array(
	'sidebar' => $sidebar,
	'style'   => $container_style,
	'layout'  => kenta_get_container_layout( 'post' ),
) );

/**
 * Hook - kenta_action_before_single_post_container.
 */
do_action( 'kenta_action_before_single_post_container', $sidebar );
?>

<main class="<?php Utils::the_clsx( $container_css ) ?>">
    <div id="content" class="kenta-article-content-wrap relative flex-grow max-w-full">
		<?php
		// posts loop
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook - kenta_action_before_single_post.
			 */
			do_action( 'kenta_action_before_single_post' );

			/**
			 * Hook - kenta_action_single_post.
			 */
			do_action( 'kenta_action_single_post', $sidebar );

			/**
			 * Hook - kenta_action_after_single_post.
			 */
			do_action( 'kenta_action_after_single_post' );
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
