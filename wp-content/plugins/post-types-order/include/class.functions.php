<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class CptoFunctions
        {
            
            /**
            * Return the user level
            * 
            * This is deprecated, will be removed in the next versions
            * 
            * @param mixed $return_as_numeric
            */
            function userdata_get_user_level($return_as_numeric = FALSE)
                {
                    global $userdata;
                    
                    $user_level = '';
                    for ($i=10; $i >= 0;$i--)
                        {
                            if (current_user_can('level_' . $i) === TRUE)
                                {
                                    $user_level = $i;
                                    if ($return_as_numeric === FALSE)
                                        $user_level = 'level_'.$i;    
                                    break;
                                }    
                        }        
                    return ($user_level);
                }
                
                
            
            /**
            * Resolve the capability required to use the re-order interfaces / AJAX endpoints
            * 
            * Centralises the logic previously duplicated in add_menu() and init_cpto(),
            * so the menu, the page-load gate, and the AJAX handlers can never drift apart.
            * 
            * @param string $post_type_name Optional post type, passed through to the pto/edit_capability filter
            * @return string A capability name suitable for current_user_can()
            */
            function get_required_capability($post_type_name = '')
                {
                    $options = self::get_options();

                    if (isset($options['capability']) && !empty($options['capability']))
                        {
                            $capability = $options['capability'];
                        }
                    else if (isset($options['level']) && is_numeric($options['level']))
                        {
                            $capability = $this->userdata_get_user_level();
                        }
                        else
                            {
                                $capability = 'manage_options';
                            }

                    return apply_filters('pto/edit_capability', $capability, $post_type_name);
                }
                
            
            /**
            * Retrieve the plugin options
            * 
            */
            static public function get_options()
                {
                    //make sure the vars are set as default
                    $options = get_option('cpto_options');
                    
                    $defaults   = array (
                                            'show_reorder_interfaces'           =>  array(),
                                            'allow_reorder_default_interfaces'  =>  array(),
                                            'autosort'                          =>  1,
                                            'adminsort'                         =>  1,
                                            'use_query_ASC_DESC'                =>  '',
                                            'capability'                        =>  'manage_options',
                                            'edit_view_links'                   =>  '',
                                            'navigation_sort_apply'             =>  1,
                                            'navigation_sort_revert'            =>  '',
                                            
                                        );
                    $options          = wp_parse_args( $options, $defaults );
                    
                    $options            =   apply_filters('pto/get_options', $options);
                    
                    return $options;            
                }
                
            
            /**
            * Update the plugin options
            * 
            */
            static public function update_options( $options )
                {
                    update_option('cpto_options', $options);    
                }
            
            
            /**
            * General messages box
            *     
            */
            function cpt_info_box()
                {
                    ?>
                        <div id="cpt_info_box">
                            <h4><a href="https://www.nsp-code.com/premium-plugins/advanced-post-types-order/" target="_blank"><img width="151" src="<?php echo esc_url ( CPTURL . "/images/logo.png" ) ?>" class="attachment-large size-large wp-image-36927" alt=""></a><br /><?php esc_html_e('Did you know an Advanced version of this plug-in is available, with Automatic Sorting, AI Prompts, and many more?', 'post-types-order') ?> <a target="_blank" href="https://www.nsp-code.com/premium-plugins/advanced-post-types-order/"><?php esc_html_e('Read more', 'post-types-order') ?></a></h4>
                            <p><?php esc_html_e('Check our', 'post-types-order') ?> <a target="_blank" href="https://wordpress.org/plugins/taxonomy-terms-order/">Category Order - Taxonomy Terms Order</a> <?php esc_html_e('plugin which allow to custom sort categories and custom taxonomies terms', 'post-types-order') ?> </p>
                            <p><span style="color:#CC0000" class="dashicons dashicons-megaphone" alt="f488">&nbsp;</span> <?php esc_html_e('Check our', 'post-types-order') ?> <a href="https://wordpress.org/plugins/wp-hide-security-enhancer/" target="_blank"><b>WP Hide & Security Enhancer</b></a> <?php esc_html_e('an extra layer of security for your site. It provides an easy way to protect your website’s code from being exploited by hiding your WordPress core files, themes, and plugins.', 'post-types-order') ?>.</p>
                            <p><span style="color:#CC0000" class="dashicons dashicons-megaphone" alt="f488">&nbsp;</span> <?php esc_html_e('Check our', 'post-types-order') ?> <a href="https://wordpress.org/plugins/software-license-lite/" target="_blank"><b>Software License Lite for WooCommerce</b></a> <?php esc_html_e('A centralized licensing solution for WooCommerce that manages product licenses, delivers software updates, supports ongoing maintenance, and helps protect your code.', 'post-types-order') ?>.</p>
                            <div class="clear"></div>
                        </div>
                    
                    <?php   
                }

                
            /**
            * Gte previous post WHERE
            * 
            * @param mixed $where
            * @param mixed $in_same_term
            * @param mixed $excluded_terms
            */
            function cpto_get_previous_post_where($where, $in_same_term, $excluded_terms)
                {
                    global $post, $wpdb;

                    if ( empty( $post ) )
                        return $where;
                    
                    //?? WordPress does not pass through this varialbe, so we presume it's category..
                    $taxonomy = 'category';
                    if(preg_match('/ tt.taxonomy = \'([^\']+)\'/i',$where, $match)) 
                        $taxonomy   =   $match[1];
                    
                    $_join = '';
                    $_where = '';
                    
                    if ( $in_same_term || ! empty( $excluded_terms ) ) 
                        {
                            $_join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
                            $_where = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );

                            if ( ! empty( $excluded_terms ) && ! is_array( $excluded_terms ) ) 
                                {
                                    // back-compat, $excluded_terms used to be $excluded_terms with IDs separated by " and "
                                    if ( false !== strpos( $excluded_terms, ' and ' ) ) 
                                        {
                                            _deprecated_argument( __FUNCTION__, '3.3', sprintf( esc_html__( 'Use commas instead of %s to separate excluded terms.', 'post-types-order' ), "'and'" ) );
                                            $excluded_terms = explode( ' and ', $excluded_terms );
                                        } 
                                    else 
                                        {
                                            $excluded_terms = explode( ',', $excluded_terms );
                                        }

                                    $excluded_terms = array_map( 'intval', $excluded_terms );
                                }

                            if ( $in_same_term ) 
                                {
                                    $term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

                                    // Remove any exclusions from the term array to include.
                                    $term_array = array_diff( $term_array, (array) $excluded_terms );
                                    $term_array = array_map( 'intval', $term_array );
                            
                                    $_where .= " AND tt.term_id IN (" . implode( ',', $term_array ) . ")";
                                }

                            if ( ! empty( $excluded_terms ) ) {
                                $_where .= " AND p.ID NOT IN ( SELECT tr.object_id FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.term_id IN (" . implode( ',', $excluded_terms ) . ') )';
                            }
                        }
                        
                    $current_menu_order = $post->menu_order;
                    $options            = $this->get_options();

                    $navigation_sort_revert = (strval($options['navigation_sort_revert']) === "1") ? TRUE : FALSE;
                    $navigation_sort_revert = apply_filters('pto/navigation_sort_revert', $navigation_sort_revert);

                    if ( $navigation_sort_revert )
                        {
                            /*
                             * Reverted sequence:
                             * menu_order DESC, post_date ASC
                             */
                            $navigation_where = $wpdb->prepare(
                                "(
                                    p.menu_order > %d
                                    OR (
                                        p.menu_order = %d
                                        AND p.post_date < %s
                                    )
                                )",
                                $current_menu_order,
                                $current_menu_order,
                                $post->post_date
                            );
                        }
                        else
                        {
                            /*
                             * Normal sequence:
                             * menu_order ASC, post_date DESC
                             */
                            $navigation_where = $wpdb->prepare(
                                "(
                                    p.menu_order < %d
                                    OR (
                                        p.menu_order = %d
                                        AND p.post_date > %s
                                    )
                                )",
                                $current_menu_order,
                                $current_menu_order,
                                $post->post_date
                            );
                        }

                    $where = str_replace(
                        "p.post_date < '" . $post->post_date . "'",
                        $navigation_where,
                        $where
                    );

                    return $where;
                }
            
            
            /**
            * Get the previous post sort
            *     
            * @param mixed $sort
            */
            function cpto_get_previous_post_sort($sort)
                {
                    global $post, $wpdb;
                    
                    $options          =     $this->get_options();
                    
                    $navigation_sort_revert = (strval($options['navigation_sort_revert']) === "1") ? TRUE : FALSE;
                    $navigation_sort_revert = apply_filters('pto/navigation_sort_revert', $navigation_sort_revert);
                    
                    //$sort = 'ORDER BY p.menu_order DESC, p.post_date ASC LIMIT 1';
                    $sort = $navigation_sort_revert ? 'ORDER BY p.menu_order ASC, p.post_date DESC LIMIT 1' : 'ORDER BY p.menu_order DESC, p.post_date ASC LIMIT 1';

                    return $sort;
                }

                
            /**
            * Get the next post WHERE
            * 
            * @param mixed $where
            * @param mixed $in_same_term
            * @param mixed $excluded_terms
            */
            function cpto_get_next_post_where($where, $in_same_term, $excluded_terms)
                {
                    global $post, $wpdb;

                    if ( empty( $post ) )
                        return $where;
                    
                    $taxonomy = 'category';
                    if(preg_match('/ tt.taxonomy = \'([^\']+)\'/i',$where, $match)) 
                        $taxonomy   =   $match[1];
                    
                    $_join = '';
                    $_where = '';
                                
                    if ( $in_same_term || ! empty( $excluded_terms ) ) 
                        {
                            $_join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
                            $_where = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );

                            if ( ! empty( $excluded_terms ) && ! is_array( $excluded_terms ) ) 
                                {
                                    // back-compat, $excluded_terms used to be $excluded_terms with IDs separated by " and "
                                    if ( false !== strpos( $excluded_terms, ' and ' ) ) 
                                        {
                                            _deprecated_argument( __FUNCTION__, '3.3', sprintf( esc_html__( 'Use commas instead of %s to separate excluded terms.', 'post-types-order' ), "'and'" ) );
                                            $excluded_terms = explode( ' and ', $excluded_terms );
                                        } 
                                    else 
                                        {
                                            $excluded_terms = explode( ',', $excluded_terms );
                                        }

                                    $excluded_terms = array_map( 'intval', $excluded_terms );
                                }

                            if ( $in_same_term ) 
                                {
                                    $term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

                                    // Remove any exclusions from the term array to include.
                                    $term_array = array_diff( $term_array, (array) $excluded_terms );
                                    $term_array = array_map( 'intval', $term_array );
                            
                                    $_where .= " AND tt.term_id IN (" . implode( ',', $term_array ) . ")";
                                }

                            if ( ! empty( $excluded_terms ) ) {
                                $_where .= " AND p.ID NOT IN ( SELECT tr.object_id FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.term_id IN (" . implode( ',', $excluded_terms ) . ') )';
                            }
                        }
                        
                    $current_menu_order = $post->menu_order;
                    $options            = $this->get_options();

                    $navigation_sort_revert = (strval($options['navigation_sort_revert']) === "1") ? TRUE : FALSE;
                    $navigation_sort_revert = apply_filters('pto/navigation_sort_revert', $navigation_sort_revert);

                    if ( $navigation_sort_revert )
                        {
                            /*
                             * Reverted sequence:
                             * menu_order DESC, post_date ASC
                             *
                             * Next item:
                             * - lower menu_order
                             * - or same menu_order with a newer post_date
                             */
                            $navigation_where = $wpdb->prepare(
                                "(
                                    p.menu_order < %d
                                    OR (
                                        p.menu_order = %d
                                        AND p.post_date > %s
                                    )
                                )",
                                $current_menu_order,
                                $current_menu_order,
                                $post->post_date
                            );
                        }
                        else
                        {
                            /*
                             * Normal sequence:
                             * menu_order ASC, post_date DESC
                             *
                             * Next item:
                             * - higher menu_order
                             * - or same menu_order with an older post_date
                             */
                            $navigation_where = $wpdb->prepare(
                                "(
                                    p.menu_order > %d
                                    OR (
                                        p.menu_order = %d
                                        AND p.post_date < %s
                                    )
                                )",
                                $current_menu_order,
                                $current_menu_order,
                                $post->post_date
                            );
                        }

                    $where = str_replace(
                        "p.post_date > '" . $post->post_date . "'",
                        $navigation_where,
                        $where
                    );

                    return $where;
                }

            
            /**
            * Get next post sort
            * 
            * @param mixed $sort
            */
            function cpto_get_next_post_sort($sort)
                {
                    global $post, $wpdb; 
                    
                    $options          =     $this->get_options();
                    
                    $navigation_sort_revert = (strval($options['navigation_sort_revert']) === "1") ? TRUE : FALSE;
                    $navigation_sort_revert = apply_filters('pto/navigation_sort_revert', $navigation_sort_revert);
                    
                    //$sort = 'ORDER BY p.menu_order ASC, p.post_date DESC LIMIT 1';
                    $sort = $navigation_sort_revert ? 'ORDER BY p.menu_order DESC, p.post_date ASC LIMIT 1' : 'ORDER BY p.menu_order ASC, p.post_date DESC LIMIT 1';
                    
                    return $sort;    
                }
                
                
                
            
            
            /**
             * Return the available menu locations for post types.
             *
             * When $_non_hierarhical_post_types is TRUE, only menu locations
             * containing at least one custom, non-hierarchical post type are returned.
             *
             * @param bool $_non_hierarhical_post_types
             *
             * @return array
             */
            static function get_available_menu_locations( $_non_hierarhical_post_types = true )
                {
                    global $menu;

                    $locations = array();

                    $ignore_post_types = array(
                        '/^reply$/',
                        '/^topic$/',
                        '/^report$/',
                        '/^status$/',
                        '/^acf-/',
                        '/^acfe-/',
                    );

                    $post_types = get_post_types(
                        array(
                            'show_ui' => true,
                        ),
                        'objects'
                    );

                    foreach ( $post_types as $post_type => $post_type_object )
                    {
                        /*
                         * Ignore specific post types / post type patterns.
                         */
                        foreach ( $ignore_post_types as $ignore_pattern )
                        {
                            if ( preg_match( $ignore_pattern, $post_type ) )
                                continue 2;
                        }

                        /*
                         * When filtering for non-hierarchical custom post types,
                         * ignore built-in post types and hierarchical post types.
                         */
                        if ( $_non_hierarhical_post_types === true )
                        {
                            if ( ! empty( $post_type_object->hierarchical ) )
                                continue;
                        }

                        $show_in_menu = $post_type_object->show_in_menu;

                        if ( $show_in_menu === false )
                            continue;

                        /*
                         * Determine the menu slug.
                         */
                        if ( $show_in_menu === true )
                        {
                            if ( $post_type === 'post' )
                                $menu_slug = 'edit.php';
                            elseif ( $post_type === 'attachment' )
                                $menu_slug = 'upload.php';
                            else
                                $menu_slug = 'edit.php?post_type=' . $post_type;
                        }
                        else
                        {
                            /*
                             * The post type is attached to another top-level menu.
                             */
                            $menu_slug = $show_in_menu;
                        }

                        if ( empty( $menu_slug ) )
                            continue;

                        /*
                         * If this menu was already found, add this post type
                         * to the existing location instead of creating a duplicate.
                         */
                        if ( isset( $locations[ $menu_slug ] ) )
                        {
                            $locations[ $menu_slug ]['post_types'][] = $post_type;

                            continue;
                        }

                        /*
                         * Try to retrieve the actual top-level menu title.
                         */
                        $menu_title = '';

                        foreach ( $menu as $menu_item )
                        {
                            if ( ! isset( $menu_item[2] ) )
                                continue;

                            if ( $menu_item[2] !== $menu_slug )
                                continue;

                            $menu_title = isset( $menu_item[0] )
                                ? $menu_item[0]
                                : '';

                            break;
                        }

                        /*
                         * Fall back to the post type label.
                         */
                        if ( empty( $menu_title ) )
                        {
                            $menu_title = $post_type_object->labels->menu_name;
                        }

                        /*
                         * Clean the menu title.
                         */
                        $tags = array( 'p', 'span' );

                        $menu_title = preg_replace(
                            '#<(' . implode( '|', $tags ) . ')[^>]+>.*?</\1>#s',
                            '',
                            $menu_title
                        );

                        $menu_title = trim(
                            wp_strip_all_tags( $menu_title )
                        );

                        $locations[ $menu_slug ] = array(
                            'slug'       => sanitize_title( $menu_slug ),
                            'name'       => $menu_title,
                            'post_type'  => $post_type,
                            'post_types' => array( $post_type ),
                            'menu_slug'  => $menu_slug,
                        );
                    }

                    $locations  =   apply_filters( 'pto/available_menu_locations', $locations );
                    
                    return $locations;
                }

            
            
            /**
            * Clear any cache plugins
            *     
            */
            static public function site_cache_clear()
                {
                    wp_cache_flush();
                    
                    $cleared_cache  =   FALSE;
                    
                    if ( function_exists('wp_cache_clear_cache'))
                        {
                            wp_cache_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( function_exists('w3tc_flush_all'))
                        {
                            w3tc_flush_all();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('opcache_reset')    &&  ! ini_get( 'opcache.restrict_api' ) )
                        {
                            @opcache_reset();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( function_exists( 'rocket_clean_domain' ) )
                        {
                            rocket_clean_domain();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('wp_cache_clear_cache')) 
                        {
                            wp_cache_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                
                    global $wp_fastest_cache;
                    if ( method_exists( 'WpFastestCache', 'deleteCache' ) && !empty( $wp_fastest_cache ) )
                        {
                            $wp_fastest_cache->deleteCache();
                            $cleared_cache  =   TRUE;
                        }
                
                    //If your host has installed APC cache this plugin allows you to clear the cache from within WordPress
                    if ( function_exists('apc_clear_cache'))
                        {
                            apc_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('fvm_purge_all'))
                        {
                            fvm_purge_all();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( class_exists( 'autoptimizeCache' ) )     
                        {
                            autoptimizeCache::clearall();
                            $cleared_cache  =   TRUE;
                        }

                    //WPEngine
                    if ( class_exists( 'WpeCommon' ) ) 
                        {
                            if ( method_exists( 'WpeCommon', 'purge_memcached' ) )
                                WpeCommon::purge_memcached();
                            if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) )
                                WpeCommon::clear_maxcdn_cache();
                            if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) )
                                WpeCommon::purge_varnish_cache();
                            
                            $cleared_cache  =   TRUE;
                        }
                        
                    if (class_exists('Cache_Enabler_Disk') && method_exists('Cache_Enabler_Disk', 'clear_cache'))
                        {
                            Cache_Enabler_Disk::clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                        
                    //Perfmatters
                    if ( class_exists('Perfmatters\CSS') && method_exists('Perfmatters\CSS', 'clear_used_css') )
                        {
                            Perfmatters\CSS::clear_used_css();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( defined( 'BREEZE_VERSION' ) )
                        {
                            do_action( 'breeze_clear_all_cache' );
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('sg_cachepress_purge_everything'))
                        {
                            sg_cachepress_purge_everything();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( defined ( 'FLYING_PRESS_VERSION' ) )
                        {
                            do_action('flying_press_purge_everything:before');

                            @unlink(FLYING_PRESS_CACHE_DIR . '/preload.txt');

                            // Delete all files and subdirectories
                            FlyingPress\Purge::purge_everything();

                            @mkdir(FLYING_PRESS_CACHE_DIR, 0755, true);

                            do_action('flying_press_purge_everything:after');
                            
                            $cleared_cache  =   TRUE;
                        }
                        
                    if (class_exists('\LiteSpeed\Purge'))
                        {
                            \LiteSpeed\Purge::purge_all();
                            $cleared_cache  =   TRUE;
                        }
                        
                    return $cleared_cache;
                        
                }    
                
        }