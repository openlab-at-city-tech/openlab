<?php if ( ! $data['added'] ) : ?>
	<span class="portfolio-actions">
		<button id="add-to-portfolio-<?php echo (int) $data['id']; ?>" class="add" data-entry="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">Add to my Portfolio</button>
	</span>
<?php else: ?>
	<span class="portfolio-actions">
		<a class="button" href="<?php echo esc_url( $data['edit_link'] ); ?>">Added to my Portfolio</a>
	</span>
<?php endif; ?>
