<?php
/**
 * Generate a data array from post content.
 *
 * @since 3.0.0
 */

class Fixedtoc_Data {
	/**
	 * An array of all of the matches.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	private $matches = array();

	/**
	 * The number of matches.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var int
	 */
	private $matches_num = 0;
	
	/**
	 * The current matched string.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	private $match = '';
	
	/**
	 * Data array.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	private $data = array();

	/**
	 * Current indext in the self::$data.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var number
	 */
	private $index = 0;
	
	/**
	 * Current datum in the self::$data.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var number
	 */
	private $datum = array();

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $content the post content.
	 */
	public function __construct( $content ) {
		$headings = fixedtoc_get_val( 'general_h_tags' );
		if ( $headings ) {
			$h = implode( '|', $headings );
			$preg = '/\<(' . $h . ').*?>.+?\<\/(' . $h . ')\>/is';
			$this->matches_num = preg_match_all( $preg, $content, $this->matches );
		}
	}

	/**
	 * Has matches include headings or not
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool.
	 */
	public function has_matches() {
		return (bool) $this->matches_num;
	}
	
	/**
	 * Create a data
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $objs_datum An array of Fixedtoc_Datum instances
	 * @return void.
	 */	
	public function create_data( $objs_datum ) {
		foreach ( $this->matches[0] as $match ) {
			$this->match = $match;
			foreach ( $objs_datum as $obj_datum ) {
				$this->add_datum( $obj_datum );
				
				// delete the datum and continue current loop if the title is empty.
				if ( isset( $this->data[ $this->index ]['origin_title'] ) && empty( $this->data[ $this->index ]['origin_title'] ) ) {
					unset( $this->data[ $this->index ] );
					continue 2;
				}
			}

			$this->index++;
		}
	}

	/**
	 * Add a datum.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $datum an instance of Fixedtoc_Datum.
	 * @return void.
	 */
	private function add_datum( Fixedtoc_Datum $datum ) {
		$datum->set_name( $this );
		$datum->set_value( $this );
		$key = $datum->get_name();
		$value = $datum->get_value();
		
		$this->datum[$key] = $value;
		$this->data[ $this->index ][ $key ] = $value;
	}

	/**
	 * Get the current matched string.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string.
	 */
	public function get_match() {
		return $this->match;
	}
	
	/**
	 * Get the cuttent datum index.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return int.
	 */
	public function get_index() {
		return $this->index;
	}
	
	/**
	 * Get the cuttent datum.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array.
	 */
	public function get_datum() {
		return $this->datum;
	}

	/**
	 * Get the data.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array.
	 */	
	public function get_data() {
		return $this->data;
	}
	
}