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
    
    if(($post->post_type == 'help' || is_taxonomy('help_category')) && $item->title == 'Help' ){
        $classes[] = ' current-menu-item';
    }
    
    return $classes;
}

add_filter('nav_menu_css_class','openlab_custom_nav_classes', 10, 2);