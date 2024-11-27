<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
            
    class Post_Types_Order_Walker extends Walker 
        {

            var $db_fields = array (    
                                        'parent' => 'post_parent', 
                                        'id' => 'ID'
                                        );

        


            /**
            * Start element
            * 
            * @param mixed $output
            * @param mixed $page
            * @param mixed $depth
            * @param mixed $args
            * @param mixed $id
            */
            function start_el( &$output, $object, $depth = 0, $args = array(), $id = 0) 
                {                   
                    $options    =   CptoFunctions::get_options();
                    
                    if ( $depth )
                        $indent = str_repeat("\t", $depth);
                    else
                        $indent = '';

                    extract($args, EXTR_SKIP);

                    $item_title     =   apply_filters( 'the_title', $object->post_title, $object->ID );
                    
                    $item_details   =   apply_filters('pto/interface_item_data', '', $object );
                                    
                                        
                    $output        .=   $indent . '<tr id="item_' . $object->ID . '">
                                                    <td>
                                                        <strong><a class="row-title" href="'. get_edit_post_link( $object ) .'">' . $item_title .'</a> ' . $item_details .'</strong>';
                    
                    if ( $options['edit_view_links']    ===  1 )
                        $output .=  '<div class="row-actions"><span class="edit"><a href="' . get_edit_post_link( $object ) .'">Edit</a> | </span><span class="view"><a target="_blank" href="'. get_permalink( $object ) .'">View</a></span></div>';
                    
                    $output .=  '</td>';
                    
                    $output .=  apply_filters( 'pto/interface/table/tbody', '', $object );
               
                }


            /**
            * End element
            * 
            * @param mixed $output
            * @param mixed $page
            * @param mixed $depth
            * @param mixed $args
            */
            function end_el(&$output, $page, $depth = 0, $args = array()) 
                {
                    $output .= "</tr>\n";
                }

        }