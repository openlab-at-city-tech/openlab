<div class="wrap">
	<h2>OpenLab Attributions Settings</h2>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php settings_fields( 'ol_attribution_settings' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="title">Edit Footer Title</label>
					</th>
					<td>
						<input name="ol_attribution_settings[title]" type="text" id="title" class="regular-text" value="<?php echo esc_attr( $settings['title'] ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="description">Edit Description</label>
					</th>
					<td>
						<textarea name="ol_attribution_settings[description]" rows="10" id="description" class="regular-text"><?php echo esc_textarea( $settings['description'] ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">Bibliography</th>
					<td>
						<fieldset>
							<label>
								<input name="ol_attribution_settings[bibliography]" type="radio" value="page" <?php checked( $settings['bibliography'], 'page' ); ?> />
								Page
							</label>
							<br>
							<label>
								<input name="ol_attribution_settings[bibliography]" type="radio" value="post" <?php checked( $settings['bibliography'], 'post' ); ?> />
								Post
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
