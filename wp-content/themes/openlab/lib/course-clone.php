<?php

/**
 * Course cloning
 */

/**
 * Fetches the current clone async process.
 */
function openlab_clone_async_process() {
	static $process;

	if ( null === $process ) {
		$process = new \OpenLab\Clone_Async_Process();
	}

	return $process;
}

/**
 * Get the courses that a user is an admin of
 */
function openlab_get_groups_of_type_owned_by_user( $user_id, $type ) {
	global $wpdb, $bp;

	// This is pretty hackish, but the alternatives are all hacks too
	// First, get list of all groups a user is in
	$is_admin_of     = BP_Groups_Member::get_is_admin_of( $user_id );
	$is_admin_of_ids = wp_list_pluck( $is_admin_of['groups'], 'id' );
	if ( empty( $is_admin_of_ids ) ) {
		$is_admin_of_ids = array( 0 );
	}

	// Next, get list of those that are courses
	$user_course_ids = $wpdb->get_col( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_group_type' AND meta_value = %s AND group_id IN (" . implode( ',', wp_parse_id_list( $is_admin_of_ids ) ) . ')', $type ) );
	if ( empty( $user_course_ids ) ) {
		$user_course_ids = array( 0 );
	}

	// Finally, get a pretty list
	$user_courses = groups_get_groups(
		array(
			'type'            => 'alphabetical',
			'include'         => $user_course_ids,
			'show_hidden'     => true,
			'per_page'        => 100000,
			'populate_extras' => false,
		)
	);

	return $user_courses;
}

/**
 * Catch form submits and save to the new group
 */
function openlab_clone_create_form_catcher() {
	$new_group_id = bp_get_new_group_id();

	switch ( bp_get_groups_current_create_step() ) {
		case 'group-details':
			if ( isset( $_POST['create-or-clone'] ) && 'clone' === $_POST['create-or-clone'] ) {
				$clone_source_group_id = isset( $_POST['group-to-clone'] ) ? (int) $_POST['group-to-clone'] : 0;

				if ( ! $clone_source_group_id ) {
					return;
				}

				groups_update_groupmeta( $new_group_id, 'clone_source_group_id', $clone_source_group_id );

				$change_authorship = ! empty( $_POST['change-cloned-content-attribution'] );
				groups_update_groupmeta( $new_group_id, 'change_cloned_content_attribution', $change_authorship );

				// Bust ancestor cache.
				openlab_invalidate_ancestor_clone_cache( $new_group_id );

				$clone_steps = [
					'groupmeta',
					'avatar',
					'docs',
					'files',
					'topics',
				];

				if ( isset( $_POST['new_or_old'] ) && ( 'clone' === $_POST['new_or_old'] ) && isset( $_POST['blog-id-to-clone'] ) && isset( $_POST['wds_website_check'] ) ) {
					$clone_source_blog_id = groups_get_groupmeta( $clone_source_group_id, 'wds_bp_group_site_id' );
					groups_update_groupmeta( $new_group_id, 'clone_source_blog_id', $clone_source_blog_id );

					// @todo validation
					$clone_destination_path = friendly_url( stripslashes( $_POST['clone-destination-path'] ) );
					groups_update_groupmeta( $new_group_id, 'clone_destination_path', $clone_destination_path );

					$clone_steps[] = 'site';
				}

				groups_update_groupmeta( $new_group_id, 'clone_steps', $clone_steps );

				$async = openlab_clone_async_process();
				$async->data( [ 'group_id' => $new_group_id ] )->dispatch();
			}
			break;

		case 'group-settings':
			$clone_source_group_id = intval( groups_get_groupmeta( $new_group_id, 'clone_source_group_id' ) );

			if ( ! $clone_source_group_id ) {
				return;
			}

			// Set activity item visibility based on newly saved group status.
			$a = bp_activity_get(
				array(
					'show_hidden' => true,
					'filter'      => array(
						'component'  => 'groups',
						'primary_id' => buddypress()->groups->new_group_id,
					),
				)
			);

			$group         = groups_get_group( buddypress()->groups->new_group_id );
			$hide_sitewide = 'public' !== $group->status;

			foreach ( $a['activities'] as $activity ) {
				$a_obj = new BP_Activity_Activity( $activity->id );
				if ( $hide_sitewide !== $a_obj->hide_sitewide ) {
					$a_obj->hide_sitewide = $hide_sitewide;
					$a_obj->save();
				}
			}

			break;
	}
}
add_action( 'groups_create_group_step_complete', 'openlab_clone_create_form_catcher' );

/** FILTERS ***********************************************************/

/**
 * AJAX handler for fetching group details
 */
function openlab_group_clone_fetch_details() {
	$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
	$retval   = openlab_group_clone_details( $group_id );

	die( json_encode( $retval ) );
}
add_action( 'wp_ajax_openlab_group_clone_fetch_details', 'openlab_group_clone_fetch_details' );

