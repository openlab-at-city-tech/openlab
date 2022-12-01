<?php // phpcs:ignore WPThemeReview.Templates.ReservedFileNamePrefix.ReservedTemplatePrefixFound
/**
 * Page header for search results page.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.11
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! Content\Component::show_primary_title() ) {
	return;
}

?>

<header id="page-header" class="page-header">
	<div class="page-header-content">
		<?php

		/**
		 * Fires before page header text content.
		 *
		 * @since  1.0.0
		 */
		do_action( 'michelle/page_header/top' );

		?>
		<div class="page-header-text">
			<h1 class="page-title"><?php

			printf(
				/* translators: %s: search query. */
				esc_html__( 'Search Results for: %s', 'michelle' ),
				'<span>' . get_search_query() . '</span>'
			);

			echo Content\Component::get_paged_info(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			?></h1>
		</div>
		<?php

		/**
		 * Fires after page header text content.
		 *
		 * @since  1.0.0
		 */
		do_action( 'michelle/page_header/bottom' );

		?>
	</div>
</header>
