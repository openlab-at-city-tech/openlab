<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class PTO_Update
        {
                                  
            function __construct()
                {                    
                    $this->_run();
                }
                
                
            private function _run()
                {                    
                    $options            =     CptoFunctions::get_options();
                    
                    $version            =   isset ( $options['plugin_version'] )    ?   $options['plugin_version']  :   '1';
                                                    
                    if ( version_compare ( $version, PTO_VERSION, '<' ) ) 
                        {
                                                                                                    
                            if(version_compare($version, '2.5.0', '<'))
                                {
                                    add_action ( 'admin_menu', array ( $this, '_update_2_5_0' ), -1 );
                                    
                                    $version =   '2.5.0';
                                }
                         
                            //save the last code version
                            $options['plugin_version'] =   PTO_VERSION;
                            CptoFunctions::update_options( $options );
                                    
                        }
                    
                     
                }
                
                
                
            /**
             * Migrate the old post-type based show_reorder_interfaces option
             * to the new menu-location based format.
             *
             * Old format:
             * array(
             *     'post'       => 'show',
             *     'attachment' => 'show',
             *     'wp_block'   => 'show',
             * )
             *
             * New format:
             * array(
             *     'edit.php'   => 'show',
             *     'upload.php' => 'show',
             * )
             *
             * @param array $options Plugin options.
             * @return array
             */
            public function _update_2_5_0( )
                {
                    $options            =     CptoFunctions::get_options();
                    
                    if ( ! is_array( $options ) || ! isset( $options['show_reorder_interfaces'] ) )
                        return $options;

                    $old_interfaces = $options['show_reorder_interfaces'];

                    if ( ! is_array( $old_interfaces ) || empty( $old_interfaces ) )
                        return;

                    $locations = CptoFunctions::get_available_menu_locations();

                    if ( empty( $locations ) )
                        return;

                    /*
                     * Detect the legacy format.
                     *
                     * New format keys are menu locations.
                     * Old format keys are post type names.
                     */
                    $is_legacy = false;

                    foreach ( $old_interfaces as $key => $value )
                    {
                        if ( post_type_exists( $key ) )
                        {
                            $is_legacy = true;
                            break;
                        }
                    }

                    if ( ! $is_legacy )
                        return;

                    $new_interfaces = array();

                    foreach ( $locations as $location => $location_data )
                    {
                        $found = false;
                        $show  = false;

                        if ( ! empty( $location_data['post_types'] ) )
                        {
                            foreach ( $location_data['post_types'] as $post_type )
                            {
                                if ( ! isset( $old_interfaces[ $post_type ] ) )
                                    continue;

                                $found = true;

                                if ( $old_interfaces[ $post_type ] === 'show' )
                                {
                                    $show = true;
                                    break;
                                }
                            }
                        }

                        /*
                         * Only create a value when the old setting contained
                         * at least one of the post types belonging to this menu.
                         */
                        if ( $found )
                            $new_interfaces[ $location ] = $show ? 'show' : 'hide';
                    }

                    $options['show_reorder_interfaces'] = $new_interfaces;

                    CptoFunctions::update_options( $options );
                }

        }
        
    
    new PTO_Update();
         
?>