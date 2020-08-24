<?php


    function ignore_post_types_order_sort( $ignore, $orderBy, $query ) 
        {
            if ( isset($query->query_vars)  &&  !empty( $query->query_vars['post_type'] )) 
                {
                    $query_post_types   =   array();
                    foreach ( (array)$query->query_vars['post_type'] as $_post_type ) 
                        {
                            $query_post_types[] =   $_post_type;
                        }
                    
                    if  ( in_array("tribe_events", $query_post_types ) )
                        $ignore =   TRUE; 
                    
                }
            
            return $ignore;
        }
    add_filter( 'pto/posts_orderby/ignore', 'ignore_post_types_order_sort', 10, 3 );


?>