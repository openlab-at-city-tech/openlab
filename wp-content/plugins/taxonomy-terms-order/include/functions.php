<?php
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    /**
    * Return default plugin options
    * 
    */
    function tto_get_settings()
        {
            
            $settings = get_option('tto_options'); 
            
            $defaults   = array (
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
    function tto_userdata_get_user_level($return_as_numeric = FALSE)
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
        
        
    function tto_activate()
        {
            global $wpdb;
            
            //check if the menu_order column exists;
            $query = "SHOW COLUMNS FROM $wpdb->terms 
                        LIKE 'term_order'";
            $result = $wpdb->query($query);
            
            if ($result == 0)
                {
                    $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
                    $result = $wpdb->query($query); 
                }
        }
     
        
    function tto_info_box()
        {
            ?>
                <div id="cpt_info_box">                   
                    <p><?php _e( "Did you find this plugin useful? Please support our work with a donation or write an article about this plugin in your blog with a link to our site", 'taxonomy-terms-order' ) ?> <strong>https://www.nsp-code.com/</strong></p>
                    <h4><?php _e( "Did you know there is an Advanced Version of this plug-in?", 'taxonomy-terms-order' ) ?> <a target="_blank" href="https://www.nsp-code.com/premium-plugins/wordpress-plugins/advanced-taxonomy-terms-order/"><?php _e( "Read more", 'taxonomy-terms-order' ) ?></a></h4>
                    <p><?php _e( "Check our", 'taxonomy-terms-order' ) ?> <a target="_blank" href="https://wordpress.org/plugins/post-types-order/">Post Types Order</a> <?php _e( "plugin which allows to custom sort all posts, pages, custom post types", 'taxonomy-terms-order' ) ?> </p>
                    <p><?php _e('Check our', 'taxonomy-terms-order') ?> <a target="_blank" href="https://wordpress.org/plugins/post-terms-order/">Post Terms Order</a> <?php _e('plugin which allows to custom sort categories and custom taxonomies terms per post basis', 'taxonomy-terms-order') ?> </p>
                    
                    <div class="clear"></div>
                </div>
            
            <?php   
        }

?>