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
		add_action( 'save_post', array( __CLASS__, 'save_meta' ) );

		add_action( 'admin_print_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'admin_styles' ) );
		add_action( 'wp_ajax_cac_ncs_groups_query', array( __CLASS__, 'cac_ncs_groups_query' ) );
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

	public static function save_meta( $post_id ) {
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

		if ( $post_type == static::POST_TYPE ) {
			update_post_meta( $post_id, 'cac_ncs_vcode', $vcode );
			update_post_meta( $post_id, 'cac_ncs_groups', explode( ',', $_POST['cac_ncs_group_ids'] ) );
		}

		return $post_id;
	}

	public static function admin_scripts() {
		global $post;

		if ( isset( $post->post_type ) && static::POST_TYPE == $post->post_type ) {
			wp_enqueue_script( 'suggest' );
			wp_enqueue_script( 'cac_ncs_js', WP_CONTENT_URL . '/plugins/cac-non-cuny-signup/js/admin.js', array( 'jquery', 'suggest' ) );
		}
	}

	public static function admin_styles() {
		global $post;

		if ( isset( $post->post_type ) && static::POST_TYPE == $post->post_type ) {
			wp_enqueue_style( 'cac_ncs_css', WP_CONTENT_URL . '/plugins/cac-non-cuny-signup/css/admin.css' );
		}
	}

	public static function cac_ncs_groups_query() {
		$q      = isset( $_REQUEST['query'] ) ? urldecode( $_REQUEST['query'] ) : false;
		$retval =  array(
			'query'       => $q,
			'suggestions' => array(),
			'data'        => array()
		);

		if ( $q ) {
			stataic::$search_terms = $q;

			// Shitty but necessary. 'search_terms' is too generous
			add_filter( 'bp_groups_get_paged_groups_sql', array( __CLASS__, 'filter_group_sql' ) );
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

	public static function filter_group_sql( $sql ) {
		$sqla = explode( 'WHERE', $sql );

		$new_sql = $sqla[0] . ' WHERE g.name LIKE "%' . static::$search_terms . '%" AND ' . $sqla[1];

		return $new_sql;
	}

	/**
	 * Static Methods
	 */

	public static function get_wp_query_args( $code = false ) {
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

	public static function validate_code( $code = false ) {
		$validate = false;

		if ( $code ) {
			$args = self::get_wp_query_args( $code );

			$vcodes = new \WP_Query( $args );

			$validate = $vcodes->have_posts();
		}

		return $validate;
	}

	public static function get_code_data( $code = false ) {
		$data = false;

		if ( $code ) {
			$args = self::get_wp_query_args( $code );

			$vcodes = new \WP_Query( $args );

			// Avoid the BS and just take the first one
			$data = isset( $vcodes->posts[0] ) ? $vcodes->posts[0] : false;

			// Pull up the postmeta
			$data->vcode = get_post_meta( $data->ID, 'cac_ncs_vcode', true );
			$data->groups = get_post_meta( $data->ID, 'cac_ncs_groups', true );
		}

		return $data;
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
