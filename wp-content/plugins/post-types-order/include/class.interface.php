<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class PTO_Interface 
        {
            
            var $functions;
            var $CPTO;
            
            // Max number of items queried/shown in the sortable list (performance safeguard)
            var $items_limit           =   700;
            
            // Real total of items found for the current post type (set by list_pages())
            var $total_found_posts     =   0;
                        
            /**
            * Constructor
            * 
            */
            function __construct() 
                {

                    $this->functions    =   new CptoFunctions();
                    
                    global $CPTO;
                    $this->CPTO         =   $CPTO;
                    
                    // Allow the limit itself to be filtered, then keep it on the object
                    // so callers (e.g. sort_page()) can compare it against found_posts.
                    $this->items_limit = (int) apply_filters( 'pto/interface/query/limit', $this->items_limit );
                    
                    add_action( 'admin_init',                               array ( $this, 'admin_init'), 10 );
                    
                }
            
            
            
            /**
             * Resolve the current Re-Order post type and process Reset Order.
             */
            function admin_init()
                {
                    if ( ! isset( $_GET['page'] ) || substr( $_GET['page'], 0, 17 ) !== 'order-post-types-' )
                        {
                            return;
                        }

                    if ( isset( $_GET['_post_type'] ) && ! empty( $_GET['_post_type'] ) )
                        {
                            $post_type = sanitize_key( wp_unslash( $_GET['_post_type'] ) );
                        }
                    else
                        {
                            $post_type = str_replace(
                                'order-post-types-',
                                '',
                                sanitize_text_field( wp_unslash( $_GET['page'] ) )
                            );
                        }

                    $this->CPTO->current_post_type = get_post_type_object( $post_type );

                    if (
                        $this->CPTO->current_post_type === null
                        || ! $this->CPTO->is_reorder_interface_enabled_for_post_type( $post_type )
                    )
                        {
                            wp_die( 'Invalid post type' );
                        }

                    if (
                        ! isset( $_POST['pto_order_reset'] )
                        || 'true' !== sanitize_key( wp_unslash( $_POST['pto_order_reset'] ) )
                    )
                        {
                            return;
                        }

                    $submitted_post_type = isset( $_POST['_post_type'] )
                        ? sanitize_key( wp_unslash( $_POST['_post_type'] ) )
                        : '';

                    $nonce = isset( $_POST['pto_reset_nonce'] )
                        ? sanitize_text_field( wp_unslash( $_POST['pto_reset_nonce'] ) )
                        : '';

                    if ( $submitted_post_type !== $post_type )
                        {
                            wp_die(
                                esc_html__( 'Invalid post type.', 'post-types-order' ),
                                '',
                                array( 'response' => 400 )
                            );
                        }

                    if ( ! wp_verify_nonce( $nonce, 'pto-interface-reset:' . $post_type ) )
                        {
                            wp_die(
                                esc_html__( 'Invalid security token.', 'post-types-order' ),
                                '',
                                array( 'response' => 403 )
                            );
                        }

                    if ( ! current_user_can( $this->functions->get_required_capability( $post_type ) ) )
                        {
                            wp_die(
                                esc_html__( 'You are not allowed to reset this order.', 'post-types-order' ),
                                '',
                                array( 'response' => 403 )
                            );
                        }

                    global $wpdb;

                    /*
                     * The bulk SQL statement changes every post of this type.
                     * Authorize the entire write set before performing any update.
                     */
                    $post_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
                            $post_type
                        )
                    );

                    foreach ( $post_ids as $post_id )
                        {
                            if ( ! current_user_can( 'edit_post', (int) $post_id ) )
                                {
                                    wp_die(
                                        esc_html__( 'You cannot reset the order because one or more items are not editable by your account.', 'post-types-order' ),
                                        '',
                                        array( 'response' => 403 )
                                    );
                                }
                        }

                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$wpdb->posts}
                             SET menu_order = %d
                             WHERE post_type = %s",
                            0,
                            $post_type
                        )
                    );

                    apply_filters( 'pto/order_reset', $post_type );

                    CptoFunctions::site_cache_clear();

                    $redirect_page = isset( $_GET['page'] )
                        ? sanitize_key( wp_unslash( $_GET['page'] ) )
                        : 'order-post-types-' . $post_type;

                          
                    $redirect_url       =   $this->get_reorder_screen_url(
                                            $this->get_menu_slug_for_post_type( $post_type ),
                                            $redirect_page,
                                            $post_type
                                        );
                    $redirect_url       =   add_query_arg( array ( 'reset-order'    =>  'true' ), $redirect_url );
                    
                    wp_safe_redirect( $redirect_url );
                    exit;
                }


            /**
             * Find the WordPress admin parent slug for a post type's Re-Order screen.
             *
             * @param string $post_type
             * @return string
             */
            function get_menu_slug_for_post_type( $post_type )
                {
                    $menu_locations = $this->functions->get_available_menu_locations( true );

                    foreach ( $menu_locations as $menu_location )
                        {
                            if (
                                empty( $menu_location['post_types'] )
                                || ! in_array( $post_type, $menu_location['post_types'], true )
                            )
                                {
                                    continue;
                                }

                            if ( ! empty( $menu_location['menu_slug'] ) )
                                {
                                    return $menu_location['menu_slug'];
                                }
                        }

                    return '';
                }


            /**
             * Admin file and query args for the Re-Order screen.
             *
             * WordPress uses GET `post_type` to resolve the current admin parent.
             * That arg is required on `edit.php?post_type=...` menus, but on
             * `admin.php` it makes core look under the wrong parent and die with
             * "Cannot load {page}.". The type being reordered is always `_post_type`.
             *
             * @param string $menu_slug
             * @param string $page
             * @param string $selected_post_type
             * @return array{0:string,1:array}
             */
            function get_reorder_screen_query( $menu_slug, $page, $selected_post_type )
                {
                    $menu_slug = (string) $menu_slug;
                    $query     = array(
                        'page'       => $page,
                        '_post_type' => $selected_post_type,
                    );

                    if ( $menu_slug === 'upload.php' || strpos( $menu_slug, 'upload.php' ) === 0 )
                        {
                            return array( 'upload.php', $query );
                        }

                    if ( strpos( $menu_slug, 'edit.php' ) === 0 )
                        {
                            $parent_query = array();
                            $qs           = wp_parse_url( $menu_slug, PHP_URL_QUERY );

                            if ( ! empty( $qs ) )
                                {
                                    parse_str( $qs, $parent_query );
                                }

                            if ( ! empty( $parent_query['post_type'] ) )
                                {
                                    $query['post_type'] = sanitize_key( $parent_query['post_type'] );
                                }

                            return array( 'edit.php', $query );
                        }

                    return array( 'admin.php', $query );
                }


            /**
             * Full admin URL for the Re-Order screen.
             *
             * @param string $menu_slug
             * @param string $page
             * @param string $selected_post_type
             * @return string
             */
            function get_reorder_screen_url( $menu_slug, $page, $selected_post_type )
                {
                    list( $admin_file, $query ) = $this->get_reorder_screen_query(
                        $menu_slug,
                        $page,
                        $selected_post_type
                    );

                    return add_query_arg( $query, admin_url( $admin_file ) );
                }
            
                
                
            /**
            * Sort interfaces
            * 
            */
            function sort_page() 
                {
                    
                    $options          =     $this->functions->get_options();
                    $current_post_type =   $this->CPTO->current_post_type;
                    $menu_locations    =   $this->functions->get_available_menu_locations( true );
                    $current_location  =   array();

                    foreach ( $menu_locations as $menu_location )
                        {
                            if ( ! empty( $menu_location['post_types'] )
                                && in_array( $current_post_type->name, $menu_location['post_types'], true ) )
                                {
                                    $current_location = $menu_location;
                                    break;
                                }
                        }

                    $reorder_page = isset( $_GET['page'] )
                        ? sanitize_key( wp_unslash( $_GET['page'] ) )
                        : 'order-post-types-' . $current_post_type->name;

                    $reorder_menu_slug = ! empty( $current_location['menu_slug'] )
                        ? $current_location['menu_slug']
                        : $this->get_menu_slug_for_post_type( $current_post_type->name );

                    list( $reorder_admin_file, $reorder_query ) = $this->get_reorder_screen_query(
                        $reorder_menu_slug,
                        $reorder_page,
                        $current_post_type->name
                    );
                    
                    ?>
                    <div id="cpto" class="wrap">
                        <div class="icon32" id="icon-edit"><br></div>
                        <h2><?php echo esc_html( $current_post_type->labels->singular_name . ' -  '. esc_html__('Re-Order', 'post-types-order') ); ?></h2>
                        
                        <?php $this->functions->cpt_info_box(); ?>  
                        
                        <div id="ajax-response"><?php
                        
                        if ( isset ( $_GET['reset-order'] ) &&  $_GET['reset-order']    === 'true' )
                            {
                                ?><div class="notice notice-success is-dismissible pto-auto-close">
                                        <p><?php esc_html_e('The order has been successfully reset.', 'post-types-order'); ?></p>
                                    </div>

                                <script>
                                    setTimeout(function() {
                                        jQuery('.pto-auto-close').fadeOut(300, function() {
                                            jQuery(this).remove();
                                        });
                                    }, 3000);
                                </script>
                                <?php
                            }
                        
                        ?></div>
                        
                        <noscript>
                            <div class="error message">
                                <p><?php esc_html_e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'post-types-order'); ?></p>
                            </div>
                        </noscript>
                        

                        <?php if ( ! empty( $current_location['post_types'] ) ) : ?>
                            <form action="<?php echo esc_url( admin_url( $reorder_admin_file ) ); ?>" method="get" id="pto_form">
                                
                                <input type="hidden"
                                       name="page"
                                       value="<?php echo esc_attr( $reorder_page ); ?>" />

                                <?php if ( isset( $reorder_query['post_type'] ) ) : ?>
                                    <input type="hidden"
                                           name="post_type"
                                           value="<?php echo esc_attr( $reorder_query['post_type'] ); ?>" />
                                <?php endif; ?>

                                <input type="hidden"
                                       name="_post_type"
                                       value="<?php echo esc_attr( $current_post_type->name ); ?>" />

                                <?php
                                
                                if ( apply_filters( 'cpto/interface/show_available_posts', TRUE, $current_location['post_types'] ) )
                                    { 
                                
                                ?>
                                       
                                <h2 class="subtitle">
                                    <?php esc_html_e( 'Available Post Types', 'post-types-order' ); ?>
                                </h2>
                                <table cellspacing="0" class="wp-list-taxonomy">
                                    <thead>
                                        <tr>
                                            <th class="column-cb check-column" id="cb" scope="col">&nbsp;</th>
                                            <th id="author" scope="col"><?php esc_html_e( 'Post Type Title', 'post-types-order' ); ?></th>
                                            <th class="manage-column" id="categories" scope="col"><?php esc_html_e( 'Total Posts', 'post-types-order' ); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="the-list">
                                        <?php
                                            $alternate = false;
                                            foreach ( $current_location['post_types'] as $post_type_name )
                                                {
                                                    $post_type = get_post_type_object( $post_type_name );

                                                    if ( ! $post_type )
                                                        continue;

                                                    $alternate = ! $alternate;
                                                    $post_count = wp_count_posts( $post_type_name );
                                                    $total_posts = array_sum( (array) $post_count );
                                                    ?>
                                                    <tr valign="top" class="<?php echo $alternate ? 'alternate ' : ''; ?>" id="post-type-<?php echo esc_attr( $post_type_name ); ?>">
                                                        <th class="check-column" scope="row">
                                                        <input
                                                            type="radio"
                                                            onclick="pto_change_post_type(this)"
                                                            value="<?php echo esc_attr( $post_type_name ); ?>"
                                                            <?php checked( $post_type_name, $current_post_type->name ); ?> />&nbsp;</th>
                                                        <td class="categories column-categories"><b><?php echo esc_html( $post_type->label ); ?></b> (<?php echo esc_html( $post_type->labels->singular_name ); ?>)</td>
                                                        <td class="categories column-categories"><?php echo esc_html( $total_posts ); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                        ?>
                                    </tbody>
                                </table>
                                
                                <?php }  ?>
                                
                            </form>
                        <?php endif; ?>
                        
                        <br />
           
                        <div id="order-objects">
           
                            <div id="nav-menu-header">
                                <div class="major-publishing-actions">

                                        
                                        <div class="alignright actions">
                                            <p class="actions">
              
                                                <span class="img_spacer">&nbsp;
                                                    <img alt="" src="<?php echo esc_url ( CPTURL . "/images/wpspin_light.gif" ) ?>" class="waiting pto_ajax_loading" style="display: none;">
                                                </span>
                                                <a href="javascript:;" class="save-order button-primary"><?php esc_html_e('Update', 'post-types-order') ?></a>
                                            </p>
                                        </div>
                                        
                                        <div class="clear"></div>

                                </div><!-- END .major-publishing-actions -->
                            </div><!-- END #nav-menu-header -->
           
            
                            <div id="post-body"> 
                            
                                <?php
                                    // Run the (limited) query first so we know the real total before printing anything.
                                    ob_start();
                                    $this->list_pages('hide_empty=0&title_li=&post_type=' . $this->CPTO->current_post_type->name );
                                    $sortable_items = ob_get_clean();
                                    
                                    if ( $this->total_found_posts > $this->items_limit ) :
                                ?>
                                    <div class="pto-notice pto-notice--limit">
                                        <p>
                                            <?php
                                                printf(
                                                    /* translators: 1: total number of items found, 2: number of items currently shown/sortable */
                                                    esc_html__( 'This post type contains %1$d items, but for performance reasons, only the first %2$d items (based on the current order) are displayed here. Items beyond this limit cannot be reordered from this screen. Use the pto/interface/query/limit filter to adjust the number of displayed items, or consider the Advanced Post Types Order plugin and its pagination features.', 'post-types-order' ),
                                                    (int) $this->total_found_posts,
                                                    (int) $this->items_limit
                                                );
                                            ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                                <ul id="sortable" class="sortable ui-sortable">
                                
                                    <?php echo $sortable_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already sanitized with wp_kses_post() in list_pages() ?>
                                    
                                </ul>
                            </div>
                            
                            <div id="nav-menu-footer">
                                <div class="major-publishing-actions">
                                        
                                        <a class="button-primary warning" href="javascript: void(0)" onclick="confirmSubmit()"><?php esc_html_e( "Reset Order", 'post-types-order' ) ?></a>
                                
                                        <div class="alignright actions">
                                            <img alt="" src="<?php echo esc_url ( CPTURL . "/images/wpspin_light.gif" ) ?>" class="waiting pto_ajax_loading" style="display: none;">
                                            <a href="javascript:;" class="save-order button-primary"><?php esc_html_e('Update', 'post-types-order') ?></a>
                                        </div>
                                        
                                        <div class="clear"></div>

                                </div><!-- END .major-publishing-actions -->
                            </div><!-- END #nav-menu-header -->
             
                        </div>
                                       
                        <input type="hidden" id="pto-current-post-type" value="<?php echo esc_attr( $current_post_type->name ); ?>" />
                        <?php wp_nonce_field( 'pto-interface-sort:' . $current_post_type->name, 'interface_sort_nonce' ); ?>
                        
                        <script type="text/javascript">
                        
                            function confirmSubmit()
                                {
                                    var agree=confirm("<?php esc_html_e( "Are you sure you want to reset the order??", 'post-types-order' ) ?>");
                                    if (agree)
                                        {
                                            jQuery('#pto_form_order_reset').submit();   
                                        }
                                        else
                                        return false ;
                                }

                            function pto_change_post_type(element)
                                {
                                    jQuery('#pto_form input[name="_post_type"]').val(
                                        jQuery(element).val()
                                    );

                                    jQuery('#pto_form').submit();
                                }
                        
                            jQuery(document).ready(function() {
                                jQuery("#sortable").sortable({
                                    'tolerance':'intersect',
                                    'cursor':'pointer',
                                    'items':'li',
                                    'placeholder':'placeholder',
                                    'nested': 'ul'
                                });
                                
                                jQuery("#sortable").disableSelection();
                                jQuery(".save-order").bind( "click", function() {
                                    var $button = jQuery(this);

                                    if ($button.data('pto-saving')) {
                                        return;
                                    }

                                    jQuery(".save-order").data('pto-saving', true)
                                        .addClass('disabled')
                                        .attr('aria-disabled', 'true')
                                        .css('pointer-events', 'none');

                                    $button.parent().find('img').show();
                                    jQuery("html, body").animate({ scrollTop: 0 }, "fast");
                                    
                                    jQuery.ajax({
                                        url: ajaxurl,
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            action: 'update-custom-type-order',
                                            order: jQuery("#sortable").sortable("serialize"),
                                            post_type: jQuery('#pto-current-post-type').val(),
                                            interface_sort_nonce: jQuery('#interface_sort_nonce').val()
                                        }
                                    })
                                    .done(function(response) {
                                        var message = response && response.data && response.data.message
                                            ? response.data.message
                                            : '';

                                        if (!message) {
                                            return;
                                        }

                                        jQuery('#ajax-response').empty().append(
                                            jQuery('<div>', { class: response.success ? 'notice notice-success' : 'notice notice-error' }).append(
                                                jQuery('<p>').text(message)
                                            )
                                        );
                                    })
                                    .fail(function(jqXHR) {
                                        var response = jqXHR.responseJSON || {};
                                        var message = response.data && response.data.message
                                            ? response.data.message
                                            : '';

                                        if (!message) {
                                            return;
                                        }

                                        jQuery('#ajax-response').empty().append(
                                            jQuery('<div>', { class: 'notice notice-error' }).append(
                                                jQuery('<p>').text(message)
                                            )
                                        );
                                    })
                                    .always(function() {
                                        jQuery('img.pto_ajax_loading').hide();

                                        jQuery(".save-order").data('pto-saving', false)
                                            .removeClass('disabled')
                                            .removeAttr('aria-disabled')
                                            .css('pointer-events', '');
                                    });
                                });
                            });
                        </script>
                        
                        <form
                            action="<?php
                                echo esc_url(
                                    $this->get_reorder_screen_url(
                                        $reorder_menu_slug,
                                        $reorder_page,
                                        $current_post_type->name
                                    )
                                );
                            ?>"
                            method="post"
                            id="pto_form_order_reset">
                            <input type="hidden" name="pto_order_reset" value="true" />
                            <input type="hidden" name="_post_type" value="<?php echo esc_attr( $current_post_type->name ); ?>" />
                            <?php wp_nonce_field( 'pto-interface-reset:' . $current_post_type->name, 'pto_reset_nonce' ); ?>
                        </form>
                        
                        
                    </div>
                    <?php
                }

                
            /**
            * List pages
            * 
            * @param mixed $args
            */
            function list_pages($args = '') 
                {
                    $defaults = array(
                        'depth'             => -1, 
                        'date_format'       => get_option('date_format'),
                        'child_of'          => 0, 
                        'sort_column'       => 'menu_order',
                        'post_status'       =>  'any' 
                    );

                    $r = wp_parse_args( $args, $defaults );
                    extract( $r, EXTR_SKIP );

                    $output = '';
                    
                    // Query pages.
                    $r['hierarchical'] = 0;
                    
                    // Allow the limit itself to be filtered, then keep it on the object
                    // so callers (e.g. sort_page()) can compare it against found_posts.
                    
                    $args = array(
                                'sort_column'       =>  'menu_order',
                                'post_type'         =>  $post_type,
                                'posts_per_page'    =>  $this->items_limit,
                                'post_status'       =>  'any',
                                'orderby'            => array(
                                                            'menu_order'    => 'ASC',
                                                            'post_date'     =>  'DESC'
                                                            )
                    );
                    
                    //allow customisation of the query if necesarelly
                    $args   =   apply_filters('pto/interface/query/args', $args ); 
                    
                    $the_query  = new WP_Query( $args );
                    $pages      = $the_query->posts;
                    
                    // found_posts reflects the TOTAL matching rows regardless of posts_per_page,
                    // so we can warn the user even though we only fetched/display $this->items_limit of them.
                    $this->total_found_posts = (int) $the_query->found_posts;

                    if ( !empty($pages) ) 
                        {
                            $output .= $this->walk_tree($pages, $r['depth'], $r);
                        }

                    echo    wp_kses_post    (   $output );
                }
            
            /**
            * Tree walker
            * 
            * @param mixed $pages
            * @param mixed $depth
            * @param mixed $r
            */
            function walk_tree($pages, $depth, $r) 
                {
                    include_once ( CPTPATH . '/include/class.walkers.php' );
                    
                    $walker = new Post_Types_Order_Walker;

                    $args = array($pages, $depth, $r);
                    return call_user_func_array(array(&$walker, 'walk'), $args);
                }
            
            
        }

?>