function openlab_group_clone_details( $group_id ) {
	$group_admin_ids = openlab_get_all_group_contact_ids( $group_id );
	$is_shared_clone = ! in_array( bp_loggedin_user_id(), $group_admin_ids, true );

	$retval = array(
		'group_id'               => $group_id,
		'enable_sharing'         => false,
		'is_shared_clone'        => $is_shared_clone,
		'name'                   => '',
		'description'            => '',
		'schools'                => array(),
		'offices'                => array(),
		'departments'            => array(),
		'course_code'            => '',
		'section_code'           => '',
		'additional_description' => '',
		'categories'             => '',
		'site_id'                => '',
		'site_url'               => '',
		'site_path'              => '',
	);

	if ( $group_id ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );

		$retval['enable_sharing'] = openlab_group_can_be_cloned( $group_id );

		$retval['name']        = $group->name;
		$retval['description'] = $group->description;

		$group_units = openlab_get_group_academic_units( $group_id );
		foreach ( $group_units as $unit_type => $units ) {
			$retval[ $unit_type ] = $units;
		}

		$retval['course_code']            = groups_get_groupmeta( $group_id, 'wds_course_code' );
		$retval['section_code']           = groups_get_groupmeta( $group_id, 'wds_section_code' );
		$retval['additional_description'] = groups_get_groupmeta( $group_id, 'wds_course_html' );

		$group_categories = bpcgc_get_group_selected_terms( $group_id );
		if ( ! empty( $group_categories ) ) {
			$retval['categories'] = array_values( wp_list_pluck( $group_categories, 'term_id' ) );
		}

		$retval['site_id']   = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' );
		$retval['site_url']  = get_blog_option( $retval['site_id'], 'home' );
		$retval['site_path'] = str_replace( bp_get_root_domain(), '', $retval['site_url'] );
	}

	return $retval;
}

function openlab_clone_course_group( $group_id, $source_group_id ) {
	$c = new Openlab_Clone_Course_Group( $group_id, $source_group_id );
	$c->go();
}

function openlab_clone_course_site( $group_id, $source_group_id, $source_site_id, $clone_destination_path ) {
	$c = new Openlab_Clone_Course_Site( $group_id, $source_group_id, $source_site_id, $clone_destination_path );
	$c->go();
}

/** CREATE / EDIT *************************************************************/

/**
 * Outputs the markup for the Sharing Settings panel.
 *
 * @param string $group_type Group type.
 */
function openlab_group_sharing_settings_markup( $group_type = null ) {
	$sharing_enabled = openlab_group_can_be_cloned();
	$group_label_uc  = openlab_get_group_type_label( [
		'case'       => 'upper',
		'group_type' => $group_type
	] );

	if ( 'course' === $group_type ) {
		$gloss = 'This setting enables other faculty to clone your Course. If enabled, other faculty can reuse, remix, transform, and build upon the material in this Course, using it as their own. Acknowledgement of original Course authors will be included on the Course Profile and in the sidebar of the Site.';
	} else {
		$gloss = sprintf( 'This setting enables other OpenLab members to clone your %s. If enabled, other OpenLab members can reuse, remix, transform, and build upon the material in this %s, using it as their own. Acknowledgement of original %s authors will be included on the %s Profile and in the sidebar of the Site.', esc_html( $group_label_uc ), esc_html( $group_label_uc ), esc_html( $group_label_uc ), esc_html( $group_label_uc ) );
	}

	?>

	<div class="panel panel-default sharing-settings-panel">
		<div class="panel-heading semibold">Sharing Settings</div>
		<div class="panel-body">
			<p><?php echo esc_html( $gloss ); ?></p>

			<div class="checkbox">
				<label><input type="checkbox" name="openlab-enable-sharing" id="openlab-enable-sharing" value="1"<?php checked( $sharing_enabled ); ?> /> Enable shared cloning</label>
			</div>
		</div>

		<?php wp_nonce_field( 'openlab_sharing_settings', 'openlab_sharing_settings_nonce', false ); ?>
	</div>

	<?php
}

/**
 * Processes Sharing Settings on create/edit.
 *
 * @param BP_Groups_Group $group Group object.
 */
function openlab_sharing_settings_save( $group ) {
	$nonce = '';
	if ( isset( $_POST['openlab_sharing_settings_nonce'] ) ) {
		$nonce = urldecode( $_POST['openlab_sharing_settings_nonce'] );
	}

	if ( ! wp_verify_nonce( $nonce, 'openlab_sharing_settings' ) ) {
		return;
	}

	// Admins only.
	if ( ! current_user_can( 'bp_moderate' ) && ! groups_is_user_admin( bp_loggedin_user_id(), $group->id ) ) {
		return;
	}

	$enable_sharing = ! empty( $_POST['openlab-enable-sharing'] );

	if ( $enable_sharing ) {
		groups_update_groupmeta( $group->id, 'enable_sharing', 1 );

		$site_id = openlab_get_site_id_by_group_id( $group->id );
		if ( $site_id ) {
			switch_to_blog( $site_id );
			openlab_add_widget_to_main_sidebar( 'openlab_shareable_content_widget' );
			restore_current_blog();
		}
	} else {
		groups_delete_groupmeta( $group->id, 'enable_sharing' );
	}
}
add_action( 'groups_group_after_save', 'openlab_sharing_settings_save' );

/**
 * Adds 'Clone this Course' button to group profile.
 */
