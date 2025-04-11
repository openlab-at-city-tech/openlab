<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Search
 *
 * Queries for translations in custom trp tables.
 *
 */
class TRP_Search extends WP_Query{

    protected $settings;
    protected $db;


    /**
     * TRP_Search constructor.
     * @param $settings
     */
    public function __construct( $settings ){


        parent::__construct('');

        global $wpdb;
        $this->db = $wpdb;
        $this->settings = $settings;
    }

    /**
     * Filter function to replace the search results on other languages. Basically we destroy the search query by unseting the s query var and give it post__in argument with the
     * results from our own query
     * @param $query
     * @return mixed
     */
    public function trp_search_filter( $query ) {
        global $TRP_LANGUAGE;

        if ( $TRP_LANGUAGE !== $this->settings['default-language'] ) {
            if ( ( !is_admin() && $query->is_main_query() && $query->is_search() ) || apply_filters( 'trp_force_search', false ) ) {

                // Get the "s" query arg from the initial search
                $search_query = get_query_var('s');
                //in some cases for instance some ajax searches we might need to get it from the query
                if( empty($search_query) && !empty( $query->query['s'] ) )
                    $search_query = $query->query['s'];

                $search_result_ids = $this->get_post_ids_containing_search_term($search_query, $query);

                if( !empty($search_result_ids) ) {
                    $query->set('s', '');
                    $query->set('post__in', $search_result_ids);
                }
            }
        }

        return $query;
    }

    public function get_post_ids_containing_search_term($search_query, $query = null ){
        global $TRP_LANGUAGE;
        /* start adapted from parse_search() function from WP_Query */

        // added slashes screw with quote grouping when done early, so done later
        $search_query = stripslashes( $search_query );
        // there are no line breaks in <input /> fields
        $search_query                  = str_replace( array( "\r", "\n" ), '', $search_query );
        $search_terms_count = 1;

        if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $search_query, $matches ) ) {
            $search_terms_count = count( $matches[0] );
            $search_terms       = $this->parse_search_terms( $matches[0] );
            // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
            if ( empty( $search_terms ) || count( $search_terms ) > 9 ) {
                $search_terms = array( $search_query );
                $search_terms_count = 1;
            }
        } else {
            $search_terms = array( $search_query );
        }
        /* end adapted from parse_search() function from WP_Query */


        $trp = TRP_Translate_Press::get_trp_instance();
        if ( ! $this->trp_query ) {
            $this->trp_query = $trp->get_component( 'query' );
        }

        $search_result_ids = array();
        $trp_search_query = '';
        $dictionary_name = $this->trp_query->get_table_name( apply_filters( 'trp_change_search_dictionary_language', $TRP_LANGUAGE, $this, $query ) );
        $meta_table_name =  $this->trp_query->get_table_name_for_original_meta();

        if( $search_terms_count === 1 ){
            /**
             * for one search term we can find it directly in the translated column
             */
            $trp_search_query = $this->db->prepare( "SELECT meta_value FROM
                                        $dictionary_name
                                        INNER JOIN $meta_table_name ON $dictionary_name.original_id = $meta_table_name.original_id AND $meta_table_name.meta_key = '". $this->trp_query->get_meta_key_for_post_parent_id() ."'
                                        WHERE $dictionary_name.translated LIKE %s", '%' . $search_terms[0] . '%' );
        }
        else{
            $where_terms_or = array();
            $where_terms_and = array();
            foreach ( $search_terms as $search_term ){
                $where_terms_or[] = $this->db->prepare("$dictionary_name.translated LIKE %s", '%' . $search_term . '%');
                $where_terms_and[] = $this->db->prepare("t1.tra LIKE %s", '%' . $search_term . '%');
            }

            $where_or = implode( ' OR ', $where_terms_or);
            $where_and = implode( ' AND ', $where_terms_and);

            /**
             * in the inner SELECT we search for the translated strings in dictionaries that have either of the search terms ( OR )
             * and their original strings belong to the same post_id and we combine them in a virtual column with GROUP_CONCAT()
             * basically we recreate the translated post_content but in a random order (we don't care about the order of the strings)
             * in the outer SELECT we search in the result from inner SELECT the values that have all the search terms (AND)
             */
            $trp_search_query = "SELECT meta_value FROM 
                                            ( SELECT meta_value, GROUP_CONCAT( translated SEPARATOR ' ' ) AS tra FROM $dictionary_name 
                                            INNER JOIN $meta_table_name ON $dictionary_name.original_id = $meta_table_name.original_id AND $meta_table_name.meta_key = '". $this->trp_query->get_meta_key_for_post_parent_id() ."' 
                                            WHERE ( ". $where_or ." ) GROUP BY meta_value ) AS t1 
                                         WHERE ( ".$where_and." )";

        }

        $search_result_ids = $this->db->get_results( $trp_search_query, OBJECT_K );
        $search_result_ids = array_keys($search_result_ids);
        return $search_result_ids;
    }

    /**
     * In our search filter we unset the s variable from the query so we need to recreate it later from the $_GET
     * @param $s string the search query var
     * @return string
     */
    public function trp_search_query( $s ){
        global $TRP_LANGUAGE;

        if ( $TRP_LANGUAGE !== $this->settings['default-language'] ) {
            if ( !is_admin() && isset( $_GET['s'] ) && empty( $s )  ){
                $s = sanitize_text_field( wp_unslash( $_GET['s'] ) );
            }
        }

        return $s;
    }
}
