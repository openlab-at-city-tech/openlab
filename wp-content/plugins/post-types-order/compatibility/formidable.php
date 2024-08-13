<?php


    /**
    * Compatibility     : Formidable Forms
    * Introduced at     :  6.8.2
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class PTO_Compatibility_formidables
        {
                
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                        
                    add_filter( 'pto/posts_orderby/ignore', array ( $this, 'ignore_post_types_order_sort' ), 10, 3 );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if ( is_plugin_active ( 'formidable/formidable.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function ignore_post_types_order_sort( $ignore, $orderBy, $query ) 
                {
                    if ( isset($query->query)  &&  !empty( $query->query['post_type'] ) &&  $query->query['post_type'] == 'frm_styles' ) 
                        $ignore =   TRUE; 
                    
                    return $ignore;
                }                        
                                
        }
        
    new PTO_Compatibility_formidables();



?>