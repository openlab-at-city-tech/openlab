<?php
/**
 * Entry header for singular views.
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

$paged_info = Content\Component::get_paged_info();

$has_page_summary = false;
if (
	empty( $paged_info )
	&& has_excerpt()
) {
	$has_page_summary = true;
}

?>

<header id="page-header" class="entry-header page-header">
	<div class="page-header-content">
		<?php

		/**
		 * Fires before page header text content.
		 *
		 * @since  1.0.0
		 */
		do_action( 'michelle/page_header/top' );

		?>
		<div class="entry-header-text page-header-text<?php
			if ( $has_page_summary ) {
				echo ' has-page-summary';
			}
			?>">
			<h1 class="entry-title page-title"><?php
				the_title();
				echo $paged_info; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?></h1>
			<?php

			if (
				empty( $paged_info )
				&& has_excerpt()
			) {
				echo str_replace( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'entry-summary',
					'entry-summary page-summary',
					get_the_excerpt()
				);
			}

			get_template_part( 'templates/parts/meta/entry-meta-page-header', get_post_type( get_the_ID() ) );

			?>
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
