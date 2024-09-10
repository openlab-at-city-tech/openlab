<?php
if (!defined('ABSPATH')) exit;  // if direct access



add_action('breadcrumb_settings_tabs_content_options', 'breadcrumb_settings_tabs_content_options');

function breadcrumb_settings_tabs_content_options()
{

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_text = get_option('breadcrumb_text');
    $breadcrumb_separator = get_option('breadcrumb_separator');
    $breadcrumb_display_last_separator = get_option('breadcrumb_display_last_separator');
    $breadcrumb_word_char = get_option('breadcrumb_word_char');
    $breadcrumb_word_char_count = get_option('breadcrumb_word_char_count');
    $breadcrumb_word_char_end = get_option('breadcrumb_word_char_end');
    $breadcrumb_display_home = get_option('breadcrumb_display_home');
    $breadcrumb_home_text = get_option('breadcrumb_home_text');
    $breadcrumb_url_hash = get_option('breadcrumb_url_hash');
    $breadcrumb_hide_wc_breadcrumb = get_option('breadcrumb_hide_wc_breadcrumb');
    //    $breadcrumb_display_auto_post_types = get_option( 'breadcrumb_display_auto_post_types' );
    //    $breadcrumb_display_auto_post_title_positions = get_option( 'breadcrumb_display_auto_post_title_positions' );


    //var_dump($breadcrumb_home_text);

?>


    <div class="section">
        <div class="section-title"><?php echo __('General option', 'breadcrumb'); ?></div>
        <p class="description section-description"><?php echo __('Set some basic option to get start.', 'breadcrumb'); ?></p>

        <?php

        $args = array(
            'id'        => 'breadcrumb_text',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb front text', 'breadcrumb'),
            'details'    => __('Display custom text before breadcrumb.', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_text,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_separator',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb separator text', 'breadcrumb'),
            'details'    => __('You can display custom separator. ex: <code>&raquo;</code>', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_separator,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);





        $args = array(
            'id'        => 'breadcrumb_display_last_separator',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Display last separator', 'breadcrumb'),
            'details'    => __('Display or hide last separator.', 'breadcrumb'),
            'type'        => 'select',
            'value'        => $breadcrumb_display_last_separator,
            'default'        => 'no',
            'args'        => array(
                'no' => __('No', 'breadcrumb'),
                'yes' => __('Yes', 'breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'        => 'breadcrumb_word_char',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb link text limit by?', 'breadcrumb'),
            'details'    => __('You can limit link text by word or character', 'breadcrumb'),
            'type'        => 'select',
            'value'        => $breadcrumb_word_char,
            'default'        => 'word',
            'args'        => array(
                'none' => __('None', 'breadcrumb'),
                'word' => __('Word', 'breadcrumb'),
                'character' => __('Character', 'breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_word_char_count',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Limit count', 'breadcrumb'),
            'details'    => __('Set custom limit value, number only.', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_word_char_count,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'        => 'breadcrumb_word_char_end',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Ending character', 'breadcrumb'),
            'details'    => __('Set custom Ending character, ex: ...', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_word_char_end,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_display_home',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Display "Home" on breadcrumb?', 'breadcrumb'),
            'details'    => __('You can hide or display Home on breadcrumb.', 'breadcrumb'),
            'type'        => 'select',
            'value'        => $breadcrumb_display_home,
            'default'        => 'no',
            'args'        => array(
                'no' => __('No', 'breadcrumb'),
                'yes' => __('Yes', 'breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'        => 'breadcrumb_home_text',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Custom home text', 'breadcrumb'),
            'details'    => __('You can set custom text for "Home"', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_home_text,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_url_hash',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Current URL hash', 'breadcrumb'),
            'details'    => __('If you want to keep # on current url, otherwise keep empty', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_url_hash,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_hide_wc_breadcrumb',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Hide WooCommerce breadcrumb', 'breadcrumb'),
            'details'    => __('Display or hide WooCommerce default breadcrumb', 'breadcrumb'),
            'type'        => 'select',
            'value'        => $breadcrumb_hide_wc_breadcrumb,
            'default'        => 'no',
            'args'        => array(
                'no' => __('No', 'breadcrumb'),
                'yes' => __('Yes', 'breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);


        //
        //        $post_types_list = get_post_types( '', 'names' );
        //        $post_types_array = array();
        //
        //        foreach ( $post_types_list as $post_type ) {
        //
        //            $obj = get_post_type_object($post_type);
        //            $singular_name = $obj->labels->singular_name;
        //            $post_types_array[$post_type] = $singular_name;
        //        }
        //
        //
        //        $args = array(
        //            'id'		=> 'breadcrumb_display_auto_post_types',
        //            //'parent'		=> 'related_post_settings',
        //            'title'		=> __('Choose post types','related-post'),
        //            'details'	=> __('Display related post automatically under selected post types.','related-post'),
        //            'type'		=> 'checkbox',
        //            'value'		=> $breadcrumb_display_auto_post_types,
        //            'default'		=> array(),
        //            'style'		=> array('inline' => false),
        //            'args'		=> $post_types_array,
        //        );
        //
        //        $settings_tabs_field->generate_field($args);
        //
        //        $args = array(
        //            'id'		=> 'breadcrumb_display_auto_post_title_positions',
        //            //'parent'		=> 'related_post_settings',
        //            'title'		=> __('Title positions','related-post'),
        //            'details'	=> __('Display before or after post title on single post types.','related-post'),
        //            'type'		=> 'checkbox',
        //            'value'		=> $breadcrumb_display_auto_post_title_positions,
        //            'default'		=> array(),
        //            'style'		=> array('inline' => false),
        //            'args'		=> array('before' => 'Before', 'after'=> 'After'),
        //
        //        );
        //
        //        $settings_tabs_field->generate_field($args);


        ?>


    </div>
<?php

}



add_action('breadcrumb_settings_tabs_content_builder', 'breadcrumb_settings_tabs_content_builder');

function breadcrumb_settings_tabs_content_builder()
{

    $settings_tabs_field = new settings_tabs_field();
    $breadcrumb_options = get_option('breadcrumb_options');
    $permalinks = isset($breadcrumb_options['permalinks']) ? $breadcrumb_options['permalinks'] : array();


    $posttypes_array = breadcrumb_posttypes_array();
    $breadcrumb_pages_objects = breadcrumb_pages_objects();


    $page_views = breadcrumb_page_views();

    $breadcrumb_tags = breadcrumb_tags();
    $breadcrumb_tag_options = array();

    foreach ($breadcrumb_tags as $tagGroupIndex => $tags) :
        foreach ($tags as $tagIndex => $tag) :

            ob_start();

            do_action('breadcrumb_tag_options_' . $tagIndex);

            $breadcrumb_tag_options[$tagIndex] = ob_get_clean();

        endforeach;
    endforeach;

    $breadcrumb_tag_options = json_encode($breadcrumb_tag_options);


?>
    <div class="section">
        <div class="section-title"><?php echo __('Breadcrumb builder', 'breadcrumb'); ?></div>
        <p class="description section-description"><?php echo __('Build your own breadcrumb.', 'breadcrumb'); ?></p>

        <?php


        ob_start();
        ?>
        <script>
            jQuery(document).ready(function($) {
                breadcrumb_tag_options = <?php echo $breadcrumb_tag_options; ?>;

                console.log(breadcrumb_tag_options);


                $(document).on('click', '.breadcrumb-tags span', function() {
                    tag_id = $(this).attr('tag_id');
                    input_name = $(this).attr('input_name');
                    isPro = $(this).attr('is-pro');

                    console.log(isPro);

                    if (isPro == 1) {

                        alert('Sorry this element only avilable in pro version')

                    } else {
                        tag_options_html = breadcrumb_tag_options[tag_id];
                        var res = tag_options_html.replaceAll("{input_name}", input_name);
                        var res2 = res.replaceAll("[0]", "[" + Date.now() + "]");

                        console.log(res2);


                        $(this).parent().parent().children('.elements').append(res2);
                    }







                })
            })
        </script>



        <div class="output_posttypes">

            <?php

            foreach ($page_views as $view_type => $view) {

            ?>
                <h2 style="margin: 50px 0 10px 0;font-size:25px"><?php echo ucfirst(str_replace('_', ' ', esc_html($view_type))); ?></h2>
                <hr>
                <?php




                foreach ($view as $postType => $postTypeData) :

                    $post_type_name = isset($postTypeData['name']) ? $postTypeData['name'] : '';


                    if (empty($post_type_name)) continue;

                ?>
                    <div class="item">
                        <p style="font-weight: bold;font-size:18px"><?php echo esc_html($post_type_name); ?></p>
                        <div class="breadcrumb-tags">
                            <?php

                            if (!empty($breadcrumb_tags[$postType]))
                                foreach ($breadcrumb_tags[$postType] as $tag_id => $tag) :
                                    $tag_name = isset($tag['name']) ? $tag['name'] : '';
                                    $tag_is_pro = isset($tag['is_pro']) ? $tag['is_pro'] : false;

                                    $input_name = 'breadcrumb_options[permalinks]' . '[' . $postType . ']';

                            ?>
                                <span <?php echo ($tag_is_pro) ? 'is-pro="1"' : ''; ?> input_name="<?php echo esc_attr($input_name); ?>" tag_id="<?php echo esc_attr($tag_id); ?>"><?php echo esc_html($tag_name); ?></span>
                            <?php
                                endforeach;
                            ?>
                        </div>
                        <div class="elements expandable sortable">

                            <?php
                            $post_permalinks = isset($permalinks[$postType]) ? $permalinks[$postType] : array();
                            $args = array('input_name' => 'breadcrumb_options[permalinks]' . '[' . $postType . ']');

                            //echo '<pre>' . var_export($post_permalinks, true) . '</pre>';


                            if (!empty($post_permalinks)) :
                                foreach ($post_permalinks as $permalinkIndex => $permalink) {

                                    $elementId = isset($permalink['elementId']) ? $permalink['elementId'] : '';


                                    //var_dump($permalinkIndex);
                                    $args['options'] = $permalink;
                                    $args['index'] = $permalinkIndex;

                                    do_action('breadcrumb_tag_options_' . $elementId, $args);
                                }
                            else :
                            ?>
                                <div class="empty-element">
                                    <?php echo sprintf(__('%s Click to add tags.', 'breadcrumb'), '<i class="far fa-hand-point-up"></i>') ?>
                                </div>
                            <?php
                            endif;




                            ?>

                        </div>
                    </div>
            <?php
                endforeach;
            }
            ?>

        </div>

        <style type="text/css">
            .output_posttypes {}

            span[is-pro] {
                opacity: 0.5;
            }

            .output_posttypes .item {}

            .output_posttypes .breadcrumb-tags {}

            .output_posttypes .breadcrumb-tags span {
                display: inline-block;
                padding: 2px 10px;
                margin: 5px 5px 5px 0;
                background: #eaeaeabf;
                cursor: pointer;
                border-radius: 3px;
                border: 1px solid #a7a7a7;
            }

            .output_posttypes .breadcrumb-tags span:hover {
                background: #dadada;
            }

            .output_posttypes .empty-element {
                padding: 10px 10px;
                background: #f1f1f1;
                border: 1px dashed #999;
                margin-top: 15px;
            }
        </style>

        <?php

        $html = ob_get_clean();

        $args = array(
            'id'        => 'output_posttypes_args',
            //            'parent'		=> 'related_post_settings',
            'title'        => __('Page objects', 'breadcrumb'),
            'details'    => '',
            'type'        => 'custom_html',
            'html'        => $html,

        );

        $settings_tabs_field->generate_field($args);


        ?>



    </div>


<?php


}




add_action('breadcrumb_settings_tabs_content_style', 'breadcrumb_settings_tabs_content_style');

function breadcrumb_settings_tabs_content_style()
{

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_padding = get_option('breadcrumb_padding');
    $breadcrumb_margin = get_option('breadcrumb_margin');
    $breadcrumb_bg_color = get_option('breadcrumb_bg_color');
    $breadcrumb_link_color = get_option('breadcrumb_link_color');
    $breadcrumb_font_size = get_option('breadcrumb_font_size');

    $breadcrumb_themes = get_option('breadcrumb_themes');
    $breadcrumb_separator_color = get_option('breadcrumb_separator_color');



?>
    <div class="section">
        <div class="section-title"><?php echo __('Choose style', 'breadcrumb'); ?></div>
        <p class="description section-description"><?php echo __('Customize the breadcrumb.', 'breadcrumb'); ?></p>

        <?php



        $args = array(
            'id'        => 'breadcrumb_themes',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb Themes', 'breadcrumb'),
            'details'    => __('Choose breadcrumb theme', 'breadcrumb'),
            'type'        => 'radio_image',
            'value'        => $breadcrumb_themes,
            'default'        => 'theme5',
            'width'        => '350px',
            'args'        => apply_filters('breadcrumb_theme_args', array(

                'theme1' => array('name' => 'theme1', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme1.png'),
                'theme2' => array('name' => 'theme1', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme2.png'),

                'theme3' => array('name' => 'theme1', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme3.png'),
                'theme4' => array('name' => 'theme1', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme4.png'),

                'theme5' => array('name' => 'theme5', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme5.png'),
                'theme6' => array('name' => 'theme6', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme6.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),

                'theme7' => array('name' => 'theme7', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme7.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme8' => array('name' => 'theme8', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme8.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme9' => array('name' => 'theme9', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme9.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme10' => array('name' => 'theme10', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme10.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme11' => array('name' => 'theme11', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme11.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme12' => array('name' => 'theme12', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme12.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme13' => array('name' => 'theme13', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme13.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme14' => array('name' => 'theme14', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme14.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),
                'theme15' => array('name' => 'theme15', 'thumb' => breadcrumb_plugin_url . 'assets/admin/images/theme15.png', 'disabled' => true, 'pro_msg' => 'Only in pro'),




            )),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'        => 'breadcrumb_font_size',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb font size', 'breadcrumb'),
            'details'    => __('Set custom font size', 'breadcrumb'),
            'type'        => 'text',
            'value'        => $breadcrumb_font_size,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'        => 'breadcrumb_padding',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb container padding', 'breadcrumb'),
            'details'    => __('Put custom padding size for breadcrumb container.', 'breadcrumb'),
            'type'        => 'text',
            'placeholder'        => '10px',
            'value'        => $breadcrumb_padding,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'        => 'breadcrumb_margin',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb container margin', 'breadcrumb'),
            'details'    => __('Put custom margin size for breadcrumb container.', 'breadcrumb'),
            'type'        => 'text',
            'placeholder'        => '10px',
            'value'        => $breadcrumb_margin,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'        => 'breadcrumb_bg_color',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb link background color', 'breadcrumb'),
            'details'    => __('Choose custom background color for links', 'breadcrumb'),
            'type'        => 'colorpicker',
            'value'        => $breadcrumb_bg_color,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'        => 'breadcrumb_link_color',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb link color', 'breadcrumb'),
            'details'    => __('Choose custom link color', 'breadcrumb'),
            'type'        => 'colorpicker',
            'value'        => $breadcrumb_link_color,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_separator_color',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Breadcrumb separator color', 'breadcrumb'),
            'details'    => __('Choose custom separator color', 'breadcrumb'),
            'type'        => 'colorpicker',
            'value'        => $breadcrumb_separator_color,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);

        ?>


    </div>
<?php

}







add_action('breadcrumb_settings_tabs_content_custom_scripts', 'breadcrumb_settings_tabs_content_custom_scripts');

function breadcrumb_settings_tabs_content_custom_scripts()
{

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_custom_css = get_option('breadcrumb_custom_css');
    $breadcrumb_custom_js = get_option('breadcrumb_custom_js');


?>
    <div class="section">
        <div class="section-title"><?php echo __('Custom scripts', 'breadcrumb'); ?></div>
        <p class="description section-description"><?php echo __('Add your own scripts and style css.', 'breadcrumb'); ?></p>

        <?php

        $args = array(
            'id'        => 'breadcrumb_custom_css',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Custom CSS', 'breadcrumb'),
            'details'    => __('Add your own CSS, do not use &lt;style>&lt;/style> tag. its recommend to use <code>!important</code> to override.', 'breadcrumb'),
            'type'        => 'scripts_css',
            'value'        => $breadcrumb_custom_css,
            'default'        => '.breadcrumb-container{}&#10;.breadcrumb-container ul{}&#10;.breadcrumb-container li{}&#10;.breadcrumb-container a{}&#10;.breadcrumb-container .separator{}&#10;',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'        => 'breadcrumb_custom_js',
            //'parent' => 'breadcrumb_options',
            'title'        => __('Custom JS', 'breadcrumb'),
            'details'    => __('Add your own JS, do not use &lt;script>&lt;/script> tag.', 'breadcrumb'),
            'type'        => 'scripts_js',
            'value'        => $breadcrumb_custom_js,
            'default'        => '',
        );

        $settings_tabs_field->generate_field($args);


        ?>


    </div>
    <?php

}








add_action('breadcrumb_settings_tabs_content_help_support', 'breadcrumb_settings_tabs_content_help_support');

if (!function_exists('breadcrumb_settings_tabs_content_help_support')) {
    function breadcrumb_settings_tabs_content_help_support($tab)
    {

        $settings_tabs_field = new settings_tabs_field();

    ?>
        <div class="section">
            <div class="section-title"><?php echo __('Get support', 'breadcrumb'); ?></div>
            <p class="description section-description"><?php echo __('Use following to get help and support from our expert team.', 'breadcrumb'); ?></p>

            <?php


            ob_start();
            ?>

            <div class="copy-to-clipboard">
                <input type="text" value="[breadcrumb]"> <span class="copied"><?php echo __('Copied', 'breadcrumb'); ?></span>
                <p class="description"><?php echo __('You can use this shortcode under post content', 'breadcrumb'); ?></p>
            </div>


            <div class="copy-to-clipboard">
                <textarea cols="50" rows="2" style="background:#bfefff" onClick="this.select();"><?php echo '<?php echo do_shortcode("[breadcrumb';
                                                                                                    echo "]";
                                                                                                    echo '"); ?>'; ?></textarea> <span class="copied"><span class="copied"><?php echo __('Copied', 'breadcrumb'); ?></span>
                    <p class="description"><?php echo __('PHP Code, you can use under theme .php files.', 'breadcrumb'); ?></p>
            </div>



            <style type="text/css">
                .copy-to-clipboard {}

                .copy-to-clipboard .copied {
                    display: none;
                    background: #e5e5e5;
                    padding: 4px 10px;
                    line-height: normal;
                }
            </style>

            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.copy-to-clipboard input, .copy-to-clipboard textarea', function() {
                        $(this).focus();
                        $(this).select();
                        document.execCommand('copy');
                        $(this).parent().children('.copied').fadeIn().fadeOut(2000);
                    })
                })
            </script>
            <?php
            $html = ob_get_clean();
            $args = array(
                'id' => 'breadcrumb_shortcodes',
                'title' => __('Get shortcode', 'breadcrumb'),
                'details' => '',
                'type' => 'custom_html',
                'html' => $html,
            );
            $settings_tabs_field->generate_field($args);



            ob_start();
            ?>

            <p><?php echo __('Ask question for free on our forum and get quick reply from our expert team members.', 'breadcrumb'); ?></p>
            <a class="button" href="https://www.pickplugins.com/create-support-ticket/"><?php echo __('Create support ticket', 'breadcrumb'); ?></a>

            <p><?php echo __('Read our documentation before asking your question.', 'breadcrumb'); ?></p>
            <a class="button" href="https://www.pickplugins.com/documentation/breadcrumb/"><?php echo __('Documentation', 'breadcrumb'); ?></a>

            <p><?php echo __('Watch video tutorials.', 'breadcrumb'); ?></p>
            <a class="button" href="https://www.youtube.com/playlist?list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb"><i class="fab fa-youtube"></i> <?php echo __('All tutorials', 'breadcrumb'); ?></a>

            <ul>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HTbEIOEcc0c&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb"><?php echo __('Install & setup', 'breadcrumb'); ?></a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=jc1EzF_5kxs&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=2"><?php echo __('Limit link text', 'breadcrumb'); ?></a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=91fC7hOl6W0&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=3"><?php echo __('Customize home text', 'breadcrumb'); ?></a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=B3xpe9BZWWI&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=4"><?php echo __('Install pro and setup', 'breadcrumb'); ?></a> [Premium]</li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=xdPiM7UlNTs&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=5"><?php echo __('Hide on archives', 'breadcrumb'); ?></a> [Premium]</li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=l1LA5m6HaRQ&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=6"><?php echo __('Hide by post types', 'breadcrumb'); ?></a> [Premium]</li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=LJg_d7UUTEA&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=7"><?php echo __('Hide by post ids', 'breadcrumb'); ?></a> [Premium]</li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=7mYp27fzXY0&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=8"><?php echo __('Change style', 'breadcrumb'); ?></a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HgFRmOqi-yk&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=9"><?php echo __('Build your own breadcrumb', 'breadcrumb'); ?></a></li>
                <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=KjyBEhzH-N8&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=10"><?php echo __('Customize breadcrumb items', 'breadcrumb'); ?></a></li>

            </ul>



            <?php

            $html = ob_get_clean();

            $args = array(
                'id'        => 'get_support',
                //                'parent'		=> 'related_post_settings',
                'title'        => __('Ask question', 'breadcrumb'),
                'details'    => '',
                'type'        => 'custom_html',
                'html'        => $html,

            );

            $settings_tabs_field->generate_field($args);


            ob_start();
            ?>

            <p class=""><?php echo __('We wish your 2 minutes to write your feedback about plugin. give us 5 star.', 'breadcrumb'); ?> <span style="color: #ffae19"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span></p>

            <a target="_blank" href="https://wordpress.org/support/plugin/breadcrumb/reviews/#new-post" class="button"><i class="fab fa-wordpress"></i> <?php echo __('Write a review', 'breadcrumb'); ?></a>


            <?php

            $html = ob_get_clean();

            $args = array(
                'id'        => 'reviews',
                //                'parent'		=> 'related_post_settings',
                'title'        => __('Submit reviews', 'breadcrumb'),
                'details'    => '',
                'type'        => 'custom_html',
                'html'        => $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>


        </div>
    <?php


    }
}




add_action('breadcrumb_settings_tabs_content_buy_pro', 'breadcrumb_settings_tabs_content_buy_pro');

if (!function_exists('breadcrumb_settings_tabs_content_buy_pro')) {
    function breadcrumb_settings_tabs_content_buy_pro($tab)
    {

        $settings_tabs_field = new settings_tabs_field();


    ?>
        <div class="section">
            <div class="section-title"><?php echo __('Get Premium', 'breadcrumb'); ?></div>
            <p class="description section-description"><?php echo __('Thanks for using our plugin, if you looking for some advance feature please buy premium version.', 'breadcrumb'); ?></p>

            <?php


            ob_start();
            ?>

            <p><?php echo __('If you love our plugin and want more feature please consider to buy pro version.', 'breadcrumb'); ?></p>
            <a class="button" href="https://pickplugins.com/breadcrumb/?ref=dashobard"><?php echo __('Buy premium', 'breadcrumb'); ?></a>

            <h2><?php echo __('See the differences', 'breadcrumb'); ?></h2>

            <table class="pro-features">
                <thead>
                    <tr>
                        <th class="col-features"><?php echo __('Features', 'breadcrumb'); ?></th>
                        <th class="col-free"><?php echo __('Free', 'breadcrumb'); ?></th>
                        <th class="col-pro"><?php echo __('Premium', 'breadcrumb'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td class="col-features"><?php echo __('Hide on archives', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Hide by post types', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>


                <tr>
                    <td class="col-features"><?php echo __('Hide by post ids', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Extra ready 10 themes', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb builder for archives', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-times"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb builder for posttypes', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb front text', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb separator text', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Display or hide last separator', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb link text limit', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Ending character', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Display "Home" on breadcrumb', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Custom home text', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>



                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb text font size', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb link background color', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb link color', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Breadcrumb separator color', 'breadcrumb'); ?></td>
                    <td><i class="fas fa-check"></i></td>
                    <td><i class="fas fa-check"></i></td>
                </tr>

                <tr>
                    <th class="col-features"><?php echo __('Features', 'breadcrumb'); ?></th>
                    <th class="col-free"><?php echo __('Free', 'breadcrumb'); ?></th>
                    <th class="col-pro"><?php echo __('Premium', 'breadcrumb'); ?></th>
                </tr>
                <tr>
                    <td class="col-features"><?php echo __('Buy now', 'breadcrumb'); ?></td>
                    <td> </td>
                    <td><a class="button" href="https://pickplugins.com/breadcrumb/?ref=dashobard"><?php echo __('Buy premium', 'breadcrumb'); ?></a></td>
                </tr>

            </table>



            <?php

            $html = ob_get_clean();

            $args = array(
                'id'        => 'get_pro',
                //                'parent'		=> 'related_post_settings',
                'title'        => __('Get pro version', 'breadcrumb'),
                'details'    => '',
                'type'        => 'custom_html',
                'html'        => $html,

            );

            $settings_tabs_field->generate_field($args);


            ?>


        </div>

        <style type="text/css">
            .pro-features {
                margin: 30px 0;
                border-collapse: collapse;
                border: 1px solid #ddd;
            }

            .pro-features th {
                width: 120px;
                background: #ddd;
                padding: 10px;
            }

            .pro-features tr {}

            .pro-features td {
                border-bottom: 1px solid #ddd;
                padding: 10px 10px;
                text-align: center;
            }

            .pro-features .col-features {
                width: 230px;
                text-align: left;
            }

            .pro-features .col-free {}

            .pro-features .col-pro {}

            .pro-features i.fas.fa-check {
                color: #139e3e;
                font-size: 16px;
            }

            .pro-features i.fas.fa-times {
                color: #f00;
                font-size: 17px;
            }
        </style>
    <?php


    }
}





add_action('breadcrumb_settings_tabs_right_panel_options', 'breadcrumb_settings_tabs_right_panel_options');
add_action('breadcrumb_settings_tabs_right_panel_builder', 'breadcrumb_settings_tabs_right_panel_options');
add_action('breadcrumb_settings_tabs_right_panel_style', 'breadcrumb_settings_tabs_right_panel_options');
add_action('breadcrumb_settings_tabs_right_panel_custom_scripts', 'breadcrumb_settings_tabs_right_panel_options');
add_action('breadcrumb_settings_tabs_right_panel_help_support', 'breadcrumb_settings_tabs_right_panel_options');
add_action('breadcrumb_settings_tabs_right_panel_buy_pro', 'breadcrumb_settings_tabs_right_panel_options');



if (!function_exists('breadcrumb_settings_tabs_right_panel_options')) {
    function breadcrumb_settings_tabs_right_panel_options($tab)
    {

    ?>
        <h3><?php echo __('Help & Support', 'breadcrumb'); ?></h3>
        <p><?php echo __('Ask question for free on our forum and get quick reply from our expert team members.', 'breadcrumb'); ?></p>
        <a class="button" href="https://www.pickplugins.com/create-support-ticket/"><?php echo __('Create support ticket', 'breadcrumb'); ?></a>

        <p><?php echo __('Read our documentation before asking your question.', 'breadcrumb'); ?></p>
        <a class="button" href="https://www.pickplugins.com/documentation/breadcrumb/"><?php echo __('Documentation', 'breadcrumb'); ?></a>

        <p><?php echo __('Watch video tutorials.', 'breadcrumb'); ?></p>
        <a class="button" href="https://www.youtube.com/playlist?list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb"><i class="fab fa-youtube"></i> <?php echo __('All tutorials', 'breadcrumb'); ?></a>

        <ul>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HTbEIOEcc0c&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb"><?php echo __('Install & setup', 'breadcrumb'); ?></a></li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=jc1EzF_5kxs&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=2"><?php echo __('Limit link text', 'breadcrumb'); ?></a></li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=91fC7hOl6W0&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=3"><?php echo __('Customize home text', 'breadcrumb'); ?></a></li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=B3xpe9BZWWI&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=4"><?php echo __('Install pro and setup', 'breadcrumb'); ?></a> [Premium]</li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=xdPiM7UlNTs&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=5"><?php echo __('Hide on archives', 'breadcrumb'); ?></a> [Premium]</li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=l1LA5m6HaRQ&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=6"><?php echo __('Hide by post types', 'breadcrumb'); ?></a> [Premium]</li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=LJg_d7UUTEA&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=7"><?php echo __('Hide by post ids', 'breadcrumb'); ?></a> [Premium]</li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=7mYp27fzXY0&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=8"><?php echo __('Change style', 'breadcrumb'); ?></a></li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=HgFRmOqi-yk&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=9"><?php echo __('Build your own breadcrumb', 'breadcrumb'); ?></a></li>
            <li><i class="far fa-dot-circle"></i> <a href="https://www.youtube.com/watch?v=KjyBEhzH-N8&list=PL0QP7T2SN94bnUjguNbBXAjW1yJjjeLtb&index=10"><?php echo __('Customize breadcrumb items', 'breadcrumb'); ?></a></li>

        </ul>

        <h3><?php echo __('Submit reviews', 'breadcrumb'); ?></h3>

        <p class=""><?php echo __('We wish your 2 minutes to write your feedback about plugin. give us', 'breadcrumb'); ?> <br /><span style="color: #ffae19"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span></p>

        <a target="_blank" href="https://wordpress.org/support/plugin/breadcrumb/reviews/#new-post" class="button"><i class="fab fa-wordpress"></i> Write a review</a>

<?php

    }
}






add_action('breadcrumb_settings_save', 'breadcrumb_settings_save');



if (!function_exists('breadcrumb_settings_save')) {
    function breadcrumb_settings_save()
    {

        $breadcrumb_options = isset($_POST['breadcrumb_options']) ? breadcrumb_recursive_sanitize_arr($_POST['breadcrumb_options']) : array();
        update_option('breadcrumb_options', $breadcrumb_options);

        $breadcrumb_text = wp_kses_post($_POST['breadcrumb_text']);
        update_option('breadcrumb_text', $breadcrumb_text);

        $breadcrumb_separator = wp_kses_post($_POST['breadcrumb_separator']);
        update_option('breadcrumb_separator', $breadcrumb_separator);

        $breadcrumb_display_last_separator = sanitize_text_field($_POST['breadcrumb_display_last_separator']);
        update_option('breadcrumb_display_last_separator', $breadcrumb_display_last_separator);

        $breadcrumb_word_char = sanitize_text_field($_POST['breadcrumb_word_char']);
        update_option('breadcrumb_word_char', $breadcrumb_word_char);

        $breadcrumb_word_char_count = sanitize_text_field($_POST['breadcrumb_word_char_count']);
        update_option('breadcrumb_word_char_count', $breadcrumb_word_char_count);

        $breadcrumb_word_char_end = wp_kses_post($_POST['breadcrumb_word_char_end']);
        update_option('breadcrumb_word_char_end', $breadcrumb_word_char_end);


        $breadcrumb_margin = sanitize_text_field($_POST['breadcrumb_margin']);
        update_option('breadcrumb_margin', $breadcrumb_margin);

        $breadcrumb_padding = sanitize_text_field($_POST['breadcrumb_padding']);
        update_option('breadcrumb_padding', $breadcrumb_padding);

        $breadcrumb_font_size = sanitize_text_field($_POST['breadcrumb_font_size']);
        update_option('breadcrumb_font_size', $breadcrumb_font_size);

        $breadcrumb_link_color = sanitize_text_field($_POST['breadcrumb_link_color']);
        update_option('breadcrumb_link_color', $breadcrumb_link_color);

        $breadcrumb_separator_color = sanitize_text_field($_POST['breadcrumb_separator_color']);
        update_option('breadcrumb_separator_color', $breadcrumb_separator_color);

        $breadcrumb_bg_color = sanitize_text_field($_POST['breadcrumb_bg_color']);
        update_option('breadcrumb_bg_color', $breadcrumb_bg_color);

        $breadcrumb_themes = sanitize_text_field($_POST['breadcrumb_themes']);
        update_option('breadcrumb_themes', $breadcrumb_themes);

        $breadcrumb_display_home = sanitize_text_field($_POST['breadcrumb_display_home']);
        update_option('breadcrumb_display_home', $breadcrumb_display_home);

        $breadcrumb_home_text = wp_kses_post($_POST['breadcrumb_home_text']);
        update_option('breadcrumb_home_text', $breadcrumb_home_text);

        $breadcrumb_url_hash = sanitize_text_field($_POST['breadcrumb_url_hash']);
        update_option('breadcrumb_url_hash', $breadcrumb_url_hash);

        $breadcrumb_hide_wc_breadcrumb = sanitize_text_field($_POST['breadcrumb_hide_wc_breadcrumb']);
        update_option('breadcrumb_hide_wc_breadcrumb', $breadcrumb_hide_wc_breadcrumb);


        $breadcrumb_custom_css = wp_filter_nohtml_kses($_POST['breadcrumb_custom_css']);
        update_option('breadcrumb_custom_css', $breadcrumb_custom_css);

        $breadcrumb_custom_js = wp_filter_nohtml_kses($_POST['breadcrumb_custom_js']);
        update_option('breadcrumb_custom_js', $breadcrumb_custom_js);
    }
}
