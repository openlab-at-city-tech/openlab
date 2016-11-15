<?php

/**
 * @group activity
 * @group groups
 */
class BPEO_Tests_Group_Bpeo_Activity_Action_Format_For_Groups extends BPEO_UnitTestCase {
	public function test_item_unconnected_to_group_should_not_have_group_content() {
		$u = $this->factory->user->create();
		$g = $this->factory->group->create();

		$this->add_user_to_group( $u, $g );

		$now = time();
		$e = eo_insert_event( array(
			'post_author' => $u,
			'start' => new DateTime( date( 'Y-m-d H:i:s', $now - 60*60 ) ),
			'end' => new DateTime( date( 'Y-m-d H:i:s' ) ),
			'post_status' => 'publish'
		) );

		$a = bpeo_get_activity_by_event_id( $e );

		$this->assertNotContains( 'in the group', $a[0]->action );
	}
}
