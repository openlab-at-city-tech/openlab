<?php
/**
 * Template part for displaying a post's header
 *
 * @package kadence
 */

namespace Kadence;

?>
<header class="entry-header">

	<?php
	/**
	 * Hook for entry header.
	 *
	 * @hooked Kadence\loop_entry_taxonomies - 10
	 * @hooked Kadence\loop_entry_title - 20
	 * @hooked Kadence\loop_entry_meta - 30
	 */
	do_action( 'kadence_loop_entry_header' );
	?>
</header><!-- .entry-header -->
