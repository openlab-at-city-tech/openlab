<?php if ( ! empty( $description ) ) { ?>
	<p>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses() with allowed HTML tags provides safe output
	print wp_kses( $description, \Imagely\NGG\Display\I18N::get_kses_allowed_html() );
	?>
	</p>
<?php } ?>