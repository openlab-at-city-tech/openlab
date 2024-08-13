<?php

/**
 * Recursively render checkboxes for academic units.
 *
 * @since 1.6.0
 *
 * @param CBOX\OL\AcademicUnit   $unit    Academic unit object.
 * @param CBOX\OL\AcademicUnit[] $units   List of academic unit objects.
 * @param int                    $depth   Depth of the current unit.
 * @param int                    $post_id Post ID.
 * @param CBOX\OL\AcademicUnit[] $selected_academic_units Selected academic units.
 * @return string HTML for the checkboxes.
 */
function cboxol_render_checkbox( $unit, $units, $depth, $post_id, $selected_academic_units = [] ) {
	$html = '';

	$types            = cboxol_get_academic_unit_types();
	$unit_type        = $unit->get_type();
	$unit_type_object = $types[ $unit_type ];
	$unit_type_parent = $unit_type_object->get_parent();

	$checked_ids = array_map(
		function( $academic_unit ) {
			return $academic_unit->get_wp_post_id();
		},
		$selected_academic_units
	);

	$html .= sprintf(
		'<label>%s<input type="checkbox" class="checkbox-depth-%s" data-slug="%s" data-parent="%s" name="template-visibility-limit-to-academic-unit[]" value="%s" %s /> %s</label><br />',
		str_repeat( '&nbsp;', $depth * 6 ),
		esc_attr( $depth ),
		esc_attr( $unit->get_type() . '-' . $unit->get_slug() ),
		esc_attr( $unit_type_parent . '-' . $unit->get_parent() ),
		esc_attr( $unit->get_wp_post_id() ),
		checked( in_array( $unit->get_wp_post_id(), $checked_ids, true ), true, false ),
		esc_html( $unit->get_name() )
	);

	$children = array_filter(
		$units,
		function( $child ) use ( $unit ) {
			return $child->get_parent() === $unit->get_slug();
		}
	);

	foreach ( $children as $child ) {
		$html .= cboxol_render_checkbox( $child, $units, $depth + 1, $post_id, $selected_academic_units );
	}

	return $html;
}

$all_member_types = cboxol_get_member_types();

$academic_unit_types = array_filter(
	cboxol_get_academic_unit_types(),
	function( $type ) {
		$group_types = $type->get_group_types();
		return ! empty( $group_types );
	}
);

$units = [];
foreach ( $academic_unit_types as $academic_unit_type ) {
	$units = array_merge(
		$units,
		cboxol_get_academic_units(
			[
				'type' => $academic_unit_type->get_slug(),
			]
		)
	);
}

$academic_unit_checkbox_html = '';
foreach ( $academic_unit_types as $academic_unit_type ) {
	$parent = $academic_unit_type->get_parent();
	if ( $parent ) {
		continue;
	}

	$academic_unit_checkbox_html .= sprintf(
		'<h3>%s</h3>',
		esc_html( $academic_unit_type->get_name() )
	);

	foreach ( $units as $unit ) {
		$unit_type = $unit->get_type();
		if ( $unit_type !== $academic_unit_type->get_slug() ) {
			continue;
		}

		$parent = $unit->get_parent();
		if ( $parent ) {
			continue;
		}

		$academic_unit_checkbox_html .= cboxol_render_checkbox( $unit, $units, 0, get_the_ID(), $selected_academic_units );
	}
}

?>

<p><?php esc_html_e( 'Control who can select this template when creating a new site.', 'commons-in-a-box' ); ?></p>

<div class="cboxol-site-template-visibility-section">
	<fieldset class="template-visibility-radios">
		<legend><?php esc_html_e( 'By Member Type', 'commons-in-a-box' ); ?></legend>
		<label><input type="radio" name="template-visibility-limit-by-member-type" id="template-visibility-limit-by-member-type-yes" value="yes" aria-controls="template-visibility-suboptions-member-type" <?php checked( $limit_by_member_types ); ?> /> <?php esc_html_e( 'Restrict by member type', 'commons-in-a-box' ); ?></label><br />
		<fieldset class="template-visibility-suboptions" id="template-visibility-suboptions-member-type">
			<legend><?php esc_html_e( 'This template will be available only to users belonging to the member types selected below:', 'commons-in-a-box' ); ?></legend>
			<?php foreach ( $all_member_types as $member_type ) : ?>
				<label><input type="checkbox" class="template-visibility-limit-to-member-types" name="template-visibility-limit-to-member-types[]" value="<?php echo esc_attr( $member_type->get_slug() ); ?>" <?php checked( isset( $selected_member_types[ $member_type->get_slug() ] ) ); ?> /> <?php echo esc_html( $member_type->get_name() ); ?></label><br />
			<?php endforeach; ?>

			<div class="template-visibility-limit-to-member-types-message">
				<?php
				printf(
					/* translators: 1: group type name, 2: member type name */
					esc_html__( 'Note: This template is limited to groups of type %1$s, which can be created only by members of type %2$s. Other options have been disabled.', 'commons-in-a-box' ),
					'<span class="group-type-names">' . esc_html( cboxol_get_course_group_type()->get_name() ) . '</span>',
					'<span class="member-type-names"></span>'
				);
				?>
			</div>
		</fieldset>

		<label><input type="radio" name="template-visibility-limit-by-member-type" id="template-visibility-limit-by-member-type-no" value="no" aria-controls="template-visibility-suboptions-member-type" <?php checked( ! $limit_by_member_types ); ?> /> <?php esc_html_e( 'Allow for all member types', 'commons-in-a-box' ); ?></label>
	</fieldset>
</div>

<div class="cboxol-site-template-visibility-section">
	<fieldset class="template-visibility-radios">
		<legend><?php esc_html_e( 'By Academic Unit', 'commons-in-a-box' ); ?></legend>
		<label><input type="radio" name="template-visibility-limit-by-academic-unit" id="template-visibility-limit-by-academic-unit-yes" value="yes" aria-controls="template-visibility-suboptions-academic-unit" <?php checked( $limit_by_academic_units ); ?> /> <?php esc_html_e( 'Restrict by academic unit', 'commons-in-a-box' ); ?></label><br />
		<fieldset class="template-visibility-suboptions" id="template-visibility-suboptions-academic-unit">
			<legend><?php esc_html_e( 'This template will be available only to groups associated with one or more of the academic units selected below:', 'commons-in-a-box' ); ?></legend>
			<div class="cboxol-academic-unit-visibility-selector">
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $academic_unit_checkbox_html; ?>
			</div>
		</fieldset>

		<label><input type="radio" name="template-visibility-limit-by-academic-unit" id="template-visibility-limit-by-academic-unit-no" value="no" aria-controls="template-visibility-suboptions-academic-unit" <?php checked( ! $limit_by_academic_units ); ?> /> <?php esc_html_e( 'Allow for groups associated with any academic unit', 'commons-in-a-box' ); ?></label>
	</fieldset>
</div>

<?php wp_nonce_field( 'cboxol-template-visibility', 'cboxol-template-visibility-nonce', false ); ?>
