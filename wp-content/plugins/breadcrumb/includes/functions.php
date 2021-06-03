<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


function breadcrumb_pages_objects(){

    // post-types

    $post_types = array();
    global $wp_post_types;
    $post_types_all = get_post_types( array('public'=>true), 'names' );
    foreach ( $post_types_all as $post_type ){

        $obj = $wp_post_types[$post_type];
        $post_type_name = isset($obj->labels->singular_name) ? $obj->labels->singular_name : '';
        $objects[$post_type] = array('name' => $post_type_name);

        $post_types[] = $post_type;
    }

    $taxonomies = get_object_taxonomies( $post_types );

    // Taxonomies
    foreach ( $taxonomies as $taxonomy ){

        $taxonomyData = get_taxonomy($taxonomy);
        $taxonomy_name = isset($taxonomyData->labels->name) ? $taxonomyData->labels->name : '';
        $taxonomy_public = isset($taxonomyData->public) ? $taxonomyData->public : '';

        if($taxonomy_public){
            $objects[$taxonomy] = array('name' => $taxonomy_name);
        }
    }


    //archives
    $objects['front_page'] = array('name' => __('Front page','breadcrumb'));
    $objects['home'] = array('name' => __('Home','breadcrumb'));
    $objects['blog'] = array('name' => __('Blog','breadcrumb'));
    $objects['author'] = array('name' => __('Author','breadcrumb'));
    $objects['search'] = array('name' => __('Search','breadcrumb'));
    $objects['year'] = array('name' => __('Year','breadcrumb'));
    $objects['month'] = array('name' => __('Month','breadcrumb'));
    $objects['date'] = array('name' => __('Date','breadcrumb'));

    //WooCommmerce
    $objects['wc_shop'] = array('name' => __('Shop','breadcrumb'));

    $objects['privacy_policy'] = array('name' => __('Privacy policy','breadcrumb'));
    $objects['404'] = array('name' => __('404','breadcrumb'));

    unset($objects['product_shipping_class']);


    return apply_filters('breadcrumb_pages_objects', $objects);
}



