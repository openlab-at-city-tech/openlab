<?php
$post = get_post();
$added = (int) get_post_meta( $post->ID, 'portfolio_post_id', true );
$endpoits = [
	'post' => 'posts',
	'page' => 'pages',
];

// Prepare entry data.
$entry = [
	'id'        => (int) $post->ID,
	'type'      => isset( $endpoits[ $post->post_type ] ) ? $endpoits[ $post->post_type ]: 'posts',
	'date'      => get_the_date( '', $post ),
	'site_id'   => get_current_blog_id(),
	'site_name' => get_option( 'blogname' ),
];
?>

<?php if ( ! $added ) : ?>
	<span class="portfolio-actions">
		<button id="add-to-portfolio-<?php echo (int) $post->ID; ?>" class="add" data-entry="<?php echo esc_attr( wp_json_encode( $entry ) ); ?>">Add to my Portfolio</button>
	</span>
<?php else: ?>
	<span class="portfolio-actions">
		<button class="added" disabled>Added to my Portfolio</button>
	</span>
<?php endif; ?>
