<?php

$the_unit_type      = get_query_var( 'academic_unit_type' );
$allowed_unit_types = [ 'school', 'office', 'department' ];

if ( ! in_array( $the_unit_type, $allowed_unit_types, true ) ) {
	return;
}

switch ( get_query_var( 'academic_unit_type' ) ) {
	case 'school' :
		$label     = 'Select School';
		$all_name  = 'All Schools';
		$url_param = 'school';

		$units_of_type = [];
		foreach ( openlab_get_school_list() as $school_slug => $school_name ) {
			$units_of_type[] = [
				'name'   => $school_name,
				'slug'   => $school_slug,
				'parent' => '',
			];
		}
	break;

	case 'office' :
		$label     = 'Select Office';
		$all_name  = 'All Offices';
		$url_param = 'office';

		$units_of_type = [];
		foreach ( openlab_get_office_list() as $office_slug => $office_name ) {
			$units_of_type[] = [
				'name'   => $office_name,
				'slug'   => $office_slug,
				'parent' => '',
			];
		}
	break;

	case 'department' :
	default :
		$label     = 'Select Department';
		$all_name  = 'All Departments';
		$url_param = 'department';

		$units_of_type = [];
		foreach ( openlab_get_entity_departments() as $parent => $depts ) {
			foreach ( $depts as $dept_slug => $dept_data ) {
				$units_of_type[] = [
					'name'   => $dept_data['label'],
					'slug'   => $dept_slug,
					'parent' => $parent,
				];
			}
		}
	break;
}

$current_unit = isset( $_GET[ $url_param ] ) ? wp_unslash( $_GET[ $url_param ] ) : 'all';
if ( 'school' === $url_param && 'all' !== $current_unit ) {
	$schools_and_offices = array_merge( openlab_get_school_list(), openlab_get_office_list() );

	// @todo departments whitelist
	if ( $current_unit && ! isset( $schools_and_offices[ $current_unit ] ) ) {
		$current_unit = '';
	}
}

?>

<div class="sidebar-filter custom-select academic-unit-type-select" id="academic-unit-type-select-<?php echo esc_attr( $url_param ); ?>">
	<label for="<?php echo esc_attr( $url_param ); ?>-select" class="sr-only"><?php echo esc_html( $label ); ?></label>
	<select name="<?php echo esc_attr( $url_param ); ?>" class="last-select" id="<?php echo esc_attr( $url_param ); ?>-select" data-unittype="<?php echo esc_attr( $url_param ); ?>">

		<option class="academic-unit" value="" data-academic-unit-type="<?php echo esc_attr( $url_param ); ?>" data-parent="" <?php selected( '', $current_unit ) ?>><?php echo esc_html( $label ); ?></option>
		<option class="academic-unit" value="all" data-parent="" data-academic-unit-type="<?php echo esc_attr( $url_param ); ?>" <?php selected( 'all', $current_unit ) ?>><?php echo esc_html( $all_name ); ?></option>

		<?php foreach ( $units_of_type as $unit ) : ?>
			<option class="academic-unit academic-unit-nonempty" data-academic-unit-type="<?php echo esc_attr( $url_param ); ?>" data-parent="<?php echo esc_html( $unit['parent'] ); ?>" value='<?php echo esc_attr( $unit['slug'] ); ?>' <?php selected( $unit['slug'], $current_unit ) ?>><?php echo esc_html( $unit['name'] ); ?></option>
		<?php endforeach; ?>

	</select>
</div><!-- #academic-unit-type-select-<?php echo esc_html( $url_param ); ?> -->
