<?php
/**
 * Block area: Error 404 page.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.6
 * @version  1.3.0
 */

namespace WebManDesign\Michelle;

use WP_Query;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$area = Customize\Mod::get( 'block_area_error_404' );

if ( empty( $area ) ) {
	get_template_part( 'templates/parts/content/content', '404' );
	return;
}

$area_query = new WP_Query( array(
	'p'         => $area,
	'post_type' => Content\Block_Area::get_post_type(),
) );

if (
	! $area_query->have_posts()
	|| empty( trim( (string) $area_query->post->post_content ) )
) {
	get_template_part( 'templates/parts/content/content', '404' );
	return;
}

?>

<div class="error-404-content error-404-blocks">
	<?php

	/**
	 * Fires before error 404 block area content.
	 *
	 * @since  1.0.0
	 */
	do_action( 'michelle/block/area/404/before' );

	while ( $area_query->have_posts() ) {
		$area_query->the_post();

		the_content();
	}

	wp_reset_postdata();

	/**
	 * Fires after error 404 block area content.
	 *
	 * @since  1.0.0
	 */
	do_action( 'michelle/block/area/404/after' );

	?>
</div>
