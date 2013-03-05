<?php

class OpenLab_Test_Template_Picker extends BP_UnitTestCase {

	function testSample() {
		// replace this with some actual testing code
		$this->assertEquals( did_action( 'bp_init' ), 1 );
		$this->assertTrue( function_exists( 'openlab_get_user_portfolio_id' ) );
	}
}

