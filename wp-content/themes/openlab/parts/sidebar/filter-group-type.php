<?php
$current_group_types = openlab_get_current_filter( 'group-types' );

// Hardcoded here to ensure order.
$group_types = [
	[
		'slug' => 'course',
		'name' => 'Courses',
	],
	[
		'slug' => 'project',
		'name' => 'Projects',
	],
	[
		'slug' => 'club',
		'name' => 'Clubs',
	],
	[
		'slug' => 'portfolio',
		'name' => 'Portfolios',
	],
];

?>

<div class="sidebar-filter sidebar-filter-group-type">
	<div class="form-group">
		<?php foreach ( $group_types as $group_type ) : ?>
			<div class="sidebar-filter-checkbox">
				<label for="checkbox-group-type-<?php echo esc_attr( $group_type['slug'] ); ?>">
					<input type="checkbox" name="group-types[]" id="checkbox-group-type-<?php echo esc_attr( $group_type['slug'] ); ?>" <?php checked( in_array( $group_type['slug'], $current_group_types, true ) ); ?> value="<?php echo esc_attr( $group_type['slug'] ); ?>" /> <?php echo esc_html( $group_type['name'] ); ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
</div>
