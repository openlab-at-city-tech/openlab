<?php

defined( 'ABSPATH' ) or die();

class Admin_Commenters_Comments_Count_Test extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
		c2c_AdminCommentersCommentsCount::reset_cache();

		wp_deregister_style( 'c2c_AdminCommentersCommentsCount_admin' );
		wp_dequeue_style( 'c2c_AdminCommentersCommentsCount_admin' );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	private function create_comments( $post_id = null, $count = 1, $name = 'alpha', $comment_info = array() ) {
		$default_comment_info = array(
			'comment_approved'     => '1',
			'comment_author'       => ucfirst( $name ) . ' User',
			'comment_author_email' => $name . '@example.org',
			'comment_author_url'   => 'http://example.org/' . $name . '/',
		);
		$comment_info = wp_parse_args( $comment_info, $default_comment_info );

		if ( ! $post_id ) {
			$post_id = $this->factory->post->create();
		}

		if ( 1 == $count ) {
			$comments = $this->factory->comment->create( array_merge( array( 'comment_post_ID' => $post_id ), $comment_info ) );
		} else {
			$comments = $this->factory->comment->create_post_comments( $post_id, $count, $comment_info );
		}

		return $comments;
	}

	private function expected_output( $approved_count = 0, $pending_count = 0, $name = '', $email = '', $is_dashboard = false, $no_comments_bubble = true ) {
		$title = sprintf( _n( '%d comment', '%d comments', $approved_count ), $approved_count );
		$pending_class = $pending_count ? '' : ' author-com-count-no-pending';

		if ( ! $no_comments_bubble && ! $approved_count && ! $pending_count ) {
			return '<span aria-hidden="true">—</span><span class="screen-reader-text">No comments</span>';
		}

		$ret = $is_dashboard ? '' : '</strong>';

		$url = ( ! $approved_count && ! $pending_count )
			? '#'
			: add_query_arg( 's', urlencode( $email), 'http://example.org/wp-admin/edit-comments.php' );

		$ret .= '<span class="column-response"><span class="post-com-count-wrapper post-and-author-com-count-wrapper author-com-count' . $pending_class . '">' . "\n";

		$comments_number = number_format_i18n( $approved_count );

		if ( $approved_count )  {
			$ret .= sprintf(
				'<a href="%s" title="%s" class="post-com-count post-com-count-approved">
					<span class="comment-count-approved" aria-hidden="true">%s</span>
					<span class="screen-reader-text">%s comments</span>
				</a>',
				esc_url( add_query_arg( 'comment_status', 'approved', $url ) ),
				esc_attr( $title ),
				$comments_number,
				$approved_count
			);
		} else {
			$ret .= sprintf(
				'<span class="post-com-count post-com-count-no-comments" title="%s"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				esc_attr( $title ),
				$comments_number,
				$pending_count ? __( 'No approved comments', 'admin-commenters-comments-count' ) : __( 'No comments', 'admin-commenters-comments-count' )
			);
		}

		$pending_phrase = sprintf( _n( '%s pending comment', '%s pending comments', $pending_count ), number_format_i18n( $pending_count ) );
		if ( $pending_count ) {
			$ret .= sprintf(
'<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( 'http://example.org/wp-admin/edit-comments.php?s=' . urlencode( $email ) . '&comment_status=moderated' ),
				$pending_count,
				$pending_phrase
			);
		} else {
			$ret .= sprintf(
				'<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$pending_count,
				$approved_count ? __( 'No pending comments' ) : __( 'No comments' )
			);
		}
		$ret .= "</span></span>";

		$ret .= $is_dashboard ? '' : '<strong>';

		$ret .= $name;

		return $ret;
	}

	private function get_comment_author_output( $comment_id ) {
		ob_start();
		comment_author( $comment_id );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_hooks() {
		return array(
			array( 'filter', 'comment_author',                      'comment_author',          10 ),
			array( 'filter', 'get_comment_author_link',             'get_comment_author_link', 10 ),
			array( 'action', 'admin_enqueue_scripts',               'enqueue_admin_css',       10 ),
			array( 'filter', 'manage_users_columns',                'add_user_column',         10 ),
			array( 'filter', 'manage_users_custom_column',          'handle_column_data',      10 ),
			array( 'filter', 'akismet_show_user_comments_approved', '__return_false',          10, false ),
		);
	}


	//
	//
	// TESTS
	//
	//


	public function test_plugin_version() {
		$this->assertEquals( '1.9.4', c2c_AdminCommentersCommentsCount::version() );
	}

	public function test_class_is_available() {
		$this->assertTrue( class_exists( 'c2c_AdminCommentersCommentsCount' ) );
	}

	public function test_plugins_loaded_action_triggers_do_init() {
		$this->assertNotFalse( has_filter( 'plugins_loaded', array( 'c2c_AdminCommentersCommentsCount', 'init' ) ) );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority, $class_method = true ) {
		$callback = $class_method ? array( 'c2c_AdminCommentersCommentsCount', $function ) : $function;

		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );

		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
	}

	public function test_get_comment_author_link_unaffected_on_frontend() {
		$comments = $this->create_comments( null, 3 );
		$GLOBALS['comment'] = get_comment( $comments[0] );

		$this->assertEquals( "<a href='http://example.org/alpha/' rel='external nofollow ugc' class='url'>Alpha User</a>", get_comment_author_link( $comments[0] ) );
		$this->assertEquals( 'originallink', apply_filters( 'get_comment_author_link', 'originallink' ) );
	}

	public function test_comment_author_link_unaffected_on_frontend() {
		$comments = $this->create_comments( null, 3 );
		$GLOBALS['comment'] = get_comment( $comments[0] );

		$this->assertEquals( 'Alpha User', $this->get_comment_author_output( $comments[0] ) );
		$this->assertEquals( 'originallink', apply_filters( 'get_comment_author_link', 'originallink' ) );
	}

	/*
	 * TESTS AFTER THIS SHOULD ASSUME THEY ARE IN THE ADMIN AREA
	 */

	// This should be the first of the admin area tests and is
	// necessary to set the environment to be the admin area.
	public function test_in_admin_area() {
		define( 'WP_ADMIN', true );

		$this->assertTrue( is_admin() );
	}

	/*
	 * get_comment_author_link()
	 */

	public function test_get_comment_author_link_affected_on_backend() {
		$post_id = $this->factory->post->create();

		$this->create_comments( $post_id, 5, 'alpha' );
		$bravo_comments = $this->create_comments( $post_id, 2, 'bravo' );
		$comment_id = $this->create_comments( $post_id, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$GLOBALS['comment'] = get_comment( $comment_id );

		$expected_output = $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' );
		$this->assertEquals( $expected_output, get_comment_author_link( $comment_id ) );
		$this->assertEquals( $expected_output, c2c_AdminCommentersCommentsCount::get_comment_author_link( $comment_id ) );

		$GLOBALS['comment'] = get_comment( $bravo_comments[0] );

		$expected_output = $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' );
		$this->assertEquals( $expected_output, get_comment_author_link( $comment_id ) );
		$this->assertEquals( $expected_output, c2c_AdminCommentersCommentsCount::get_comment_author_link( $bravo_comments[0] ) );
	}

	/*
	 * comment_author()
	 */

	public function test_comment_author_link_affected_on_backend() {
		$post_id = $this->factory->post->create();

		$this->create_comments( $post_id, 5, 'alpha' );
		$bravo_comments = $this->create_comments( $post_id, 2, 'bravo' );
		$comment_id = $this->create_comments( $post_id, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$GLOBALS['comment'] = get_comment( $comment_id );

		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), $this->get_comment_author_output( $comment_id ) );
		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), c2c_AdminCommentersCommentsCount::comment_author( $comment_id ) );

		$GLOBALS['comment'] = get_comment( $bravo_comments[0] );

		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), $this->get_comment_author_output( $bravo_comments[0] ) );
		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), c2c_AdminCommentersCommentsCount::comment_author( $bravo_comments[0] ) );
	}

	/*
	 * get_comments_count()
	 */

	public function test_get_comments_count_by_comment_author_email() {
		$post_id = $this->factory->post->create();
		$this->create_comments( $post_id, 5, 'alpha' );
		$this->create_comments( $post_id, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$this->assertEquals( array( 5, 1 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author_email', 'alpha@example.org' ) );
	}

	public function test_get_comments_count_by_comment_author() {
		$this->create_comments( null, 5, 'alpha' );
		$this->create_comments( null, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$this->assertEquals( array( 5, 1 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author', 'Alpha User' ) );
	}

	public function test_get_comments_count_by_comment_author_email_and_user_id() {
		$user_id = $this->factory->user->create( array( 'user_email' => 'something@example.com' ) );

		$this->create_comments( null, 5, 'alpha' );
		$this->create_comments( null, 1, 'alpha', array( 'comment_approved' => '0' ) );
		$this->create_comments( null, 1, 'alpha', array( 'comment_author_email' => 'notalpha@example.com', 'user_id' => $user_id ) );

		$this->assertEquals( array( 5, 1 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author_email', 'alpha@example.org' ) );
		$this->assertEquals( array( 6, 1 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author_email', 'alpha@example.org', 'comment', $user_id ) );
	}

	public function test_get_comments_count_on_user_without_comments() {
		$this->assertEquals( array( 0, 0 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author_email', 'alpha@example.org' ) );
		$this->assertEquals( array( 0, 0 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author', 'alpha' ) );
	}

	public function test_get_comments_count_on_pingback() {
		$this->create_comments( null, 2, 'A pingbacking site', array(
			'comment_author'       => 'A pingbacking site',
			'comment_author_email' => '',
			'comment_author_url'   => 'http://example.com/post-that-pinged-back/',
			'comment_type'         => 'pingback',
		) );
		$this->create_comments( null, 1, 'A pingbacking site', array(
			'comment_author'       => 'A pingbacking site',
			'comment_author_email' => '',
			'comment_author_url'   => 'http://example.com/post-that-pinged-back/',
			'comment_type'         => 'pingback',
		) );
		$this->create_comments( null, 2, 'Another pingbacking site', array(
			'comment_author'       => 'Another pingbacking site',
			'comment_author_email' => '',
			'comment_author_url'   => 'http://test.example.com/post-that-pinged-back/',
			'comment_type'         => 'pingback',
		) );

		$this->assertEquals( array( 3, 0 ), c2c_AdminCommentersCommentsCount::get_comments_count( 'comment_author_url', 'http://example.com/post-that-pinged-back/', 'pingback' ) );
	}

	/*
	 * get_comments_url()
	 */

	public function test_get_comments_url() {
		$this->assertEquals(
			'http://example.org/wp-admin/edit-comments.php?s=' . urlencode( 'test@example.com' ),
			c2c_AdminCommentersCommentsCount::get_comments_url( 'test@example.com' )
		);
	}

	/*
	 * get_comments_bubble()
	 */

	public function test_get_comments_bubble() {
		$this->assertEquals(
			$this->expected_output( 10, 3, '', 'test@example.com', true ),
			c2c_AdminCommentersCommentsCount::get_comments_bubble( 'test@example.com', 10, 3, '10 comments' )
		);
	}

	public function test_get_comments_bubble_when_no_comments_and_true_no_comments_bubble() {
		$this->assertEquals(
			$this->expected_output( 0, 0, '', 'test@example.com', true, false ),
			c2c_AdminCommentersCommentsCount::get_comments_bubble( 'test@example.com', 0, 0, '', false )
		);
	}

	public function test_get_comments_bubble_when_no_comments_and_false_no_comments_bubble() {
		$this->assertEquals(
			$this->expected_output( 0, 0, '', 'test@example.com', true, true ),
			c2c_AdminCommentersCommentsCount::get_comments_bubble( 'test@example.com', 0, 0, '0 comments', true )
		);
	}

	/*
	 * enqueue_admin_css()
	 */

	 public function test_enqueue_admin_css() {
		$this->assertFalse( wp_style_is( 'c2c_AdminCommentersCommentsCount_admin', 'registered' ) );
		$this->assertFalse( wp_style_is( 'c2c_AdminCommentersCommentsCount_admin', 'enqueued' ) );

		c2c_AdminCommentersCommentsCount::enqueue_admin_css();

		$this->assertTrue( wp_style_is( 'c2c_AdminCommentersCommentsCount_admin', 'registered' ) );
		$this->assertTrue( wp_style_is( 'c2c_AdminCommentersCommentsCount_admin', 'enqueued' ) );
	}

	/*
	 * add_user_column()
	 */

	public function test_add_user_column() {
		$expected = array( 'a' => 'A', 'b' => 'B', 'commenters_count' => 'Comments' );
		$actual = c2c_AdminCommentersCommentsCount::add_user_column( array( 'a' => 'A', 'b' => 'B' ) );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( array_keys( $expected ), array_keys( $actual ) );
	}

	/*
	 * handle_column_data()
	 */

	public function test_handle_column_data_with_some_other_column() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$post_id = $this->factory->post->create( array( 'comment_status' => 'open' ) );

		$expected = 'something';

		$this->assertEquals( $expected, c2c_AdminCommentersCommentsCount::handle_column_data( $expected, 'address', $post_id ) );
	}

	public function test_handle_column_data() {
		$post_id = $this->factory->post->create();
		$user = self::factory()->user->create_and_get( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user->ID );

		$this->create_comments( $post_id, 5, 'alpha' );
		$this->create_comments( $post_id, 5, 'alpha', array( 'comment_approved' => '0' ) );
		$this->create_comments( $post_id, 5, 'alpha', array( 'comment_author_email' => 'notalpha@example.com', 'user_id' => $user->ID ) );

		$expected = $this->expected_output( 5, 0, '', $user->user_email, true );

		$this->assertEquals( $expected, c2c_AdminCommentersCommentsCount::handle_column_data( $expected, 'commenters_count', $user->ID ) );
	}

}
