<?php if ( ! $data['added'] ) : ?>
	<span class="portfolio-actions">
		<button id="add-to-portfolio-<?php echo (int) $data['id']; ?>" class="add" data-entry="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">Add to my Portfolio</button>
	</span>
<?php else: ?>
	<span class="portfolio-actions">
		<button class="added" disabled>Added to my Portfolio</button>
	</span>
<?php endif; ?>
