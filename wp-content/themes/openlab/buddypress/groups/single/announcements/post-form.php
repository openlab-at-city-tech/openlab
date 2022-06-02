<div class="announcement-item announcement-item-new" data-editor-id="new-announcement">
	<form action="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/post/' ); ?>" method="post" id="primary-announcement-form" name="announcement-form" class="announcement-form primary-announcement-form">

		<div class="announcement-title announcement-form-section">
			<label for="announcement-title">Title:</label>
			<input type="text" class="form-control" name="announcement-title" id="announcement-title" />
		</div>

		<div class="announcement-textarea announcement-form-section">
			<label class="announcement-text-label" for="announcement-text">Content:</label>
			<textarea name="announcement-text" id="announcement-text" cols="50" rows="10"></textarea>
			<div class="announcement-editor-wrapper">
				<div id="new-announcement-rich-text-editor" class="announcement-rich-text-editor hide-if-no-js"></div>

				<div class="announcement-options">
					<div class="announcement-submit-container">
						<button id="announcement-submit" type="submit" class="btn btn-primary"><span class="button-text">Post</span> <i class="fa fa-long-arrow-right"></i></button>
					</div>

					<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id() ?>" />

				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>

	</form>
</div>
