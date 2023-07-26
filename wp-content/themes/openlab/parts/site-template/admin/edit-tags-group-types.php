<div class="form-field term-description-wrap">
	<label for="tag-description"><?php esc_html_e( 'Group Types', 'cboxol-site-template-picker' ); ?></label>

	<ul>
		<?php foreach ( $group_types as $group_type ) : ?>
			<li><label for="group-types-<?php echo esc_attr( $group_type['value'] ); ?>"><input type="checkbox" name="group-types[]" value="<?php echo esc_attr( $group_type['value'] ); ?>" id="group-types-<?php echo esc_attr( $group_type['value'] ); ?>" /> <?php echo esc_html( $group_type['label'] ); ?></label></li>
		<?php endforeach; ?>
	</ul>

	<p><?php esc_html_e( 'When creating a group, users will be able to select from categories that are associated with the Group Type to which the new group belongs.', 'cboxol-site-template-picker' ); ?></p>

	<?php wp_nonce_field( 'cboxol_edit_term', 'cboxol-edit-term-nonce' ); ?>
</div>
