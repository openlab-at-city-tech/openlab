<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


add_action('breadcrumb_main', 'breadcrumb_main_items');

function breadcrumb_main_items(){
    $breadcrumb_items = breadcrumb_trail_array_list();

    $breadcrumb_items = apply_filters('breadcrumb_items_array', $breadcrumb_items);

    //echo '<pre>'.var_export($breadcrumb_items, true).'</pre>';


    if(!empty($breadcrumb_items)):
        ?>
        <ul>
            <?php
            foreach ($breadcrumb_items as $item_index => $item):
                do_action('breadcrumb_main_item_loop', $item);
            endforeach;
            ?>
        </ul>
        <?php
    else:
        ?>
        <style type="text/css">
            .breadcrumb-container{
                display: none;
            }
        </style>
        <?php

    endif;

}



add_action('breadcrumb_main_item_loop', 'breadcrumb_main_item_loop');

function breadcrumb_main_item_loop($item){

    $breadcrumb_word_char = get_option('breadcrumb_word_char');
    $breadcrumb_word_char_count = get_option('breadcrumb_word_char_count');
    $breadcrumb_word_char_end = get_option('breadcrumb_word_char_end');
    $breadcrumb_separator = get_option('breadcrumb_separator','&raquo;');

    $title_original = !empty($item['title']) ? $item['title'] : '';
    $title = apply_filters('breadcrumb_link_text', $title_original);

    $link = isset($item['link']) ? $item['link'] : '';
    $link = apply_filters('breadcrumb_link_url', $link);

    if(!empty($title)){
        ?>
        <li ><a title="<?php echo $title_original; ?>" href="<?php echo $link; ?>"><span><?php echo $title; ?></span></a><span class="separator"><?php echo $breadcrumb_separator; ?></span></li>
        <?php
    }


}


add_action('breadcrumb_main', 'breadcrumb_main_schema');

function breadcrumb_main_schema(){
    $breadcrumb_items = breadcrumb_trail_array_list();

    $breadcrumb_items_count = count($breadcrumb_items);

    //echo '<pre>'.var_export($breadcrumb_items_count, true).'</pre>';

    ?>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement":
            [
                <?php
                $i = 1;
                if(!empty($breadcrumb_items))
                foreach ($breadcrumb_items as $item):
                    $title = !empty($item['title']) ? $item['title'] : '';
                    $link = isset($item['link']) ? $item['link'] : '';

                    if(!empty($title)){
                        ?>
                        {
                            "@type": "ListItem",
                            "position":<?php echo $i; ?>,
                            "item":
                            {
                                "@id": "<?php echo $link; ?>",
                                "name": "<?php echo $title; ?>"
                            }
                        }<?php if($i < $breadcrumb_items_count) echo ','; ?>
                        <?php
                    }


                    $i++;
                endforeach;
                ?>
            ]
        }
    </script>
    <?php
}




add_action('breadcrumb_main', 'breadcrumb_main_style_css');

function breadcrumb_main_style_css(){

    $breadcrumb_font_size = get_option('breadcrumb_font_size');
    $breadcrumb_link_color = get_option('breadcrumb_link_color','#fff');
    $breadcrumb_separator_color = get_option('breadcrumb_separator_color');
    $breadcrumb_bg_color = get_option('breadcrumb_bg_color','#278df4');
    $breadcrumb_padding = get_option('breadcrumb_padding');
    $breadcrumb_margin = get_option('breadcrumb_margin');


    $breadcrumb_word_char = get_option('breadcrumb_word_char');
    $breadcrumb_word_char_count = get_option('breadcrumb_word_char_count');
    $breadcrumb_word_char_end = get_option('breadcrumb_word_char_end');

    $breadcrumb_display_home = get_option('breadcrumb_display_home');
    $breadcrumb_home_text = get_option('breadcrumb_home_text');
    $breadcrumb_url_hash = get_option('breadcrumb_url_hash');

    $breadcrumb_separator = get_option('breadcrumb_separator','&raquo;');
    $breadcrumb_display_last_separator = get_option('breadcrumb_display_last_separator');
    $breadcrumb_themes = get_option( 'breadcrumb_themes', 'theme5' );


    ?>
    <style type="text/css">
        .breadcrumb-container {
            font-size: 13px;
        }
        .breadcrumb-container ul {
            margin: 0;
            padding: 0;
        }
        .breadcrumb-container li {
            box-sizing: unset;
            display: inline-block;
            margin: 0;
            padding: 0;
        }
        .breadcrumb-container li a {
            box-sizing: unset;
            padding: 0 10px;
        }
        .breadcrumb-container {
            font-size: <?php echo $breadcrumb_font_size; ?>  !important;
            padding: <?php echo $breadcrumb_padding; ?>;
            margin: <?php echo $breadcrumb_margin; ?>;
        }
        .breadcrumb-container li a{
            color:  <?php echo $breadcrumb_link_color; ?>  !important;
            font-size:  <?php echo $breadcrumb_font_size; ?>  !important;
            line-height:  <?php echo $breadcrumb_font_size; ?>  !important;
        }
        .breadcrumb-container li .separator {
            color: <?php echo $breadcrumb_separator_color; ?>  !important;
            font-size:  <?php echo $breadcrumb_font_size; ?>  !important;
        }
        <?php
        if($breadcrumb_display_last_separator=='no'){
            ?>
            .breadcrumb-container li:last-child .separator {
                display: none;
            }
            <?php
    }
     ?>
    </style>
    <?php

    $themes_css = breadcrumb_themes_css($breadcrumb_themes);

    echo $themes_css;


}



add_action('breadcrumb_main', 'breadcrumb_main_custom_scripts');

function breadcrumb_main_custom_scripts(){

    $breadcrumb_custom_js = get_option( 'breadcrumb_custom_js' );
    $breadcrumb_custom_css = get_option('breadcrumb_custom_css');

    ?>
    <style type="text/css">
        <?php
        echo esc_html($breadcrumb_custom_css);
        ?>
    </style>
    <script>
        <?php
        echo esc_js($breadcrumb_custom_js);
        ?>
    </script>
    <?php
}

