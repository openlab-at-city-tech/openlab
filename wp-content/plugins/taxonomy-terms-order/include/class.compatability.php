<?php
    
    
    class TTO_compatibility
        {
            
            function __construct()
                {
                    if ( ! function_exists( 'is_plugin_active' ) )
                        require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
                        
                    if ( is_plugin_active( 'et-core-plugin/et-core-plugin.php' ) ) 
                        {
                            add_action( 'elementor/element/etheme_brands/settings/before_section_end', array ( $this, 'et_elementor_before_section_end' ), 10, 2 );
                        }
                    
                }
                
                
            
            function et_elementor_before_section_end( $element, $args ) 
                {

                    $control = $element->get_controls( 'orderby' );

                    if ( empty( $control ) || empty( $control['options'] ) ) {
                        return;
                    }

                    // Add your custom option
                    $control['options']['term_order'] = esc_html__(
                        'Term Order',
                        'xstore-core'
                    );

                    // Update control
                    $element->update_control( 'orderby', $control );

                }
            
                             
        }
        
    new TTO_compatibility();    
    
?>