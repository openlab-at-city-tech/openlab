<div class="announcement-item announcement-item-new" data-editor-id="new-announcement">
	<form action="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/post/' ); ?>" method="post" id="primary-announcement-form" name="announcement-form" class="announcement-form primary-announcement-form form-panel">
		<div class="panel panel-default">
			<div class="panel-heading">
				Create New Announcement
			</div>

			<div class="panel-body">
				<div class="announcement-title announcement-form-section">
					<label for="title-new-announcement">Title:</label>
					<input type="text" class="form-control announcement-title" name="announcement-title" id="title-new-announcement" />
				</div>

				<div class="announcement-textarea announcement-form-section">
					<label class="announcement-text-label screen-reader-text" for="announcement-text">Content:</label>
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
			</div>
		</div>
	</form>
</div>