function openlab_add_clone_button_to_profile() {
	$group_id = bp_get_current_group_id();

	if ( ! openlab_group_can_be_cloned( $group_id ) ) {
		return;
	}

	if ( ! openlab_user_can_clone_group( get_current_user_id(), $group_id ) ) {
		return;
	}

	$group_type       = openlab_get_group_type( $group_id );
	$group_type_label = openlab_get_group_type_label(
		array(
			'group_id' => $group_id,
			'case'     => 'upper',
		)
	);

	$clone_link = add_query_arg(
		array(
			'clone' => $group_id,
			'type'  => $group_type,
		),
		bp_get_groups_directory_permalink() . 'create/step/group-details/'
	);

	?>
	<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo esc_attr( $clone_link ); ?>"><i class="fa fa-clone" aria-hidden="true"></i> Clone this <?php echo esc_html( $group_type_label ); ?></a>
	<?php
}
add_action( 'bp_group_header_actions', 'openlab_add_clone_button_to_profile', 50 );

/**
 * 'descendant-of' parameter support for group directories.
 */
add_filter(
	'bp_before_groups_get_groups_parse_args',
	function( $args ) {
		$group_id = openlab_get_current_filter( 'descendant-of' );
		if ( ! $group_id ) {
			return $args;
		}

		$group = groups_get_group( $group_id );

		$exclude_hidden = ! current_user_can( 'bp_moderate' );
		$descendant_ids = openlab_get_clone_descendants_of_group( $group_id, [ $group->creator_id ], $exclude_hidden );
		if ( ! $descendant_ids ) {
			$descendant_ids = [ 0 ];
		}

		$args['include'] = $descendant_ids;

		return $args;
	}
);

/** CLASSES ******************************************************************/

class Openlab_Clone_Course_Group {
	var $group_id;
	var $source_group_id;

	var $source_group_admins = array();

	public function __construct( $group_id, $source_group_id ) {
		$this->group_id        = $group_id;
		$this->source_group_id = $source_group_id;
	}

	public function migrate_groupmeta() {
		$keys = array(
			'ass_default_subscription',
			'bpdocs',
			'external_site_comments_feed',
			'external_site_posts_feed',
			'external_site_type',
			'external_site_url',
			'invite_status',
		);

		foreach ( $keys as $k ) {
			$v = groups_get_groupmeta( $this->source_group_id, $k );
			groups_update_groupmeta( $this->group_id, $k, $v );
		}
	}

	public function migrate_avatar() {
		// Don't allow avatar to be migrated if cloning another's group.
		$group_admin_ids = openlab_get_all_group_contact_ids( $this->source_group_id );

		if ( ! in_array( bp_loggedin_user_id(), $group_admin_ids, true ) ) {
			return;
		}

		$avatar_path       = trailingslashit( bp_core_avatar_upload_path() ) . trailingslashit( 'group-avatars' );
		$source_avatar_dir = $avatar_path . $this->source_group_id;

		if ( file_exists( $source_avatar_dir ) ) {
			if ( $av_dir = opendir( $source_avatar_dir ) ) {
				$dest_avatar_dir = $avatar_path . $this->group_id;
				mkdir( $dest_avatar_dir );
				while ( false !== ( $source_avatar_file = readdir( $av_dir ) ) ) {
					if ( 2 < strlen( $source_avatar_file ) ) {
						copy( $source_avatar_dir . '/' . $source_avatar_file, $dest_avatar_dir . '/' . $source_avatar_file );
					}
				}
			}
		}
	}

