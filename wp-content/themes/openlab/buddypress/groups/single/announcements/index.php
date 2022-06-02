<?php if ( openlab_user_can_post_announcements() ) : ?>
	<?php bp_get_template_part( 'groups/single/announcements/post-form' ); ?>
<?php endif; ?>

<?php

$per_page = 3;
$paged    = isset( $_GET['apage'] ) ? (int) $_GET['apage'] : 1;

$announcement_query = new WP_Query(
	[
		'post_type'      => 'openlab_announcement',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $paged,
		'meta_query'     => [
			[
				'key'   => 'openlab_announcement_group_id',
				'value' => bp_get_current_group_id(),
			]
		],
	]
);

$pagination = paginate_links(
	[
		'base'               => add_query_arg( [ 'apage' => '%#%' ] ),
		'format'             => '',
		'total'              => ceil( $announcement_query->found_posts / (int) $per_page ),
		'current'            => $paged,
		'prev_text'          => '<i class="fa fa-angle-left" aria-hidden="true"></i><span class="sr-only">Previous</span>',
		'next_text'          => '<i class="fa fa-angle-right" aria-hidden="true"></i><span class="sr-only">Next</span>',
		'mid_size'           => 3,
		'type'               => 'list',
		'before_page_number' => '<span class="sr-only">Page</span>',
	]
);

$pagination = str_replace( 'page-numbers', 'page-numbers pagination', $pagination );

//for screen reader only text - current page
$pagination = str_replace( 'current\'><span class="sr-only">Page', 'current\'><span class="sr-only">Current Page', $pagination );

?>

<?php do_action( 'template_notices' ); ?>

<?php if ( $announcement_query->posts ) : ?>

	<div class="item-list announcement-list clearfix">
		<?php foreach ( $announcement_query->posts as $announcement ) : ?>
			<?php bp_get_template_part( 'groups/single/announcements/entry', '', [ 'announcement_id' => $announcement->ID ] ); ?>
		<?php endforeach; ?>
	</div>

	<div class="pagination-links">
		<?php echo $pagination; ?>
	</div>

<?php else: ?>

	<div class="item-list announcement-list clearfix"></div>

	<div id="no-announcement-message" class="info no-announcement-message">
		<p>This group has no announcements.</p>
	</div>

<?php endif; ?>

<script type="text/html" id="tmpl-openlab-announcement-edit-form">
	<div class="row announcement-form announcement-edit-form" id="edit-form-{{ data.editorId }}" data-announcement-id="{{ data.announcementId }}" data-reply-id="{{ data.replyId }}" data-editor-id="{{ data.editorId }}">
		<div class="announcement-title announcement-form-section">
			<label for="title-{{ data.editorId }}">Title:</label>
			<input type="text" class="form-control announcement-title" id="title-{{ data.editorId }}" value="{{ data.title }}" />
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

<script type="text/html" id="tmpl-openlab-announcement-reply-edit-form">
	<div class="row announcement-form announcement-edit-form" id="edit-form-{{ data.editorId }}" data-announcement-id="{{ data.announcementId }}" data-reply-id="{{ data.replyId }}" data-editor-id="{{ data.editorId }}">
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

		<div class="announcement-textarea announcement-form-section">
			<div class="announcement-editor-wrapper">
				<div class="announcement-rich-text-editor"></div>

				<div class="announcement-options">
					<div class="announcement-submit-container">
						<button class="announcement-reply-submit btn btn-primary"><span class="button-text">Reply</span> <i class="fa fa-long-arrow-right"></i></button>

						<button class="edit-cancel">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-openlab-announcement-quill-toolbar">
	<div id="{{ data.toolbarId }}" class="quill-toolbar hide-if-no-js">
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
</script>
