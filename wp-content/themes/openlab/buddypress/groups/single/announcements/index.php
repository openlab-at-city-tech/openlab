<?php if ( openlab_user_can_post_announcements() ) : ?>
	<?php bp_get_template_part( 'groups/single/announcements/post-form' ); ?>
<?php endif; ?>

<?php
$announcements = get_posts(
	[
		'post_type'   => 'openlab_announcement',
		'post_status' => 'publish',
		'meta_query'  => [
			[
				'key'   => 'openlab_announcement_group_id',
				'value' => bp_get_current_group_id(),
			]
		],
		// @todo pagination
	]
);
?>

<?php if ( $announcements ) : ?>

	<div class="item-list announcement-list clearfix">
		<?php foreach ( $announcements as $announcement ) : ?>
			<?php bp_get_template_part( 'groups/single/announcements/entry', '', [ 'announcement_id' => $announcement->ID ] ); ?>
		<?php endforeach; ?>
	</div>

	<div id="pag-top" class="pagination clearfix">
		<div class="pagination-links" id="member-dir-pag-top">
			pagination links
		</div>

	</div>

<?php else: ?>

	<div class="item-list announcement-list clearfix"></div>

	<div id="no-announcement-message" class="info no-announcement-message">
		<p>This group has no announcements.</p>
	</div>

<?php endif; ?>

<script type="text/html" id="tmpl-openlab-announcement-edit-form">
	<div class="row announcement-form announcement-edit-form" id="edit-form-{{ data.editorId }}" data-announcement-id="{{ data.announcementId }}" data-reply-id="{{ data.replyId }}" data-editor-id="{{ data.editorId }}">
		<div id="quill-toolbar-edit-{{ data.editorId }}" class="quill-toolbar hide-if-no-js">
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

		<div class="announcement-textarea">
			<div class="announcement-rich-text-editor"></div>
		</div>

		<div class="announcement-options">
			<div class="announcement-submit-container">
				<button class="announcement-edit-submit btn btn-primary"><span class="button-text">Edit</span> <i class="fa fa-long-arrow-right"></i></button>

				<button class="edit-cancel">Cancel</button>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-openlab-announcement-reply-form">
	<div class="announcement-form announcement-reply-form" id="reply-form-{{ data.editorId }}" data-announcement-id="{{ data.announcementId }}" data-reply-id="{{ data.replyId }}" data-editor-id="{{ data.editorId }}">
		<div id="quill-toolbar-edit-{{ data.editorId }}" class="quill-toolbar hide-if-no-js">
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

		<div class="announcement-textarea">
			<div class="announcement-rich-text-editor"></div>
		</div>

		<div class="announcement-options">
			<div class="announcement-submit-container">
				<button class="announcement-reply-submit btn btn-primary"><span class="button-text">Reply</span> <i class="fa fa-long-arrow-right"></i></button>

				<button class="edit-cancel">Cancel</button>
			</div>
		</div>
	</div>
</script>
