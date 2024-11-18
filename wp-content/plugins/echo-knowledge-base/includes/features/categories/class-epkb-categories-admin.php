<?php

/**
 * Setup hooks for KB Categories
 */
class EPKB_Categories_Admin {

	const KB_CATEGORIES_SEQ_META = 'epkb_categories_sequence';

	public function __construct() {

		// handle KB categories sequence
		add_action( 'created_term', array( $this, 'update_categories_sequence' ), 10, 3 );
		add_action( 'edited_term', array( $this, 'update_categories_sequence' ), 10, 3 );
		add_action( 'delete_term', array( $this, 'update_categories_sequence' ), 10, 3 );

		// check flag for get started page
		add_action( 'created_term', array( $this, 'update_edit_articles_categories_visited_flag' ), 10, 3 );
		add_action( 'edited_term', array( $this, 'update_edit_articles_categories_visited_flag' ), 10, 3 );
	}

	/**
	 * After a category is saved, updated or deleted, update categories sequence.
	 *
	 * @param int $updated_term_id
	 * @param int $taxonomy_id
	 * @param string $taxonomy_slug
	 * @return bool
	 */
	public function update_categories_sequence( $updated_term_id=0, $taxonomy_id=0, $taxonomy_slug='' ) {

		// return if the current hook is activated for a non-KB Category
		$kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $taxonomy_slug );
		if ( is_wp_error( $kb_id ) ) {
			return false;
		}
		