	public function migrate_docs() {
		$docs = array();

		$bp_docs_query = new BP_Docs_Query();

		$group_type_term = term_exists( 'group', $bp_docs_query->associated_item_tax_name );

		$source_group = groups_get_group( array( 'group_id' => $this->source_group_id ) );

		$source_group_term = term_exists( $source_group->slug, $bp_docs_query->associated_item_tax_name, $group_type_term['term_id'] );

		$docs_args = array(
			'post_type'      => $bp_docs_query->post_type_name,
			'tax_query'      => array(
				array(
					'taxonomy' => $bp_docs_query->associated_item_tax_name,
					'field'    => 'id',
					'terms'    => $source_group_term['term_id'],
				),
			),
			'posts_per_page' => '-1',
		);

		$dq = new WP_Query( $docs_args );

		// Activity items should default to the setting of the source group.
		$callback = function( $hide_sitewide ) use ( $source_group ) {
			return 'public' !== $source_group->status;
		};
		add_filter( 'bp_docs_groups_hide_sitewide', $callback );

		if ( $dq->have_posts() ) {

			$source_group_admins = $this->get_source_group_admins();

			$group = groups_get_group( array( 'group_id' => $this->group_id ) );

			// manually create the term
			$item_term_args = array(
				'description' => $group->name,
				'slug'        => $group->slug,
				'parent'      => $group_type_term['term_id'],
			);

			$item_term = wp_insert_term( $group->id, $bp_docs_query->associated_item_tax_name, $item_term_args );

			if ( ! is_wp_error( $item_term ) ) {
				while ( $dq->have_posts() ) {
					$dq->the_post();

					global $post;

					// Skip non-admin posts
					if ( in_array( $post->post_author, $source_group_admins ) ) {

						// Docs has no good way of mass producing posts
						// We will insert the post via WP and manually
						// add the metadata
						$post_a = (array) $post;
						unset( $post_a['ID'] );

						if ( $this->change_content_attribution() ) {
							$post_a['post_author'] = bp_loggedin_user_id();
						}

						$new_doc_id = wp_insert_post( $post_a );

						// Associated group
						wp_set_post_terms( $new_doc_id, $item_term['term_id'], $bp_docs_query->associated_item_tax_name );

						// Set last editor
						$last_editor = get_post_meta( $post->ID, 'bp_docs_last_editor', true );
						update_post_meta( $new_doc_id, 'bp_docs_last_editor', $last_editor );

						// Migrate settings. @todo Access validation? in case new group has more restrictive settings than previous
						$settings = get_post_meta( $post->ID, 'bp_docs_settings', true );
						update_post_meta( $new_doc_id, 'bp_docs_settings', $settings );

						// Set revision count to 1 - we're not bringing revisions with us
						update_post_meta( $new_doc_id, 'bp_docs_revision_count', 1 );

						// Update activity stream
						$temp_query             = new stdClass();
						$temp_query->doc_id     = $new_doc_id;
						$temp_query->is_new_doc = true;
						$temp_query->item_type  = 'group';
						$temp_query->item_id    = $this->group_id;
						BP_Docs_BP_Integration::post_activity( $temp_query );
					}
				}
			}
		}

		remove_filter( 'bp_docs_groups_hide_sitewide', $callback );

		/* 1.4+ */
		/*
		$docs_args = array(
			'group_id' => $this->source_group_id,
			'posts_per_page' => '-1',
		);

		if ( bp_docs_has_docs( $docs_args ) ) {

			$bp_docs_query = new BP_Docs_Query;
			$source_group_admins = $this->get_source_group_admins();

			while ( bp_docs_has_docs() ) {
				bp_docs_the_doc();

				global $post;

				// Skip non-admin posts
				if ( in_array( $post->post_author, $source_group_admins ) ) {

					// Docs has no good way of mass producing posts
					// We will insert the post via WP and manually
					// add the metadata
					$post_a = (array) $post;
					unset( $post_a['ID'] );
					$new_doc_id = wp_insert_post( $post_a );

					// Associated group
					bp_docs_set_associated_group_id( $new_doc_id, $this->group_id );

					// Associated user tax
					$user = new WP_User( $post->post_author );
					$user_term_id = bp_docs_get_item_term_id( $user->ID, 'user', $user->display_name );
					wp_set_post_terms( $new_doc_id, $user_term_id, $bp_docs_query->associated_item_tax_name, true );

					// Set last editor
					$last_editor = get_post_meta( $post->ID, 'bp_docs_last_editor', true );
					update_post_meta( $new_doc_id, 'bp_docs_last_editor', $last_editor );

					// Migrate settings. @todo Access validation? in case new group has more restrictive settings than previous
					$settings = get_post_meta( $post->ID, 'bp_docs_settings', true );
					update_post_meta( $new_doc_id, 'bp_docs_settings', $settings );

					// Read setting to a taxonomy
					$read_setting = isset( $settings['read'] ) ? $settings['read'] : 'anyone';
					bp_docs_update_doc_access( $new_doc_id, $read_setting );

					// Set revision count to 1 - we're not bringing revisions with us
					update_post_meta( $new_doc_id, 'bp_docs_revision_count', 1 );

					// Update activity stream
					$temp_query = new stdClass;
					$temp_query->doc_id = $new_doc_id;
					$temp_query->is_new_doc = true;
					$temp_query->item_type = 'group';
					$temp_query->item_id = $this->group_id;
					BP_Docs_Component::post_activity( $temp_query );
				}
			}
		}
		*/
	}

	public function migrate_files() {
		$source_group_admins = $this->get_source_group_admins();
		$source_files        = BP_Group_Documents::get_list_by_group( $this->source_group_id );

		$source_group_parent_term = get_term_by( 'name', "g" . $this->source_group_id, 'group-documents-category' );
		if ( $source_group_parent_term ) {
			$source_group_cats = get_terms(
				'group-documents-category',
				array(
					'parent'     => $source_group_parent_term->term_id ,
					'hide_empty' => false,
				)
			);
		} else {
			$source_group_cats = [];
		}

		$used_cats = [];

		$parent_term_info = wp_insert_term( 'g' . $this->group_id, 'group-documents-category' );
		if ( is_wp_error( $parent_term_info ) ) {
			return;
		}

		$parent_term_id = $parent_term_info['term_id'];

		foreach ( $source_files as $source_file ) {
			if ( ! in_array( $source_file['user_id'], $source_group_admins ) ) {
				continue;
			}

			// Set up the document info
			$document = new BP_Group_Documents();

			$document->group_id = $this->group_id;

			if ( $this->change_content_attribution() ) {
				$document->user_id = bp_loggedin_user_id();
			} else {
				$document->user_id = $source_file['user_id'];
			}

			$document->name        = $source_file['name'];
			$document->description = $source_file['description'];
			$document->file        = $source_file['file'];
			$document->save( false ); // false is "don't check file upload"

			// Categories/folders.
			$source_file_categories = wp_get_object_terms( $source_file['id'], 'group-documents-category' );

			$categories_to_add = [];
			foreach ( $source_file_categories as $source_category ) {
				$dest_category = term_exists( $source_category->name, 'group-documents-category', $parent_term_id );
				$dest_category_id = null;
				if ( ! $dest_category ) {
					$term_info = wp_insert_term(
						$source_category->name,
						'group-documents-category',
						[
							'parent' => $parent_term_id,
						]
					);

					if ( ! is_wp_error( $term_info ) ) {
						$dest_category_id = $term_info['term_id'];
					}
				} else {
					$dest_category_id = $dest_category['term_id'];
				}

				$categories_to_add[] = (int) $dest_category_id;

				$used_cats[ $source_category->name ] = 1;
			}

			if ( $categories_to_add ) {
				$added = wp_set_object_terms( $document->id, $categories_to_add, 'group-documents-category' );
			}

			// Copy the file itself
			$destination_dir = bp_core_avatar_upload_path() . '/group-documents/' . $this->group_id;
			if ( ! is_dir( $destination_dir ) ) {
				mkdir( $destination_dir, 0755, true );
			}

			$destination_path = $destination_dir . '/' . $document->file;

			$source_path = bp_core_avatar_upload_path() . '/group-documents/' . $this->source_group_id . '/' . $document->file;

			copy( $source_path, $destination_path );
		}

		// Process empty folders.
		foreach ( $source_group_cats as $source_group_cat ) {
			if ( isset( $used_cats[ $source_group_cat->name ] ) ) {
				continue;
			}

			$term_info = wp_insert_term(
				$source_group_cat->name,
				'group-documents-category',
				[
					'parent' => $parent_term_id,
				]
			);
		}
	}

