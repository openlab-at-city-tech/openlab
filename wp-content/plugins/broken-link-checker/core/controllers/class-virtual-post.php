<?php
/**
 * Virtual Post Parent Class.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Controllers
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Controllers;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use stdClass;
use WP_Post;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Mailer
 *
 * @package WPMUDEV_BLC\Core\Controllers
 */
abstract class Virtual_Post extends Base {
	/**
	 * The default query args. Can be overridden in child class and 3rd party classes.
	 *
	 * @var array
	 */
	public $query_args = array(
		'is_page'     => true,
		'is_singular' => true,
		'is_home'     => false,
		'is_archive'  => false,
		'is_category' => false,
		'reset_error' => true,
		'error'       => '',
		'is_404'      => false,
	);
	/**
	 * The final post args. Can be overridden in child and 3rd party class.
	 *
	 * @var array
	 */
	public $post_args = array();
	/**
	 * The virtual post title.
	 *
	 * @var string $post_title The post title.
	 */
	protected $post_title = '';
	/**
	 * A boolean property that defines if virtual post should load or not.
	 *
	 * @var bool $load_virtual_post
	 */
	protected $load_virtual_post = false;
	/**
	 * The default post args. Set in set_default_post_args. Can not be overridden in child class.
	 *
	 * @var array
	 */
	private $default_post_args = array();

	/**
	 * Init Webhook
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		/*
		 * Prepare post vars.
		 */
		$this->prepare_vars();

		/*
		 * Default post values need to be set after child class properties are set as default post values may depend
		 * on those props.
		 */
		$this->set_default_post_args();

		/**
		 * The virtual post will be loaded from the_posts filter.
		 */
		add_filter( 'the_posts', array( $this, 'virtual_post' ), 10, 2 );
	}

	/**
	 * Prepares the class properties. Required in each child class.
	 *
	 * @return void
	 */
	abstract public function prepare_vars();

	/**
	 * Sets default virtual post's args.
	 *
	 * @return void
	 */
	private function set_default_post_args() {
		//$this->default_post_args['ID']             = - 99;
		$this->default_post_args['ID']             = 0;
		$this->default_post_args['post_type']      = 'post';
		$this->default_post_args['post_author']    = 1;
		$this->default_post_args['post_name']      = sanitize_title( $this->post_title );
		$this->default_post_args['guid']           = get_bloginfo( site_url() . '/' . $this->default_post_args['post_name'] );
		$this->default_post_args['post_title']     = sanitize_text_field( $this->post_title );
		$this->default_post_args['post_status']    = 'static';
		$this->default_post_args['comment_status'] = 'closed';
		$this->default_post_args['ping_status']    = 'closed';
		$this->default_post_args['comment_count']  = 0;
		$this->default_post_args['post_date']      = current_time( 'mysql' );
		$this->default_post_args['post_date_gmt']  = current_time( 'mysql', 1 );
		$this->default_post_args['filter']         = 'raw';
	}

	/**
	 * Returns the posts list which contains only the virtual post.
	 *
	 * @param array $posts .
	 *
	 * @return array Array of \WP_Post
	 */
	public function virtual_post( $posts, $query ) {
		if ( ! $query->is_main_query() || ( ! empty( $posts[0]->post_type ) && 'wp_global_styles' === $posts[0]->post_type ) || ! $this->can_load_virtual_post() ) {
			return $posts;
		}

		global $wp_query;
		$wp_query->is_page = true;
		$wp_query->is_singular = true;

		$this->set_query_vars();

		return array( $this->get_virtual_post() );
	}

	/**
	 * Defines if current virtual post should load or not. This is important to be handled in each child class
	 * separately.
	 *
	 * @return bool
	 */
	abstract public function can_load_virtual_post();

	/**
	 * Sets wp_query vars.
	 *
	 * @return void
	 */
	protected function set_query_vars() {
		global $wp_query;

		$query_args = apply_filters(
			'wpmudev_blc_virtual_post_query_args',
			$this->query_args,
			$this
		);

		$wp_query->is_page     = ! isset( $query_args['is_page'] ) || (bool) $query_args['is_page'];
		$wp_query->is_singular = ! isset( $query_args['is_singular'] ) || (bool) $query_args['is_singular'];
		$wp_query->is_home     = $query_args['is_home'] ?? false;
		$wp_query->is_archive  = $query_args['is_archive'] ?? false;
		$wp_query->is_category = $query_args['is_category'] ?? false;

		if ( isset( $query_args['reset_error'] ) && $query_args['reset_error'] ) {
			unset( $wp_query->query['error'] );
			$wp_query->query_vars['error'] = $query_args['error'] ?? '';
		}

		$wp_query->is_404 = isset( $query_args['is_404'] ) && (bool) $query_args['is_404'];
	}

	/**
	 * Returns the virtual post object.
	 *
	 * @return WP_Post
	 */
	protected function get_virtual_post() {
		$post            = new stdClass();
		$this->post_args = apply_filters(
			'wpmudev_blc_virtual_post_args',
			wp_parse_args( $this->post_args, $this->default_post_args ),
			$this::instance()
		);

		foreach ( $this->post_args as $post_arg_key => $post_arg_value ) {
			$post->$post_arg_key = $post_arg_value;
		}

		/*
		 * Instead of using $post->content we prefer to hook into the the_content filter, because block themes (https://developer.wordpress.org/block-editor/how-to-guides/themes/block-theme-overview/) such
		 * as Twenty Twenty Two will repeat the post content in multiple page parts.
		 */
		/*
		 *
		 */
		/*
		add_filter(
			'the_content',
			function () {
				return wp_kses_post( $this->post_content() );
			}
		);
		*/

		/*
		 * Update. We are now checking condition `$query->is_main_query()` in `virtual_post`.
		 * So we don't have page blocks repeating the post content.
		 */
		$post->post_content = wp_kses_post( $this->post_content() );

		return new WP_Post( $post );
	}

	/**
	 * Returns a string with the content of virtual post.
	 *
	 * @param string $the_content
	 *
	 * @return string
	 */
	abstract protected function post_content( string $the_content = '' );
}
