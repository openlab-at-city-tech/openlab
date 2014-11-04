<?php
/**
 * AJAX handler for registration email check
 *
 * Return values:
 *   1: success
 *   2: no email provided
 *   3: not a valid email address
 *   4: unsafe
 *   5: not in domain whitelist
 *   6: email exists
 *   7: a known undergraduate student address
 */
function cac_ajax_email_check() {
	$email = isset( $_POST['email'] ) ? $_POST['email'] : false;

	$retval = '1';

	if ( !$email ) {
		$retval = '2'; // no email
	} else {
		if ( !is_email( $email ) ) {
			$retval = '3'; // Not an email address
		} else if ( function_exists( 'is_email_address_unsafe' ) && is_email_address_unsafe( $email ) ) {
			$retval = '4'; // Unsafe
		} else if ( ! cac_ncs_email_domain_is_in_whitelist( $email ) ) {
			$retval = '5';
		} else if ( email_exists( $email ) ) {
			$retval = '6';
		}
	}

	die( $retval );
}
add_action( 'wp_ajax_cac_ajax_email_check', 'cac_ajax_email_check' );
add_action( 'wp_ajax_nopriv_cac_ajax_email_check', 'cac_ajax_email_check' );

/**
 * AJAX handler for registration code check
 *
 * Return values:
 *   1: success
 *   0: failure
 */
function cac_ajax_vcode_check() {
	$vcode = isset( $_POST['code'] ) ? $_POST['code'] : '';

	$retval = cac_ncs_validate_code( $vcode ) ? '1' : '0';

	echo $retval;
	die();
}
add_action( 'wp_ajax_cac_ajax_vcode_check', 'cac_ajax_vcode_check' );
add_action( 'wp_ajax_nopriv_cac_ajax_vcode_check', 'cac_ajax_vcode_check' );

/**
 * Verify that an email address is from a whitelisted domain.
 */
function cac_ncs_email_domain_is_in_whitelist( $email ) {
	$domains = array(
		'mail.citytech.cuny.edu',
		'citytech.cuny.edu',
	);

	$email = explode( '@', trim( $email ) );

	if ( ! isset( $email[1] ) || ! in_array( $email[1], $domains, true ) ) {
		return false;
	}

	return true;
}

/**
 * Checks non-CUNY validation codes
 */
function cac_check_signup_validation_code( $result ) {

	foreach( $result['errors']->errors as $error_key => $error ) {
		if ( in_array( 'Sorry, that email address is not allowed!', $error ) ) {
			// Check for a validation code
			if ( isset( $_POST['signup_validation_code'] ) ) {
				$vcode = $_POST['signup_validation_code'];

				if ( cac_ncs_validate_code( $vcode ) ) {
					unset( $result['errors']->errors['user_email'] );
				} else {

					// Otherwise we will have to add a new error and hook something into

					// the registration template. See how BP does this.

					$result['errors']->errors['user_email'][0] = 'Non-CUNY registrations are only permitted with a specially provided signup code.';
				}
			}
		}
	}

	return $result;
}
add_filter( 'bp_core_validate_user_signup', 'cac_check_signup_validation_code', 5 );

/**
 * At user signup, grab the code details and stash in wp_signup usermeta
 *
 * This will then have to be loaded at activation time and recorded in wp_usermeta
 */
function cac_ncs_signup_meta( $usermeta ) {
	if ( isset( $_POST['signup_validation_code'] ) ) {
		$data = CAC_NCS_Schema::get_code_data( $_POST['signup_validation_code'] );
		$usermeta['cac_signup_code'] = $data;
	}

	return $usermeta;
}
add_action( 'bp_signup_usermeta', 'cac_ncs_signup_meta' );

/**
 * At user activation, grabs CAC signup code data and moves to usermeta
 */
function cac_ncs_activation_meta( $user_id, $key, $user ) {

	if ( isset( $user['meta']['cac_signup_code'] ) ) {
		update_user_meta( $user_id, 'cac_signup_code_data', $user['meta']['cac_signup_code'] );
		update_user_meta( $user_id, 'cac_signup_code', $user['meta']['cac_signup_code']->ID );

		if ( isset( $user['meta']['cac_signup_code']->groups ) ) {
			foreach ( (array)$user['meta']['cac_signup_code']->groups as $group_id ) {
				groups_join_group( $group_id, $user_id );
			}
		}
	}
}
add_action( 'bp_core_activated_user', 'cac_ncs_activation_meta', 10, 3 );

/**
 * Validates a code
 */
function cac_ncs_validate_code( $code = false ) {
	return CAC_NCS_Schema::validate_code( $code );
}

/**
 * Data schema class
 */
class CAC_NCS_Schema {
	var $post_type_name;
	var $search_terms;

	function __construct() {
		$this->post_type_name = 'cac_ncs_code';

		add_action( 'init', array( $this, 'register_post_type' ), 99 );
		add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );

