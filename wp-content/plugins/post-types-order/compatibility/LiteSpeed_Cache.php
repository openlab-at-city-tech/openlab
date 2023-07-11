<?php
    
    /**
    * Compatibility     : LiteSpeed Cache
    * Introduced at     : 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class PTO_LiteSpeed_Cache
        {
                        
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    add_action( 'PTO/order_update_complete', array( $this, 'order_update_complete') );
                }
                
            
            function is_plugin_active()
                {
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if ( is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
            function order_update_complete()
                {
                    
                    if( method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) 
                        {
                            LiteSpeed_Cache_API::purge_all() ;
                        }
                
                }                        
                                
        }
        
    new PTO_LiteSpeed_Cache();


?>