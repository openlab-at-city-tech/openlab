<?php
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class TTO_functions
        {
            /**
            * Return default plugin options
            * 
            */
            static public function get_settings()
                {
                    
                    $settings = get_option('tto_options'); 
                    
                    $defaults   = array (
                                            'show_reorder_interfaces'   =>  array(),
                                            'capability'                =>  'manage_options',
                                            'autosort'                  =>  '1',
                                            'adminsort'                 =>  '1'
                                        );
                    $settings          = wp_parse_args( $settings, $defaults );
                    
                    return $settings;   
                    
                }
                
                
            /**
            * @desc 
            * 
            * Return UserLevel
            * 
            */
            static public function userdata_get_user_level($return_as_numeric = FALSE)
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
                
                
            static public function check_table_column()
                {
                    global $wpdb;
                    
                    //check if the menu_order column exists;
                    $query = "SHOW COLUMNS FROM $wpdb->terms 
                                LIKE 'term_order'";
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared WordPress.DB.DirectDatabaseQuery.DirectQuery
                    $result = $wpdb->query( $query );
                    
                    if ($result == 0)
                        {
                            $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
                            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared WordPress.DB.DirectDatabaseQuery.DirectQuery
                            $result = $wpdb->query($query);
                        }
                }
             
                
            static public function info_box()
                {
                    ?>
                        <div id="cpt_info_box">                   
                            <h4><a href="https://www.nsp-code.com/premium-plugins/advanced-taxonomy-terms-order/" target="_blank"><img width="151" src="<?php echo esc_url ( TOURL . "/images/logo.png" ) ?>" class="attachment-large size-large wp-image-36927" alt=""></a><br /><?php esc_html_e( "Did you know there is an Advanced Version of this plug-in?", 'taxonomy-terms-order' ) ?> <a target="_blank" href="https://www.nsp-code.com/premium-plugins/advanced-taxonomy-terms-order/"><?php esc_html_e( "Read more", 'taxonomy-terms-order' ) ?></a></h4>
                            <p><?php esc_html_e( "Check our", 'taxonomy-terms-order' ) ?> <a target="_blank" href="https://wordpress.org/plugins/post-types-order/">Post Types Order</a> <?php esc_html_e( "plugin which allows to custom sort all posts, pages, custom post types", 'taxonomy-terms-order' ) ?> </p>

                            <p><span style="color:#CC0000" class="dashicons dashicons-megaphone" alt="f488">&nbsp;</span> <?php esc_html_e('Check our', 'post-types-order') ?> <a href="https://wordpress.org/plugins/wp-hide-security-enhancer/" target="_blank"><b>WP Hide & Security Enhancer</b></a> <?php esc_html_e('an extra layer of security for your site. It provides an easy way to protect your website’s code from being exploited by hiding your WordPress core files, themes, and plugins.', 'post-types-order') ?>.</p>
                            <p><span style="color:#CC0000" class="dashicons dashicons-megaphone" alt="f488">&nbsp;</span> <?php esc_html_e('Check our', 'post-types-order') ?> <a href="https://wordpress.org/plugins/software-license-lite/" target="_blank"><b>Software License Lite for WooCommerce</b></a> <?php esc_html_e('A centralized licensing solution for WooCommerce that manages product licenses, delivers software updates, supports ongoing maintenance, and helps protect your code.', 'post-types-order') ?>.</p>
                            <div class="clear"></div>
                            
                            <div class="clear"></div>
                        </div>
                    
                    <?php   
                }   
            
        }
    

?>