		// 1. get all KB category ids  ( do not use WP function get_terms() to avoid recursions )
		$categories_order_method = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'categories_display_sequence' );
		$order_by =  $categories_order_method == 'created-date' ? 'date' : 'name';  // use order by name as default and temporary for custom order
		$all_terms = EPKB_Core_Utilities::get_kb_categories_visible( $kb_id, $order_by );

		// remove new category from the list if it is draft
		if ( ! empty( EPKB_Utilities::get('epkb_category_is_draft' ) ) ) {
			foreach( $all_terms as $key => $term ) {
				if ( $term->term_id == $updated_term_id ) {
					unset( $all_terms[$key] );
				}
			}
		}

		if ( $all_terms === null ) {
			return false;
		}

		$stored_article_ids = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( $stored_article_ids === null ) {
			return false;
		}

		// 2. create hierarchy of IDs from all terms
		$all_ids_tree = self::create_ID_hierarchy( $all_terms );
		$new_cat_ids_obj = new EPKB_Categories_Array( $all_ids_tree ); // normalizes the array as well

		// 3. update article sequence configuration with possibly new category name and/or description
		$article_ids_updated = false;
		if ( ! empty($updated_term_id) ) {
			$kb_taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
			$term = get_term( $updated_term_id, $kb_taxonomy );   // handle WP 4.0 - needs 2nd parameter
			if ( ! empty( $term ) && ! is_wp_error( $term ) && property_exists( $term, 'term_id' ) ) {   // handle WP 4.0 - returns object not WP_Term
				$stored_article_ids[ $term->term_id ][0] = $term->name;
				// remove extra paragraph user might have added around term description
				$term_description =  preg_replace('/^<p>/', '', trim($term->description) );
				$term_description =  preg_replace('/<\/p>$/', '', $term_description);
				$stored_article_ids[ $term->term_id ][1] = $term_description;
				$article_ids_updated = true;
			}
		}

		// 4. store the new configuration

		// for custom sequenced update the custom sequence with changes
		if ( $categories_order_method == 'user-sequenced' ) {

			$orig_sequence = $this->get_orig_custom_sequence( $kb_id, $stored_article_ids );

			// only update the custom sequence only if the previous one exists
			if ( ! empty( $orig_sequence ) ) {
				$config_seq = new EPKB_KB_Config_Sequence();
				$result = $config_seq->update_categories_order( $kb_id, $orig_sequence, $new_cat_ids_obj );
				if ( ! empty( $result) ) {
					$new_cat_ids_obj = $result;
				}
			}
		}

		EPKB_Utilities::save_kb_option( $kb_id, self::KB_CATEGORIES_SEQ_META, $new_cat_ids_obj->ids_array );
		if ( $article_ids_updated  ) {
			EPKB_Utilities::save_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, $stored_article_ids );
		}

		// 5. Sync article seq
		$article_sequence = new EPKB_Articles_Admin();
		$article_sequence->update_articles_sequence( $kb_id );

		// 6. Check image icons
		EPKB_KB_Config_Category::remove_missing_terms_and_images_from_categories_icons( $kb_id );

		return true;
	}

	/**
	 * Update custom order with changed articles/categories
	 *
	 * @param $kb_id
	 * @param $stored_article_ids
	 * @return false|array
	 */
	public function get_orig_custom_sequence( $kb_id, $stored_article_ids ) {

		// retrieve previous sequence since we will be adding any new categories to the end
		$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_id, self::KB_CATEGORIES_SEQ_META, null, true );
		if ( $custom_categories_data === null ) {
			return false;
		}
		$custom_ids_obj = new EPKB_Categories_Array( $custom_categories_data ); // normalizes the array as well

		$custom_sequence = array();
		foreach( $custom_ids_obj->ids_array as $category_id => $sub_array ) {

			$custom_sequence[] = array( $category_id, 'category' );

			if ( ! empty($sub_array) && is_array($sub_array) ) {
				foreach( $sub_array as $sub_category_id => $sub_sub_array ) {

					$custom_sequence[] = array( $sub_category_id, 'sub-category' );

					if ( ! empty($sub_sub_array) && is_array($sub_sub_array) ) {
						foreach( $sub_sub_array as $sub_sub_category_id => $sub_sub_sub_array ) {

							$custom_sequence[] = array( $sub_sub_category_id, 'sub-sub-category' );

							if ( ! empty($sub_sub_sub_array) && is_array($sub_sub_sub_array) ) {
								foreach( $sub_sub_sub_array as $sub_sub_sub_category_id => $sub_sub_sub_sub_array ) {
									$custom_sequence[] = array( $sub_sub_sub_category_id, 'sub-sub-sub-category' );

									if ( ! empty($sub_sub_sub_sub_array) && is_array($sub_sub_sub_sub_array) ) {
										foreach( $sub_sub_sub_sub_array as $sub_sub_sub_sub_category_id => $sub_sub_sub_sub_sub_array ) {
											$custom_sequence[] = array( $sub_sub_sub_sub_category_id, 'sub-sub-sub-sub-category' );
											
											if ( ! empty($sub_sub_sub_sub_sub_array) && is_array($sub_sub_sub_sub_sub_array) ) {
												foreach( $sub_sub_sub_sub_sub_array as $sub_sub_sub_sub_sub_category_id => $other ) {
													$custom_sequence[] = array( $sub_sub_sub_sub_sub_category_id, 'sub-sub-sub-sub-sub-category' );
													$this->add_articles( $custom_sequence, $stored_article_ids, $sub_sub_sub_sub_sub_category_id, 'sub-sub-sub-sub-sub-article' );
												}
											}
											
											$this->add_articles( $custom_sequence, $stored_article_ids, $sub_sub_sub_sub_category_id, 'sub-sub-sub-sub-article' );
										}
									}
									
									$this->add_articles( $custom_sequence, $stored_article_ids, $sub_sub_sub_category_id, 'sub-sub-sub-article' );
								}
							}
							$this->add_articles( $custom_sequence, $stored_article_ids, $sub_sub_category_id, 'sub-sub-article' );
						}
					}
					$this->add_articles( $custom_sequence, $stored_article_ids, $sub_category_id, 'sub-article' );
				}
			}
			$this->add_articles( $custom_sequence, $stored_article_ids, $category_id, 'article' );
		}

		return $custom_sequence;
	}

	/**
	 * Add custom article list.
	 *
	 * @param $custom_sequence
	 * @param $stored_article_ids
	 * @param $category_id
	 * @param $level
	 */
	private function add_articles( & $custom_sequence, $stored_article_ids, $category_id, $level ) {

		if ( empty($category_id) || empty($stored_article_ids[$category_id]) || ! is_array($stored_article_ids[$category_id]) ) {
			return;
		}

		$ix = 0;
		foreach( $stored_article_ids[$category_id] as $article_id => $article_title ) {
			if ( $ix++ < 2 ) {
				continue;
			}
			$custom_sequence[] = array( $article_id, $level );
		}
	}

	/**
	 * Return terms sorted in hierarchy
	 * @param array $terms
	 * @return array mixed
	 */
	private function create_ID_hierarchy( array $terms ) {
		$ids_in_hierarchy = array();
		$this->create_ID_hierarchy_recursive( $terms, $ids_in_hierarchy );
		return $ids_in_hierarchy;
	}

	/**
	 * Create tree of term IDs
	 *
	 * @param array $terms - PASSED BY REFERENCE - taxonomy term objects to sort - will be truncated
	 * @param array $terms_in_hierarchy- PASSED BY REFERENCE - in_hierarchy result array to put them in
	 * @param integer $parent_id the current parent ID to put them in
	 * @param int $level - how deep the recursion went; guard
	 */
	private function create_ID_hierarchy_recursive( array &$terms, array &$terms_in_hierarchy, $parent_id = 0, $level = 0 ) {
		$level++;
		foreach ( $terms as $num => $term ) {
			if ( $term->parent == $parent_id ) {
				$terms_in_hierarchy[$term->term_id] = array();
				unset($terms[$num]);
			}
		}

		foreach ( $terms_in_hierarchy as $term_id => $empty) {
			if ( $level < 7 ) {
				$this->create_ID_hierarchy_recursive( $terms, $terms_in_hierarchy[$term_id], $term_id, $level );
				$level--;
			}
		}
	}

	/**
	 * Retrieve non-custom sequence of categories e.g. based on date or name
	 *
	 * @param $kb_id
	 * @param $categories_order_method
	 * @return array|false on error
	 */
	public function get_categories_sequence_non_custom( $kb_id, $categories_order_method='' ) {

		// 1. get all KB category ids  ( do not use WP function get_terms() to avoid recursions )
		$order_by =  $categories_order_method == 'created-date' ? 'date' : 'name';  // use order by name as default and temporary for custom order
		$all_terms = EPKB_Core_Utilities::get_kb_categories_visible( $kb_id, $order_by );
		if ( $all_terms === null ) {
			return false;
		}

		// 2. create hierarchy of IDs from all terms
		$all_ids_tree = self::create_ID_hierarchy( $all_terms );
		$new_cat_ids_obj = new EPKB_Categories_Array( $all_ids_tree ); // normalizes the array as well

		return $new_cat_ids_obj->ids_array;
	}

	public function update_edit_articles_categories_visited_flag( $updated_term_id=0, $taxonomy_id=0, $taxonomy_slug='' ) {

		$kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $taxonomy_slug );
		if ( is_wp_error( $kb_id ) ) {
			return false;
		}

		// add flag for get started page
		if ( ! EPKB_Core_Utilities::run_setup_wizard_first_time() ) {
			EPKB_Core_Utilities::add_kb_flag( 'edit_articles_categories_visited' );
		}

		return true;
	}
}