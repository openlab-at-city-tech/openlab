<?php
    
    /**
    * Compatibility     : LiteSpeed Cache
    * Introduced at     : 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class PTO_Blocksy
        {
            /**
            * Constructor
            * 
            */
            function __construct()
                {                      
                      add_filter ( 'pto/get_options',               array ( $this, 'get_options' ) );
                }
                
                
            function get_options( $options )
                {   
                    if ( in_array( 'administrator', (array) wp_get_current_user()->roles, true ) )    
                        return $options;
                        
                    if ( ! is_admin () )
                        return $options;
                        
                    if ( isset ( $options['show_reorder_interfaces']['ct-dashboard'] ) ) 
                        {
                            if ( $options['show_reorder_interfaces']['ct-dashboard'] ===    'hide' )
                                $options['show_reorder_interfaces']['edit.php?post_type=ct_content_block'] =   'hide';
                                else
                                $options['show_reorder_interfaces']['edit.php?post_type=ct_content_block'] =   'show';
                        }
                        
                    return $options;
                }    
            
        }
        
        
    new PTO_Blocksy();