<div class="entry-source-note">
	<div class="entry__citation"><?php echo wp_kses_post( $data['citation'] ); ?></div>

	<?php if ( ! empty( $data['annotation'] ) ) : ?>
		<div class="entry__annotation show-more"><?php echo esc_html( $data['annotation'] ); ?></div>
	<?php endif; ?>
</div>
