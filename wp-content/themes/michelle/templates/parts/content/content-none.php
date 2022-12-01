<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.6
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( is_search() ) {
	get_template_part( 'templates/parts/component/page-header-search', get_query_var( 'post_type' ) );
} else {
	get_template_part( 'templates/parts/component/page-header', 'none' );
}

?>

<div class="page-content no-results not-found">

	<?php
	if ( is_home() && current_user_can( 'publish_posts' ) ) :

		printf(
			'<p>' . wp_kses(
				/* translators: 1: link to WP admin new post page. */
				__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'michelle' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			) . '</p>',
			esc_url( admin_url( 'post-new.php' ) )
		);

	elseif ( is_search() ) :
		?>

		<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'michelle' ); ?></p>

		<?php

		get_search_form();

	else :
		?>

		<p><?php esc_html_e( 'It seems we can not find what you are looking for. Perhaps searching can help.', 'michelle' ); ?></p>

		<?php

		get_search_form();

	endif;
	?>

</div>
