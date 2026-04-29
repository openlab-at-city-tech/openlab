<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_String_Translation_Helper {
	/* @var TRP_Query */
	protected $trp_query;
	/* @var TRP_String_Translation */
	protected $string_translation;
	protected $settings;


	/** Functions used by regular, gettext and slugs from SEO Pack */
	public function check_ajax( $type, $action ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
				wp_die( -1, 403 );
			}
            $handle_suffix = ( $action !== 'delete' ) ? '_' . $type : '';
			check_ajax_referer( 'string_translation_' . $action . '_strings' . $handle_suffix, 'security' );

            $map = [ 'save' => 'trp_save_translations_', 'get' => 'trp_string_translation_get_strings_', 'delete' => 'trp_string_translation_delete_' ];
			if ( isset( $_POST['action'] ) && $_POST['action'] === $map[ $action ] . $type ) {
				return true;
			}
		}
		wp_die();
	}

	public function get_sanitized_query_args( $string_type ) {
		$trp = TRP_Translate_Press::get_trp_instance();
		if ( ! $this->string_translation ) {
			$this->string_translation = $trp->get_component( 'string_translation' );
		}
		if ( ! $this->trp_query ) {
			$this->trp_query = $trp->get_component( 'query' );
		}
		if ( ! $this->settings ) {
			$trp_settings   = $trp->get_component( 'settings' );
			$this->settings = $trp_settings->get_settings();
		}
		$query_args   = array();
		$posted_query = ( empty( $_POST['query'] ) ) ? array() : json_decode( stripslashes( $_POST['query'] ), true ); /* phpcs:ignore */ /* sanitized below */

		// translation status
		$translation_status_filters = $this->string_translation->get_translation_status_filters();
		$query_args['status']       = array();
		foreach ( $translation_status_filters['translation_status'] as $translation_status_key => $value ) {
			if ( ! empty( $posted_query[ $translation_status_key ] ) && ( $posted_query[ $translation_status_key ] === true || $posted_query[ $translation_status_key ] === 'true' ) ) {
				$constant_func_name     = 'get_constant_' . $translation_status_key;
				$query_args['status'][] = $this->trp_query->$constant_func_name();
			}
		}
		if ( count( $query_args['status'] ) === 3 ) {
			// if all 3 states are true then consider the query as if the no special translation status requirement was requested
			$query_args['status'] = array();
		}

		// search string - sanitize but don't escape (escaping happens in wpdb->prepare)
		$query_args['s'] = ( empty( $posted_query['s'] ) ) ? '' : trim( sanitize_text_field( $posted_query['s'] ) );

		// page
		$query_args['page'] = ( empty( $posted_query['page'] ) ? 1 : ( ( intval( $posted_query['page'] ) < 1 ) ? 1 : intval( $posted_query['page'] ) ) );

		// language
		$query_args['language'] = ( ! empty( $posted_query['language'] ) && in_array( $posted_query['language'], $this->settings['translation-languages'] ) ) ?
			$posted_query['language'] : '';

		// order
		$query_args['order']   = ( empty( $posted_query['order'] ) || ! in_array( $posted_query['order'], array(
				'asc',
				'desc'
			) ) ) ? '' : sanitize_text_field( $posted_query['order'] );
		$query_args['orderby'] = ( empty( $posted_query['orderby'] ) ) ? '' : sanitize_text_field( $posted_query['orderby'] );


		// specific filters for each string type
		$string_types                = $this->string_translation->get_string_types();
		$specific_string_type_config = $string_types[ $string_type ];
		foreach ( $specific_string_type_config['filters'] as $specific_filter_key => $specific_filter_values ) {
            //check if filter domain is selected and assign the domain value
            if ( $specific_filter_key=='domain' && !empty($posted_query['domain'])){
                $specific_filter_values = $this->string_translation->get_gettext_domains();
            }
			$query_args[ $specific_filter_key ] =
				( ! empty( $posted_query[ $specific_filter_key ] ) && isset( $specific_filter_values[ $posted_query[ $specific_filter_key ] ] ) ) ?
					$posted_query[ $specific_filter_key ] : '';
		}


		return apply_filters( 'trp_sanitized_query_args', $query_args, $string_type, $string_types );
	}


	/** Functions used for regular and gettext */
	public function add_where_clauses_to_query( $query, $where_clauses ) {
		if ( count( $where_clauses ) > 0 ) {
			$query .= 'WHERE ';
			foreach ( $where_clauses as $where_clause ) {
				$query .= $where_clause . ' AND ';
			}
			$query = rtrim( $query, ' AND' ) . ' ';
		}

		return $query;
	}

	public function get_language_table_column_based_query_for_filters( $filters, $translation_languages, $sanitized_args ) {
		$where_clauses = array();
		foreach ( $filters as $column_name => $filter_name ) {
			if ( ! empty( $sanitized_args[ $filter_name ] ) ) {
				$column_query = '( ';
				foreach ( $translation_languages as $language ) {
					$column_query .= $this->get_column_query( $column_name, $sanitized_args[ $filter_name ], esc_sql( sanitize_text_field( $language ) ) ) . ' OR ';
				}
				$column_query    = rtrim( $column_query, ' OR ' ) . ' ) ';
				$where_clauses[] = $column_query;
			}
		}

		return $where_clauses;
	}

	public function get_column_query( $column_name, $column_values, $language ) {
		$query = '';

		if ( is_array( $column_values ) ) {
			foreach ( $column_values as $value ) {
				$query .= $language . '.' . $column_name . ' = ' . $value . ' OR ';
			}
		} else {
			$query .= $language . '.' . $column_name . ' = ' . $column_values . ' OR ';
		}
		$query = rtrim( $query, ' OR ' );

		return $query;
	}

	public function get_join_language_table_sql( $table_name, $language ) {
		return 'LEFT JOIN ' . $table_name . ' AS ' . $language . ' ON ' . $language . '.original_id = original_strings.id ';
	}

	public function get_join_meta_table_sql( $table_name ) {
		return 'LEFT JOIN ' . $table_name . ' AS original_meta ON original_meta.original_id = original_strings.id ';
	}

	/**
	 * Parse search input for exact match detection
	 *
	 * Detects if the search term is wrapped in quotes (plain or escaped) for exact matching.
	 * Supports both "term" and \"term\" formats.
	 *
	 * @param string $search_input The raw search input from user
	 * @return array {
	 *     Array containing parsed search information
	 *
	 *     @type bool   $is_exact_match Whether this is an exact match search (quoted)
	 *     @type string $search_term    The cleaned search term without quotes
	 * }
	 */
	public function parse_search_input( $search_input ) {
		$is_exact_match = false;
		$search_term = $search_input;

		if ( strlen( $search_input ) >= 2 ) {
			// Check for escaped quotes \"...\"
			if ( substr( $search_input, 0, 2 ) === '\"' && substr( $search_input, -2 ) === '\"' ) {
				$is_exact_match = true;
				$search_term = substr( $search_input, 2, -2 );
			}
			// Check for plain quotes "..."
			elseif ( isset( $search_input[0] ) && $search_input[0] === '"' && $search_input[ strlen( $search_input ) - 1 ] === '"' ) {
				$is_exact_match = true;
				$search_term = substr( $search_input, 1, -1 );
			}
		}

		return array(
			'is_exact_match' => $is_exact_match,
			'search_term'    => $search_term
		);
	}

	/**
	 * Used by regular and gettext strings for returning original ids matching filters
	 *
	 * @param $type
	 * @param $original_table
	 * @param $original_meta_table
	 * @param $get_table_name_func
	 * @param $filters
	 *
	 * @return array array( 'original_ids' => $original_ids, 'total_item_count' => $total_item_count );
	 */
	public function get_originals_results( $type, $original_table, $original_meta_table, $get_table_name_func, $filters ) {

		$this->check_ajax( $type, 'get' );

        global $wpdb;

        $trp                = TRP_Translate_Press::get_trp_instance();
		$string_translation = $trp->get_component( 'string_translation' );
		$trp_query          = $trp->get_component( 'query' );
		$trp_settings       = $trp->get_component( 'settings' );
		$settings           = $trp_settings->get_settings();
		$config             = $string_translation->get_configuration_options();
		$sanitized_args     = $this->get_sanitized_query_args( $type );
		$where_clauses      = array();


		if ( ! empty( $sanitized_args['translation-block-type'] ) ) {
			$mapping_array                            = array(
				'individual_string' => 0,
				'translation_block' => 1
			);
			$sanitized_args['translation-block-type'] = $mapping_array[ $sanitized_args['translation-block-type'] ];
		}

		// language filter
		if ( empty( $sanitized_args['language'] ) ) {
			// all language tables are needed for table joining
			$translation_languages = array();
			foreach ( $settings['translation-languages'] as $language ) {
				// regular strings don't have default language table. English language does not react to "Not translated/Manually/Automatically" if no specific language is selected
				if ( $language === $settings['default-language'] && $type === 'regular' || ( $type === 'gettext' && $this->string_starts_with($language, 'en') ) ) {
					continue;
				}
				$translation_languages[] = $language;
			}
		} else {
			// only current language is needed for table joining
			$translation_languages = array( $sanitized_args['language'] );
		}

		$counting_query = "SELECT COUNT(*) ";
		$results_query  = "SELECT DISTINCT original_strings.id, original_strings.original ";
		$results_query  .= ( $type === 'gettext' ) ? ', original_strings.domain, original_strings.context, original_strings.original_plural ' : '';
		$query          = "FROM `" . sanitize_text_field( $original_table ) . "` AS original_strings ";

		if ( ( ! empty( $sanitized_args['status'] ) || ! empty( $sanitized_args['translation-block-type'] ) ) && empty( $sanitized_args['s'] ) ) {

			// joining translation tables is needed only when we have filter for translation status or for translation block type
			foreach ( $translation_languages as $language ) {
				$query .= $this->get_join_language_table_sql( sanitize_text_field( $trp_query->$get_table_name_func( $language ) ), esc_sql( sanitize_text_field( $language ) ) );
			}

			// translation status and block type
			$where_clauses = array_merge( $where_clauses,
				$this->get_language_table_column_based_query_for_filters( $filters, $translation_languages, $sanitized_args ) );
		}

		// original_meta table only needed when filter by type is set
		if ( ! empty( $sanitized_args['type'] ) && $sanitized_args['type'] !== 'trp_default' ) {
			$query .= $this->get_join_meta_table_sql( $original_meta_table );
		}

		// Filter by type ( email )
		if ( ! empty( $sanitized_args['type'] ) && $sanitized_args['type'] !== 'trp_default' ) {
			if ( $sanitized_args['type'] === 'email' ) {
				$where_clauses[] = "original_meta.meta_key='in_email' and original_meta.meta_value = 'yes' ";
			}
		}

		// search
		if ( ! empty( $sanitized_args['s'] ) ) {
            // Use helper method to parse search input for exact match detection
            $search_data = $this->parse_search_input( $sanitized_args['s'] );
            $is_exact_match = $search_data['is_exact_match'];
            $search_term = $search_data['search_term'];

            // Properly escape the search term for SQL
            $search_term_escaped = esc_sql( $search_term );

            $search = [
                'queries' => [],
                'clauses' => []
            ];

            foreach ( $translation_languages as $language ){
                $table = $trp_query->$get_table_name_func( $language );

                $search['queries'][ $language ] = $results_query . "FROM `" . sanitize_text_field( $original_table ) . "` AS original_strings "
                    . "LEFT JOIN $table AS $language ON $language.original_id = original_strings.id ";

                if ( ! empty( $sanitized_args['type'] ) && $sanitized_args['type'] !== 'trp_default' ) {
                    // Ensure we also join the meta table for language-specific search queries when filtering by type (e.g., 'email')
                    $search['queries'][ $language ] .= $this->get_join_meta_table_sql( $original_meta_table );
                }

                // Use exact match or partial match based on quotes
                if ( $is_exact_match ) {
                    $language_clauses = ["$language.translated = '$search_term_escaped'"];
                } else {
                    // Use esc_like to escape special LIKE wildcards (%, _, \)
                    $search_term_like = '%' . $wpdb->esc_like( $search_term_escaped ) . '%';
                    $language_clauses = ["$language.translated LIKE '$search_term_like'"];
                }

                if ( ! empty( $sanitized_args['status'] ) ) {
                    $status_array = array_map( function( $status ) use ( $language ){
                        return "$language.status = $status";
                    }, $sanitized_args['status'] );

                    $status_clause = implode( ' OR ', $status_array );

                    $language_clauses[] = "($status_clause)";
                }

                if ( ! empty( $sanitized_args['translation-block-type'] ) ) {
                    $block_type = $sanitized_args['translation-block-type'];

                    $language_clauses[] = "$language.block_type = $block_type";
                }

                $search['clauses'][$language] = array_merge( $where_clauses, $language_clauses );
            }

            // Use exact match or partial match for originals based on quotes
            if ( $is_exact_match ) {
                $where_clauses[] = "(original_strings.original = '$search_term_escaped' )";
            } else {
                // Use esc_like to escape special LIKE wildcards (%, _, \)
                $search_term_like = '%' . $wpdb->esc_like( $search_term_escaped ) . '%';
                $where_clauses[] = "(original_strings.original LIKE '$search_term_like' )";
            }
		}

		if ( ! empty( $sanitized_args['domain'] ) ) {
			$domain_escaped = esc_sql( $sanitized_args['domain'] );
			$domain_like = '%' . $wpdb->esc_like( $domain_escaped ) . '%';
			$where_clauses[] = "(original_strings.domain LIKE '$domain_like' )";
		}

		$query = $this->add_where_clauses_to_query( $query, $where_clauses );

        if ( isset( $search ) ) {
            foreach ( $search['queries'] as $language => &$search_query ) {
                $search_query = $this->add_where_clauses_to_query( $search_query, $search['clauses'][$language] );
            }

            $search['queries'] = array_merge( $search['queries'], [$results_query . $query] );
            $query             = implode( ' UNION ', $search['queries'] );
        }

		$counting_query .= isset( $search ) ? "FROM ($query) as union_query" : $query;

		// order by
		if ( ! empty( $sanitized_args['orderby'] ) ) {
			if ( $sanitized_args['orderby'] === 'original' ) {
                // When using UNION (search), can't use table-qualified column names in ORDER BY
                $column_name = isset( $search ) ? $sanitized_args['orderby'] : 'original_strings.' . $sanitized_args['orderby'];
                $order_clause = 'ORDER BY ' . $column_name . ' ' . $sanitized_args['order'] . ' ';

				$query .= $order_clause;
			}
		}

		// pagination
		$query .= 'LIMIT ' . ( $sanitized_args['page'] - 1 ) * $config['items_per_page'] . ', ' . $config['items_per_page'];

        $total_item_count = $wpdb->get_var( $counting_query );
		$original_ids     = array();
		$originals        = array();
		if ( $total_item_count > 0 ) {

			// query search to retrieve IDs of original strings needed
			$results_query = isset( $search ) ? $query : $results_query . $query;
			$originals     = $wpdb->get_results( $results_query, OBJECT_K );
			$original_ids  = array_keys( $originals );
		}

		return array(
			'original_ids'     => $original_ids,
			'originals'        => $originals,
			'total_item_count' => $total_item_count
		);

	}

	private function string_starts_with($haystack, $needle){
		return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
	}

    public function get_original_ids_from_post_request() {
        $trp = TRP_Translate_Press::get_trp_instance();
        if ( !$this->settings ) {
            $trp_settings   = $trp->get_component( 'settings' );
            $this->settings = $trp_settings->get_settings();
        }

        $all_strings = json_decode( stripslashes( $_POST['strings'] ), true ); //phpcs:ignore
        $ids         = [];
        foreach ( $all_strings as $string ) {
            if ( !empty( $string['originalId'] ) ) {
                $ids[] = (int)$string['originalId'];
            } else {
                foreach ( $this->settings['translation-languages'] as $language ) {
                    if ( $this->settings['default-language'] == $language ) {
                        continue;
                    }
                    if ( isset( $string['translationsArray'][ $language ]['original_id'] ) && (int)$string['translationsArray'][ $language ]['original_id'] > 0 ) {
                        $ids[] = (int)$string['translationsArray'][ $language ]['original_id'];
                        // all languages have identical original table id
                        break;
                    };
                }
            }
        }
        return $ids;
    }
}
