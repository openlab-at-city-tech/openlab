<?php

/**
 * Handle manipulation of Categories with articles relationship
 *
 * - [cat term id]
 *        -> [0] -> category name
 *        -> [1] -> category desc
 *        -> [article id -> article title]  (could be without articles)
 *        -> [article id -> article title]
 */
class EPKB_Articles_Array {

	public $ids_array;

	public function __construct( array $cat_sequences ) {
		$this->ids_array = $cat_sequences;
		$this->normalize_and_sanitize();
	}

	public function normalize_and_sanitize() {
		foreach ($this->ids_array as $key => $data_array) {

			if ( ! EPKB_Utilities::is_positive_or_zero_int( $key ) || ! is_array($data_array) ||
			     empty($data_array[0]) || is_array($data_array[0]) || ! isset($data_array[1]) || is_array($data_array[1]) ) {
				unset($this->ids_array[$key]);
				continue;
			}

			if ( count($data_array) > 2 ) {
				$ix = 0;
				foreach( $data_array as $data_key => $data_value ) {
					if ( ++$ix < 3 ) {
						continue;
					}
					if ( ! EPKB_Utilities::is_positive_int( $data_key )  || is_array($data_value) ) {
						unset($data_array[$data_key]);
					}
					$data_array[$data_key] = sanitize_text_field( $data_value );
				}
			}
		}
	}

	public static function retrieve_article_sequence( $articles ) {
		$new_article_seq = array();

		$articles_id_title = array();
		foreach( $articles as $article ) {
			$articles_id_title[$article->ID] = $article->post_title;
		}

		foreach( $articles_id_title as $article_id => $article_title ) {
			$new_article_seq[$article_id] = $article_title;
		}

		return $new_article_seq;
	}
}