	public function migrate_topics() {
		$source_group_admins = $this->get_source_group_admins();
		$forum_ids           = bbp_get_group_forum_ids( $this->group_id );

		// Should never happen, but just in case
		// (without this, it returns all topics)
		if ( empty( $forum_ids ) ) {
			return;
		}
		$forum_id = $forum_ids[0];

		// Get source topics
		$source_forum_ids = bbp_get_group_forum_ids( $this->source_group_id );
		if ( empty( $source_forum_ids ) ) {
			return;
		}

		$source_forum_id = $source_forum_ids[0];
		if ( ! $source_forum_id ) {
			return;
		}

		$source_forum_topics = new WP_Query(
			array(
				'post_type'      => bbp_get_topic_post_type(),
				'post_parent'    => $source_forum_id,
				'posts_per_page' => -1,
				'author__in'     => $source_group_admins,
			)
		);
		$group               = groups_get_group( array( 'group_id' => $this->group_id ) );

		// Set the default forum status
		switch ( $group->status ) {
			case 'hidden':
				$status = bbp_get_hidden_status_id();
				break;
			case 'private':
				$status = bbp_get_private_status_id();
				break;
			case 'public':
			default:
				$status = bbp_get_public_status_id();
				break;
		}

		// Then post them
		foreach ( $source_forum_topics->posts as $sftk ) {
			$topic_args = [
				'post_parent'  => $forum_id,
				'post_status'  => $status,
				'post_author'  => $sftk->post_author,
				'post_content' => $sftk->post_content,
				'post_title'   => $sftk->post_title,
				'post_date'    => $sftk->post_date,
			];

			if ( $this->change_content_attribution() ) {
				$topic_args['post_author'] = bp_loggedin_user_id();
			}

			bbp_insert_topic(
				$topic_args,
				array(
					'forum_id' => $forum_id,
				)
			);
		}
	}

	/**
	 * Gets the admins of all source groups.
	 *
	 * This returns admins for ALL courses in a clone history, so that grandchildren+ get
	 * original content cloned properly.
	 */
	protected function get_source_group_admins() {
		if ( ! empty( $this->source_group_admins ) ) {
			return $this->source_group_admins;
		}

		$clone_history = openlab_get_group_clone_history( $this->group_id );

		$admin_ids = array();
		foreach ( $clone_history as $group_id ) {
			$group     = groups_get_group( $group_id );
			$admin_ids = array_merge( $admin_ids, wp_list_pluck( $group->admins, 'user_id' ) );
		}

		$this->source_group_admins = array_unique( $admin_ids );

		return $admin_ids;
	}

	/**
	 * Determines whether content attribution should be switched to current user.
	 *
	 * @return bool
	 */
	protected function change_content_attribution() {
		$change = groups_get_groupmeta( $this->group_id, 'change_cloned_content_attribution' );
		return (bool) $change;
	}
}

class Openlab_Clone_Course_Site {
	var $group_id;
	var $site_id;

	var $source_group_id;
	var $source_site_id;
	var $destination_path;

	var $source_group_admins = array();

	public function __construct( $group_id, $source_group_id, $source_site_id, $destination_path ) {
		$this->group_id         = $group_id;
		$this->source_group_id  = $source_group_id;
		$this->source_site_id   = $source_site_id;
		$this->destination_path = $destination_path;
	}

