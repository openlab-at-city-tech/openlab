<?php
/**
 * Template part for displaying a post
 *
 * @package kadence
 */

namespace Kadence;

?>
<li class="entry-list-item">
	<article <?php post_class( 'entry content-bg loop-entry' ); ?>>
		<?php
			/**
			 * Hook for entry thumbnail.
			 *
			 * @hooked Kadence\loop_entry_thumbnail
			 */
			do_action( 'kadence_loop_entry_thumbnail' );
		?>
		<div class="entry-content-wrap">
			<?php
			/**
			 * Hook for entry content.
			 *
			 * @hooked Kadence\loop_entry_header - 10
			 * @hooked Kadence\loop_entry_summary - 20
			 * @hooked Kadence\loop_entry_footer - 30
			 */
			do_action( 'kadence_loop_entry_content' );
			?>
		</div>
	</article>
</li>
