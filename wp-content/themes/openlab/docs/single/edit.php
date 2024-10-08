<?php

wp_enqueue_script( 'bp-docs-edit' );

$doc_id = 0;
$current_doc = bp_docs_get_current_doc();
if ( $current_doc ) {
	$doc_id = $current_doc->ID;
}

$group_type_label = '';
$group            = null;
if ( bp_is_group() ) {
	$group = groups_get_current_group();

	$group_type_label = openlab_get_group_type_label(
		[
			'group_id' => bp_get_current_group_id(),
			'case'     => 'upper',
		]
	);
}
?>

<div class="<?php bp_docs_container_class(); ?>">
	<?php include( bp_docs_locate_template( 'single/sidebar.php' ) ) ?>

	<?php include( apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ) ) ?>

	<?php
	// No media support at the moment. Want to integrate with something like BP Group Documents
	// include_once ABSPATH . '/wp-admin/includes/media.php' ;

	if ( ! function_exists( 'wp_editor' ) ) {
		require_once ABSPATH . '/wp-admin/includes/post.php';
		wp_tiny_mce();
	}
	?>

	<div class="doc-content img-rounded edit-doc">

		<form action="" method="post" class="form-group form-panel" id="doc-form">

			<div class="panel panel-default">
				<div class="panel-heading"><?php echo ( $current_doc ) ? 'Edit Doc' : 'New Doc'; ?></div>
				<div class="panel-body">

					<?php do_action('template_notices') ?>

					<div id="idle-warning" style="display:none">
						<p><?php _e('You have been idle for <span id="idle-warning-time"></span>', 'bp-docs') ?></p>
					</div>

					<div class="doc-header">
						<?php if ( bp_docs_is_existing_doc() ) : ?>
							<input type="hidden" id="existing-doc-id" value="<?php echo esc_attr( $doc_id ); ?>" />
						<?php endif ?>
					</div>
					<div class="doc-content-wrapper">
						<div id="doc-content-title">
							<label for="doc-title">Title</label>
							<input type="text" id="doc-title" name="doc[title]" class="form-control" value="<?php bp_docs_edit_doc_title() ?>" />
						</div>

						<?php if ( bp_docs_is_existing_doc() ) : ?>
							<div id="doc-content-permalink">
								<label for="doc-permalink">Permalink</label>
								<code><?php echo trailingslashit( bp_get_group_permalink() ) . BP_DOCS_SLUG . '/' ?></code><input type="text" id="doc-permalink" name="doc[permalink]" class="long" value="<?php bp_docs_edit_doc_slug() ?>" />
							</div>
						<?php endif ?>

						<div id="doc-content-textarea">
							<label id="content-label" for="doc_content">Content</label>
							<div id="editor-toolbar">
								<?php
								$wp_editor_args = apply_filters( 'bp_docs_wp_editor_args', array(
									'media_buttons' => false,
									'dfw'		    => false,
								) );
								wp_editor( bp_docs_get_edit_doc_content(), 'doc_content', $wp_editor_args );
								?>
							</div>
						</div>

						<div id="doc-meta">
							<?php do_action( 'bp_docs_opening_meta_box', $doc_id ) ?>

							<?php if ( current_user_can( 'bp_docs_manage', $doc_id ) ) : ?>
								<div id="doc-privacy" class="doc-meta-box">
									<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'privacy-meta-box' ) ?>">
										<p id="privacy-toggle-edit" class="toggle-switch">
											<span class="hide-if-js toggle-link-no-js">Tags</span>
											<a class="hide-if-no-js toggle-link" id="privacy-toggle-link" href="#">Doc and Comment Settings</a>
										</p>

										<div class="toggle-content">
											<div class="desc-column">
												<fieldset>
													<legend>Allow comments on this Doc?</legend>
													<label><input type="radio" name="doc[allow_comments]" value="1" <?php checked( openlab_comments_allowed_on_doc( $doc_id ) ) ?> /> Yes</label><br />
													<label><input type="radio" name="doc[allow_comments]" value="0" <?php checked( ! openlab_comments_allowed_on_doc( $doc_id ) ) ?> /> No</label>
												</fieldset>
											</div>

											<div class="desc-column">
												<fieldset>
													<legend>Who can view this Doc and its comments?</legend>
													<?php if ( $group && 'public' === $group->status ) : ?>
														<label><input type="radio" name="doc[view_setting]" value="everyone" <?php checked( openlab_get_doc_view_setting( $doc_id ), 'everyone' ) ?> /> Everyone</label><br />
													<?php endif; ?>

													<label><input type="radio" name="doc[view_setting]" value="group-members" <?php checked( openlab_get_doc_view_setting( $doc_id ), 'group-members' ) ?> /> <?php echo esc_html( $group_type_label ); ?> members only</label><br />
													<label><input type="radio" name="doc[view_setting]" value="admins" <?php checked( openlab_get_doc_view_setting( $doc_id ), 'admins' ) ?> /> <?php echo esc_html( $group_type_label ); ?> admins and Doc creator only</label><br />

												</fieldset>
											</div>

											<div class="desc-column">
												<fieldset id="doc-edit-settings">
													<legend>Who can edit this Doc?</legend>
													<label><input type="radio" name="doc[edit_setting]" id="doc-edit-setting-group-members" value="group-members" <?php checked( openlab_get_doc_edit_setting( $doc_id ), 'group-members' ) ?> /> <?php echo esc_html( $group_type_label ); ?> members only</label><br />
													<label><input type="radio" name="doc[edit_setting]" id="doc-edit-setting-admins" value="admins" <?php checked( openlab_get_doc_edit_setting( $doc_id ), 'admins' ) ?> /> <?php echo esc_html( $group_type_label ); ?> admins and Doc creator only</label><br />

												</fieldset>
											</div>
										</div>
									</div>
								</div>

								<?php wp_nonce_field( 'bp-docs-save-doc-privacy', 'bp-docs-save-doc-privacy-nonce', false ); ?>
							<?php endif; ?>

							<div id="doc-tax" class="doc-meta-box">
								<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'tags-meta-box' ) ?>">
									<p id="tags-toggle-edit" class="toggle-switch">
										<span class="hide-if-js toggle-link-no-js">Tags</span>
										<a class="hide-if-no-js toggle-link" id="tags-toggle-link" href="#">Tags</a>
									</p>

									<div class="toggle-content">
										<div class="desc-column">
											<p class="doc-meta-label"><?php _e('Tags are words or phrases that help to describe and organize your Docs.', 'bp-docs') ?></p>
											<p class="description doc-meta-desc"><?php _e('Separate tags with commas (for example: <em>orchestra, snare drum, piccolo, Brahms</em>)', 'bp-docs') ?></p>
										</div>
										<?php bp_docs_post_tags_meta_box() ?>
									</div>
								</div>
							</div>

							<div id="doc-parent" class="doc-meta-box">
								<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'parent-meta-box' ) ?>">
									<p id="parent-toggle-edit" class="toggle-switch">
										<span class="hide-if-js toggle-link-no-js">Parent</span>
										<a class="hide-if-no-js toggle-link" id="parent-toggle-link" href="#">Parent</a>
									</p>

									<div class="toggle-content">
										<div class="desc-column">
											<p class="doc-meta-label"><?php _e('Select a parent for this Doc. <em>(Optional)</em>', 'bp-docs') ?></p>
											<p class="description doc-meta-desc"><?php _e('Assigning a parent Doc means that a link to the parent will appear at the bottom of this Doc, and a link to this Doc will appear at the bottom of the parent.', 'bp-docs') ?></span>
										</div>
										<?php bp_docs_edit_parent_dropdown() ?>
									</div>
								</div>
							</div>

							<?php if ( current_user_can( 'bp_docs_manage' ) && apply_filters( 'bp_docs_allow_access_settings', true ) ) : ?>
								<div id="doc-settings" class="doc-meta-box">
									<div class="toggleable <?php bp_docs_toggleable_open_or_closed_class( 'parent-meta-box' ) ?>">
										<p id="settings-toggle-edit" class="toggle-switch">
											<span class="hide-if-js toggle-link-no-js">Settings</span>
											<a class="hide-if-no-js toggle-link" id="settings-toggle-link" href="#">Settings</a>
										</p>

										<div class="toggle-content">
											<table class="toggle-table" id="toggle-table-settings">
												<?php bp_docs_doc_settings_markup() ?>
											</table>
										</div>
									</div>
								</div>
							<?php endif ?>
						</div>

						<div style="clear: both"> </div>

						<div class="notify-group-members-ui">
							<?php openlab_notify_group_members_ui( ! bp_docs_is_existing_doc() ); ?>
						</div>

						<div id="doc-submit-options">

							<?php wp_nonce_field('bp_docs_save') ?>

							<?php /* Very important! Saving existing docs will not work without this */ ?>
							<input type="hidden" id="doc_id" name="doc_id" value="<?php echo esc_attr( $doc_id ); ?>" />

							<input class="btn btn-primary" type="submit" name="doc-edit-submit" id="doc-edit-submit" value="<?php _e('Save', 'bp-docs') ?>"> <a href="<?php bp_docs_cancel_edit_link() ?>" class="action safe btn btn-default no-deco"><?php _e('Cancel', 'bp-docs'); ?></a>

							<?php if ( current_user_can( 'bp_docs_manage' ) ) : ?><a class="delete-doc-button confirm" href="<?php bp_docs_delete_doc_link() ?>">Delete</a><?php endif ?>
						</div>


						<div style="clear: both"> </div>
					</div>

				</div>
			</div>
		</form>

	</div><!-- .doc-content -->

	<?php bp_docs_inline_toggle_js() ?>

	<?php if (!function_exists('wp_editor')) : ?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				/* On some setups, it helps TinyMCE to load if we fire the switchEditors event on load */
				if (typeof (switchEditors) == 'object') {
					if (!$("#edButtonPreview").hasClass('active')) {
						switchEditors.go('doc[content]', 'tinymce');
					}
				}
			}, (jQuery));
		</script>
	<?php endif ?>

	<?php /* Important - do not remove. Needed for autosave stuff */ ?>
	<div style="display:none;">
		<div id="still_working_content" name="still_working_content">
			<br />
			<h3><?php _e('Are you still there?', 'bp-docs') ?></h3>

			<p><?php _e('In order to prevent overwriting content, only one person can edit a given doc at a time. For that reason, you must periodically ensure the system that you\'re still actively editing. If you are idle for more than 30 minutes, your changes will be auto-saved, and you\'ll be sent out of Edit mode so that others can access the doc.', 'bp-docs') ?></p>

			<a href="#" onclick="jQuery.colorbox.close();
					return false" class="button"><?php _e('I\'m still editing!', 'bp-docs') ?></a>
		</div>
	</div>
</div>
