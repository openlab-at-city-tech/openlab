<?php

//navigation based functionality
//help navigation via Ambrosite plugin
function openlab_help_navigation($loc = 'bottom') {
    $prev_args = array(
        'order_by' => 'menu_order',
        'order_2nd' => 'post_date',
        'post_type' => '"help"',
        'format' => '<span class="fa fa-chevron-circle-left"></span> %link',
        'link' => '%title',
        'date_format' => '',
        'tooltip' => '%title',
        'ex_posts' => '',
    );

    $next_args = array(
        'order_by' => 'menu_order',
        'order_2nd' => 'post_date',
        'post_type' => '"help"',
        'format' => '%link <span class="fa fa-chevron-circle-right"></span>',
        'link' => '%title',
        'date_format' => '',
        'tooltip' => '%title',
        'ex_posts' => '',
    );

    if (function_exists('previous_post_link_plus') && function_exists('next_post_link_plus')) {
        echo '<nav id="nav-single" class="' . $loc . ' clearfix page-nav">';
        echo '<div class="nav-previous pull-left">';
        previous_post_link_plus($prev_args);
        echo '</div>';
        echo '<div class="nav-next pull-right">';
        next_post_link_plus($next_args);
        echo '</div>';
        echo '</nav><!-- #nav-single -->';
    }
}

function openlab_custom_nav_classes($classes,$item){
    global $post;

    if ( ! ( $post instanceof WP_Post ) ) {
	    return $classes;
    }

    if(($post->post_type == 'help') && $item->title == 'Help' ){
        $classes[] = ' current-menu-item';
    } else if ($post->post_parent == get_page_by_path('about')->ID && $item->title == 'About'){
        $classes[] = ' current-menu-item';
    }

    return $classes;
}

add_filter('nav_menu_css_class','openlab_custom_nav_classes', 10, 2);

/**
 * Filter pagination links on group/member directories to include misc GET params.
 */
function openlab_loop_pagination_links_filter($has_items) {
    global $groups_template, $members_template;
    // Only run on directories.
    $current_page = get_queried_object();
    if ( ! isset( $current_page->post_name ) || ! in_array( $current_page->post_name, array( 'people', 'courses', 'projects', 'clubs', 'portfolios', ) ) ) {
            return $has_items;
    }
    switch (current_filter()) {
        case 'bp_has_groups' :
            $t = $groups_template;
            $pagarg = 'grpage';
            $count = (int) $t->total_group_count;
            break;
        case 'bp_has_members' :
            $t = $members_template;
            $pagarg = 'upage';
            $count = (int) $t->total_member_count;
            break;
    }

    if ( ! isset( $t ) || ! isset( $pagarg ) || ! isset( $count ) ) {
        return $has_items;
    }

    if ($count && (int) $t->pag_num) {
        $pag_args = array(
            $pagarg => '%#%',
            'num' => $t->pag_num,
        );

        if(isset($t->sort_by)){
            $pag_args['sortby'] = $t->sort_by;
        }

        if(isset($t->order)){
            $pag_args['order'] = $t->order;
        }

        if (defined('DOING_AJAX') && true === (bool) DOING_AJAX) {
            $base = remove_query_arg('s', wp_get_referer());
        } else {
            $base = '';
        }
        $ol_args = array(
            'department',
            'group_sequence',
            'school',
            'search',
            'semester',
            'usertype',
        );
        foreach ($ol_args as $ol_arg) {
            if (isset($_GET[$ol_arg])) {
                $pag_args[$ol_arg] = urldecode($_GET[$ol_arg]);
            }
        }
        $t->pag_links = paginate_links(array(
            'base' => add_query_arg($pag_args, $base),
            'format' => '',
            'total' => ceil($count / (int) $t->pag_num),
            'current' => $t->pag_page,
            'prev_text' => _x('&larr;', 'Group pagination previous text', 'buddypress'),
            'next_text' => _x('&rarr;', 'Group pagination next text', 'buddypress'),
            'mid_size' => 1
                ));
    }
    return $has_items;
}

add_filter('bp_has_groups', 'openlab_loop_pagination_links_filter');
add_filter('bp_has_members', 'openlab_loop_pagination_links_filter');

function openlab_toggle_button($target = '#menu', $backgroundonly = false){
    $button_out = '';

    $button = <<<HTML
            <button data-target="{$target}" data-backgroundonly="{$backgroundonly}" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
HTML;

    return $button;
}
