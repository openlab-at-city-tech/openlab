<?php

defined( 'ABSPATH' ) or die();

class Text_Hover_Test extends WP_UnitTestCase {

	protected $obj;

	protected $captured_filter_value = array();

	protected static $text_to_hover = array(
		'WP Tavern'      => 'Site for WordPress-related news',
		'WP'             => 'WordPress',
		'WP COM'         => 'WordPress.com',
		"coffee2code"    => 'Plugin developer',
		'Matt Mullenweg' => 'Co-Founder of WordPress',
		'blank'          => '',
		'C&C'            => 'Command & Control',
		'漢字はユニコード'  => 'Kanji Unicode',
		'HTML'           => '<strong>HTML</strong>',
		'水'             => 'water',
		'<em>Test</em>'  => 'This is text associated with a replacement that includes HTML tags.',
		'piñata'         => 'Full of candy',
		'Kónståntîn český คำถาม 問題和答案 Поделитьс' => 'lots of special characters',
		'#something'     => 'blablabla',
		'&anotherthing'  => 'thisandthat',
		'@coffee2code'   => 'My Twitter handle',
		'damn!'          => 'darn.',
		'100%'           => '99+%',
		':colon:'        => 'bookended with colons',
		'_unknown'       => 'underscore unknown',
		'highlight'      => 'This <em>should</em> get rendered in most cases.',
		'empty'          => '',
		'false'          => false,
	);

	public static function setUpBeforeClass() {
		c2c_TextHover::get_instance()->install();

		add_role(
			'manage_options_but_no_unfiltered_html',
			'Admin without unfiltered HTML',
			array(
				'manage_options'  => true,
				'unfiltered_html' => false,
			)
		);
	}

	public function setUp() {
		parent::setUp();

		$this->obj = c2c_TextHover::get_instance();

		$this->set_option();
	}

