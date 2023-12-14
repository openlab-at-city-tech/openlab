<?php if ( ! empty( $description ) ) { ?>
	<p><?php print wp_kses( $description, \Imagely\NGG\Display\I18N::get_kses_allowed_html() ); ?></p>
<?php } ?>