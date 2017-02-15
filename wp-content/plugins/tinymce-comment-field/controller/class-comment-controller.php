<?php

class TMCECF_CommentController {

    private function __construct() {
        add_filter('comment_text', array(&$this, 'autoembed'), 8);
        add_filter('wp_kses_allowed_html', array(&$this, 'allow_images'), 10, 2);
        add_filter('preprocess_comment', array(&$this, 'resize_images'), 99, 1);
        add_action('comment_post', array(&$this, 'set_moderation'));
        add_filter('comments_template', array(&$this, 'shortcodes_whitelist'), 10);
        add_filter('dynamic_sidebar', array(&$this, 'reset_shortcodes'));
        add_filter('widget_execphp', 'do_shortcode');
        add_filter('comment_text', array(&$this, 'admin_comment_text'));
    }

    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    public function autoembed($text) {
        if (TMCECF_PluginManager::isTitanEnabled() && TMCECF_PluginManager::isTMCECFEnabled()) {
            $titan = TitanFramework::getInstance('tinymce-comment-field');
            $autoembed = $titan->getOption('autoembed');

            if ($autoembed) {
                global $wp_embed;
                return $wp_embed->autoembed($text);
            }
        }

        return $text;
    }

    public function allow_images($tags, $context) {

        if (!TMCECF_PluginManager::isTitanEnabled() || !TMCECF_PluginManager::isTMCECFEnabled()) {
            return $tags;
        }

        if ($context === 'pre_comment_content' && TMCECF_PluginManager::imagesAllowed()) {
            $tags['img'] = array('id' => true, 'src' => true, 'alt' => true, 'height' => true, 'width' => true, 'class' => true);
            $tags['a'] = array('target' => true, 'href' => true, 'class' => true);
        }

        return $tags;
    }

    public function resize_images($commentdata) {

        if (!TMCECF_PluginManager::isTitanEnabled() || !TMCECF_PluginManager::isTMCECFEnabled() || is_admin()) {
            return $commentdata;
        }

        if (TMCECF_PluginManager::imagesAllowed()) {
            $comment_content = $commentdata['comment_content'];
            $commentdata['comment_content'] = $this->resizeImages($comment_content);
            $commentdata['comment_content'] = $this->resizeCaption($commentdata['comment_content']);
        }

        return $commentdata;
    }

    public function set_moderation($comment_id) {

        if (!TMCECF_PluginManager::isTitanEnabled() || !TMCECF_PluginManager::isTMCECFEnabled() || is_admin()) {
            return;
        }

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $moderate = $titan->getOption('moderate_comments_with_images');

        if (!$moderate || current_user_can('unfiltered_html')) {
            return;
        }
        $comment = get_comment($comment_id);
        $comment_content = $comment->comment_content;

        if ($this->hasImageTags($comment_content)) {
            wp_update_comment(array('commment_ID' => $comment_id, 'comment_approved' => 0));
        }
    }

    public function admin_comment_text($comment_text) {

        if (!is_admin()) {
            return $comment_text;
        }

        $this->shortcodesWhitelist();
        $text = do_shortcode($comment_text);
        $this->reset_shortcodes();

        return $text;
    }

    public function shortcodes_whitelist() {

        if (!TMCECF_PluginManager::displayEditor() && !is_admin()):
            return;
        endif;

        $this->shortcodesWhitelist();
    }

    public function reset_shortcodes() {
        if (!TMCECF_PluginManager::displayEditor()):
            return;
        endif;

        $this->resetShortcode();
    }

    private function getImageTags($comment_content) {
        preg_match_all('/<img[^>]+>/i', $comment_content, $result);

        return $result[0];
    }

    private function hasImageTags($comment_content) {
        return count($this->getImageTags($comment_content)) > 0;
    }

