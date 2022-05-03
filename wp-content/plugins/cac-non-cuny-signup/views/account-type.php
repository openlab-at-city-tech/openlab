<ul class="children">
	<?php foreach ( $options as $option ) : ?>
		<li>
			<?php /* We store names rather than slugs, for backward compatibility */ ?>
			<label class="selectit"><input type="radio" name="olsc_account_type" value="<?php echo esc_attr( $option->name ); ?>" <?php checked( $account_type, $option->name ); ?> /><?php echo esc_html( $option->name ); ?></label>
		</li>
	<?php endforeach; ?>
</ul>
