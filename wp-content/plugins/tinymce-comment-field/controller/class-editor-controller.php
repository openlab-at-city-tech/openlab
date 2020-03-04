<?php

class TMCECF_EditorController {

    private function __construct() {
        add_filter('comment_form_field_comment', array(&$this, 'comment_editor'));
        add_filter('teeny_mce_buttons', array(&$this, 'comment_editor_buttons'));
        add_filter('comment_form_defaults', array(&$this, 'comment_editor_form_options'));
        add_action('wp_enqueue_scripts', array(&$this, 'comment_editor_scripts'));
        add_filter('comment_reply_link', array(&$this, 'comment_reply_link_fix'));
        add_action('template_redirect', array(&$this, 'comment_editor_content_css'));
    }

    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function comment_editor($default) {

        if (!TMCECF_PluginManager::displayEditor()):
            return $default;
        endif;

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $text_direction = $titan->getOption('text-direction');

        global $wp_styles;

        $content_css = get_option('tinymce-comment-field_css-url');
        $height = $titan->getOption('height');

        foreach ($wp_styles->registered as $wp_style):
            $style = substr($wp_style->handle, 0, 17);
            if ('tf-google-webfont' === $style):
                $google_web_font_src = 'https:' . $wp_style->src;
                $google_web_font_url_object = parse_url($google_web_font_src);
                $google_web_font_query = array();
                parse_str($google_web_font_url_object['query'], $google_web_font_query);
                $google_web_font_url = '//' . $google_web_font_url_object['host'] . $google_web_font_url_object['path'] . '?' . http_build_query($google_web_font_query);
                $content_css .= ", {$google_web_font_url}";
            endif;
        endforeach;

        ob_start();
        wp_editor('', 'comment', array('textarea_rows' => 15, 'teeny' => true, 'quicktags' => false,
                                       'media_buttons' => false,
                                       'tinymce' => array('height' => $height, 'directionality' => $text_direction,
                                                          'content_css' => $content_css)));
        $comment_editor = ob_get_contents();
        ob_end_clean();
        $comment_editor = str_replace('post_id=0', 'post_id=' . get_the_ID(), $comment_editor);

        return $comment_editor;
    }

    public function comment_editor_buttons($default_buttons) {

        if (is_admin()):
            return $default_buttons;
        endif;

        if (!TMCECF_PluginManager::displayEditor()):
            return $default_buttons;
        endif;

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $buttons = $titan->getOption('buttons');
        
        if (TMCECF_PluginManager::imagesAllowed()) {
            $buttons[] =  'image';
        }

        return $buttons;
    }

    public function comment_editor_form_options($defaults) {

        if (!TMCECF_PluginManager::displayEditor()):
            return $defaults;
        endif;

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $comments_notes_after = $titan->getOption('text-below-commentfield');

        $defaults['comment_notes_after'] = $comments_notes_after;
        return $defaults;
    }

    public function comment_editor_scripts() {

        if (!TMCECF_PluginManager::displayEditor()):
            return;
        endif;

        $current_version = (float)get_option('tinymce-comment-field_version');

        global $wp_version;

        $wp_version_float = (float)$wp_version;

        $script_version = '4.3.1';

        if($wp_version_float >= 4.8 && $wp_version_float <= 5) {
            $script_version = '4.8.0';
        } else {
            $script_version = '5.2.3';
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('tinymce-comment-field', TMCECF_PLUGIN_URL . 'js/tinymce-comment-field.js', 'jquery', $current_version, true);
        wp_enqueue_script('tinymce-comment-field-comment-reply', TMCECF_PLUGIN_URL . 'js/comment-reply-' . $script_version .'.js', array('jquery', 'comment-reply'), $current_version, true);
        wp_enqueue_style('mce-comments-no-status-bar', TMCECF_PLUGIN_URL . 'css/editor-no-statusbar.css');
    }

    public function comment_reply_link_fix($link) {

        if (!TMCECF_PluginManager::displayEditor()):
            return $link;
        endif;

        return str_replace('onclick=', 'data-onclick=', $link);
    }

    public function comment_editor_content_css() {

        $action = filter_input(INPUT_GET, 'mcec_action', FILTER_SANITIZE_STRIPPED);

        if (!empty($action) && $action === 'comment_editor_content_css') {

            if (class_exists('TitanFramework')) {

                $titan = TitanFramework::getInstance('tinymce-comment-field');
                $editor_font = $titan->getOption('editor-font');
                $background_color = $titan->getOption('background-color');
                $custom_css = $titan->getOption('custom-css');

                header('Content-type: text/css')
                ?>
                body {
                <?php
                foreach ($editor_font as $key => $css):
                    echo $key . ' : ' . $css . ';' . chr(13);
                endforeach;
                ?>
                background-color: <?php echo $background_color; ?>;
                }

                <?php echo !empty($custom_css) ? $custom_css : ''; ?>
                <?php
            }
            exit();
        }
    }
}