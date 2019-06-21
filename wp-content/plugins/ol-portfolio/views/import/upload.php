<div class="wrap nosubsub">
	<h1>Import Portfolio</h1>
	<p>Upload Portfolio export .zip file.</p>

	<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( $this->get_url( 1 ) ); ?>">
		<input type="hidden" name="action" value="ol-import-upload" />
		<?php wp_nonce_field( 'import-upload' ); ?>

		<label class="screen-reader-text" for="importzip"><?php _e( 'Import zip file' ); ?></label>
		<input type="file" id="importzip" name="importzip" />

		<?php submit_button( 'Upload Archive', '', 'submit', false ); ?>
	</form>
</div>
