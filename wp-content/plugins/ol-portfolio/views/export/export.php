<div class="wrap nosubsub">
	<h1>Export Portfolio</h1>

	<?php settings_errors(); ?>

	<p>Use this tool to export your OpenLab Portfolio.</p>
	<p>A Portfolio Archive file (.zip) will be downloaded to your computer and can be used with Import Portfolio tool.</p>

	<form method="post" id="export-portfolio" action="<?php echo admin_url( 'admin-post.php' ); ?>">
		<input type="hidden" name="action" value="export-portfolio" />
		<?php wp_nonce_field( 'ol-export-portfolio' ); ?>
		<?php submit_button( 'Download Archive File' ); ?>
	</form>
</div>
