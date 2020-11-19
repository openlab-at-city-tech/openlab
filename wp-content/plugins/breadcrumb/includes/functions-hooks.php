<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_filter('breadcrumb_items_array', 'breadcrumb_items_override_permalinks');

function breadcrumb_items_override_permalinks($breadcrumb_items){

    $breadcrumb_options = get_option('breadcrumb_options');
    $permalinks = isset($breadcrumb_options['permalinks']) ? $breadcrumb_options['permalinks'] : array();



    if(is_singular('post') && !empty($permalinks['post'])){

        $post_id = get_the_id();
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['post']) ? $permalinks['post'] : array();

        $i = 0;
        if(!empty($post_permalinks))
        foreach ($post_permalinks as $permalinkIndex => $permalink):

            $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());

            if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                foreach ($breadcrumb_items_new[$i] as $item):
                    $breadcrumb_items_latest[] = $item;
                endforeach;

            else:
                $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
            endif;

            $i++;
        endforeach;
        return $breadcrumb_items_latest;
    }elseif(is_singular('product') && !empty($permalinks['product'])){

        $post_id = get_the_id();
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['product']) ? $permalinks['product'] : array();

        $i = 0;
        if(!empty($post_permalinks))
            foreach ($post_permalinks as $permalinkIndex => $permalink):

                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());


                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;

                else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;
        return $breadcrumb_items_latest;


    }elseif(is_singular('page') && !empty($permalinks['page'])){

        $post_id = get_the_id();
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['page']) ? $permalinks['page'] : array();


        $i = 0;
        if(!empty($post_permalinks))
            foreach ($post_permalinks as $permalinkIndex => $permalink):


                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());


                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;
                    else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;
        return $breadcrumb_items_latest;
    }
    elseif(is_singular('attachment') && !empty($permalinks['attachment'])){

        $post_id = get_the_id();
        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['attachment']) ? $permalinks['attachment'] : array();

        $i = 0;
        if(!empty($post_permalinks))
            foreach ($post_permalinks as $permalinkIndex => $permalink):

                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());

                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;

                else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;
        return $breadcrumb_items_latest;
    }else if(is_search()){

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $search_permalinks = isset($permalinks['search']) ? $permalinks['search'] : array();

        $i = 0;
        if(!empty($search_permalinks))
            foreach ($search_permalinks as $permalinkIndex => $permalink):

                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());

                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;

                else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;


        return $breadcrumb_items_latest;

    }
    elseif(is_tax('product_cat')){

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['product_cat']) ? $permalinks['product_cat'] : array();

        $i = 0;
        if(!empty($post_permalinks))
            foreach ($post_permalinks as $permalinkIndex => $permalink):

                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());


                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;

                else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;
        return $breadcrumb_items_latest;


    }
    elseif(is_tax('product_tag')){

        $breadcrumb_items_new = array();
        $breadcrumb_items_latest = array();

        $post_permalinks = isset($permalinks['product_tag']) ? $permalinks['product_tag'] : array();

        $i = 0;
        if(!empty($post_permalinks))
            foreach ($post_permalinks as $permalinkIndex => $permalink):

                $breadcrumb_items_new[$i] = apply_filters('breadcrumb_permalink_'.$permalinkIndex, array());


                if(!empty($breadcrumb_items_new[$i][0]) && is_array($breadcrumb_items_new[$i][0])):

                    foreach ($breadcrumb_items_new[$i] as $item):
                        $breadcrumb_items_latest[] = $item;
                    endforeach;

                else:
                    $breadcrumb_items_latest[] = $breadcrumb_items_new[$i];
                endif;

                $i++;
            endforeach;
        return $breadcrumb_items_latest;


    }

    else{
        return $breadcrumb_items;
    }




}


add_filter('breadcrumb_permalink_front_text', 'breadcrumb_permalink_front_text');

function breadcrumb_permalink_front_text($breadcrumb_items){

    $breadcrumb_text = get_option('breadcrumb_text', __('You are here','breadcrumb'));
    return array(
        'link'=> '#',
        'title' => $breadcrumb_text,
    );

}


add_filter('breadcrumb_permalink_home', 'breadcrumb_permalink_home');

function breadcrumb_permalink_home($breadcrumb_items){

    $breadcrumb_home_text = get_option('breadcrumb_home_text', __('Home','breadcrumb'));
    $home_url = get_bloginfo('url');

    return array(
        'link'=> $home_url,
        'title' => $breadcrumb_home_text,
    );

}