    private function resizeImages($comment_content) {

        $image_tags = $this->getImageTags($comment_content);

        if (count($image_tags) === 0) {
            return $comment_content;
        }

        foreach ($image_tags as $img_tag) {

            $original_img_tag = $img_tag;
            $img_tag = stripslashes($img_tag);
            preg_match_all('/(width|height|src|alt)=("[^"]*")/i', $img_tag, $attributes);

            $width = false;
            $height = false;

            $max_width = 200;
            $max_height = 300;
            $src = false;
            $alt = '';
            foreach ($attributes[0] as $attribute_string) {

                $attribute = explode('=', str_replace("\"", '', $attribute_string));

                if ($attribute[0] === 'width') {
                    $width = filter_var($attribute[1], FILTER_SANITIZE_NUMBER_INT);
                }

                if ($attribute[0] === 'height') {
                    $height = filter_var($attribute[1], FILTER_SANITIZE_NUMBER_INT);
                }

                if ($attribute[0] === 'src') {
                    $src = filter_var($attribute[1], FILTER_SANITIZE_STRIPPED);
                }

                if ($attribute[0] === 'alt') {
                    $alt = filter_var($attribute[1], FILTER_SANITIZE_STRIPPED);
                }
            }

            if (empty($width)) {
                $width = $max_width;
            }

            if (empty($height)) {
                $height = $max_height;
            }

            if ($width > $max_width) {
                $ratio = $max_width / $width;
                $height *= $ratio;
                $width *= $ratio;
            }

            if ($height > $max_height) {
                $ratio = $max_height / $height;
                $height *= $ratio;
                $width *= $ratio;
            }

            $height = ceil($height);
            $width = ceil($width);


            $new_image_tag = addslashes("<a class=\"tmcecf-comment-image-href\" href=\"{$src}\" target=\"_blank\"><img class=\"tmcecf-comment-image\" alt=\"{$alt}\" src=\"{$src}\" width=\"{$width}\" height=\"{$height}\" /></a>");

            $comment_content = str_replace($original_img_tag, $new_image_tag, $comment_content);
        }


        return $comment_content;
    }

    private function resizeCaption($comment_content) {
        preg_match_all('/\[caption[^>]+\]/i', $comment_content, $result);

        if (count($result[0]) === 0) {
            return $comment_content;
        }

        foreach ($result[0] as $caption_shortcode) {

            $original_caption_shortcode = $caption_shortcode;
            $caption_shortcode = stripslashes($caption_shortcode);
            preg_match_all('/(width|height|src)=("[^"]*")/i', $caption_shortcode, $attributes);

            $width = false;
            $max_width = 200;
            $original_width = 0;

            foreach ($attributes[0] as $attribute_string) {

                $attribute = explode('=', str_replace("\"", '', $attribute_string));

                if ($attribute[0] === 'width') {
                    $width = (int)$attribute[1];
                    $original_width = $width;
                }
            }

            if (empty($width)) {
                $width = $max_width;
            }

            if ($width > $max_width) {
                $ratio = $max_width / $width;
                $width *= $ratio;
            }

            $new_caption_shortcode = addslashes(str_replace($original_width, $width, $caption_shortcode));
            $comment_content = str_replace($original_caption_shortcode, $new_caption_shortcode, $comment_content);
        }

        return $comment_content;
    }

    private function shortcodesWhitelist() {
        global $shortcode_tags;
        global $shortcode_tags_saved;
        /** @noinspection OnlyWritesOnParameterInspection */
        $shortcode_tags_saved = $shortcode_tags;

        $titan = TitanFramework::getInstance('tinymce-comment-field');
        $allowed_shortcodes = $titan->getOption('allowed-shortcodes');
        $images_allowed = $titan->getOption('allow_images_as_tag');

        if ($images_allowed) {
            $images_shortcodes = array('caption', 'wp_caption');
            $allowed_shortcodes = array_merge($allowed_shortcodes, $images_shortcodes);
        }

        foreach ($shortcode_tags as $shortcode_tag => $value):
            $shortcode_found = false;
            foreach ($allowed_shortcodes as $allowed_shortcode):
                if ($allowed_shortcode === $shortcode_tag):
                    $shortcode_found = true;
                endif;
            endforeach;

            if ($shortcode_found === false):
                remove_shortcode($shortcode_tag);
            endif;
        endforeach;

        add_filter('comment_text', 'do_shortcode');
    }

    private function resetShortcode() {
        global $shortcode_tags;
        global $shortcode_tags_saved;

        if (!empty($shortcode_tags_saved)) {
            /** @noinspection OnlyWritesOnParameterInspection */
            $shortcode_tags = $shortcode_tags_saved;
        }
    }
}