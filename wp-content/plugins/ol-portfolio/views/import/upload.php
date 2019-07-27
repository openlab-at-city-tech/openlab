<div class="wrap nosubsub import-page">
	<?php $this->render_header(); ?>

	<p><strong>Step 1: Choose and upload your Portfolio Archive file (.zip).</strong></p>

	<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( $this->get_url(1) ); ?>">
		<input type="hidden" name="action" value="ol-import-upload" />
		<?php wp_nonce_field( 'import-upload' ); ?>

		<label class="screen-reader-text" for="importzip">Import zip file</label>
		<input type="file" id="importzip" name="importzip" />

		<?php submit_button( 'Upload Archive File' ); ?>
	</form>
</div>
