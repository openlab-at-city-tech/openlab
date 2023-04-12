<?php

/**
 * Stats reporting for openlab.citytech.cuny.edu
 */

class OpenLab_Stats {
	protected $term;
	protected $terms;
	protected $year;
	protected $start_date;
	protected $end_date;

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! is_super_admin() ) {
			return;
		}

		$this->term = 'Fall';
		$this->year = 2013;
		$this->start_date = '2013-08-28 00:00:00';
		$this->end_date = '2014-08-27 23:59:59';

		$this->terms = array(
			'fall-2013' => array(
				'name' => 'Fall 2013',
				'term' => 'Fall',
				'year' => 2014,
				'start' => '2014-08-28 00:00:00',
				'end' => '2014-12-31 23:59:59',
			),
			'winter-2014' => array(
				'name' => 'Winter 2014',
				'term' => 'Winter',
				'year' => 2014,
				'start' => '2014-01-01 00:00:00',
				'end' => '2014-01-26 23:59:59',
			),
			'spring-2014' => array(
				'name' => 'Spring 2014',
				'term' => 'Spring',
				'year' => 2014,
				'start' => '2014-01-27 00:00:00',
				'end' => '2014-05-29 23:59:59',
			),
			'summer-2014' => array(
				'name' => 'Summer 2014',
				'term' => 'Summer',
				'year' => 2014,
				'start' => '2014-05-30 00:00:00',
				'end' => '2014-08-27 23:59:59',
			),
		);

		add_action( 'network_admin_menu', array( $this, 'setup' ) );
	}

	public function setup() {
		if ( isset( $_POST['eportfolio-create'] ) ) {
			$this->create_report_eportfolio();
		}

		if ( isset( $_POST['active-course-create'] ) ) {
			$this->create_report_active_course();
		}

		add_submenu_page(
			'settings.php',
			'OpenLab Stats',
			'OpenLab Stats',
			'delete_users',
			'openlab-stats',
			array( $this, 'panel' )
		);
	}

	public function panel() {
		?>
		<div class="wrap">
			<h2>OpenLab Stats</h2>
		</div>

		<p>For the period: <strong><?php echo date( 'F d, Y', strtotime( $this->start_date ) ) ?> - <?php echo date( 'F d, Y', strtotime( $this->end_date ) ) ?></strong></p>

		<form action="" method="post">
			1) For each student ePortfolio created in period <?php echo $this->start_date ?>-<?php echo $this->end_date ?>: First Name, Last Name, Email Address, Date ePF Created, Last Activity (posts or comments).
			<input name="eportfolio-create" type="submit" value="Create Report" />
		</form>

		<form action="" method="post">
			2) For each course in the time period: Semester, School, Department, Course Name, Course Code, Section Number, Faculty Name, Activity (see below), Course Profile link.
			<input name="active-course-create" type="submit" value="Create Report" />
		</form>
		<?php
	}

	private function create_report_active_course() {
		global $wpdb, $bp;

		// Get the qualifying courses
		$term_sql_chunks = array();
		foreach ( $this->terms as $term ) {
			$term_sql_chunks[] = $wpdb->prepare( "( gm2.meta_value = %s AND gm3.meta_value = %d )", $term['term'], $term['year'] );
		}
		$term_sql = implode( ' OR ', $term_sql_chunks );

		$course_sql = "SELECT g.id, u.display_name as faculty_name, g.name, g.slug, gm2.meta_value AS semester, gm3.meta_value AS year
			FROM
			  {$bp->groups->table_name} g
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm
			  ON
			  (g.id = gm.group_id)
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm2
			  ON
			  (g.id = gm2.group_id)
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm3
			  ON
			  (g.id = gm3.group_id)
			  JOIN
			  {$wpdb->users} u
			  ON
			  (g.creator_id = u.ID)
			WHERE
			  gm.meta_key = 'wds_group_type'
			  AND
			  gm.meta_value = 'course'
			  AND
			  gm2.meta_key = 'wds_semester'
			  AND
			  gm3.meta_key = 'wds_year'
			  AND
			  (
			    $term_sql
			  )";

		$courses = $wpdb->get_results( $course_sql );

		// Append courses that are not labeled as being in one of these
		// semesters, but active during the time.
		$course_ids = wp_list_pluck( $courses, 'id' );
		$active = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT item_id
			 FROM {$bp->activity->table_name}
			 WHERE
			   component = 'groups'
			   AND
			   item_id NOT IN (" . implode( ',', $course_ids ) . ")
			   AND
			   date_recorded > %s
			   AND
			   date_recorded < %s
			", $this->start_date, $this->end_date
		) );

		if ( empty( $active ) ) {
			$active = array( 0 );
		}

		$other_course_sql = "SELECT g.id, u.display_name as faculty_name, g.name, g.slug, gm2.meta_value AS semester, gm3.meta_value AS year
			FROM
			  {$bp->groups->table_name} g
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm
			  ON
			  (g.id = gm.group_id)
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm2
			  ON
			  (g.id = gm2.group_id)
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm3
			  ON
			  (g.id = gm3.group_id)
			  JOIN
			  {$wpdb->users} u
			  ON
			  (g.creator_id = u.ID)
			WHERE
			  gm.meta_key = 'wds_group_type'
			  AND
			  gm.meta_value = 'course'
			  AND
			  gm2.meta_key = 'wds_semester'
			  AND
			  gm3.meta_key = 'wds_year'
			  AND
			  g.id IN (" . implode( ',', $active ) . ")";

		$other_courses = $wpdb->get_results( $other_course_sql );

		$courses = array_merge( $courses, $other_courses );

		// Start the canonical data file
		$cdata = array(
			array(
				'semester' => 'Semester',
				'school' => 'School',
				'department' => 'Department',
				'course_name' => 'Course Name',
				'course_code' => 'Course Code',
				'section_number' => 'Section Number',
				'faculty_name' => 'Faculty Name',
				'active' => 'Activity Count',
				'course_profile_link' => 'Course Profile Link',
				'external_site_url' => 'External Site URL',
				'last_active' => 'Last Active',
			),
		);

		foreach ( $courses as $course ) {
			$cdata[ $course->id ] = array(
				'semester' => trim( $course->semester . ' ' . $course->year ),
				'school' => '',
				'department' => '',
				'course_name' => trim( $course->name ),
				'course_code' => '',
				'section_number' => '',
				'faculty_name' => trim( $course->faculty_name ),
				'active' => '',
				'course_profile_link' => 'http://openlab.citytech.cuny.edu/groups/' . $course->slug,
				'external_site_url' => openlab_get_external_site_url_by_group_id( $course->id ),
				'last_active' => $wpdb->get_var( $wpdb->prepare( "SELECT date_recorded FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id = %d ORDER BY date_recorded DESC LIMIT 1", $course->id ) ),
			);
		}

		// Check for activity
		foreach ( $cdata as $cid => $c ) {
			// skip first row
			if ( ! $cid ) {
				continue;
			}

			$cdata[ $cid ]['active'] = $this->get_activity_count( $cid );
		}

		// Get extra data
		$course_ids = array_keys( $cdata );
		$course_ids_sql = implode( ',', $course_ids );

		$course_data = $wpdb->get_results(
			"SELECT group_id, meta_key, meta_value
			 FROM
			 {$bp->groups->table_name_groupmeta}
			 WHERE meta_key IN ('wds_group_school', 'wds_departments', 'wds_course_code', 'wds_section_code')
			 AND group_id IN ({$course_ids_sql})
			"
		);

		foreach ( $course_data as $cd ) {
			$k = null;
			$v = '';
			switch ( $cd->meta_key ) {
				case 'wds_group_school' :
					$k = 'school';

					switch ( $cd->meta_value ) {
						case 'arts' :
							$v = 'SoAS';
							break;
						case 'studies' :
							$v = 'SoPS';
							break;
						case 'tech' :
							$v = 'SoTD';
							break;
						default :
							$v = $cd->meta_value;
							break;
					}

					break;
				case 'wds_departments' :
					$k = 'department';
					$v = $cd->meta_value;
					break;
				case 'wds_course_code' :
					$k = 'course_code';
					$v = $cd->meta_value;
					break;
				case 'wds_section_code' :
					$k = 'section_number';
					$v = $cd->meta_value;
					break;
			}

			if ( $k ) {
				$cdata[ $cd->group_id ][ $k ] = trim( $v );
			}
		}
		//echo '<pre>';
		//print_r( $cdata ); die();

		$filename = 'openlab-course-data-' . date( 'Y-m-d', strtotime( $this->start_date ) ) . '--' . date( 'Y-m-d', strtotime( $this->end_date ) ) . '.csv';

		$this->send_csv( $cdata, $filename );

	}

	/**
	 * Is a group active?
	 *
	 * - If it's never had any activity aside from group creation and
	 *   new group members, then NO
	 * - If it's had less than 10 activity items, then MAYBE
	 * - If it's had more than 10, then YES
	 */
	private function get_activity_count( $cid ) {
		global $wpdb, $bp;

		$acount = intval( $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(id)
			 FROM {$bp->activity->table_name}
			 WHERE
			   component = 'groups'
			   AND
			   item_id = %d
			   AND
			   type NOT IN ('created_group', 'joined_group')
			   AND
			   date_recorded > %s
			   AND
			   date_recorded < %s
			",
			$cid, $this->start_date, $this->end_date
		) ) );

		// Activity comments won't appear in the stream.
		// This is a problem with the "parent activity" lookup in bp_blogs_record_comment(), not accounting for
		// the way we modify the blog activity item for group-blogs. See openlab_group_blog_activity()>
		$blog_id = openlab_get_site_id_by_group_id( $cid );
		if ( $blog_id ) {
			$prefix = $wpdb->get_blog_prefix( $blog_id );
			$ccount = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(comment_ID)
				 FROM {$prefix}comments
				 WHERE
				  comment_approved = 1
				  AND
				  comment_date > %s
				  AND
				  comment_date < %s
				", $this->start_date, $this->end_date
			) ) );

			$acount += $ccount;
		}


		return $acount;

		$retval = 'yes';

		if ( 0 === $acount ) {
			$retval = 'no';
		} else if ( 10 >= $acount ) {
			$retval = 'maybe';
		}

		return $retval;
	}

	private function create_report_eportfolio() {
		global $wpdb, $bp;

		$at_field = xprofile_get_field_id_from_name( 'Account Type' );
		$portfolios_sql = $wpdb->prepare(
			"SELECT g.id, g.creator_id, g.date_created
			FROM
			  {$bp->groups->table_name} g
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm
			  ON
			  (g.id = gm.group_id)
			  JOIN
			  {$bp->profile->table_name_data} p
			  on
			  (g.creator_id = p.user_id)
			WHERE
			  gm.meta_key = 'wds_group_type'
			  AND
			  gm.meta_value = 'portfolio'
			  AND
			  g.date_created > %s
			  AND
			  g.date_created < %s
			  AND
			  p.field_id = %d
			  AND
			  p.value = 'Student'
			"
		, $this->start_date, $this->end_date, $at_field );

		$portfolios = $wpdb->get_results( $portfolios_sql );

		$created_ids = implode( ',', wp_list_pluck( $portfolios, 'id' ) );
		$active_sql = $wpdb->prepare(
			"SELECT g.id, g.creator_id, g.date_created
			FROM
			  {$bp->groups->table_name} g
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm
			  ON
			  (g.id = gm.group_id)
			  JOIN
			  {$bp->profile->table_name_data} p
			  on
			  (g.creator_id = p.user_id)
			  JOIN
			  {$bp->groups->table_name_groupmeta} gm2
			  ON
			  (g.id = gm2.group_id)
			WHERE
			  gm.meta_key = 'wds_group_type'
			  AND
			  gm.meta_value = 'portfolio'
			  AND
			  gm2.meta_key = 'last_activity'
			  AND
			  gm2.meta_value > %s
			  AND
			  gm2.meta_value < %s
			  AND
			  g.id NOT IN ({$created_ids})
			  AND
			  p.field_id = %d
			  AND
			  p.value = 'Student'
			"
		, $this->start_date, $this->end_date, $at_field );
		$active = $wpdb->get_results( $active_sql );

		$portfolios = array_merge( $portfolios, $active );

		$creator_ids = wp_list_pluck( $portfolios, 'creator_id' );
		$creator_ids_sql = implode( ',', wp_parse_id_list( $creator_ids ) );

		$fn_field = xprofile_get_field_id_from_name( 'First Name' );
		$ln_field = xprofile_get_field_id_from_name( 'Last Name' );
		$creator_data = $wpdb->get_results( $wpdb->prepare(
			"SELECT u.ID, u.user_email, u.user_registered, p.field_id, p.value
			 FROM
			   {$wpdb->users} u
			   JOIN
			   {$bp->profile->table_name_data} p
			   ON
			   (u.ID = p.user_id)
			 WHERE
			   p.field_id IN (%d, %d)
			   AND
			   u.ID IN ({$creator_ids_sql})
			",
			$fn_field,
			$ln_field
		) );

		// reorganize
		$cdata = array();
		foreach ( $creator_data as $cd ) {
			if ( ! isset( $cdata[ $cd->ID ] ) ) {
				$cdata[ $cd->ID ] = array();
			}

			$cdata[ $cd->ID ]['email'] = $cd->user_email;

			if ( $cd->field_id == $fn_field ) {
				$cdata[ $cd->ID ]['first_name'] = $cd->value;
			}

			if ( $cd->field_id == $ln_field ) {
				$cdata[ $cd->ID ]['last_name'] = $cd->value;
			}

			$cdata[ $cd->ID ]['user_registered'] = $cd->user_registered;
		}

		// Assemble the beast
		$pdata = array(
			array(
				'First Name',
				'Last Name',
				'Email',
				'ePortfolio Date Created',
				'User Date Registered',
				'ePortfolio URL',
				'Last Active (posts or comments)',
			),
		);

		foreach ( $portfolios as $p ) {
			$userdata = $cdata[ $p->creator_id ];

			$last_activity = groups_get_groupmeta( $p->id, 'last_activity' );
			if ( ! $last_activity ) {
				$last_activity = $p->date_created;
			}

			$pdata[] = array(
				'first_name' => $userdata['first_name'],
				'last_name' => $userdata['last_name'],
				'email' => $userdata['email'],
				'date_created' => $p->date_created,
				'user_registered' => $userdata['user_registered'],
				'portfolio_url' => openlab_get_group_site_url( $p->id ),
				'last_active' => $last_activity,
			);
		}

		$filename = 'openlab-eportfolio-data-' . date( 'Y-m-d', strtotime( $this->start_date ) ) . '--' . date( 'Y-m-d', strtotime( $this->end_date ) ) . '.csv';

		$this->send_csv( $pdata, $filename );
	}

	private function send_csv( $data, $filename ) {
		header( 'Content-type: application/ms-excel' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$fp = fopen( "php://output", "w" );

		foreach ( $data as $d ) {
			fputcsv( $fp, $d );
		}

		fclose( $fp );
		exit;
	}
}

function openlab_stats() {
	new OpenLab_Stats;
}
add_action( 'init', 'openlab_stats' );
