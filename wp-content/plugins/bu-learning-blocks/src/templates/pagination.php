<?php
/**
 * Template to render pagination for linear lesson pages.
 *
 * @package BU Learning Blocks
 */

?>
<div class="bulb-pagination" aria-label="pagination">
	<div class="bulb-pagination-label">Page <?php echo esc_html( $current_module_index + 1 ); ?> of <?php echo esc_html( $total_pages ); ?></div>

	<?php
	if ( 1 !== $total_pages ) {
		if ( $current_module_index > 1 && $current_module_index > $range - 1 && $showitems < $total_pages ) {
			echo "<a href='" . esc_url( get_permalink( $module_ids[0] ) ) . "'>&laquo; First Page</a>";
		}

		if ( $current_module_index > 0 && $showitems < $total_pages ) {
			echo "<a href='" . esc_url( get_permalink( $module_ids[ $current_module_index - 1 ] ) ) . "'>&lsaquo; Previous Page</a>";
		}

		foreach ( $module_ids as $position => $page_id ) {
			if ( 1 !== $total_pages && ( ! ( $position >= $current_module_index + $range + 2 || $position <= $current_module_index - $range ) || $total_pages <= $showitems ) ) {
				echo $current_post_id === $module_ids[ $position ] ? '<span class="current">' . esc_html( $position + 1 ) . '</span>' : '<a href=' . esc_url( get_permalink( $module_ids[ $position ] ) ) . ' class="inactive">' . esc_html( $position + 1 ) . '</a>';
			}
		}

		if ( $current_module_index < $total_pages - 1 && $showitems < $total_pages ) {
			echo '<a href="' . esc_url( get_permalink( $module_ids[ $current_module_index + 1 ] ) ) . '">Next Page &rsaquo;</a>';
		}

		if ( $current_module_index < $total_pages - 2 && $showitems < $total_pages ) {
			echo '<a href="' . esc_url( get_permalink( end( $module_ids ) ) ) . '">Last Page &raquo;</a>';
		}
	}
	?>
</div>