	public function tearDown() {
		parent::tearDown();

		// Reset options
		$this->obj->reset_options();

		// Dequeue scripts and styles.
		wp_dequeue_script( 'qtip2' );
		wp_dequeue_script( 'text-hover' );
		wp_dequeue_style( 'qtip2' );
		wp_dequeue_style( 'text-hover' );

		$this->captured_filter_value = array();
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_filters() {
		$filters = [];
		foreach ( self::get_core_filters() as $filter ) {
			$filters[] = [ $filter ];
		}

		return $filters;
	}

	public static function get_comment_filters() {
		return array(
			array( 'get_comment_text' ),
			array( 'get_comment_excerpt' ),
		);
	}

	public static function get_third_party_filters() {
		$filters = [];
		foreach ( self::get_3rd_party_filters() as $filter ) {
			$filters[] = [ $filter ];
		}

		return $filters;
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


	protected static function get_core_filters() {
		return array( 'the_content', 'the_excerpt', 'widget_text' );
	}

	protected static function get_3rd_party_filters() {
		return array(
			'acf/format_value/type=text',
			'acf/format_value/type=textarea',
			'acf/format_value/type=url',
			'acf_the_content',
			'elementor/frontend/the_content',
			'elementor/widget/render_content',
		);
	}

	protected function text_hovers( $term = '' ) {
		$text_to_hover = self::$text_to_hover;

		if ( $term ) {
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
		$this->obj->update_option( $settings, true );
	}

	protected function text_hover( $text ) {
		return $this->obj->text_hover( $text );
	}

	/**
	 * @param string $display_term Term that should be in link if it doesn't match $term.
	 */
	protected function expected_text( $term, $display_term = '' ) {
		$hover_text = $this->text_hovers( $term );
		if ( ! $hover_text ) {
			$hover_text = $this->text_hovers( strtolower( $term ) );
		}
		if ( ! $hover_text ) {
			return $term;
		}
		if ( $display_term ) {
			$term = $display_term;
		}
		return "<abbr class='c2c-text-hover' title='" . esc_attr( $hover_text ) . "'>$term</abbr>";
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

	protected function get_filter_names() {
		return array_map(
			function ( $x ) { return reset( $x ); },
			array_merge(
				$this->get_third_party_filters(),
				$this->get_default_filters()
			)
		);
	}

	public function unhook_default_filters( $priority = 3 ) {
		$filters = $this->get_filter_names();

		// Unhook filters.
		foreach ( $filters as $filter ) {
			remove_filter( $filter, array( $this->obj, 'text_hover' ), $priority );
		}
	}

	public function c2c_text_hover_filter_priority( $priority, $filter = '' ) {
		return ( 'filter_20' === $filter ) ? 20 : 11;
	}

	public function capture_filter_value( $value ) {
		return $this->captured_filter_value[ current_filter() ] = $value;
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
		$this->assertTrue( class_exists( 'c2c_Plugin_064' ) );
	}

	public function test_plugin_framework_version() {
		$this->assertEquals( '064', $this->obj->c2c_plugin_version() );
	}

	public function test_version() {
		$this->assertEquals( '4.2', $this->obj->version() );
	}

	public function test_instance_object_is_returned() {
		$this->assertTrue( is_a( $this->obj, 'c2c_TextHover' ) );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_TextHover', 'get_instance' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_text_hover', c2c_TextHover::SETTING_NAME );
	}

	/*
	 * Setting defaults.
	 */

	public function test_default_value_of_text_to_replace() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$expected = array(
			'WP' => "WordPress",
		);

		$this->assertEquals( $expected, $options['text_to_hover'] );
	}

	public function test_default_value_of_text_hover_comments() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertFalse( $options['text_hover_comments'] );
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

	public function test_default_value_of_use_pretty_tooltips() {
		$this->obj->reset_options();
		$options = $this->obj->get_options();

		$this->assertTrue( $options['use_pretty_tooltips'] );
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
	 * Text hovers
	 */

	public function test_no_text_change_when_no_hovers_defined() {
		$text = 'This is a 2019 test.';

		$this->set_option( array( 'text_to_hover' => '' ) );

		$this->assertEquals( $text, $this->text_hover( $text ) );

		$this->set_option( array( 'text_to_hover' => $this->text_hovers() ) );
	}

	public function test_no_hover_when_hover_string_is_falsey() {
		$this->assertEquals( 'empty', $this->text_hover( 'empty' ) );
		$this->assertEquals( 'false', $this->text_hover( 'false' ) );
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
			"<abbr class='c2c-text-hover' title='&lt;strong&gt;HTML&lt;/strong&gt;'>HTML</abbr>",
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
		$text = array(
			'<a href="http://example.com" title="Learn the 水 character">link</a>',
			'<a class="fusion-button button-flat button-round button-small button-green button-1" target="_self" href="https://example.com/coffee2code-login.php"><span class="fusion-button-icon-divider button-icon-divider-left"><i class="fa fa-unlock"></i></span><span class="fusion-button-text fusion-button-text-left">Login</span></a>'
		);

		foreach ( $text as $t ) {
			$this->assertEquals(
				$t,
				$this->text_hover( $t )
			);
		}
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
			"Have you met <abbr class='c2c-text-hover' title='The man from Gallifrey'><strong>The Doctor</strong></abbr>?",
			$this->text_hover( 'Have you met <strong>The Doctor</strong>?' )
		);
	}

	public function test_treats_single_angle_bracket_as_text() {
		$this->set_option( array( 'text_to_hover' => array( 'I <3 dogs' => 'Mostly boxers and pit bulls' ) ) );

		$this->assertEquals(
			'<a href="#" title="I <3 dogs">Did you know <abbr class=\'c2c-text-hover\' title=\'Mostly boxers and pit bulls\'>I <3 dogs</abbr>?</a>',
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

	public function test_does_not_replace_within_markup_attributes() {
		$expected = '<a href="http://test.com" title="A coffee2code site">gibberish</a>';

		$this->assertEquals( $expected, $this->text_hover( $expected ) );
	}

	public function test_does_not_replace_within_markup_attributes_but_does_between_tags() {
		$text = '<span title="A coffee2code endeavor">the coffee2code project</a>';
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( '<span title="A coffee2code endeavor">the ' . $expected . ' project</a>', $this->text_hover( $text ) );
	}

	public function test_does_not_replace_within_abbr_content() {
		$expected = '<abbr title="A coffee2code endeavor">the coffee2code project</abbr>';

		$this->assertEquals( $expected, $this->text_hover( $expected ) );
	}

	public function test_hovers_with_case_sensitivity_by_default() {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( $expected,     $this->text_hover( 'coffee2code' ) );
		$this->assertEquals( 'Coffee2code', $this->text_hover( 'Coffee2code' ) );
		$this->assertEquals( 'COFFEE2CODE', $this->text_hover( 'COFFEE2CODE' ) );
	}

	/*
	 * With 'WP Tavern' followed by 'WP' as hover defines, the former should not
	 * hand the latter's hover applied to it.
	 */
	public function test_does_not_hover_a_general_term_that_is_included_in_earlier_listed_term() {
		$string = 'WP Tavern';

		$this->assertEquals( $this->expected_text( $string ), $this->text_hover( $string ) );
	}

	/**
	 * Ensure a more specific string matches with priority over a less specific
	 * string, regardless of what order they were defined.
	 *
	 *  MAYBE! Not sure if this is desired. But the theory is if both
	 * "WP" and "WP COM" are defined, then the text latter should get
	 * hovered, even though the former was defined first.
	 */
	public function test_does_not_hover_a_more_general_term_when_general_is_first() {
		$expected = $this->expected_text( 'WP COM' );

		$this->assertEquals( "This $expected is true", $this->text_hover( 'This WP COM is true' ) );
	}

	public function test_hovers_term_split_across_multiple_lines() {
		$expected = array(
			"Did you see " . $this->expected_text( 'Matt Mullenweg', "Matt\nMullenweg" ) . " at the party?"
				=> $this->text_hover( "Did you see Matt\nMullenweg at the party?" ),
			"Did you see " . $this->expected_text( 'Matt Mullenweg', 'Matt  Mullenweg' ) . " at the party?"
				=> $this->text_hover( "Did you see Matt  Mullenweg at the party?" ),
			"Did you see " . $this->expected_text( 'Kónståntîn český คำถาม 問題和答案 Поделитьс', "Kónståntîn\nčeský\tคำถาม\n\t問題和答案   Поделитьс" ) . " at the party?"
				=> $this->text_hover( "Did you see Kónståntîn\nčeský\tคำถาม\n\t問題和答案   Поделитьс at the party?" ),
		);

		foreach ( $expected as $expect => $actual ) {
			$this->assertEquals( $expect, $actual );
		}
	}

	public function test_hovers_search_strings_start_or_end_with_special_characters() {
		$linked = $this->expected_text( '#something' );
		$linked2 = $this->expected_text( 'damn!' );

		$expected = array(
			"i $linked" => $this->text_hover( 'i #something' ),
			"$linked incinerate" => $this->text_hover( '#something incinerate' ),
			"well hot $linked2" => $this->text_hover( 'well hot damn!' ),
			"$linked2$linked" => $this->text_hover( 'damn!#something' ),
		);

		foreach ( $expected as $expect => $actual ) {
			$this->assertEquals( $expect, $actual );
		}
	}

	public function test_hovers_multibyte_text_once_via_setting() {
		$linked = $this->expected_text( '漢字はユニコード' );

		$this->set_option( array( 'replace_once' => true ) );

		$expected = array(
			"$linked cat 漢字はユニコード cat 漢字はユニコード"
				=> $this->text_hover( '漢字はユニコード cat 漢字はユニコード cat 漢字はユニコード' ),
			'dock ' . $linked . ' cart 漢字はユニコード'
				=> $this->text_hover( 'dock 漢字はユニコード cart 漢字はユニコード' ),
		);

		foreach ( $expected as $expect => $actual ) {
			$this->assertEquals( $expect, $actual );
		}
	}

	public function test_hovers_once_via_setting() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hovers_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertEquals( "$expected coffee2code coffee2code", $this->text_hover( 'coffee2code coffee2code coffee2code' ) );
	}

	public function test_hovers_once_via_trueish_setting_value() {
		$expected = $this->expected_text( 'coffee2code' );
		$this->test_hovers_single_term_multiple_times();
		$this->set_option( array( 'replace_once' => '1' ) );

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
		$expected = "<abbr class='c2c-text-hover' title='Forum Software'>bbPress</abbr>";
		add_filter( 'c2c_text_hover', array( $this, 'add_text_to_hover' ) );

		$this->assertEquals( $expected, $this->text_hover( 'bbPress' ) );
	}

	public function test_hovers_filter_added_via_more_filters() {
		$filter = 'custom_filter';
		$this->assertEquals( 'WP', apply_filters( $filter, 'WP' ) );

		$expected = "<abbr class='c2c-text-hover' title='WordPress'>WP</abbr>";

		$this->set_option( array( 'more_filters' => array( $filter ) ) );
		$this->obj->register_filters();

		$this->assertEquals( 3, has_filter( $filter, array( $this->obj, 'text_hover' ) ) );
		$this->assertEquals( $expected, apply_filters( $filter, 'WP' ) );
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
	public function test_hover_applies_to_default_filters( $filter, $priority = 3 ) {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( $priority, has_filter( $filter, array( $this->obj, 'text_hover' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a coffee2code' ), $expected ) );
	}

	/**
	 * @dataProvider get_comment_filters
	 */
	public function test_hover_applies_to_comment_filters( $filter ) {
		$expected = $this->expected_text( 'coffee2code' );

		add_filter( 'c2c_text_hover_comments', '__return_true' );

		$this->assertEquals( 11, has_filter( $filter, array( $this->obj, 'text_hover_comment_text' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a coffee2code' ), $expected ) );
	}

	public function test_disallowed_markup_is_stripped() {
		$orig_text_to_hover = self::$text_to_hover;
		self::$text_to_hover['xss'] = '<script>alert(1);</script> Hi';
		self::$text_to_hover['myspan'] = 'This has <span>text in a span</span>.';

		$this->set_option();

		$this->assertEquals(
			"<abbr class='c2c-text-hover' title='alert(1); Hi'>xss</abbr>",
			$this->text_hover( 'xss' )
		);
		$this->assertEquals(
			"<abbr class='c2c-text-hover' title='This has text in a span.'>myspan</abbr>",
			$this->text_hover( 'myspan' )
		);

		self::$text_to_hover = $orig_text_to_hover;
	}

	/**
	 * @dataProvider get_third_party_filters
	 */
	public function test_hover_applies_to_third_party_filters( $filter ) {
		$expected = $this->expected_text( 'coffee2code' );

		$this->assertEquals( 3, has_filter( $filter, array( $this->obj, 'text_hover' ) ) );
		$this->assertGreaterThan( 0, strpos( apply_filters( $filter, 'a coffee2code' ), $expected ) );
	}

	public function test_third_party_filters_are_part_of_c2c_text_hover_filters() {
		$filters = array_map(
			function ( $x ) { return reset( $x ); },
			array_merge(
				$this->get_third_party_filters(),
				$this->get_default_filters()
			)
		 );

		add_filter( 'c2c_text_hover_filters', array( $this, 'capture_filter_value' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertSame( $filters, $this->captured_filter_value[ 'c2c_text_hover_filters' ] );

		remove_filter( 'c2c_text_hover_filters', array( $this, 'capture_filter_value' ) );
	}

	public function test_hover_applies_to_custom_filter_via_filter() {
		$this->assertEquals( 'coffee2code', apply_filters( 'custom_filter', 'coffee2code' ) );

		add_filter( 'c2c_text_hover_filters', array( $this, 'add_custom_filter' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( 'coffee2code' ), apply_filters( 'custom_filter', 'coffee2code' ) );
	}

	public function test_hover_applies_to_custom_third_party_filter_via_filter() {
		$this->assertEquals( 'coffee2code', apply_filters( 'custom_filter', 'coffee2code' ) );

		add_filter( 'c2c_text_hover_third_party_filters', array( $this, 'add_custom_filter' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( $this->expected_text( 'coffee2code' ), apply_filters( 'custom_filter', 'coffee2code' ) );
	}

	/*
	 * filter: c2c_text_hover_filter_priority
	 */

	public function test_changing_priority_via_c2c_text_hover_filter_priority() {
		$filters = $this->get_filter_names();

		$this->unhook_default_filters();

		add_filter( 'c2c_text_hover_filter_priority', array( $this, 'c2c_text_hover_filter_priority' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$priority = 11;

		foreach ( $filters as $filter ) {
			$this->test_hover_applies_to_default_filters( $filter, $priority );
		}
	}

	public function test_default_priority_for_filter_c2c_text_hover_filter_priority_is_based_on_when_setting() {
		$this->unhook_default_filters();

		add_filter( 'c2c_text_hover_filter_priority', array( $this, 'capture_filter_value' ) );

		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( 3, $this->captured_filter_value[ 'c2c_text_hover_filter_priority' ] );

		$this->unhook_default_filters();
		$this->set_option( array( 'when' => 'late' ) );
		$this->obj->register_filters(); // Plugins would typically register their filter before this originally fires

		$this->assertEquals( 1000, $this->captured_filter_value[ 'c2c_text_hover_filter_priority' ] );

		$this->unhook_default_filters( 1000 );
	}

	/*
	 * enqueue_scripts()
	 */

	public function test_enqueue_scripts_default() {
		$this->obj->enqueue_scripts();

		$this->assertFalse( wp_script_is( 'qtip2', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'text-hover', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'qtip2', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'text-hover', 'enqueued' ) );
	}

	public function test_enqueue_scripts_when_pretty_tooltips_enabled_by_setting() {
		$this->set_option( array( 'use_pretty_tooltips' => true ) );
		$this->obj->enqueue_scripts();

		$this->assertTrue( wp_script_is( 'qtip2', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'text-hover', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'qtip2', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'text-hover', 'enqueued' ) );
	}

	public function test_enqueue_scripts_when_disabled() {
		$this->set_option( array( 'use_pretty_tooltips' => false ) );
		$this->obj->enqueue_scripts();

		$this->assertFalse( wp_script_is( 'qtip2', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'text-hover', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'qtip2', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'text-hover', 'enqueued' ) );
	}

	/*
	 * filter: c2c_text_hover_use_pretty_tooltips
	 */

	public function test_enqueue_scripts_when_pretty_tooltips_enabled_by_filter() {
		$this->set_option( array( 'use_pretty_tooltips' => false ) );
		add_filter( 'c2c_text_hover_use_pretty_tooltips', '__return_true' );
		$this->obj->enqueue_scripts();

		$this->assertTrue( wp_script_is( 'qtip2', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'text-hover', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'qtip2', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'text-hover', 'enqueued' ) );
	}

	/*
	 * get_default_filters()
	 */

	public function test_get_default_filters_default() {
		$this->assertEquals( self::get_core_filters(), $this->obj->get_default_filters() );
	}

	public function test_get_default_filters_empty_string() {
		$this->assertEquals( self::get_core_filters(), $this->obj->get_default_filters( '' ) );
	}

	public function test_get_default_filters_core() {
		$this->assertEquals( self::get_core_filters(), $this->obj->get_default_filters( 'core' ) );
	}

	public function test_get_default_filters_invalid() {
		$this->assertEmpty( $this->obj->get_default_filters( 'invalid' ) );
	}

	public function test_get_default_filters_third_party() {
		$filters = self::get_3rd_party_filters();

		$this->assertEquals( $filters, $this->obj->get_default_filters( 'third_party' ) );
	}

	public function test_get_default_filters_both() {
		$filters = self::get_3rd_party_filters();

		$this->assertEquals(
			array_merge( $this->get_core_filters(), $filters ),
			$this->obj->get_default_filters( 'both' )
		);
	}

	/*
	 * options_page_description()
	 */

	// Note: By no means a text of the full output of the function.
	public function test_options_page_description() {
		$expected = '<h1>Text Hover Settings</h1>' . "\n";
		$expected .= '<p class="see-help">See the "Help" link to the top-right of the page for more help.</p>' . "\n";
		$expected .= '<p>Text Hover is a plugin that allows you to add hover text (aka tooltips) to content in posts. Handy for providing explanations of names, terms, phrases, abbreviations, and acronyms.</p>';

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
		$option_name = c2c_TextHover::SETTING_NAME;
		// Get the options just to see if they may get saved.
		$options     = $this->obj->get_options();

		$this->assertFalse( get_option( $option_name ) );
	}
	*/

	public function test_uninstall_deletes_option() {
		$option_name = c2c_TextHover::SETTING_NAME;
		$options     = $this->obj->get_options();

		// Explicitly set an option to ensure options get saved to the database.
		$this->set_option( array( 'replace_once' => true ) );

		$this->assertNotEmpty( $options );
		$this->assertNotFalse( get_option( $option_name ) );

		c2c_TextHover::uninstall();

		$this->assertFalse( get_option( $option_name ) );
	}

}
