<?php

class OpenLab_Test_Template_Picker extends BP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->old_current_user = get_current_user_id();
		wp_set_current_user( $this->factory->user->create( array( 'role' => 'subscriber' ) ) );
	}

	public function tearDown() {
		parent::tearDown();
		wp_set_current_user( $this->old_current_user );
	}

	function test_get_template_from_group_type() {
		$tp = new OpenLab_GroupBlog_Template_Picker();

		$tp->set_group_type( 'foo' );
		$this->assertEquals( $tp->get_template_from_group_type(), 'template-group' );

		$tp->set_group_type( 'course' );
		$this->assertEquals( $tp->get_template_from_group_type(), 'template-course' );

		$tp->set_group_type( 'club' );
		$this->assertEquals( $tp->get_template_from_group_type(), 'template-club' );

		$tp->set_group_type( 'project' );
		$this->assertEquals( $tp->get_template_from_group_type(), 'template-project' );
	}

	function test_get_user_account_type() {
		$user_id = $this->factory->user->create();

		$xprofile_group = $this->factory->xprofile_group->create();
		$account_type_field = $this->factory->xprofile_field->create( array(
			'field_group_id' => $xprofile_group->id,
			'name' => 'Account Type',
			'type' => 'textbox',
		) );

		xprofile_set_field_data( $account_type_field->id, $user_id, 'Student' );

		$tp = new OpenLab_GroupBlog_Template_Picker( $user_id );
		$this->assertEquals( $tp->get_user_type(), 'student' );
	}

	/**
	 * Students are handled with their own method
	 */
	function test_get_portfolio_template_for_user() {
		$user_id = $this->factory->user->create();
		$tp = new OpenLab_GroupBlog_Template_Picker( $user_id );
		$tp->set_user_type( 'bbb' );
		$this->assertEquals( $tp->get_portfolio_template_for_user(), '' );

		$user_id2 = $this->factory->user->create();
		$tp2 = new OpenLab_GroupBlog_Template_Picker( $user_id2 );
		$tp2->set_user_type( 'faculty' );
		$this->assertEquals( $tp2->get_portfolio_template_for_user(), 'template-portfolio' );

		$user_id3 = $this->factory->user->create();
		$tp3 = new OpenLab_GroupBlog_Template_Picker( $user_id3 );
		$tp3->set_user_type( 'staff' );
		$this->assertEquals( $tp3->get_portfolio_template_for_user(), 'template-portfolio-staff' );
	}

	function test_get_student_department() {
		$xprofile_group = $this->factory->xprofile_group->create();
		$dept_field = $this->factory->xprofile_field->create( array(
			'field_group_id' => $xprofile_group->id,
			'name' => 'Major Program of Study',
			'type' => 'textbox',
		) );

		$user_id = $this->factory->user->create();
		$tp = new OpenLab_GroupBlog_Template_Picker( $user_id );
		$tp->set_user_type( 'student' );
		xprofile_set_field_data( $dept_field->id, $user_id, 'Foo' );

		$this->assertEquals( $tp->get_student_department(), 'Foo' );
	}

	function test_get_portfolio_template_for_student() {
		$user_id = $this->factory->user->create();
		$tp = new OpenLab_GroupBlog_Template_Picker( $user_id );

		$tp->set_student_department( 'Foo' );
		$tp->department_templates['Foo'] = 'Bar';
		$this->assertEquals( $tp->get_portfolio_template_for_student(), 'Bar' );

		$tp->set_student_department( 'Foo2' );
		$this->assertEquals( $tp->get_portfolio_template_for_student(), 'template-eportfolio' );
	}

	/**
	 * Testing the whole chain
	 */
	function test_openlab_get_groupblog_template() {
		// Set up template blogs
		$this->factory->blog->create( array( 'path' => 'template-portfolio' ) );
		$this->factory->blog->create( array( 'path' => 'template-eportfolio' ) );
		$this->factory->blog->create( array( 'path' => 'template-portfolio-staff' ) );
		$this->factory->blog->create( array( 'path' => 'template-eportfolio-nursing' ) );

		// Set up xprofile fields
		$xprofile_group = $this->factory->xprofile_group->create();
		$account_type_field = $this->factory->xprofile_field->create( array(
			'field_group_id' => $xprofile_group->id,
			'name' => 'Account Type',
			'type' => 'textbox',
		) );

		$xprofile_group = $this->factory->xprofile_group->create();
		$dept_field = $this->factory->xprofile_field->create( array(
			'field_group_id' => $xprofile_group->id,
			'name' => 'Major Program of Study',
			'type' => 'textbox',
		) );

		// student without department
		$user_id = $this->factory->user->create();
		xprofile_set_field_data( $account_type_field->id, $user_id, 'Student' );
		$this->assertEquals( openlab_get_groupblog_template( $user_id ), 'template-eportfolio' );

		// student without department portfolio
		$user_id2 = $this->factory->user->create();
		xprofile_set_field_data( $account_type_field->id, $user_id2, 'Student' );
		xprofile_set_field_data( $dept_field->id, $user_id2, 'Foo' );
		$this->assertEquals( openlab_get_groupblog_template( $user_id2 ), 'template-eportfolio' );

		// student with department
		$user_id3 = $this->factory->user->create();
		xprofile_set_field_data( $account_type_field->id, $user_id3, 'Student' );
		xprofile_set_field_data( $dept_field->id, $user_id3, 'Nursing' );
		add_filter( 'openlab_department_templates', array( $this, 'department_templates' ) );
		$this->assertEquals( openlab_get_groupblog_template( $user_id3 ), 'template-eportfolio-nursing' );
		remove_filter( 'openlab_department_templates', array( $this, 'department_templates' ) );

		// staff
		$user_id4 = $this->factory->user->create();
		xprofile_set_field_data( $account_type_field->id, $user_id4, 'Staff' );
		$this->assertEquals( openlab_get_groupblog_template( $user_id4 ), 'template-portfolio-staff' );

		// faculty
		$user_id5 = $this->factory->user->create();
		xprofile_set_field_data( $account_type_field->id, $user_id5, 'Staff' );
		$this->assertEquals( openlab_get_groupblog_template( $user_id5 ), 'template-portfolio-staff' );
	}

	function department_templates() {
		return array( 'Nursing' => 'template-eportfolio-nursing' );
	}
}

