<?php
$current_member_type = urldecode( openlab_get_current_filter( 'member_type' ) );

$type_slugs = [
	'student',
	'faculty',
	'staff',
	'alumni',
];

$member_types = [];
foreach ( $type_slugs as $type_slug ) {
	$member_type_obj = openlab_get_member_type_object( $type_slug );
	if ( $member_type_obj ) {
		$member_types[ $type_slug ] = $member_type_obj->name;
	}
}

?>

<div class="custom-select">
	<label for="portfolio-user-member-type-select" class="sr-only">Select Member Type</label>
	<select name="member_type" class="last-select" id="portfolio-user-member-type-select">
		<option value='' <?php selected( '', $current_member_type ) ?>>Select Member Type</option>
		<?php foreach ( $member_types as $member_type_slug => $member_type_label ) : ?>
			<option value='<?php echo esc_attr( $member_type_slug ); ?>' <?php selected( $current_member_type, $member_type_slug ) ?>><?php echo esc_html( $member_type_label ); ?></option>
		<?php endforeach; ?>
		<option value='all' <?php selected( 'all', $current_member_type ) ?>>All</option>
	</select>
</div>
