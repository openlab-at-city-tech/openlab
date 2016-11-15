<?php

class BPEO_UnitTestCase extends BP_UnitTestCase {
	public $event_factory;

	public function setUp() {
		parent::setUp();
		$this->event_factory = new EO_UnitTest_Factory();
	}
}
