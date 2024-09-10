<?php
if (!defined('ABSPATH')) exit;  // if direct access

add_filter('breadcrumb_items_array', 'breadcrumb_items_override_permalinks');

function breadcrumb_items_override_permalinks($breadcrumb_items)
{

    $breadcrumb_options = get_option('breadcrumb_options');
    $permalinks = isset($breadcrumb_options['permalinks']) ? $breadcrumb_options['permalinks'] : array();
    $active_plugins = get_option('active_plugins');


    if (is_front_page() && is_home()) {


        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['front_page']) && !empty($permalinks['front_page'])) {
            $post_type_permalinks = isset($permalinks['front_page']) ? $permalinks['front_page'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :

                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_front_page()) {
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();




        if (isset($permalinks['home']) && !empty($permalinks['home'])) {
            $post_type_permalinks = isset($permalinks['home']) ? $permalinks['home'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;



            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_home()) {
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();





        if (isset($permalinks['blog']) && !empty($permalinks['blog'])) {
            $post_type_permalinks = isset($permalinks['blog']) ? $permalinks['blog'] : array();



            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :

                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';



                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :


                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;



            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (in_array('woocommerce/woocommerce.php', (array) $active_plugins) && is_woocommerce() && is_shop()) {


        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['wc_shop']) && !empty($permalinks['wc_shop'])) {
            $post_type_permalinks = isset($permalinks['wc_shop']) ? $permalinks['wc_shop'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :

                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_singular()) {

        $post_type = get_post_type();
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks[$post_type]) && !empty($permalinks[$post_type])) {
            $post_type_permalinks = isset($permalinks[$post_type]) ? $permalinks[$post_type] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :

                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';


                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;



            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_tax()) {

        $queried_object = get_queried_object();

        $taxonomy = $queried_object->taxonomy;

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks[$taxonomy]) && !empty($permalinks[$taxonomy])) {
            $post_type_permalinks = isset($permalinks[$taxonomy]) ? $permalinks[$taxonomy] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_category()) {

        $queried_object = get_queried_object();

        $taxonomy = $queried_object->taxonomy;

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks[$taxonomy]) && !empty($permalinks[$taxonomy])) {
            $post_type_permalinks = isset($permalinks[$taxonomy]) ? $permalinks[$taxonomy] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_tag()) {

        $queried_object = get_queried_object();

        $taxonomy = $queried_object->taxonomy;

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks[$taxonomy]) && !empty($permalinks[$taxonomy])) {
            $post_type_permalinks = isset($permalinks[$taxonomy]) ? $permalinks[$taxonomy] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_author()) {
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['author']) && !empty($permalinks['author'])) {
            $post_type_permalinks = isset($permalinks['author']) ? $permalinks['author'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_search()) {

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['search']) && !empty($permalinks['search'])) {
            $post_type_permalinks = isset($permalinks['search']) ? $permalinks['search'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_year()) {

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['year']) && !empty($permalinks['year'])) {
            $post_type_permalinks = isset($permalinks['year']) ? $permalinks['year'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_month()) {

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['month']) && !empty($permalinks['month'])) {
            $post_type_permalinks = isset($permalinks['month']) ? $permalinks['month'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));

                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else if (is_date()) {

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['date']) && !empty($permalinks['date'])) {
            $post_type_permalinks = isset($permalinks['date']) ? $permalinks['date'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));




                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } elseif (is_404()) {

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        if (isset($permalinks['404']) && !empty($permalinks['404'])) {
            $post_type_permalinks = isset($permalinks['404']) ? $permalinks['404'] : array();

            $i = 0;
            if (!empty($post_type_permalinks))
                foreach ($post_type_permalinks as $permalinkIndex => $permalink) :
                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';

                    $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_' . $elementId, array('permalink' => $permalink));




                    if (!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])) :

                        foreach ($breadcrumb_items_new[$i] as $item) :
                            $breadcrumb_items_latest[] = $item;
                        endforeach;

                    else :
                        $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                    endif;

                    $i++;
                endforeach;
            return $breadcrumb_items_latest;
        } else {
            return $breadcrumb_items;
        }
    } else {
        return $breadcrumb_items;
    }
}


