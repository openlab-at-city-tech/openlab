<div class="wrap nosubsub import-page">
	<?php $this->render_header(); ?>

	<p><strong>Step 1: Choose and upload your Portfolio Archive file (.zip).</strong></p>
	<p><strong class="error"><?php echo $error->get_error_message(); ?></strong></p>

	<p><?php printf( '<a class="button button-primary" href="%s">Try Again</a>', esc_url( $this->get_url( 0 ) ) ); ?></p>
</div>
