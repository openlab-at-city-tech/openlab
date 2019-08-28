<?php
/*
Plugin Name: CityTech Networkwide Custom (wds-citytech)
Plugin URI: https://openlab.citytech.cuny.edu/
Description: Custom networkwide functionality for the City Tech OpenLab.
Version: 1.0
Author: City Tech OpenLab
Author URI: https://openlab.citytech.cuny.edu
 */

define( 'WDS_CITYTECH_DIR', dirname( __FILE__ ) );
define( 'WDS_CITYTECH_URL', plugin_dir_url( __FILE__ ) );

require 'wds-register.php';
require 'wds-docs.php';
require 'includes/oembed.php';
require 'includes/library-widget.php';
require 'includes/clone.php';
require 'includes/print-this-page.php';
require 'includes/license-widget.php';

/**
 * Loading BP-specific stuff in the global scope will cause issues during activation and upgrades
 * Ensure that it's only loaded when BP is present.
 * See http://openlab.citytech.cuny.edu/redmine/issues/31
 */
function openlab_load_custom_bp_functions() {
	require( dirname( __FILE__ ) . '/wds-citytech-bp.php' );
	require( dirname( __FILE__ ) . '/includes/email.php' );
	require( dirname( __FILE__ ) . '/includes/groupmeta-query.php' );
	require( dirname( __FILE__ ) . '/includes/group-blogs.php' );
	require( dirname( __FILE__ ) . '/includes/group-types.php' );
	require( dirname( __FILE__ ) . '/includes/group-activity.php' );
	require( dirname( __FILE__ ) . '/includes/portfolios.php' );
	require( dirname( __FILE__ ) . '/includes/related-links.php' );
	require( dirname( __FILE__ ) . '/includes/search.php' );
}

add_action( 'bp_init', 'openlab_load_custom_bp_functions' );

global $wpdb;
//date_default_timezone_set( 'America/New_York' );

function wds_default_signup_avatar( $img ) {
	if ( false !== strpos( $img, 'mystery-man' ) ) {
		$img = "<img src='" . wds_add_default_member_avatar() . "' width='200' height='200'>";
	}

	return $img;
}
add_filter( 'bp_get_signup_avatar', 'wds_default_signup_avatar' );

/**
 * Get the stylesheet directory for the main site.
 */
function openlab_get_stylesheet_dir_uri() {
	return content_url( '/themes/openlab' );
}

/**
 * Custom mysteryman
 * @return type
 */
function openlab_new_mysteryman() {
	return openlab_get_stylesheet_dir_uri() . '/images/default-avatar.jpg';
}
add_filter( 'bp_core_mysteryman_src', 'openlab_new_mysteryman', 2, 7 );

/**
 * Custom default avatar
 * @param string $url
 * @param type $params
 * @return string
 */
function openlab_default_get_group_avatar( $url, $params ) {
	if ( strstr( $url, 'default-avatar' ) || strstr( $url, 'wavatar' ) || strstr( $url, 'mystery-group.png' ) ) {
		$url = openlab_get_stylesheet_dir_uri() . '/images/default-avatar.jpg';
	}

	return $url;
}
add_filter( 'bp_core_fetch_avatar_url', 'openlab_default_get_group_avatar', 10, 2 );

function openlab_default_group_avatar_img( $html ) {
	$default_avatar = buddypress()->plugin_url . 'bp-core/images/mystery-group.png';
	return str_replace( $default_avatar, openlab_get_stylesheet_dir_uri() . '/images/default-avatar.jpg', $html );
}
add_filter( 'bp_core_fetch_avatar', 'openlab_default_group_avatar_img' );

//
//   This function creates an excerpt of the string passed to the length specified and
//   breaks on a word boundary
//
function wds_content_excerpt( $text, $text_length ) {
	return bp_create_excerpt( $text, $text_length );
}

/**
 * On activation, copies the BP first/last name profile field data into the WP 'first_name' and
 * 'last_name' fields.
 *
 * @todo This should probably be moved to a different hook. This $last_user lookup is hackish and
 *       may fail in some edge cases. I believe the hook bp_activated_user is correct.
 */
function wds_bp_complete_signup() {
	global $bp, $wpdb;

	$last_user         = $wpdb->get_results( 'SELECT * FROM wp_users ORDER BY ID DESC LIMIT 1', 'ARRAY_A' );
	$user_id           = $last_user[0]['ID'];
	$first_name        = xprofile_get_field_data( 'First Name', $user_id );
	$last_name         = xprofile_get_field_data( 'Last Name', $user_id );
	$update_user_first = update_user_meta( $user_id, 'first_name', $first_name );
	$update_user_last  = update_user_meta( $user_id, 'last_name', $last_name );
}
add_action( 'bp_after_activation_page', 'wds_bp_complete_signup' );

/**
 * On secondary sites, add our additional buttons to the site nav
 *
 * This function filters wp_page_menu, which is what shows up when no custom
 * menu has been selected. See cuny_add_group_menu_items() for the
 * corresponding method for custom menus.
 */
function my_page_menu_filter( $menu ) {
	global $bp, $wpdb;

	if ( strpos( $menu, 'Home' ) !== false ) {
		$menu = str_replace( 'Site Home', 'Home', $menu );
		$menu = str_replace( 'Home', 'Site Home', $menu );
	} else {
		$menu = str_replace( '<div class="menu"><ul>', '<div class="menu"><ul><li><a title="Site Home" href="' . site_url() . '">Site Home</a></li>', $menu );
	}
	$menu = str_replace( 'Site Site Home', 'Site Home', $menu );

	// Only say 'Home' on the ePortfolio theme
	// @todo: This will probably get extended to all sites
	$menu = str_replace( 'Site Home', 'Home', $menu );

	$wds_bp_group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

	if ( $wds_bp_group_id ) {
		$group = groups_get_group( $wds_bp_group_id );
		if ( $group->is_visible ) {
			$group_type = ucfirst( groups_get_groupmeta( $wds_bp_group_id, 'wds_group_type' ) );
			$menu_a     = explode( '<ul>', $menu );
			$menu_a     = array(
				$menu_a[0],
				'<ul>',
				'<li id="group-profile-link" class="menu-item"><a title="Site" href="' . bp_get_root_domain() . '/groups/' . $group->slug . '/">' . $group_type . ' Profile</a></li>',
				$menu_a[1],
			);
			$menu       = implode( '', $menu_a );
		}
	}
	return $menu;
}
add_filter( 'wp_page_menu', 'my_page_menu_filter' );

//child theme menu filter to link to website
function cuny_add_group_menu_items( $items, $args ) {
	/**
	 * Allows individual themes to opt out.
	 */
	$allow = apply_filters( 'openlab_add_dynamic_nav_items', true, $args );
	if ( ! $allow ) {
		return $items;
	}

	if ( bp_is_root_blog() ) {
		return $items;
	}

	// Only add the Home link if one is not already found
	// See http://openlab.citytech.cuny.edu/redmine/isues/1031
	$has_home = false;
	foreach ( $items as $item ) {
		if ( 'Home' === $item->title && trailingslashit( site_url() ) === trailingslashit( $item->url ) ) {
			$has_home = true;
			break;
		}
	}

	if ( ! $has_home ) {
		$post_args          = new stdClass();
		$home_link          = new WP_Post( $post_args );
		$home_link->title   = 'Home';
		$home_link->url     = trailingslashit( site_url() );
		$home_link->slug    = 'home';
		$home_link->ID      = 'home';
		$home_link->classes = [ 'menu-item', 'menu-item-home' ];
		$items              = array_merge( array( $home_link ), $items );
	}

	$items = array_merge( cuny_group_menu_items(), $items );

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );

/**
 * Disable the filter above when we're in a sidebar.
 */
add_action(
	'dynamic_sidebar_before',
	function() {
		remove_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );

		add_action(
			'dynamic_sidebar_after',
			function() {
				add_filter( 'wp_nav_menu_objects', 'cuny_add_group_menu_items', 10, 2 );
			}
		);
	},
	0
);

function cuny_group_menu_items() {
	global $bp, $wpdb;

	$items = array();

	$wds_bp_group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

	if ( $wds_bp_group_id ) {
		$group = groups_get_group( $wds_bp_group_id );
		if ( $group->is_visible ) {
			$group_type = ucfirst( groups_get_groupmeta( $wds_bp_group_id, 'wds_group_type' ) );

			$post_args             = new stdClass();
			$profile_item          = new WP_Post( $post_args );
			$profile_item->ID      = 'group-profile-link';
			$profile_item->title   = sprintf( '%s Profile', $group_type );
			$profile_item->slug    = 'group-profile-link';
			$profile_item->url     = bp_get_group_permalink( $group );
			$profile_item->classes = [ 'menu-item', 'menu-item-group-profile-link' ];

			$items[] = $profile_item;
		}
	}

	return $items;
}

//Default BP Avatar Full
if ( ! defined( 'BP_AVATAR_FULL_WIDTH' ) ) {
	define( 'BP_AVATAR_FULL_WIDTH', 225 );
}

if ( ! defined( 'BP_AVATAR_FULL_HEIGHT' ) ) {
	define( 'BP_AVATAR_FULL_HEIGHT', 225 );
}

/**
 * Don't let child blogs use bp-default or a child thereof
 *
 * @todo Why isn't this done by network disabling BP Default and its child themes?
 * @todo Why isn't BP_DISABLE_ADMIN_BAR defined somewhere like bp-custom.php?
 */
function wds_default_theme() {
	global $wpdb, $blog_id;
	if ( $blog_id > 1 ) {
		if ( ! defined( 'BP_DISABLE_ADMIN_BAR' ) ) {
					define( 'BP_DISABLE_ADMIN_BAR', true );
		}
		$theme = get_option( 'template' );
		if ( 'bp-default' === $theme ) {
			switch_theme( 'twentyten', 'twentyten' );
			wp_redirect( home_url() );
			exit();
		}
	}
}
add_action( 'init', 'wds_default_theme' );

//register.php -hook for new div to show account type fields
add_action( 'bp_after_signup_profile_fields', 'wds__bp_after_signup_profile_fields' );

function wds__bp_after_signup_profile_fields() {
	?>
	<div class="editfield"><div id="wds-account-type" aria-live="polite"></div></div>
	<?php
}

function wds_registration_ajax() {
	wp_print_scripts( array( 'sack' ) );
	$sack    = 'var isack = new sack( "' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php" );';
	$loading = '<img src="' . get_bloginfo( 'template_directory' ) . '/_inc/images/ajax-loader.gif">';
	?>
	<?php
}
add_action( 'wp_head', 'wds_registration_ajax' );

function wds_load_default_account_type() {
	$return = '<script type="text/javascript">';

	$account_type   = isset( $_POST['field_7'] ) ? $_POST['field_7'] : '';
	$type           = '';
	$selected_index = '';

	if ( 'Student' === $account_type ) {
		$type           = 'Student';
		$selected_index = 1;
	}

	if ( 'Faculty' === $account_type ) {
		$type           = 'Faculty';
		$selected_index = 2;
	}

	if ( 'Staff' === $account_type ) {
		$type           = 'Staff';
		$selected_index = 3;
	}

	if ( $type && $selected_index ) {
		$return .= 'var select_box=document.getElementById( \'field_7\' );';
		$return .= 'select_box.selectedIndex = ' . $selected_index . ';';
	}
	$return .= '</script>';
	echo $return;
}
add_action( 'bp_after_registration_submit_buttons', 'wds_load_default_account_type' );

function wds_load_account_type() {
	$return = '';

	$account_type = $_POST['account_type'];
	$post_data    = isset( $_POST['post_data'] ) ? wp_unslash( $_POST['post_data'] ) : array();

	if ( $account_type ) {
		$return .= wds_get_register_fields( $account_type, $post_data );
		$return .= "<div class='sr-only'>" . $account_type . ' selected.</div>';
	} else {
		$return = 'Please select an Account Type.';
	}
		//@to-do: determine why this is here, and if it can be deprecated
	//$return = str_replace( "'", "\'", $return );
	die( $return );
}
add_action( 'wp_ajax_wds_load_account_type', 'wds_load_account_type' );
add_action( 'wp_ajax_nopriv_wds_load_account_type', 'wds_load_account_type' );

