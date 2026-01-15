<?php

     if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
     
     class CPT_Compatibility
        {
         
            /**
            * Constructor
            * 
            */
            function __construct()
                {
                    
                    $this->init();
                    
                }
                
                
            /**
            * Initialisation function
            *     
            */
            function init()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                        
                    $CompatibilityFiles  =  array(
                                                    'the-events-calendar.php',
                                                    'LiteSpeed_Cache.php',
                                                    'formidable.php'
                                                 
                                                    );
                    foreach( $CompatibilityFiles as $CompatibilityFile ) 
                        {
                            if  ( is_file( CPTPATH . 'compatibility/' . $CompatibilityFile ) )
                                include_once( CPTPATH . 'compatibility/' . $CompatibilityFile );
                        }
                        
                    
                    if ( $this->is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) )
                        include_once( CPTPATH . 'compatibility/acf.php' );

                      
                    /**
                    * Themes
                    */
                    
                    $theme  =   wp_get_theme();
                    
                    if( ! $theme instanceof WP_Theme )
                        return FALSE;
                        
                    $compatibility_themes   =   array(
                                                        'enfold'             =>  'enfold.php',
                                                        );
                    
                    if (isset( $theme->template ) )
                        {
                            foreach ( $compatibility_themes as  $theme_slug     =>  $compatibility_file )
                                {
                                    if ( strtolower( $theme->template ) == $theme_slug  ||   strtolower( $theme->name ) == $theme_slug )
                                        {
                                            include_once( CPTPATH . 'compatibility/themes/' .   $compatibility_file );    
                                        }
                                }
                        }
                    
                          
                    do_action('cpt/compatibility/init');
                    
                }
                
            
            /**
            * Check if the plugin is active
            * 
            */
            private function is_plugin_active( $plugin )
                {
                    
                    if ( is_plugin_active ( $plugin ) )
                        return TRUE;
                        else
                        return FALSE;
                        
                }
            
    
                
        }   
            

     new CPT_Compatibility();

?>