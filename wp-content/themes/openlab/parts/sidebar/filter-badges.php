<?php

if ( ! defined( 'OLBADGES_VERSION' ) ) {
	return;
}

$badges = \OpenLab\Badges\Badge::get(
	[
		'hide_empty' => true,
	]
);
if ( ! $badges ) {
	return;
}

$current_badges = openlab_get_current_filter( 'badges' );

?>

<div class="sidebar-filter sidebar-filter-badges">
	<div class="form-group">
		<?php foreach ( $badges as $badge ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="badges[]" id="checkbox-badge-<?php echo esc_attr( $badge->get_id() ); ?>" <?php checked( in_array( $badge->get_id(), $current_badges, true ) ); ?> value="<?php echo esc_attr( $badge->get_id() ); ?>" /> <label for="checkbox-badge-<?php echo esc_attr( $badge->get_id() ); ?>"><?php echo esc_html( $badge->get_name() ); ?></label>
			</div>
		<?php endforeach; ?>
	</div>
</div>

