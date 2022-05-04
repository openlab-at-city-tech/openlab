<?php
/**
 * Library of activity stream related functions
 *
 */

use OpenLab\Favorites\Favorite\Query;

/**
 * Construct the array of arguments for the activities loop.
 * 
 */
function openlab_activities_loop_args( $activity_type = '' ) {
    $args['count_total'] = true;
    
    switch( $activity_type ) {
        case 'mine':
            $args += [
                'scope' => 'just-me',
            ];
            break;
        case 'favorites':
            $favorites = Query::get_results(
                [
                    'user_id' => bp_loggedin_user_id(),
                ]
            );
    
            $group_ids = '';
    
            if( $favorites ) {
                $group_ids = [];
                foreach( $favorites as $favorite ) {
                    array_push( $group_ids, $favorite->get_group_id() );
                }
            }
    
            $args += [
                'filter_query'	=> [
                    'relation'	=> 'AND',
                    'component'	=> [
                        'column'	=> 'component',
                        'value'		=> 'groups',
                    ],
                    'group_id'	=> [
                        'column'	=> 'item_id',
                        'value'		=> $group_ids,
                        'compare'	=> 'IN',
                    ],
                ],
            ];
            
            break;
        case 'mentions':
            $args += [
                'scope' => 'mentions'
            ];
            break;
        case 'pins':
            $args += [
                'scope' => 'favorites'
            ];
            break;
        default:
            $args += [
                'scope' => 'groups',
            ];
    }

    return $args;
}

/**
 * User's activity stream pagination.
 * 
 */
function openlab_activities_pagination_links() {
    global $activities_template;

    $pagination = paginate_links(array(
        'base' => add_query_arg(array('acpage' => '%#%') ),
        'format' => '',
        'total' => ceil((int) $activities_template->total_activity_count / (int) $activities_template->pag_num),
        'current' => $activities_template->pag_page,
        'prev_text' => _x('<i class="fa fa-angle-left" aria-hidden="true"></i><span class="sr-only">Previous</span>', 'Group pagination previous text', 'buddypress'),
        'next_text' => _x('<i class="fa fa-angle-right" aria-hidden="true"></i><span class="sr-only">Next</span>', 'Group pagination next text', 'buddypress'),
        'mid_size' => 3,
        'type' => 'list',
        'before_page_number' => '<span class="sr-only">Page</span>',
    ));

    $pagination = str_replace('page-numbers', 'page-numbers pagination', $pagination);

    //for screen reader only text - current page
    $pagination = str_replace('current\'><span class="sr-only">Page', 'current\'><span class="sr-only">Current Page', $pagination);

    return $pagination;
}
