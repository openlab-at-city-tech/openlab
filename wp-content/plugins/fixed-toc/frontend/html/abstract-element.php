<?php
/**
 * Define an element
 *
 * @since 3.0.0
 */
abstract class Fixedtoc_Element {
	/**
	 * A tag name.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var string
	 */	
	protected $tagname;

	/**
	 *Ccontent.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var string
	 */	
	protected $content;
	
	/**
	 * An array of attributes
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */	
	protected $attrs = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $data Data of TOC
	 */
	public function __construct() {
		$this->set_tagname();
		$this->set_content();
		$this->set_attrs();
	}
	
	/**
	 * Set a tag name.
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @return void
	 */
	abstract protected function set_tagname();

	/**
	 * Set a content.
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function set_content() {}
	
	/**
	 * Set attributes.
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function set_attrs() {}

	/**
	 * Get the tag name.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_tagname() {
		return $this->tagname;
	}	

	/**
	 * Get the Content.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}	

	/**
	 * Get the array of the attributes.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_attrs() {
		return $this->attrs;
	}
}