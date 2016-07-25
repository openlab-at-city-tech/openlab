<?php

defined( 'ABSPATH' ) or die();

class Text_Replace_Test extends WP_UnitTestCase {

	protected static $text_to_link = array(
		':wp:'           => 'WordPress',
		":coffee2code:"  => "<a href='http://coffee2code.com' title='coffee2code'>coffee2code</a>",
		'Matt Mullenweg' => '<span title="Founder of WordPress">Matt Mullenweg</span>',
		'<strong>to be linked</strong>' => '<a href="http://example.org/link">to be linked</a>',
		'comma, here'    => 'Yes, a comma',
		'"quoted text"'  => 'quoted "text"',
		'blank'          => '',
		':WP:'           => "<a href='https://w.org'>WP</a> <!-- Replacement by <contact>person</contact> -->",
		'example.com/wp-content/uploads' => 'example.org/wp-content/uploads',
		':A&A:'          => 'Axis & Allies',
		'は'             => 'Foo',
		'@macnfoco'      => "Mac'N",
	);

	public function setUp() {
		parent::setUp();
		$this->set_option();
	}

	public function tearDown() {
		parent::tearDown();

		remove_filter( 'c2c_text_replace',                array( $this, 'add_text_to_replace' ) );
		remove_filter( 'c2c_text_replace_once',           '__return_true' );
		remove_filter( 'c2c_text_replace_case_sensitive', '__return_false' );
		remove_filter( 'c2c_text_replace_comments',       '__return_true' );
		remove_filter( 'c2c_text_replace_filters',        array( $this, 'add_custom_filter' ) );
	}


	/*
	 *
	 * DATA PROVIDERS
	 *
	 */


	public static function get_default_filters() {
		return array(
			array( 'the_content' ),
			array( 'the_excerpt' ),
			array( 'widget_text' ),
		);
	}

	public static function get_comment_filters() {
		return array(
			array( 'get_comment_text' ),
			array( 'get_comment_excerpt' ),
		);
	}