		add_action( 'admin_print_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_styles' ) );
		add_action( 'wp_ajax_cac_ncs_groups_query', array( $this, 'cac_ncs_groups_query' ) );
	}

	function register_post_type() {
		$labels = array(
			'name'		=> 'Non-CUNY Signup Codes',
			'singular_name' => 'Signup Code',
			'add_new'	=> 'Add New',
			'all_items'	=> 'All Signup Codes',
			'add_new_item'	=> 'Add New Signup Code',
			'edit_item'	=> 'Edit Signup Code',
			'new_item'	=> 'New Signup Code',
			'view_item'	=> 'View Signup Code',
			'search_items'	=> 'Search Signup Codes',
			'not_found'	=> 'No Signup Codes found',
			'not_found_in_trash' => 'No Signup Codes found in trash'
		);

		register_post_type( $this->post_type_name, array(
			'labels' => $labels,
			'public' => false,
			'show_in_menu' => current_user_can( 'delete_users' ),
			'show_ui' => true,
			'supports' => array( 'title' )
		) );
	}

	function add_meta_boxes() {
		add_meta_box( 'cac_ncs_vcode', "Code", array( $this, 'code_meta_box_render' ), $this->post_type_name, "normal", "high" );
		add_meta_box( 'cac_ncs_groups', "Add to Groups", array( $this, 'group_meta_box_render' ), $this->post_type_name, "normal" );
	}

	function code_meta_box_render() {
		global $post;

		$vcode = isset( $post->ID ) ? get_post_meta( $post->ID, 'cac_ncs_vcode', true ) : '';

		echo '<input type="text" name="cac_ncs_vcode" value="' . esc_attr( $vcode ) . '" />';
	}

	function group_meta_box_render() {
		global $post;

		$groups = isset( $post->ID ) ? get_post_meta( $post->ID, 'cac_ncs_groups', true ) : array();

		if ( !$groups )
			$groups = array();

		echo '<input type="text" id="cac_ncs_groups" name="cac_ncs_groups" />';

		echo '<ul class="cac-ncs-groups-results">';
		foreach( $groups as $group_id ) {
			$group_obj = new BP_Groups_Group( $group_id );

			if ( empty( $group_obj->name ) )
				continue;

			echo '<li id="cac-nsc-add-to-group-' . $group_id . '">' . $group_obj->name . ' <span class="cac-nsc-remove-group"><a href="#">x</a></span></li>';
		}
		echo '</ul>';

		echo '<input type="hidden" id="cac_nsc_group_ids" name="cac_ncs_group_ids" value="' . implode( ',', $groups ) . '" />';
	}

	function save_meta( $post_id ) {
		global $post;

		// WP's autosave javascript will wipe out our custom fields
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$vcode = isset( $_POST['cac_ncs_vcode'] ) ? $_POST['cac_ncs_vcode'] : '';
		$groups = isset( $_POST['cac_ncs_group_ids'] ) ? explode( ',', $_POST['cac_ncs_group_ids'] ) : '';

		if ( isset( $_POST['post_type'] ) ) {
			$post_type = $_POST['post_type'];
		} else if ( isset( $post->post_type ) ) {
			$post_type = $post->post_type;
		} else {
			$post_type = '';
		}

		if ( $post_type == $this->post_type_name ) {
			update_post_meta( $post_id, 'cac_ncs_vcode', $vcode );
			update_post_meta( $post_id, 'cac_ncs_groups', explode( ',', $_POST['cac_ncs_group_ids'] ) );
		}

		return $post_id;
	}

	/**
	 * STATIC METHODS
	 */

	function get_wp_query_args( $code = false ) {
		$args = array(
			'post_type'  => 'cac_ncs_code',
			'meta_query' => array(
				array(
					'key' => 'cac_ncs_vcode',
					'value' => $code,
					'compare' => '='
				)
			)
		);

		return $args;
	}

	function validate_code( $code = false ) {
		$validate = false;

		if ( $code ) {
			$args = self::get_wp_query_args( $code );

			$vcodes = new WP_Query( $args );

			$validate = $vcodes->have_posts();
		}

		return $validate;
	}

	function get_code_data( $code = false ) {
		$data = false;

		if ( $code ) {
			$args = self::get_wp_query_args( $code );

			$vcodes = new WP_Query( $args );

			// Avoid the BS and just take the first one
			$data = isset( $vcodes->posts[0] ) ? $vcodes->posts[0] : false;

			// Pull up the postmeta
			$data->vcode = get_post_meta( $data->ID, 'cac_ncs_vcode', true );
			$data->groups = get_post_meta( $data->ID, 'cac_ncs_groups', true );
		}

		return $data;
	}

	function admin_scripts() {
		global $post;

		if ( isset( $post->post_type ) && $this->post_type_name == $post->post_type ) {
			wp_enqueue_script( 'suggest' );
			wp_enqueue_script( 'cac_ncs_js', WP_CONTENT_URL . '/plugins/cac-non-cuny-signup/js/admin.js', array( 'jquery', 'suggest' ) );
		}
	}

	function admin_styles() {
		global $post;

		if ( isset( $post->post_type ) && $this->post_type_name == $post->post_type ) {
			wp_enqueue_style( 'cac_ncs_css', WP_CONTENT_URL . '/plugins/cac-non-cuny-signup/css/admin.css' );
		}
	}

	function cac_ncs_groups_query() {
		$q = isset( $_REQUEST['query'] ) ? urldecode( $_REQUEST['query'] ) : false;
		$retval = array(
			'query' 	=> $q,
			'suggestions' 	=> array(),
			'data' 		=> array()
		);

		if ( $q ) {
			$this->search_terms = $q;

			// Shitty but necessary. 'search_terms' is too generous
			add_filter( 'bp_groups_get_paged_groups_sql', array( $this, 'filter_group_sql' ) );
			if ( bp_has_groups() ) {
				while ( bp_groups() ) {
					bp_the_group();
					$retval['suggestions'][] = bp_get_group_name();
					$retval['data'][]	 = bp_get_group_id();
				}
			}
		}

		die( json_encode( $retval ) );
	}

	function filter_group_sql( $sql ) {
		global $bp;

		$sqla = explode( 'WHERE', $sql );

		$new_sql = $sqla[0] . ' WHERE g.name LIKE "%' . $this->search_terms . '%" AND ' . $sqla[1];

		return $new_sql;
	}

}
$cac_ncs_schema = new CAC_NCS_Schema;