	/**
	 * Summary:
	 *
	 * 1) Create new empty blog with necessary details
	 * 2) Copy settings from old blog, using blacklist
	 * 3) Copy admin-authored posts from old blog
	 */
	public function go() {
		global $wpdb;
//		wp_suspend_cache_invalidation();

		remove_action( 'bp_activity_after_save', 'ass_group_notification_activity', 50 );

		remove_action ('to/get_terms_orderby/ignore', 'to_get_terms_orderby_ignore_coauthors', 10, 3);
		remove_action ('to/get_terms_orderby/ignore', 'to_get_terms_orderby_ignore_woocommerce', 10, 3);

		remove_filter('terms_clauses', 'TO_apply_order_filter', 10, 3);

		switch_to_blog( $this->site_id );
		$eo_is_active = is_plugin_active( 'event-organiser/event-organiser.php' );
		restore_current_blog();

		remove_action( 'eventorganiser_save_event', '_eventorganiser_delete_calendar_cache' );
		remove_action( 'eventorganiser_delete_event', '_eventorganiser_delete_calendar_cache' );
		remove_action( 'wp_trash_post', '_eventorganiser_delete_calendar_cache' );

		if ( ! $eo_is_active ) {
			remove_action( 'delete_post', 'eo_delete_event_occurences', 10 );
		}

		remove_action( 'delete_post', '_update_posts_count_on_delete' );
		remove_action( 'delete_post', '_wp_delete_post_menu_item' );
		remove_action( 'delete_attachment', '_delete_attachment_theme_mod' );
		remove_action( 'publish_post', '_publish_post_hook', 5, 1 );

		$this->create_site();

		if ( ! empty( $this->site_id ) ) {
			$this->migrate_site_settings();
			$this->migrate_posts();
			$this->migrate_forms();
		}

		add_action( 'bp_activity_after_save', 'ass_group_notification_activity', 50 );
		add_action ('to/get_terms_orderby/ignore', 'to_get_terms_orderby_ignore_coauthors', 10, 3);
		add_action ('to/get_terms_orderby/ignore', 'to_get_terms_orderby_ignore_woocommerce', 10, 3);
		add_filter('terms_clauses', 'TO_apply_order_filter', 10, 3);

		if ( function_exists( '_eventorganiser_delete_calendar_cache' ) ) {
			_eventorganiser_delete_calendar_cache();
			add_action( 'wp_trash_post', '_eventorganiser_delete_calendar_cache' );
		}

		add_action( 'delete_post', '_update_posts_count_on_delete' );
		add_action( 'delete_post', '_wp_delete_post_menu_item' );
		add_action( 'delete_attachment', '_delete_attachment_theme_mod' );
		add_action( 'publish_post', '_publish_post_hook', 5, 1 );

		update_posts_count();

		wp_cache_flush();
	}

	protected function create_site() {
		global $wpdb;

		// Assemble args and create the new site
		$domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->blogs} WHERE blog_id = 1" );

		$clone_destination_path = groups_get_groupmeta( $this->group_id, 'clone_destination_path' );
		$path                   = '/' . $clone_destination_path . '/';

		$group = groups_get_group( array( 'group_id' => $this->group_id ) );
		$title = $group->name;

		$user_id = $group->creator_id;

		$meta = array(
			'public' => 1,
		);

		// We take care of this ourselves later on
		remove_action( 'wpmu_new_blog', 'st_wpmu_new_blog', 10, 6 );

		$site_id = wpmu_create_blog(
			$domain,
			$path,
			$title,
			$user_id,
			$meta
		);

