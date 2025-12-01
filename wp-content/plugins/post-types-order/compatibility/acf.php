<?php

    /**
    * Compatibility     : Advanced Custom Fields PRO
    * Introduced at     :  6.5.0.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class PTO_Compatibility_acf
        {
                
            /**
            * Constructor
            * 
            */
            function __construct()
                {
                    $this->init();
                }                        
            
            
            
            function init()
                {
                    
                    add_filter ( 'pto/posts_orderby/ignore',    array ( $this, 'posts_orderby_ignore' ), 99, 3 );
                    
                }
                
                
            function posts_orderby_ignore( $ignore, $orderBy, $query )
                {
                    if ( isset ( $query->query['post_type'] )   &&  $query->query['post_type'] === 'acf-taxonomy' )
                        $ignore =   TRUE;
                        
                    return $ignore;   
                }                    
                                
        }
        
    new PTO_Compatibility_acf();



?>