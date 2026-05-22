<?php
namespace Gravity_Forms\Gravity_Forms_APC;

defined( 'ABSPATH' ) || die();

use GF_Advanced_Post_Creation;
use WP_User;
use GFAPI;
use GF_Block_APC_Posts;
/**
 * Gravity Forms Advanced Posts Creation Posts list handler.
 *
 * This class acts as a wrapper for the posts list shortcode.
 *
 * @since      1.6.0
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2025, Rocketgenius
 */
class Posts_List_Handler {

	/**
	 * Instance of a GF_Advanced_Post_Creation object.
	 *
	 * @since  1.6.0
	 *
	 * @var GF_Advanced_Post_Creation
	 */
	protected $addon;

	/**
	 * The logged in user that the links are being retrieved for.
	 *
	 * @since  1.6.0
	 *
	 * @var WP_User
	 */
	protected $user;

	/**
	 * Number of shortcode/block instances on the page.
	 *
	 * @since 1.6.0
	 *
	 * @var int
	 */
	public static $instance_count = 0;

	/**
	 * Posts_List_Handler constructor.
	 *
	 * @since  1.6.0
	 *
	 * @param GF_Advanced_Post_Creation $addon Instance of a GF_Advanced_Post_Creation object.
	 */
	public function __construct( $addon ) {
		$this->addon = $addon;
	}

	/**
	* Displays the list of posts for the logged in user.
	*
	* @since  1.6.0
	*
	* @param string $shortcode_string The full shortcode string.
	* @param array  $attributes       The attributes within the shortcode.
	* @param string $content          The content of the shortcode, if available.
	*
	* @return string
	*/
	public function posts_list_shortcode( $shortcode_string, $attributes, $content ) {
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract(
			shortcode_atts(
				array(
					'form_id'        => 0,
					'formToShow'     => 0,
					'posts_per_page' => 5,
					'header_text'    => '',
					'button_color'   => '',
					'client_id'      => wp_generate_uuid4(),
					'style'          => 'light',
					'transparent_bg' => false,
					'hide_date'      => false,
					'hide_status'    => false,
				),
				$attributes
			)
		);

		/**
		 * Change the array keys from snake_case to camelCase to be compatible with the block attributes.
		 * The shortcode doesn't use the formsToShow attribute, so we override it with the form_id value.
		 */
		$attributes = array(
			'formId'        => $form_id,
			'formToShow'    => $form_id,
			'postsPerPage'  => $posts_per_page,
			'gridTitle'     => $header_text,
			'buttonColor'   => $button_color,
			'clientId'      => $client_id,
			'gridStyle'     => $style,
			'transparentBg' => filter_var( $transparent_bg, FILTER_VALIDATE_BOOLEAN ),
			'hideDate'      => filter_var( $hide_date, FILTER_VALIDATE_BOOLEAN ),
			'hideStatus'    => filter_var( $hide_status, FILTER_VALIDATE_BOOLEAN ),
			'loginMessage'  => __( 'You must be logged in to view your posts.', 'gravityformsadvancedpostcreation' ),
		);

		$block = new GF_Block_APC_Posts();
		return $block->render_block( $attributes );
	}

