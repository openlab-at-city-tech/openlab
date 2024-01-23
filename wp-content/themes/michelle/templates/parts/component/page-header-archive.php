<?php // phpcs:ignore WPThemeReview.Templates.ReservedFileNamePrefix.ReservedTemplatePrefixFound
/**
 * Page header, for archive pages.
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
	&& get_the_archive_description()
) {
	$has_page_summary = true;
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
		<div class="page-header-text<?php
			if ( $has_page_summary ) {
				echo ' has-page-summary';
			}
			?>">
			<h1 class="page-title"><?php the_archive_title( '', $paged_info ); ?></h1>
			<?php

			if ( empty( $paged_info ) ) {
				the_archive_description( '<div class="archive-description page-summary">', '</div>' );
			}

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