function wds_bp_profile_group_tabs() {
	global $bp, $group_name;

	$groups = wp_cache_group( 'xprofile_groups_inc_empty', 'bp' );
	if ( false === $groups ) {
		$groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		wp_cache_set( 'xprofile_groups_inc_empty', $groups, 'bp' );
	}

	if ( empty( $group_name ) ) {
		$group_name = bp_profile_group_name( false );
	}

	for ( $i = 0; $i < count( $groups ); $i++ ) {
		if ( $group_name == $groups[ $i ]->name ) {
			$selected = ' class="current"';
		} else {
			$selected = '';
		}

		$account_type = bp_get_profile_field_data( 'field=Account Type' );
		if ( $groups[ $i ]->fields ) {
			echo '<li' . $selected . '><a href="' . $bp->displayed_user->domain . $bp->profile->slug . '/edit/group/' . $groups[ $i ]->id . '">' . esc_attr( $groups[ $i ]->name ) . '</a></li>';
		}
	}

	do_action( 'xprofile_profile_group_tabs' );
}

function wds_load_group_departments() {
	global $wpdb, $bp;
	$group           = $_POST['group'];
	$schools         = $_POST['schools'];
	$group_type      = $_POST['group_type'];
	$is_group_create = (bool) $_POST['is_group_create'];
	$schools         = str_replace( '0,', '', $schools );
	$schools         = explode( ',', $schools );

	$schools_list     = '';
	$schools_list_ary = array();

	$schools_canonical = openlab_get_school_list();
	foreach ( $schools as $school ) {
		if ( isset( $schools_canonical[ $school ] ) ) {
			array_push( $schools_list_ary, $schools_canonical[ $school ] );
		}
	}

	$schools_list = implode( ', ', $schools_list_ary );

	$departments_canonical = openlab_get_department_list();

	// We want to prefill the School and Dept fields, which means we have
	// to prefetch the dept field and figure out School backward
	if ( 'portfolio' == strtolower( $group_type ) && $is_group_create ) {
		$account_type = strtolower(
			bp_get_profile_field_data(
				array(
					'field'   => 'Account Type',
					'user_id' => bp_loggedin_user_id(),
				)
			)
		);
		$dept_field   = 'student' == $account_type ? 'Major Program of Study' : 'Department';

		$wds_departments = (array) bp_get_profile_field_data(
			array(
				'field'   => $dept_field,
				'user_id' => bp_loggedin_user_id(),
			)
		);

		foreach ( $wds_departments as $d ) {
			foreach ( $departments_canonical as $the_school => $the_depts ) {
				if ( in_array( $d, $the_depts, true ) ) {
					$schools[] = $the_school;
				}
			}
		}
	}

	$departments = array();
	foreach ( $schools as $school ) {
		if ( isset( $departments_canonical[ $school ] ) ) {
			$departments = array_merge( $departments, $departments_canonical[ $school ] );
		}
	}
	sort( $departments );

	if ( 'portfolio' == strtolower( $group_type ) && $is_group_create ) {
		$wds_departments = (array) bp_get_profile_field_data(
			array(
				'field'   => $dept_field,
				'user_id' => bp_loggedin_user_id(),
			)
		);
	} else {
		$wds_departments = groups_get_groupmeta( $group, 'wds_departments' );
		$wds_departments = explode( ',', $wds_departments );
	}

	$return = '<div class="department-list-container checkbox-list-container"><div class="sr-only">' . $schools_list . ' selected</div>';
	foreach ( $departments as $i => $value ) {
		$checked = '';
		if ( in_array( $value, $wds_departments ) ) {
			$checked = 'checked';
		}
		$return .= "<label class='passive block'><input type='checkbox' class='wds-department' name='wds_departments[]' value='$value' $checked> $value</label>";
	}

	$return .= '</div>';
	$return  = str_replace( "'", "\'", $return );
	die( "document.getElementById( 'departments_html' ).innerHTML='$return'" );
}
add_action( 'wp_ajax_wds_load_group_departments', 'wds_load_group_departments' );

/**
 * Get a list of schools
 */
function openlab_get_school_list() {
	return array(
		'arts'    => 'Arts & Sciences',
		'studies' => 'Professional Studies',
		'tech'    => 'Technology & Design',
		'other'   => 'Other',
	);
}

/**
 * Get a list of departments
 *
 * @param str Optional. Leave out to get all departments
 */
function openlab_get_department_list( $school = '', $label_type = 'full' ) {
	// Sanitize school name
	$schools = openlab_get_school_list();
	if ( isset( $schools[ $school ] ) ) {
		$school = $school;
	} elseif ( in_array( $school, $schools ) ) {
		$school = array_search( $school, $schools );
	}

	// Lazy - I didn't feel like manually converting to key-value structure
	$departments_sorted = array();
	foreach ( $schools as $s_key => $s_label ) {
		// Skip if we only want one school
		if ( $school && $s_key != $school ) {
			continue;
		}

		$departments_sorted[ $s_key ] = array();
	}

	if ( $school ) {
		$d_schools = array( $school );
	} else {
		$d_schools = array_keys( $schools );
	}

	foreach ( $d_schools as $d_school ) {
		$depts = openlab_get_entity_departments( $d_school );

		foreach ( $depts as $dept_name => $dept ) {
			if ( 'short' == $label_type ) {
				$d_label = isset( $dept['short_label'] ) ? $dept['short_label'] : $dept['label'];
			} else {
				$d_label = $dept['label'];
			}

			$departments_sorted[ $d_school ][ $dept_name ] = $d_label;
		}
	}

	if ( $school ) {
		$departments_sorted = $departments_sorted[ $school ];
	}

	return $departments_sorted;
}

/**
 * Returns a list of Offices.
 */
function openlab_get_office_list() {
	return array(
		'academic-affairs' => 'Academic Affairs',
		'administration'   => 'Administration & Finance',
		'president'        => 'President\'s Office',
		'student-affairs'  => 'Student Affairs & Enrollment Management',
	);
}

/**
 * Returns information about departments belonging to an entity (School or Office).
 *
 * @param string $entity Optional. Entity slug.
 */
function openlab_get_entity_departments( $entity = null ) {
	$all_departments = array(
		'tech'             => array(
			'architectural-technology'          => array(
				'label' => 'Architectural Technology',
			),
			'communication-design'              => array(
				'label' => 'Communication Design',
			),
			'computer-engineering-technology'   => array(
				'label' => 'Computer Engineering Technology',
			),
			'computer-systems-technology'       => array(
				'label' => 'Computer Systems Technology',
			),
			'construction-management-and-civil-engineering-technology' => array(
				'label'       => 'Construction Management and Civil Engineering Technology',
				'short_label' => 'Construction & Civil Engineering Tech',
			),
			'electrical-and-telecommunications-engineering-technology' => array(
				'label'       => 'Electrical and Telecommunications Engineering Technology',
				'short_label' => 'Electrical & Telecom Engineering Tech',
			),
			'entertainment-technology'          => array(
				'label' => 'Entertainment Technology',
			),
			'environmental-control-technology'  => array(
				'label' => 'Environmental Control Technology',
			),
			'mechanical-engineering-technology' => array(
				'label' => 'Mechanical Engineering Technology',
			),
		),
		'studies'          => array(
			'business'                                  => array(
				'label' => 'Business',
			),
			'career-and-technology-teacher-education'   => array(
				'label' => 'Career and Technology Teacher Education',
			),
			'dental-hygiene'                            => array(
				'label' => 'Dental Hygiene',
			),
			'health-services-administration'            => array(
				'label' => 'Health Services Administration',
			),
			'hospitality-management'                    => array(
				'label' => 'Hospitality Management',
			),
			'human-services'                            => array(
				'label' => 'Human Services',
			),
			'law-and-paralegal-studies'                 => array(
				'label' => 'Law and Paralegal Studies',
			),
			'nursing'                                   => array(
				'label' => 'Nursing',
			),
			'radiologic-technology-and-medical-imaging' => array(
				'label' => 'Radiologic Technology and Medical Imaging',
			),
			'restorative-dentistry'                     => array(
				'label' => 'Restorative Dentistry',
			),
			'vision-care-technology'                    => array(
				'label' => 'Vision Care Technology',
			),
		),
		'arts'             => array(
			'african-american-studies'           => array(
				'label' => 'African American Studies',
			),
			'biological-sciences'                => array(
				'label' => 'Biological Sciences',
			),
			'biomedical-informatics'             => array(
				'label' => 'Biomedical Informatics',
			),
			'chemistry'                          => array(
				'label' => 'Chemistry',
			),
			'english'                            => array(
				'label' => 'English',
			),
			'humanities'                         => array(
				'label' => 'Humanities',
			),
			'liberal-arts'                       => array(
				'label' => 'Liberal Arts & Sciences',
			),
			'library'                            => array(
				'label' => 'Library',
			),
			'mathematics'                        => array(
				'label' => 'Mathematics',
			),
			'professional-and-technical-writing' => array(
				'label' => 'Professional and Technical Writing',
			),
			'physics'                            => array(
				'label' => 'Physics',
			),
			'social-science'                     => array(
				'label' => 'Social Science',
			),
		),
		'other'            => array(
			'clip' => array(
				'label' => 'CLIP',
			),
		),
		'academic-affairs' => array(
			'adjunct-workload-management-office' => array(
				'label' => 'Adjunct Workload Management Office',
			),
			'air'                                => array(
				'label' => 'AIR',
			),
			'asap'                               => array(
				'label' => 'ASAP',
			),
			'bmi'                                => array(
				'label' => 'BMI',
			),
			'c-step'                             => array(
				'label' => 'C-Step',
			),
			'college-learning-center'            => array(
				'label' => 'College Learning Center',
			),
			'city-poly'                          => array(
				'label' => 'City Poly',
			),
			'college-now'                        => array(
				'label' => 'College Now',
			),
			'continuing-education'               => array(
				'label' => 'Continuing Education',
			),
			'faculty-commons'                    => array(
				'label' => 'Faculty Commons',
			),
			'first-year-programs'                => array(
				'label' => 'First Year Programs',
			),
			'honors-scholors'                    => array(
				'label' => 'Honors Scholars',
			),
			'instructional-technology'           => array(
				'label' => 'Instructional Technology',
			),
			'library'                            => array(
				'label' => 'Library',
			),
			'openlab'                            => array(
				'label' => 'OpenLab',
			),
			'provost'                            => array(
				'label' => 'Provost\'s Office',
			),
			'ptech'                              => array(
				'label' => 'PTECH',
			),
			'arts-dean'                          => array(
				'label' => 'School of Arts and Sciences, Dean’s Office',
			),
			'professional-dean'                  => array(
				'label' => 'School of Professional Studies, Dean’s Office',
			),
			'tech-dean'                          => array(
				'label' => 'School of Technology & Design, Dean’s Office',
			),
			'sponsored-programs'                 => array(
				'label' => 'Sponsored Programs',
			),
			'undergraduate-research'             => array(
				'label' => 'Undergraduate Research & Emerging Scholars',
			),
		),
		'student-affairs'  => array(
			'admissions'                       => array(
				'label' => 'Admissions',
			),
			'athletics'                        => array(
				'label' => 'Athletics & Recreation',
			),
			'center-for-student-accessibility' => array(
				'label' => 'Center for Student Accessibility',
			),
			'childcare-services'               => array(
				'label' => 'Childcare Services',
			),
			'cope'                             => array(
				'label' => 'COPE',
			),
			'counseling'                       => array(
				'label' => 'Counseling',
			),
			'cuny-edge'                        => array(
				'label' => 'CUNY Edge',
			),
			'cuny-service-corps'               => array(
				'label' => 'CUNY Service Corps',
			),
			'financial-aid'                    => array(
				'label' => 'Financial Aid',
			),
			'new-student-center'               => array(
				'label' => 'New Student Center',
			),
			'registrar'                        => array(
				'label' => 'Registrar',
			),
			'seek'                             => array(
				'label' => 'SEEK',
			),
			'student-affairs-dept'             => array(
				'label' => 'Student Affairs',
			),
			'student-life'                     => array(
				'label' => 'Student Life & Development',
			),
			'transfer-and-recruitment'         => array(
				'label' => 'Transfer & Recruitment Office',
			),
			'veterans-support-services'        => array(
				'label' => 'Veterans Support Services',
			),
			'wellness-center'                  => array(
				'label' => 'Wellness Center',
			),
		),
		'administration'   => array(
			'bookstore'                     => array(
				'label' => 'Bookstore',
			),
			'buildings-and-grounds'         => array(
				'label' => 'Buildings & Grounds',
			),
			'business-office'               => array(
				'label' => 'Business Office',
			),
			'computer-information-services' => array(
				'label' => 'Computer Information Services',
			),
			'health-and-safety'             => array(
				'label' => 'Health & Safety',
			),
			'human-resources'               => array(
				'label' => 'Human Resources',
			),
		),
		'president'        => array(
			'alumni-relations'                  => array(
				'label' => 'Alumni Relations',
			),
			'beoc'                              => array(
				'label' => 'BEOC',
			),
			'city-tech-foundation'              => array(
				'label' => 'City Tech Foundation',
			),
			'communications'                    => array(
				'label' => 'Communications',
			),
			'image-and-visual-communications'   => array(
				'label' => 'Image & Visual Communications',
			),
			'legal-and-title-ix'                => array(
				'label' => 'Legal and Title IX',
			),
			'office-of-faculty-staff-relations' => array(
				'label' => 'Office of Faculty / Staff Relations',
			),
			'presidents-office'                 => array(
				'label' => 'President\'s Office',
			),
			'professional-development-center'   => array(
				'label' => 'Professional Development Center',
			),
			'public-relations'                  => array(
				'label' => 'Public Relations',
			),
			'public-safety'                     => array(
				'label' => 'Public Safety',
			),
		),
	);

	if ( ! $entity ) {
		return $all_departments;
	}

	if ( ! isset( $all_departments[ $entity ] ) ) {
		return array();
	}

	return $all_departments[ $entity ];
}