add_filter('breadcrumb_permalink_front_text', 'breadcrumb_permalink_front_text');

function breadcrumb_permalink_front_text($args)
{

    $permalink = isset($args['permalink']) ? $args['permalink'] : [];
    $text = isset($permalink['text']) ? $permalink['text'] : '';


    $breadcrumb_text = get_option('breadcrumb_text', __('You are here', 'breadcrumb'));

    $text = !empty($text) ? $text : $breadcrumb_text;

    return array(
        'link' => '#',
        'title' => $text,
    );
}


add_filter('breadcrumb_permalink_custom_text', 'breadcrumb_permalink_custom_text');

function breadcrumb_permalink_custom_text($args)
{

    $permalink = isset($args['permalink']) ? $args['permalink'] : '';
    $text = isset($permalink['text']) ? $permalink['text'] : '';
    $link = isset($permalink['link']) ? $permalink['link'] : '#';


    return array(
        'link' => $link,
        'title' => $text,
    );
}



add_filter('breadcrumb_permalink_home', 'breadcrumb_permalink_home');

function breadcrumb_permalink_home($args)
{
    $permalink = isset($args['permalink']) ? $args['permalink'] : '';
    $url = isset($permalink['url']) ? $permalink['url'] : '#';
    $text = isset($permalink['text']) ? $permalink['text'] : '';

    $breadcrumb_home_text = get_option('breadcrumb_home_text', __('Home', 'breadcrumb'));
    $home_url = !empty($url) ? $url : get_bloginfo('url');


    return array(
        'link' => $home_url,
        'title' => !empty($text) ? $text : $breadcrumb_home_text,
    );
}


add_filter('breadcrumb_permalink_post_title', 'breadcrumb_permalink_post_title');

function breadcrumb_permalink_post_title($breadcrumb_items)
{
    $post_id = get_the_id();

    return array(
        'link' => get_permalink($post_id),
        'title' => strip_tags(get_the_title($post_id)),
    );
}


add_filter('breadcrumb_permalink_post_ancestors', 'breadcrumb_permalink_post_ancestors');

function breadcrumb_permalink_post_ancestors($breadcrumb_items)
{
    $post_id = get_the_id();
    $array_list = array();

    global $post;
    $home = get_post(get_option('page_on_front'));

    $j = 2;

    for ($i = count($post->ancestors) - 1; $i >= 0; $i--) {
        if (($home->ID) != ($post->ancestors[$i])) {

            $array_list[] = array(
                'link' => get_permalink($post->ancestors[$i]),
                'title' => get_the_title($post->ancestors[$i]),
            );
        }

        $j++;
    }

    return $array_list;
}


add_filter('breadcrumb_permalink_post_author', 'breadcrumb_permalink_post_author');

function breadcrumb_permalink_post_author($breadcrumb_items)
{

    $post_id = get_the_id();

    $post = get_post($post_id);
    $author_id = $post->post_author;
    $author_posts_url = get_author_posts_url($author_id);
    $author_name = get_the_author_meta('display_name', $author_id);

    return array(
        'link' => $author_posts_url,
        'title' => $author_name,
    );
}


add_filter('breadcrumb_permalink_post_category', 'breadcrumb_permalink_post_category');

