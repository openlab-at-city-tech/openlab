<?php
/**
 * Buddypress Group Documents functions
 * These functions are clones of those found in the BuddyPress Group Documents plugin
 * They are duplicated here so that Bootstrap markup can be injected for uniform styling
 */

// Plugin must set up nav late. See http://redmine.citytech.cuny.edu/issues/2335
add_action( 'bp_setup_nav', 'bp_group_documents_setup_nav', 20 );

/**
 * Dequeue inherit styling from plugin
 */
function openlab_dequeue_bp_files_styles() {
	global $bp;
	wp_dequeue_style( 'bp-group-documents' );

	remove_action( 'bp_template_content', 'bp_group_documents_display_content' );
	if ( 'files' === $bp->current_action ) {
		add_action( 'bp_template_content', 'openlab_bp_group_documents_display_content' );
	}
}
add_action( 'wp_print_styles', 'openlab_dequeue_bp_files_styles', 999 );

// Don't force Files to be active on all groups.
add_filter( 'pre_option_bp_group_documents_enable_all_groups', '__return_zero' );

/**
 * Checks whether Files tab is enabled for a group.
 *
 * @param int $group_id Group id.
 * @return bool
 */
function openlab_is_files_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to false in case no value is found.
	if ( ! $group_id ) {
		return false;
	}

	$is_disabled = groups_get_groupmeta( $group_id, 'group_documents_documents_disabled' );

	// Empty value should default to disabled for portfolios.
	if ( '' === $is_disabled && openlab_is_portfolio( $group_id ) ) {
		$is_disabled = true;
	}

	return empty( $is_disabled );
}

