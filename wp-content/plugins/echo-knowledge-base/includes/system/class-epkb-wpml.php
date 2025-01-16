<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * A class that makes KB compatible with WPML
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_WPML {

	/**
	 * Filter all categories not belonging to the current language
	 * @param $category_seq_data
	 * @return mixed
	 */
	public static function apply_category_language_filter( $category_seq_data ) {
		$current_lang = apply_filters( 'wpml_current_language', NULL );

		foreach ( $category_seq_data as $box_category_id => $box_sub_categories ) {
			$unset_category = self::remove_language_category( $box_category_id, $current_lang );
			if ( $unset_category ) {
				unset($category_seq_data[$box_category_id]);
				continue;
			}

			foreach ( $box_sub_categories as $box_sub_category_id => $box_sub_sub_category ) {
				$unset_category = self::remove_language_category( $box_sub_category_id, $current_lang );
				if ( $unset_category ) {
					unset($box_sub_categories[$box_sub_category_id]);
					continue;
				}

				foreach ( $box_sub_sub_category as $box_sub_sub_category_id => $box_sub_sub_sub_category ) {
					$unset_category = self::remove_language_category( $box_sub_sub_category_id, $current_lang );
					if ( $unset_category ) {
						unset($box_sub_sub_category[$box_sub_sub_category_id]);
						continue;
					}
					
					foreach ( $box_sub_sub_sub_category as $box_sub_sub_sub_category_id => $box_sub_sub_sub_sub_category ) {
						$unset_category = self::remove_language_category( $box_sub_sub_sub_category_id, $current_lang );
						if ( $unset_category ) {
							unset($box_sub_sub_sub_category[$box_sub_sub_sub_category_id]);
							continue;
						}
						
						foreach ( $box_sub_sub_sub_sub_category as $box_sub_sub_sub_sub_category_id => $box_sub_sub_sub_sub_sub_category ) {
							$unset_category = self::remove_language_category( $box_sub_sub_sub_sub_category_id, $current_lang );
							if ( $unset_category ) {
								unset($box_sub_sub_sub_sub_category[$box_sub_sub_sub_sub_category_id]);
								continue;
							}
							
							foreach ( $box_sub_sub_sub_sub_sub_category as $box_sub_sub_sub_sub_sub_category_id => $unused ) {
								$unset_category = self::remove_language_category( $box_sub_sub_sub_sub_sub_category_id, $current_lang );
								if ( $unset_category ) {
									unset($box_sub_sub_sub_sub_sub_category[$box_sub_sub_sub_sub_sub_category_id]);
									continue;
								}
							}
						}
					}
					
				}
			}
		}

		return $category_seq_data;
	}

	public static function remove_language_category( $category_id, $current_lang ) {
		global $icl_adjust_id_url_filter_off;

		$temp_icl_adjust_id_url_filter_off = $icl_adjust_id_url_filter_off;

		$icl_adjust_id_url_filter_off = true;
		$category = get_term( $category_id );
		if ( empty( $category ) || is_wp_error( $category ) || ! property_exists( $category, 'term_id' ) ) {
			return true;
		}

		$args = array( 'element_id' => $category->term_taxonomy_id, 'element_type' => 'category' );
		$my_category_language_code = apply_filters( 'wpml_element_language_code', null, $args );

		$icl_adjust_id_url_filter_off = $temp_icl_adjust_id_url_filter_off;

		return $my_category_language_code != $current_lang;
	}

	/**
	 * Remove all articles that do not belong to the currently selected language
	 * @param $articles_seq_data
	 * @return mixed
	 */
	public static function apply_article_language_filter( $articles_seq_data ) {
		$current_lang = apply_filters( 'wpml_current_language', NULL );

		foreach ( $articles_seq_data as $category_id => $unused) {
			self::remove_language_article( $articles_seq_data, $category_id, $current_lang );
		}

		return $articles_seq_data;
	}

	private static function remove_language_article( &$seq_data, $category_id, $current_lang ) {

		global $icl_adjust_id_url_filter_off;

		$temp_icl_adjust_id_url_filter_off = $icl_adjust_id_url_filter_off;

		$icl_adjust_id_url_filter_off = true;
		$category = get_term($category_id);
		if ( empty( $category ) || is_wp_error( $category ) || ! property_exists( $category, 'term_id' ) ) {
			return;
		}

		$args = array( 'element_id' => $category->term_taxonomy_id, 'element_type' => 'category' );
		$my_category_language_code = apply_filters( 'wpml_element_language_code', null, $args );

		if ( $my_category_language_code != $current_lang ) {
			unset($seq_data[$category_id]);
		}

		$icl_adjust_id_url_filter_off = $temp_icl_adjust_id_url_filter_off;
	}
}