function wds_new_group_type() {
	if ( isset( $_GET['new'] ) && 'true' === $_GET['new'] && isset( $_GET['type'] ) ) {
		global $bp;
		unset( $bp->groups->current_create_step );
		unset( $bp->groups->completed_create_steps );

		setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH );
		setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH );
		setcookie( 'wds_bp_group_type', $_GET['type'], time() + 20000, COOKIEPATH );
		bp_core_redirect( $bp->root_domain . '/' . $bp->groups->slug . '/create/step/group-details/?type=' . $_GET['type'] );
	}
}
add_action( 'init', 'wds_new_group_type' );

function wds_load_group_type( $group_type ) {
	global $wpdb, $bp, $user_ID;

	$return = '';

	if ( $group_type ) {
		$echo   = true;
		$return = '<input type="hidden" name="group_type" value="' . ucfirst( $group_type ) . '">';
	} else {
		$group_type = $_POST['group_type'];
	}

	$wds_group_school = groups_get_groupmeta( bp_get_current_group_id(), 'wds_group_school' );
	$wds_group_school = explode( ',', $wds_group_school );

	$account_type = xprofile_get_field_data( 'Account Type', bp_loggedin_user_id() );

	$return = '<div class="panel panel-default">';

	$return .= '<div class="panel-heading">School(s)';
	if ( openlab_is_school_required_for_group_type( $group_type ) && ( 'staff' != strtolower( $account_type ) || is_super_admin( get_current_user_id() ) ) ) {
		$return .= ' <span class="required">(required)</span>';
	}
	$return .= '</div><div class="panel-body">';
	$return .= '<table>';

	$return .= '<tr class="school-tooltip"><td colspan="2">';

	// associated school/dept tooltip
	switch ( $group_type ) {
		case 'course':
			$return .= '<p class="ol-tooltip">If your course is associated with one or more of the college’s schools or departments, please select from the checkboxes below.</p>';
			break;
		case 'portfolio':
			$return .= '<p class="ol-tooltip">If your ' . openlab_get_portfolio_label() . ' is associated with one or more of the college’s schools or departments, please select from the checkboxes below.</p>';
			break;
		case 'project':
			$return .= '<p class="ol-tooltip">Is your Project associated with one or more of the college\'s schools?</p>';
			break;
		case 'club':
			$return .= '<p class="ol-tooltip">Is your Club associated with one or more of the college\'s schools?</p>';
			break;
	}

	$return .= '</td></tr>';

	// If this is a Portfolio, we'll pre-check the school and department
	// of the logged-in user
	$checked_array = array(
		'schools'     => array(),
		'departments' => array(),
		'offices'     => array(),
	);

	if ( 'portfolio' == $group_type && bp_is_group_create() ) {
		$account_type = strtolower(
			bp_get_profile_field_data(
				array(
					'field'   => 'Account Type',
					'user_id' => bp_loggedin_user_id(),
				)
			)
		);
		$dept_field   = 'student' == $account_type ? 'Major Program of Study' : 'Department';

		$user_department = bp_get_profile_field_data(
			array(
				'field'   => $dept_field,
				'user_id' => bp_loggedin_user_id(),
			)
		);

		if ( $user_department ) {
			$all_departments = openlab_get_department_list();
			foreach ( $all_departments as $school => $depts ) {
				if ( in_array( $user_department, $depts ) ) {
					$checked_array['schools'][]     = $school;
					$checked_array['departments'][] = array_search( $user_department, $depts );
					break;
				}
			}
		}
	} else {
		foreach ( (array) $wds_group_school as $school ) {
			$checked_array['schools'][] = $school;
		}
	}

	$do_sod_selector = 'course' !== $group_type && 'student' !== $account_type;

	$selector_args = [];
	if ( ! $do_sod_selector ) {
		$selector_args['entities'] = [ 'school' ];
		$selector_args['legacy']   = true;
	}

	// Special case: student/alumni portfolio creation doesn't see Office.
	if ( 'portfolio' === $group_type && in_array( strtolower( $account_type ), [ 'student', 'alumni' ], true ) ) {
		$selector_args['entities'] = [ 'school' ];
	}

	$selector_args['required'] = openlab_is_school_required_for_group_type( $group_type ) && 'staff' != strtolower( $account_type );
	$selector_args['checked']  = openlab_get_group_academic_units( bp_get_current_group_id() );

	$return .= '<tr><td class="school-inputs" colspan="2">';

	ob_start();
	openlab_academic_unit_selector( $selector_args );
	$selector = ob_get_contents();
	ob_end_clean();

	$return .= $selector;

	$return .= '</td>';
	$return .= '</tr>';

	$wds_faculty      = '';
	$wds_course_code  = '';
	$wds_section_code = '';
	$wds_semester     = '';
	$wds_year         = '';
	$wds_course_html  = '';

	if ( bp_get_current_group_id() ) {
		$wds_faculty      = groups_get_groupmeta( bp_get_current_group_id(), 'wds_faculty' );
		$wds_course_code  = groups_get_groupmeta( bp_get_current_group_id(), 'wds_course_code' );
		$wds_section_code = groups_get_groupmeta( bp_get_current_group_id(), 'wds_section_code' );
		$wds_semester     = groups_get_groupmeta( bp_get_current_group_id(), 'wds_semester' );
		$wds_year         = groups_get_groupmeta( bp_get_current_group_id(), 'wds_year' );
		$wds_course_html  = groups_get_groupmeta( bp_get_current_group_id(), 'wds_course_html' );
	}

	$last_name = xprofile_get_field_data( 'Last Name', $bp->loggedin_user->id );

	$faculty_name = bp_core_get_user_displayname( bp_loggedin_user_id() );
	$return      .= '<input type="hidden" name="wds_faculty" value="' . esc_attr( $faculty_name ) . '">';

	$return .= '</table></div></div>';

	if ( 'course' == $group_type ) {

		$return .= '<div class="panel panel-default">';
		$return .= '<div class="panel-heading">Course Information</div>';
		$return .= '<div class="panel-body"><table>';

		$return .= '<tr><td colspan="2"><p class="ol-tooltip">The following fields are not required, but including this information will make it easier for others to find your Course.</p></td></tr>';

		$return .= '<tr class="additional-field course-code-field">';
		$return .= '<td class="additional-field-label"><label class="passive" for="wds_course_code">Course Code:</label></td>';
		$return .= '<td><input class="form-control" type="text" id="wds_course_code" name="wds_course_code" value="' . $wds_course_code . '"></td>';
		$return .= '</tr>';

		$return .= '<tr class="additional-field section-code-field">';
		$return .= '<td class="additional-field-label"><label class="passive" for="wds_section_code">Section Code:</label></td>';
		$return .= '<td><input class="form-control" type="text" id="wds_section_code" name="wds_section_code" value="' . $wds_section_code . '"></td>';
		$return .= '</tr>';

		$return .= '<tr class="additional-field semester-field">';
		$return .= '<td class="additional-field-label"><label class="passive" for="wds_semester">Semester:</label></td>';
		$return .= '<td><select class="form-control" id="wds_semester" name="wds_semester">';
		$return .= '<option value="">--select one--';

		$return .= '<option value="Spring" ' . selected( $wds_semester, 'Spring', false ) . '>Spring';
		$return .= '<option value="Summer" ' . selected( $wds_semester, 'Summer', false ) . '>Summer';
		$return .= '<option value="Fall" ' . selected( $wds_semester, 'Fall', false ) . '>Fall';
		$return .= '<option value="Winter" ' . selected( $wds_semester, 'Winter', false ) . '>Winter';
		$return .= '</select></td>';
		$return .= '</tr>';

		$return .= '<tr class="additional-field year-field">';
		$return .= '<td class="additional-field-label"><label class="passive" for="wds_year">Year:</label></td>';
		$return .= '<td><input class="form-control" type="text" id="wds_year" name="wds_year" value="' . $wds_year . '"></td>';
		$return .= '</tr>';

		$return .= '<tr class="additional-field additional-description-field">';
		$return .= '<td colspan="2" class="additional-field-label"><label class="passive" for="additional-desc-html">Additional Description/HTML:</label></td></tr>';
		$return .= '<tr><td colspan="2"><textarea class="form-control" name="wds_course_html" id="additional-desc-html">' . $wds_course_html . '</textarea></td></tr>';
		$return .= '</tr>';

		$return .= '</table></div></div><!--.panel-->';
	}

	if ( $echo ) {
		return $return;
	} else {
		$return = str_replace( "'", "\'", $return );
		die( "document.getElementById( 'wds-group-type' ).innerHTML='$return'" );
	}
}

/**
 * Are School and Department required for this group type?
 */
function openlab_is_school_required_for_group_type( $group_type = '' ) {
	$req_types = array( 'course', 'portfolio' );

	return in_array( $group_type, $req_types );
}

/**
 * School and Department are required for courses and portfolios
 *
 * Hook in before BP's core function, so we get first dibs on returning errors
 */
function openlab_require_school_and_department_for_groups() {
	global $bp;

	// Only check at group creation and group admin
	if ( ! bp_is_group_admin_page() && ! bp_is_group_create() ) {
		return;
	}

	// Don't check at deletion time ( groan )
	if ( bp_is_group_admin_screen( 'delete-group' ) ) {
		return;
	}

	// No payload, no check
	if ( empty( $_POST ) ) {
		return;
	}

	if ( bp_is_group_create() ) {
		$group_type = isset( $_GET['type'] ) ? $_GET['type'] : '';
		$redirect   = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/';
	} else {
		$group_type = openlab_get_current_group_type();
		$redirect   = bp_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/';
	}

	if ( openlab_is_school_required_for_group_type( $group_type ) && ( bp_is_action_variable( 'group-details', 1 ) || bp_is_action_variable( 'edit-details' ) ) ) {

		if ( ( empty( $_POST['schools'] ) && empty( $_POST['offices'] ) ) || empty( $_POST['departments'] ) ) {
			bp_core_add_message( 'You must provide a school and department.', 'error' );
			bp_core_redirect( $redirect );
		}
	}
}

add_action( 'bp_actions', 'openlab_require_school_and_department_for_groups', 5 );

