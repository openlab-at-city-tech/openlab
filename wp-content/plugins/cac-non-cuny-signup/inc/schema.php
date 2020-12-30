<?php
/**
 * Core data schema.
 */

namespace OpenLab\SignupCodes;

/**
 * Data schema class
 */
class Schema {
	public const POST_TYPE = 'cac_ncs_code';

	protected static $search_terms = null;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 99 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'expiration_date_render' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta' ), 10, 2 );

		add_action( 'admin_print_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'admin_styles' ) );
		add_action( 'wp_ajax_cac_ncs_groups_query', array( __CLASS__, 'cac_ncs_groups_query' ) );

		add_action( 'pre_get_users', array( __CLASS__, 'filter_users' ) );
	}

	public static function register_post_type() {
		$labels = array(
			'name'               => 'Non-CUNY Signup Codes',
			'singular_name'      => 'Signup Code',
			'add_new'            => 'Add New',
			'all_items'          => 'All Signup Codes',
			'add_new_item'       => 'Add New Signup Code',
			'edit_item'          => 'Edit Signup Code',
			'new_item'           => 'New Signup Code',
			'view_item'          => 'View Signup Code',
			'search_items'       => 'Search Signup Codes',
			'not_found'          => 'No Signup Codes found',
			'not_found_in_trash' => 'No Signup Codes found in trash'
		);

		register_post_type( static::POST_TYPE, array(
			'labels'       => $labels,
			'public'       => false,
			'show_in_menu' => current_user_can( 'delete_users' ),
			'show_ui'      => true,
			'supports'     => array( 'title' )
		) );
	}

	public static function add_meta_boxes() {
		add_meta_box( 'cac_ncs_vcode', 'Code', array( __CLASS__, 'code_meta_box_render' ), static::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'cac_ncs_groups', 'Add to Groups', array( __CLASS__, 'group_meta_box_render' ), static::POST_TYPE, 'normal' );
		add_meta_box( 'cac_ncs_users', 'Users', array( __CLASS__, 'users_meta_box_render' ), static::POST_TYPE, 'side', 'low' );
		add_meta_box( 'cac_ncs_account_type', 'Account Type', array( __CLASS__, 'account_type_meta_box_render' ), static::POST_TYPE, 'side' );
	}

	public static function expiration_date_render( $post ) {
		$timestamp = get_post_meta( $post->ID, 'olsc_expiration_date', true );
		$date      = date_create( '@' . $timestamp );

		static::view( 'expiration-date', [
			'date' => $date ? $date->format( 'm-d-Y' ) : '',
		] );
	}

	public static function code_meta_box_render( $post ) {
		static::view( 'code', [
			'vcode' => get_post_meta( $post->ID, 'cac_ncs_vcode', true ),
		] );
	}

	public static function group_meta_box_render( $post ) {
		$group_ids = get_post_meta( $post->ID, 'cac_ncs_groups', true );
		$group_ids = empty( $group_ids ) ? [] : $group_ids;

		$groups = array_map( 'groups_get_group', $group_ids );
		$groups = array_filter( $groups, function( $group ) {
			return ! empty( $group->name );
		} );

		static::view( 'groups', [
			'group_ids' => $group_ids,
			'groups'    => $groups,
		] );
	}

	/**
	 * Renders metabox for filtering users by signup code.
	 *
	 * @param \WP_Post $post
	 * @return void
	 */
	public static function users_meta_box_render( $post ) {
		static::view( 'users', [
			'url' => add_query_arg( 'ol_signup_code', $post->ID, self_admin_url( 'users.php' ) ),
		] );
	}

	/**
	 * Renders metabox to associate code with the account type.
	 *
	 * @param \WP_Post $post
	 * @return void
	 */
	public static function account_type_meta_box_render( $post ) {
		$type     = get_post_meta( $post->ID, 'olsc_account_type', true );
		$field_id = xprofile_get_field_id_from_name( 'Account Type' );
		$field    = new \BP_XProfile_Field( $field_id );

		static::view( 'account-type', [
			'account_type' => ! empty( $type ) ? $type : 'Non-City Tech',
			'options'      => $field->get_children(),
		] );
	}

	public static function save_meta( $post_id, $post ) {
		$nonce = isset( $_POST['openlab_signup_codes_nonce'] ) ? $_POST['openlab_signup_codes_nonce'] : null;

		if ( ! wp_verify_nonce( $nonce, 'openlab_signup_codes' ) ) {
			return $post_id;
		}

		// WP's autosave javascript will wipe out our custom fields
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( $post->post_type !== static::POST_TYPE ) {
			return $post_id;
		}

		$vcode = isset( $_POST['cac_ncs_vcode'] ) ? $_POST['cac_ncs_vcode'] : '';
		$groups = isset( $_POST['cac_ncs_group_ids'] ) ? explode( ',', $_POST['cac_ncs_group_ids'] ) : null;

		update_post_meta( $post_id, 'cac_ncs_vcode', $vcode );
		update_post_meta( $post_id, 'cac_ncs_groups', $groups );

		// Validate and save account type.
		if ( ! empty( $_POST['olsc_account_type'] ) ) {
			$field_id = xprofile_get_field_id_from_name( 'Account Type' );
			$field    = new \BP_XProfile_Field( $field_id );
			$options  = wp_list_pluck( $field->get_children(), 'name' );

			if ( in_array( $_POST['olsc_account_type'], $options, true ) ) {
				update_post_meta( $post_id, 'olsc_account_type', $_POST['olsc_account_type'] );
			}
		}

		// Expiration date.
		if ( ! empty( $_POST['olsc_expiration_date'] ) ) {
			$expires = \DateTime::createFromFormat( 'm-d-Y', $_POST['olsc_expiration_date'] );

			if ( $expires ) {
				update_post_meta( $post_id, 'olsc_expiration_date', $expires->getTimestamp() );
			}
		} else {
			delete_post_meta( $post_id, 'olsc_expiration_date' );
		}

		return $post_id;
	}