add_filter('breadcrumb_permalink_post_title', 'breadcrumb_permalink_post_title');

function breadcrumb_permalink_post_title($breadcrumb_items){
    $post_id = get_the_id();

    return array(
        'link'=> get_permalink($post_id),
        'title' => get_the_title($post_id),
    );

}


add_filter('breadcrumb_permalink_post_ancestors', 'breadcrumb_permalink_post_ancestors');

function breadcrumb_permalink_post_ancestors($breadcrumb_items){
    $post_id = get_the_id();
    $array_list = array();

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

    return $array_list;

}


add_filter('breadcrumb_permalink_post_author', 'breadcrumb_permalink_post_author');

function breadcrumb_permalink_post_author($breadcrumb_items){

    $post_id = get_the_id();

    $post = get_post($post_id);
    $author_id = $post->post_author;
    $author_posts_url = get_author_posts_url($author_id);
    $author_name = get_the_author_meta('display_name', $author_id);

    return array(
        'link'=> $author_posts_url,
        'title' => $author_name,
    );

}


add_filter('breadcrumb_permalink_post_category', 'breadcrumb_permalink_post_category');

function breadcrumb_permalink_post_category($breadcrumb_items){
    $category_string = get_query_var('category_name');
    $category_arr = array();
    $breadcrumb_items = array();

    $taxonomy = 'category';
    $array_list = array();
    $breadcrumb_items_new = array();

    if(!empty($category_string)){

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


            foreach($parents_id as $id){

                $parent_term_link = get_term_link( $id , $taxonomy);
                $paren_term_name = get_term_by('id', $id, $taxonomy);

                $breadcrumb_items_new[] = array(
                    'link'=> $parent_term_link,
                    'title' => $paren_term_name->name,
                );
            }

            $breadcrumb_items_new[] = array(
                'link'=> $term_link,
                'title' => $term_name,
            );


            $breadcrumb_items = $breadcrumb_items_new;

        }else{

            $term_data = get_term_by('slug', $category_string, $taxonomy);

            $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
            $term_name = isset($term_data->name) ? $term_data->name : '';

            if(!empty($term_id)):
                $term_link = get_term_link( $term_id , $taxonomy);

                $breadcrumb_items_new = array(
                    'link'=> $term_link,
                    'title' => $term_name,
                );
            endif;

            $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);


        }

    }else{
        $post_id = get_the_id();
        $post_terms = wp_get_post_terms($post_id,$taxonomy);

        $first_term = isset($post_terms[0]) ? $post_terms[0] : '';

        //var_dump($first_term);

        if(!empty($first_term)){
            $breadcrumb_items_new[] = array(
                'link'=> get_term_link($first_term->term_id),
                'title' => $first_term->name,
            );
        }

        $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);


    }





    return $breadcrumb_items;

}


add_filter('breadcrumb_permalink_product_cat', 'breadcrumb_permalink_product_cat');

function breadcrumb_permalink_product_cat($breadcrumb_items){
    $category_string = get_query_var('product_cat');
    $category_arr = array();
    $breadcrumb_items = array();

    $taxonomy = 'product_cat';
    $array_list = array();

    if(strpos( $category_string, '/' )){

        $category_arr = explode('/', $category_string);
        $category_count = count($category_arr);
        $last_cat = $category_arr[($category_count-1)];
        $breadcrumb_items_new = array();
        $term_data = get_term_by('slug',$last_cat, $taxonomy);

        $term_id = $term_data->term_id;
        $term_name = $term_data->name;
        $term_link = get_term_link( $term_id , $taxonomy);

        $parents_id  = get_ancestors( $term_id, $taxonomy );
        $parents_id = array_reverse($parents_id);

        foreach($parents_id as $id){

            $parent_term_link = get_term_link( $id , $taxonomy);
            $paren_term_name = get_term_by('id', $id, $taxonomy);

            $breadcrumb_items_new[] = array(
                'link'=> $parent_term_link,
                'title' => $paren_term_name->name,
            );
        }

        $breadcrumb_items_new[] = array(
            'link'=> $term_link,
            'title' => $term_name,
        );


        $breadcrumb_items = $breadcrumb_items_new;

    }else{

        $term_data = get_term_by('slug',$category_string, $taxonomy);

        $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
        $term_name = isset($term_data->name) ? $term_data->name : '';

        if(!empty($term_id)):
            $term_link = get_term_link( $term_id , $taxonomy);

            $breadcrumb_items_new = array(
                'link'=> $term_link,
                'title' => $term_name,
            );
        endif;

        $breadcrumb_items = array_merge($breadcrumb_items, $breadcrumb_items_new);


    }




    return $breadcrumb_items;

}


