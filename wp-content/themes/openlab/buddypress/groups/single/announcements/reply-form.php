<?php
$parent_id       = $args['parent_id'];
$announcement_id = $args['announcement_id'];

$editor_id = $parent_id ? 'reply-' . $parent_id : 'announcement-' . $announcement_id;

$group_id = (int) get_post_meta( $announcement_id, 'openlab_announcement_group_id', true );
?>

<form action="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/post/' ); ?>" method="post" id="announcement-form-<?php echo esc_attr( $editor_id ); ?>" class="announcement-form announcement-reply-form">

	<div id="quill-toolbar-<?php echo esc_attr( $editor_id ); ?>" class="quill-toolbar hide-if-no-js">
	  <div class="quill-toolbar-buttons">
		  <button class="ql-bold"></button>
		  <button class="ql-italic"></button>
		  <button class="ql-underline"></button>
		  <button class="ql-link"></button>

		  <button class="ql-list" value="ordered"></button>
		  <button class="ql-list" value="bullet"></button>
	  </div>

	  <div class="quill-toolbar-avatar">
		<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php bp_loggedin_user_avatar( 'width=40&height=40' ) ?>
		</a>
	  </div>
	</div>

	<div class="announcement-content">
		<div class="announcement-textarea">
			<textarea name="announcement-text" id="announcement-text" cols="50" rows="10"></textarea>
		</div>

		<div class="announcement-options">
			<div class="announcement-submit-container">
				<span class="ajax-loader"></span> &nbsp;
				<button type="submit" class="reply-submit btn btn-primary"><span class="button-text">Reply</span> <i class="fa fa-long-arrow-right"></i></button>
			</div>

			<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php echo esc_attr( $group_id ); ?>" />
		</div>
	</div>

	<?php wp_nonce_field( 'announcement_reply', 'announcement-reply-nonce-' . $editor_id ); ?>

</form>