function breadcrumb_tags(){

    $tags['post']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['post']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['post']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['post']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['post']['post_category'] = array('name' => __('Post category','breadcrumb'));
    $tags['post']['post_tag'] = array('name' => __('Post tag','breadcrumb'));
    $tags['post']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['post']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['post']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['post']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['page']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['page']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['page']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['page']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['page']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['page']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['page']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['page']['post_id'] = array('name' => __('Post ID','breadcrumb'));
    $tags['page']['post_ancestors'] = array('name' => __('Post ancestors','breadcrumb'));


    $tags['attachment']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['attachment']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['attachment']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['attachment']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['attachment']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['attachment']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['attachment']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['attachment']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['product']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['product']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['product']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['product']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['product']['product_cat'] = array('name' => __('Product category','breadcrumb'));
    $tags['product']['product_tag'] = array('name' => __('Product tag','breadcrumb'));
    $tags['product']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['product']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['product']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['product']['post_id'] = array('name' => __('Post ID','breadcrumb'));
    $tags['product']['wc_shop'] = array('name' => __('Shop','breadcrumb'));



    $tags['front_page']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['front_page']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['front_page']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['front_page']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['front_page']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['front_page']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['front_page']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['front_page']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['home']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['home']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['home']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['home']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['home']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['home']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['home']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['home']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['blog']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['blog']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['blog']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['blog']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['blog']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['blog']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['blog']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['blog']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['author']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['author']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['author']['author_name'] = array('name' => __('Author name','breadcrumb'));

    $tags['search']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['search']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['search']['search_word'] = array('name' => __('Search word','breadcrumb'));


    $tags['year']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['year']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['year']['year_text'] = array('name' => __('Year','breadcrumb'));

    $tags['month']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['month']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['month']['month_text'] = array('name' => __('Month','breadcrumb'));

    $tags['date']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['date']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['date']['date_text'] = array('name' => __('Date','breadcrumb'));


    $tags['404']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['404']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['404']['404_text'] = array('name' => __('404 text','breadcrumb'));

    $tags['privacy_policy']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['privacy_policy']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['privacy_policy']['post_title'] = array('name' => __('Post title','breadcrumb'));
    $tags['privacy_policy']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['privacy_policy']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['privacy_policy']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['privacy_policy']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['privacy_policy']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['wc_shop']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['wc_shop']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['wc_shop']['post_title'] = array('name' => __('Shop title','breadcrumb'));
    $tags['wc_shop']['post_author'] = array('name' => __('Post author','breadcrumb'));
    $tags['wc_shop']['post_date'] = array('name' => __('Post date','breadcrumb'));
    $tags['wc_shop']['post_month'] = array('name' => __('Post month','breadcrumb'));
    $tags['wc_shop']['post_year'] = array('name' => __('Post year','breadcrumb'));
    $tags['wc_shop']['post_id'] = array('name' => __('Post ID','breadcrumb'));

    $tags['category']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['category']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['category']['term_title'] = array('name' => __('Category title','breadcrumb'));
    $tags['category']['term_parent'] = array('name' => __('Category parent','breadcrumb'));
    $tags['category']['term_ancestors'] = array('name' => __('Category ancestors','breadcrumb'));


    $tags['post_tag']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['post_tag']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['post_tag']['term_title'] = array('name' => __('Tag title','breadcrumb'));

    $tags['product_cat']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['product_cat']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['product_cat']['wc_shop'] = array('name' => __('Shop','breadcrumb'));
    $tags['product_cat']['term_title'] = array('name' => __('Category title','breadcrumb'));
    $tags['product_cat']['term_parent'] = array('name' => __('Category parent','breadcrumb'));
    $tags['product_cat']['term_ancestors'] = array('name' => __('Category ancestors','breadcrumb'));

    $tags['product_tag']['front_text'] = array('name' => __('Front text','breadcrumb'));
    $tags['product_tag']['home'] = array('name' => __('Home','breadcrumb'));
    $tags['product_tag']['wc_shop'] = array('name' => __('Shop','breadcrumb'));
    $tags['product_tag']['term_title'] = array('name' => __('Tag title','breadcrumb'));




    return apply_filters('breadcrumb_tags', $tags);



}









add_action('breadcrumb_tag_options_post_id', 'breadcrumb_tag_options_post_id');

function breadcrumb_tag_options_post_id($parameters){
    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post ID','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_id]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_post_ancestors', 'breadcrumb_tag_options_post_ancestors');

function breadcrumb_tag_options_post_ancestors($parameters){
    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post Ancestors','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_ancestors]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_post_year', 'breadcrumb_tag_options_post_year');

function breadcrumb_tag_options_post_year($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post year','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_year]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_post_month', 'breadcrumb_tag_options_post_month');

function breadcrumb_tag_options_post_month($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post month','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_month]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}

add_action('breadcrumb_tag_options_post_date', 'breadcrumb_tag_options_post_date');

function breadcrumb_tag_options_post_date($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post date','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_date]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_post_tag', 'breadcrumb_tag_options_post_tag');

function breadcrumb_tag_options_post_tag($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post tag','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_tag]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}

add_action('breadcrumb_tag_options_front_text', 'breadcrumb_tag_options_front_text');

function breadcrumb_tag_options_front_text($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Front text','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[front_text]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}




add_action('breadcrumb_tag_options_home', 'breadcrumb_tag_options_home');