	public static function get_text_to_link() {
		return array_map( function($v) { return array( $v ); }, array_keys( self::$text_to_link ) );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	protected function text_replacements( $term = '' ) {
		$text_to_link = self::$text_to_link;

		if ( ! empty( $term ) ) {
			$text_to_link = isset( $text_to_link[ $term ] ) ? $text_to_link[ $term ] : '';
		}

		return $text_to_link;
	}

	protected function set_option( $settings = array() ) {
		$defaults = array(
			'text_to_replace' => $this->text_replacements(),
			'case_sensitive'  => true,
		);
		$settings = wp_parse_args( $settings, $defaults );
		c2c_TextReplace::get_instance()->update_option( $settings, true );
	}

	protected function text_replace( $text ) {
		return c2c_TextReplace::get_instance()->text_replace( $text );
	}

	protected function expected_text( $term ) {
		return $this->text_replacements( $term );
	}

	public function add_text_to_replace( $text_to_replace ) {
		$text_to_replace = (array) $text_to_replace;
		$text_to_replace['bbPress'] = '<a href="https://bbpress.org">bbPress - Forum Software</a>';
		return $text_to_replace;
	}

	public function add_custom_filter( $filters ) {
		$filters[] = 'custom_filter';
		return $filters;
	}


	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_TextReplace' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_TextReplace_Plugin_043' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '043', c2c_TextReplace::get_instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '3.7', c2c_TextReplace::get_instance()->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( c2c_TextReplace::get_instance(), 'c2c_TextReplace' ) );
	}

	public function test_replaces_text() {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( $expected, $this->text_replace( ':coffee2code:' ) );
		$this->assertEquals( "ends with $expected", $this->text_replace( 'ends with :coffee2code:' ) );
		$this->assertEquals( "ends with period $expected.", $this->text_replace( 'ends with period :coffee2code:.' ) );
		$this->assertEquals( "$expected starts", $this->text_replace( ':coffee2code: starts' ) );

		$this->assertEquals( $this->expected_text( 'Matt Mullenweg' ), $this->text_replace( 'Matt Mullenweg' ) );
	}

	/**
	 * @dataProvider get_text_to_link
	 */
	public function test_replaces_text_as_defined_in_setting( $text ) {
		$this->assertEquals( $this->expected_text( $text ), $this->text_replace( $text ) );
	}

	public function test_replaces_text_with_html_encoded_amp_ampersand() {
		$this->assertEquals( $this->expected_text( ':A&A:' ), $this->text_replace( ':A&amp;A:' ) );
	}

	public function test_replaces_text_with_html_encoded_038_ampersand() {
		$this->assertEquals( $this->expected_text( ':A&A:' ), $this->text_replace( ':A&#038;A:' ) );
	}

	public function test_replaces_multibyte_text() {
		$this->assertEquals( '漢字Fooユニコード', $this->text_replace( '漢字はユニコード' ) );
	}

	public function test_replaces_single_term_multiple_times() {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( "$expected $expected $expected", $this->text_replace( ':coffee2code: :coffee2code: :coffee2code:' ) );
	}

	public function test_replaces_html_multiple_times() {
		$orig = '<strong>to be linked</strong>';
		$expected = $this->expected_text( $orig );

		$this->assertEquals( "$expected $expected $expected", $this->text_replace( "$orig $orig $orig" ) );
	}

	public function test_replaces_substrings() {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( 'x' . $expected,       $this->text_replace( 'x:coffee2code:' ) );
		$this->assertEquals( 'y' . $expected . 'y', $this->text_replace( 'y:coffee2code:y' ) );
		$this->assertEquals( $expected . 'z',       $this->text_replace( ':coffee2code:z' ) );
	}

	public function test_replaces_html() {
		$this->assertEquals( $this->expected_text( '<strong>to be linked</strong>' ), $this->text_replace( '<strong>to be linked</strong>' ) );
	}

	public function test_replace_with_html_comment() {
		$expected = $this->expected_text( ':WP:' );

		$this->assertEquals( $expected, $this->text_replace( ':WP:' ) );
	}

	public function test_empty_replacement_removes_term() {
		$this->assertEquals( '', $this->text_replace( 'blank' ) );
	}

	public function test_does_not_replace_within_markup_attributes() {
		$format = '<a href="http://%s/file.png">http://%s/file.png</a>';
		$old    = 'example.com/wp-content/uploads';
		$new    = $this->expected_text( $old );

		$this->assertEquals(
			sprintf(
				$format,
				$old,
				$new
			),
			$this->text_replace( sprintf(
				$format,
				$old,
				$old
			) )
		);
	}

	public function test_replaces_with_case_sensitivity_by_default() {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( $expected,       $this->text_replace( ':coffee2code:' ) );
		$this->assertEquals( ':Coffee2code:', $this->text_replace( ':Coffee2code:' ) );
		$this->assertEquals( ':COFFEE2CODE:', $this->text_replace( ':COFFEE2CODE:' ) );
	}

	public function test_replaces_once_via_setting() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertEquals( "$expected :coffee2code: :coffee2code:", $this->text_replace( ':coffee2code: :coffee2code: :coffee2code:' ) );
	}

	public function test_replaces_once_via_filter() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_single_term_multiple_times();
		add_filter( 'c2c_text_replace_once', '__return_true' );

		$this->assertEquals( "$expected :coffee2code: :coffee2code:", $this->text_replace( ':coffee2code: :coffee2code: :coffee2code:' ) );
	}

	public function test_replaces_html_once_when_replace_once_is_true() {
		$orig = '<strong>to be linked</strong>';
		$expected = $this->expected_text( $orig );
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertEquals( "$expected $orig $orig", $this->text_replace( "$orig $orig $orig" ) );
	}

	public function test_replaces_with_case_insensitivity_via_setting() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_with_case_sensitivity_by_default();
		$this->set_option( array( 'case_sensitive' => false ) );

		$this->assertEquals( $expected, $this->text_replace( ':coffee2code:' ) );
		$this->assertEquals( $expected, $this->text_replace( ':Coffee2code:' ) );
		$this->assertEquals( $expected, $this->text_replace( ':COFFEE2CODE:' ) );
	}

	public function test_replaces_with_case_insensitivity_via_filter() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_with_case_sensitivity_by_default();
		add_filter( 'c2c_text_replace_case_sensitive', '__return_false' );

		$this->assertEquals( $expected, $this->text_replace( ':coffee2code:' ) );
		$this->assertEquals( $expected, $this->text_replace( ':Coffee2code:' ) );
		$this->assertEquals( $expected, $this->text_replace( ':COFFEE2CODE:' ) );
	}

	public function test_replaces_html_when_case_insensitive_is_true() {
		$expected = $this->expected_text( '<strong>to be linked</strong>' );
		$this->set_option( array( 'case_sensitive' => false ) );

		$this->assertEquals( $expected, $this->text_replace( '<strong>to be linked</strong>' ) );
		$this->assertEquals( $expected, $this->text_replace( '<strong>To Be Linked</strong>' ) );
	}

	public function test_replaces_html_when_case_insensitive_and_replace_once_are_true() {
		$expected = $this->expected_text( '<strong>to be linked</strong>' );
		$this->set_option( array( 'case_sensitive' => false, 'replace_once' => true ) );

		$str = '<strong>TO BE linked</strong>';
		$this->assertEquals( "$expected $str", $this->text_replace( "$str $str" ) );
	}

	public function test_replaces_term_added_via_filter() {
		$this->assertEquals( 'bbPress', $this->text_replace( 'bbPress' ) );
		$expected = '<a href="https://bbpress.org">bbPress - Forum Software</a>';
		add_filter( 'c2c_text_replace', array( $this, 'add_text_to_replace' ) );

		$this->assertEquals( $expected, $this->text_replace( 'bbPress' ) );
	}

	public function test_replace_does_not_apply_to_comments_by_default() {
		$this->assertEquals( ':coffee2code:', apply_filters( 'get_comment_text', ':coffee2code:' ) );
		$this->assertEquals( ':coffee2code:', apply_filters( 'get_comment_excerpt', ':coffee2code:' ) );
	}

	public function test_replace_applies_to_comments_via_setting() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replace_does_not_apply_to_comments_by_default();
		$this->set_option( array( 'text_replace_comments' => true ) );

		$this->assertEquals( $expected, apply_filters( 'get_comment_text', ':coffee2code:' ) );
		$this->assertEquals( $expected, apply_filters( 'get_comment_excerpt', ':coffee2code:' ) );
	}

	public function test_replace_applies_to_comments_via_filter() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replace_does_not_apply_to_comments_by_default();

		add_filter( 'c2c_text_replace_comments', '__return_true' );

		$this->assertEquals( $expected, apply_filters( 'get_comment_text', ':coffee2code:' ) );
		$this->assertEquals( $expected, apply_filters( 'get_comment_excerpt', ':coffee2code:' ) );
	}

	/**
	 * @dataProvider get_default_filters
	 */
	public function test_replace_applies_to_default_filters( $filter ) {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertNotFalse( has_filter( $filter, array( c2c_TextReplace::get_instance(), 'text_replace' ), 12 ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a :coffee2code:' ), $expected ) );
	}

	/**
	 * @dataProvider get_comment_filters
	 */
	public function test_replace_applies_to_comment_filters( $filter ) {
		$expected = $this->expected_text( ':coffee2code:' );

		add_filter( 'c2c_text_replace_comments', '__return_true' );

		$this->assertNotFalse( has_filter( $filter, array( c2c_TextReplace::get_instance(), 'text_replace_comment_text' ), 11 ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a :coffee2code:' ), $expected ) );
	}

	public function test_replace_applies_to_custom_filter_via_filter() {
		$this->assertEquals( ':coffee2code:', apply_filters( 'custom_filter', ':coffee2code:' ) );

		add_filter( 'c2c_text_replace_filters', array( $this, 'add_custom_filter' ) );

		c2c_TextReplace::get_instance()->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( ':coffee2code:' ), apply_filters( 'custom_filter', ':coffee2code:' ) );
	}

	public function test_uninstall_deletes_option() {
		$option = 'c2c_text_replace';
		c2c_TextReplace::get_instance()->get_options();

		$this->assertNotFalse( get_option( $option ) );

		c2c_TextReplace::uninstall();

		$this->assertFalse( get_option( $option ) );
	}
}
