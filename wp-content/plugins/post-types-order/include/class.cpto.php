<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class CPTO 
        {
            var $current_post_type = null;
            
            var $functions;
            
            var $options_interface  =   FALSE;
            
            /**
            * Constructor
            * 
            */
            function __construct() 
                {

                    $this->functions    =   new CptoFunctions();
                    $this->compatibility();
                   
                    $is_configured = get_option('CPT_configured');
                    if ($is_configured == '')
                        add_action( 'admin_notices', array ( $this, 'admin_configure_notices'));
                        
                    $this->updater();
                    
                    add_filter('init',                      array ( $this, 'on_init'));
                    
                    add_filter('pre_get_posts', array ( $this, 'pre_get_posts'));
                    add_filter('posts_orderby', array ( $this, 'posts_orderby'), 99, 2);                        
                }
                
            
            /**
            * Initialisation function
            *     
            */
            function init()
                {
                    if ( is_admin() )
                        $this->interface_init();
                    
                    add_action( 'admin_init',                               array ( $this, 'admin_init'), 10 );
                    add_action( 'admin_menu',                               array ( $this, 'add_menu') );
                    
                    add_action( 'admin_menu',                               array ( $this, 'plugin_options_menu'));
                    
                    //load archive drag&drop sorting dependencies
                    add_action( 'admin_enqueue_scripts',                    array ( $this, 'archiveDragDrop'), 10 );
                    
                    add_action( 'wp_ajax_update-custom-type-order',         array ( $this, 'saveAjaxOrder') );
                    add_action( 'wp_ajax_update-custom-type-order-archive', array ( $this, 'saveArchiveAjaxOrder') );
                    
                    add_filter( 'plugin_action_links_post-types-order/post-types-order.php',                  array ( $this,  'add_plugin_action_links') );
                    add_filter( 'network_admin_plugin_action_links_post-types-order/post-types-order.php' ,   array ( $this,  'add_plugin_action_links')  );
                
                }

            
            /**
            * On WordPress Init hook
            * This is being used to set the navigational links
            * 
            */
            function on_init()
                {
                    if( is_admin() )
                        return;
                    
                    
                    //check the navigation_sort_apply option
                    $options          =     $this->functions->get_options();
                    
                    $navigation_sort_apply   =  ( strval ( $options['navigation_sort_apply'] ) ===  "1")    ?   TRUE    :   FALSE;
                    
                    //Deprecated, rely on pto/navigation_sort_apply
                    $navigation_sort_apply   =  apply_filters('cpto/navigation_sort_apply', $navigation_sort_apply);
                    
                    $navigation_sort_apply   =  apply_filters('pto/navigation_sort_apply', $navigation_sort_apply);
                    
                    if( !   $navigation_sort_apply)
                        return;
                    
                    add_filter('get_previous_post_where',   array ( $this->functions, 'cpto_get_previous_post_where'),    99, 3);
                    add_filter('get_previous_post_sort',    array ( $this->functions, 'cpto_get_previous_post_sort')          );
                    add_filter('get_next_post_where',       array ( $this->functions, 'cpto_get_next_post_where'),        99, 3);
                    add_filter('get_next_post_sort',        array ( $this->functions, 'cpto_get_next_post_sort')              );
                
                }    
            
            
            /**
            * Compatibility with different 3rd codes
            * 
            */
            function compatibility()
                {
                    include_once( CPTPATH . '/include/class.compatibility.php');                    
                }
                
                
                
            /**
            * Update wrapper
            * 
            */
            function updater()
                {
                    
                    //no not run on plugin activation
                    if ( isset($_GET['action'])   &&  ( $_GET['action']     ==  'activate-plugin' || $_GET['action']     ==  'activate'  ) )
                        return;
                       
                    //check for update from older version
                    include_once( CPTPATH . '/include/class.updater.php');
                    
                }
                
                
            /**
            * Pre get posts filter
            * 
            * @param mixed $query
            */
            function pre_get_posts($query)
                {
                        
                    //no need if it's admin interface
                    if (is_admin())
                        return $query;
                    
                    //check for ignore_custom_sort
                    if (isset($query->query_vars['ignore_custom_sort']) && $query->query_vars['ignore_custom_sort'] === TRUE)
                        return $query; 
                    
                    //ignore if  "nav_menu_item"
                    if(isset($query->query_vars)    &&  isset($query->query_vars['post_type'])   && $query->query_vars['post_type'] ==  "nav_menu_item")
                        return $query;    
                        
                    $options          =     $this->functions->get_options();
                    
                    //if auto sort    
                    if ( strval ( $options['autosort'] ) === "1")
                        {                                    
                            //remove the supresed filters;
                            if (isset($query->query['suppress_filters']))
                                $query->query['suppress_filters'] = FALSE;    
                            
                 
                            if (isset($query->query_vars['suppress_filters']))
                                $query->query_vars['suppress_filters'] = FALSE;
                 
                        }
                        
                    return $query;
                }
            
            
            
            /**
            * Posts OrderBy filter
            * 
            * @param mixed $orderBy
            * @param mixed $query
            */
            function posts_orderby($orderBy, $query) 
                {
                    global $wpdb;
                    
                    $options          =     $this->functions->get_options();
                    
                    //check for ignore_custom_sort
                    if (isset($query->query_vars['ignore_custom_sort']) && $query->query_vars['ignore_custom_sort'] === TRUE)
                        return $orderBy;  
                    
                    //ignore the bbpress
                    if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("reply", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "reply")))
                        return $orderBy;
                    if (isset($query->query_vars['post_type']) && ((is_array($query->query_vars['post_type']) && in_array("topic", $query->query_vars['post_type'])) || ($query->query_vars['post_type'] == "topic")))
                        return $orderBy;
                        
                    //check for orderby GET paramether in which case return default data
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    if (isset($_GET['orderby']) && $_GET['orderby'] !==  'menu_order')
                        return $orderBy;
                        
                    //Avada orderby
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    if (isset($_GET['product_orderby']) && $_GET['product_orderby'] !==  'default')
                        return $orderBy;
                    
                    //check to ignore
                    /**
                    * Deprecated filter
                    * do not rely on this anymore
                    */
                    if (  apply_filters('pto/posts_orderby', $orderBy, $query )  === FALSE )
                        return $orderBy;
                        
                    $ignore =   apply_filters('pto/posts_orderby/ignore', FALSE, $orderBy, $query );
                    if( boolval( $ignore )  === TRUE )
                        return $orderBy;
                    
                    //ignore search
                    if( $query->is_search()  &&  isset( $query->query['s'] )   &&  ! empty ( $query->query['s'] ) )
                        return( $orderBy );
                        
                    //If already sorted by FIELD return as is
                    if ( preg_match('/FIELD\s*\(/i', $orderBy ))
                        return( $orderBy );
                                        
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    if ( ( is_admin() &&  !wp_doing_ajax() )    ||  ( wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments') )
                            {
                                
                                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                if ( strval ( $options['adminsort'] ) === "1" || ( wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments') )
                                    {
                                        
                                        global $post;
                                        
                                        $order  =   apply_filters('pto/posts_order', '', $query);
                                        
                                        //temporary ignore ACF group and admin ajax calls, should be fixed within ACF plugin sometime later
                                        if (is_object($post) && $post->post_type    ===  "acf-field-group"
                                                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                                                ||  (defined('DOING_AJAX') && isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'acf/') === 0))
                                            return $orderBy;
                                        
                                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended    ordPress.Security.NonceVerification.Missing     
                                        if(isset($_POST['query'])   &&  isset($_POST['query']['post__in'])  &&  is_array($_POST['query']['post__in'])   &&  count($_POST['query']['post__in'])  >   0)
                                            return $orderBy;   
                                        
                                        $orderBy = "{$wpdb->posts}.menu_order {$order}, {$wpdb->posts}.post_date DESC";
                                    }
                            }
                        else
                            {   
                                $order  =   '';
                                if ( strval ( $options['use_query_ASC_DESC'] ) === "1" )
                                    $order  =   isset($query->query_vars['order'])  ?   " " . $query->query_vars['order'] : '';
                                
                                $order  =   apply_filters('pto/posts_order', $order, $query);
                                
                                if ( strval ( $options['autosort'] ) === "1")
                                    {
                                        if(trim($orderBy) == '')
                                            $orderBy = "{$wpdb->posts}.menu_order " . $order;
                                        else
                                            $orderBy = "{$wpdb->posts}.menu_order". $order .", " . $orderBy;
                                    }
                            }

                    return($orderBy);
                }
            
            
            
            /**
            * Show the Not Configured notice
            *     
            */
            function admin_configure_notices()
                {
                    if (isset($_POST['form_submit']))
                        return;
                        
                    ?>
                        <div class="error fade">
                            <p><strong><?php esc_html_e('Post Types Order must be configured. Please go to', 'post-types-order'); ?> <a href="<?php echo esc_attr( get_admin_url() ); ?>options-general.php?page=cpto-options"><?php esc_html_e('Settings Page', 'post-types-order'); ?></a> <?php esc_html_e('make the configuration and save', 'post-types-order'); ?></strong></p>
                        </div>
                    <?php
                }
            
            
            
            /**
            * Interface object init
            * 
            */
            function interface_init()
                {
                    include (CPTPATH . '/include/class.options.php');
                    
                    $this->options_interface  =    new CptoOptionsInterface();
                    $this->options_interface->check_options_update();
                }
            
            
            /**
            * Plugin options menu
            * 
            */
            function plugin_options_menu()
                {
                     if ( ! $this->options_interface )
                        return;
                                        
                    $hookID   =     add_options_page('Post Types Order', '<img class="menu_pto" src="'. CPTURL .'/images/menu-icon.png" alt="" /> Post Types Order', 'manage_options', 'cpto-options', array( $this->options_interface, 'plugin_options_interface'));
                    add_action('admin_print_styles-' . $hookID ,    array($this, 'admin_options_print_styles'));
                }    
            
            
            /**
            * Admin options styles
            * 
            */
            function admin_options_print_styles()
                {
                    wp_register_style('pto-options', CPTURL . '/css/cpt-options.css', array(), PTO_VERSION );
                    wp_enqueue_style( 'pto-options'); 
                }
                
            
            /**
            * Load archive drag&drop sorting dependencies
            * 
            * Since version 1.8.8
            */
            function archiveDragDrop()
                {
                    $options          =     $this->functions->get_options();
                    
                                        
                    //if adminsort turned off no need to continue
                    if( strval ( $options['adminsort'] )           !==      '1')
                        return;
                    
                    $screen = get_current_screen();
                        
                    //check if the right interface
                    if( !isset( $screen->post_type )   ||  empty($screen->post_type))
                        return;
                    
                    if( isset( $screen->taxonomy ) && !empty($screen->taxonomy) )
                        return;
                    
                    if ( isset( $options['allow_reorder_default_interfaces'][$screen->post_type] )  && $options['allow_reorder_default_interfaces'][$screen->post_type] !== 'yes' )
                        return;
                        
                    if ( wp_is_mobile() || ( function_exists( 'jetpack_is_mobile' ) && jetpack_is_mobile() ) )
                        return;
                                                                
                    //if is taxonomy term filter return
                    if( is_category()    ||  is_tax() )
                        return;
                    
                    //return if use orderby columns
                    if (isset($_GET['orderby']) && $_GET['orderby'] !==  'menu_order')
                        return false;
                        
                    //return if post status filtering
                    if ( isset( $_GET['post_status'] )  &&  $_GET['post_status']    !== 'all' )
                        return false;
                        
                    //return if post author filtering
                    if (isset($_GET['author']))
                        return false;
                    
                    //load required dependencies
                    wp_enqueue_style('cpt-archive-dd', CPTURL . '/css/cpt-archive-dd.css', array(), PTO_VERSION );
                    
                    wp_enqueue_script('jquery');
                    wp_enqueue_script('jquery-ui-sortable');
                    wp_register_script('cpto', CPTURL . '/js/cpt.js', array('jquery'), PTO_VERSION, array( 'in_footer' => true ) ); 
                    
                    global $userdata;
                    
                    // Localize the script with new data
                    $CPTO_variables = array(
                                                'post_type'             =>  $screen->post_type,
                                                'archive_sort_nonce'    => wp_create_nonce( 'pto-archive-sort:' . $screen->post_type )
                                            );
                    wp_localize_script( 'cpto', 'CPTO', $CPTO_variables );

                    // Enqueued script with localized data.
                    wp_enqueue_script( 'cpto' );   
                    
                }    
            

            /**
            * Admin init
            * 
            */
            function admin_init() 
                {
                    if ( isset($_GET['page']) && substr($_GET['page'], 0, 17) === 'order-post-types-' ) 
                        {
                            $this->current_post_type = get_post_type_object ( str_replace ( 'order-post-types-', '', sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) );
                            if ( $this->current_post_type === null) 
                                {
                                    wp_die('Invalid post type');
                                }
                        }                    
                }
                
                
            
            /**
             * Check whether a post type is enabled for the dedicated Re-Order interface.
             */
            function is_reorder_interface_enabled_for_post_type( $post_type )
                {
                    $post_type = sanitize_key( $post_type );

                    if ( empty( $post_type ) )
                        return false;

                    $options        = $this->functions->get_options();
                    $menu_locations = $this->functions->get_available_menu_locations( true );

                    foreach ( $menu_locations as $location => $menu_location )
                        {
                            if (
                                empty( $menu_location['post_types'] )
                                || ! in_array( $post_type, $menu_location['post_types'], true )
                            )
                                {
                                    continue;
                                }

                            return (
                                ! isset( $options['show_reorder_interfaces'][ $location ] )
                                || $options['show_reorder_interfaces'][ $location ] === 'show'
                            );
                        }

                    return false;
                }
                
            
            
            /**
             * Save the order set through the dedicated Re-Order interface.
             */
            function saveAjaxOrder()
                {
                    global $wpdb;

                    $post_type = isset( $_POST['post_type'] )
                        ? sanitize_key( wp_unslash( $_POST['post_type'] ) )
                        : '';

                    $nonce = isset( $_POST['interface_sort_nonce'] )
                        ? sanitize_text_field( wp_unslash( $_POST['interface_sort_nonce'] ) )
                        : '';

                    if ( ! $this->is_reorder_interface_enabled_for_post_type( $post_type ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'Invalid post type.', 'post-types-order' ) ),
                                400
                            );
                        }

                    if ( ! wp_verify_nonce( $nonce, 'pto-interface-sort:' . $post_type ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'You are not allowed to access this area.', 'post-types-order' ) ),
                                403
                            );
                        }

                    if ( ! current_user_can( $this->functions->get_required_capability( $post_type ) ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'You are not allowed to reorder these items.', 'post-types-order' ) ),
                                403
                            );
                        }

                    parse_str( sanitize_text_field( wp_unslash( $_POST['order'] ) ) , $data );
                    
                    $processed_ids  =   array();
                    
                    if (is_array($data))
                        {
                            foreach($data as $key => $values ) 
                                {
                                    if ( $key === 'item' ) 
                                        {
                                            foreach( $values as $position => $id ) 
                                                {
                                                    //sanitize
                                                    $id =   intval ( $id ); 
                                                    
                                                    $data = array('menu_order' => $position);
                                                    
                                                    //Deprecated, rely on pto/save-ajax-order
                                                    $data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
                                                    
                                                    $data = apply_filters('pto/save-ajax-order', $data, $key, $id);
                                                    
                                                    $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
                                                    
                                                    $processed_ids[]    =   $id;
                                                } 
                                        } 
                                    else 
                                        {
                                            foreach( $values as $position => $id ) 
                                                {
                                                    
                                                    //sanitize
                                                    $id =   intval ( $id );
                                                    
                                                    $data = array('menu_order' => $position, 'post_parent' => str_replace('item_', '', $key));
                                                    
                                                    //Deprecated, rely on pto/save-ajax-order 
                                                    $data = apply_filters('post-types-order_save-ajax-order', $data, $key, $id);
                                                    
                                                    $data = apply_filters('pto/save-ajax-order', $data, $key, $id);
                                                    
                                                    $wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
                                                    
                                                    $processed_ids[]    =   $id;
                                                }
                                        }
                                }
                            
                        }

                    //Anything of the same post type that was left out of this batch (hidden by the 
                    //reorder screen's display cap, or a brand-new post that was never sorted) is still
                    //sitting at the default menu_order = 0. Push those to the back instead of letting
                    //them resurface at the front of the list on the next page load.
                    $this->park_unordered_items( $processed_ids );
                        
                    do_action( 'PTO/order_update_complete' );

                    CptoFunctions::site_cache_clear();

                    wp_send_json_success(
                        array( 'message' => __( 'Items Order Updated', 'post-types-order' ) )
                    );
                }
                
                
            /**
            * Park items left out of an order-save batch that are still sitting at the
            * default menu_order = 0, so they don't jump to the front of the list just
            * because 0 sorts first.
            * 
            * @param array $processed_ids IDs that were just given a real menu_order.
            */
            function park_unordered_items( $processed_ids ) 
                {
                    global $wpdb;
                    
                    $processed_ids  =   array_filter( array_map( 'intval', (array) $processed_ids ) );
                    
                    if ( empty( $processed_ids ) )
                        return;
                    
                    //every item in a single save batch belongs to the same post type
                    $post_type  =   get_post_type( reset( $processed_ids ) );
                    
                    if ( empty( $post_type ) )
                        return;
                    
                    $park_at        =   apply_filters( 'pto/park_unordered_items_at', 999999999, $post_type );
                    
                    $placeholders   =   implode( ',', array_fill( 0, count( $processed_ids ), '%d' ) );
                    
                    $sql            =   "UPDATE {$wpdb->posts}
                                        SET menu_order = %d
                                        WHERE post_type = %s
                                        AND menu_order = 0
                                        AND ID NOT IN ($placeholders)";
                    
                    $params         =   array_merge( array( $park_at, $post_type ), array_values( $processed_ids ) );
                    
                    /*
                     * Restrict the update to the current WPML language
                     * only when WPML is active.
                     */
                    if ( defined( 'ICL_SITEPRESS_VERSION' ) )
                        {
                            $current_language = apply_filters( 'wpml_current_language', null );

                            // Do not modify items if the current language is unknown
                            // or the language context includes all languages.
                            if ( ! is_string( $current_language )
                                || '' === $current_language
                                || 'all' === strtolower( $current_language ) )
                            {
                                return;
                            }

                            $translations_table = $wpdb->prefix . 'icl_translations';

                            // Get the WPML element type, e.g. "post_post" or "post_product".
                            $element_type = apply_filters( 'wpml_element_type', $post_type );

                            $sql .= " AND ID IN (
                                        SELECT element_id
                                        FROM {$translations_table}
                                        WHERE element_type = %s
                                        AND language_code = %s
                                      )";

                            $params[] = $element_type;
                            $params[] = $current_language;
                        }
                        
                        
                    /*
                     * Polylang support.
                     */
                    elseif ( function_exists( 'pll_current_language' ) )
                        {
                            $current_language = pll_current_language( 'slug' );

                            // The admin context may not expose a current language.
                            // In that case, use the language of the first processed post.
                            if ( ! is_string( $current_language ) || '' === $current_language )
                            {
                                $current_language = function_exists( 'pll_get_post_language' )
                                    ? pll_get_post_language( reset( array_values( $processed_ids ) ), 'slug' )
                                    : false;
                            }

                            if ( ! is_string( $current_language )
                                || '' === $current_language
                                || 'all' === strtolower( $current_language ) )
                            {
                                return;
                            }

                            /*
                             * Restrict the update to posts assigned to this language
                             * through Polylang's language taxonomy.
                             */
                            $sql .= " AND ID IN (
                                        SELECT tr.object_id
                                        FROM {$wpdb->term_relationships} AS tr
                                        INNER JOIN {$wpdb->term_taxonomy} AS tt
                                            ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                        INNER JOIN {$wpdb->terms} AS t
                                            ON t.term_id = tt.term_id
                                        WHERE tt.taxonomy = %s
                                        AND t.slug = %s
                                      )";

                            $params[] = 'language';
                            $params[] = $current_language;
                        }
                    /*
                     * Bogo support.
                     */
                    elseif (
                        function_exists( 'bogo_get_post_locale' )
                        && function_exists( 'bogo_get_default_locale' )
                    )
                    {
                        // Only apply language filtering to post types managed by Bogo.
                        if (
                            function_exists( 'bogo_is_localizable_post_type' )
                            && ! bogo_is_localizable_post_type( $post_type )
                        )
                        {
                            return;
                        }

                        $current_locale = false;

                        /*
                         * Prefer the explicitly selected language in the admin URL.
                         */
                        if ( isset( $_REQUEST['lang'] ) )
                        {
                            if (
                                ! is_string( $_REQUEST['lang'] )
                                || '' === trim( $_REQUEST['lang'] )
                            )
                            {
                                return;
                            }

                            $requested_language = sanitize_text_field(
                                wp_unslash( $_REQUEST['lang'] )
                            );

                            if ( 'all' === strtolower( $requested_language ) )
                                return;

                            $current_locale = function_exists( 'bogo_get_closest_locale' )
                                ? bogo_get_closest_locale( $requested_language )
                                : false;

                            // Do not guess when an explicit language cannot be resolved.
                            if ( ! is_string( $current_locale ) || '' === $current_locale )
                                return;
                        }
                        else
                        {
                            /*
                             * Otherwise, infer the language from the first processed item.
                             * This assumes the processed IDs belong to the same language.
                             */
                            $current_locale = bogo_get_post_locale(
                                reset( $processed_ids )
                            );
                        }

                        if ( ! is_string( $current_locale ) || '' === $current_locale )
                            return;

                        /*
                         * Bogo stores each post's language in the _locale post meta.
                         *
                         * Posts without this meta belong to the default language.
                         * Include those posts only when parking the default language.
                         */
                        $sql .= " AND ID IN (
                                    SELECT p.ID
                                    FROM {$wpdb->posts} AS p
                                    LEFT JOIN {$wpdb->postmeta} AS pm
                                        ON p.ID = pm.post_id
                                        AND pm.meta_key = %s
                                    WHERE (
                                        pm.meta_value = %s";

                        $params[] = '_locale';
                        $params[] = $current_locale;

                        if ( $current_locale === bogo_get_default_locale() )
                        {
                            $sql .= " OR pm.meta_id IS NULL
                                      OR pm.meta_value = ''";
                        }

                        $sql .= " )
                                  )";
                    }
                    
                          
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                }
                
                
            
            /**
             * Save the order set through the archive screen.
             */
            function saveArchiveAjaxOrder()
                {
                    global $wpdb;

                    $post_type = isset( $_POST['post_type'] )
                        ? sanitize_key( wp_unslash( $_POST['post_type'] ) )
                        : '';

                    $paged = isset( $_POST['paged'] )
                        ? max( 1, absint( $_POST['paged'] ) )
                        : 1;

                    $nonce = isset( $_POST['archive_sort_nonce'] )
                        ? sanitize_text_field( wp_unslash( $_POST['archive_sort_nonce'] ) )
                        : '';

                    $post_type_object = get_post_type_object( $post_type );

                    if ( ! $post_type_object )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'Invalid post type.', 'post-types-order' ) ),
                                400
                            );
                        }

                    if ( ! wp_verify_nonce( $nonce, 'pto-archive-sort:' . $post_type ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'You are not allowed to access this area.', 'post-types-order' ) ),
                                403
                            );
                        }

                    if ( ! current_user_can( $this->functions->get_required_capability( $post_type ) ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'You are not allowed to reorder these items.', 'post-types-order' ) ),
                                403
                            );
                        }

                    $order = isset( $_POST['order'] )
                        ? sanitize_text_field( wp_unslash( $_POST['order'] ) )
                        : '';

                    parse_str( $order, $order_data );

                    if (
                        ! is_array( $order_data )
                        || ! isset( $order_data['post'] )
                        || ! is_array( $order_data['post'] )
                    )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'Invalid order data.', 'post-types-order' ) ),
                                400
                            );
                        }
                        
                        
                    $submitted_ids = array();

                    foreach ( $order_data['post'] as $submitted_id )
                        {
                            if ( ! is_scalar( $submitted_id ) || absint( $submitted_id ) < 1 )
                                {
                                    wp_send_json_error(
                                        array( 'message' => __( 'Invalid order data.', 'post-types-order' ) ),
                                        400
                                    );
                                }

                            $submitted_ids[] = absint( $submitted_id );
                        }
           
                    
                    $submitted_ids = array_values( array_unique( array_map( 'absint', $submitted_ids ) ) );

                    $sql = "SELECT ID
                            FROM {$wpdb->posts}
                            WHERE post_type = %s
                            AND post_status IN ('publish', 'pending', 'draft', 'private', 'future', 'inherit')";

                    $sql_params = array( $post_type );

                    $language_code     = null;
                    $language_provider = null;
                    $requested_language = null;
                    $all_languages     = false;

                    // Check the language selected in the admin interface.
                    if ( isset( $_REQUEST['lang'] ) && is_string( $_REQUEST['lang'] ) )
                        {
                            $requested_language = sanitize_text_field(
                                wp_unslash( $_REQUEST['lang'] )
                            );

                            if ( strtolower( $requested_language ) === 'all' )
                                {
                                    $all_languages = true;
                                }
                        }

                    // Detect the language from the submitted items.
                    if ( ! $all_languages && ! empty( $submitted_ids ) )
                        {
                            /*
                             * WPML support.
                             */
                            if ( defined( 'ICL_SITEPRESS_VERSION' ) )
                                {
                                    $translations_table = $wpdb->prefix . 'icl_translations';

                                    $element_type = apply_filters(
                                        'wpml_element_type',
                                        $post_type
                                    );

                                    if ( is_string( $element_type ) && '' !== $element_type )
                                        {
                                            $placeholders = implode(
                                                ',',
                                                array_fill( 0, count( $submitted_ids ), '%d' )
                                            );

                                            $translation_sql = "SELECT element_id, language_code
                                                                FROM {$translations_table}
                                                                WHERE element_type = %s
                                                                AND element_id IN ($placeholders)";

                                            $translation_params = array_merge(
                                                array( $element_type ),
                                                $submitted_ids
                                            );

                                            $translation_rows = $wpdb->get_results(
                                                $wpdb->prepare(
                                                    $translation_sql,
                                                    $translation_params
                                                )
                                            );

                                            $languages_by_id = array();

                                            foreach ( $translation_rows as $row )
                                                {
                                                    $languages_by_id[ absint( $row->element_id ) ]
                                                        = $row->language_code;
                                                }

                                            // Every submitted item must have a language assignment.
                                            $all_have_language = true;

                                            foreach ( $submitted_ids as $submitted_id )
                                                {
                                                    if (
                                                        ! isset( $languages_by_id[ $submitted_id ] )
                                                        || ! is_string( $languages_by_id[ $submitted_id ] )
                                                        || '' === $languages_by_id[ $submitted_id ]
                                                    )
                                                        {
                                                            $all_have_language = false;
                                                            break;
                                                        }
                                                }

                                            if ( $all_have_language )
                                                {
                                                    $languages = array_values(
                                                        array_unique( array_values( $languages_by_id ) )
                                                    );

                                                    // Only infer a language when all submitted items
                                                    // belong to the same language.
                                                    if ( count( $languages ) === 1 )
                                                        {
                                                            $detected_language = $languages[0];

                                                            if (
                                                                null === $requested_language
                                                                || '' === $requested_language
                                                                || $requested_language === $detected_language
                                                            )
                                                                {
                                                                    $language_code = $detected_language;
                                                                    $language_provider = 'wpml';
                                                                }
                                                        }
                                                }
                                        }
                                }

                            /*
                             * Polylang support.
                             */
                            elseif ( function_exists( 'pll_get_post_language' ) )
                                {
                                    $submitted_languages = array();
                                    $all_have_language   = true;

                                    foreach ( $submitted_ids as $submitted_id )
                                        {
                                            $language = pll_get_post_language(
                                                $submitted_id,
                                                'slug'
                                            );

                                            if ( ! is_string( $language ) || '' === $language )
                                                {
                                                    $all_have_language = false;
                                                    break;
                                                }

                                            $submitted_languages[] = $language;
                                        }

                                    if ( $all_have_language )
                                        {
                                            $submitted_languages = array_values(
                                                array_unique( $submitted_languages )
                                            );

                                            if ( count( $submitted_languages ) === 1 )
                                                {
                                                    $detected_language = $submitted_languages[0];

                                                    if (
                                                        null === $requested_language
                                                        || '' === $requested_language
                                                        || $requested_language === $detected_language
                                                    )
                                                        {
                                                            $language_code = $detected_language;
                                                            $language_provider = 'polylang';
                                                        }
                                                }
                                        }
                                }
                        }

                    /*
                     * Restrict the complete ordering list to the detected language.
                     */
                    if ( 'wpml' === $language_provider )
                        {
                            $sql .= " AND ID IN (
                                        SELECT element_id
                                        FROM {$wpdb->prefix}icl_translations
                                        WHERE element_type = %s
                                        AND language_code = %s
                                    )";

                            $sql_params[] = apply_filters( 'wpml_element_type', $post_type );
                            $sql_params[] = $language_code;
                        }
                    elseif ( 'polylang' === $language_provider )
                        {
                            $sql .= " AND ID IN (
                                        SELECT tr.object_id
                                        FROM {$wpdb->term_relationships} AS tr
                                        INNER JOIN {$wpdb->term_taxonomy} AS tt
                                            ON tt.term_taxonomy_id = tr.term_taxonomy_id
                                        INNER JOIN {$wpdb->terms} AS t
                                            ON t.term_id = tt.term_id
                                        WHERE tt.taxonomy = %s
                                        AND t.slug = %s
                                    )";

                            $sql_params[] = 'language';
                            $sql_params[] = $language_code;
                        }

                    $sql .= " ORDER BY menu_order, post_date DESC";

                    $results = $wpdb->get_results(
                        $wpdb->prepare( $sql, $sql_params )
                    );

                    if ( ! is_array( $results ) || empty( $results ) )
                        {
                            wp_send_json_error(
                                array( 'message' => __( 'No items were found.', 'post-types-order' ) ),
                                400
                            );
                        }

                    $object_ids = wp_list_pluck( $results, 'ID' );
                    $object_ids = array_map( 'absint', $object_ids );

                    $per_page_key = ( $post_type === 'attachment' )
                        ? 'upload_per_page'
                        : 'edit_' . $post_type . '_per_page';

                    $objects_per_page = absint(
                        get_user_meta( get_current_user_id(), $per_page_key, true )
                    );

                    $objects_per_page = absint(
                        apply_filters( "edit_{$post_type}_per_page", $objects_per_page )
                    );

                    if ( $objects_per_page < 1 )
                        {
                            $objects_per_page = 20;
                        }

                    $edit_start_at = ( $paged - 1 ) * $objects_per_page;
            
                    // Remove any submitted IDs from the existing full list, preserving the order
                    // of everything else in $object_ids.
                    $object_ids = array_values(
                        array_filter(
                            $object_ids,
                            function ( $id ) use ( $submitted_ids ) {
                                return ! in_array( $id, $submitted_ids, true );
                            }
                        )
                    );

                    // Insert the submitted IDs at the beginning of the current page,
                    // rather than at the beginning of the entire list.
                    $insert_at = min( $edit_start_at, count( $object_ids ) );

                    array_splice( $object_ids, $insert_at, 0, $submitted_ids );

                    /*
                     * This handler renumbers the entire post-type sequence, so every
                     * affected row must be editable before any database write occurs.
                     */
                    foreach ( $object_ids as $id )
                        {
                            $post = get_post( $id );

                            if (
                                ! $post
                                || $post->post_type !== $post_type
                               // || ! current_user_can( 'edit_post', $id )     //commented out, so the editor roles can still re-order
                            )
                                {
                                    wp_send_json_error(
                                        array( 'message' => __( 'You cannot reorder one or more items in this archive.', 'post-types-order' ) ),
                                        403
                                    );
                                }
                        }

                    foreach ( $object_ids as $menu_order => $id )
                        {
                            $update_data = array( 'menu_order' => $menu_order );

                            $update_data = apply_filters(
                                'post-types-order_save-ajax-order',
                                $update_data,
                                $menu_order,
                                $id
                            );

                            $update_data = apply_filters(
                                'pto/save-ajax-order',
                                $update_data,
                                $menu_order,
                                $id
                            );

                            $wpdb->update(
                                $wpdb->posts,
                                $update_data,
                                array( 'ID' => $id )
                            );

                            clean_post_cache( $id );
                        }

                    do_action( 'PTO/order_update_complete' );

                    CptoFunctions::site_cache_clear();

                    wp_send_json_success(
                        array( 'message' => __( 'Items Order Updated', 'post-types-order' ) )
                    );
                }

                
                
            /**
            * Add the dashboard menus
            * 
            */
            function add_menu()
                {
                    include_once ( CPTPATH . '/include/class.interface.php' );

                    global $userdata;

                    $options = $this->functions->get_options();

                    $PTO_Interface = new PTO_Interface();

                    /*
                     * Get the available menu locations and post types.
                     */
                    $menu_locations = $this->functions->get_available_menu_locations( true );

                    /*
                     * Register only one Re-Order submenu per menu location.
                     */
                    $registered_menus = array();

                    foreach ( $menu_locations as $location => $menu_location )
                    {
                        if ( empty( $menu_location['menu_slug'] ) || empty( $menu_location['post_types'] ) )
                            continue;

                        /*
                         * Respect the "Show / Hide re-order interface" option.
                         *
                         * IMPORTANT:
                         * This setting is stored by MENU LOCATION, not post type.
                         */
                        if (
                            isset( $options['show_reorder_interfaces'][ $location ] )
                            && $options['show_reorder_interfaces'][ $location ] !== 'show'
                        )
                        {
                            continue;
                        }

                        $menu_slug = $menu_location['menu_slug'];

                        /*
                         * Avoid registering the same parent menu more than once.
                         */
                        if ( isset( $registered_menus[ $menu_slug ] ) )
                            continue;

                        /*
                         * Find the first valid post type that belongs to this menu.
                         */
                        $post_type_name = null;

                        foreach ( $menu_location['post_types'] as $candidate_post_type )
                        {
                            $post_type_data = get_post_type_object( $candidate_post_type );

                            if ( ! $post_type_data )
                                continue;

                            /*
                             * Do not require the post type setting here.
                             * Visibility is controlled by the MENU LOCATION setting.
                             */
                            $post_type_name = $candidate_post_type;
                            break;
                        }

                        if ( empty( $post_type_name ) )
                            continue;

                        $registered_menus[ $menu_slug ] = true;

                        /*
                         * Resolve the required capability.
                         */
                        $required_capability = $this->functions->get_required_capability( $post_type_name );

                        /*
                         * Add ONE Re-Order submenu for this menu location.
                         */
                        $hookID = add_submenu_page(
                            $menu_slug,
                            __( 'Re-Order', 'post-types-order' ),
                            __( 'Re-Order', 'post-types-order' ),
                            $required_capability,
                            'order-post-types-' . $post_type_name,
                            array( $PTO_Interface, 'sort_page' )
                        );

                        add_action(
                            'admin_print_styles-' . $hookID,
                            array( $this, 'admin_reorder_styles' )
                        );
                    }
                }
                
            
            /**
            * Admin reorder print styles
            * 
            */
            function admin_reorder_styles() 
                {
                    
                    if ( $this->current_post_type != null ) 
                        {
                            wp_enqueue_script('jQuery');
                            wp_enqueue_script('jquery-ui-sortable');
                        }
                        
                    wp_register_style('CPTStyleSheets', CPTURL . '/css/cpt.css', array(), PTO_VERSION );
                    wp_enqueue_style( 'CPTStyleSheets');
                }
                
                
                
            function add_plugin_action_links( $plugin_actions )
                {
                    $new_actions = array();

                    $new_actions['cpto_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'post-types-order' ), esc_url( admin_url( 'options-general.php?page=cpto-options' ) ) );

                    return array_merge( $new_actions, $plugin_actions );    
                }
            
            
        }
