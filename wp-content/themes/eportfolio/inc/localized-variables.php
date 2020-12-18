<?php
if (!function_exists('eportfolio_get_localized_variables')) :
    /**
     * Returns localized variable.
     *
     * @since 1.0.0
     *
     * return array localized variables
     */
    function eportfolio_get_localized_variables(){

        /*For Ajax Load Posts*/
        $args['nonce'] = wp_create_nonce( 'me-load-more-nonce' );
        $args['ajaxurl'] = admin_url( 'admin-ajax.php' );


        if( is_front_page() ){
            $args['post_type'] = 'post';
        }

        /*Support for custom post types*/
        if( is_post_type_archive() ){
            $args['post_type'] = get_queried_object()->name;
        }
        /**/

        /*Support for categories and taxonomies*/
        if( is_category() || is_tag() || is_tax() ){
            $args['cat'] = get_queried_object()->slug;
            $args['taxonomy'] = get_queried_object()->taxonomy;
            /*Get the associated post type for custom taxonomy*/
            if( is_tax() ){
                global $wp_taxonomies;
                $tax_object = isset( $wp_taxonomies[$args['taxonomy']] ) ? $wp_taxonomies[$args['taxonomy']]->object_type : array();
                $args['post_type'] = array_pop($tax_object);
            }
            /**/
        }
        /**/

        /*Support for search*/
        if( is_search() ){
            $args['search'] = get_search_query();
        }
        /**/

        /*Support for author*/
        if( is_author() ){
            $args['author'] = get_the_author_meta( 'user_nicename' ) ;
        }
        /**/

        /*Support for date archive*/
        if( is_date() ){
            $args['year'] = get_query_var('year');
            $args['month'] = get_query_var('monthnum');
            $args['day'] = get_query_var('day');
        }
        /**/

        return $args;
    }
endif;