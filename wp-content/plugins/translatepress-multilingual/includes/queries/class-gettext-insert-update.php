<?php

/**
 * Class TRP_Gettext_Normalization
 *
 * Queries for inserting and updating strings in gettext tables
 *
 * To access this component use:
 *        $trp = TRP_Translate_Press::get_trp_instance();
 *      $trp_query = $trp->get_component( 'query' );
 *      $gettext_insert_update = $trp_query->get_query_component('gettext_insert_update');
 *
 */
class TRP_Gettext_Insert_Update extends TRP_Query {

	public    $db;
	protected $settings;
	protected $error_manager;

	/**
	 * TRP_Query constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		global $wpdb;
		$this->db       = $wpdb;
		$this->settings = $settings;
	}

	/**
	 * Inserts gettext strings in trp_gettext_{language_code} table and trp_gettext_original_strings table
	 *
	 * @param $new_strings
	 * @param $language_code
	 *
	 * @return int|null
	 */
	public function insert_gettext_strings( $new_strings, $language_code ) {
		if ( count( $new_strings ) == 0 ) {
			return;
		}
		$query = "INSERT INTO `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) ) . "` ( original, translated, domain, status, plural_form, original_id ) VALUES ";

		$values        = array();
		$place_holders = array();
		$original_ids  = $this->gettext_original_strings_sync( $new_strings );
		foreach ( $new_strings as $key => $string ) {
			//make sure we don't insert empty strings in db
			if ( empty( $string['original'] ) ) {
				continue;
			}

			if ( $string['original'] == $string['translated'] || $string['original_plural'] == $string['translated'] || $string['translated'] == '' ) {
				$translated = null;
				$status     = self::NOT_TRANSLATED;
			} else {
				$translated = $string['translated'];
				$status     = self::HUMAN_REVIEWED;
			}
			array_push( $values, $string['original'], $translated, $string['domain'], $status, $string['plural_form'], $original_ids[ $key ] );
			$place_holders[] = "( '%s', '%s', '%s', '%d', '%d', '%d')";
		}


		$query .= implode( ', ', $place_holders );
		$this->db->query( $this->db->prepare( $query . ' ', $values ) );

		$this->maybe_record_automatic_translation_error( array( 'details' => 'Error running insert_gettext_strings()' ) );

		if ( count( $new_strings ) == 1 ) {
			return $this->db->insert_id;
		} else {
			return null;
		}
	}

	/**
	 * Returns originals table ids of $new_strings
	 *
	 * Also inserts in gettext_original_strings table if strings not found
	 *
	 * @param $language_code
	 * @param $new_strings
	 *
	 * @return array|object|null
	 */
	public function gettext_original_strings_sync( $new_strings ) {
		if ( count( $new_strings ) === 0 ) {
			return array();
		}
		$new_strings_in_dictionary_with_original_id = array();
		$insert_strings                             = array();
		$originals_table                            = $this->get_table_name_for_gettext_original_strings();

		$possible_new_strings = array();
		foreach ( $new_strings as $string ) {
			$possible_new_strings[] = $this->db->prepare( "%s", $string['original'] );
		}

		// query for originals disregarding domain. Later, only the ones matching the domain too get selected.
		$existing_strings = $this->db->get_results( "SELECT id, original, domain, context FROM `$originals_table` WHERE BINARY $originals_table.original IN (" . implode( ',', $possible_new_strings ) . ")", ARRAY_A );

		// filtering queried strings to match exact domain and context. If not found in db, prepare for inserting. At the same time, prepare ids for return
		if ( ! empty( $existing_strings ) ) {
			foreach ( $new_strings as $key => $new_string ) {
				foreach ( $existing_strings as $existing_string ) {
					if ( $existing_string['original'] === $new_string['original'] &&
					     $existing_string['domain'] === $new_string['domain'] &&
					     $existing_string['context'] === $new_string['context']
					) {
						$new_strings_in_dictionary_with_original_id[ $key ] = $existing_string['id'];
						break;
					}
				}
				if ( ! isset( $new_strings_in_dictionary_with_original_id[ $key ] ) ) {
					$insert_strings[] = $new_string;
				}

			}
		} else {
			$insert_strings = $new_strings;
		}

		if ( ! empty( $insert_strings ) ) {
			foreach ( $insert_strings as $k => $string ) {
				$insert_strings[ $k ] = $this->db->prepare( "( '%s', '%s', '%s', '%s')", $string['original'], $string['domain'], $string['context'], $string['original_plural'] );
			}

			//insert the strings that are missing
			$this->db->query( "INSERT INTO `$originals_table` (original, domain, context, original_plural) VALUES " . implode( ',', $insert_strings ) );

			//get the ids for inserted the new strings (new in dictionary)
			$new_strings_inserted = $this->db->get_results( "SELECT id, original, domain, context FROM `$originals_table` WHERE BINARY $originals_table.original IN (" . implode( ',', $possible_new_strings ) . ")", OBJECT_K );

			// filtering queried strings to match exact domain and context
			foreach ( $new_strings as $key => $new_string ) {
				foreach ( $new_strings_inserted as $new_string_inserted ) {
					if ( $new_string_inserted->original === $new_string['original'] &&
					     $new_string_inserted->domain === $new_string['domain'] &&
					     $new_string_inserted->context === $new_string['context']
					) {
						$new_strings_in_dictionary_with_original_id[ $key ] = $new_string_inserted->id;
						break;
					}
				}
			}
		}

		return $new_strings_in_dictionary_with_original_id;

	}

	/**
	 * Update gettext strings in trp_gettext_{language_code} table
	 *
	 * @param $updated_strings
	 * @param $language_code
	 * @param $columns_to_update array Only update specified columns
	 *
	 * @return void
	 */
	public function update_gettext_strings( $updated_strings, $language_code, $columns_to_update = array('id','original','translated','domain','status','plural_form')) {
		if ( count( $updated_strings ) == 0 ) {
			return;
		}

		$placeholder_array_mapping = array(
			'id'          => '%d',
			'original'    => '%s',
			'translated'  => '%s',
			'domain'      => '%s',
			'status'      => '%d',
			'plural_form' => '%d'
		);
		$columns_query_part        = '';
		foreach ( $columns_to_update as $column ) {
			$columns_query_part .= $column . ',';
			$placeholders[]     = $placeholder_array_mapping[ $column ];
		}
		$columns_query_part = rtrim( $columns_query_part, ',' );

		$query = "INSERT INTO `" . sanitize_text_field( $this->get_gettext_table_name( $language_code ) ) . "` ( " . $columns_query_part . " ) VALUES ";

		$values        = array();
		$place_holders = array();

		$placeholders_query_part = '(';
		foreach ( $placeholders as $placeholder ) {
			$placeholders_query_part .= "'" . $placeholder . "',";
		}
		$placeholders_query_part = rtrim( $placeholders_query_part, ',' );
		$placeholders_query_part .= ')';

		$update_id_and_original = in_array( 'id', $columns_to_update ) && in_array( 'original', $columns_to_update );
		foreach ( $updated_strings as $string ) {
			if ( ! $update_id_and_original || ( ! empty( $string['id'] ) && is_numeric( $string['id'] ) && ! empty( $string['original'] ) ) ) { //we must have an ID and an original if columns to update include id and original
				$string['status'] = ! empty( $string['status'] ) ? $string['status'] : self::NOT_TRANSLATED;
				foreach ( $columns_to_update as $column ) {
					array_push( $values, $string[ $column ] );
				}
				$place_holders[] = $placeholders_query_part;
			}
		}

		$on_duplicate    = ' ON DUPLICATE KEY UPDATE ';
		$key_term_values = $this->is_values_accepted() ? 'VALUES' : 'VALUE';
		foreach ( $columns_to_update as $column ) {
			if ( $column == 'id' ) {
				continue;
			}
			$on_duplicate .= $column . '=' . $key_term_values . '(' . $column . '),';
		}
		$query .= implode( ', ', $place_holders );

		$on_duplicate = rtrim( $on_duplicate, ',' );
		$query        .= $on_duplicate;

		$this->db->query( $this->db->prepare( $query . ' ', $values ) );

		$this->maybe_record_automatic_translation_error( array( 'details' => 'Error running update_gettext_strings()' ) );
	}

	/**
	 * Insert in the DB gettext_original_meta the pair meta_key = meta_value for all original ids
	 *
	 * If exact pair meta_key = meta_value exists then skip inserting
	 *
	 * @param $original_ids
	 * @param $meta_key
	 * @param $meta_value
	 *
	 * @return void
	 */
	public function bulk_insert_original_id_meta( $original_ids, $meta_key, $meta_value ) {
		$meta_key   = sanitize_text_field( $meta_key );
		$meta_value = sanitize_text_field( $meta_value );

		if ( ! empty( $original_ids ) ) {
			$original_id_values = array();
			foreach ( $original_ids as $key => $original_id ) {
				$original_ids[$key] = (int)$original_id;
				$original_id_values[] = $this->db->prepare( "%d", $original_id );
			}

			// if an original_id exists with the same meta_key and meta_value then skip insert
			$existing_entries = $this->db->get_results( $this->db->prepare(
				"SELECT original_id FROM " . $this->get_table_name_for_gettext_original_meta() . " WHERE meta_key = '" . $meta_key . "'  AND meta_value = '" . $meta_value . "' AND  original_id IN ( %2s )",
				implode( ', ', $original_id_values )
			), OBJECT_K );

			$existing_entries = array_keys($existing_entries);
			$insert_this = array_unique( array_diff( $original_ids, $existing_entries ) );
			if ( ! empty( $insert_this ) ) {
				$insert_values = array();
				foreach ( $insert_this as $missing_entry ) {
					$insert_values[] = $this->db->prepare( "( %d, %s, %s )", $missing_entry, $meta_key, $meta_value );
				}

				$this->db->query( "INSERT INTO " . $this->get_table_name_for_gettext_original_meta() . " ( original_id, meta_key, meta_value ) VALUES " . implode( ', ', $insert_values ) );
			}

		}

	}

}
