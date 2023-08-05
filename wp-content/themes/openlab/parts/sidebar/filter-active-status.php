<?php
$is_active = openlab_get_current_filter( 'active-status' );

if ( ! in_array( $is_active, [ 'active', 'inactive', 'all' ], true ) ) {
	$is_active = '';
}

$group_type = openlab_get_group_directory_group_type();

?>

<div class="custom-select sidebar-filter sidebar-filter-active">
	<label for="active-status" class="sr-only">Active Status</label>
	<select name="active-status" class="last-select" id="active-status">
		<option value='' <?php selected( '', $is_active ) ?>>Active Status</option>
		<option value='active' <?php selected( 'active', $is_active ) ?>>Active</option>
		<option value='inactive' <?php selected( 'inactive', $is_active ) ?>>Not Active</option>
		<option value='all' <?php selected( 'all', $is_active ); ?>>All</option>
	</select>
</div>
