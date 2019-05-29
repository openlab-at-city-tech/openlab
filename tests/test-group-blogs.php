<?php

class OpenLab_Test_Group_Blogs extends BP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->old_current_user = get_current_user_id();
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );

	}

	public function tearDown() {
		parent::tearDown();
		wp_set_current_user( $this->old_current_user );
	}

	function return_1() {
		return 1;
	}

	function test_anonymous_comment() {
		$this->assertTrue( function_exists( 'bp_blogs_record_post' ) );
		$group = $this->factory->group->create();
		$blog_id = $this->factory->blog->create();
		openlab_set_group_site_id( $group_id, $blog_id );

		switch_to_blog( $blog_id );
		update_option( 'blog_public', '1' );

		$post_id = $this->factory->post->create();

		add_filter( 'pre_comment_approved', array( $this, 'return_1' ) );
		$comment_id = wp_new_comment( array(
			'comment_author' => 'Commenter Foo',
			'comment_author_url' => 'http://example.com/foo',
			'comment_content' => 'foo',
			'comment_post_ID' => $post_id,
			'comment_approved' => 1,
		) );
		remove_filter( 'pre_comment_approved', array( $this, 'return_1' ) );

		$ca = bp_activity_get( array(
			'max' => 1,
			'show_hidden' => 1,
			'spam' => 'all',
			'filter' => array(
				'user_id' => 0,
				'object' => 'groups',
				'action' => 'new_blog_comment',
				'primary_id' => $group_id,
				'secondary_id' => $comment_id,
			),
		) );

		$this->assertTrue( ! empty( $ca['activities'] ) );
		restore_current_blog();
	}

}

