<?php

/**
 * Handle update of sequence in the database:
 *
 *  a) after user changes categories/articles while sort order is custom
 *  b) after user uses KB Config
 *
 */
class EPKB_KB_Config_Sequence {


	/********************************************************************************************************
	 *
	 *                 SYNC and UPDATE CUSTOM and NON-CUSTOM CATEGORIES/ARTICLES SEQUENCE
	 *
	 ********************************************************************************************************/

	/**
	 * If necessary update sequence of articles and save to the database
	 *
	 * @param $kb_id
	 * @param $new_kb_config
	 */
	public function update_articles_sequence( $kb_id, & $new_kb_config ) {

		$is_custom_sequence = $new_kb_config['articles_display_sequence'] == 'user-sequenced';
		if ( $is_custom_sequence ) {
			// update the sequence of articles based on user sequence
			$new_sequence = $this->get_new_sequence();
			if ( $new_sequence === false ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save article sequence', 'echo-knowledge-base' ) . ' (1)' );
			}

			$orig_articles_obj = $this->update_articles_order( $kb_id, $new_sequence );
			if ( $orig_articles_obj === false ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save article sequence', 'echo-knowledge-base' ) . ' (2)' );
			}

			$result = EPKB_Utilities::save_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, $orig_articles_obj->ids_array );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save article sequence', 'echo-knowledge-base' ) . ' (3)' );
			}
		} else {
			// update the sequence based on title or creation date
			$article_admin = new EPKB_Articles_Admin();
			$article_admin->update_articles_sequence( $kb_id );
		}
	}

	/**
	 * If necessary update sequence of categories and save to the database
	 *
	 * @param $kb_id
	 * @param $new_kb_config
	 */
	public function update_categories_sequence( $kb_id, & $new_kb_config ) {

		$is_custom_sequence = $new_kb_config['categories_display_sequence'] == 'user-sequenced';
		if ( $is_custom_sequence ) {
			// update the sequence of articles based on user actions
			$new_sequence = $this->get_new_sequence();
			if ( $new_sequence === false ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save category sequence. Please try again later.', 'echo-knowledge-base' ) . ' (4)');
			}

			// NOTE: Uncategorized (0) category will not be ordered.
			$orig_categories_obj = $this->update_categories_order( $kb_id, $new_sequence );
			if ( $orig_categories_obj === false ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save category sequence. Please try again later.', 'echo-knowledge-base' ) . ' (5)' );
			}

			$result = EPKB_Utilities::save_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, $orig_categories_obj->ids_array );
			if ( is_wp_error( $result ) ) {
				EPKB_Utilities::ajax_show_error_die(__( 'Could not save category sequence. Please try again later.', 'echo-knowledge-base' ) . ' (6)' );
			}
		} else {
			$category_admin = new EPKB_Categories_Admin();
			$category_taxonomy_slug = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
			$category_admin->update_categories_sequence( 0, 0, $category_taxonomy_slug );
		}
	}

	/**
	 * Retrieve new sequence passed in (categories + articles) and remove duplicate categories
	 *
	 * @return array|bool
	 */
	public function get_new_sequence() {

		$new_sequence = isset( $_POST['epkb_new_sequence'] ) ? $this->sanitize_sequence( wp_unslash( $_POST['epkb_new_sequence'] ) ) : false;
		if ( $new_sequence === false ) {
			return false;
		}

		// some layouts could have duplicate categories as we associated all sub-categories to that category
		// but now we just need the first association so remove the duplicates
		$last_category_id = 0;
		foreach( $new_sequence as $key => $item ) {
			if ( empty($item[0]) || empty($item[1]) ) {
				continue;
			}
			$item_id = $item[0];
			if ( $item[1] == 'category' ) {
				if ( $item_id == $last_category_id ) {
					unset( $new_sequence[ $key ] );
				}
				$last_category_id = $item_id;
			}
		}

		// for layouts with top level categories we need to arrange the sub-categories with its parents
		$top_cat_sequence = isset($_POST['top_cat_sequence']) ? $this->sanitize_top_cat_sequence( $_POST['top_cat_sequence'] ) : false;
		if ( ! empty($top_cat_sequence) && is_array($top_cat_sequence) ) {

			$top_category_new_sequence = array();
			foreach( $top_cat_sequence as $top_category_id ) {

				$found_top_category = false;
				foreach( $new_sequence as $key => $item ) {
					if ( empty( $item[0] ) || empty( $item[1] ) ) {
						continue;
					}

					$item_id = $item[0];
					$level = $item[1];

					if ( $top_category_id == $item_id ) {
						$top_category_new_sequence[] = array($item_id, $level);
						$found_top_category = true;
						continue;
					}

					// end if we found a different parent
					if ( $level == 'category' && $found_top_category ) {
						break;
					}

					// we found child
					if ( $found_top_category ) {
						$top_category_new_sequence[] = array($item_id, $level);
					}
				}

			}

			$new_sequence = empty($top_category_new_sequence) ? $new_sequence : $top_category_new_sequence;
		}

		return $new_sequence;
	}

	/**
	 * Sanitize the new sequence : array of  item_id => level
	 *
	 * @param $new_sequence
	 * @return array|bool
	 */
	private function sanitize_sequence( $new_sequence ) {

		if ( ! is_string($new_sequence) ) {
			return false;
		}

		// make an array from passed sequence string
		$ix = 0;
		$sanitized_sequence = array();
		$new_sequence = explode('xx', $new_sequence);
		foreach( $new_sequence as $item_tmp ) {
			$items = explode('x', $item_tmp);
			if ( empty($items[0]) || empty($items[1]) ) {
				continue;
			}

			$item_id = EPKB_Utilities::sanitize_int( $items[0] );
			if ( empty($item_id) ) {
				continue;
			}

			$item_level = EPKB_Utilities::sanitize_english_text( $items[1] );
			if ( empty($item_level) ) {
				continue;
			}

			$sanitized_sequence[$ix++] = array(0 => $item_id, 1 => $item_level);
		}

		return $sanitized_sequence;
	}

	private function sanitize_top_cat_sequence( $new_top_cat_sequence ) {

		if ( empty($new_top_cat_sequence) || ! is_string($new_top_cat_sequence) ) {
			return false;
		}

		$sanitized_sequence = array();
		$new_top_cat_sequence = explode('xx', $new_top_cat_sequence);
		foreach( $new_top_cat_sequence as $top_category_id ) {
			if ( empty($top_category_id) ) {
				continue;
			}
			$item_id = EPKB_Utilities::sanitize_int( $top_category_id );
			if ( empty($item_id) ) {
				continue;
			}
			$sanitized_sequence[] = $item_id;
		}

		return $sanitized_sequence;
	}


	/********************************************************************************************************
	 *
	 *                             CUSTOM CATEGORIES/ARTICLES SEQUENCE
	 *
	 ********************************************************************************************************/

	/**
	 * Given new sequence of categories and articles return a new object listing categories
	 *
	 * @param $kb_id
	 * @param $new_sequence
	 * @param null $orig_categories_obj
	 * @return EPKB_Categories_Array|false on error
	 */
	public function update_categories_order( $kb_id, $new_sequence, $orig_categories_obj=null ) {

		if ( ! is_array($new_sequence) ) {
			EPKB_Logging::add_log( "New sequence is empty" );
			return false;
		}

        // if necessary get the original sequence from the database
        if ( empty($orig_categories_obj) ) {
            $orig_categories_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
            if ( $orig_categories_data === null ) {
	            EPKB_Logging::add_log("orig_categories_data is null" );
	            return false;
            }
            $orig_categories_obj = new EPKB_Categories_Array( $orig_categories_data ); // normalizes the array as well
        }

        // error if the original sequence has data but not new sequence in the Ajax; otherwise just return empty array
        if ( empty( $new_sequence ) ) {
            return empty($orig_categories_data) ? $orig_categories_obj : false;
        }

		$new_sequence = $this->verify_and_format_new_sequence( $new_sequence );
		if ( empty( $new_sequence ) ) {
			EPKB_Logging::add_log("formatted new sequence is empty" );
			return false;
		}


		// 1. order CATEGORIES
		$parent_cat_id = 0;
		$parent_level = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level  ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'category' ) {
				$builder_child_categories[] = $item_id;
			}
		}
		$this->sync_category_level( $orig_categories_obj, $parent_cat_id, $parent_level, $builder_child_categories );

		// 2. order SUB-CATEGORIES
		$parent_level = 1;
		$last_category_id = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( ! in_array($item_level, array('category', 'sub-category')) ) {
				continue;
			}

			if ( $item_level == 'sub-category' ) {
				$builder_child_categories[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_categories) ) {
				$this->sync_category_level( $orig_categories_obj, $last_category_id, $parent_level, $builder_child_categories );
				$builder_child_categories = array();
			}

			if ( $item_level == 'category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_categories) && ! empty($last_category_id) ) {
			$this->sync_category_level( $orig_categories_obj, $last_category_id, $parent_level, $builder_child_categories );
		}

		// 3. order SUB-SUB-CATEGORIES
		$parent_level = 2;
		$last_sub_category_id = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( ! in_array($item_level, array('category', 'sub-category', 'sub-sub-category')) ) {
				continue;
			}

			if ( $item_level == 'sub-sub-category' ) {
				$builder_child_categories[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_categories) ) {
				$this->sync_category_level( $orig_categories_obj, $last_sub_category_id, $parent_level, $builder_child_categories );
				$builder_child_categories = array();
			}

			if ( $item_level == 'sub-category' ) {
				$last_sub_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_categories) && ! empty($last_sub_category_id) ) {
			$this->sync_category_level( $orig_categories_obj, $last_sub_category_id, $parent_level, $builder_child_categories );
		}

		// 4. order SUB-SUB-SUB-CATEGORIES
		$parent_level = 3;
		$last_sub_sub_category_id = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( ! in_array($item_level, array('category', 'sub-category', 'sub-sub-category', 'sub-sub-sub-category')) ) {
				continue;
			}

			if ( $item_level == 'sub-sub-sub-category' ) {
				$builder_child_categories[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_categories) ) {
				$this->sync_category_level( $orig_categories_obj, $last_sub_sub_category_id, $parent_level, $builder_child_categories );
				$builder_child_categories = array();
			}

			if ( $item_level == 'sub-sub-category' ) {
				$last_sub_sub_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_categories) && ! empty($last_sub_sub_category_id) ) {
			$this->sync_category_level( $orig_categories_obj, $last_sub_sub_category_id, $parent_level, $builder_child_categories );
		}
		
		// 5. order SUB-SUB-SUB-SUB-CATEGORIES
		$parent_level = 4;
		$last_sub_sub_sub_category_id = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( ! in_array($item_level, array('category', 'sub-category', 'sub-sub-category', 'sub-sub-sub-category', 'sub-sub-sub-sub-category')) ) {
				continue;
			}

			if ( $item_level == 'sub-sub-sub-sub-category' ) {
				$builder_child_categories[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_categories) ) {
				$this->sync_category_level( $orig_categories_obj, $last_sub_sub_sub_category_id, $parent_level, $builder_child_categories );
				$builder_child_categories = array();
			}

			if ( $item_level == 'sub-sub-sub-category' ) {
				$last_sub_sub_sub_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_categories) && ! empty($last_sub_sub_sub_category_id) ) {
			$this->sync_category_level( $orig_categories_obj, $last_sub_sub_sub_category_id, $parent_level, $builder_child_categories );
		}
		
		// 6. order SUB-SUB-SUB-SUB-SUB-CATEGORIES
		$parent_level = 5;
		$last_sub_sub_sub_sub_category_id = 0;
		$builder_child_categories = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( ! in_array($item_level, array('category', 'sub-category', 'sub-sub-category', 'sub-sub-sub-category', 'sub-sub-sub-sub-category', 'sub-sub-sub-sub-sub-category')) ) {
				continue;
			}

			if ( $item_level == 'sub-sub-sub-sub-sub-category' ) {
				$builder_child_categories[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_categories) ) {
				$this->sync_category_level( $orig_categories_obj, $last_sub_sub_sub_sub_category_id, $parent_level, $builder_child_categories );
				$builder_child_categories = array();
			}

			if ( $item_level == 'sub-sub-sub-sub-category' ) {
				$last_sub_sub_sub_sub_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_categories) && ! empty($last_sub_sub_sub_sub_category_id) ) {
			$this->sync_category_level( $orig_categories_obj, $last_sub_sub_sub_sub_category_id, $parent_level, $builder_child_categories );
		}
		return $orig_categories_obj;
	}

	/**
	 * For given parent sequence its children categories
	 *
	 * @param EPKB_Categories_Array $orig_categories_obj
	 * @param $parent_cat_id
	 * @param $parent_level
	 * @param $builder_child_categories
	 * @return array|null tree starting with parent
	 */
	public function sync_category_level( &$orig_categories_obj, $parent_cat_id, $parent_level, $builder_child_categories ) {

		// first find child categories for given parent
		$orig_parent_cat_ref = & $orig_categories_obj->get_parent_category_reference( $parent_cat_id, $parent_level );
		// no children or unknown parent category id
		if ( empty($orig_parent_cat_ref) ) {
			return $orig_parent_cat_ref;
		}

		// arrange original child categories according to the new order
		$new_cat_array = array();
		$orig_child_cat_array = $parent_level == 0 ? $orig_parent_cat_ref : $orig_parent_cat_ref[$parent_cat_id];
		foreach( $builder_child_categories as $ix => $builder_child_cat_id ) {
			$orig_cat_keys = array_keys($orig_child_cat_array);
			if ( in_array( $builder_child_cat_id, $orig_cat_keys ) ) {
				$new_cat_array[$builder_child_cat_id] = $orig_child_cat_array[$builder_child_cat_id];
				unset($orig_child_cat_array[$builder_child_cat_id]);
			}
		}
		foreach( $orig_child_cat_array as $orig_cat_id => $sub_array ) {
			$new_cat_array[$orig_cat_id] = $sub_array;
		}
		// add categories not in builder array
		if ( $parent_level == 0 ) {
			$orig_parent_cat_ref = $new_cat_array;
		} else {
			$orig_parent_cat_ref[ $parent_cat_id ] = $new_cat_array;
		}

		return $orig_parent_cat_ref;
	}

	/**
	 * Based on new sequence of articles return article object
	 *
	 * @param $kb_id
	 * @param $new_sequence
	 * @param null $orig_articles_obj
	 * @return EPKB_Articles_Array|false
	 */
	public function update_articles_order( $kb_id, $new_sequence, $orig_articles_obj=null ) {

		if ( ! is_array($new_sequence) ) {
			return false;
		}

        // if necessary get the original sequence from the database
        if ( empty($orig_articles_obj) ) {

            $orig_articles_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
            if ( $orig_articles_data === null ) {
                return false;
            }
            $orig_articles_obj = new EPKB_Articles_Array( $orig_articles_data ); // normalizes the array as well
        }

        // error if the original sequence has data but not new sequence in the Ajax; otherwise just return empty array
		if ( empty($new_sequence) ) {
			return empty($orig_articles_data) ? $orig_articles_obj : false;
		}

		$new_sequence = $this->verify_and_format_new_sequence( $new_sequence );
		if ( empty($new_sequence) ) {
			return false;
		}

		// 1. order all ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}

		// 2. order all SUB-ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'sub-article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'sub-category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}

		// 3. order all SUB-SUB-ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'sub-sub-article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'sub-sub-category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}
		
		// 4. order all SUB-SUB-ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'sub-sub-sub-article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'sub-sub-sub-category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}
		
		// 5. order all SUB-SUB-SUB-ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'sub-sub-sub-sub-article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'sub-sub-sub-sub-category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}
		// 6. order all SUB-SUB-SUB-SUB-ARTICLES
		$last_category_id = 0;
		$builder_child_articles = array();
		foreach( $new_sequence as $item_id_level ) {
			$pieces = explode("_", $item_id_level);
			$item_id = $pieces[0];
			$item_level = $pieces[1];

			if ( $item_level == 'sub-sub-sub-sub-sub-article' ) {
				$builder_child_articles[] = $item_id;
				continue;
			}

			if ( ! empty($builder_child_articles) ) {
				$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
				$builder_child_articles = array();
			}

			if ( $item_level == 'sub-sub-sub-sub-sub-category' ) {
				$last_category_id = $item_id;
			}
		}
		if ( ! empty($builder_child_articles) && ! empty($last_category_id) ) {
			$this->sync_article_level( $orig_articles_obj, $last_category_id, $builder_child_articles );
		}
		
		return $orig_articles_obj;
	}

	/**
	 * Arrange articles per new order.
	 *
	 * @param $orig_articles_obj
	 * @param $category_id
	 * @param $builder_child_categories
	 */
	public function sync_article_level( & $orig_articles_obj, $category_id, $builder_child_categories ) {

		$orig_articles_array = & $orig_articles_obj->ids_array;
		if ( empty($orig_articles_array[$category_id]) ) {
			return;
		}

		$articles_array_ref = $orig_articles_array[$category_id];
		$new_article_array = array(0 => $articles_array_ref[0], 1 => $articles_array_ref[1]);
		$new_article_array[0] = isset($articles_array_ref[0]) ? $articles_array_ref[0] : '';
		$new_article_array[1] = isset($articles_array_ref[1]) ? $articles_array_ref[1] : '';
		unset($articles_array_ref[0]);
		unset($articles_array_ref[1]);
		$orig_array_keys = array_keys($articles_array_ref);
		foreach( $builder_child_categories as $ix => $builder_child_cat_id ) {
			if ( in_array( $builder_child_cat_id, $orig_array_keys ) ) {
				$new_article_array[$builder_child_cat_id] = $articles_array_ref[$builder_child_cat_id];
				unset($articles_array_ref[$builder_child_cat_id]);
			}
		}
		// add new articles not present from builder sequence
		foreach( $articles_array_ref as $article_id => $article_title ) {
			$new_article_array[$article_id] = $article_title;
		}

		$orig_articles_array[$category_id] = $new_article_array;
	}

	/**
	 * Ensure that received sequence has correct format AND return in format id_level e.g. 333_category
	 *
	 * @param $new_sequence
	 * @return array|false - false to indicate an error
	 */
	public function verify_and_format_new_sequence( $new_sequence ) {
		$levels = array('category', 'sub-category', 'sub-sub-category', 'sub-sub-sub-category', 'sub-sub-sub-sub-category', 'sub-sub-sub-sub-sub-category', 'article', 'sub-article', 'sub-sub-article', 'sub-sub-sub-article', 'sub-sub-sub-sub-article', 'sub-sub-sub-sub-sub-article');
		$new_sequence_out = array();
		$last_level = null;
		foreach( $new_sequence as $ix => $data ) {
			if ( empty($data) || ! is_array($data) ) {
				return false;
			}

			// for Uncategorized category need to use 999999
			if ( isset($data[0]) && $data[0] === 0 ) {
				$data[0] = 9999999;
			}

			if ( empty( $data[0] ) || empty( $data[1] ) ) {
				return false;
			}

			$current_level = $data[1];
			if ( ! EPKB_Utilities::is_positive_int( $data[0] ) ||  ! in_array( $current_level, $levels ) ) {
				return false;
			}

			if ( $last_level === null && $current_level != 'category' ) {
				return false;
			}

			// top item should be category, sub-category after category etc.
			if ( $last_level !== null ) {
				switch ( $current_level ) {
					case 'category':
					case 'article':
						break;
					case 'sub-category':
						if ( $last_level == 'article' ) {
							return false;
						}
						break;
					case 'sub-sub-category':
						if ( $last_level == 'article' || $last_level == 'category' ) {
							return false;
						}
						break;
					case 'sub-sub-sub-category':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category') {
							return false;
						}
						break;
					case 'sub-sub-sub-sub-category':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-sub-category') {
							return false;
						}
						break;
					case 'sub-sub-sub-sub-sub-category':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-sub-category' || $last_level == 'sub-sub-sub-category') {
							return false;
						}
						break;
					case 'sub-article':
						if ( $last_level == 'article' || $last_level == 'category' ) {
							return false;
						}
						break;
					case 'sub-sub-article':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-article' ) {
							return false;
						}
						break;
					case 'sub-sub-sub-article':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-sub-category' || $last_level == 'sub-article' ) {
							return false;
						}
						break;
					case 'sub-sub-sub-sub-article':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-sub-category' || $last_level == 'sub-sub-sub-category' || $last_level == 'sub-article' ) {
							return false;
						}
						break;
					case 'sub-sub-sub-sub-sub-article':
						if ( $last_level == 'article' || $last_level == 'category' || $last_level == 'sub-category' || $last_level == 'sub-sub-category' || $last_level == 'sub-sub-sub-category' || $last_level == 'sub-sub-sub-sub-category' || $last_level == 'sub-article' ) {
							return false;
						}
						break;
					default:
						return false;
				}
			}

			array_push($new_sequence_out, $data[0] . '_' . $current_level);
			$last_level = $current_level;
		}
		return empty($new_sequence_out) ? false : $new_sequence_out;
	}
}