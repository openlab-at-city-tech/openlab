<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');

/**
 * Profile table sorter
 *
 * @author GoDaddy.com
 * @version 1.0
 * @package P3_Profiler
 */
class P3_Profiler_Table_Sorter {

	/**
	 * The field name to sort by
	 * @var string
	 */
	private $field = null;

	/**
	 * The data to sort
	 * @var array
	 */
	private $data = null;

	/**
	 * Constructor.
	 * @param array $data
	 * @param string $field Default is 'name'
	 */
	public function __construct( array $data, $field = 'name' ) {
		$this->data  = $data;
		$this->field = $field;
	}

	/**
	 * Sort the data in 'asc' or 'desc' directions
	 * @param string $direction Default is 'asc'
	 * @return array
	 */
	public function sort( $direction = 'asc' ) {
		usort( $this->data, array( $this, '_compare' ) );
		return ( 'asc' == $direction ) ? $this->data : array_reverse( $this->data );
	}
	
	/**
	 * Compare the data
	 * @link http://us.php.net/usort
	 * @param type $a
	 * @param type $b
	 * @return bool
	 */
	private function _compare( $a, $b ) {
		if ( in_array( $this->field, array( '_filesize', '_count', '_date' ) ) ) {
			return $a[$this->field] < $b[$this->field];
		} else {
			return strcmp( $a[$this->field], $b[$this->field] );
		}
	}
}
