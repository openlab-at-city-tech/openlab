<?php

defined( 'ABSPATH' ) or die();

class Text_Hover_Test extends WP_UnitTestCase {

	protected static $text_to_hover = array(
		'WP'             => 'WordPress',
		"coffee2code"    => 'Plugin developer',
		'Matt Mullenweg' => 'Co-Founder of WordPress',
		'blank'          => '',
		'C&C'            => 'Command & Control',
		'漢字はユニコード'  => 'Kanji Unicode',
		'HTML'           => '<strong>HTML</strong>',
		'水'             => 'water',
		'<em>Test</em>'  => 'This is text associated with a replacement that includes HTML tags.',
		'piñata'         => 'Full of candy',
	);

	public function setUp() {
		parent::setUp();
		$this->set_option();
	}

	public function tearDown() {
		parent::tearDown();

		remove_filter( 'c2c_text_hover',                array( $this, 'add_text_to_hover' ) );
		remove_filter( 'c2c_text_hover_once',           '__return_true' );
		remove_filter( 'c2c_text_hover_case_sensitive', '__return_false' );
		remove_filter( 'c2c_text_hover_comments',       '__return_true' );
		remove_filter( 'c2c_text_hover_filters',        array( $this, 'add_custom_filter' ) );
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_filters() {
		return array(
			array( 'the_content' ),
			array( 'get_the_excerpt' ),
			array( 'widget_text' ),
		);
	}

	public static function get_comment_filters() {
		return array(
			array( 'get_comment_text' ),
			array( 'get_comment_excerpt' ),
		);
	}

	public static function get_text_to_hover() {
		return array_map( function($v) { return array( $v ); }, array_keys( self::$text_to_hover ) );
	}

	public static function get_ending_punctuation() {
		return array(
			array( '.' ),
			array( ',' ),
			array( '!' ),
			array( '?' ),
		);
	}

	public static function get_special_chars() {
		return array(
			array( array( '>', '<' ) ),
			array( array( '(', ')' ) ),
			array( array( ')', '(' ) ),
			array( array( '{', '}' ) ),
			array( array( '<strong>', '</strong>' ) ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//

	protected function text_hovers( $term = '' ) {
		$text_to_hover = self::$text_to_hover;

		if ( ! empty( $term ) ) {
			$text_to_hover = isset( $text_to_hover[ $term ] ) ? $text_to_hover[ $term ] : '';
		}

		return $text_to_hover;
	}

	protected function set_option( $settings = array() ) {
		$defaults = array(
			'text_to_hover'  => $this->text_hovers(),
			'case_sensitive' => true,
		);
		$settings = wp_parse_args( $settings, $defaults );
		c2c_TextHover::get_instance()->update_option( $settings, true );
	}

	protected function text_hover( $text ) {
		return c2c_TextHover::get_instance()->text_hover( $text );
	}

	/**
	 * @param string $display_term Term that should be in link if it doesn't match $term.
	 */
	protected function expected_text( $term, $display_term = '' ) {
		$hover_text = $this->text_hovers( $term );
		if ( empty( $hover_text ) ) {
			$hover_text = $this->text_hovers( strtolower( $term ) );
		}
		if ( empty( $hover_text ) ){
			return $term;
		}
		if ( $display_term ) {
			$term = $display_term;
		}
		return "<acronym class='c2c-text-hover' title='" . esc_attr( $hover_text ) . "'>$term</acronym>";
	}

	public function add_text_to_hover( $text_to_hover ) {
		$text_to_hover = (array) $text_to_hover;
		$text_to_hover['bbPress'] = 'Forum Software';
		return $text_to_hover;
	}

	public function add_custom_filter( $filters ) {
		$filters[] = 'custom_filter';
		return $filters;
	}


	/*
	 *
	 * TESTS
	 *
	 */


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_TextHover' ) );
	}

	public function test_plugin_framework_class_name() {
		$this->assertTrue( class_exists( 'c2c_TextHover_Plugin_044' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '044', c2c_TextHover::get_instance()->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '3.7.1', c2c_TextHover::get_instance()->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( c2c_TextHover::get_instance(), 'c2c_TextHover' ) );
	}

	public function test_hovers_text() {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( $expected,                     $this->text_hover( 'coffee2code' ) );
		$this->assertEquals( "ends with $expected",         $this->text_hover( 'ends with coffee2code' ) );
		$this->assertEquals( "ends with period $expected.", $this->text_hover( 'ends with period coffee2code.' ) );
		$this->assertEquals( "$expected starts",            $this->text_hover( 'coffee2code starts' ) );

		$this->assertEquals( $this->expected_text( 'Matt Mullenweg' ), $this->text_hover( 'Matt Mullenweg' ) );
	}

	/**
	 * @dataProvider get_text_to_hover
	 */
	public function test_hovers_text_as_defined_in_setting( $text ) {
		$this->assertEquals( $this->expected_text( $text ), $this->text_hover( $text ) );
	}

	// This duplicates the test just previously done, but doing it again to ensure
	// this is explicitly tested.
	public function test_hover_text_is_attribute_escaped() {
		$this->assertEquals(
			"<acronym class='c2c-text-hover' title='&lt;strong&gt;HTML&lt;/strong&gt;'>HTML</acronym>",
			$this->text_hover( 'HTML' )
		);
	}

	/**
	 * @dataProvider get_ending_punctuation
	 */
	public function test_hover_text_adjacent_to_punctuation( $punctuation ) {
		$this->assertEquals(
			$this->text_hover( '水' ) . $punctuation,
			$this->text_hover( '水' . $punctuation )
		);
	}

	public function test_hover_text_at_end_of_line() {
		$text = "This is a multi-line\npiece of text that end\nwith a multibyte character ";

		$this->assertEquals(
			$text . $this->text_hover( '水' ),
			$this->text_hover( $text . '水' )
		);
	}

	public function test_hover_text_when_followed_by_tag() {
		$this->assertEquals(
			'word ' . $this->text_hover( '水' ) . '</p>',
			$this->text_hover( 'word 水</p>' )
		);
	}

	public function test_no_hover_text_in_attribute() {
		$text  = '<a href="http://example.com" title="Learn the 水 character">link</a>';

		$this->assertEquals(
			$text,
			$this->text_hover( $text )
		);
	}

	/**
	 * @dataProvider get_special_chars
	 */
	public function test_hover_text_adjacent_to_special_characters( $data ) {
		list( $start_char, $end_char ) = $data;

		$this->assertEquals(
			$start_char . $this->text_hover( 'WP' ) . ' & ' . $this->text_hover( 'C&C' ) . $end_char,
			$this->text_hover( $start_char . 'WP & C&C' . $end_char ),
			"Failed asserting text hover within special characters '{$start_char}' and '{$end_char}'."
		);
	}

	public function test_hovers_text_with_html_encoded_amp_ampersand() {
		$this->assertEquals( $this->expected_text( 'C&C', 'C&amp;C' ), $this->text_hover( 'C&amp;C' ) );
	}

	public function test_hovers_text_with_html_encoded_038_ampersand() {
		$this->assertEquals( $this->expected_text( 'C&C', 'C&#038;C' ), $this->text_hover( 'C&#038;C' ) );
	}

	public function test_hovers_single_term_multiple_times() {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( "$expected  $expected  $expected", $this->text_hover( 'coffee2code  coffee2code  coffee2code' ) );
	}

	public function test_hovers_single_multibyte_term_multiple_times() {
		$expected = $this->expected_text( '水' );

		$this->assertEquals( "$expected  $expected  $expected", $this->text_hover( '水  水  水' ) );
	}

	public function test_permits_html_substitutions() {
		$this->set_option( array( 'text_to_hover' => array( '<strong>The Doctor</strong>' => 'The man from Gallifrey' ) ) );

		$this->assertEquals(
			"Have you met <acronym class='c2c-text-hover' title='The man from Gallifrey'><strong>The Doctor</strong></acronym>?",
			$this->text_hover( 'Have you met <strong>The Doctor</strong>?' )
		);
	}

	public function test_treats_single_angle_bracket_as_text() {
		$this->set_option( array( 'text_to_hover' => array( 'I <3 dogs' => 'Mostly boxers and pit bulls' ) ) );

		$this->assertEquals(
			'<a href="#" title="I <3 dogs">Did you know <acronym class=\'c2c-text-hover\' title=\'Mostly boxers and pit bulls\'>I <3 dogs</acronym>?</a>',
			$this->text_hover( '<a href="#" title="I <3 dogs">Did you know I <3 dogs?</a>' )
		);
	}

	public function test_does_not_hover_substrings() {
		$this->assertEquals( 'xcoffee2code',  $this->text_hover( 'xcoffee2code' ) );
		$this->assertEquals( 'ycoffee2codey', $this->text_hover( 'ycoffee2codey' ) );
		$this->assertEquals( 'coffee2codez',  $this->text_hover( 'coffee2codez' ) );
	}

	public function test_empty_hover_does_nothing() {
		$this->assertEquals( 'blank', $this->text_hover( 'blank' ) );
	}

	public function test_hovers_text_via_filter_get_comment_text() {
		$this->set_option( array( 'text_hover_comments' => true ) );
		$text = "This is a multi-line\npiece of text that end\nwith a multibyte character ";

		$comment_id = $this->factory->comment->create( array( 'comment_content' => $text . '水' ) );

		$this->assertEquals(
			wpautop( $text . $this->text_hover( '水' ) ),
			apply_filters( 'comment_text', get_comment_text( $comment_id ), get_comment( $comment_id ) )
		);
	}

	public function test_hovers_with_case_sensitivity_by_default() {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( $expected,     $this->text_hover( 'coffee2code' ) );
		$this->assertEquals( 'Coffee2code', $this->text_hover( 'Coffee2code' ) );
		$this->assertEquals( 'COFFEE2CODE', $this->text_hover( 'COFFEE2CODE' ) );
	}

	public function test_hovers_once_via_setting() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hovers_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertEquals( "$expected coffee2code coffee2code", $this->text_hover( 'coffee2code coffee2code coffee2code' ) );
	}

	public function test_hovers_once_via_filter() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hovers_single_term_multiple_times();
		add_filter( 'c2c_text_hover_once', '__return_true' );

		$this->assertEquals( "$expected coffee2code coffee2code", $this->text_hover( 'coffee2code coffee2code coffee2code' ) );
	}

	public function test_hovers_multibyte_once_via_filter() {
		$expected = $this->expected_text( '水' );
		$this->test_hovers_single_multibyte_term_multiple_times();
		add_filter( 'c2c_text_hover_once', '__return_true' );

		$this->assertEquals( "$expected 水 水", $this->text_hover( '水 水 水' ) );
	}

	public function test_hovers_with_case_insensitivity_via_setting() {
		$this->test_hovers_with_case_sensitivity_by_default();
		$this->set_option( array( 'case_sensitive' => false ) );

		$this->assertEquals( $this->expected_text( 'coffee2code' ), $this->text_hover( 'coffee2code' ) );
		$this->assertEquals( $this->expected_text( 'Coffee2code' ), $this->text_hover( 'Coffee2code' ) );
		$this->assertEquals( $this->expected_text( 'COFFEE2CODE' ), $this->text_hover( 'COFFEE2CODE' ) );
	}

	public function test_hovers_with_case_insensitivity_via_filter() {
		$this->test_hovers_with_case_sensitivity_by_default();
		add_filter( 'c2c_text_hover_case_sensitive', '__return_false' );

		$this->assertEquals( $this->expected_text( 'coffee2code' ), $this->text_hover( 'coffee2code' ) );
		$this->assertEquals( $this->expected_text( 'Coffee2code' ), $this->text_hover( 'Coffee2code' ) );
		$this->assertEquals( $this->expected_text( 'COFFEE2CODE' ), $this->text_hover( 'COFFEE2CODE' ) );
	}

	public function test_hovers_term_added_via_filter() {
		$this->assertEquals( 'bbPress', $this->text_hover( 'bbPress' ) );
		$expected = "<acronym class='c2c-text-hover' title='Forum Software'>bbPress</acronym>";
		add_filter( 'c2c_text_hover', array( $this, 'add_text_to_hover' ) );

		$this->assertEquals( $expected, $this->text_hover( 'bbPress' ) );
	}

	public function test_hover_does_not_apply_to_comments_by_default() {
		$this->assertEquals( 'coffee2code', apply_filters( 'get_comment_text', 'coffee2code' ) );
		$this->assertEquals( 'coffee2code', apply_filters( 'get_comment_excerpt', 'coffee2code' ) );
	}

	public function test_hover_applies_to_comments_via_setting() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hover_does_not_apply_to_comments_by_default();
		$this->set_option( array( 'text_hover_comments' => true ) );

		$this->assertEquals( $expected, apply_filters( 'get_comment_text', 'coffee2code' ) );
		$this->assertEquals( $expected, apply_filters( 'get_comment_excerpt', 'coffee2code' ) );
	}

	public function test_hover_applies_to_comments_via_filter() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hover_does_not_apply_to_comments_by_default();
		add_filter( 'c2c_text_hover_comments', '__return_true' );

		$this->assertEquals( $expected, apply_filters( 'get_comment_text', 'coffee2code' ) );
		$this->assertEquals( $expected, apply_filters( 'get_comment_excerpt', 'coffee2code' ) );
	}

