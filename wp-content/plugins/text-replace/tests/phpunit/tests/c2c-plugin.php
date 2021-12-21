<?php

defined( 'ABSPATH' ) or die();

class c2c_Plugin extends WP_UnitTestCase {

	protected $obj;

	public function setUp() {
		parent::setUp();

		add_filter( 'gettext_text-replace', array( $this, 'translate_text' ), 10, 2 );
		$this->obj = c2c_TextReplace::get_instance();
	}

	public function tearDown() {
		parent::tearDown();
	}


	//
	//
	// HELPERS
	//
	//


	public function translate_text( $translation, $text ) {
		if ( 'Donate' === $text ) {
			$translation = 'Donar';
		}

		return $translation;
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function wp_version_comparisons() {
		return array(
			//[ WP ver, version to compare to, operator, expected result ]
			[ '5.5', '5.5.1', '>=', false ],
			[ '5.5', '5.6',   '>=', false ],
			[ '5.5', '5.5',   '>=', true ],
			[ '5.5', '5.4.3', '>=', true ],
			[ '5.5', '5.5.1', '',   false ],
			[ '5.5', '5.6',   '',   false ],
			[ '5.5', '5.5',   '',   true ],
			[ '5.5', '5.5',   '>',  false ],
			[ '5.5', '5.5',   '<',  false ],
			[ '5.5', '5.5.1', '<=', true ],
			[ '5.5', '5.6',   '<=', true ],
			[ '5.5', '5.5',   '<=', true ],
			[ '5.5', '5.4.3', '<=', false ],
			[ '5.5', '5.5',   '=',  true ],
			[ '5.5', '5.5.1', '=',  false ],
			[ '5.5', '5.5',   '!=', false ],
		);
	}


	//
	//
	// TESTS
	//
	//


	/*
	 * __clone()
	 */

	/**
	 * @expectedException Error
	 */
	public function test_unable_to_clone_object() {
		$clone = clone $this->obj;
		$this->assertEquals( $clone, $this->obj );
	}

	/*
	 * __wakeup()
	 */

	/**
	 * @expectedException Error
	 */
	public function test_unable_to_instantiate_object_from_class() {
		new get_class( $this->obj );
	}

	/**
	 * @expectedException Error
	 */
	public function test_unable_to_unserialize_an_instance_of_the_class() {
		$class = get_class( $this->obj );
		$data = 'O:' . strlen( $class ) . ':"' . $class . '":0:{}';

		unserialize( $data );
	}

	/*
	 * is_wp_version_cmp()
	 */

	/**
	 * @dataProvider wp_version_comparisons
	 */
	public function test_is_wp_version_cmp( $wp_ver, $ver, $op, $expected ) {
		global $wp_version;
		$orig_wp_verion = $wp_version;

		$wp_version = $wp_ver;
		$this->{ $expected ? 'assertTrue' : 'assertFalse' }( $this->obj->is_wp_version_cmp( $ver, $op ) );

		$wp_version = $orig_wp_verion;
	}

	/*
	 * get_c2c_string()
	 */

	 public function test_get_c2c_string_size() {
		$this->assertEquals( 24, count( $this->obj->get_c2c_string( '' ) ) );
	}

	public function test_get_c2c_string_for_unknown_string() {
		$str = 'unknown string';

		$this->assertEquals( $str, $this->obj->get_c2c_string( $str ) );
	}

	public function test_get_c2c_string_for_known_string_translated() {
		$this->assertEquals( 'Donar', $this->obj->get_c2c_string( 'Donate' ) );
	}

	public function test_get_c2c_string_for_known_string_untranslated() {
		$str = 'A value is required for: "%s"';

		$this->assertEquals( $str, $this->obj->get_c2c_string( $str ) );
	}

}
