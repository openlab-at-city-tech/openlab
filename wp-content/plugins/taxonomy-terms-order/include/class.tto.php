<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class TTO 
        {            
            var $functions;
            
            /**
            * Constructor
            * 
            */
            function __construct() 
                {
                    add_action('admin_enqueue_scripts',             array ( $this,  'admin_enqueue_scripts' ) );
                    
                    add_action('admin_menu',                        array ( $this,  'admin_menu' ), 99);
                    add_filter('terms_clauses',                     array ( $this,  'apply_order_filter' ), 10, 3);
                    add_filter('get_terms_orderby',                 array ( $this,  'get_terms_orderby' ), 1, 2);
                    
                    add_action( 'wp_ajax_update-taxonomy-order',    array ( $this, 'saveAjaxOrder' ) );
                    
                    add_filter( 'plugin_action_links_taxonomy-terms-order/taxonomy-terms-order.php',                  array ( $this,  'add_plugin_action_links') );
                    add_filter( 'network_admin_plugin_action_links_taxonomy-terms-order/taxonomy-terms-order.php' ,   array ( $this,  'add_plugin_action_links')  );
                    
                    if ( is_admin() )
                        TTO_functions::check_table_column();                    
                }
            
            
            /**
            * Enqueue the styles nd scripts assets
            * 
            * @param mixed $hook
            * @return mixed
            */
            function admin_enqueue_scripts( $hook )
                {
                    if ( strpos ( $hook, 'to-interface' )   === FALSE   &&  strpos ( $hook, 'settings_page_to-options' )   === FALSE )
                        return;
                        
                    wp_register_style('to.css', TOURL . '/css/to.css', array(), TTO_VERSION );
                    wp_enqueue_style( 'to.css');
                    
                    wp_enqueue_script('jquery');                    
                    wp_enqueue_script('jquery-ui-sortable');
                    wp_register_script('to-javascript', TOURL . '/js/to-javascript.js', array(), TTO_VERSION, FALSE );
                    wp_enqueue_script( 'to-javascript');
                }
                
                
            /**
            * Plugin menus
            *     
            */
            function admin_menu() 
                {
                    include (TOPATH . '/include/class.interface.php');
                    include (TOPATH . '/include/class.terms_walker.php');
                    include (TOPATH . '/include/class.options.php');
                    
                    $TTO_plugin_options =   new TTO_plugin_options(); 
                    add_options_page('Taxonomy Terms Order', '<img class="menu_tto" src="'. TOURL .'/images/menu-icon.png" alt="" />' . __('Taxonomy Terms Order', 'taxonomy-terms-order'), 'manage_options', 'to-options', array ( $TTO_plugin_options, 'plugin_options' ) );
                            
                    $options = TTO_functions::get_settings();
                                      
                    $capability = $this->get_interface_capability() ;
                            
                     //put a menu within all custom types if apply
                    $post_types = get_post_types();
                    foreach( $post_types as $post_type) 
                        {
                                
                            //check if there are any taxonomy for this post type
                            $post_type_taxonomies = get_object_taxonomies($post_type);
                            
                            foreach ($post_type_taxonomies as $key => $taxonomy_name)
                                {
                                    $taxonomy_info = get_taxonomy($taxonomy_name);  
                                    if (empty($taxonomy_info->hierarchical) ||  $taxonomy_info->hierarchical !== TRUE) 
                                        unset($post_type_taxonomies[$key]);
                                }
                                
                            if (count($post_type_taxonomies) == 0)
                                continue;
                                
                            if  ( isset ( $options['show_reorder_interfaces'][ $post_type ]) && $options['show_reorder_interfaces'][ $post_type ] == 'hide' )
                                continue;
                            
                            $TTO_Interface =    new TTO_Interface();
                            
                            $post_type_object = get_post_type_object( $post_type );

                            if ( $post_type === 'post' ) {
                                $parent_slug = 'edit.php';
                            } elseif ( $post_type === 'attachment' ) {
                                $parent_slug = 'upload.php';
                            } elseif ( $post_type_object && is_string( $post_type_object->show_in_menu ) ) {
                                // CPT is registered as a submenu of another top-level menu.
                                $parent_slug = $post_type_object->show_in_menu;
                            } else {
                                // Standard standalone CPT menu.
                                $parent_slug = 'edit.php?post_type=' . $post_type;
                            }

                            add_submenu_page(
                                                $parent_slug,
                                                __( 'Taxonomy Order', 'taxonomy-terms-order' ),
                                                __( 'Taxonomy Order', 'taxonomy-terms-order' ),
                                                $capability,
                                                'to-interface-' . $post_type,
                                                array( $TTO_Interface, 'Interface' )
                            );

                        }
                }
                
                
            function get_interface_capability() 
                {
                    $options = TTO_functions::get_settings();

                    if ( ! empty( $options['capability'] ) ) {
                        return $options['capability'];
                    }

                    if ( isset( $options['level'] ) && is_numeric( $options['level'] ) ) {
                        return TTO_functions::userdata_get_user_level();
                    }

                    return 'manage_options';
                }
                
            
            /**
            * Apply order filter
            *     
            * @param mixed $clauses
            * @param mixed $taxonomies
            * @param mixed $args
            */
            function apply_order_filter( $clauses, $taxonomies, $args)
                {
                    if ( apply_filters('to/get_terms_orderby/ignore', FALSE, $clauses['orderby'], $args) )
                        return $clauses;
                    
                    $options = TTO_functions::get_settings();
                    
                    $ignore_term_order = $args['ignore_term_order'] ?? false;
                    
                    //if admin make sure use the admin setting
                    if (is_admin())
                        {
                            
                            //return if use orderby columns
                            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                            if (isset($_GET['orderby']) && sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) !==  'term_order')
                                return $clauses;
                            
                            if ($options['adminsort'] === "1" && $ignore_term_order !== true)
                                {
                                    if ( $clauses['orderby']    ==  'ORDER BY t.name' )
                                        $clauses['orderby'] =   'ORDER BY t.term_order '. $clauses['order'] .', t.name';
                                        else
                                        $clauses['orderby'] =   'ORDER BY t.term_order';
                                    
                                }
                                
                            return $clauses;    
                        }
                    
                    //if autosort, then force the menu_order
                    if ($options['autosort'] === "1" && $ignore_term_order !== true ) 
                        {
                            $clauses['orderby'] =   'ORDER BY t.term_order';
                            
                            return $clauses;
                        }
                    
                    $rest_route = $GLOBALS['wp']->query_vars['rest_route'] ?? '';

                    // Admin/dashboard REST calls typically use these namespaces
                    $is_admin_rest = (
                                        strpos( $rest_route, '/wp/v2/' ) === 0 ||   // core WP admin REST
                                        strpos( $rest_route, '/wp-block' ) === 0      // block editor
                                    );
                        
                    if ( $is_admin_rest &&  $options['adminsort'] === "1"   &&  defined( 'REST_REQUEST' ) && REST_REQUEST )
                        {
                            $clauses['orderby'] =   'ORDER BY t.term_order';
                            
                            return $clauses; 
                        } 
                        
                    return $clauses; 
                }
                
                
            /**
            * Get terms orderby
            * 
            * @param mixed $orderby
            * @param mixed $args
            * @return mixed
            */
            function get_terms_orderby($orderby, $args)
                {
                    if ( apply_filters('to/get_terms_orderby/ignore', FALSE, $orderby, $args) )
                        return $orderby;
                        
                    if (isset($args['orderby']) && $args['orderby'] == "term_order" && $orderby != "term_order")
                        return "t.term_order";
                        
                    return $orderby;
                }
                
            
            /**
            * Save the AJAX order update
            *     
            */
            function saveAjaxOrder()
                {
                    global $wpdb; 
                    
                    if  ( ! isset ( $_POST['nonce'] ) ||  ! wp_verify_nonce( sanitize_text_field ( wp_unslash ( $_POST['nonce'] ) ), 'update-taxonomy-order' ) )
                        wp_send_json_error( array( 'message' => __( 'You are not allowed to access this area.', 'taxonomy-terms-order' ) ), 403 );
                        
                    if ( ! current_user_can( $this->get_interface_capability() ) )
                        wp_send_json_error( array( 'message' => __( 'You are not allowed to reorder these terms.', 'taxonomy-terms-order' ) ), 403 );
                     
                    $data               = isset ( $_POST['order'] )  ?   stripslashes( sanitize_text_field ( wp_unslash ( $_POST['order'] ) ) )   :   "";
                    $unserialised_data  = json_decode($data, TRUE);
                    
                    if ( ! is_array( $unserialised_data ) )
                        wp_send_json_error( array( 'message' => __( 'Invalid order data.', 'taxonomy-terms-order' ) ), 400 );
                            
                    foreach($unserialised_data as $key => $values ) 
                        {
                            //$key_parent = str_replace("item_", "", $key);
                            $items = explode("&", $values);
                            unset($item);
                            foreach ($items as $item_key => $item_)
                                {
                                    $items[$item_key] = trim(str_replace("item[]=", "",$item_));
                                }
                            
                            if (is_array($items) && count($items) > 0)
                                {
                                    foreach( $items as $item_key => $term_id ) 
                                        {
                                            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery 
                                            $wpdb->update( $wpdb->terms, array('term_order' => ($item_key + 1)), array('term_id' => $term_id) );
                                        }
                                    clean_term_cache($items);
                                } 
                        }
                        
                    do_action('tto/update-order');
                    
                    wp_cache_flush();
                        
                    wp_send_json_success( array( 'message' => __( 'Items Order Updated', 'taxonomy-terms-order' ) ) );
                }
                
                
                
            function add_plugin_action_links( $plugin_actions )
                {
                    $new_actions = array();

                    $new_actions['to_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'taxonomy-terms-order' ), esc_url( admin_url( 'options-general.php?page=to-options' ) ) );

                    return array_merge( $new_actions, $plugin_actions );    
                }
           
        }