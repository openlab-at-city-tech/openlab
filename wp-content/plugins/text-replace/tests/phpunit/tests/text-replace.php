<?php

defined( 'ABSPATH' ) or die();

class Text_Replace_Test extends WP_UnitTestCase {

	protected $obj;

	protected $captured_filter_value = array();

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
		'Cocktail glacé' => 'http://www.domain.com/cocktail-glace.html',
		'ユニコード漢字'   => 'http://php.net/manual/en/ref.mbstring.php',
		'ユニコード漢字 は' => 'replacment text',
		'Apple iPhone 6' => 'http://example.com/apple1',
		'iPhone 6'       => 'http://example.com/aople2',
		'test'           => 'http://example.com/txst1',
		'test place'     => 'http://example.com/txst2',
		'zero'           => '0',
		'empty string'   => '',
	);

	public static function setUpBeforeClass() {
		c2c_TextReplace::get_instance()->install();
	}

	public function setUp() {
		parent::setUp();

		$this->obj = c2c_TextReplace::get_instance();

		$this->set_option();
	}

	public function tearDown() {
		parent::tearDown();

		$this->captured_filter_value = array();

		// Reset options
		$this->obj->reset_options();
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

	public static function get_third_party_filters() {
		return array(
			array( 'acf/format_value/type=text' ),
			array( 'acf/format_value/type=textarea' ),
			array( 'acf/format_value/type=url' ),
			array( 'acf_the_content' ),
			array( 'elementor/frontend/the_content' ),
			array( 'elementor/widget/render_content' ),
		);
	}

	public static function get_text_to_link() {
		return array_map( function($v) { return array( $v ); }, array_keys( self::$text_to_link ) );
	}

	public static function get_ending_punctuation() {
		return array(
			array( '.' ),
			array( ',' ),
			array( '!' ),
			array( '?' ),
			array( ';' ),
			array( ':' ),
		);
	}

	public static function get_special_chars() {
		return array(
			array( array( '>', '<' ) ),
			array( array( '(', ')' ) ),
			array( array( ')', '(' ) ),
			array( array( '{', '}' ) ),
			array( array( ']', '[' ) ),
			array( array( '[', ']' ) ),
			array( array( '<strong>', '</strong>' ) ),
		);
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
		$this->obj->update_option( $settings, true );
	}

	protected function text_replace( $text ) {
		return $this->obj->text_replace( $text );
	}

	protected function expected_text( $term ) {
		return $this->text_replacements( $term );
	}

	protected function get_filter_names() {
		return array_map(
			function ( $x ) { return reset( $x ); },
			array_merge(
				$this->get_third_party_filters(),
				$this->get_default_filters()
			)
		);
	}

	public function unhook_default_filters( $priority = 2 ) {
		$filters = $this->get_filter_names();

		// Unhook filters.
		foreach ( $filters as $filter ) {
			remove_filter( $filter, array( $this->obj, 'text_replace' ), $priority );
		}
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

	public function c2c_text_replace_filter_priority( $priority, $filter = '' ) {
		return ( 'filter_20' === $filter ) ? 20 : 11;
	}

	public function capture_filter_value( $value ) {
		return $this->captured_filter_value[ current_filter() ] = $value;
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
		$this->assertTrue( class_exists( 'c2c_Plugin_064' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '064', $this->obj->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '4.0', $this->obj->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( $this->obj, 'c2c_TextReplace' ) );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_TextReplace', 'get_instance' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_text_replace', c2c_TextReplace::SETTING_NAME );
	}

	/*
	 * Setting defaults.
	 */

	public function test_default_value_of_text_to_replace() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$expected = array(
			':wp:'          => "<a href='https://wordpress.org'>WordPress</a>",
			':codex:'       => "<a href='https://codex.wordpress.org'>WordPress Codex</a>",
			':coffee2code:' => "<a href='https://coffee2code.com' title='coffee2code'>coffee2code</a>",
		);

		$this->assertEquals( $expected, $options['text_to_replace'] );
	}

	public function test_default_value_of_text_replace_comments() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertFalse( $options['text_replace_comments'] );
	}

	public function test_default_value_of_replace_once() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertFalse( $options['replace_once'] );
	}

	public function test_default_value_of_case_sensitive() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertTrue( $options['case_sensitive'] );
	}

	public function test_default_value_of_when() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertEquals( 'early', $options['when'] );
	}

	public function test_default_value_of_more_filters() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertEmpty( $options['more_filters'] );
	}

	/*
	 * Text replacements
	 */

	public function test_no_text_change_when_no_replacements_defined() {
		$text = 'This is a 2019 test.';

		$this->set_option( array( 'text_to_replace' => '' ) );

		$this->assertEquals( $text, $this->text_replace( $text ) );

		$this->set_option( array( 'text_to_replace' => $this->text_replacements() ) );
	}

	public function test_replaces_text() {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( $expected, $this->text_replace( ':coffee2code:' ) );
		$this->assertEquals( "ends with $expected", $this->text_replace( 'ends with :coffee2code:' ) );
		$this->assertEquals( "ends with period $expected.", $this->text_replace( 'ends with period :coffee2code:.' ) );
		$this->assertEquals( "$expected starts", $this->text_replace( ':coffee2code: starts' ) );

		$this->assertEquals( $this->expected_text( 'Matt Mullenweg' ), $this->text_replace( 'Matt Mullenweg' ) );
	}

	public function test_replaces_partially_matching_text() {
		$this->assertEquals( 'con' . $this->expected_text( 'test' ), $this->text_replace( 'contest' ) );
	}

	/**
	 * @dataProvider get_text_to_link
	 */
	public function test_replaces_text_as_defined_in_setting( $text ) {
		$this->assertEquals( $this->expected_text( $text ), $this->text_replace( $text ) );
	}

	/**
	 * @dataProvider get_ending_punctuation
	 */
	public function test_replaces_text_adjacent_to_punctuation( $punctuation ) {
		$this->assertEquals(
			$this->text_replace( ':wp:' ) . $punctuation,
			$this->text_replace( ':wp:' . $punctuation )
		);
	}

	/**
	 * @dataProvider get_special_chars
	 */
	public function test_replaces_text_adjacent_to_special_characters( $data ) {
		list( $start_char, $end_char ) = $data;

		$this->assertEquals(
			$start_char . $this->text_replace( ':wp:' ) . ' & ' . $this->text_replace( ':coffee2code:' ) . $end_char,
			$this->text_replace( $start_char . ':wp: & :coffee2code:' . $end_char ),
			"Failed asserting text replace within special characters '{$start_char}' and '{$end_char}'."
		);
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
		$expected = '<a href="http://test.com" title="A test site">gibberish</a>';

		$this->assertEquals( $expected, $this->text_replace( $expected ) );
	}

	public function test_does_not_replace_within_markup_attributes_but_does_between_tags() {
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

	/*
	 * With 'Apple iPhone 6' followed by 'iPhone 6' as replacement defines, the string
	 * 'Apple iPhone 6' should not have the 'iPhone 6' replacement applied to it.
	 */
	public function test_does_not_replace_a_general_term_that_is_included_in_earlier_listed_term() {
		$string = 'Apple iPhone 6';

		$this->assertEquals( $this->expected_text( $string ), $this->text_replace( $string ) );
	}

	/**
	 * Ensure a more specific string matches with priority over a less specific
	 * string, regardless of what order they were defined.
	 *
	 *  MAYBE! Not sure if this is desired. But the theory is if both
	 * "test" and "test place" are defined, then the text "test place" should get
	 * replaced, even though "test" was defined first.
	 */
	public function test_does_not_replace_a_more_general_term_when_general_is_first() {
		$expected = $this->expected_text( 'test place' );

		$this->assertEquals( "This $expected is true", $this->text_replace( 'This test place is true' ) );
	}

	public function test_replaces_term_split_across_multiple_lines() {
		$expected = array(
			"See my " . $this->expected_text( 'test place' ) . " site to read."
				=> $this->text_replace( "See my test\nplace site to read." ),
			"See my " . $this->expected_text( 'test place' ) . " site to read."
				=> $this->text_replace( "See my test   place site to read." ),
			"These are " . $this->expected_text( 'Cocktail glacé' ) . " to read"
				=> $this->text_replace( "These are Cocktail\n\tglacé to read" ),
			"This is interesting " . $this->expected_text( "ユニコード漢字 は" ) . " if I do say so"
				=> $this->text_replace( "This is interesting ユニコード漢字\nは if I do say so" ),
			"This is interesting " . $this->expected_text( "ユニコード漢字 は" ) . " if I do say so"
				=> $this->text_replace( "This is interesting ユニコード漢字\t  は if I do say so" ),
		);

		foreach ( $expected as $expect => $actual ) {
			$this->assertEquals( $expect, $actual );
		}
	}

	// Note: This KNOWN FAILURE test presumes that replacement text should not
	// be at risk of seeing a replacement itself. This may not be a valid
	// presumption though.
	public function test_does_not_replace_a_previous_replacement() {
		$expected = 'this may not actually be considered a valid test';

		$this->set_option( array(
			'text_to_replace' => array(
				'test'       => 'http://example.com/txst1',
				'test thing' => $expected,
			)
		) );

		$this->assertEquals(
			$expected,
			$this->text_replace( 'test thing' ),
			'This is a KNOWN FAILURE that presumes replacement text should not themselves see replacements. The existing behavior could be considered a feature.'
		);
	}

	// Note: This KNOWN FAILURE test presumes that shortcode tag should
	// not be at risk of seeing a replacement itself. This may not be a valid
	// presumption though.
	public function test_does_not_replace_shortcode() {
		$expected = '[test title="This may not actually be a valid test"]gibberish[/test]';

		$this->assertEquals(
			$expected,
			$this->text_replace( $expected ),
			'This is a KNOWN FAILURE that presumes shortcode tags should not get replaced. The existing behavior could be considered a feature.'
		);
	}

	// Note: This KNOWN FAILURE test presumes that shortcode attributes should
	// not be at risk of seeing a replacement itself. This may not be a valid
	// presumption though.
	public function test_does_not_replace_within_shortcodes_attributes() {
		$expected = '[caption title="This may not actually be a valid test"]gibberish[/caption]';

		$this->assertEquals(
			$expected,
			$this->text_replace( $expected ),
			'This is a KNOWN FAILURE that presumes shortcode attributes should not get replacements. The existing behavior could be considered a feature.'
		);
	}

	public function test_replaces_multibyte_text_once_via_setting() {
		$linked = $this->expected_text( 'Cocktail glacé' );

		$this->set_option( array( 'replace_once' => true ) );

		$expected = array(
			"$linked Cocktail glacé Cocktail glacé"
				=> $this->text_replace( 'Cocktail glacé Cocktail glacé Cocktail glacé' ),
			'dock ' . $this->expected_text( 'ユニコード漢字' ) . ' cart ユニコード漢字'
				=> $this->text_replace( 'dock ユニコード漢字 cart ユニコード漢字' ),
		);

		foreach ( $expected as $expect => $actual ) {
			$this->assertEquals( $expect, $actual );
		}
	}

	public function test_replaces_once_via_setting() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertEquals( "$expected :coffee2code: :coffee2code:", $this->text_replace( ':coffee2code: :coffee2code: :coffee2code:' ) );
	}

	public function test_replaces_once_via_trueish_setting_value() {
		$expected = $this->expected_text( ':coffee2code:' );
		$this->test_replaces_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => '1' ) );

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

	public function test_replaces_filter_added_via_more_filters() {
		$this->assertEquals( 'Hello :wp:', apply_filters( 'the_title', 'Hello :wp:' ) );

		$this->set_option( array( 'more_filters' => array( 'the_title' ) ) );
		$this->obj->register_filters();

		$this->assertEquals( 2, has_filter( 'the_title', array( $this->obj, 'text_replace' ) ) );
		$this->assertEquals( 'Hello WordPress', apply_filters( 'the_title', 'Hello :wp:' ) );
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
	public function test_replace_applies_to_default_filters( $filter, $priority = 2 ) {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( $priority, has_filter( $filter, array( $this->obj, 'text_replace' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a :coffee2code:' ), $expected ) );
	}

	/**
	 * @dataProvider get_comment_filters
	 */
	public function test_replace_applies_to_comment_filters( $filter ) {
		$expected = $this->expected_text( ':coffee2code:' );

		add_filter( 'c2c_text_replace_comments', '__return_true' );

		$this->assertEquals( 11, has_filter( $filter, array( $this->obj, 'text_replace_comment_text' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a :coffee2code:' ), $expected ) );
	}

	/**
	 * @dataProvider get_third_party_filters
	 */
	public function test_replace_applies_to_third_party_filters( $filter ) {
		$expected = $this->expected_text( ':coffee2code:' );

		$this->assertEquals( 2, has_filter( $filter, array( $this->obj, 'text_replace' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a :coffee2code:' ), $expected ) );
	}

	public function test_third_party_filters_are_part_of_c2c_text_replace_filters() {
		$filters = $this->get_filter_names();

		add_filter( 'c2c_text_replace_filters', array( $this, 'capture_filter_value' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertSame( $filters, $this->captured_filter_value[ 'c2c_text_replace_filters' ] );

		remove_filter( 'c2c_text_replace_filters', array( $this, 'capture_filter_value' ) );
	}

	public function test_replace_applies_to_custom_filter_via_filter() {
		$this->assertEquals( ':coffee2code:', apply_filters( 'custom_filter', ':coffee2code:' ) );

		add_filter( 'c2c_text_replace_filters', array( $this, 'add_custom_filter' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( ':coffee2code:' ), apply_filters( 'custom_filter', ':coffee2code:' ) );
	}

	public function test_replace_applies_to_custom_third_party_filter_via_filter() {
		$this->assertEquals( ':coffee2code:', apply_filters( 'custom_filter', ':coffee2code:' ) );

		add_filter( 'c2c_text_replace_third_party_filters', array( $this, 'add_custom_filter' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( ':coffee2code:' ), apply_filters( 'custom_filter', ':coffee2code:' ) );
	}

	/*
	 * filter: c2c_text_replace_filter_priority
	 */

	public function test_changing_priority_via_c2c_text_replace_filter_priority() {
		$filters = $this->get_filter_names();

		$this->unhook_default_filters();

		add_filter( 'c2c_text_replace_filter_priority', array( $this, 'c2c_text_replace_filter_priority' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$priority = 11;

		foreach ( $filters as $filter ) {
			$this->test_replace_applies_to_default_filters( $filter, $priority );
		}
	}

	public function test_default_priority_for_filter_c2c_text_replace_filter_priority_is_based_on_when_setting() {
		$this->unhook_default_filters();

		add_filter( 'c2c_text_replace_filter_priority', array( $this, 'capture_filter_value' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( 2, $this->captured_filter_value[ 'c2c_text_replace_filter_priority' ] );

		$this->unhook_default_filters();
		$this->set_option( array( 'when' => 'late' ) );
		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( 1000, $this->captured_filter_value[ 'c2c_text_replace_filter_priority' ] );

		$this->unhook_default_filters( 1000 );
	}

	/*
	 * get_default_filters()
	 */

	public function test_get_default_filters_default() {
		$this->assertEquals( array( 'the_content', 'the_excerpt', 'widget_text' ), $this->obj->get_default_filters() );
	}

	public function test_get_default_filters_core() {
		$this->assertEquals( array( 'the_content', 'the_excerpt', 'widget_text' ), $this->obj->get_default_filters( 'core' ) );
	}

	public function test_get_default_filters_invalid() {
		$this->assertEmpty( $this->obj->get_default_filters( 'invalid' ) );
	}

	public function test_get_default_filters_third_party() {
		$filters = array(
			'acf/format_value/type=text',
			'acf/format_value/type=textarea',
			'acf/format_value/type=url',
			'acf_the_content',
			// Support for Elementor plugin.
			'elementor/frontend/the_content',
			'elementor/widget/render_content',
		);

		$this->assertEquals( $filters, $this->obj->get_default_filters( 'third_party' ) );
	}

	public function test_get_default_filters_both() {
		$filters = array(
			'acf/format_value/type=text',
			'acf/format_value/type=textarea',
			'acf/format_value/type=url',
			'acf_the_content',
			// Support for Elementor plugin.
			'elementor/frontend/the_content',
			'elementor/widget/render_content',
		);

		$this->assertEquals(
			array_merge( array( 'the_content', 'the_excerpt', 'widget_text' ), $filters ),
			$this->obj->get_default_filters( 'both' )
		);
	}

	/*
	 * options_page_description()
	 */

	// Note: By no means a text of the full output of the function.
	public function test_options_page_description() {
		$expected = '<h1>Text Replace Settings</h1>' . "\n";
		$expected .= '<p class="see-help">See the "Help" link to the top-right of the page for more help.</p>' . "\n";
		$expected .= '<p>Text Replace is a plugin that allows you to replace text with other text in posts, etc. Very handy to create shortcuts to commonly-typed and/or lengthy text/HTML, or for smilies.</p>';

		$this->expectOutputRegex( '~' . preg_quote( $expected ) . '~', $this->obj->options_page_description() );
	}

	/*
	 * Setting handling
	 */

	/*
	// This is normally the case, but the unit tests save the setting to db via
	// setUp(), so until the unit tests are restructured somewhat, this test
	// would fail.
	public function test_does_not_immediately_store_default_settings_in_db() {
		$option_name = c2c_TextReplace::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = $this->obj->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_TextReplace::SETTING_NAME;
		$options     = $this->obj->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_TextReplace::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
