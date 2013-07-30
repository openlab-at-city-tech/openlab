<?php

class BPINMC_Tests extends BP_UnitTestCase {
	protected $blog_id;
	protected $post_id;

	function setUp() {
		parent::setUp();
		$this->blog_id = $this->factory->blog->create();
		switch_to_blog( $this->blog_id );
		update_option( 'blog_public', '1' );

		$this->post_id = $this->factory->post->create();

		remove_action( 'check_comment_flood', 'check_comment_flood_db', 10, 3 );
	}

	function tearDown() {
		restore_current_blog();
		add_action( 'check_comment_flood', 'check_comment_flood_db', 10, 3 );
	}

	static public function return_1() {
		return 1;
	}

	static public function return_spam() {
		return 'spam';
	}

	public function activity_exists_for_comment( $comment_id ) {
		$ca = bp_activity_get( array(
			'max' => 1,
			'show_hidden' => true,
			'spam' => 'all',
			'filter' => array(
				'user_id' => 0,
				'object' => 'blogs',
				'action' => 'new_blog_comment',
				'primary_id' => $this->blog_id,
				'secondary_id' => $comment_id,
			),
		) );

		return ! empty( $ca['activities'] );
	}

	/**
	 * Can't use WP library because we need to call wp_new_comment()
	 */
	public function create_comment() {
		$comment_id = wp_new_comment( array(
			'comment_author' => 'Commenter Foo',
			'comment_author_url' => 'http://example.com/foo/',
			'comment_content' => 'foo',
			'comment_post_ID' => $this->post_id,
			'comment_approved' => 1,
		) );
		return $comment_id;
	}

	function test_autoapproved_comment() {
		add_filter( 'pre_comment_approved', array( $this, 'return_1' ) );
		$comment_id = $this->create_comment();
		remove_filter( 'pre_comment_approved', array( $this, 'return_1' ) );

		$this->assertTrue( $this->activity_exists_for_comment( $comment_id ) );
	}

	function test_unapproved_comments() {
		$comment_id = $this->create_comment();

		$this->assertFalse( $this->activity_exists_for_comment( $comment_id ) );
	}

	function test_manually_approved_comments() {
		$comment_id = $this->create_comment();

		wp_set_comment_status( $comment_id, 'approve' );

		$this->assertTrue( $this->activity_exists_for_comment( $comment_id ) );
	}

	function test_manually_spammed_comments() {
		$comment_id = $this->create_comment();

		wp_set_comment_status( $comment_id, 'spam' );

		$this->assertFalse( $this->activity_exists_for_comment( $comment_id ) );
	}

	function test_auto_spammed_comments() {
		add_filter( 'pre_comment_approved', array( $this, 'return_spam' ) );
		$comment_id = $this->create_comment();
		remove_filter( 'pre_comment_approved', array( $this, 'return_spam' ) );

		$this->assertFalse( $this->activity_exists_for_comment( $comment_id ) );

		wp_set_comment_status( $comment_id, 'approve' );

		$this->assertTrue( $this->activity_exists_for_comment( $comment_id ) );
	}
}

