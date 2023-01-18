<?php
/**
 * WP_Style_Engine_CSS_Rule_Gutenberg
 *
 * An object for CSS rules.
 *
 * @package Gutenberg
 */

if ( class_exists( 'WP_Style_Engine_CSS_Rule_Gutenberg' ) ) {
	return;
}

/**
 * Holds, sanitizes, processes and prints CSS declarations for the Style Engine.
 *
 * @access private
 */
class WP_Style_Engine_CSS_Rule_Gutenberg {

	/**
	 * The selector.
	 *
	 * @var string
	 */
	protected $selector;

	/**
	 * The selector declarations.
	 *
	 * Contains a WP_Style_Engine_CSS_Declarations_Gutenberg object.
	 *
	 * @var WP_Style_Engine_CSS_Declarations_Gutenberg
	 */
	protected $declarations;

	/**
	 * Constructor
	 *
	 * @param string                                    $selector     The CSS selector.
	 * @param string[]|WP_Style_Engine_CSS_Declarations_Gutenberg $declarations An associative array of CSS definitions, e.g., array( "$property" => "$value", "$property" => "$value" ),
	 *                                                                or a WP_Style_Engine_CSS_Declarations_Gutenberg object.
	 */
	public function __construct( $selector = '', $declarations = array() ) {
		$this->set_selector( $selector );
		$this->add_declarations( $declarations );
	}

	/**
	 * Sets the selector.
	 *
	 * @param string $selector The CSS selector.
	 *
	 * @return WP_Style_Engine_CSS_Rule_Gutenberg Returns the object to allow chaining of methods.
	 */
	public function set_selector( $selector ) {
		$this->selector = $selector;
		return $this;
	}

	/**
	 * Sets the declarations.
	 *
	 * @param array|WP_Style_Engine_CSS_Declarations_Gutenberg $declarations An array of declarations (property => value pairs),
	 *                                                             or a WP_Style_Engine_CSS_Declarations_Gutenberg object.
	 *
	 * @return WP_Style_Engine_CSS_Rule_Gutenberg Returns the object to allow chaining of methods.
	 */
	public function add_declarations( $declarations ) {
		$is_declarations_object = ! is_array( $declarations );
		$declarations_array     = $is_declarations_object ? $declarations->get_declarations() : $declarations;

		if ( null === $this->declarations ) {
			if ( $is_declarations_object ) {
				$this->declarations = $declarations;
				return $this;
			}
			$this->declarations = new WP_Style_Engine_CSS_Declarations_Gutenberg( $declarations_array );
		}
		$this->declarations->add_declarations( $declarations_array );

		return $this;
	}

	/**
	 * Gets the declarations object.
	 *
	 * @return WP_Style_Engine_CSS_Declarations_Gutenberg The declarations object.
	 */
	public function get_declarations() {
		return $this->declarations;
	}

	/**
	 * Gets the full selector.
	 *
	 * @return string
	 */
	public function get_selector() {
		return $this->selector;
	}

	/**
	 * Gets the CSS.
	 *
	 * @param bool   $should_prettify Whether to add spacing, new lines and indents.
	 * @param number $indent_count    The number of tab indents to apply to the rule. Applies if `prettify` is `true`.
	 *
	 * @return string
	 */
	public function get_css( $should_prettify = false, $indent_count = 0 ) {
		$rule_indent         = $should_prettify ? str_repeat( "\t", $indent_count ) : '';
		$declarations_indent = $should_prettify ? $indent_count + 1 : 0;
		$suffix              = $should_prettify ? "\n" : '';
		$spacer              = $should_prettify ? ' ' : '';
		// Trims any multiple selectors strings.
		$selector         = $should_prettify ? implode( ',', array_map( 'trim', explode( ',', $this->get_selector() ) ) ) : $this->get_selector();
		$selector         = $should_prettify ? str_replace( array( ',' ), ",\n", $selector ) : $selector;
		$css_declarations = $this->declarations->get_declarations_string( $should_prettify, $declarations_indent );

		if ( empty( $css_declarations ) ) {
			return '';
		}

		return "{$rule_indent}{$selector}{$spacer}{{$suffix}{$css_declarations}{$suffix}{$rule_indent}}";
	}
}
