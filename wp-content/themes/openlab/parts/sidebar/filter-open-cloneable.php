<?php
/*
 * Combined because they're on a single line in the interface.
 */

$is_open      = openlab_get_current_filter( 'open' );
$is_cloneable = openlab_get_current_filter( 'cloneable' );

$group_type = openlab_get_group_directory_group_type();

// Only show Open on group directories.
// @todo search results
$show_open = ! empty( $group_type );

// Cloneable should not appear if the content type is not cloneable.
// @todo search results
$show_cloneable = $group_type && 'portfolio' !== $group_type;

// Don't render the element if there's nothing to show.
if ( ! $show_cloneable && ! $show_open ) {
	return;
}

?>

<div class="sidebar-filter sidebar-filter-open-cloneable">
	<div class="form-group">
		<?php if ( $show_open ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="is_open" id="checkbox-is-open" <?php checked( $is_open ); ?> value="1" /> <label for="checkbox-is-open"><?php esc_html_e( 'Open', 'openlab-theme' ); ?></label>
			</div>
		<?php endif; ?>

		<?php if ( $show_cloneable ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="is_cloneable" id="checkbox-is-cloneable" <?php checked( $is_cloneable ); ?> value="1" /> <label for="checkbox-is-cloneable"><?php esc_html_e( 'Cloneable', 'openlab-theme' ); ?></label>
			</div>
		<?php endif; ?>
	</div>
</div>