function breadcrumb_tag_options_home($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Home','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[home]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_post_title', 'breadcrumb_tag_options_post_title');

function breadcrumb_tag_options_post_title($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post title','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_title]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_post_author', 'breadcrumb_tag_options_post_author');

function breadcrumb_tag_options_post_author($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'


    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post author','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_author]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_post_category', 'breadcrumb_tag_options_post_category');

function breadcrumb_tag_options_post_category($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Post category','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[post_category]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}




add_action('breadcrumb_tag_options_product_cat', 'breadcrumb_tag_options_product_cat');

function breadcrumb_tag_options_product_cat($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Product category','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[product_cat]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_product_tag', 'breadcrumb_tag_options_product_tag');

function breadcrumb_tag_options_product_tag($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Product tag','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[product_tag]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}




add_action('breadcrumb_tag_options_wc_shop', 'breadcrumb_tag_options_wc_shop');

function breadcrumb_tag_options_wc_shop($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Shop','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[wc_shop]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_term_title', 'breadcrumb_tag_options_term_title');

function breadcrumb_tag_options_term_title($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Term title','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[term_title]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_term_parent', 'breadcrumb_tag_options_term_parent');

function breadcrumb_tag_options_term_parent($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Term parent','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[term_parent]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}

add_action('breadcrumb_tag_options_term_ancestors', 'breadcrumb_tag_options_term_ancestors');

function breadcrumb_tag_options_term_ancestors($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Term ancestors','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[term_ancestors]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_404_text', 'breadcrumb_tag_options_404_text');

function breadcrumb_tag_options_404_text($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('404 text','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[404_text]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_date_text', 'breadcrumb_tag_options_date_text');

function breadcrumb_tag_options_date_text($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Archive date','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[date_text]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_month_text', 'breadcrumb_tag_options_month_text');

function breadcrumb_tag_options_month_text($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Archive month','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[month_text]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('breadcrumb_tag_options_year_text', 'breadcrumb_tag_options_year_text');

function breadcrumb_tag_options_year_text($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Archive year','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[year_text]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}





add_action('breadcrumb_tag_options_search_word', 'breadcrumb_tag_options_search_word');

function breadcrumb_tag_options_search_word($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Search word','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[search_word]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('breadcrumb_tag_options_author_name', 'breadcrumb_tag_options_author_name');

function breadcrumb_tag_options_author_name($parameters){
    $settings_tabs_field = new settings_tabs_field();
    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}'



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Author name','breadcrumb'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $prefix_text = '';
            $args = array(
                'id'		=> 'prefix_text',
                'parent' => $input_name.'[author_name]',
                'title'		=> __('Prefix text','breadcrumb'),
                'details'	=> __('Add prefix text.','breadcrumb'),
                'type'		=> 'text',
                'value'		=> $prefix_text,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}

/*
 * Generate breadcrumb items
 *
 * */
function breadcrumb_trail_array_list(){

    $breadcrumb_home_text = get_option('breadcrumb_home_text', __('Home','breadcrumb'));
    $breadcrumb_text = get_option('breadcrumb_text', __('You are here','breadcrumb'));

    $breadcrumb_display_home = get_option('breadcrumb_display_home', 'yes');
    $breadcrumb_url_hash = get_option('breadcrumb_url_hash');

    $home_url = get_bloginfo('url');

    $array_list = array();
    $active_plugins = get_option('active_plugins');


    $array_list[] = array(
        'link'=> '#',
        'title' => $breadcrumb_text,
    );



    if(is_front_page() && is_home()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
                'title' => $breadcrumb_home_text,

            );

    }elseif( is_front_page()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
                'title' => $breadcrumb_home_text,
            );

    }elseif( is_home()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
                'title' => __('Blog','breadcrumb'),
            );


    }else if(is_attachment()){

        $current_attachment_id = get_query_var('attachment_id');
        $current_attachment_link = get_attachment_link($current_attachment_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $current_attachment_link,
                'title' => get_the_title(),
            );

    }
    else if(in_array( 'woocommerce/woocommerce.php', (array) $active_plugins ) && is_woocommerce() && is_shop()){
        $shop_page_id = wc_get_page_id('shop');

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($shop_page_id),
                'title' => get_the_title($shop_page_id),
            );
    }


    else if(is_page()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );


            global $post;
            $home = get_post(get_option('page_on_front'));

            $j = 2;

            for ($i = count($post->ancestors)-1; $i >= 0; $i--) {
                if (($home->ID) != ($post->ancestors[$i])){

                    $array_list[] = array(
                        'link'=>get_permalink($post->ancestors[$i]),
                        'title' => get_the_title($post->ancestors[$i]),
                    );
                }

                $j++;
            }


            $array_list[] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash :  get_permalink($post->ID),
                'title' => get_the_title($post->ID),
            );

    }

    else if(is_singular()){

        if ( is_preview() ) {

            $array_list[] = array(
                'link'=> '#',
                'title' => __('Post preview','breadcrumb'),
            );


            return $array_list;
        }


        $permalink_structure = get_option('permalink_structure',true);
//        $permalink_structure = str_replace('%postname%','',$permalink_structure);
//        $permalink_structure = str_replace('%post_id%','',$permalink_structure);

        $permalink_items = array_filter(explode('/',$permalink_structure));

        global $post;
        $author_id = $post->post_author;
        $author_posts_url = get_author_posts_url($author_id);
        $author_name = get_the_author_meta('display_name', $author_id);

        $post_date_year = get_the_time('Y');
        $post_date_month = get_the_time('m');
        $post_date_day = get_the_time('d');

        $get_month_link = get_month_link($post_date_year,$post_date_month);
        $get_year_link = get_year_link($post_date_year);
        $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);


        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );


        //echo '<pre>'.var_export($permalink_items, true).'</pre>';

        if(!empty($permalink_structure) && get_post_type()=='post'){

            $item_count = 2;
            foreach ($permalink_items as $item):


                if($item == '%year%'){

                    $array_list[] = array(
                        'link'=> $get_year_link,
                        'title' => $post_date_year,
                    );

                }elseif ($item == '%monthnum%'){

                    $array_list[] = array(
                        'link'=> $get_month_link,
                        'title' => $post_date_month,
                    );
                }elseif ($item == '%day%'){

                    $array_list[] = array(
                        'link'=> $get_day_link,
                        'title' => $post_date_day,
                    );
                }elseif ($item == '%author%'){

                    $array_list[] = array(
                        'link'=> $author_posts_url,
                        'title' => $author_name,
                    );
                }elseif ($item == '%post_id%'){

                    $array_list[] = array(
                        'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                        'title' => get_the_title($post->ID),
                    );
                }elseif ($item == '%postname%'){

                    $array_list[] = array(
                        'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                        'title' => get_the_title($post->ID),
                    );
                }elseif ($item == 'archives'){

                    $array_list[] = array(
                        'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                        'title' => __('Archives','breadcrumb'),
                    );
                }elseif ($item == '%category%'){

                    $category_string = get_query_var('category_name');
                    $category_arr = array();
                    $taxonomy = 'category';

                    //echo '<pre>'.var_export($category_string, true).'</pre>';

                    if(strpos( $category_string, '/' )){

                        $category_arr = explode('/', $category_string);
                        $category_count = count($category_arr);
                        $last_cat = $category_arr[($category_count-1)];

                        $term_data = get_term_by('slug',$last_cat, $taxonomy);

                        $term_id = $term_data->term_id;
                        $term_name = $term_data->name;
                        $term_link = get_term_link( $term_id , $taxonomy);



                        $parents_id  = get_ancestors( $term_id, $taxonomy );

                        $parents_id = array_reverse($parents_id);

                        $i = $item_count+1;
                        foreach($parents_id as $id){

                            $parent_term_link = get_term_link( $id , $taxonomy);
                            $paren_term_name = get_term_by('id', $id, $taxonomy);

                            $array_list[] = array(
                                'link'=> $parent_term_link,
                                'title' => $paren_term_name->name,
                            );


                            $i++;
                        }

                        $array_list[] = array(
                            'link'=> $term_link,
                            'title' => $term_name,
                        );

                    }else{

                        $term_data = get_term_by('slug',$category_string, $taxonomy);

                        $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                        $term_name = isset($term_data->name) ? $term_data->name : '';

                        if(!empty($term_id)):
                            $term_link = get_term_link( $term_id , $taxonomy);

                            $array_list[] = array(
                                'link'=> $term_link,
                                'title' => $term_name,
                            );
                        endif;

                    }


                }






                $item_count++;

            endforeach;



        }elseif(get_post_type()=='product'){

            $shop_page_id = wc_get_page_id('shop');
            $woocommerce_permalinks = get_option('woocommerce_permalinks', '');
            $product_base = $woocommerce_permalinks['product_base'];
            $permalink_items = array_filter(explode('/',$product_base));

            if(in_array('shop',$permalink_items)){

                $array_list[] = array(
                    'link'=> get_permalink($shop_page_id),
                    'title' => get_the_title($shop_page_id),
                );


            }

            if(in_array('%product_cat%',$permalink_items)){

                $category_string = get_query_var('product_cat');

                //$category_string = get_query_var('category_name');
                $category_arr = array();
                $taxonomy = 'product_cat';
                if(strpos( $category_string, '/' )){

                    $category_arr = explode('/', $category_string);
                    $category_count = count($category_arr);
                    $last_cat = $category_arr[($category_count-1)];

                    $term_data = get_term_by('slug',$last_cat, $taxonomy);

                    $term_id = $term_data->term_id;
                    $term_name = $term_data->name;
                    $term_link = get_term_link( $term_id , $taxonomy);


                    $parents_id  = get_ancestors( $term_id, $taxonomy );

                    $parents_id = array_reverse($parents_id);

                    $i = 3;
                    foreach($parents_id as $id){

                        $parent_term_link = get_term_link( $id , $taxonomy);
                        $paren_term_name = get_term_by('id', $id, $taxonomy);

                        $array_list[] = array(
                            'link'=> $parent_term_link,
                            'title' => $paren_term_name->name,
                        );


                        $i++;
                    }

                    $array_list[] = array(
                        'link'=> $term_link,
                        'title' => $term_name,
                    );

                }else{

                    $term_data = get_term_by('slug',$category_string, $taxonomy);

                    $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                    $term_name = isset($term_data->name) ? $term_data->name : '';

                    if(!empty($term_id)):
                        $term_link = get_term_link( $term_id , $taxonomy);

                        $array_list[] = array(
                            'link'=> $term_link,
                            'title' => $term_name,
                        );

                        $array_list[] = array(
                            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                            'title' => get_the_title($post->ID),
                        );
                    endif;



                }


            }

            $array_list_count = count($array_list);
            $array_list[] = array(
                'link'=>!empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                'title' => get_the_title($post->ID),
            );



//            $array_list[3] = array(
//                'link'=>get_permalink($post->ID),
//                'title' => get_the_title($post->ID),
//            );


        }else{

            $array_list[] = array(
                'link'=> '#',
                'title' => get_post_type(),
            );

            $array_list[] = array(
                'link'=>!empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                'title' => get_the_title($post->ID),
            );
        }
    }else if( is_tax()){

        $queried_object = get_queried_object();
        $term_name = $queried_object->name;
        $term_id = $queried_object->term_id;

        $taxonomy = $queried_object->taxonomy;
        $term_link = get_term_link( $term_id , $taxonomy);
        $parents_id  = get_ancestors( $term_id, $taxonomy );

        $parents_id = array_reverse($parents_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $i = 2;
        foreach($parents_id as $id){

            $parent_term_link = get_term_link( $id , $taxonomy);
            $paren_term_name = get_term_by('id', $id, $taxonomy);

            $array_list[] = array(
                'link'=> $parent_term_link,
                'title' => $paren_term_name->name,
            );


            $i++;
        }

        $array_list[] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $term_link,
            'title' => $term_name,
        );



    }


    else if(is_category()){

        $current_cat_id = get_query_var('cat');
        $queried_object = get_queried_object();

        $taxonomy = $queried_object->taxonomy;
        $term_id = $queried_object->term_id;
        $term_name = $queried_object->name;
        $term_link = get_term_link( $term_id , $taxonomy);

        $parents_id  = get_ancestors( $term_id, $taxonomy );
        $parents_id = array_reverse($parents_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $array_list[] = array(
            'link'=> '#',
            'title' => $taxonomy,
        );


        $i = 3;
        foreach($parents_id as $id){

            $parent_term_link = get_term_link( $id , $taxonomy);
            $paren_term_name = get_term_by('id', $id, $taxonomy);

            $array_list[] = array(
                'link'=> $parent_term_link,
                'title' => $paren_term_name->name,
            );


            $i++;
        }

        $array_list[] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $term_link,
            'title' => $term_name,
        );






    }


    else if(is_tag()){

        $current_tag_id = get_query_var('tag_id');
        $current_tag = get_tag($current_tag_id);
        $current_tag_name = $current_tag->name;

        $current_tag_link = get_tag_link($current_tag_id);;

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $array_list[] = array(
            'link'=> '#',
            'title' => __('Tag','breadcrumb'),
        );


        $array_list[] = array(
            'link'=>  !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $current_tag_link,
            'title' => $current_tag_name,
        );
    }



    else if(is_author()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );


            $array_list[] = array(
                'link'=> '#',
                'title' => __('Author','breadcrumb'),
            );

            $array_list[] = array(
                'link'=>  !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_author_posts_url( get_the_author_meta( "ID" ) ),
                'title' => get_the_author(),
            );



    }else if(is_search()){

        $current_query = sanitize_text_field(get_query_var('s'));


        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $array_list[] = array(
            'link'=>  '#',
            'title' => __('Search','breadcrumb'),
        );


        $array_list[] = array(
            'link'=>  '#',
            'title' => $current_query,
        );

    }else if(is_year()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $array_list[] = array(
            'link'=> '#',
            'title' => __('Year','breadcrumb'),
        );

        $array_list[] = array(
            'link'=>  '#',
            'title' => get_the_date('Y'),
        );

    }else if(is_month()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );
        $array_list[] = array(
            'link'=> '#',
            'title' => __('Month','breadcrumb'),
        );


        $array_list[] = array(
            'link'=>  '#',
            'title' => get_the_date('F'),
        );

    }
    else if(is_date()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

        $array_list[] = array(
            'link'=> '#',
            'title' => __('Date','breadcrumb'),
        );

        $array_list[] = array(
            'link'=>  '#',
            'title' => get_the_date(),
        );
    }
    elseif(is_404()){

        if($breadcrumb_display_home == 'yes')
            $array_list[] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
            );

            $array_list[] = array(
                'link'=>  '#',
                'title' => __('404', 'breadcrumb'),
            );

    }

    return $array_list;

}



