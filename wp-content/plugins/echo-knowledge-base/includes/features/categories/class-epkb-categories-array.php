<?php

/**
 * Handle manipulation of tree of IDs
 *
 *  [cat term id] -> []
 *  [cat term id] -> []
 *  [cat term id]
 *     ->  [sub-cat term id] -> []
 *     ->  [sub-cat term id]
 *             -> [sub-sub-cat term id] -> []
 *     ->  [sub-cat term id] -> []
 *
 */
class EPKB_Categories_Array {

	public $ids_array;

	public function __construct( array $cat_sequences ) {
		$this->ids_array = $cat_sequences;
		$this->normalize_and_sanitize();
	}

	public function normalize_and_sanitize() {
		if ( empty($this->ids_array) || ! is_array($this->ids_array) ) {
			$this->ids_array = array();
		}
		$this->normalize_recursive( $this->ids_array );
	}

	private function normalize_recursive( &$array, &$level=0 ) {
		$level++;
		foreach ($array as $key => &$value) {

			if ( ! EPKB_Utilities::is_positive_int( $key ) ||
			     ( ! empty($value) && ! is_array($value) ) ) {
				unset($array[$key]);
				continue;
			}

			if ( is_array($value) ) {
				if ( $level < 7 ) {
					$this->normalize_recursive( $value, $level );
					$level--;
				} else {
					unset($array[$key]);
				}
			}
		}
	}

	public function get_all_keys() {
		$keys = array();
		$this->get_all_keys_recursive( $this->ids_array, $keys );
		return $keys;
	}

	private function get_all_keys_recursive( $array, &$keys, &$level=0 ) {
		$level++;
		foreach ($array as $key => $value) {
			$keys[$key] = $level;
			if ( is_array($value) ) {
				if ( $level < 7 && ! empty($array) ) {
					$this->get_all_keys_recursive( $value, $keys, $level );
					$level--;
				}
			}
		}
		return $keys;
	}

	public function get_all_keys_keep_order() {
		$keys = array();
		$this->get_all_keys_keep_order_recursive( $this->ids_array, $keys );
		return $keys;
	}

	private function get_all_keys_keep_order_recursive( $array, &$keys, &$level=0 ) {
		$level++;
		foreach ($array as $key => $value) {
			$keys[] = array($key => $level);
			if ( is_array($value) ) {
				if ( $level < 7 && ! empty($array) ) {
					$this->get_all_keys_keep_order_recursive( $value, $keys, $level );
					$level--;
				}
			}
		}
		return $keys;
	}

	public function get_all_leafs() {
		$keys = array();
		$this->get_all_leafs_recursive( $this->ids_array, $keys );
		return $keys;
	}

	private function get_all_leafs_recursive( $array, &$keys, &$level=0 ) {
		$level++;
		foreach ($array as $key => $value) {
			if ( ! empty($value) ) {
				if ( $level < 7 ) {
					$this->get_all_leafs_recursive( $value, $keys, $level );
					$level--;
				}
			} else {
				$keys[] = $key;
			}
		}
		return $keys;
	}

	public function &get_parent_category_reference( $parent_category_id, $parent_level ) {

		// LEVEL 0
		if ( $parent_level == 0 ) {
			return $this->ids_array;
		}

		// LEVEL 1
		$null_array = null;
		foreach ( $this->ids_array as $category_id => $sub_categories ) {
			// if we are on the level then see if we found the category
			if ( $parent_level == 1 ) {
				if ( $parent_category_id == $category_id ) {
					return $this->ids_array;
				}
				continue;
			}

			// next level 2
			if ( ! is_array( $sub_categories ) ) {
				return $null_array;
			}

			if ( empty($sub_categories) ) {
				continue;
			}

			// LEVEL 2
			$null_array = null;
			foreach ( $sub_categories as $sub_category_id => $sub_sub_categories ) {
				// if we are on the level then see if we found the category
				if ( $parent_level == 2 ) {
					if ( $parent_category_id == $sub_category_id && isset($this->ids_array[$category_id])) {
						return $this->ids_array[$category_id];
					}
					continue;
				}

				// next level 3
				if ( ! is_array( $sub_sub_categories ) ) {
					return $null_array;
				}

				if ( empty($sub_sub_categories) ) {
					continue;
				}

				// LEVEL 3
				$null_array = null;
				foreach ( $sub_sub_categories as $sub_sub_category_id => $sub_sub_sub_categories ) {
					// if we are on the level then see if we found the category
					if ( $parent_level == 3 ) {
						if ( $parent_category_id == $sub_sub_category_id && isset( $this->ids_array[$category_id][$sub_category_id] ) ) {
							return $this->ids_array[$category_id][$sub_category_id];
						}
						continue;
					}

					// next level 4
					if ( ! is_array( $sub_sub_sub_categories ) ) {
						return $null_array;
					}

					if ( empty($sub_sub_sub_categories) ) {
						continue;
					}

					// LEVEL 4
					$null_array = null;
					foreach ( $sub_sub_sub_categories as $sub_sub_sub_category_id => $sub_sub_sub_sub_categories ) {
						// if we are on the level then see if we found the category
						if ( $parent_level == 4 ) {
							if ( $parent_category_id == $sub_sub_sub_category_id && isset( $this->ids_array[$category_id][$sub_category_id][$sub_sub_category_id] ) ) {
								return $this->ids_array[$category_id][$sub_category_id][$sub_sub_category_id];
							}
							continue;
						}
						
						// next level 5
						if ( ! is_array( $sub_sub_sub_sub_categories ) ) {
							return $null_array;
						}

						if ( empty($sub_sub_sub_sub_categories) ) {
							continue;
						}
						
						// LEVEL 5
						$null_array = null;
						
						foreach ( $sub_sub_sub_sub_categories as $sub_sub_sub_sub_category_id => $sub_sub_sub_sub_sub_categories ) {
							// if we are on the level then see if we found the category
							if ( $parent_level == 5 ) {
								if ( $parent_category_id == $sub_sub_sub_sub_category_id && isset( $this->ids_array[$category_id][$sub_category_id][$sub_sub_category_id][$sub_sub_sub_category_id] ) ) {
									return $this->ids_array[$category_id][$sub_category_id][$sub_sub_category_id][$sub_sub_sub_category_id];
								}
								continue;
							}
						} // for level 5
					} // for - level 4
				} // for - level 3
			} // for - level 2
		} // for - level 1

		// did not find it
		return $null_array;
	}
}