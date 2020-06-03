<?php
$current_term = openlab_get_current_filter( 'term' );
?>

<div class="custom-select">
	<label for="course-term-select" class="sr-only">Select Semester</label>
	<select name="term" class="last-select" id="course-term-select">
		<option value='' <?php selected( '', $current_term ) ?>>Select Semester</option>
		<option value='term_all' <?php selected( 'term_all', $current_term ) ?>><?php esc_html_e( 'All', 'openlab-theme' ); ?></option>
		<?php foreach ( openlab_get_active_semesters() as $term ) : ?>
			<option value="<?php echo esc_attr( $term['option_value'] ) ?>" <?php selected( $current_term, $term['option_value'] ) ?>><?php echo esc_attr( $term['option_label'] ) ?></option>
		<?php endforeach; ?>
	</select>
</div>