	/**
	 * @dataProvider get_default_filters
	 */
	public function test_hover_applies_to_default_filters( $filter ) {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertNotFalse( has_filter( $filter, array( c2c_TextHover::get_instance(), 'text_hover' ), 3 ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a coffee2code' ), $expected ) );
	}

	/**
	 * @dataProvider get_comment_filters
	 */
	public function test_hover_applies_to_comment_filters( $filter ) {
		$expected = $this->expected_text( 'coffee2code' );

		add_filter( 'c2c_text_hover_comments', '__return_true' );

		$this->assertNotFalse( has_filter( $filter, array( c2c_TextHover::get_instance(), 'text_hover_comment_text' ), 11 ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a coffee2code' ), $expected ) );
	}

	public function test_hover_applies_to_custom_filter_via_filter() {
		$this->assertEquals( 'coffee2code', apply_filters( 'custom_filter', 'coffee2code' ) );

		add_filter( 'c2c_text_hover_filters', array( $this, 'add_custom_filter' ) );

		c2c_TextHover::get_instance()->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( 'coffee2code' ), apply_filters( 'custom_filter', 'coffee2code' ) );
	}

	public function test_uninstall_deletes_option() {
		$option = 'c2c_text_hover';
		c2c_TextHover::get_instance()->get_options();

		$this->assertNotFalse( get_option( $option ) );

		c2c_TextHover::uninstall();

		$this->assertFalse( get_option( $option ) );
	}

}
