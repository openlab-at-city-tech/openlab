<form action="<?php echo esc_url( bp_get_group_permalink( groups_get_current_group() ) . 'announcements/post/' ); ?>" method="post" id="announcement-form" name="announcement-form" class="announcement-form">

	<?php do_action( 'bp_before_activity_post_form' ) ?>

	<div id="quill-toolbar" class="quill-toolbar hide-if-no-js">
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
				<button id="announcement-submit" type="submit" class="btn btn-primary">Post <i class="fa fa-long-arrow-right"></i></button>
			</div>

			<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id() ?>" />

		</div>
	</div>

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>

</form>
