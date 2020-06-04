<?php
$current_member_type = urldecode( openlab_get_current_filter( 'member_type' ) );

$member_types = [
	'student' => 'Student',
	'faculty' => 'Faculty',
	'staff'   => 'Staff',
	'alumni'  => 'Alumni',
];

?>

<div class="custom-select">
	<label for="portfolio-user-member-type-select" class="sr-only">Select Member Type</label>
	<select name="member_type" class="last-select <?php echo $user_color; ?>-text" id="portfolio-user-member-type-select">
		<option value='' <?php selected( '', $current_member_type ) ?>>Select Member Type</option>
		<?php foreach ( $member_types as $member_type_slug => $member_type_label ) : ?>
			<option value='<?php echo esc_attr( $member_type_slug ); ?>' <?php selected( $current_member_type, $member_type_slug ) ?>><?php echo esc_html( $member_type_label ); ?></option>
		<?php endforeach; ?>
		<option value='all' <?php selected( 'all', $current_member_type ) ?>>All</option>
	</select>
</div>
