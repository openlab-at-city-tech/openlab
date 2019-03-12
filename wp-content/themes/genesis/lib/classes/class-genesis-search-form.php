<?php
/**
 * Genesis Framework
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * @package  StudioPress\Genesis
 * @author   StudioPress
 * @license  GPL-2.0-or-later
 * @link     https://my.studiopress.com/themes/genesis/
 */

/**
 * Search form class.
 *
 * @since 2.7.0
 *
 * @link https://gist.github.com/cdils/caa151461a2d494dc85ed860cedcd503
 */
class Genesis_Search_Form {

	/**
	 * Unique ID for this search field.
	 *
	 * @var string
	 */
	protected $unique_id;

	/**
	 * Holds form strings.
	 *
	 * @var array
	 */
	protected $strings;

	/**
	 * Constructor.
	 *
	 * @since 2.7.0
	 *
	 * @param array $strings Optional. Array of strings. Default is an empty array.
	 */
	public function __construct( array $strings = array() ) {

		$default_strings = array(
			'label'        => __( 'Search site', 'genesis' ),
			'placeholder'  => '',
			'input_value'  => apply_filters( 'the_search_query', get_search_query() ),
			'submit_value' => __( 'Search', 'genesis' ),
		);

		$this->strings = array_merge( $default_strings, $strings );

		$this->unique_id = 'searchform-' . uniqid( '', true );

	}

	/**
	 * Return markup.
	 *
	 * @since 2.7.0
	 */
	protected function markup( $args ) {
		$args = array_merge(
			$args,
			array(
				'echo' => false,
			)
		);

		return genesis_markup( $args );
	}

	/**
	 * Render the search form.
	 *
	 * @since 2.7.0
	 */
	public function render() {
		echo $this->get_form();
	}

	/**
	 * Get form markup.
	 *
	 * @since 1.0.0
	 *
	 * @return string Form markup.
	 */
	public function get_form() {

		return $this->markup(
			array(
				'open'    => '<form %s>',
				'close'   => '</form>',
				'content' => $this->get_label() . $this->get_input() . $this->get_submit(),
				'context' => 'search-form',
			)
		);

	}

	/**
	 * Get label markup.
	 *
	 * @since 1.0.0
	 *
	 * @return string Label markup.
	 */
	protected function get_label() {
		return $this->markup(
			array(
				'open'    => '<label %s>',
				'close'   => '</label>',
				'content' => $this->strings['label'],
				'context' => 'search-form-label',
				'params'  => array(
					'input_id' => $this->get_input_id()
				),
			)
		);
	}

	/**
	 * Get input markup.
	 *
	 * @since 1.0.0
	 *
	 * @return string Input field markup.
	 */
	protected function get_input() {
		return $this->markup(
			array(
				'open'    => '<input %s>',
				'context' => 'search-form-input',
				'params'  => array(
					'id'          => $this->get_input_id(),
					'value'       => $this->strings['input_value'],
					'placeholder' => $this->strings['placeholder'],
				),
			)
		);
	}

	/**
	 * Get submit button markup.
	 *
	 * @since 2.7.0
	 *
	 * @return string Submit button markup.
	 */
	protected function get_submit() {
		return $this->markup(
			array(
				'open'    => '<input %s>',
				'context' => 'search-form-submit',
				'params'  => array(
					'value' => $this->strings['submit_value'],
				),
			)
		);
	}

	/**
	 * Get a unique ID for the search input.
	 *
	 * @since 2.7.0
	 *
	 * @return string Unique ID.
	 */
	protected function get_input_id() {
		return $this->unique_id;
	}
}
