<tr>
	<td class="column1">
		<label for="watermark_image_url"
				title="<?php echo esc_attr( $watermark_image_text ); ?>"
				class="tooltip">
			<?php echo esc_html( $image_url_label ); ?>
		</label>
	</td>
	<td>
		<input
			type="text"
			name="watermark_options[wmPath]"
			id="watermark_image_url"
			value="<?php echo esc_attr( $watermark_image_url ); ?>"
		/>
	</td>
</tr>