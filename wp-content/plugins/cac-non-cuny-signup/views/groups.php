<input type="text" id="cac_ncs_groups" name="cac_ncs_groups" />

<?php if ( ! empty( $groups ) ) : ?>
	<ul class="cac-ncs-groups-results">
		<?php foreach ( $groups as $group ) : ?>
			<li id="cac-nsc-add-to-group-<?php echo $group->id; ?>">
				<?php echo esc_html( $group->name ) ?>
				<span class="cac-nsc-remove-group"><a href="#">x</a></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php wp_nonce_field( 'openlab_signup_codes', 'openlab_signup_codes_nonce' ); ?>
<input type="hidden" id="cac_nsc_group_ids" name="cac_ncs_group_ids" value="<?php echo implode( ',', $group_ids ); ?>" />