add_filter('breadcrumb_permalink_wc_shop', 'breadcrumb_permalink_wc_shop');

function breadcrumb_permalink_wc_shop($breadcrumb_items){

    $shop_page_id = wc_get_page_id('shop');
    return array(
        'link'=> get_permalink($shop_page_id),
        'title' => get_the_title($shop_page_id),
    );

}


add_filter('breadcrumb_permalink_post_tag', 'breadcrumb_permalink_post_tag');

function breadcrumb_permalink_post_tag($breadcrumb_items){

    $post_id = get_the_id();

    $post_tags = get_the_tags($post_id);
    //echo '<pre>'.var_export($post_tags, true).'</pre>';

    $first_tag = isset($post_tags[0]) ? $post_tags[0] : '';
    if(!empty($first_tag)):

        $term_name = isset($first_tag->name) ? $first_tag->name : '';

        $term_id = isset($first_tag->term_id) ? $first_tag->term_id : '';
        $term_url = get_term_link($term_id);

        return array(
            'link'=> $term_url,
            'title' => $term_name,
        );

    endif;
}




add_filter('breadcrumb_permalink_term_title', 'breadcrumb_permalink_term_title');

function breadcrumb_permalink_term_title($breadcrumb_items){

    $queried_object = get_queried_object();
    $term_name = $queried_object->name;
    $term_id = $queried_object->term_id;

    $taxonomy = $queried_object->taxonomy;
    $term_link = get_term_link( $term_id , $taxonomy);

    return array(
        'link'=> $term_link,
        'title' => $term_name,
    );

}







add_filter('breadcrumb_permalink_post_date', 'breadcrumb_permalink_post_date');

function breadcrumb_permalink_post_date($breadcrumb_items){

    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time('m');
    $post_date_day = get_the_time('d');
    $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);

    return array(
        'link'=> $get_day_link,
        'title' => $post_date_day,
    );

}


add_filter('breadcrumb_permalink_post_month', 'breadcrumb_permalink_post_month');

function breadcrumb_permalink_post_month($breadcrumb_items){
    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $post_date_month = get_the_time('m');
    $get_month_link = get_month_link($post_date_year,$post_date_month);

    return array(
        'link'=> $get_month_link,
        'title' => $post_date_month,
    );

}


add_filter('breadcrumb_permalink_post_year', 'breadcrumb_permalink_post_year');

function breadcrumb_permalink_post_year($breadcrumb_items){

    $post_id = get_the_id();

    $post_date_year = get_the_time('Y');
    $get_year_link = get_year_link($post_date_year);

    return array(
        'link'=> $get_year_link,
        'title' => $post_date_year,
    );

}



add_filter('breadcrumb_permalink_post_id', 'breadcrumb_permalink_post_id');

function breadcrumb_permalink_post_id($breadcrumb_items){
    $post_id = get_the_id();

    return array(
        'link'=> get_permalink($post_id),
        'title' => $post_id,
    );

}


add_filter('breadcrumb_permalink_search_word', 'breadcrumb_permalink_search_word');

function breadcrumb_permalink_search_word($breadcrumb_items){

    $current_query = sanitize_text_field(get_query_var('s'));
    return array(
        'link'=> '#',
        'title' => $current_query,
    );

}




//add_filter('the_title','related_post_display_auto_20200409');


function related_post_display_auto_20200409($title) {

    $post_types = get_option( 'breadcrumb_display_auto_post_types' );
    $breadcrumb_posttitle_positions = get_option( 'breadcrumb_display_auto_post_title_positions' );


    $html = '';
    $post_id = get_the_ID();
    $post_type = get_post_type( $post_id );


    if( in_array($post_type, $post_types) && in_the_loop()){

        //echo '<pre>'.var_export($post_types, true).'</pre>';
        //echo '<pre>'.var_export($breadcrumb_posttitle_positions, true).'</pre>';

        ob_start();

        echo do_shortcode('[breadcrumbgh]');

        $html .= ob_get_clean();

        //if( in_array('before', $breadcrumb_posttitle_positions)){
        //$html .= do_shortcode('[breadcrumb]');
        //}

        $html .= $title;

    }else{
        $html .= $title;
    }




    return $html;

}