function openlab_bp_group_documents_display_content() {

	global $bp;

	// There are no hooks in buddypress-group-documents to filter the query.
	$query_filter_callback = function( $query ) {
		global $wpdb, $bp;

		// We are filtering the query from get_list_by_group().
		$matched = preg_match( '/SELECT \* FROM ' . $bp->group_documents->table_name . ' WHERE group_id = (\d+)/', $query, $matches );

		if ( ! $matched ) {
			return $query;
		}

		$group_id = (int) $matches[1];

		$group_privacy = groups_get_groupmeta( $group_id, 'group_document_privacy_settings' );
		if ( empty( $group_privacy ) ) {
			return $query;
		}

		if ( groups_is_user_admin( bp_loggedin_user_id(), $group_id ) ) {
			$exclude_levels = [];
		} elseif ( groups_is_user_member( bp_loggedin_user_id(), $group_id ) ) {
			$exclude_levels = [ 'admins' ];
		} else {
			$exclude_levels = [ 'admins', 'members' ];
		}

		$exclude_ids = [];
		foreach ( $group_privacy as $doc_id => $level ) {
			if ( in_array( $level, $exclude_levels, true ) ) {
				$exclude_ids[] = (int) $doc_id;
			}
		}

		// Users should also be able to see any documents they uploaded.
		$user_created_files = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->group_documents->table_name} WHERE group_id = %d AND user_id = %d", $group_id, bp_loggedin_user_id() ) ) );

		$exclude_ids = array_filter(
			$exclude_ids,
			function( $doc_id ) use ( $user_created_files ) {
				return ! in_array( $doc_id, $user_created_files, true );
			}
		);

		if ( empty( $exclude_ids ) ) {
			return $query;
		}

		$query = str_replace( 'WHERE', 'WHERE id NOT IN (' . implode( ',', $exclude_ids ) . ') AND', $query );

		return $query;
	};

	add_filter( 'query', $query_filter_callback );
	$template = new BP_Group_Documents_Template();
	remove_filter( 'query', $query_filter_callback );

	$folders = $template->get_group_categories( false );
	$folders = bp_sort_by_key( $folders, 'name' );

	$non_empty_folders = array_filter(
		$folders,
		function( $folder ) {
			return $folder->count > 0;
		}
	);

	$current_category      = false;
	$current_category_data = get_term_by( 'id', $template->category, 'group-documents-category' );

	if ( !empty( $current_category_data->name ) ) {
		$current_category = $current_category_data->name;
	}

	$is_edit_mode = bp_is_action_variable( 'edit', 0 );

	$classes = [];
	if ( $non_empty_folders ) {
		$classes[] = 'has-folders';
	}
	if ( $is_edit_mode ) {
		$classes[] = 'is-edit-mode';
	}
	if ( $current_category ) {
		$classes[] = 'is-folder';
	}

	$user_can_upload = current_user_can( 'bp_moderate' ) || groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() );

	$sort_form_action = $template->action_link;

	$header_text = 'add' === $template->operation ? 'Add a New File' : 'Edit a File';
	?>

	<div id="bp-group-documents" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

		<?php do_action( 'template_notices' ); // (error/success feedback) ?>

		<?php //-----------------------------------------------------------------------LIST VIEW-- ?>

		<?php if ( is_array( $template->document_list ) && count( $template->document_list ) ) { ?>

			<div id="bp-group-documents-sorting">
				<div class="row">
					<div class="col-sm-8 sorting-column">
						<form id="bp-group-documents-sort-form" method="get" action="<?php echo esc_attr( $sort_form_action ); ?>">
							<label for="group-documents-orderby">
								<?php esc_html_e( 'Order by:', 'bp-group-documents' ); ?>
							</label>

							<select name="order" id="group-documents-orderby" class="form-control group-documents-orderby">
								<option value="newest"
								<?php
								if ( 'newest' === $template->order ) {
									echo 'selected="selected"';}
								?>
								><?php esc_html_e( 'Newest', 'bp-group-documents' ); ?></option>
								<option value="alpha"
								<?php
								if ( 'alpha' === $template->order ) {
									echo 'selected="selected"';}
								?>
								><?php esc_html_e( 'Alphabetical', 'bp-group-documents' ); ?></option>
								<option value="popular"
								<?php
								if ( 'popular' === $template->order ) {
									echo 'selected="selected"';}
								?>
								><?php esc_html_e( 'Most Popular', 'bp-group-documents' ); ?></option>
							</select>

							<?php if ( $template->category ) : ?>
								<input type="hidden" name="category" value="<?php echo esc_attr( $template->category ); ?>" />
							<?php endif; ?>

							<input type="submit" class="bp-group-documents-go button" value="<?php esc_html_e( 'Go', 'bp-group-documents' ); ?>" />
						</form>
					</div>

					<?php if ( $user_can_upload ) : ?>
						<div class="pull-right upload-new-file">
							<?php if ( 'add' === $template->operation ) { ?>
								<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;"><?php esc_html_e( 'Add new file', 'openlab' ); ?></a>
							<?php } ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="bp-group-documents-list-container">
				<?php if ( $current_category ) : ?>
					<div class="bp-group-documents-list-folder-header">
						<i class="fa fa-folder-open-o"></i> Folder: <?php echo esc_html( $current_category ); ?>
						<div class="admin-links pull-right">
							<?php
							if ( bp_is_item_admin() ) {
								$delete_link = wp_nonce_url( $template->action_link . 'delete-folder/' . $current_category_data->term_id, 'group-documents-delete-folder-link' );
								echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='" . esc_attr( $delete_link ) . "' id='bp-group-documents-folder-delete'>Delete</a>";
							}
							?>
						</div>
					</div>
				<?php endif; ?>

				<ul id="bp-group-documents-list" class="bp-group-documents-list item-list group-list inline-element-list">
					<?php
					//loop through each document and display content along with admin options
					$count = 0;
					foreach ( $template->document_list as $document_params ) {
						$document = new BP_Group_Documents( $document_params['id'], $document_params );
						$count++;
						$alt_class = ( $count % 2 ) ? 'alt' : '';

						$document->doc_type = openlab_get_document_type( $document->file );
						$document->doc_url = ( $document->doc_type === 'upload' ) ? $document->get_url() : $document->file;
						?>

						<li class="list-group-item <?php echo esc_attr( $alt_class ); ?>">
							<?php
							// show edit and delete options if user is privileged
							echo '<div class="admin-links pull-right">';
							if ( $document->current_user_can( 'edit' ) ) {
								$edit_link = wp_nonce_url( $template->action_link . 'edit/' . $document->id, 'group-documents-edit-link' );
								echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='" . esc_attr( $edit_link ) . "'>" . esc_html__( 'Edit', 'bp-group-documents' ) . '</a> ';
							}
							if ( $document->current_user_can( 'delete' ) ) {
								$delete_link = wp_nonce_url( $template->action_link . 'delete/' . $document->id, 'group-documents-delete-link' );
								echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='" . esc_attr( $delete_link ) . "' id='bp-group-documents-delete'>" . esc_html__( 'Delete', 'bp-group-documents' ) . '</a>';
							}

							echo '</div>';
							?>

							<?php
							if ( get_option( 'bp_group_documents_display_icons' ) ) {
								if( $document->doc_type === 'upload' ) {
									$document->icon();
								} else {
									openlab_external_link_icon( $document->file );
								}
							}
							?>

							<a class="group-documents-title" id="group-document-link-<?php echo esc_attr( $document->id ); ?>" href="<?php echo $document->doc_url; ?>" target="_blank"><?php echo esc_html( stripslashes( $document->name ) ); ?>

								<?php
								if ( get_option( 'bp_group_documents_display_file_size' ) ) {
									echo ' <span class="group-documents-filesize">(' . esc_html( get_file_size( $document ) ) . ')</span>';
								}
								?>
								</a> &nbsp;

							<?php if( $document->doc_type === 'upload' ) { ?>
							<p class="group-documents-meta"><?php printf( esc_html__( 'Uploaded by %1$s on %2$s', 'bp-group-documents' ), bp_core_get_userlink( $document->user_id ), esc_html( date( get_option( 'date_format' ), $document->created_ts ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
							<?php } else { ?>
							<p class="group-documents-meta"><?php printf( esc_html__( 'Added by %1$s on %2$s', 'openlab' ), bp_core_get_userlink( $document->user_id ), esc_html( date( get_option( 'date_format' ), $document->created_ts ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
							<?php } ?>

							<?php
							if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS && $document->description ) {
								echo '<p class="group-documents-description"><em>Description:</em> ' . esc_html( nl2br( stripslashes( $document->description ) ) ) . '</p>';
							}

							echo '</li>';
					}
					?>
				</ul>
			</div>

		<?php } else { ?>
			<div id="message" class="info">
				<p class="bold">
					No files have been added yet.
					<?php if( is_user_logged_in() && $user_can_upload ) { ?>
					Add a new file below.
					<?php } ?>
				</p>

				<?php if ( $user_can_upload ) : ?>
					<div class="upload-new-file">
						<?php if ( 'add' === $template->operation ) { ?>
							<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;">Add new file</a>
						<?php } ?>
					</div>
				<?php endif; ?>
			</div>

		<?php } ?>

		<div class="bp-group-documents-folder-links">
			<label>Folders:</label>
			<div class="group-file-folder-nav">
				<ul>
					<li class="show-all-files<?php if ( ! $current_category ) : ?> current-category<?php endif ?>"><i class="fa <?php echo $current_category ? 'fa-folder-o' : 'fa-folder-open-o'; ?>"></i> <a href="<?php echo remove_query_arg( 'category', $template->action_link ) ?>">All Files</a></li>
					<hr>

					<?php foreach ( $non_empty_folders as $category ) { ?>
						<?php $is_current_category = ( $category->name === $current_category ); ?>
						<li class="folder<?php if ( $is_current_category ) : ?> current-category<?php endif ?>"><i class="fa <?php echo $is_current_category ? 'fa-folder-open-o' : 'fa-folder-o'; ?>"></i> <a href="<?php echo esc_attr( add_query_arg( 'category', $category->term_id, $template->action_link ) ); ?>"><?php echo esc_html( $category->name ); ?> <?php /* (<?php echo $category->count ?>) */ ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div><!-- .bp-group-documents-folder-links -->

			<div class="spacer" style="clear:both;">&nbsp;</div>

			<div class="pagination no-ajax">
				<?php if ( $template->show_pagination() ) { ?>
					<div class="pagination" id="pag-bottom">

						<div id="member-dir-pag-bottom" class="pagination-links">
							<ul class="page-numbers pagination">
								<?php echo openlab_bp_group_documents_custom_pagination_links( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</ul>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php //-------------------------------------------------------------------DETAIL VIEW-- ?>

			<?php if ( $template->show_detail ) { ?>

				<?php
				if ( 'add' === $template->operation ) {
					$this_id = 'bp-group-documents-upload-new';
				} else {
					$this_id = 'bp-group-documents-edit';

					// Get current document by ID
					$document = new BP_Group_Documents( $template->id );
					$template->file = $document->file;
					$template->doc_type = openlab_get_document_type( $template->file );
				}
				?>

				<div id="<?php echo esc_attr( $this_id ); ?>">

					<form method="post" id="bp-group-documents-form" class="standard-form form-panel" action="<?php echo esc_attr( $template->action_link ); ?>" enctype="multipart/form-data">

						<div class="panel panel-default">
							<div class="panel-heading"><?php echo esc_html( $header_text ); ?></div>
							<div class="panel-body">
								<?php if( 'add' === $template->operation ) { ?>
								<p>You can link to an external file, such as a OneDrive or Dropbox file. Or you can upload a file from your computer.</p>
								<?php } ?>

								<input type="hidden" name="bp_group_documents_operation" value="<?php echo esc_attr( $template->operation ); ?>" />
								<input type="hidden" name="bp_group_documents_id" value="<?php echo esc_attr( $template->id ); ?>" />

								<?php if( 'edit' === $template->operation ) { ?>
								<input type="hidden" name="bp_group_documents_file_type" value="<?php echo $template->doc_type; ?>" />
								<?php } ?>

								<div class="bp-group-documents-fields <?php echo ( $template->operation === 'add' ) ? 'show-upload' : 'show-' . $template->doc_type; ?>">
									<!-- Link -->
									<?php if( 'add' === $template->operation ) { ?>
									<div class="bp-group-documents-file-type-selector">
										<input type="radio" name="bp_group_documents_file_type" class="bp-group-documents-file-type" id="bp-group-documents-file-type-link" value="link" />
										<label for="bp-group-documents-file-type-link">Link to external file</label>
									</div>
									<?php } ?>
									<?php if( 'add' === $template->operation || ( 'edit' === $template->operation && 'link' === $template->doc_type ) ) { ?>
									<div class="bp-group-documents-fields-for-file-type" id="bp-group-documents-fields-for-file-type-link">
										<label for="bp-group-documents-link-url"><?php esc_html_e( 'File URL:', 'bp-group-documents' ); ?></label>
										<input type="text" name="bp_group_documents_link_url" id="bp-group-documents-link-url" class="form-control" value="<?php echo esc_attr( stripslashes( $template->file ) ); ?>" />

										<label for="bp-group-documents-link-name"><?php esc_html_e( 'Display Name:', 'bp-group-documents' ); ?></label>
										<input type="text" name="bp_group_documents_link_name" id="bp-group-documents-link-name" class="form-control" value="<?php echo esc_attr( stripslashes( $template->name ) ); ?>" />

										<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
										<label for="bp-group-documents-link-description"><?php esc_html_e( 'Description:', 'bp-group-documents' ); ?></label>
										<textarea name="bp_group_documents_link_description" id="bp-group-documents-link-description" class="form-control"><?php echo esc_html( stripslashes( $template->description ) ); ?></textarea>
										<?php } ?>

										<div id="document-detail-clear" class="clear"></div>
										<fieldset class="group-file-folders">
											<legend>Folders</legend>
											<div class="checkbox-list-container group-file-folders-container">
												<input type="hidden" name="bp_group_documents_link_categories[]" value="0" />
												<ul>
												<?php foreach( $folders as $category ) { ?>
													<li><input type="checkbox" name="bp_group_documents_link_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" id="link-group-folder-<?php echo esc_attr( $category->term_id ); ?>" <?php if( $template->doc_in_category($category->term_id)) echo 'checked="checked"'; ?> /> <label class="passive" for="link-group-folder-<?php echo esc_attr( $category->term_id ); ?>"><?php echo $category->name; ?></label></li>
												<?php } ?>
												</ul>
											</div>
											<label for="bp-group-documents-new-category-1" class="sr-only">Add new folder</label>
											<input type="text" name="bp_group_documents_link_new_category" class="bp-group-documents-new-folder form-control" placeholder="Add new folder" id="bp-group-documents-new-category-1" />
										</fieldset>

										<?php openlab_file_access_section_markup( $template->id, 'external-link' ); ?>

									</div>
									<?php } ?>

									<!-- Upload -->
									<?php if( 'add' === $template->operation ) { ?>
									<div class="bp-group-documents-file-type-selector">
										<input type="radio" checked="checked" name="bp_group_documents_file_type" class="bp-group-documents-file-type" id="bp-group-documents-file-type-upload" value="upload" />
										<label for="bp-group-documents-file-type-upload">Upload a file</label>
									</div>
									<?php } ?>
									<?php if( 'add' === $template->operation || ( 'edit' === $template->operation && 'upload' === $template->doc_type ) ) { ?>
									<div class="bp-group-documents-fields-for-file-type" id="bp-group-documents-fields-for-file-type-upload">
										<?php if ( 'add' === $template->operation ) { ?>
										<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( return_bytes( ini_get( 'post_max_size' ) ) ); ?>" />
										<label for="bp-group-documents-file"><?php esc_html_e( 'Choose File:', 'bp-group-documents' ); ?></label>
										<div class="form-control type-file-wrapper">
											<input type="file" id="bp-group-documents-file" name="bp_group_documents_file" class="bp-group-documents-file" />
										</div>
										<?php } ?>

										<?php if ( BP_GROUP_DOCUMENTS_FEATURED ) { ?>
										<div class="checkbox">
											<label for="bp-group-documents-featured-label"><input id="bp-group-documents-featured" type="checkbox" name="bp_group_documents_featured" class="bp-group-documents-featured" value="1" <?php checked( $template->featured ); ?> /> <?php esc_html_e( 'Featured Document', 'bp-group-documents' ); ?></label>
										</div>
										<?php } ?>

										<div id="document-detail-clear" class="clear"></div>
										<div class="document-info">
											<label for="bp-group-documents-name"><?php esc_html_e( 'Display Name:', 'bp-group-documents' ); ?></label>
											<input type="text" name="bp_group_documents_name" id="bp-group-documents-name" class="form-control" value="<?php echo esc_attr( stripslashes( $template->name ) ); ?>" />

											<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
												<label for="bp-group-documents-description"><?php esc_html_e( 'Description:', 'bp-group-documents' ); ?></label>
												<textarea name="bp_group_documents_description" id="bp-group-documents-description" class="form-control"><?php echo esc_html( stripslashes( $template->description ) ); ?></textarea>
											<?php } ?>

											<fieldset class="group-file-folders">
												<legend>Folders</legend>
												<div class="checkbox-list-container group-file-folders-container">
													<input type="hidden" name="bp_group_documents_categories[]" value="0" />
													<ul>
													<?php foreach( $folders as $category ) { ?>
														<li><input type="checkbox" name="bp_group_documents_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" id="group-folder-<?php echo esc_attr( $category->term_id ); ?>" <?php if( $template->doc_in_category($category->term_id)) echo 'checked="checked"'; ?> /> <label class="passive" for="group-folder-<?php echo esc_attr( $category->term_id ); ?>"><?php echo $category->name; ?></label></li>
													<?php } ?>
													</ul>
												</div>
												<label for="bp-group-documents-new-category-2" class="sr-only">Add new folder</label>
												<input type="text" name="bp_group_documents_new_category" class="bp-group-documents-new-folder form-control" placeholder="Add new folder" id="bp-group-documents-new-category-2" />
											</fieldset>

											<?php openlab_file_access_section_markup( $template->id, 'upload' ); ?>
										</div>
									</div>
									<?php } ?>
								</div>

								<hr />
								<div class="notify-group-members-ui">
									<?php /* Default to checked for 'add' only, not 'edit' */ ?>
									<?php openlab_notify_group_members_ui( 'add' === $template->operation ); ?>
								</div>

								<input type="submit" class="btn btn-primary bp-group-documents-submit" value="<?php esc_attr_e( 'Submit', 'bp-group-documents' ); ?>" />
								<a href="<?php echo esc_attr( $template->action_link ); ?>" class="btn btn-default" <?php echo ( 'add' === $template->operation ) ? 'id="btn-group-documents-cancel"' : ''; ?>>Cancel</a>
							</div>
						</div>

					</form>

				</div>

			<?php } ?>

	</div><!--end #group-documents-->
	<?php
}

/**
 * Catch POST request for link create/edit.
 */
add_action(
	'bp_group_documents_template_do_post_action',
	function() {
		$request_type = ! empty( $_POST['bp_group_documents_file_type'] ) ? wp_unslash( $_POST['bp_group_documents_file_type'] ) : 'upload';

		// If request is not of type 'link', let buddypress-group-documents handle it.
		if ( 'link' !== $request_type ) {
			return;
		}

		// For 'link' requests, we do not want buddypress-group-documents to process the
		// form. So we unset the 'bp_group_documents_operation' flag, which short-circuits
		// BP_Group_Documents_Template::do_post_logic().
		if ( ! empty( $_POST['bp_group_documents_operation'] ) && 'edit' === $_POST['bp_group_documents_operation'] ) {
			$operation_type = 'edit';
		} else {
			$operation_type = 'add';
		}

		unset( $_POST['bp_group_documents_operation'] );

		switch ( $operation_type ) {
			case 'add' :
				$document 				= new BP_Group_Documents();
				$document->user_id  	= get_current_user_id();
				$document->group_id 	= bp_get_current_group_id();
				$document->name     	= wp_unslash( $_POST['bp_group_documents_link_name'] );
				$document->description 	= $_POST['bp_group_documents_link_description'];
				$document->file 		= $_POST['bp_group_documents_link_url'];

				// false means "don't check for a file upload".
				if ( $document->save( false ) ) {
					openlab_update_external_link_category( $document );
					do_action( 'bp_group_documents_add_success', $document );
					bp_core_add_message( __( 'External link successfully added.','bp-group-documents' ) );
				}
			break;
			case 'edit' :
				$document 				= new BP_Group_Documents( $_POST['bp_group_documents_id'] );
				$document->name 		= wp_unslash( $_POST['bp_group_documents_link_name'] );
				$document->description 	= $_POST['bp_group_documents_link_description'];

				if( $document->save( false ) ) {
					openlab_update_external_link_category( $document );
					do_action( 'bp_group_documents_edit_success', $document );
					bp_core_add_message( __('External link successfully edited', 'bp-group-documents') );
				}
			break;
		}
	}
);

/**
 * Update `file` column for the external links saved
 * in the documents table.
 *
 * BP_Group_Documents::save()
 */
add_action(
	'bp_group_documents_data_after_save',
	function( $document ) {
		$request_type = ! empty( $_POST['bp_group_documents_file_type'] ) ? wp_unslash( $_POST['bp_group_documents_file_type'] ) : 'upload';

		// If request is not of type 'link', let buddypress-group-documents handle it.
		if( 'link' !== $request_type ) {
			return;
		}

		$result = null;
		if( $document->id ) {
			global $wpdb, $bp;

			$result = $wpdb->query( $wpdb->prepare(
				"UPDATE {$bp->group_documents->table_name}
				SET
					file = %s
				WHERE id = %d",
					$_POST['bp_group_documents_link_url'],
					$document->id
				) );
		}

		if ( ! $result ) {
			return false;
		}

		return $result;
	}
);

/**
 * Save "File Access" settings.
 */
add_action(
	'bp_group_documents_data_after_save',
	function( $document ) {
		// Two forms may appear on the page, so we need to check for the correct nonce.
		if ( ! isset( $_POST['bp_group_documents_file_type'] ) ) {
			return;
		}

		$file_type = 'link' === wp_unslash( $_POST['bp_group_documents_file_type'] ) ? 'external-link' : 'upload';

		if ( ! $file_type ) {
			return;
		}

		$nonce_key = 'bp_group_documents_file_access_nonce-' . $file_type;

		if ( ! wp_verify_nonce( $_POST[ $nonce_key ], 'bp_group_documents_file_access' ) ) {
			return;
		}

		$access_key = 'bp_group_documents_privacy-' . $file_type;

		$access = ! empty( $_POST[ $access_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $access_key ] ) ) : '';

		if ( ! in_array( $access, array( 'everyone', 'members', 'admins' ), true ) ) {
			return;
		}

		$group_document_privacy_settings = groups_get_groupmeta( $document->group_id, 'group_document_privacy_settings' );
		if ( ! is_array( $group_document_privacy_settings ) ) {
			$group_document_privacy_settings = [];
		}

		$group_document_privacy_settings[ $document->id ] = $access;

		groups_update_groupmeta( $document->group_id, 'group_document_privacy_settings', $group_document_privacy_settings );
	}
);

/**
 * Set categories for the external link submitted from the
 * group documents form.
 *
 */
function openlab_update_external_link_category( $document ) {
	//update categories from checkbox list
	if ( isset( $_POST['bp_group_documents_link_categories'] ) )
		$category_ids = apply_filters( 'bp_group_documents_category_ids_in', $_POST['bp_group_documents_link_categories'] );

	if ( isset( $category_ids ) )
		wp_set_object_terms( $document->id,$category_ids, 'group-documents-category' );

	//check if new category was added, if so, append to current list
	if( isset( $_POST['bp_group_documents_link_new_category'] ) && $_POST['bp_group_documents_link_new_category'] ) {

		$parent_id = \BP_Group_Documents_Template::get_parent_category_id();

		if( ! term_exists( $_POST['bp_group_documents_link_new_category'], 'group-documents-category', $parent_id ) ) {
			$term_info = wp_insert_term( $_POST['bp_group_documents_link_new_category'], 'group-documents-category', array( 'parent' => $parent_id ) );
			wp_set_object_terms( $document->id, $term_info['term_id'], 'group-documents-category', true );
		}
	}
}

/**
 * Catch folder delete request.
 */
add_action(
	'bp_actions',
	function() {
		if ( ! bp_is_group() || ! bp_is_current_action( 'files' ) ) {
			return;
		}

		if ( ! bp_is_action_variable( 'delete-folder' ) ) {
			return;
		}

		$folder_id = (int) bp_action_variable( 1 );
		if ( ! $folder_id ) {
			return;
		}

		check_admin_referer( 'group-documents-delete-folder-link' );

		if ( ! bp_is_item_admin() ) {
			return;
		}

		wp_delete_term( $folder_id, 'group-documents-category' );

		bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'files/' );
		die;
	}
);

/**
 * Custom file pagination
 * Pulled from BP_Group_Documents_Template->do_paging_logic()
 * @global type $wpdb
 * @global type $bp
 */
function openlab_get_files_count() {
	global $wpdb, $bp;

	$start_record = 1;
	$page         = 1;

	$group_id = bp_get_group_id();

	$sql = "SELECT COUNT(*) FROM {$bp->group_documents->table_name} WHERE group_id = %d ";

	$total_records = $wpdb->get_var( $wpdb->prepare( $sql, $group_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	$items_per_page = get_option( 'bp_group_documents_items_per_page' );
	$total_pages    = ceil( $total_records / $items_per_page );

	if ( isset( $_GET['page'] ) && ctype_digit( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$page         = $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification
		$start_record = ( ( $page - 1 ) * $items_per_page ) + 1;
	}

	$last_possible = $items_per_page * $page;
	$end_record    = ( $total_records < $last_possible ) ? $total_records : $last_possible;

	printf( esc_html__( 'Viewing item %1$s to %2$s (of %3$s items)', 'bp-group-documents' ), esc_html( $start_record ), esc_html( $end_record ), esc_html( $total_records ) );
}

/**
 * Buddypress Group Documents is very secretive about it's pagination, so we'll
 * have to do this with some str_replace fun.
 *
 * @param BP_Group_Documents_Template $template BP_Group_Documents_Template template object.
 */
function openlab_bp_group_documents_custom_pagination_links( BP_Group_Documents_Template $template ) {

	//dump the echoed legacy pagination into a string
	ob_start();
	$template->pagination_links();
	$legacy_pag = ob_get_clean();

	//redesign
	$legacy_pag = str_replace( array( '<span' ), '<li><span', $legacy_pag );
	$legacy_pag = str_replace( array( '</span>' ), '</li></span>', $legacy_pag );
	$legacy_pag = str_replace( array( '<a' ), '<li><a', $legacy_pag );
	$legacy_pag = str_replace( array( '</a>' ), '</li></a>', $legacy_pag );

	$legacy_pag = str_replace( 'page-numbers', 'page-numbers pagination', $legacy_pag );

	$legacy_pag = str_replace( '&raquo;', '<i class="fa fa-angle-right"></i>', $legacy_pag );
	$legacy_pag = str_replace( '&laquo;', '<i class="fa fa-angle-left"></i>', $legacy_pag );

	return $legacy_pag;
}

/**
 * Suppress native email notifications in favor of bp-ges-single.
 */
remove_action( 'bp_group_documents_add_success', 'bp_group_documents_email_notification', 10 );

/**
 * Customization for Group Documents activity notifications.
 */
function openlab_group_documents_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'added_group_document' :
		case 'deleted_group_document' :
		case 'edited_group_document' :
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default :
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_group_documents_activity_notification_control', 100, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_group_documents_activity_notification_control', 100, 4 );

/**
 * Render external link icon
 *
 */
function openlab_external_link_icon( $url ) {
	$url_parts = parse_url( $url );

	if( ! isset( $url_parts['host'] ) ) {
		return;
	}
	?>
	<a role="presentation" class="group-documents-icon" href="<?php echo esc_url( $url ); ?>" target="_blank">
		<img class="bp-group-documents-icon" src="<?php echo get_template_directory_uri(); ?>/images/doc-icons/<?php echo openlab_get_service_from_url( $url_parts['host'] ); ?>.png" alt="">
		<span class="sr-only">View document</span>
	</a>
	<?php
}

/**
 * Get service name from host
 *
 */
function openlab_get_service_from_url( $host ) {
	switch( $host ) {
		case 'dropbox.com':
			return 'dropbox';
			break;
		case 'docs.google.com':
		case 'drive.google.com':
			return 'drive';
			break;
		case 'zoom.com':
		case 'zoom.us':
			return 'zoom';
			break;
		case '1drv.ms':
		case 'onedrive.live.com':
			return 'onedrive';
			break;
		default;
			return 'external';
	}
}

/**
 * Outputs the markup for the 'File Access' section of the edit/create form.
 *
 * @param int|string $file_id File ID.
 * @param string     $type    Type of file.
 * @return void
 */
function openlab_file_access_section_markup( $file_id, $type ) {
	$file_privacy = openlab_get_file_privacy_setting( $file_id );

	$group_type_label = openlab_get_group_type_label(
		[
			'group_id' => bp_get_current_group_id(),
			'case'     => 'upper',
		]
	);

	$file_id_sanitized = $file_id ? absint( $file_id ) : 'new';
	$field_id_base = 'bp-group-documents-privacy-' . $type . '-' . $file_id_sanitized;

	?>

	<fieldset class="group-documents-privacy">
		<legend>File Access:</legend>

		<div class="radios">
			<div class="group-documents-privacy-option">
				<label for="<?php echo esc_attr( $field_id_base ); ?>-everyone"><input type="radio" name="bp_group_documents_privacy-<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $field_id_base ); ?>-everyone" value="everyone" <?php checked( $file_privacy, 'everyone' ); ?> /> Everyone</label>

				<p class="description">Everyone who can view this <?php echo esc_html_e( $group_type_label ); ?> can access this file</p>
			</div>

			<div class="group-documents-privacy-option">
				<label for="<?php echo esc_attr( $field_id_base ); ?>-members"><input type="radio" name="bp_group_documents_privacy-<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $field_id_base ); ?>-members" value="members" <?php checked( $file_privacy, 'members' ); ?> /> <?php echo esc_html_e( $group_type_label ); ?> members only</label>

				<p class="description">Only logged-in members of this <?php echo esc_html_e( $group_type_label ); ?> can access this file</p>
			</div>

			<div class="group-documents-privacy-option">
				<label for="<?php echo esc_attr( $field_id_base ); ?>-admins"><input type="radio" name="bp_group_documents_privacy-<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $field_id_base ); ?>-admins" value="admins" <?php checked( $file_privacy, 'admins' ); ?> /> <?php echo esc_html_e( $group_type_label ); ?> admins only</label>

				<p class="description">Only logged-in admins of this <?php echo esc_html_e( $group_type_label ); ?> and the member who added this file can access the file</p>
			</div>
		</div>

		<?php wp_nonce_field( 'bp_group_documents_file_access', 'bp_group_documents_file_access_nonce-' . $type ); ?>
	</fieldset>

	<?php
}

/**
 * Gets privacy setting for a file.
 *
 * @param int $file_id File ID.
 * @return string
 */
function openlab_get_file_privacy_setting( $file_id ) {
	$document = new BP_Group_Documents( $file_id );

	$group_id = isset( $document->group_id ) ? $document->group_id : bp_get_current_group_id();

	// We store as an array in groupmeta.
	$group_document_privacy_settings = groups_get_groupmeta( $document->group_id, 'group_document_privacy_settings' );

	if ( isset( $group_document_privacy_settings[ $file_id ] ) ) {
		$setting = $group_document_privacy_settings[ $file_id ];
	} else {
		$group = groups_get_group( $group_id );

		if ( $group && 'public' === $group->status ) {
			$setting = 'everyone';
		} else {
			$setting = 'members';
		}
	}

	return $setting;
}
