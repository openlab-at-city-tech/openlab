<?php

//navigation based functionality
//help navigation via Ambrosite plugin
function openlab_help_navigation($loc = 'bottom') {
    $prev_args = array(
        'order_by' => 'menu_order',
        'order_2nd' => 'post_date',
        'post_type' => '"help"',
        'format' => '&larr; %link',
        'link' => '%title',
        'date_format' => '',
        'tooltip' => '%title',
        'ex_posts' => '',
    );

    $next_args = array(
        'order_by' => 'menu_order',
        'order_2nd' => 'post_date',
        'post_type' => '"help"',
        'format' => '%link &rarr;',
        'link' => '%title',
        'date_format' => '',
        'tooltip' => '%title',
        'ex_posts' => '',
    );

    if (function_exists('previous_post_link_plus') && function_exists('next_post_link_plus')) {
        echo '<nav id="nav-single" class="' . $loc . '">';
        echo '<div class="nav-previous">';
        previous_post_link_plus($prev_args);
        echo '</div>';
        echo '<div class="nav-next">';
        next_post_link_plus($next_args);
        echo '</div>';
        echo '<div class="clearfloat"></div>';
        echo '</nav><!-- #nav-single -->';
    }
}