<?php
/**
 * Show posts loop
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

$sidebar = 'no-sidebar';

if ( CZ::checked( 'kenta_archive_sidebar_section' ) ) {
	$sidebar = CZ::get( 'kenta_archive_sidebar_layout' );
}

$container_css = kenta_container_css( array(
	'sidebar' => $sidebar,
	'style'   => 'boxed',
	'layout'  => 'narrow',
	'css'     => [ 'kenta-posts-container' ]
) );
?>
<main class="<?php Utils::the_clsx( $container_css ); ?>">
    <div id="content" class="kenta-posts flex-grow kenta-max-w-wide has-global-padding mx-auto w-full">
		<?php kenta_render_posts_list(); ?>
    </div>

	<?php
	/**
	 * Hook - kenta_action_sidebar.
	 */
	do_action( 'kenta_action_sidebar', $sidebar );
	?>
</main>