		if ( ! is_wp_error( $site_id ) ) {
			$this->site_id = $site_id;

			// Associate site with the group in groupmeta
			groups_update_groupmeta( $this->group_id, 'wds_bp_group_site_id', $this->site_id );
		}
	}

	/**
	 * Taken from site-template
	 */
	protected function migrate_site_settings() {
		global $wpdb;

		switch_to_blog( $this->source_site_id );

		// get all old options
		$all_options = wp_load_alloptions();
		$options     = array();
		foreach ( array_keys( $all_options ) as $key ) {
			$options[ $key ] = get_option( $key );  // have to do this to deal with arrays
		}

		// theme mods -- don't show up in all_options.
		// Only add options for the current theme
		$theme = get_option( 'current_theme' );
		$mods  = get_option( 'mods_' . $theme );

		$preserve_option = array(
			'bcn_options',
			'bcn_version',
			'blog_public',
			'siteurl',
			'blogname',
			'admin_email',
			'new_admin_email',
			'home',
			'upload_path',
			'db_version',
			$wpdb->get_blog_prefix( $this->site_id ) . 'user_roles',
			'fileupload_url',
			'oplb_gradebook_db_version', // This will force reinstallation of tables.
			'duplicate_post_version', // Forces duplicate-post to initialize roles
		);

		// now write them all back
		switch_to_blog( $this->site_id );
		foreach ( $options as $key => $value ) {
			if ( ! in_array( $key, $preserve_option ) ) {
				update_option( $key, $value );
			}
		}

		// add the theme mods
		update_option( 'mods_' . $theme, $mods );

		// Just in case
		create_initial_taxonomies();
		flush_rewrite_rules();

		// Only add the Credits widget if there are non-self ancestors.
		$group              = groups_get_group( $this->group_id );
		$all_group_contacts = openlab_get_all_group_contact_ids( $this->group_id );
		if ( count( $all_group_contacts ) <= 1 ) {
			$exclude_creator = $all_group_contacts[0];
		} else {
			$exclude_creator = null;
		}

		if ( openlab_get_group_clone_history_data( $group->id, $exclude_creator ) ) {
			openlab_add_widget_to_main_sidebar( 'openlab_clone_credits_widget', 9999 );
		}

		$enable_sharing = groups_get_groupmeta( $group->id, 'enable_sharing', true );
		if ( $enable_sharing ) {
			openlab_add_widget_to_main_sidebar( 'openlab_shareable_content_widget' );
		}

		restore_current_blog();
	}

	/**
	 * The strategy is to copy all posts, postmeta, and taxonomy. Then I'll
	 * delete the irrelevant stuff. This ensures that we don't lose any
	 * tax/metadata by trying to do it all manually.
	 */
	protected function migrate_posts() {
		global $wpdb;

		$tables_to_copy = array(
			'posts',
			'postmeta',
			'terms',
			'term_taxonomy',
			'term_relationships',
		);

		// Have to use different syntax for shardb
		$source_site_prefix = $wpdb->get_blog_prefix( $this->source_site_id );
		$site_prefix        = $wpdb->get_blog_prefix( $this->site_id );
		foreach ( $tables_to_copy as $ttc ) {
			$source_table = $source_site_prefix . $ttc;
			$table        = $site_prefix . $ttc;

			// @todo
			if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
				global $shardb_hash_length, $shardb_prefix;
				$source_table_hash = strtoupper( substr( md5( $this->source_site_id ), 0, $shardb_hash_length ) );
				$table_hash        = strtoupper( substr( md5( $this->site_id ), 0, $shardb_hash_length ) );

				$source_table = $shardb_prefix . $source_table_hash . '.' . $source_table;
				$table        = $shardb_prefix . $table_hash . '.' . $table;
			}

			// Drop existing table and recreate to ensure a schema match.
			$wpdb->query( "DROP TABLE {$table}" );
			$wpdb->query( "CREATE TABLE {$table} LIKE {$source_table}" );
			$wpdb->query( "INSERT INTO {$table} SELECT * FROM {$source_table}" );
		}

		// Loop through all posts and:
		// - if it's not by an admin, delete
		// - if it's a nav item, change the GUID and the menu item URL meta
		switch_to_blog( $this->site_id );

		$source_site_url = get_blog_option( $this->source_site_id, 'home' );
		$dest_site_url   = get_option( 'home' );

		// URL replacement should be protocol-free, to account for SSL-converted sites.
		$source_site_url = preg_replace( '/^https?/', '', $source_site_url );
		$dest_site_url   = preg_replace( '/^https?/', '', $dest_site_url );

		// Copy over attachments. Whee!
		$upload_dir = wp_upload_dir();
		self::copyr( str_replace( $this->site_id, $this->source_site_id, $upload_dir['basedir'] ), $upload_dir['basedir'] );

		$site_posts          = $wpdb->get_results( "SELECT ID, guid, post_author, post_status, post_title, post_type FROM {$wpdb->posts}" );
		$source_group_admins = $this->get_source_group_admins();

		$posts_to_delete_ids = [];
		$atts_to_delete_ids  = [];
		foreach ( $site_posts as $sp ) {
			// Skip Custom CSS post.
			if ( 'custom_css' === $sp->post_type) {
				continue;
			}

			if ( ! is_super_admin( $sp->post_author ) && ! in_array( $sp->post_author, $source_group_admins ) && 'nav_menu_item' !== $sp->post_type ) {
				// Non-admins have their stuff deleted.
				if ( 'attachment' === $sp->post_type ) {
					$atts_to_delete_ids[] = $sp->ID;
				} else {
					$posts_to_delete_ids[] = $sp->ID;
				}
			} else {
				// Admin-created content comes along, but may have its authorship changed.
				if ( $this->change_content_attribution() ) {
					wp_update_post(
						[
							'ID'          => $sp->ID,
							'post_author' => bp_loggedin_user_id(),
						]
					);
				}
			}

			if ( 'nav_menu_item' === $sp->post_type ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'guid' => str_replace( $source_site_url, $dest_site_url, $sp->guid ),
					),
					array(
						'ID' => $sp->ID,
					)
				);

				$url     = get_post_meta( $sp->ID, '_menu_item_url', true );
				$classes = get_post_meta( $sp->ID, '_menu_item_classes', true );

				if ( $url ) {
					update_post_meta( $sp->ID, '_menu_item_url', str_replace( $source_site_url, $dest_site_url, $url ) );
				}

				// Update "Group Profile" nav item url.
				if ( ! empty( $classes ) && in_array( 'menu-item-group-profile-link', $classes ) ) {
					$group = groups_get_group( $this->group_id );
					update_post_meta( $sp->ID, '_menu_item_url', bp_get_group_permalink( $group ) );
				}
			}
		}

		// Delete all edit locks.
		$wpdb->delete(
			$wpdb->postmeta,
			array(
				'meta_key' => '_edit_lock',
			)
		);

		// Bulk delete metadata and revisions.
		if ( $posts_to_delete_ids ) {
			$sql_ids = implode( ',', array_map( 'intval', $posts_to_delete_ids ) );
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'revision' AND post_parent IN ({$sql_ids})" );
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ({$sql_ids})" );
		}

		foreach ( $atts_to_delete_ids as $att_id ) {
			// Will delete file as well.
			wp_delete_attachment( $att_id, true );
		}

		foreach ( $posts_to_delete_ids as $post_id ) {
			wp_delete_post( $post_id, true );
		}

		// Replace the site URL in all post content.
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE( post_content, %s, %s )", $source_site_url, $dest_site_url ) );

		restore_current_blog();
	}

	/**
	 * Migrate Gravity Forms data.
	 *
	 * @return void
	 */
	protected function migrate_forms() {
		global $wpdb;

		switch_to_blog( $this->source_site_id );

		// Gravity Forms isn't active. Bail early.
		if ( ! is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
			restore_current_blog();
			return;
		}

		restore_current_blog();

		$source_prefix = $wpdb->get_blog_prefix( $this->source_site_id );
		$site_prefix   = $wpdb->get_blog_prefix( $this->site_id );

		$tables_to_copy = [
			'gf_draft_submissions',
			'gf_entry',
			'gf_entry_meta',
			'gf_entry_notes',
			'gf_form',
			'gf_form_meta',
			'gf_form_revisions',
			'gf_form_view',
			'gf_addon_feed',
			'rg_form',
			'rg_form_meta',
			'rg_form_view',
			'rg_incomplete_submissions',
			'rg_lead',
			'rg_lead_detail',
			'rg_lead_detail_long',
			'rg_lead_meta',
			'rg_lead_notes',
		];

		$with_data = [
			'gf_form',
			'gf_form_meta',
			'gf_form_revisions',
			'rg_form',
			'rg_form_meta',
		];

		foreach ( $tables_to_copy as $ttc ) {
			$source_table = $source_prefix . $ttc;
			$table        = $site_prefix . $ttc;

			// Handle SharDB.
			if ( defined( 'DO_SHARDB' ) && DO_SHARDB ) {
				global $shardb_hash_length, $shardb_prefix;

				$source_table_hash = strtoupper( substr( md5( $this->source_site_id ), 0, $shardb_hash_length ) );
				$table_hash        = strtoupper( substr( md5( $this->site_id ), 0, $shardb_hash_length ) );

				$source_table = $shardb_prefix . $source_table_hash . '.' . $source_table;
				$table        = $shardb_prefix . $table_hash . '.' . $table;
			}

			// Drop existing table and recreate to ensure a schema match.
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
			$wpdb->query( "CREATE TABLE {$table} LIKE {$source_table}" );

			// Clone form database objects.
			if ( in_array( $ttc, $with_data, true ) ) {
				$wpdb->query( "INSERT INTO {$table} SELECT * FROM {$source_table}" );
			}
		}
	}

	protected function get_source_group_admins() {
		if ( ! empty( $this->source_group_admins ) ) {
			return $this->source_group_admins;
		}

		$clone_history = openlab_get_group_clone_history( $this->group_id );

		// Must switch back to root site to get admins. See #2201.
		$switched = false;
		if ( ! bp_is_root_blog() ) {
			$switched = true;
			switch_to_blog( bp_get_root_blog_id() );
		}

		$admin_ids = array();
		foreach ( $clone_history as $group_id ) {
			$group     = groups_get_group( $group_id );
			$admin_ids = array_merge( $admin_ids, wp_list_pluck( $group->admins, 'user_id' ) );
		}

		if ( $switched ) {
			restore_current_blog();
		}

		$this->source_group_admins = array_unique( $admin_ids );

		return array_map( 'intval', $this->source_group_admins );
	}

	/**
	 * Determines whether content attribution should be switched to current user.
	 *
	 * @return bool
	 */
	protected function change_content_attribution() {
		$change = groups_get_groupmeta( $this->group_id, 'change_cloned_content_attribution' );
		return (bool) $change;
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	public static function copyr( $source, $dest ) {
		// Don't copy files under the gravity_forms directory
		if( strpos( $source, 'gravity_forms' ) !== false ) {
			// Check for symlinks
			if ( is_link( $source ) ) {
				return symlink( readlink( $source ), $dest );
			}

			// Simple copy for a file
			if ( is_file( $source ) ) {
				return copy( $source, $dest );
			}

			// Make destination directory
			if ( ! is_dir( $dest ) ) {
				wp_mkdir_p( $dest );
			}

			// Loop through the folder
			$dir = dir( $source );
			while ( false !== $entry = $dir->read() ) {
				// Skip pointers
				if ( '.' === $entry || '..' === $entry ) {
					continue;
				}

				// Deep copy directories
				self::copyr( "$source/$entry", "$dest/$entry" );
			}

			// Clean up
			$dir->close();
			return true;
		}
	}
}

/**
 * Cleanup routine for clones that didn't fully finish.
 */
function openlab_cleanup_clone_async_processes() {
	if ( ! bp_is_group() ) {
		return;
	}

	if ( ! is_user_logged_in() ) {
		return;
	}

	$group_id = bp_get_current_group_id();

	$clone_steps = groups_get_groupmeta( $group_id, 'clone_steps', true );
	if ( ! $clone_steps ) {
		return;
	}

	$async = openlab_clone_async_process();
	$async->data( [ 'group_id' => $group_id ] )->dispatch();
}
add_action( 'bp_actions', 'openlab_cleanup_clone_async_processes' );