add_filter('breadcrumb_link_text', 'breadcrumb_link_text_limit');

function breadcrumb_link_text_limit($string){
    $breadcrumb_word_char = get_option('breadcrumb_word_char');
    $breadcrumb_word_char_count = get_option('breadcrumb_word_char_count');
    $breadcrumb_word_char_end = get_option('breadcrumb_word_char_end');

    $limit_count = !empty($breadcrumb_word_char_count) ? (int) $breadcrumb_word_char_count : 5;
    $limit_by = $breadcrumb_word_char;
    $ending= $breadcrumb_word_char_end;

    $string_length = (int) strlen($string);


    if($limit_by == 'character'){

        if ($limit_count < $string_length){
            $string = mb_substr($string, 0, $limit_count);

            return $string.$ending;
        }
        else{
            return $string;
        }
    }elseif($limit_by == 'word'){

        $string = wp_trim_words($string, $limit_count, $ending);
        return $string;
    }else{
        return $string;
    }


}





function breadcrumb_posttypes_array(){

    $post_types_array = array();
    global $wp_post_types;

    $post_types_all = get_post_types( array('public'=>true), 'names' );
    foreach ( $post_types_all as $post_type ) {


        $obj = $wp_post_types[$post_type];
        $post_types_array[$post_type] = $obj->labels->singular_name;
    }


    return $post_types_array;
}




add_action( 'init', 'breadcrumb_remove_wc_breadcrumbs' );
function breadcrumb_remove_wc_breadcrumbs() {

    $breadcrumb_hide_wc_breadcrumb = get_option('breadcrumb_hide_wc_breadcrumb');

    if($breadcrumb_hide_wc_breadcrumb == 'yes'){
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
    }


}




function breadcrumb_recursive_sanitize_arr($array) {

    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = breadcrumb_recursive_sanitize_arr($value);
        }
        else {
            $value = sanitize_text_field( $value );
        }
    }

    return $array;
}