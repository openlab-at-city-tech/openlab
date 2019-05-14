<?php
// Prepare entry data.
$comment = get_comment();
$added = (int) get_comment_meta( $comment->comment_ID, 'portfolio_post_id', true );
$entry = [
	'id'        => (int) $comment->comment_ID,
	'type'      => 'comments',
	'date'      => get_comment_date( '', $comment ),
	'site_id'   => get_current_blog_id(),
	'site_name' => get_option( 'blogname' ),
];
?>
<?php if ( ! $added ) : ?>
<span class="portfolio-actions">
	<button id="add-to-portfolio-<?php echo (int) $comment->comment_ID; ?>" class="add" data-entry="<?php echo esc_attr( wp_json_encode( $entry ) ); ?>">Add to Portfolio</button>
</span>
<?php else: ?>
	<span class="portfolio-actions">
		<button class="added" disabled>Added to my Portfolio</button>
	</span>
<?php endif; ?>