	/**
	 * Retrieves a list of posts created by the logged-in user for the provided form.
	 *
	 * @since 1.6.0
	 *
	 * @param integer $form_id        The ID of te form to get the posts from.
	 * @param integer $posts_per_page The number of posts per page.
	 *
	 * @return array Posts data and pagination information.
	 */
	public function get_user_posts( $form_id = 0, $posts_per_page = 5 ) {

		$current_page = isset( $_GET[ 'apc_page_' . self::$instance_count ] ) ? absint( rgget( 'apc_page_' . self::$instance_count ) ) : 1;
		$offset       = ( $current_page - 1 ) * $posts_per_page;

		$total_posts_count = 0;
		$user              = $this->get_user();
		if ( ! $user ) {
			$this->addon->log_error( 'User is not logged in, abort.' );
			return array();
		}

		// We need avoid any entry that once had a post but doesn't any more so it is not counted in pagination.
		$deleted_entry_ids = $this->get_deleted_posts_entry_ids( $form_id );
		$field_filters     = array(
			array(
				'key'      => $this->addon->get_slug() . '_post_id',
				'operator' => 'ISNOT',
				'value'    => '',
			),
			array(
				'key'   => 'created_by',
				'value' => $user->ID,
			),
		);
		if ( ! empty( $deleted_entry_ids ) ) {
			$field_filters[] = array(
				'key'      => 'id',
				'operator' => 'not in',
				'value'    => $deleted_entry_ids,
			);
		}

		// Retrieve entries with pagination parameters.
		$entries = GFAPI::get_entries(
			$form_id,
			array(
				'field_filters' => $field_filters,
			),
			null,
			array(
				'offset'    => $offset,
				'page_size' => $posts_per_page,
			),
			$total_posts_count
		);

		$posts = array();

		foreach ( $entries as $entry ) {
			// Get post IDs from entry meta
			$created_posts = gform_get_meta( $entry['id'], $this->addon->get_slug() . '_post_id' );
			if ( ! $created_posts ) {
				continue;
			}

			foreach ( $created_posts as $post ) {
				if ( rgar( $post, 'post_id' ) ) {
					// Make sure the post author is the same as the user who created the entry.
					$author = get_post_field( 'post_author', $post['post_id'] );
					if ( $author == $user->ID ) {
						$posts[] = array(
							'id'       => $post['post_id'],
							'entry_id' => $entry['id'],
						);
					}
				}

			}
		}

		// Return an array with posts and pagination data
		return array(
			'posts'          => $posts,
			'total_count'    => $total_posts_count,
			'current_page'   => $current_page,
			'posts_per_page' => $posts_per_page,
		);
	}

	/**
	 * Retrieves a list of entry ids for entries that are associated with deleted posts.
	 *
	 * @since 1.6.0
	 *
	 * @param integer $form_id The ID of te form to get the posts from.
	 *
	 * @return array
	 */
	protected function get_deleted_posts_entry_ids( $form_id ) {
		$cache_key               = $this->addon->get_slug() . '_deleted_posts_entry_ids';
		$deleted_posts_entry_ids = \GFCache::get( $cache_key );
		if ( ! $deleted_posts_entry_ids ) {
			$deleted_posts_entry_ids = array();
			$entries                 = GFAPI::get_entries(
				array( $form_id ),
				array(
					'field_filters' => array(
						array(
							'key'      => $this->addon->get_slug() . '_post_id',
							'operator' => 'ISNOT',
							'value'    => '',
						),
						array(
							'key'   => 'created_by',
							'value' => $this->get_user()->ID,
						),
					),
				)
			);

			foreach ( $entries as $entry ) {
				// Get post IDs from entry meta
				$created_posts = gform_get_meta( $entry['id'], $this->addon->get_slug() . '_post_id' );
				if ( ! $created_posts ) {
					continue;
				}

				foreach ( $created_posts as $post ) {
					$post_obj = get_post( rgar( $post, 'post_id' ) );
					if ( ! $post_obj ) {
						$deleted_posts_entry_ids[] = rgar( $entry, 'id' );
					}
				}
			}

			\GFCache::set( $cache_key, $deleted_posts_entry_ids );
		}

		return $deleted_posts_entry_ids;
	}

	/**
	 * Deletes the cache that stores the deleted posts entry ids, triggered when a post is deleted.
	 *
	 * @since 1.6.0
	 */
	public function reset_deleted_posts_entry_ids_cache() {
		\GFCache::delete( $this->addon->get_slug() . '_deleted_posts_entry_ids' );
	}


	/**
	 * Outputs JS code to redirect the user if not logged in.
	 *
	 * @since 1.6.0
	 *
	 * @param string $redirect_url where to redirect the user to login.
	 *
	 * @return string
	 */
	protected function get_redirect_script( $redirect_url = '' ) {

		$current_page_url = \RGFormsModel::get_current_page_url();

		if ( ! $redirect_url ) {
			$redirect_url = wp_login_url( $current_page_url );
		} else {
			$redirect_url = add_query_arg(
				array(
					'redirect_to' => urlencode( $current_page_url ),
				),
				$redirect_url
			);
		}

		return '
		<script type="text/javascript">
			window.location.href = "' . $redirect_url . '";
		</script>

		';
	}


	/**
	 * Returns the current logged in user.
	 *
	 * @since  1.6.0
	 *
	 * @return false|WP_User
	 */
	protected function get_user() {

		if ( $this->user ) {
			return $this->user;
		}

		if ( is_user_logged_in() ) {

			$this->user = wp_get_current_user();

			return $this->user;
		}

		return false;
	}
}
