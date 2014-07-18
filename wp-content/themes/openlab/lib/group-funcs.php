<?php
/**
 * Library of group-related functions
 *
 */

/**
 * This function consolidates the group privacy settings in one spot for easier updating
 *
 */
function openlab_group_privacy_settings( $group_type ) {
	global $bp;

	$group_type_name    = $group_type;
	$group_type_name_uc = ucfirst( $group_type );

	if ( 'portfolio' === $group_type ) {
		$group_type_name = openlab_get_portfolio_label( array(
			'group_id' => bp_get_current_group_id(),
		) );
		$group_type_name_uc = openlab_get_portfolio_label( array(
			'group_id' => bp_get_current_group_id(),
			'case' => 'upper',
		) );
	}

	// If this is a cloned group/site, fetch the clone source's details
	$clone_source_group_status = $clone_source_blog_status = '';
	if ( bp_is_group_create() ) {
		$new_group_id = bp_get_new_group_id();
		if ( 'course' === $group_type ) {
			$clone_source_group_id = groups_get_groupmeta( $new_group_id, 'clone_source_group_id' );
			$clone_source_site_id = groups_get_groupmeta( $new_group_id, 'clone_source_blog_id' );

			$clone_source_group = groups_get_group( array( 'group_id' => $clone_source_group_id ) );
			$clone_source_group_status = $clone_source_group->status;

			$clone_source_blog_status = get_blog_option( $clone_source_site_id, 'blog_public' );
		}
	}
	?>
	<h4 class="privacy-title"><?php _e( 'Privacy Settings', 'buddypress' ); ?></h4>
	<p class="privacy-settings-tag-a">Set privacy options for your <?php echo $group_type_name_uc ?></p>
	<?php if ( $bp->current_action == 'admin' || $bp->current_action == 'create' || openlab_is_portfolio() ): ?>
		<h5><?php echo $group_type_name_uc ?> Profile</h5>
	<?php endif; ?>

	<?php if ( $bp->current_action == 'create' ): ?>
		<p id="privacy-settings-tag-b"><?php _e( 'To change these settings later, use the ' . $group_type_name . ' Profile Settings page.', 'buddypress' ); ?></p>
	<?php else: ?>
		<p class="privacy-settings-tag-c"><?php _e( 'These settings affect how others view your ' . $group_type_name_uc . ' Profile.' ) ?></p>
	<?php endif; ?>

	<div class="radio group-profile">

		<?php
		$new_group_status = bp_get_new_group_status();
		if ( !$new_group_status ) {
			$new_group_status = !empty( $clone_source_group_status ) ? $clone_source_group_status : 'public';
		}
		?>

		<label>
			<input type="radio" name="group-status" value="public" <?php checked( 'public', $new_group_status ) ?> />
			<strong>This is a public <?php echo $group_type_name_uc ?></strong>
			<ul>
				<li>This <?php echo $group_type_name_uc ?> Profile and related content and activity will be visible to the public.</li>
				<li><?php _e( 'This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . 's directory, search results, and may be displayed on the OpenLab home page.', 'buddypress' ) ?></li>
				<li><?php _e( 'Any OpenLab member may join this ' . $group_type_name_uc . '.', 'buddypress' ) ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="private" <?php checked( 'private', $new_group_status ) ?> />
			<strong><?php _e( 'This is a private ' . $group_type_name_uc, 'buddypress' ) ?></strong>
			<ul>
				<li><?php _e( 'This ' . $group_type_name_uc . ' Profile and related content and activity will only be visible to members of the group.', 'buddypress' ) ?></li>
				<li><?php _e( 'This ' . $group_type_name_uc . ' will be listed in the ' . $group_type_name_uc . ' directory, search results, and may be displayed on the OpenLab home page.', 'buddypress' ) ?></li>
				<li><?php _e( 'Only OpenLab members who request membership and are accepted may join this ' . $group_type_name_uc . '.', 'buddypress' ) ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="hidden" <?php checked( 'hidden', $new_group_status ) ?> />
			<strong><?php _e( 'This is a hidden ' . $group_type_name_uc, 'buddypress' ) ?></strong>
			<ul>
				<li><?php _e( 'This ' . $group_type_name_uc . ' Profile, related content and activity will only be visible only to members of the ' . $group_type_name_uc . '.', 'buddypress' ) ?></li>
				<li><?php _e( 'This ' . $group_type_name_uc . ' Profile will NOT be listed in the ' . $group_type_name_uc . ' directory, search results, or OpenLab home page.', 'buddypress' ) ?></li>
				<li><?php _e( 'Only OpenLab members who are invited may join this ' . $group_type_name_uc . '.', 'buddypress' ) ?></li>
			</ul>
		</label>
	</div>

	<?php /* Site privacy markup */ ?>

	<?php if ( $site_id = openlab_get_site_id_by_group_id() ) : ?>
		<h5><?php _e( $group_type_name_uc . ' Site' ) ?></h5>
		<p class="privacy-settings-tag-c"><?php _e( 'These settings affect how others view your ' . $group_type_name_uc . ' Site.' ) ?></p>
		<?php openlab_site_privacy_settings_markup( $site_id ) ?>
	<?php endif ?>

	<?php if ( $bp->current_action == 'admin' ): ?>
		<?php do_action( 'bp_after_group_settings_admin' ); ?>
		<p><input type="submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?> &rarr;" id="save" name="save" /></p>
		<?php wp_nonce_field( 'groups_edit_group_settings' ); ?>
	<?php elseif ( $bp->current_action == 'create' ): ?>
		<?php wp_nonce_field( 'groups_create_save_group-settings' ) ?>
		<?php
	endif;
}

/**
 * This function outputs the full archive for a specific group, currently delineated by the archive page slug
 *
 */
function openlab_group_archive() {
	global $wpdb, $bp, $groups_template, $post;

	if ( ! bp_is_active( 'groups' ) ) {
		return;
	}

//geting the grouptype by slug - the archive pages are curently WP pages and don't have a specific grouptype associated with them - this function uses the curent page slug to assign a grouptype
//@to-do - get the archive page in the right spot to function correctly within the BP framework
	$group_type = openlab_page_slug_to_grouptype();

	$sequence_type = '';
	if ( !empty( $_GET['group_sequence'] ) ) {
		$sequence_type = "type=" . $_GET['group_sequence'] . "&";
	}

	$search_terms = $search_terms_raw = '';

	if ( !empty( $_POST['group_search'] ) ) {
		$search_terms_raw = $_POST['group_search'];
		$search_terms = "search_terms=" . $search_terms_raw . "&";
	}
	if ( !empty( $_GET['search'] ) ) {
		$search_terms_raw = $_GET['search'];
		$search_terms = "search_terms=" . $search_terms_raw . "&";
	}

	if ( !empty( $_GET['school'] ) ) {
		$school = $_GET['school'];
		/* if ( $school=="tech" ) {
          $school="Technology & Design";
          } elseif ( $school=="studies" ) {
          $school="Professional Studies";
          } elseif ( $school=="arts" ) {
          $school="Arts & Sciences";
          } */
	}

	if ( !empty( $_GET['department'] ) ) {
		$department = str_replace( "-", " ", $_GET['department'] );
		$department = ucwords( $department );
	}
	if ( !empty( $_GET['semester'] ) ) {
		$semester = str_replace( "-", " ", $_GET['semester'] );
		$semester = explode( " ", $semester );
		$semester_season = ucwords( $semester[0] );
		$semester_year = ucwords( $semester[1] );
		$semester = trim( $semester_season . ' ' . $semester_year );
	}

// Set up filters
	$meta_query = array(
		array(
			'key' => 'wds_group_type',
			'value' => $group_type,
		),
	);

	if ( !empty( $school ) && 'school_all' != strtolower( $school ) ) {
		$meta_query[] = array(
			'key' => 'wds_group_school',
			'value' => $school,
			'compare' => 'LIKE',
		);
	}

	if ( !empty( $department ) && 'dept_all' != strtolower( $department ) ) {
		$meta_query[] = array(
			'key' => 'wds_departments',
			'value' => $department,
			'compare' => 'LIKE',
		);
	}

	if ( !empty( $semester ) && 'semester_all' != strtolower( $semester ) ) {
		$meta_query[] = array(
			'key' => 'wds_semester',
			'value' => $semester_season,
		);
		$meta_query[] = array(
			'key' => 'wds_year',
			'value' => $semester_year,
		);
	}

	if ( !empty( $_GET['usertype'] ) && 'user_type_all' != $_GET['usertype'] ) {
		$meta_query[] = array(
			'key' => 'portfolio_user_type',
			'value' => ucwords( $_GET['usertype'] ),
		);
	}

	$group_args = array(
		'search_terms' => $search_terms_raw,
		'per_page' => 12,
		'meta_query' => $meta_query,
	);

	if ( !empty( $_GET['group_sequence'] ) ) {
		$group_args['type'] = $_GET['group_sequence'];
	}

	if ( bp_has_groups( $group_args ) ) :
		?>
		<div class="current-group-filters current-portfolio-filters">
		<?php openlab_current_directory_filters(); ?>
		</div>
		<div class="group-count"><?php cuny_groups_pagination_count( ucwords( $group_type ) . 's' ); ?></div>
		<div class="clearfloat"></div>
		<ul id="group-list" class="item-list">
			<?php
			$count = 1;
			while ( bp_groups() ) : bp_the_group( );
				$group_id = bp_get_group_id();
				?>
				<li class="course<?php echo cuny_o_e_class( $count ) ?>">
					<div class="item-avatar alignleft">
						<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar( array( 'type' => 'full', 'width' => 100, 'height' => 100 ) ) ?></a>
					</div>
					<div class="item">

						<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name( ) ?>"><?php bp_group_name( ) ?></a></h2>
						<?php
						//course group type
						if ( $group_type == 'course' ):
							?>

							<?php
							$admins = groups_get_group_admins( $group_id );
							$faculty_id = $admins[0]->user_id;
							$first_name = ucfirst( xprofile_get_field_data( 'First Name', $faculty_id ) );
							$last_name = ucfirst( xprofile_get_field_data( 'Last Name', $faculty_id ) );
							$wds_faculty = $first_name . " " . $last_name;
							$wds_course_code = groups_get_groupmeta( $group_id, 'wds_course_code' );
							$wds_semester = groups_get_groupmeta( $group_id, 'wds_semester' );
							$wds_year = groups_get_groupmeta( $group_id, 'wds_year' );
							$wds_departments = groups_get_groupmeta( $group_id, 'wds_departments' );
							?>
							<div class="info-line"><?php echo $wds_faculty; ?> | <?php echo openlab_shortened_text( $wds_departments, 20 ); ?> | <?php echo $wds_course_code; ?><br /> <?php echo $wds_semester; ?> <?php echo $wds_year; ?></div>
						<?php elseif ( $group_type == 'portfolio' ): ?>

							<div class="info-line"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></div>

						<?php endif; ?>

						<?php
						$len = strlen( bp_get_group_description() );
						if ( $len > 135 ) {
							$this_description = substr( bp_get_group_description(), 0, 135 );
							$this_description = str_replace( "</p>", "", $this_description );
							echo $this_description . '&hellip; <a href="' . bp_get_group_permalink() . '">See&nbsp;More</a></p>';
						} else {
							bp_group_description();
						}
						?>
					</div><!--item-->

				</li>
				<?php
				if ( $count % 2 == 0 ) {
					echo '<hr style="clear:both;" />';
				}
				?>
				<?php $count++ ?>
		<?php endwhile; ?>
		</ul>

		<div class="pagination-links" id="group-dir-pag-top">
		<?php bp_groups_pagination_links() ?>
		</div>
		<?php else: ?>
		<div class="current-group-filters current-portfolio-filters">
		<?php openlab_current_directory_filters(); ?>
		</div>
		<div class="widget-error">
		<?php _e( 'There are no ' . $group_type . 's to display.', 'buddypress' ) ?>
		</div>

	<?php endif; ?>
	<?php
}

/*
 * Redirect to users profile after deleting a group
 */
add_action( 'groups_group_deleted', 'openlab_delete_group', 20 );

/**
 * After portfolio delete, redirect to user profile page
 */
function openlab_delete_group() {
	bp_core_redirect( bp_loggedin_user_domain() );
}

/**
 * This function prints out the departments for the course archives ( non ajax )
 *
 * @param string $school The id of the school to return a course list for
 * @param string $department The id of the deparment currently selected in
 *        the dropdown.
 */
function openlab_return_course_list( $school, $department ) {

	$list = '<option value="dept_all" ' . selected( '', $department ) . ' >All</option>';

	// Sanitize. If no value is found, don't return any
	// courses
	if ( ! in_array( $school, array( 'tech', 'studies', 'arts' ) ) ) {
		return $list;
	}

	$depts = openlab_get_department_list( $school, 'short' );

	foreach ( $depts as $dept_name => $dept_label ) {
		$list .= '<option value="' . esc_attr( $dept_name ) . '" ' . selected( $department, $dept_name, false ) . '>' . esc_attr( $dept_label ) . '</option>';
	}

	return $list;
}

function openlab_group_post_count( $filters, $group_args ) {

	$post_count = 0;

	$meta_filter = new BP_Groups_Meta_Filter( $filters );
	if ( bp_has_groups( $group_args ) ) :

		while ( bp_groups() ) : bp_the_group( );
			$post_count++;
		endwhile;

	endif;
	$meta_filter->remove_filters();

	return $post_count;
}
