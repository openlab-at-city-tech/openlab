<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle operations on article count
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Article_Count_Handler {

	private $kb_id;

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $this->kb_id ) ) {
			return;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $this->kb_id, true );
		if ( is_wp_error( $kb_config ) ) {
			return;
		}

		// Check if article count is enabled
		if ( $kb_config['article_views_counter_enable'] != 'on' ) {
			return;
		}

		add_action( 'add_meta_boxes', [ $this, 'show_article_counter_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_article_metabox' ], 10, 2 );
		add_action( 'admin_init', array( $this, 'add_ID_to_columns_list' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_by_views' ) );
	}

	/**
	 * Increase article views counter when user views article if enabled but prevent multiple counting.
     * Does not apply to PHP mode.
     *
	 * @param $article_id
	 * @return bool
	 */
	public static function maybe_increase_article_count( $article_id ) {

		$post = EPKB_Core_Utilities::get_kb_post_secure( $article_id );
		if ( empty( $post ) ) {
			return false;
		}

		// check user already viewed this article (delay, scroll and php methods)
		if ( self::is_article_recently_viewed( $article_id ) ) {
			return false;
		}

		// save total like meta field and check if it was removed
		$views = (int)EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views', 0 );
		$views++;

		EPKB_Utilities::save_postmeta( $article_id, 'epkb-article-views', $views, true );

		// increase counter for year meta
		$year = date( 'Y' );
		$week_number = date( 'W' );

		$year_meta = EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views-' . $year, [] );
		if ( ! is_array( $year_meta ) ) {
			$year_meta = [];
		}

		if ( empty( $year_meta[ $week_number ] ) || ! is_numeric( $year_meta[ $week_number ] ) ) {
			$year_meta[ $week_number ] = 0;
		}

		$year_meta[ $week_number ]++;

		EPKB_Utilities::save_postmeta( $article_id, 'epkb-article-views-' . $year, $year_meta, true );

		self::update_article_view_time_cookie( $article_id );

		return true;
	}

	function show_article_counter_meta_box() {
		global $post, $pagenow;

		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		if ( $pagenow != 'post-new.php' && $pagenow != 'post.php' ) {
			return;
		}

		// ignore non-KB posts
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return;
		}

		add_meta_box( 'epkb_article_counter_meta_box', esc_html__( 'KB Article Views', 'echo-knowledge-base' ), array( $this, 'display_article_counter_meta_box' ), EPKB_KB_Handler::get_post_type( $kb_id ), 'side', 'high' );
	}

	/**
	 * Display HTML for views meta box
	 */
	public function display_article_counter_meta_box() {
		global $post; ?>
		<input id="epkb-article-views" name="epkb-article-views" type="number"
			   value="<?php echo esc_attr( EPKB_Utilities::get_postmeta( $post->ID, 'epkb-article-views', 0 ) ); ?>"
			   min="0"><?php
	}

	/**
	 * When page is added/updated, check if it contains KB articl views parameter
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 */
	function save_article_metabox( $post_id, $post ) {

		// ignore autosave/revision which is not article submission; same with ajax and bulk edit
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) || empty( $post->post_status ) ) {
			return;
		}

		// get new 'epkb-article-views' value
		$views = (int)EPKB_Utilities::post( 'epkb-article-views', 0 );
		$views = max( $views, 0 );

		// get old 'epkb-article-views' value
		$old_views = (int)EPKB_Utilities::get_postmeta( $post_id, 'epkb-article-views', 0 );

		if ( $views == $old_views ) {
			return;
		}

		// update this year counters
		$year = date( 'Y' );
		$week_number = date( 'W' );
		EPKB_Utilities::save_postmeta( $post_id, 'epkb-article-views-' . $year, [ $week_number => $views ], true );

		// save new 'epkb-article-views' value
		EPKB_Utilities::save_postmeta( $post_id, 'epkb-article-views', $views, true );
	}

	/**
	 * Check if article was viewed within the last 6 hours by looking up article last viewed time using article ID in cookie
	 *
	 * @return boolean
	 */
	public static function is_article_recently_viewed( $article_id ) {

		if ( headers_sent() ) {
			return false;
		}

		// check is cookie set to track articles and their last view time
		$article_views = EPKB_Utilities::post( 'epkb_article_views_counter', [], 'db-config-json' );
		if ( ! is_array( $article_views ) ) {
			setcookie( 'epkb_article_views_counter', wp_json_encode( [] ), time() + MONTH_IN_SECONDS, '/' );
			return false;
		}

		// get cookie value [ post_id => time() ]
		if ( empty( $article_views[ $article_id ] ) ) {
			return false;
		}

		if ( ! is_numeric( $article_views[ $article_id ] ) ) {
			return false;
		}

		// is article last view time within the last 6 hours?
		return (int)$article_views[ $article_id ] > ( time() - 6 * HOUR_IN_SECONDS );
	}

	/**
	 * Update cookie with article ID
	 * @param int $article_id
	 * @return void
	 */

	public static function update_article_view_time_cookie( $article_id ) {
		$article_views = [];

		if ( headers_sent() ) {
			return;
		}

		$article_views = EPKB_Utilities::post( 'epkb_article_views_counter', [], 'db-config-json' );
		if ( ! is_array( $article_views ) ) {
			$article_views = [];
		}

		// clean old and wrong values
		foreach( $article_views as $key => $value ) {
			// sanitize
			if ( ! is_numeric( $key ) || ! is_numeric( $value ) ) {
				unset( $article_views[ $key ] );
				continue;
			}

            // remove articles viewed more than 6 hours ago
			if ( (int)$value < ( time() - 6 * HOUR_IN_SECONDS ) ) {
				unset( $article_views[ $key ] );
				continue;
			}
		}

		$article_views[ $article_id ] = time();

		setcookie( 'epkb_article_views_counter', wp_json_encode( $article_views ), time() + MONTH_IN_SECONDS, '/' );
	}

	/**
	 * Add Views to All articles pages
	 */
	public function add_ID_to_columns_list() {

		$kb_post_type = EPKB_KB_Handler::get_post_type( $this->kb_id );

		add_action( "manage_" . $kb_post_type . "_posts_columns", array( $this, 'add_column_heading' ), 99, 1 );
		add_filter( "manage_" . $kb_post_type . "_posts_custom_column", array( $this, 'add_column_value' ), 99, 2 );
		add_filter( "manage_edit-" . $kb_post_type . "_sortable_columns", array( $this, 'add_sortable_columns' ), 99 );
	}

	public function add_column_heading( $columns ) {

		$columns = empty( $columns ) ? array() : $columns;
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return $columns;
		}

		$columns['epkb_article_views'] = esc_html__( 'Views', 'echo-knowledge-base' );

		return $columns;
	}

	public function add_column_value( $column_name, $post_id ) {

		if ( $column_name != 'epkb_article_views' ) {
			return;
		}

		$view_count = EPKB_Utilities::get_postmeta( $post_id, 'epkb-article-views', 0 );

		echo esc_html( $view_count );
	}

	public function add_sortable_columns( $sortable_columns ) {
		$sortable_columns['epkb_article_views'] = 'epkb_article_views';
		return $sortable_columns;
	}

	/**
	 * User sorts by views column values.
	 * @param WP_Query $query
	 */
	public function sort_by_views( $query ) {

		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$kb_post_type = EPKB_KB_Handler::get_post_type( $this->kb_id );
		if ( $query->query['post_type'] != $kb_post_type ) {
			return;
		}

		if ( empty( $query->get( 'orderby' ) ) || $query->get( 'orderby' ) != 'epkb_article_views' ) {
			return;
		}

		$query->set( 'meta_query', array(
			'relation' => 'OR',
			array(
				'key'       => 'epkb-article-views',
				'type'      => 'numeric',
				'compare'   => 'NOT EXISTS',
			),
			array(
				'key'       => 'epkb-article-views',
				'type'      => 'numeric',
				'compare'   => 'EXISTS',
			),
		) );

		$query->set( 'orderby', 'meta_value_num' );
	}

	/**
	 * Get article views counter value to display on the frontend
	 * @return int
	 */
	public static function get_article_views_counter_frontend() {
		global $post;

		if ( empty( $post ) or ! isset( $post->ID ) ) {
			return 1;
		}

        return EPKB_Utilities::get_postmeta( $post->ID, 'epkb-article-views', 0 );
	}
}