function breadcrumb_permalink_post_category($breadcrumb_items)
{
    $category_string = get_query_var('category_name');
    $category_arr = array();
    $breadcrumb_items = array();

    $taxonomy = 'category';
    $array_list = array();
    $breadcrumb_items_new = array();

    if (!empty($category_string)) {

        if (strpos($category_string, '/')) {

            $category_arr = explode('/', $category_string);
            $category_count = count($category_arr);
            $last_cat = $category_arr[($category_count - 1)];
            $term_data = get_term_by('slug', $last_cat, $taxonomy);

            $term_id = $term_data->term_id;
            $term_name = $term_data->name;
            $term_link = get_term_link($term_id, $taxonomy);




            $parents_id  = get_ancestors($term_id, $taxonomy);

            $parents_id = array_reverse($parents_id);


            foreach ($parents_id as $id) {

                $parent_term_link = get_term_link($id, $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link' => $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link' => $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;
        } else {

            $term_data = get_term_by('slug', $category_string, $taxonomy);

            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;

            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    } else {
        $post_id = get_the_id();
        $post_terms = wp_get_post_terms($post_id, $taxonomy);

        $first_term = isset($post_terms[0]) ? $post_terms[0] : '';


        if (!empty($first_term)) {
            $breadcrumb_items_new[] = array(
                'link' => get_term_link($first_term->term_id),
                'title' => $first_term->name,
            );
        }

        $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
    }





    return $breadcrumb_items;
}


add_filter('breadcrumb_permalink_product_cat', 'breadcrumb_permalink_product_cat');

function breadcrumb_permalink_product_cat($breadcrumb_items)
{

    $category_string = get_query_var('product_cat');
    $category_arr = array();
    $breadcrumb_items = array();



    $taxonomy = 'product_cat';
    $array_list = array();

    if (!empty($category_string)) {
        if (strpos($category_string, '/')) {

            $category_arr = explode('/', $category_string);
            $category_count = count($category_arr);
            $last_cat = $category_arr[($category_count - 1)];
            $breadcrumb_items_new = array();
            $term_data = get_term_by('slug', $last_cat, $taxonomy);

            $term_id = $term_data->term_id;
            $term_name = $term_data->name;
            $term_link = get_term_link($term_id, $taxonomy);

            $parents_id  = get_ancestors($term_id, $taxonomy);
            $parents_id = array_reverse($parents_id);

            foreach ($parents_id as $id) {

                $parent_term_link = get_term_link($id, $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link' => $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link' => $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;
        } else {

            $term_data = get_term_by('slug', $category_string, $taxonomy);
            $breadcrumb_items_new = array();


            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;

            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    } else {

        if (is_singular()) {
            $post_id = get_the_ID();

            //$terms = get_terms();
            $terms = get_the_terms($post_id, $taxonomy);
            $term_data = isset($terms[0]) ? $terms[0] : '';

            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;
            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    }





    return $breadcrumb_items;
}




add_filter('breadcrumb_permalink_category_ancestors', 'breadcrumb_permalink_category_ancestors');

function breadcrumb_permalink_category_ancestors($args)
{


    $permalink = isset($args['permalink']) ? $args['permalink'] : '';
    $taxonomy = isset($permalink['taxonomy']) ? $permalink['taxonomy'] : '';




    $category_string = get_query_var($taxonomy);
    $category_arr = array();
    $breadcrumb_items = array();
    $breadcrumb_items_new = array();


    $array_list = array();

    if (!empty($category_string)) {
        if (strpos($category_string, '/')) {

            $category_arr = explode('/', $category_string);
            $category_count = count($category_arr);
            $last_cat = $category_arr[($category_count - 1)];
            $term_data = get_term_by('slug', $last_cat, $taxonomy);

            $term_id = $term_data->term_id;
            $term_name = $term_data->name;
            $term_link = get_term_link($term_id, $taxonomy);

            $parents_id  = get_ancestors($term_id, $taxonomy);
            $parents_id = array_reverse($parents_id);



            foreach ($parents_id as $id) {

                $parent_term_link = get_term_link($id, $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link' => $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link' => $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;
        } else {

            $term_data = get_term_by('slug', $category_string, $taxonomy);
            $breadcrumb_items_new = array();


            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );

                $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);

            endif;
        }
    } else {

        if (is_singular()) {
            $post_id = get_the_ID();

            //$terms = get_terms();
            $terms = get_the_terms($post_id, $taxonomy);

            if (!is_wp_error($terms)) {

                $term_data = isset($terms[0]) ? $terms[0] : '';

                $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                $term_name = isset($term_data->name) ? $term_data->name : '';



                $parents_id  = get_ancestors($term_id, $taxonomy);
                $parents_id = array_reverse($parents_id);


                if (!empty($parents_id)) {
                    foreach ($parents_id as $id) {

                        $parent_term_link = get_term_link($id, $taxonomy);
                        $paren_term_name = get_term_by('id', $id, $taxonomy);

                        $breadcrumb_items_new[] = array(
                            'link' => $parent_term_link,
                            'title' => $paren_term_name->name,
                        );
                    }
                }
            }






            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    }





    return $breadcrumb_items;
}




add_filter('breadcrumb_permalink_post_term', 'breadcrumb_permalink_post_term');

function breadcrumb_permalink_post_term($args)
{


    $permalink = isset($args['permalink']) ? $args['permalink'] : '';
    $taxonomy = isset($permalink['taxonomy']) ? $permalink['taxonomy'] : '';




    $category_string = get_query_var($taxonomy);
    $category_arr = array();
    $breadcrumb_items = array();



    $array_list = array();

    if (!empty($category_string)) {
        if (strpos($category_string, '/')) {

            $category_arr = explode('/', $category_string);
            $category_count = count($category_arr);
            $last_cat = $category_arr[($category_count - 1)];
            $breadcrumb_items_new = array();
            $term_data = get_term_by('slug', $last_cat, $taxonomy);

            $term_id = $term_data->term_id;
            $term_name = $term_data->name;
            $term_link = get_term_link($term_id, $taxonomy);

            $parents_id  = get_ancestors($term_id, $taxonomy);
            $parents_id = array_reverse($parents_id);

            foreach ($parents_id as $id) {

                $parent_term_link = get_term_link($id, $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link' => $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link' => $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;
        } else {

            $term_data = get_term_by('slug', $category_string, $taxonomy);
            $breadcrumb_items_new = array();


            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;

            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    } else {

        if (is_singular()) {
            $post_id = get_the_ID();

            //$terms = get_terms();
            $terms = get_the_terms($post_id, $taxonomy);

            if (!is_wp_error($terms)) {
                $term_data = isset($terms[0]) ? $terms[0] : '';

                $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                $term_name = isset($term_data->name) ? $term_data->name : '';

                if (!empty($term_id)) :
                    $term_link = get_term_link($term_id, $taxonomy);

                    $breadcrumb_items_new = array(
                        'link' => $term_link,
                        'title' => $term_name,
                    );

                    $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);


                endif;
            }
        }
    }





    return $breadcrumb_items;
}















add_filter('breadcrumb_permalink_product_tag', 'breadcrumb_permalink_product_tag');

function breadcrumb_permalink_product_tag($breadcrumb_items)
{

    $category_string = get_query_var('product_tag');
    $category_arr = array();
    $breadcrumb_items = array();



    $taxonomy = 'product_tag';
    $array_list = array();

    if (!empty($category_string)) {
        if (strpos($category_string, '/')) {

            $category_arr = explode('/', $category_string);
            $category_count = count($category_arr);
            $last_cat = $category_arr[($category_count - 1)];
            $breadcrumb_items_new = array();
            $term_data = get_term_by('slug', $last_cat, $taxonomy);

            $term_id = $term_data->term_id;
            $term_name = $term_data->name;
            $term_link = get_term_link($term_id, $taxonomy);

            $parents_id  = get_ancestors($term_id, $taxonomy);
            $parents_id = array_reverse($parents_id);

            foreach ($parents_id as $id) {

                $parent_term_link = get_term_link($id, $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link' => $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link' => $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;
        } else {

            $term_data = get_term_by('slug', $category_string, $taxonomy);
            $breadcrumb_items_new = array();


            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;

            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    } else {

        if (is_singular()) {
            $post_id = get_the_ID();

            //$terms = get_terms();
            $terms = get_the_terms($post_id, $taxonomy);
            $term_data = isset($terms[0]) ? $terms[0] : '';

            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if (!empty($term_id)) :
                $term_link = get_term_link($term_id, $taxonomy);

                $breadcrumb_items_new = array(
                    'link' => $term_link,
                    'title' => $term_name,
                );
            endif;
            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);
        }
    }





    return $breadcrumb_items;
}


















add_filter('breadcrumb_permalink_wc_shop', 'breadcrumb_permalink_wc_shop');

function breadcrumb_permalink_wc_shop($breadcrumb_items)
{

    $shop_page_id = wc_get_page_id('shop');
    return array(
        'link' => get_permalink($shop_page_id),
        'title' => get_the_title($shop_page_id),
    );
}


add_filter('breadcrumb_permalink_post_tag', 'breadcrumb_permalink_post_tag');

function breadcrumb_permalink_post_tag($breadcrumb_items)
{

    $post_id = get_the_id();

    $post_tags = get_the_tags($post_id);

    $first_tag = isset($post_tags[0]) ? $post_tags[0] : '';
    if (!empty($first_tag)) :

        $term_name = isset($first_tag->name) ? $first_tag->name : '';

        $term_id = isset($first_tag->term_id) ? $first_tag->term_id : '';
        $term_url = get_term_link($term_id);

        return array(
            'link' => $term_url,
            'title' => $term_name,
        );

    endif;
}




add_filter('breadcrumb_permalink_term_title', 'breadcrumb_permalink_term_title');

function breadcrumb_permalink_term_title($breadcrumb_items)
{

    $queried_object = get_queried_object();
    $term_name = $queried_object->name;
    $term_id = $queried_object->term_id;

    $taxonomy = $queried_object->taxonomy;
    $term_link = get_term_link($term_id, $taxonomy);

    return array(
        'link' => $term_link,
        'title' => $term_name,
    );
}


add_filter('breadcrumb_permalink_term_parent', 'breadcrumb_permalink_term_parent');

function breadcrumb_permalink_term_parent($breadcrumb_items)
{

    $queried_object = get_queried_object();



    $term_id = isset($queried_object->term_id) ? $queried_object->term_id : '';

    $term_parent_id = isset($queried_object->parent) ? $queried_object->parent : '';


    $term_parent = get_term($term_parent_id);
    $term_parent_name = isset($term_parent->name) ? $term_parent->name : '';
    $taxonomy = isset($queried_object->taxonomy) ? $queried_object->taxonomy : '';
    $term_parent_link = get_term_link($term_parent_id, $taxonomy);

    return array(
        'link' => $term_parent_link,
        'title' => $term_parent_name,
    );
}




add_filter('breadcrumb_permalink_term_ancestors', 'breadcrumb_permalink_term_ancestors');

function breadcrumb_permalink_term_ancestors($breadcrumb_items)
{

    $queried_object = get_queried_object();
    $term_name = $queried_object->name;
    $term_id = $queried_object->term_id;

    $taxonomy = $queried_object->taxonomy;
    $term_link = get_term_link($term_id, $taxonomy);


    $parents_id  = get_ancestors($term_id, $taxonomy);
    $parents_id = array_reverse($parents_id);
    $breadcrumb_items_new = [];
    foreach ($parents_id as $id) {

        $parent_term_link = get_term_link($id, $taxonomy);
        $paren_term_name = get_term_by('id', $id, $taxonomy);

        $breadcrumb_items_new[] = array(
            'link' => $parent_term_link,
            'title' => $paren_term_name->name,
        );
    }



    return $breadcrumb_items_new;
}









add_filter('breadcrumb_permalink_post_date', 'breadcrumb_permalink_post_date');

function breadcrumb_permalink_post_date($breadcrumb_items)
{

    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time('m');
    $post_date_day = get_the_time('d');
    $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);

    return array(
        'link' => $get_day_link,
        'title' => $post_date_day,
    );
}


add_filter('breadcrumb_permalink_post_month', 'breadcrumb_permalink_post_month');

function breadcrumb_permalink_post_month($breadcrumb_items)
{
    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time('m');
    $get_month_link = get_month_link($post_date_year, $post_date_month);

    return array(
        'link' => $get_month_link,
        'title' => $post_date_month,
    );
}


add_filter('breadcrumb_permalink_post_year', 'breadcrumb_permalink_post_year');

function breadcrumb_permalink_post_year($breadcrumb_items)
{

    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $get_year_link = get_year_link($post_date_year);

    return array(
        'link' => $get_year_link,
        'title' => $post_date_year,
    );
}



add_filter('breadcrumb_permalink_date_text', 'breadcrumb_permalink_post_archive_date_text');

function breadcrumb_permalink_post_archive_date_text($args)
{


    $permalink = isset($args['permalink']) ? $args['permalink'] : array();
    $date_format = isset($permalink['date_format']) ? $permalink['date_format'] : 'd';


    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time('m');
    $post_date_day = get_the_date($date_format);
    $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);

    return array(
        'link' => $get_day_link,
        'title' => $post_date_day,
    );
}



add_filter('breadcrumb_permalink_month_text', 'breadcrumb_permalink_post_archive_month_text');

function breadcrumb_permalink_post_archive_month_text($args)
{


    $permalink = isset($args['permalink']) ? $args['permalink'] : array();
    $date_format = isset($permalink['date_format']) ? $permalink['date_format'] : 'm';


    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time($date_format);
    $post_date_day = get_the_date($date_format);
    $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);

    return array(
        'link' => $get_day_link,
        'title' => $post_date_month,
    );
}



add_filter('breadcrumb_permalink_year_text', 'breadcrumb_permalink_post_archive_year_text');

function breadcrumb_permalink_post_archive_year_text($args)
{


    $permalink = isset($args['permalink']) ? $args['permalink'] : array();
    $date_format = isset($permalink['date_format']) ? $permalink['date_format'] : 'Y';


    $post_date_year = get_the_time($date_format);
    $post_date_month = get_the_time($date_format);
    $post_date_day = get_the_date($date_format);
    $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);

    return array(
        'link' => $get_day_link,
        'title' => $post_date_year,
    );
}




add_filter('breadcrumb_permalink_post_id', 'breadcrumb_permalink_post_id');

function breadcrumb_permalink_post_id($breadcrumb_items)
{
    $post_id = get_the_id();

    return array(
        'link' => get_permalink($post_id),
        'title' => $post_id,
    );
}


add_filter('breadcrumb_permalink_search_word', 'breadcrumb_permalink_search_word');

function breadcrumb_permalink_search_word($breadcrumb_items)
{

    $current_query = sanitize_text_field(get_query_var('s'));
    return array(
        'link' => '#',
        'title' => $current_query,
    );
}




//add_filter('the_title','related_post_display_auto_20200409');


function related_post_display_auto_20200409($title)
{

    $post_types = get_option('breadcrumb_display_auto_post_types');
    $breadcrumb_posttitle_positions = get_option('breadcrumb_display_auto_post_title_positions');


    $html = '';
    $post_id = get_the_ID();
    $post_type = get_post_type($post_id);


    if (in_array($post_type, $post_types) && in_the_loop()) {


        ob_start();

        echo do_shortcode('[breadcrumb]');

        $html .= ob_get_clean();

        //if( in_array('before', $breadcrumb_posttitle_positions)){
        //$html .= do_shortcode('[breadcrumb]');
        //}

        $html .= $title;
    } else {
        $html .= $title;
    }




    return $html;
}
