<?php
$option_value = openlab_get_current_filter( 'sort' );
?>
<div class="custom-select">
	<label for="sequence-select" class="sr-only"><?php echo esc_html_e( 'Select: Order', 'openlab-theme' ); ?></label>
	<select name="sort" class="last-select" id="sequence-select">
		<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'><?php esc_html_e( 'Alphabetical', 'openlab-theme' ); ?></option>
		<option <?php selected( $option_value, 'newest' ) ?>  value='newest'><?php esc_html_e( 'Newest', 'openlab-theme' ); ?></option>
		<option <?php selected( $option_value, 'active' ) ?> value='active'><?php esc_html_e( 'Last Active', 'openlab-theme' ); ?></option>
	</select>
</div>
