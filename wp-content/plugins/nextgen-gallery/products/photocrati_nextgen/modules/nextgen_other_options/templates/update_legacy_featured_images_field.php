<?php
/**
 * @var array $i18n
 * @var string $nonce
 */
?>
<tr>
	<td class='column1'>
		<label class="tooltip"
				for="ngg_update_legacy_featured_images_button"
				title="<?php print $i18n['tooltip']; ?>">
			<?php print $i18n['label']; ?>
		</label>
	</td>
	<td>
		<input type='submit'
				name="update_legacy_featured_images"
				id="ngg_update_legacy_featured_images_button"
				class="button delete button-primary"
				value='<?php print $i18n['label']; ?>'
		/>
	</td>
	<script>
		window.addEventListener('load', function() {
			/** @var {Object} photocrati_ajax */
			let url_base = photocrati_ajax.url;
			url_base += '&nonce=<?php print esc_attr( $nonce ); ?>';
			url_base += '&action=';

			const messages = {
				header: '<?php print esc_js( $i18n['header'] ); ?>',
				no_images_found: '<?php print esc_js( $i18n['no_images_found'] ); ?>',
				operation_finished: '<?php print esc_js( $i18n['operation_finished'] ); ?>'
			};

			nggProgressBarManager.initialize(
				'ngg_update_legacy_featured_images_button',
				messages,
				url_base + 'get_legacy_featured_images_count',
				url_base + 'update_legacy_featured_images'
			);
		});
	</script>
</tr>