	public static function admin_scripts() {
		global $current_screen;

		if ( $current_screen->post_type !== static::POST_TYPE ) {
			return;
		}

		wp_register_script(
			'olsc-autocomplete',
			plugins_url( 'js/autocomplete.min.js', PLUGIN_FILE ),
			[ 'jquery' ],
			'1.4.11',
			true
		);

		wp_enqueue_script(
			'olsc-admin-js',
			plugins_url( 'js/admin.js', PLUGIN_FILE ),
			[ 'olsc-autocomplete' ],
			'2.0.0',
			true
		);
	}

	public static function admin_styles() {
		global $current_screen;

		if ( $current_screen->post_type !== static::POST_TYPE ) {
			return;
		}

		wp_enqueue_style( 'olsc', plugins_url( 'css/admin.css', PLUGIN_FILE ) );
	}

	public static function cac_ncs_groups_query() {
		$q      = isset( $_REQUEST['query'] ) ? urldecode( $_REQUEST['query'] ) : false;
		$retval =  array(
			'query'       => $q,
			'suggestions' => array(),
			'data'        => array()
		);

		if ( $q ) {
			static::$search_terms = $q;

			// Shitty but necessary. 'search_terms' is too generous
			add_filter( 'bp_groups_get_paged_groups_sql', array( __CLASS__, 'filter_group_sql' ) );
			if ( bp_has_groups() ) {
				while ( bp_groups() ) {
					bp_the_group();
					$retval['suggestions'][] = [
						'value' => bp_get_group_name(),
						'data'  => bp_get_group_id(),
					];
				}
			}
		}

		die( json_encode( $retval ) );
	}

	public static function filter_group_sql( $sql ) {
		$sqla = explode( 'WHERE', $sql );

		$new_sql = $sqla[0] . ' WHERE g.name LIKE "%' . static::$search_terms . '%" AND ' . $sqla[1];

		return $new_sql;
	}

	/**
	 * Filter dashboard Users page based on "Signup Code" id.
	 *
	 * @param \WP_User_Query $user_query
	 * @return void
	 */
	public static function filter_users( $user_query ) {
		global $pagenow;

		if ( ! is_admin() && 'users.php' !== $pagenow ) {
			return;
		}

		// This is post type ID, so we're expecting a integer.
		$code = isset( $_GET['ol_signup_code'] ) ? absint( $_GET['ol_signup_code'] ) : 0;

		if ( $code ) {
			$user_query->set( 'meta_key', 'cac_signup_code' );
			$user_query->set( 'meta_value', $code );
		}
	}

	/**
	 * Static Methods
	 */

	public static function get_wp_query_args( $code = false ) {
		return [
			'post_type'              => 'cac_ncs_code',
			'posts_per_page'         => 1,
			'update_post_term_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'cac_ncs_vcode',
					'value'   => $code,
					'compare' => '='
				],
			],
		];
	}

	/**
	 * Get code data.
	 *
	 * @param string $code Signup code.
	 * @return \WP_Post|null
	 */
	public static function get_code_data( $code = null ) {
		if ( ! $code ) {
			return null;
		}

		$query = new \WP_Query( self::get_wp_query_args( $code ) );
		if ( ! $query->found_posts ) {
			return null;
		}

		// Get the first entry.
		$data = $query->posts[ 0 ];

		// Setup metadata.
		$data->vcode = get_post_meta( $data->ID, 'cac_ncs_vcode', true );
		$data->groups = get_post_meta( $data->ID, 'cac_ncs_groups', true );

		return $data;
	}

	/**
	 * Checks if code is valid.
	 *
	 * @param string $code Signup code.
	 * @return bool
	 */
	public static function is_code_valid( $code = null ) {
		$data = static::get_code_data( $code );

		if ( ! $data ) {
			return false;
		}

		if ( static::is_code_expired( $data ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if code has expired.
	 *
	 * @param \WP_Post $data Code post instance.
	 * @return bool
	 */
	public static function is_code_expired( \WP_Post $data ) {
		$current = time();
		$expires = (int) get_post_meta( $data->ID, 'olsc_expiration_date', true );

		// Code has no expiration date.
		if ( empty( $expires ) ) {
			return false;
		}

		return $expires < $current;
	}

	/**
	 * Helper method for rendering views.
	 *
	 * @param string $name Name of the view.
	 * @param array $args Arguments passed to the view.
	 * @return void
	 */
	public static function view( $name, array $args = [] ) {
		extract( $args, EXTR_SKIP );

		include PLUGIN_DIR . '/views/'. $name . '.php';
	}
}
