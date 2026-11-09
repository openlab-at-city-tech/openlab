<?php


if ( !defined('ABSPATH' ) )
    exit();

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

		$rows         = array();
		$original_ids = $this->gettext_original_strings_sync( $new_strings );
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
				$status     = isset( $string['status'] ) ? (int) $string['status'] : self::HUMAN_REVIEWED;
				if ( $status === self::NOT_TRANSLATED ) {
				$status     = self::HUMAN_REVIEWED;
			}
			}
			// Skip if original_id doesn't exist for this key
			if ( !isset( $original_ids[ $key ] ) ) {
				continue;
			}
			// One row per (original_id, plural_form): MySQL 8 rejects a multi-row ODKU insert that repeats a unique key (ERROR 1869).
			$row_key = (int) $original_ids[ $key ] . ':' . (int) $string['plural_form'];
			if ( isset( $rows[ $row_key ] ) && ( $rows[ $row_key ][1] !== null || $translated === null ) ) {
				continue;
			}
			$rows[ $row_key ] = array( $string['original'], $translated, $string['domain'], $status, $string['plural_form'], $original_ids[ $key ] );
		}

        if ( empty( $rows ) )
            return null;

		$values = call_user_func_array( 'array_merge', array_values( $rows ) );
		$query .= implode( ', ', array_fill( 0, count( $rows ), "( '%s', '%s', '%s', '%d', '%d', '%d')" ) );
		$query .= ' ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)';
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
     *
     *      * How to call this function?
     * You should create an array of arrays that have id, original, domain and possible context:
     *
     * $gettext_with_null_original_id_array[] = array(
    'original' => $current_language_string['tt_original'],
    'id' => $current_language_string['id'],
    'domain' => $current_language_string['tt_domain'],
    'context' => $current_language_string['tt_context'], //optional
    );
     *
     * than:
     *
     * foreach ($gettext_with_null_original_id_array as $item) {
    $original_ids_null_context_false[] = $item;
    }
     *
     * if the context is null call the function like this:
     * $original_ids_null = $gettext_insert_update->gettext_original_strings_sync($original_ids_null_context_false, false);
     *
     * else call it without the second argument (or with the second argument true):
     *
     * $original_ids_null = $gettext_insert_update->gettext_original_strings_sync($original_ids_null_context_false);
     *
     * or:
     *
     * $original_ids_null = $gettext_insert_update->gettext_original_strings_sync($original_ids_null_context_false, true);
     */
	public function gettext_original_strings_sync( $new_strings, $use_context = true ) {
		if ( count( $new_strings ) === 0 ) {
			return array();
		}

			$hash_available = $this->gettext_original_lookup_hash_is_available();

			if ( ! $hash_available || ! $use_context ) {
				return $this->legacy_gettext_original_strings_sync( $new_strings, $hash_available, $use_context );
			}

		$new_strings_in_dictionary_with_original_id = array();
		$insert_strings                             = array();
		$originals_table                            = $this->get_table_name_for_gettext_original_strings();
			$lookup_hashes                              = array();

			foreach ( $new_strings as $key => $string ) {
				if ( empty( $string['original'] ) || ! isset( $string['domain'] ) ) {
					continue;
				}

				$context                                = $this->normalize_gettext_original_context( isset( $string['context'] ) ? $string['context'] : null );
				$lookup_hash                            = $this->get_gettext_original_lookup_hash( $string['original'], $string['domain'], $context );
				$lookup_hashes[ $lookup_hash ]          = true;
				$new_strings[ $key ]['context']         = $context;
				$new_strings[ $key ]['lookup_hash']     = $lookup_hash;
				$new_strings[ $key ]['original_plural'] = isset( $string['original_plural'] ) ? $string['original_plural'] : '';
			}

			$existing_strings = array();
			if ( ! empty( $lookup_hashes ) ) {
				$placeholders    = implode( ', ', array_fill( 0, count( $lookup_hashes ), '%s' ) );
				$existing_query  = "SELECT id, original, domain, context, lookup_hash FROM `$originals_table` WHERE lookup_hash IN ( $placeholders )";
				$existing_strings = $this->db->get_results( $this->db->prepare( $existing_query, array_keys( $lookup_hashes ) ), ARRAY_A );
			}

			foreach ( $new_strings as $key => $new_string ) {
				if ( empty( $new_string['lookup_hash'] ) ) {
					continue;
				}

				foreach ( $existing_strings as $existing_string ) {
					if ( $existing_string['lookup_hash'] === $new_string['lookup_hash'] &&
					     $existing_string['original'] === $new_string['original'] &&
					     $existing_string['domain'] === $new_string['domain'] &&
					     $this->normalize_gettext_original_context( $existing_string['context'] ) === $new_string['context']
					) {
                                $new_strings_in_dictionary_with_original_id[$key] = $existing_string['id'];
                                break;
                            }
				}

				if ( ! isset( $new_strings_in_dictionary_with_original_id[ $key ] ) ) {
					$insert_strings[ $key ] = $new_string;
				}
			}

			if ( ! empty( $insert_strings ) ) {
				foreach ( $insert_strings as $k => $string ) {
					$insert_strings[ $k ] = $this->db->prepare( "( '%s', '%s', '%s', '%s', '%s')", $string['original'], $string['domain'], $string['context'], $string['original_plural'], $string['lookup_hash'] );
				}

				// Rely on the unique lookup hash as the final guard. Conflicts are expected in concurrent flows.
				$this->db->query( "INSERT IGNORE INTO `$originals_table` (original, domain, context, original_plural, lookup_hash) VALUES " . implode( ',', $insert_strings ) );

				$insert_hashes = array();
				foreach ( $insert_strings as $key => $prepared_string ) {
					$insert_hashes[ $new_strings[ $key ]['lookup_hash'] ] = true;
				}

				$placeholders         = implode( ', ', array_fill( 0, count( $insert_hashes ), '%s' ) );
				$new_strings_inserted = $this->db->get_results( $this->db->prepare( "SELECT id, original, domain, context, lookup_hash FROM `$originals_table` WHERE lookup_hash IN ( $placeholders )", array_keys( $insert_hashes ) ), ARRAY_A );

				foreach ( $new_strings as $key => $new_string ) {
					if ( empty( $new_string['lookup_hash'] ) ) {
						continue;
					}

					foreach ( $new_strings_inserted as $new_string_inserted ) {
						if ( $new_string_inserted['lookup_hash'] === $new_string['lookup_hash'] &&
						     $new_string_inserted['original'] === $new_string['original'] &&
						     $new_string_inserted['domain'] === $new_string['domain'] &&
						     $this->normalize_gettext_original_context( $new_string_inserted['context'] ) === $new_string['context']
						) {
							$new_strings_in_dictionary_with_original_id[ $key ] = $new_string_inserted['id'];
                            break;
                        }
                    }
				}
				}

			return $new_strings_in_dictionary_with_original_id;

		}

		/**
		 * Legacy lookup path used before the hash migration completes or when callers explicitly ignore context.
		 *
		 * @param array $new_strings Input gettext original rows.
		 * @param bool  $hash_available Whether the lookup_hash column and unique index are usable.
		 * @param bool  $use_context Whether context must be part of the identity.
		 *
		 * @return array<int, int>
		 */
		protected function legacy_gettext_original_strings_sync( $new_strings, $hash_available = false, $use_context = true ) {
			$new_strings_in_dictionary_with_original_id = array();
			$insert_strings                             = array();
			$originals_table                            = $this->get_table_name_for_gettext_original_strings();

			$possible_new_strings = array();
			foreach ( $new_strings as $key => $string ) {
				if ( empty( $string['original'] ) || ! isset( $string['domain'] ) ) {
					continue;
				}

				$new_strings[ $key ]['context']         = $this->normalize_gettext_original_context( isset( $string['context'] ) ? $string['context'] : null );
				$new_strings[ $key ]['original_plural'] = isset( $string['original_plural'] ) ? $string['original_plural'] : '';
				$possible_new_strings[] = $this->db->prepare( "%s", $string['original'] );
			}

			if ( empty( $possible_new_strings ) ) {
				return array();
			}

			$existing_strings = $this->db->get_results( "SELECT id, original, domain, context FROM `$originals_table` WHERE BINARY $originals_table.original IN (" . implode( ',', $possible_new_strings ) . ")", ARRAY_A );

			if ( ! empty( $existing_strings ) ) {
				foreach ( $new_strings as $key => $new_string ) {
					if ( ! isset( $new_string['context'] ) ) {
						continue;
					}

					foreach ( $existing_strings as $existing_string ) {
						if ( $existing_string['original'] === $new_string['original'] &&
						     $existing_string['domain'] === $new_string['domain'] &&
						     ( ! $use_context || $this->normalize_gettext_original_context( $existing_string['context'] ) === $new_string['context'] )
						) {
							$new_strings_in_dictionary_with_original_id[ $key ] = $existing_string['id'];
							break;
						}
					}
					if ( ! isset( $new_strings_in_dictionary_with_original_id[ $key ] ) ) {
						$insert_strings[ $key ] = $new_string;
					}
			}
		} else {
				foreach ( $new_strings as $key => $new_string ) {
					if ( isset( $new_string['context'] ) ) {
						$insert_strings[ $key ] = $new_string;
					}
				}
		}

		if ( ! empty( $insert_strings ) ) {
			foreach ( $insert_strings as $k => $string ) {
					if ( $hash_available ) {
						$insert_strings[ $k ] = $this->db->prepare(
							"( '%s', '%s', '%s', '%s', '%s')",
							$string['original'],
							$string['domain'],
							$string['context'],
							$string['original_plural'],
							$this->get_gettext_original_lookup_hash( $string['original'], $string['domain'], $string['context'] )
						);
					} else {
						$insert_strings[ $k ] = $this->db->prepare(
							"( '%s', '%s', '%s', '%s')",
							$string['original'],
							$string['domain'],
							$string['context'],
							$string['original_plural']
						);
					}
			}

				if ( $hash_available ) {
					$this->db->query( "INSERT IGNORE INTO `$originals_table` (original, domain, context, original_plural, lookup_hash) VALUES " . implode( ',', $insert_strings ) );
				} else {
			$this->db->query( "INSERT INTO `$originals_table` (original, domain, context, original_plural) VALUES " . implode( ',', $insert_strings ) );
				}

				$new_strings_inserted = $this->db->get_results( "SELECT id, original, domain, context FROM `$originals_table` WHERE BINARY $originals_table.original IN (" . implode( ',', $possible_new_strings ) . ")", OBJECT_K );

			foreach ( $new_strings as $key => $new_string ) {
					if ( ! isset( $new_string['context'] ) ) {
						continue;
					}

				foreach ( $new_strings_inserted as $new_string_inserted ) {
					if ( $new_string_inserted->original === $new_string['original'] &&
					     $new_string_inserted->domain === $new_string['domain'] &&
						     ( ! $use_context || $this->normalize_gettext_original_context( $new_string_inserted->context ) === $new_string['context'] )
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
	 * @return bool Whether the strings were written to the DB ( false when nothing was saved or the query failed )
     *
     *
     * How to call this functions?
     * The id and original need to be in the call as arguments, after them you can add any values you want to update:
     * $gettext_insert_update->update_gettext_strings( array(
         array(
            'id'             => $gettext_with_null_original_id_array[$key]['id'], //id
            'original'       => $gettext_with_null_original_id_array[$key]['original'], //original
            'original_id'          => $value,  // any argument you want to update in the gettext table
            )
        ), $current_language,  //a var that contains the language of the gettext table you want to update
     *  array('id', 'original', 'original_id') ); // an array where you write the values you passed as arguments, possible arguments:
     * 'id','original','translated','domain','status','original_id','plural_form'
	 */
	public function update_gettext_strings( $updated_strings, $language_code, $columns_to_update = array('id','original','translated','domain','status','plural_form')) {
		if ( count( $updated_strings ) == 0 ) {
			return false;
		}
		$placeholder_array_mapping = array(
			'id'          => '%d',
			'original'    => '%s',
			'translated'  => '%s',
			'domain'      => '%s',
			'status'      => '%d',
            'original_id' => '%d',
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

		if ( empty( $place_holders ) || empty( $values ) ) {
			return false;
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

		$result = $this->db->query( $this->db->prepare( $query . ' ', $values ) );

		$this->maybe_record_automatic_translation_error( array( 'details' => 'Error running update_gettext_strings()' ) );

		return false !== $result;
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
