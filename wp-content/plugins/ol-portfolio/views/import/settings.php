<div class="wrap nosubsub">
	<h1>Import Portfolio</h1>
	<p>Your import is almost ready to go.</p>

	<form method="post" action="<?php echo esc_url( $this->get_url( 2 ) ); ?>">
		<input type="hidden" name="import_id" value="<?php echo esc_attr( $this->id ) ?>" />
		<?php wp_nonce_field( sprintf( 'portfolio.import:%d', $this->id ) ) ?>
		<?php submit_button( 'Start Importing' ); ?>
	</form>
</div>
