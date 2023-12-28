<?php
/**
 * Featured posts loop content.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle;

use WP_Query;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$featured = Loop\Featured_Posts::get_posts();

if (
	empty( $featured )
	|| ! $featured->have_posts()
) {
	return;
}

?>

<section class="featured-posts-section alignfull" aria-label="<?php echo esc_attr( 'Featured posts', 'michelle' ); ?>">
	<div class="featured-posts has-<?php echo absint( count( $featured->posts ) ); ?>-featured-posts">
		<?php

		do_action( 'tha_content_while_before', 'featured' );

		while ( $featured->have_posts() ) :
			$featured->the_post();

			get_template_part( 'templates/parts/content/content', 'featured' );
		endwhile;

		do_action( 'tha_content_while_after', 'featured' );

		?>
	</div>
</section>
