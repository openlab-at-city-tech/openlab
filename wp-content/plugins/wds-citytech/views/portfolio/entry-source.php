<div class="entry-source-note">
	<?php include WDS_CITYTECH_DIR . '/views/portfolio/citation.php'; ?>

	<?php if ( ! empty( $data['annotation'] ) ) : ?>
		<div class="entry__annotation"><?php echo esc_html( $data['annotation'] ); ?></div>
	<?php endif; ?>
</div>
