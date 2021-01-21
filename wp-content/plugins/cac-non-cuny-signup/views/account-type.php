<ul class="children">
	<?php foreach ( $options as $option ) : ?>
		<li>
			<label class="selectit"><input type="radio" name="olsc_account_type" value="<?php echo esc_attr( $option->name ); ?>" <?php checked( $account_type, $option->name ); ?> /><?php echo esc_html( $option->name ); ?></label>
		</li>
	<?php endforeach; ?>
</ul>
