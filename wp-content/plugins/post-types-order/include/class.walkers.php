<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class Post_Types_Order_Walker extends Walker 
        {

            var $db_fields = array (    
                                        'parent' => 'post_parent', 
                                        'id' => 'ID'
                                        );


            /**
            * Start level
            * 
            * @param mixed $output
            * @param mixed $depth
            * @param mixed $args
            */
            function start_lvl(&$output, $depth = 0, $args = array()) 
                {
                    $indent = str_repeat("\t", $depth);
                    $output .= "\n$indent<ul class='children'>\n";
                }


            /**
            * End level
            * 
            * @param mixed $output
            * @param mixed $depth
            * @param mixed $args
            */
            function end_lvl(&$output, $depth = 0, $args = array()) 
                {
                    $indent = str_repeat("\t", $depth);
                    $output .= "$indent</ul>\n";
                }


            /**
            * Start element
            * 
            * @param mixed $output
            * @param mixed $page
            * @param mixed $depth
            * @param mixed $args
            * @param mixed $id
            */
            function start_el(&$output, $page, $depth = 0, $args = array(), $id = 0) 
                {
                    if ( $depth )
                        $indent = str_repeat("\t", $depth);
                    else
                        $indent = '';

                    extract($args, EXTR_SKIP);

                    $item_details   =   apply_filters( 'the_title', $page->post_title, $page->ID );
                    
                    //Deprecated, rely on pto/interface_itme_data
                    $item_details   =   apply_filters('cpto/interface_itme_data', $item_details, $page);
                    
                    $item_details   =   apply_filters('pto/interface_item_data', $item_details, $page);
                                    
                    $output .= $indent . '<li id="item_'.$page->ID.'"><span>'. $item_details .'</span>';
               
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
                    $output .= "</li>\n";
                }

        }