// Save Group Meta
add_action( 'groups_group_after_save', 'wds_bp_group_meta_save' );

function wds_bp_group_meta_save( $group ) {
	global $wpdb, $user_ID, $bp;

	$is_editing = false;

	if ( isset( $_POST['_wp_http_referer'] ) && strpos( $_POST['_wp_http_referer'], 'edit-details' ) !== false ) {
		$is_editing = true;
	}

	if ( isset( $_POST['group_type'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_group_type', $_POST['group_type'] );

		if ( 'course' == $_POST['group_type'] ) {
			$is_course = true;
		}
	}

	if ( isset( $_POST['wds_faculty'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_faculty', $_POST['wds_faculty'] );
	}
	if ( isset( $_POST['wds_group_school'] ) ) {
		$wds_group_school = implode( ',', $_POST['wds_group_school'] );

		//fully deleting and then adding in school metadata so schools can be unchecked
		groups_delete_groupmeta( $group->id, 'wds_group_school' );
		groups_add_groupmeta( $group->id, 'wds_group_school', $wds_group_school, true );
	} elseif ( ! isset( $_POST['wds_group_school'] ) ) {
		//allows user to uncheck all schools (projects and clubs only)
		//on edit only
		if ( $is_editing ) {
			groups_update_groupmeta( $group->id, 'wds_group_school', '' );
		}
	}

	if ( isset( $_POST['wds_departments'] ) ) {
		$wds_departments = implode( ',', $_POST['wds_departments'] );

		//fully deleting and then adding in department metadata so departments can be unchecked
		groups_delete_groupmeta( $group->id, 'wds_departments' );
		groups_add_groupmeta( $group->id, 'wds_departments', $wds_departments, true );
	} elseif ( ! isset( $_POST['wds_departments'] ) ) {
		//allows user to uncheck all departments (projects and clubs only)
		//on edit only
		if ( $is_editing ) {
			groups_update_groupmeta( $group->id, 'wds_departments', '' );
		}
	}

	if ( isset( $_POST['wds_course_code'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_course_code', $_POST['wds_course_code'] );
	}
	if ( isset( $_POST['wds_section_code'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_section_code', $_POST['wds_section_code'] );
	}
	if ( isset( $_POST['wds_semester'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_semester', $_POST['wds_semester'] );
	}
	if ( isset( $_POST['wds_year'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_year', $_POST['wds_year'] );
	}
	if ( isset( $_POST['wds_course_html'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_course_html', $_POST['wds_course_html'] );
	}
	if ( isset( $_POST['group_project_type'] ) ) {
		groups_update_groupmeta( $group->id, 'wds_group_project_type', $_POST['group_project_type'] );
	}

	// Clear the active semester cache
	delete_transient( 'openlab_active_semesters' );

	// Site association. Non-portfolios have the option of not having associated sites (thus the
	// wds_website_check value).
	if ( isset( $_POST['wds_website_check'] ) ||
			openlab_is_portfolio( $group->id )
	) {

		if ( isset( $_POST['new_or_old'] ) && 'new' == $_POST['new_or_old'] ) {

			// Create a new site
			ra_copy_blog_page( $group->id );
		} elseif ( isset( $_POST['new_or_old'] ) && 'old' == $_POST['new_or_old'] && isset( $_POST['groupblog-blogid'] ) ) {

			// Associate an existing site
			openlab_set_group_site_id( $group->id, (int) $_POST['groupblog-blogid'] );

		} elseif ( isset( $_POST['new_or_old'] ) && 'external' == $_POST['new_or_old'] && isset( $_POST['external-site-url'] ) ) {

			// External site
			// Some validation
			$url = openlab_validate_url( $_POST['external-site-url'] );
			groups_update_groupmeta( $group->id, 'external_site_url', $url );

			if ( ! empty( $_POST['external-site-type'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_type', $_POST['external-site-type'] );
			}

			if ( ! empty( $_POST['external-posts-url'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_posts_feed', $_POST['external-posts-url'] );
			}

			if ( ! empty( $_POST['external-comments-url'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_comments_feed', $_POST['external-comments-url'] );
			}
		}

		if ( openlab_is_portfolio( $group->id ) ) {
			openlab_associate_portfolio_group_with_user( $group->id, bp_loggedin_user_id() );
		}
	}

	// Site privacy
	if ( isset( $_POST['blog_public'] ) ) {
		$blog_public = (float) $_POST['blog_public'];
		$site_id     = openlab_get_site_id_by_group_id( $group->id );

		if ( $site_id ) {
			update_blog_option( $site_id, 'blog_public', $blog_public );
		}
	}

	// Member roles.
	if ( openlab_get_site_id_by_group_id( $group->id ) ) {
		$role_map = [
			'admin'  => 'administrator',
			'mod'    => 'editor',
			'member' => 'author',
		];

		$site_roles = [ 'administrator', 'editor', 'author', 'contributor', 'subscriber' ];

		foreach ( $role_map as $group_role => $site_role ) {
			$role_key = 'member_role_' . $group_role;
			if ( ! isset( $_POST[ $role_key ] ) ) {
				continue;
			}

			$selected_site_role = $_POST[ $role_key ];
			if ( ! in_array( $selected_site_role, $site_roles, true ) ) {
				continue;
			}

			$role_map[ $group_role ] = $selected_site_role;
		}

		groups_update_groupmeta( $group->id, 'member_site_roles', $role_map );
	}

	if ( isset( $_POST['blog_public'] ) ) {
		$blog_public = (float) $_POST['blog_public'];
		$site_id     = openlab_get_site_id_by_group_id( $group->id );

		if ( $site_id ) {
			update_blog_option( $site_id, 'blog_public', $blog_public );
		}
	}

	// Portfolio list display
	if ( isset( $_POST['group-portfolio-list-heading'] ) ) {
		$enabled = ! empty( $_POST['group-show-portfolio-list'] ) ? 'yes' : 'no';
		groups_update_groupmeta( $group->id, 'portfolio_list_enabled', $enabled );

		groups_update_groupmeta( $group->id, 'portfolio_list_heading', strip_tags( stripslashes( $_POST['group-portfolio-list-heading'] ) ) );
	}

	// Library tools display.
	$library_tools_enabled = ! empty( $_POST['group-show-library-tools'] ) ? 'yes' : 'no';
	groups_update_groupmeta( $group->id, 'library_tools_enabled', $library_tools_enabled );

	// Feed URLs ( step two of group creation )
	if ( isset( $_POST['external-site-posts-feed'] ) || isset( $_POST['external-site-comments-feed'] ) ) {
		groups_update_groupmeta( $group->id, 'external_site_posts_feed', $_POST['external-site-posts-feed'] );
		groups_update_groupmeta( $group->id, 'external_site_comments_feed', $_POST['external-site-comments-feed'] );
	}
}

// Copy the group blog template
function ra_copy_blog_page( $group_id ) {
	global $bp, $wpdb, $current_site, $user_email, $base, $user_ID;
	$blog = isset( $_POST['blog'] ) ? $_POST['blog'] : array();

	if ( ! empty( $blog['domain'] ) && $group_id ) {
		$wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
		if ( ! defined( 'SUNRISE' ) || $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->dmtable}'" ) != $wpdb->dmtable ) {
			$join  = '';
			$where = '';
		} else {
			$join  = "LEFT JOIN {$wpdb->dmtable} d ON d.blog_id = b.blog_id ";
			$where = 'AND d.domain IS NULL ';
		}

		$src_id = intval( $_POST['source_blog'] );

		//$domain = sanitize_user( str_replace( '/', '', $blog[ 'domain' ] ) );
		//$domain = str_replace( ".","", $domain );
		$domain = friendly_url( $blog['domain'] );
		$email  = sanitize_email( $user_email );
		$title  = $_POST['group-name'];

		if ( ! $src_id ) {
			$msg = __( 'Select a source blog.' );
		} elseif ( empty( $domain ) || empty( $email ) ) {
			$msg = __( 'Missing blog address or email address.' );
		} elseif ( ! is_email( $email ) ) {
			$msg = __( 'Invalid email address' );
		} else {
			if ( constant( 'VHOST' ) == 'yes' ) {
				$newdomain = $domain . '.' . $current_site->domain;
				$path      = $base;
			} else {
				$newdomain = $current_site->domain;
				$path      = $base . $domain . '/';
			}

			$password = 'N/A';
			$user_id  = email_exists( $email );
			if ( ! $user_id ) {
				$password = generate_random_password();
				$user_id  = wpmu_create_user( $domain, $password, $email );
				if ( false == $user_id ) {
					$msg = __( 'There was an error creating the user' );
				} else {
					wp_new_user_notification( $user_id, $password );
				}
			}
			$wpdb->hide_errors();
			$new_id = wpmu_create_blog( $newdomain, $path, $title, $user_id, array( 'public' => 1 ), $current_site->id );
			$id     = $new_id;
			$wpdb->show_errors();
			if ( ! is_wp_error( $id ) ) { //if it dont already exists then move over everything
				$current_user = get_userdata( bp_loggedin_user_id() );

				openlab_set_group_site_id( $group_id, $new_id );

				/* if ( get_user_option( $user_id, 'primary_blog' ) == 1 )
				  update_user_option( $user_id, 'primary_blog', $id, true ); */
				$content_mail = sprintf( "New site created by %1$1s\n\nAddress: http://%2$2s\nName: %3$3s", $current_user->user_login, $newdomain . $path, stripslashes( $title ) );
				wp_mail( get_site_option( 'admin_email' ), sprintf( '[%s] New Blog Created', $current_site->site_name ), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
				wpmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
				$msg = __( 'Site Created' );
				// now copy
				$blogtables = $wpdb->base_prefix . $src_id . '_';
				$newtables  = $wpdb->base_prefix . $new_id . '_';
				$query      = "SHOW TABLES LIKE '{$blogtables}%'";

				// phpcs:disable
				$tables = $wpdb->get_results( $query, ARRAY_A );
				// phpcs:enable

				if ( $tables ) {
					reset( $tables );
					$create     = array();
					$data       = array();
					$len        = strlen( $blogtables );
					$create_col = 'Create Table';
					// add std wp tables to this array
					$wptables = array(
						$blogtables . 'links',
						$blogtables . 'postmeta',
						$blogtables . 'posts',
						$blogtables . 'terms',
						$blogtables . 'term_taxonomy',
						$blogtables . 'term_relationships',
					);
					for ( $i = 0; $i < count( $tables ); $i++ ) {
						$table = current( $tables[ $i ] );
						if ( substr( $table, 0, $len ) == $blogtables ) {
							if ( ! ( $table == $blogtables . 'options' || $table == $blogtables . 'comments' || $table == $blogtables . 'commentmeta' ) ) {
								// phpcs:disable
								$create[ $table ] = $wpdb->get_row( "SHOW CREATE TABLE {$table}" );
								$data[ $table ]   = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );
								// phpcs:enable
							}
						}
					}

					if ( $data ) {
						switch_to_blog( $src_id );
						$src_upload_dir = wp_upload_dir();
						$src_url        = get_option( 'siteurl' );
						$option_query   = "SELECT option_name, option_value FROM {$wpdb->options}";
						restore_current_blog();
						$new_url = get_blog_option( $new_id, 'siteurl' );
						foreach ( $data as $k => $v ) {
							$table = str_replace( $blogtables, $newtables, $k );
							if ( in_array( $k, $wptables ) ) { // drop new blog table
								// phpcs:disable
								$wpdb->query( "DROP TABLE IF EXISTS $table" );
								// phpcs:enable
							}
							$key   = (array) $create[ $k ];
							$query = str_replace( $blogtables, $newtables, $key[ $create_col ] );

							// phpcs:disable
							$wpdb->query( $query );
							// phpcs:enable

							$is_post = ( $k == $blogtables . 'posts' );
							if ( $v ) {
								foreach ( $v as $row ) {
									if ( $is_post ) {
										$row['guid']         = str_replace( $src_url, $new_url, $row['guid'] );
										$row['post_content'] = str_replace( $src_url, $new_url, $row['post_content'] );
										$row['post_author']  = $user_id;
									}
									$wpdb->insert( $table, $row );
								}
							}
						}

						// copy media
						OpenLab_Clone_Course_Site::copyr( $src_upload_dir['basedir'], str_replace( $src_id, $new_id, $src_upload_dir['basedir'] ) );

						// update options
						$skip_options = array(
							'admin_email',
							'bcn_options',
							'bcn_version',
							'blogname',
							'cron',
							'db_version',
							'doing_cron',
							'fileupload_url',
							'home',
							'new_admin_email',
							'nonce_salt',
							'random_seed',
							'rewrite_rules',
							'secret',
							'siteurl',
							'upload_path',
							'upload_url_path',
							"{$wpdb->base_prefix}{$src_id}_user_roles",
						);

						// phpcs:disable
						$options = $wpdb->get_results( $option_query );
						// phpcs:enable

						//new blog stuff
						if ( $options ) {
							switch_to_blog( $new_id );
							update_option( 'wds_bp_group_id', $group_id );

							$old_relative_url = set_url_scheme( $src_url, 'relative' );
							$new_relative_url = set_url_scheme( $new_url, 'relative' );
							foreach ( $options as $o ) {
								//								var_dump( $o );
								if ( ! in_array( $o->option_name, $skip_options ) && substr( $o->option_name, 0, 6 ) != '_trans' ) {
									// Imperfect but we generally won't have nested arrays.
									if ( is_serialized( $o->option_value ) ) {
										$new_option_value = unserialize( $o->option_value );
										foreach ( $new_option_value as $key => &$value ) {
											if ( is_string( $value ) ) {
												$value = str_replace( $old_relative_url, $new_relative_url, $value );
											}
										}
									} else {
										$new_option_value = str_replace( $old_relative_url, $new_relative_url, $o->option_value );
									}
									update_option( $o->option_name, $new_option_value );
								}
							}
							if ( version_compare( $GLOBALS['wp_version'], '2.8', '>' ) ) {
								set_transient( 'rewrite_rules', '' );
							} else {
								update_option( 'rewrite_rules', '' );
							}

							restore_current_blog();
							$msg = __( 'Blog Copied' );
						}
					}
				}
			} else {
				$msg = $id->get_error_message();
			}
		}
	}
}

// this is a function for sanitizing the website name
// source http://cubiq.org/the-perfect-php-clean-url-generator
function friendly_url( $str, $replace = array(), $delimiter = '-' ) {
	if ( ! empty( $replace ) ) {
		$str = str_replace( (array) $replace, ' ', $str );
	}

	if ( function_exists( 'iconv' ) ) {
		$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $str );
	} else {
		$clean = $str;
	}

	$clean = preg_replace( '/[^a-zA-Z0-9\/_|+ -]/', '', $clean );
	$clean = strtolower( trim( $clean, '-' ) );
	$clean = preg_replace( '/[\/_|+ -]+/', $delimiter, $clean );

	return $clean;
}

/**
 * Don't let anyone access the Create A Site page
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/160
 */
function openlab_redirect_from_site_creation() {
	if ( bp_is_create_blog() ) {
		bp_core_redirect( bp_get_root_domain() );
	}
}

add_action( 'bp_actions', 'openlab_redirect_from_site_creation' );

/**
 * Load custom language file for BP Group Documents
 */
load_textdomain( 'bp-group-documents', WP_CONTENT_DIR . '/languages/buddypress-group-documents-en_CAC.mo' );

/**
 * Allow super admins to change user type on Dashboard
 */
class OpenLab_Change_User_Type {

	public static function init() {
		static $instance;

		if ( ! is_super_admin() ) {
			return;
		}

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Change_User_Type();
		}
	}

	function __construct() {
		add_action( 'show_user_profile', array( $this, 'markup' ) );
		add_action( 'edit_user_profile', array( $this, 'markup' ) );

		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
	}

	function markup( $user ) {
		$account_type = xprofile_get_field_data( 'Account Type', $user->ID );

		$field_id = xprofile_get_field_id_from_name( 'Account Type' );
		$field    = new BP_XProfile_Field( $field_id );
		$options  = $field->get_children();
		?>

		<h3>OpenLab Account Type</h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="openlab_account_type">Account Type</label>
				</th>

				<td>
					<?php foreach ( $options as $option ) : ?>
						<label><input type="radio" name="openlab_account_type" value="<?php echo $option->name; ?>" <?php checked( $account_type, $option->name ); ?>> <?php echo $option->name; ?><br /></label>
						<?php endforeach ?>
				</td>
			</tr>
		</table>

		<?php
	}

	function save( $user_id ) {
		if ( isset( $_POST['openlab_account_type'] ) ) {
			xprofile_set_field_data( 'Account Type', $user_id, $_POST['openlab_account_type'] );
		}
	}

}

add_action( 'admin_init', array( 'OpenLab_Change_User_Type', 'init' ) );

/**
 * Only allow the site's faculty admin to see full names on the Dashboard
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/165
 */
function openlab_hide_fn_ln( $check, $object, $meta_key, $single ) {
	global $wpdb, $bp;

	if ( is_admin() && in_array( $meta_key, array( 'first_name', 'last_name' ) ) ) {

		// Faculty only
		$account_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
		if ( 'faculty' != strtolower( $account_type ) ) {
			return '';
		}

		// Make sure it's the right faculty member
		$group_id = openlab_get_group_id_by_blog_id( get_current_blog_id() );

		if ( ! empty( $group_id ) && ! groups_is_user_admin( get_current_user_id(), (int) $group_id ) ) {
			return '';
		}

		// Make sure it's a course
		$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

		if ( 'course' != strtolower( $group_type ) ) {
			return '';
		}
	}

	return $check;
}

//add_filter( 'get_user_metadata', 'openlab_hide_fn_ln', 9999, 4 );

/**
 * No access redirects should happen from wp-login.php
 */
add_filter(
	'bp_no_access_mode',
	function() {
		return 2;
	}
);

/**
 * Don't auto-link items in profiles
 * Hooked to bp_screens so that it gets fired late enough
 */
add_action(
	'bp_screens',
	function() {
		remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
	}
);

//Change "Group" to something else
class Buddypress_Translation_Mangler {
	/*
	 * Filter the translation string before it is displayed.
	 *
	 * This function will choke if we try to load it when not viewing a group page or in a group loop
	 * So we bail in cases where neither of those things is present, by checking $groups_template
	 */

	static function filter_gettext( $translation, $text, $domain ) {
		global $bp, $groups_template;

		if ( 'buddypress' != $domain ) {
			return $translation;
		}

		$group_id = 0;
		if ( ! bp_is_group_create() ) {
			if ( ! empty( $groups_template->group->id ) ) {
				$group_id = $groups_template->group->id;
			} elseif ( ! empty( $bp->groups->current_group->id ) ) {
				$group_id = $bp->groups->current_group->id;
			}
		}

		if ( $group_id ) {
			$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
		} elseif ( isset( $_GET['type'] ) ) {
			$grouptype = $_GET['type'];
		} else {
			return $translation;
		}

		$uc_grouptype = ucfirst( $grouptype );
		$translations = get_translations_for_domain( 'buddypress' );

		switch ( $text ) {
			case 'Forum':
				return $translations->translate( 'Discussion' );
				break;
			case 'Group Forum':
				return $translations->translate( "$uc_grouptype Discussion" );
				break;
			case 'Group Forum Directory':
				return $translations->translate( '' );
				break;
			case 'Group Forums Directory':
				return $translations->translate( 'Group Discussions Directory' );
				break;
			case 'Join Group':
				return $translations->translate( 'Join Now!' );
				break;
			case 'You successfully joined the group.':
				return $translations->translate( 'You successfully joined!' );
				break;
			case 'Recent Discussion':
				return $translations->translate( 'Recent Forum Discussion' );
				break;
			case 'said ':
				return $translations->translate( '' );
				break;
			case 'Create a Group':
				return $translations->translate( 'Create a ' . $uc_grouptype );
				break;
			case 'Manage':
				return $translations->translate( 'Settings' );
				break;
		}
		return $translation;
	}

}

function openlab_launch_translator() {
	add_filter( 'gettext', array( 'Buddypress_Translation_Mangler', 'filter_gettext' ), 10, 4 );
	add_filter( 'gettext', array( 'bbPress_Translation_Mangler', 'filter_gettext' ), 10, 4 );
	add_filter( 'gettext_with_context', 'openlab_gettext_with_context', 10, 4 );
}

add_action( 'bp_setup_globals', 'openlab_launch_translator' );

function openlab_gettext_with_context( $translations, $text, $context, $domain ) {
	if ( 'buddypress' !== $domain ) {
		return $translations;
	}
	switch ( $text ) {
		case 'Manage':
			if ( 'My Group screen nav' === $context ) {
				return 'Settings';
			}
			break;
	}
	return $translations;
}

// phpcs:disable
class bbPress_Translation_Mangler {
// phpcs:enable

	static function filter_gettext( $translation, $text, $domain ) {
		if ( 'bbpress' != $domain ) {
			return $translation;
		}
		$translations = get_translations_for_domain( 'buddypress' );
		switch ( $text ) {
			case 'Forum':
				return $translations->translate( 'Discussion' );
				break;
		}
		return $translation;
	}

}

class Buddypress_Ajax_Translation_Mangler {
	/*
	 * Filter the translation string before it is displayed.
	 */
	static function filter_gettext( $translation, $text, $domain ) {
		$translations = get_translations_for_domain( 'buddypress' );
		switch ( $text ) {
			case 'Friendship Requested':
			case 'Add Friend':
				return $translations->translate( 'Friend' );
				break;
		}
		return $translation;
	}

}

function openlab_launch_ajax_translator() {
	add_filter( 'gettext', array( 'Buddypress_Ajax_Translation_Mangler', 'filter_gettext' ), 10, 4 );
}

add_action( 'bp_setup_globals', 'openlab_launch_ajax_translator' );

/**
 * Interfere in the comment posting process to allow for duplicates on the same post
 *
 * Borrowed from http://www.strangerstudios.com/blog/2010/10/duplicate-comment-detected-it-looks-as-though-you%E2%80%99ve-already-said-that/
 * See http://openlab.citytech.cuny.edu/redmine/issues/351
 */
function openlab_enable_duplicate_comments_preprocess_comment( $comment_data ) {
	if ( is_user_logged_in() ) {
		//add some random content to comment to keep dupe checker from finding it
		$random                           = md5( time() );
		$comment_data['comment_content'] .= 'disabledupes{' . $random . '}disabledupes';
	}

	return $comment_data;
}

add_filter( 'preprocess_comment', 'openlab_enable_duplicate_comments_preprocess_comment' );

/**
 * Strips disabledupes string from comments. See previous function.
 */
function openlab_enable_duplicate_comments_comment_post( $comment_id ) {
	global $wpdb;

	if ( is_user_logged_in() ) {

		// Remove the random content.
		$comment_content = $wpdb->get_var( $wpdb->prepare( "SELECT comment_content FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment_id ) );
		$comment_content = preg_replace( '/disabledupes\{.*\}disabledupes/', '', $comment_content );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->comments SET comment_content = %s WHERE comment_ID = %d LIMIT 1", $comment_content, $comment_id ) );

		clean_comment_cache( array( $comment_id ) );
	}
}

add_action( 'comment_post', 'openlab_enable_duplicate_comments_comment_post', 1 );

/**
 * Adds the URL of the user profile to the New User Registration admin emails
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/334
 */
function openlab_newuser_notify_siteadmin( $message ) {

	// Due to WP lameness, we have to hack around to get the username
	preg_match( '|New User: ( .* )|', $message, $matches );

	if ( ! empty( $matches ) ) {
		$user        = get_user_by( 'login', $matches[1] );
		$profile_url = bp_core_get_user_domain( $user->ID );

		if ( $profile_url ) {
			$message_a = explode( 'Remote IP', $message );
			$message   = $message_a[0] . 'Profile URL: ' . $profile_url . "\n" . 'Remote IP' . $message_a[1];
		}
	}

	return $message;
}

add_filter( 'newuser_notify_siteadmin', 'openlab_newuser_notify_siteadmin' );

/**
 * Get the word for a group type
 *
 * Groups fall into three categories: Project, Club, and Course. Use this function to get the word
 * corresponding to the group type, with the appropriate case and count.
 *
 * @param $case 'lower' ( course ), 'title' ( Course ), 'upper' ( COURSE )
 * @param $count 'single' ( course ), 'plural' ( courses )
 * @param $group_id Will default to the current group id
 */
function openlab_group_type( $case = 'lower', $count = 'single', $group_id = 0 ) {
	if ( ! $case || ! in_array( $case, array( 'lower', 'title', 'upper' ) ) ) {
		$case = 'lower';
	}

	if ( ! $count || ! in_array( $count, array( 'single', 'plural' ) ) ) {
		$case = 'single';
	}

	// Set a group id.
	$group_id = (int) $group_id;
	if ( ! $group_id && bp_get_current_group_id() ) {
		$group_id = bp_get_current_group_id();
	} elseif ( ! $group_id && bp_get_new_group_id() ) {
		$group_id = bp_get_new_group_id();
	} elseif ( ! $group_id && bp_get_group_id() ) {
		$group_id = bp_get_group_id();
	}

	$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

	if ( empty( $group_type ) ) {
		return '';
	}

	switch ( $case ) {
		case 'lower':
			$group_type = strtolower( $group_type );
			break;

		case 'title':
			$group_type = ucwords( $group_type );
			break;

		case 'upper':
			$group_type = strtoupper( $group_type );
			break;
	}

	switch ( $count ) {
		case 'single':
			break;

		case 'plural':
			$group_type .= 's';
			break;
	}

	return $group_type;
}

/**
 * Utility function for getting a default user id when none has been passed to the function
 *
 * The logic is this: If there is a displayed user, return it. If not, check to see whether we're
 * in a members loop; if so, return the current member. If it's still 0, check to see whether
 * we're on a my-* page; if so, return the loggedin user id. Otherwise, return 0.
 *
 * Note that we have to manually check the $members_template variable, because
 * bp_get_member_user_id() doesn't do it properly.
 *
 * @return int
 */
function openlab_fallback_user() {
	global $members_template;

	$user_id = bp_displayed_user_id();

	if ( ! $user_id && ! empty( $members_template ) && isset( $members_template->member ) ) {
		$user_id = bp_get_member_user_id();
	}

	if ( ! $user_id && ( is_page( 'my-courses' ) || is_page( 'my-clubs' ) || is_page( 'my-projects' ) || is_page( 'my-sites' ) ) ) {
		$user_id = bp_loggedin_user_id();
	}

	return (int) $user_id;
}

/**
 * Utility function for getting a default group id when none has been passed to the function
 *
 * The logic is this: If this is a group page, return the current group id. If this is the group
 * creation process, return the new_group_id. If this is a group loop, return the id of the group
 * show during this iteration
 *
 * @return int
 */
function openlab_fallback_group() {
	global $groups_template;

	if ( ! bp_is_active( 'groups' ) ) {
		return 0;
	}

	$group_id = bp_get_current_group_id();

	if ( ! $group_id && bp_is_group_create() ) {
		$group_id = bp_get_new_group_id();
	}

	if ( ! $group_id && ! empty( $groups_template ) && isset( $groups_template->group ) ) {
		$group_id = $groups_template->group->id;
	}

	return (int) $group_id;
}

/**
 * Is this my profile?
 *
 * We need a specialized function that returns true when bp_is_my_profile() does, or in addition,
 * when on a my-* page
 *
 * @return bool
 */
function openlab_is_my_profile() {
	global $bp;

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( bp_is_my_profile() ) {
		return true;
	}

	if ( is_page( 'my-courses' ) || is_page( 'my-clubs' ) || is_page( 'my-projects' ) || is_page( 'my-sites' ) ) {
		return true;
	}

	// For the group creating pages.
	if ( 'create' === $bp->current_action ) {
		return true;
	}

	return false;
}

/**
 * On saving settings, save our additional fields
 */
function openlab_addl_settings_fields() {
	global $bp;

	$fname        = isset( $_POST['fname'] ) ? $_POST['fname'] : '';
	$lname        = isset( $_POST['lname'] ) ? $_POST['lname'] : '';
	$account_type = isset( $_POST['account_type'] ) ? $_POST['account_type'] : '';

	// Don't let this continue if a password error was recorded
	if ( isset( $bp->template_message_type ) && 'error' == $bp->template_message_type && 'No changes were made to your account.' != $bp->template_message ) {
		return;
	}

	if ( empty( $fname ) || empty( $lname ) ) {
		bp_core_add_message( 'First Name and Last Name are required fields', 'error' );
	} else {
		xprofile_set_field_data( 'First Name', bp_displayed_user_id(), $fname );
		xprofile_set_field_data( 'Last Name', bp_displayed_user_id(), $lname );

		bp_core_add_message( __( 'Your settings have been saved.', 'buddypress' ), 'success' );
	}

	if ( ! empty( $account_type ) ) {
		//saving account type for students or alumni
		$types        = array( 'Student', 'Alumni' );
		$account_type = in_array( $_POST['account_type'], $types ) ? $_POST['account_type'] : 'Student';
		$user_id      = bp_displayed_user_id();
		$current_type = openlab_get_displayed_user_account_type();

		// Only students and alums can do this
		if ( in_array( $current_type, $types ) ) {
			xprofile_set_field_data( 'Account Type', bp_displayed_user_id(), $account_type );
		}
	}

	bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() . '/general' ) );
}

add_action( 'bp_core_general_settings_after_save', 'openlab_addl_settings_fields' );

/**
 * A small hack to ensure that the 'Create A New Site' option is disabled on my-sites.php
 */
function openlab_disable_new_site_link( $registration ) {
	if ( '/wp-admin/my-sites.php' == $_SERVER['SCRIPT_NAME'] ) {
		$registration = 'none';
	}

	return $registration;
}

add_filter( 'site_option_registration', 'openlab_disable_new_site_link' );

function openlab_set_default_group_subscription_on_creation( $group_id ) {
	groups_update_groupmeta( $group_id, 'ass_default_subscription', 'supersub' );
}

add_action( 'groups_created_group', 'openlab_set_default_group_subscription_on_creation' );

add_filter(
	'ass_digest_format_item',
	function( $item_message, $item, $action, $timestamp ) {
		$time_posted = date( get_option( 'time_format' ), $timestamp );
		$date_posted = date( get_option( 'date_format' ), $timestamp );

		$timestamp_string = sprintf( __( 'at %s, %s', 'buddypress-group-email-subscription' ), $time_posted, $date_posted );

		$timezone = new DateTimeZone( 'America/New_York' );
		$datetime = new DateTime();
		$datetime->setTimestamp( (int) $timestamp );
		$datetime->setTimeZone( $timezone );

		$new_time_posted = $datetime->format( get_option( 'time_format' ) );
		$new_date_posted = $datetime->format( get_option( 'date_format' ) );

		$new_timestamp_string = sprintf( __( 'at %s, %s', 'buddypress-group-email-subscription' ), $new_time_posted, $new_date_posted );

		return str_replace( $timestamp_string, $new_timestamp_string, $item_message );
	},
	10,
	4
);

/**
 * Brackets in password reset emails cause problems in some clients. Remove them
 */
function openlab_strip_brackets_from_pw_reset_email( $message ) {
	$message = preg_replace( '/<(http\S*?)>/', '$1', $message );
	return $message;
}

add_filter( 'retrieve_password_message', 'openlab_strip_brackets_from_pw_reset_email' );

/**
 * Don't allow non-super-admins to Add New Users on user-new.php
 *
 * This is a hack. user-new.php shows the Add New User section for any user
 * who has the 'create_users' cap. For some reason, Administrators have the
 * 'create_users' cap even on Multisite. Instead of doing a total removal
 * of this cap for Administrators ( which may break something ), I'm just
 * removing it on the user-new.php page.
 *
 */
function openlab_block_add_new_user( $allcaps, $cap, $args ) {
	if ( ! in_array( 'create_users', $cap ) ) {
		return $allcaps;
	}

	if ( ! is_admin() || false === strpos( $_SERVER['SCRIPT_NAME'], 'user-new.php' ) ) {
		return $allcaps;
	}

	if ( is_super_admin() ) {
		return $allcaps;
	}

	unset( $allcaps['create_users'] );

	return $allcaps;
}

add_filter( 'user_has_cap', 'openlab_block_add_new_user', 10, 3 );

/**
 * Remove user from group blog when leaving group
 *
 * NOTE: This function should live in includes/group-blogs.php, but can't
 * because of AJAX load order
 */
function openlab_remove_user_from_groupblog( $group_id, $user_id ) {
	$site_id = openlab_get_site_id_by_group_id( $group_id );
	if ( ! $site_id ) {
		return;
	}

	if ( $site_id ) {
		remove_user_from_blog( $user_id, $site_id );
	}
}
add_action( 'groups_ban_member', 'openlab_remove_user_from_groupblog', 10, 2 );
add_action( 'groups_remove_member', 'openlab_remove_user_from_groupblog', 10, 2 );
add_action( 'groups_leave_group', 'openlab_remove_user_from_groupblog', 10, 2 );

/**
 * Don't let Awesome Flickr plugin load colorbox if WP AJAX Edit Comments is active
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/363
 */
function openlab_fix_colorbox_conflict_1() {
	if ( ! function_exists( 'enqueue_afg_scripts' ) ) {
		return;
	}

	$is_wp_ajax_edit_comments_active = in_array( 'wp-ajax-edit-comments/wp-ajax-edit-comments.php', (array) get_option( 'active_plugins', array() ) );

	remove_action( 'wp_print_scripts', 'enqueue_afg_scripts' );

	if ( ! get_option( 'afg_disable_slideshow' ) ) {
		if ( get_option( 'afg_slideshow_option' ) == 'highslide' ) {
			wp_enqueue_script( 'afg_highslide_js', BASE_URL . '/highslide/highslide-full.min.js' );
		}

		if ( get_option( 'afg_slideshow_option' ) == 'colorbox' && ! $is_wp_ajax_edit_comments_active ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'afg_colorbox_script', BASE_URL . '/colorbox/jquery.colorbox-min.js', array( 'jquery' ) );
			wp_enqueue_script( 'afg_colorbox_js', BASE_URL . '/colorbox/mycolorbox.js', array( 'jquery' ) );
		}
	}
}

add_action( 'wp_print_scripts', 'openlab_fix_colorbox_conflict_1', 1 );

/**
 * Prevent More Privacy Options from displaying its -2 message, and replace with our own
 *
 * See #775
 */
function openlab_swap_private_blog_message() {
	global $current_blog, $ds_more_privacy_options;

	if ( '-2' == $current_blog->public ) {
		remove_action( 'template_redirect', array( &$ds_more_privacy_options, 'ds_members_authenticator' ) );
		add_action( 'template_redirect', 'openlab_private_blog_message', 1 );
	}
}

add_action( 'wp', 'openlab_swap_private_blog_message' );

/**
 * Callback for our own "members only" blog message
 *
 * See #775
 */
function openlab_private_blog_message() {
	global $ds_more_privacy_options;

	$blog_id   = get_current_blog_id();
	$group_id  = openlab_get_group_id_by_blog_id( $blog_id );
	$group_url = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) );
	$user_id   = get_current_user_id();

	if ( is_user_member_of_blog( $user_id, $blog_id ) || is_super_admin() ) {
		return;
	} elseif ( is_user_logged_in() ) {
		openlab_ds_login_header();
		?>
		<form name="loginform" id="loginform" />
		<p>To become a member of this site, please request membership on <a href="<?php echo esc_attr( $group_url ); ?>">the profile page</a>.</p>
		</form>
		</div>
		</body>
		</html>
		<?php
		exit();
	} else {
		if ( is_feed() && isset( $ds_more_privacy_options ) && method_exists( $ds_more_privacy_options, 'ds_feed_login' ) ) {
			$ds_more_privacy_options->ds_feed_login();
		} else {
			nocache_headers();
			header( 'HTTP/1.1 302 Moved Temporarily' );
			header( 'Location: ' . wp_login_url() );
			header( 'Status: 302 Moved Temporarily' );
			exit();
		}
	}
}

/**
 * A version of the More Privacy Options login header without the redirect
 *
 * @see openlab_private_blog_message()
 */
function openlab_ds_login_header() {
	global $error, $is_iphone, $interim_login, $current_site;
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<title><?php _e( 'Private Blog Message' ); ?></title>
			<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
			<?php
			wp_admin_css( 'login', true );
			wp_admin_css( 'colors-fresh', true );

			if ( $is_iphone ) {
				?>
				<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
				<style type="text/css" media="screen">
					form { margin-left: 0px; }
					#login { margin-top: 20px; }
				</style>
				<?php
			} elseif ( isset( $interim_login ) && $interim_login ) {
				?>
				<style type="text/css" media="all">
					.login #login { margin: 20px auto; }
				</style>
				<?php
			}

			do_action( 'login_head' );
			?>
		</head>
		<body class="login">
			<div id="login">
				<h1><a href="<?php echo apply_filters( 'login_headerurl', 'http://' . $current_site->domain . $current_site->path ); ?>" title="<?php echo apply_filters( 'login_headertitle', $current_site->site_name ); ?>"><span class="hide"><?php bloginfo( 'name' ); ?></span></a></h1>
				<?php
}

/**
 * Group member portfolio list widget
 *
 * This function is here (rather than includes/portfolios.php) because it needs
 * to run at 'widgets_init'.
 */
class OpenLab_Course_Portfolios_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'openlab_course_portfolios_widget',
			'Portfolio List',
			array(
				'description' => 'Display a list of the Portfolios belonging to the members of this course.',
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'];

		$name_key   = 'display_name' === $instance['sort_by'] ? 'user_display_name' : 'portfolio_title';
		$group_id   = openlab_get_group_id_by_blog_id( get_current_blog_id() );
		$portfolios = openlab_get_group_member_portfolios( $group_id, $instance['sort_by'] );

		if ( '1' === $instance['display_as_dropdown'] ) {
			echo '<form action="" method="get">';
			echo '<select class="portfolio-goto" name="portfolio-goto">';
			echo '<option value="" selected="selected">Choose a Portfolio</option>';
			foreach ( $portfolios as $portfolio ) {
				echo '<option value="' . esc_attr( $portfolio['portfolio_url'] ) . '">' . esc_attr( $portfolio[ $name_key ] ) . '</option>';
			}
			echo '</select>';
			echo '<input class="openlab-portfolio-list-widget-submit" style="margin-top: .5em" type="submit" value="Go" />';
			wp_nonce_field( 'portfolio_goto', '_pnonce' );
			echo '</form>';
		} else {
			echo '<ul class="openlab-portfolio-links">';
			foreach ( $portfolios as $portfolio ) {
				echo '<li><a href="' . esc_url( $portfolio['portfolio_url'] ) . '">' . esc_html( $portfolio[ $name_key ] ) . '</a></li>';
			}
			echo '</ul>';
		}

		// Some lousy inline CSS
		?>
		<style type="text/css">
			.openlab-portfolio-list-widget-submit {
				margin-top: .5em;
			}
			body.js .openlab-portfolio-list-widget-submit {
				display: none;
			}
		</style>

		<?php
		echo $args['after_widget'];

		$this->enqueue_scripts();
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['display_as_dropdown'] = ! empty( $new_instance['display_as_dropdown'] ) ? '1' : '';
		$instance['sort_by']             = in_array( $new_instance['sort_by'], array( 'random', 'display_name', 'title' ) ) ? $new_instance['sort_by'] : 'display_name';
		$instance['num_links']           = isset( $new_instance['num_links'] ) ? (int) $new_instance['num_links'] : '';
		return $instance;
	}

	public function form( $instance ) {
		$settings = wp_parse_args(
			$instance,
			array(
				'title'               => 'Member Portfolios',
				'display_as_dropdown' => '0',
				'sort_by'             => 'title',
				'num_links'           => false,
			)
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label><br />
			<input name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $settings['title'] ); ?>" />
					</p>

					<p>
			<input name="<?php echo $this->get_field_name( 'display_as_dropdown' ); ?>" id="<?php echo $this->get_field_name( 'display_as_dropdown' ); ?>" value="1" <?php checked( $settings['display_as_dropdown'], '1' ); ?> type="checkbox" />
			<label for="<?php echo $this->get_field_id( 'display_as_dropdown' ); ?>">Display as dropdown</label>
					</p>

					<p>
			<label for="<?php echo $this->get_field_id( 'sort_by' ); ?>">Sort by:</label><br />
			<select name="<?php echo $this->get_field_name( 'sort_by' ); ?>" id="<?php echo $this->get_field_name( 'sort_by' ); ?>">
				<option value="title" <?php selected( $settings['sort_by'], 'title' ); ?>>Portfolio title</option>
				<option value="display_name" <?php selected( $settings['sort_by'], 'display_name' ); ?>>Member name</option>
				<option value="random" <?php selected( $settings['sort_by'], 'random' ); ?>>Random</option>
			</select>
					</p>

		<?php
	}

	protected function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );

		// poor man's dependency - jquery will be loaded by now
		add_action( 'wp_footer', array( $this, 'script' ), 1000 );
	}

	public function script() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('.portfolio-goto').on('change', function () {
					var maybe_url = this.value;
					if (maybe_url) {
						document.location.href = maybe_url;
					}
				});
			}, (jQuery));
		</script>
		<?php
	}
}

/**
 * Register the Course Portfolios widget
 */
function openlab_register_portfolios_widget() {
	register_widget( 'OpenLab_Course_Portfolios_Widget' );
}
add_action( 'widgets_init', 'openlab_register_portfolios_widget' );

/**
 * Utility function for getting the xprofile exclude groups for a given account type
 */
function openlab_get_exclude_groups_for_account_type( $type ) {
	global $wpdb, $bp;

	// phpcs:disable
	$groups = $wpdb->get_results( "SELECT id, name FROM {$bp->profile->table_name_groups}" );
	// phpcs:enable

	// Reindex
	$gs = array();
	foreach ( $groups as $group ) {
		$gs[ $group->name ] = $group->id;
	}

	$exclude_groups = array();
	foreach ( $gs as $gname => $gid ) {
		// special case for alumni
		if ( 'Alumni' === $type && 'Student' === $gname ) {
			continue;
		}

		// otherwise, non-matches are excluded
		if ( $gname !== $type ) {
			$exclude_groups[] = $gid;
		}
	}

	return implode( ',', $exclude_groups );
}

/**
 * Flush rewrite rules when a blog is created.
 *
 * There's a bug in WP that causes rewrite rules to be flushed before
 * taxonomies have been registered. As a result, tag and category archive links
 * do not work. Here we work around the issue by hooking into blog creation,
 * registering the taxonomies, and forcing another rewrite flush.
 *
 * See https://core.trac.wordpress.org/ticket/20171, http://openlab.citytech.cuny.edu/redmine/issues/1054
 */
function openlab_flush_rewrite_rules( $blog_id ) {
	switch_to_blog( $blog_id );
	create_initial_taxonomies();
	flush_rewrite_rules();
	update_option( 'openlab_rewrite_rules_flushed', 1 );
	restore_current_blog();
}
add_action( 'wpmu_new_blog', 'openlab_flush_rewrite_rules', 9999 );

/**
 * Lazyloading rewrite rules repairer.
 *
 * Repairs the damage done by WP's buggy rewrite rules generator for new blogs.
 *
 * See openlab_flush_rewrite_rules().
 */
function openlab_lazy_flush_rewrite_rules() {
	// We load late, so taxonomies should be created by now
	if ( ! get_option( 'openlab_rewrite_rules_flushed' ) ) {
		flush_rewrite_rules();
		update_option( 'openlab_rewrite_rules_flushed', 1 );
	}
}
add_action( 'init', 'openlab_lazy_flush_rewrite_rules', 9999 );

/**
 * Whitelist the 'webcal' protocol.
 *
 * Prevents the protocol from being stripped for non-privileged users.
 */
function openlab_add_webcal_to_allowed_protocols( $protocols ) {
	$protocols[] = 'webcal';
	return $protocols;
}
add_filter( 'kses_allowed_protocols', 'openlab_add_webcal_to_allowed_protocols' );

/**
 * Don't limit upload space on blog 1.
 */
function openlab_allow_unlimited_space_on_blog_1( $check ) {
	if ( 1 === get_current_blog_id() ) {
		return 0;
	}

	return $check;
}
add_filter( 'pre_get_space_used', 'openlab_allow_unlimited_space_on_blog_1' );

/**
 * Set "From" name in outgoing email to the site name.
 *
 * BP did this until 2.5, when the filters were moved to the new email system. Since we're using the legacy emails
 * for now, we must reinstate.
 *
 * @return string The blog name for the root blog.
 */
function openlab_email_from_name_filter() {
	/**
	 * Filters the "From" name in outgoing email to the site name.
	 *
	 * @since BuddyPress (1.2.0)
	 *
	 * @param string $value Value to set the "From" name to.
	 */
	return apply_filters( 'bp_core_email_from_name_filter', bp_get_option( 'blogname', 'WordPress' ) );
}
add_filter( 'wp_mail_from_name', 'openlab_email_from_name_filter' );

function openlab_email_appearance_settings( $settings ) {
	$settings['email_bg']        = '#fff';
	$settings['header_bg']       = '#fff';
	$settings['footer_bg']       = '#fff';
	$settings['highlight_color'] = '#5cd8cd';
	return $settings;
}
add_filter( 'bp_after_email_appearance_settings_parse_args', 'openlab_email_appearance_settings' );

/**
 * Add email link styles to rendered email template.
 *
 * This is only used when the email content has been merged into the email template.
 *
 * @param string $value         Property value.
 * @param string $property_name Email template property name.
 * @param string $transform     How the return value was transformed.
 * @return string Updated value.
 */
function openlab_bp_email_add_link_color_to_template( $value, $property_name, $transform ) {
	if ( 'template' !== $property_name || 'add-content' !== $transform ) {
		return $value;
	}

	$settings    = bp_email_get_appearance_settings();
	$replacement = 'style="color: ' . esc_attr( $settings['body_text_color'] ) . ';';

	// Find all links.
	preg_match_all( '#<a[^>]+>#i', $value, $links, PREG_SET_ORDER );
	foreach ( $links as $link ) {
		$new_link = array_shift( $link );

		$link = $new_link;

		// Add/modify style property.
		if ( strpos( $link, 'style="' ) !== false ) {
			$new_link = str_replace( 'style="', $replacement, $link );
		} else {
			$new_link = str_replace( '<a ', "<a {$replacement}\" ", $link );
		}

		if ( $new_link !== $link ) {
			$value = str_replace( $link, $new_link, $value );
		}
	}

	return $value;
}
remove_filter( 'bp_email_get_property', 'bp_email_add_link_color_to_template', 6, 3 );
add_filter( 'bp_email_get_property', 'openlab_bp_email_add_link_color_to_template', 6, 3 );

/**
 * Group slug blacklist.
 */
function openlab_forbidden_group_names( $names ) {
	$names[] = 'thebuzz';
	$names[] = 'the-buzz';
	$names[] = 'the-hub';
	return $names;
}
add_filter( 'groups_forbidden_names', 'openlab_forbidden_group_names' );

function openlab_disallow_tinymce_comment_stylesheet( $settings ) {
	if ( ! isset( $settings['tinymce'] ) || ! isset( $settings['tinymce']['content_css'] ) ) {
		return $settings;
	}

	if ( false !== strpos( $settings['tinymce']['content_css'], 'tinymce-comment-field-editor' ) ) {
		unset( $settings['tinymce']['content_css'] );
	}

	return $settings;
}
add_filter( 'wp_editor_settings', 'openlab_disallow_tinymce_comment_stylesheet' );

/**
 * Blogs must be public in order for BP to record their activity.
 */
add_filter( 'bp_is_blog_public', '__return_true' );

/**
 * Blacklist some Jetpack modules.
 */
function openlab_blacklist_jetpack_modules( $modules ) {
	$blacklist = array( 'masterbar' );

	foreach ( $blacklist as $module ) {
		unset( $modules[ $module ] );
	}

	return $modules;
}
add_filter( 'jetpack_get_available_modules', 'openlab_blacklist_jetpack_modules' );

/**
 * Disable WP Accessibility toolbar.
 */
add_filter( 'option_wpa_toolbar', '__return_empty_string' );

/**
 * Hide WP Accessibility Toolbar settings.
 */
add_action(
	'admin_footer',
	function() {
		global $pagenow;

		if ( 'options-general.php' !== $pagenow ) {
			return;
		}

		if ( empty( $_GET['page'] ) || 'wp-accessibility/wp-accessibility.php' !== $_GET['page'] ) {
			return;
		}

		?>
<script type="text/javascript">
	jQuery( document ).ready( function() {
		jQuery( '#wpa_toolbar' ).closest( '.postbox' ).hide();
	} );
</script>
		<?php

	}
);

/**
 * Hide target option for Link inserter in editor.
 */
add_action(
	'admin_print_footer_scripts',
	function() {
		global $pagenow;

		if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ] ) ) {
			return;
		}

		if ( 'on' !== get_option( 'wpa_target' ) ) {
			return;
		}
		?>
		<style type="text/css">
			#link-options .link-target,
			.editor-url-popover .editor-url-popover__settings-toggle {
				display: none;
			}
		</style>
		<?php
	}
);

function openlab_wpa_alt_attribute( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	if ( false === strpos( $html, 'alt-missing.png' ) ) {
		return $html;
	}

	$url  = set_url_scheme( home_url() );
	$html = str_replace( 'src=\'' . trailingslashit( $url ) . 'wp-content/plugins/wp-accessibility/imgs/alt-missing.png\'', 'src=\'' . trailingslashit( $url ) . 'wp-content/mu-plugins/img/AltTextWarning.png\'', $html );
	return $html;
}
add_filter( 'image_send_to_editor', 'openlab_wpa_alt_attribute', 20, 8 );

/**
 * Provide a default value for WP Accessibility settings.
 */
function openlab_wpa_return_on() {
	return 'on';
}
add_filter( 'default_option_wpa_target', 'openlab_wpa_return_on' );
add_filter( 'default_option_wpa_search', 'openlab_wpa_return_on' );
add_filter( 'default_option_wpa_tabindex', 'openlab_wpa_return_on' );
add_filter( 'default_option_wpa_image_titles', 'openlab_wpa_return_on' );
add_filter( 'default_option_rta_from_tag_clouds', 'openlab_wpa_return_on' );

/**
 * Prevent wp-accessibility from adding its own Log Out link to the toolbar.
 */
add_action(
	'plugins_loaded',
	function() {
		remove_action( 'admin_bar_menu', 'wpa_logout_item', 11 );
	}
);

/**
 * Unregister wp-accessibility widget.
 */
add_action(
	'widgets_init',
	function() {
		unregister_widget( 'wp_accessibility_toolbar' );
	},
	20
);

/**
 * Force bbPress roles to have the 'read' capability.
 *
 * Without 'read', users can't access my-sites.php.
 */
add_filter(
	'bbp_get_dynamic_roles',
	function( $roles ) {
		foreach ( $roles as &$role ) {
			$role['capabilities']['read'] = true;
		}

		return $roles;
	}
);

/**
 * Don't let Ultimate Category Excluder operate on loops other than the main loop.
 *
 * Prevents conflicts with Category Sticky Post. See #2263.
 */
add_action(
	'pre_get_posts',
	function( $query ) {
		if ( ! $query->is_main_query() ) {
			remove_filter( 'pre_get_posts', 'ksuce_exclude_categories' );
		}
	},
	0
);

add_filter(
	'mime_types',
	function( $types ) {
		// AutoCAD - #2332.
		$types['ctb|stb']         = 'application/octet-stream';
		$types['dwg|dxf|acd|dwt'] = 'application/acad';
		$types['vwx']             = 'application/vnd.vectorworks';

		return $types;
	}
);

/**
 * Strict mime-type fixes.
 */
function openlab_secondary_mime( $check, $filetype, $filename, $mimes ) {
	if ( empty( $check['ext'] ) && empty( $check['type'] ) ) {
		$secondary_mimes = [
			[ 'tex' => 'text/x-tex' ],
		];

		foreach ( $secondary_mimes as $secondary_mime ) {
			// Run another check, but only for our secondary mime and not on core mime types.
			remove_filter( 'wp_check_filetype_and_ext', 'openlab_secondary_mime', 99, 4 );
			$check = wp_check_filetype_and_ext( $file, $filename, $secondary_mime );
			add_filter( 'wp_check_filetype_and_ext', 'openlab_secondary_mime', 99, 4 );

			if ( ! empty( $check['ext'] ) || ! empty( $check['type'] ) ) {
				return $check;
			}
		}
	}

	return $check;
}
add_filter( 'wp_check_filetype_and_ext', 'openlab_secondary_mime', 99, 4 );

/** TablePress mods **********************************************************/

/**
 * Pagination should be disabled by default.
 */
add_filter(
	'tablepress_table_template',
	function( $table ) {
		$table['options']['datatables_paginate'] = false;
		return $table;
	}
);

/**
 * Don't let TablePress save CSS to a file.
 */
add_filter( 'tablepress_save_custom_css_to_file', '__return_false' );

/**
 * Don't let users uninstall TablePress.
 */
add_filter(
	'map_meta_cap',
	function( $caps, $cap, $user_id ) {
		if ( 'tablepress_delete_tables' !== $cap ) {
			return $caps;
		}

		return array( 'do_not_allow' );
	},
	10,
	4
);

/**
 * DK PDF cache directory.
 */
add_filter(
	'dkpdf_mpdf_temp_dir',
	function( $dir ) {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/dkpdf-tmp';
	}
);

/**
 * School/Office/Department selector markup.
 *
 * @param array $args {
 *   @type array $checked Multidimensional array of schools/offices/departments to be checked.
 *   @type array $entities Top-level entities to be included.
 *   @type bool  $legacy Whether to use the legacy interface.
 *   @type bool  $required
 * }
 */
function openlab_academic_unit_selector( $args = array() ) {
	$_checked = $args['checked'] ?: array();
	$checked  = array_merge(
		array(
			'schools'     => array(),
			'offices'     => array(),
			'departments' => array(),
		),
		$_checked
	);

	$allowed_entities = [ 'school', 'office' ];
	if ( isset( $args['entities'] ) && is_array( $args['entities'] ) ) {
		$entities = array_intersect( $args['entities'], $allowed_entities );
	} else {
		$entities = $allowed_entities;
	}

	$legacy   = ! empty( $args['legacy'] );
	$required = ! empty( $args['required'] );

	$schools = openlab_get_school_list();
	$offices = openlab_get_office_list();

	// Flatten and alphabetize.
	$_departments = openlab_get_entity_departments();
	$departments  = array();
	foreach ( $_departments as $_entity_slug => $_depts ) {
		foreach ( $_depts as $_dept_slug => $_dept_value ) {
			$_dept_value['parent'] = $_entity_slug;
			$_dept_value['slug']   = $_dept_slug;

			/*
			 * Indexes must be unique per parent+child combo, as items may appear under
			 * more than one parent.
			 */
			$dept_index = $_entity_slug . '_' . $_dept_slug;

			$departments[ $dept_index ] = $_dept_value;
		}
	}

	uasort(
		$departments,
		function( $a, $b ) {
			return strnatcasecmp( $a['label'], $b['label'] );
		}
	);

	wp_enqueue_script( 'openlab-academic-units' );

	$selector_class = 'academic-unit-selector';
	$legend_class   = '';
	if ( $legacy ) {
		$selector_class .= ' academic-unit-selector-legacy';
		$legend_class   .= 'sr-only';
	}

	$required_gloss = $required ? '(required)' : '';

	?>

	<div class="<?php echo esc_attr( $selector_class ); ?>">

	<?php if ( in_array( 'school', $entities, true ) ) : ?>
		<fieldset class="school-selector">
			<legend class="<?php echo esc_attr( $legend_class ); ?>">Schools:</legend>

			<div class="school-inputs entity-inputs">
				<ul>
				<?php foreach ( $schools as $school_slug => $school_label ) : ?>
					<li>
						<label>
							<input name="schools[]" class="academic-unit-checkbox" type="checkbox" value="<?php echo esc_attr( $school_slug ); ?>" <?php checked( in_array( $school_slug, $checked['schools'], true ) ); ?> /> <?php echo esc_html( $school_label ); ?>
						</label>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</fieldset>
	<?php endif; ?>

	<?php if ( in_array( 'office', $entities, true ) ) : ?>
		<fieldset class="office-selector">
			<legend>Offices:</legend>

			<div class="office-inputs entity-inputs">
				<ul>
				<?php foreach ( $offices as $office_slug => $office_label ) : ?>
					<li>
						<label>
							<input class="academic-unit-checkbox" name="offices[]" type="checkbox" value="<?php echo esc_attr( $office_slug ); ?>" <?php checked( in_array( $office_slug, $checked['offices'], true ) ); ?> /> <?php echo esc_html( $office_label ); ?>
						</label>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</fieldset>
	<?php endif; ?>

	<fieldset class="department-selector">
		<legend>Departments <?php echo esc_html( $required_gloss ); ?></legend>
		<div class="error-container" id="academic-unit-selector-error"></div>
		<div class="checkbox-list-container department-list-container">
			<div class="cboxol-units-of-type">
				<ul>
				<?php foreach ( $departments as $dept_index => $dept ) : ?>
					<?php $dept_slug = $dept['slug']; ?>
					<li class="academic-unit academic-unit-visible">
						<?php
						$parent_attr = $dept['parent'];
						$id_attr     = 'academic-unit-' . $dept_index;
						?>

						<input
							<?php checked( in_array( $dept_slug, $checked['departments'], true ) ); ?>
							class="academic-unit-checkbox"
							data-parent="<?php echo esc_attr( $parent_attr ); ?>"
							id="<?php echo esc_attr( $id_attr ); ?>"
							name="departments[]"
							type="checkbox"
							value="<?php echo esc_attr( $dept_slug ); ?>"
							data-parsley-atleastonedept
							data-parsley-errors-container="#academic-unit-selector-error"
							data-parsley-validate-if-empty
						/> <label class="passive" for="<?php echo esc_attr( $id_attr ); ?>"><?php echo esc_html( $dept['label'] ); ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</fieldset>

	<?php wp_nonce_field( 'openlab_academic_unit_selector', 'openlab-academic-unit-selector-nonce' ); ?>

	</div>

	<?php
}

/**
 * Don't allow TGMPA to show admin notices.
 *
 * This is meant specifically for TinyMCE Comment Field. See #2452.
 */
if ( is_admin() ) {
	add_filter(
		'get_user_metadata',
		function( $retval, $user_id, $meta_key ) {
			if ( 'tgmpa_dismissed_notice_tgmpa' !== $meta_key ) {
				return $retval;
			}

			return 1;
		},
		10,
		3
	);
}

/**
 * Set default editor settings.
 *
 * By default, the Classic editor should be the default editor,
 * and users should be allowed to switch editors. Site admins can override.
 */
add_filter(
	'classic_editor_network_default_settings',
	function() {
		return [
			'editor'      => 'classic',
			'allow-users' => true,
		];
	}
);

/**
 * Enqueue custom JS for Search & Filter, when activated.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		if ( ! defined( 'SEARCHANDFILTER_VERSION_NUM' ) ) {
			return;
		}

		wp_enqueue_script( 'openlab-search-filter', set_url_scheme( WPMU_PLUGIN_URL . '/js/search-filter.js' ), array( 'jquery' ) );
	}
);

function openlab_sanitize_url_params( $url ) {
	$request_params = parse_url( $url, PHP_URL_QUERY );
	parse_str( $request_params, $params );
	$param_keys = array_keys( $params );

	if ( isset( $params['usertype'] ) && ! in_array( $params['usertype'], openlab_valid_user_types(), true ) ) {
		unset( $params['usertype'] );
	}

	$url = remove_query_arg( $param_keys, $url );
	$url = add_query_arg( $params, $url );

	return $url;
}
