<?php
/**
 * Add shortcode feature.
 *  
 * @since 3.1.0
 */
class Fixedtoc_Shortcode {
	/**
	 * An object of TOC.
	 * 
	 * @since 3.1.0
	 * @access private
	 * 
	 * @var object
	 */
	private $obj_toc;
	
	/**
	 * Indicate if the shortcode has excuted once.
	 * 
	 * @since 3.1.0
	 * @access private
	 * 
	 * @var boolean
	 */
	private static $shortcoded_once = false;

	/**
	 * Constructor
	 * 
	 * @since 3.1.0
	 * @access public
	 * 
	 * @param Fixedtoc_Dom $obj_toc
	 */
	public function __construct( Fixedtoc_Dom $obj_toc ) {
		$this->obj_toc = $obj_toc;
		
		add_shortcode( 'toc', array( $this, 'to_toc' ) );
	}

	/**
	 * A callback function of the [toc] shortcode.
	 * 
	 * @since 3.1.0
	 * @access public
	 * 
	 * @param array $attrs
	 * @param string $content
	 * @param string $tag
	 * @return string(null)
	 */
	public function to_toc( $attrs, $content, $tag ) {
		if ( ! self::$shortcoded_once) {
			self::$shortcoded_once= true;
			return $this->obj_toc->get_html();
		}
	}

}