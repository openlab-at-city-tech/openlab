<?php
/***
{
    Module: photocrati-i18n,
    Depends: {photocrati-fs, photocrati-router}
}
 ***/
class M_I18N extends C_Base_Module
{
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-i18n',
            'Internationalization',
            "Adds I18N resources and methods",
            '3.2.10',
            'https://www.imagely.com/languages/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    function _register_adapters()
    {
        // Provides translating the name & description of images, albums, and galleries
        $this->get_registry()->add_adapter('I_Image_Mapper', 'A_I18N_Image_Translation');
        $this->get_registry()->add_adapter('I_Album_Mapper', 'A_I18N_Album_Translation');
        $this->get_registry()->add_adapter('I_Gallery_Mapper', 'A_I18N_Gallery_Translation');
        $this->get_registry()->add_adapter('I_Displayed_Gallery', 'A_I18N_Displayed_Gallery_Translation');

        // qTranslate requires we disable "Hide Untranslated Content" during routed app requests like
        // photocrati-ajax, when uploading new images, or retrieving dynamically altered (watermarked) images
        $this->get_registry()->add_adapter('I_Routing_App', 'A_I18N_Routing_App');
    }

    function _register_hooks()
    {
        add_action('init', array(&$this, 'register_translation_hooks'), 2);
    }

    function register_translation_hooks()
    {
        $dir = str_replace(
        	wp_normalize_path(WP_PLUGIN_DIR),
        	"",
        	wp_normalize_path(__DIR__ . DIRECTORY_SEPARATOR . 'lang')
        );

        // Load text domain
        load_plugin_textdomain('nggallery', false, $dir);

        // Hooks to register image, gallery, and album name & description with WPML
        add_action('ngg_image_updated', array($this, 'register_image_strings'));
        add_action('ngg_album_updated', array($this, 'register_album_strings'));
        add_action('ngg_created_new_gallery', array($this, 'register_gallery_strings'));

        // do not let WPML translate posts we use as a document store
        add_filter('get_translatable_documents', array($this, 'wpml_translatable_documents'));

        if (class_exists('SitePress'))
        {
            // Copy AttachToPost entries when duplicating posts to another language
            add_action('icl_make_duplicate', array($this, 'wpml_adjust_gallery_id'), 10, 4);
            add_action('save_post', array($this, 'wpml_set_gallery_language_on_save_post'), 101, 1);
        }

        // see function comments
        add_filter('ngg_displayed_gallery_cache_params', array($this, 'set_qtranslate_cache_parameters'));
        add_filter('ngg_displayed_gallery_cache_params', array($this, 'set_wpml_cache_parameters'));
    }

    /**
     * When QTranslate is active we must add its language & url-mode settings as display parameters
     * so as to generate a unique cache for each language.
     *
     * @param array $arr
     * @return array
     */
    function set_qtranslate_cache_parameters($arr)
    {
        if (empty($GLOBALS['q_config']) || !defined('QTRANS_INIT'))
            return $arr;

        global $q_config;
        $arr['qtranslate_language'] = $q_config['language'];
        $arr['qtranslate_url_mode'] = $q_config['url_mode'];

        return $arr;
    }

    /**
     * See notes on set_qtranslate_cache_paramters()
     *
     * @param array $arr
     * @return array
     */
    function set_wpml_cache_parameters($arr)
    {
        if (empty($GLOBALS['sitepress']) || !defined('WPML_ST_VERSION'))
            return $arr;

        global $sitepress;
        $settings = $sitepress->get_settings();
        $arr['wpml_language'] = ICL_LANGUAGE_CODE;
        $arr['wpml_url_mode'] = $settings['language_negotiation_type'];

        return $arr;
    }

    /**
     * Registers gallery strings with WPML
     *
     * @param int|object $gallery_id Gallery object or ID
     */
    function register_gallery_strings($gallery_id)
    {
        if (function_exists('icl_register_string'))
        {
            $gallery = C_Gallery_Mapper::get_instance()->find($gallery_id);
            if ($gallery)
            {
                if (isset($gallery->title) && !empty($gallery->title))
                    icl_register_string('plugin_ngg', 'gallery_' . $gallery->{$gallery->id_field} . '_name', $gallery->title, TRUE);
                if (isset($gallery->galdesc) && !empty($gallery->galdesc))
                    icl_register_string('plugin_ngg', 'gallery_' . $gallery->{$gallery->id_field} . '_description', $gallery->galdesc, TRUE);
            }
        }
    }

    /**
     * Registers image strings with WPML
     *
     * @param object $image
     */
    function register_image_strings($image)
    {
        if (function_exists('icl_register_string'))
        {
            if (isset($image->description) && !empty($image->description))
                icl_register_string('plugin_ngg', 'pic_' . $image->{$image->id_field} . '_description', $image->description, TRUE);
            if (isset($image->alttext) && !empty($image->alttext))
                icl_register_string('plugin_ngg', 'pic_' . $image->{$image->id_field} . '_alttext', $image->alttext, TRUE);
        }
    }

    /**
     * Registers album strings with WPML
     *
     * @param object $album
     */
    function register_album_strings($album)
    {
        if (function_exists('icl_register_string'))
        {
            if (isset($album->name) && !empty($album->name))
                icl_register_string('plugin_ngg', 'album_' . $album->{$album->id_field} . '_name', $album->name, TRUE);
            if (isset($album->albumdesc) && !empty($album->albumdesc))
                icl_register_string('plugin_ngg', 'album_' . $album->{$album->id_field} . '_description', $album->albumdesc, TRUE);
        }
    }

    /**
     * NextGEN stores some data in custom posts that MUST NOT be automatically translated by WPML
     *
     * @param array $icl_post_types
     * @return array $icl_post_types without any NextGEN custom posts
     */
    function wpml_translatable_documents($icl_post_types = array())
    {
        $nextgen_post_types = array(
            'ngg_album',
            'ngg_gallery',
            'ngg_pictures',
            'display_type',
            'gal_display_source',
            'lightbox_library',
            'photocrati-comments'
        );
        foreach ($icl_post_types as $ndx => $post_type) {
            if (in_array($post_type->name, $nextgen_post_types))
                unset($icl_post_types[$ndx]);
        }
        return $icl_post_types;
    }

	function wpml_adjust_gallery_id($master_post_id, $lang, $post_array, $id)
    {
        if (!isset($post_array['post_type']) || $post_array['post_type'] == "displayed_gallery")
            return;

        $re = "|preview/id--(\d+)|mi";
        if (preg_match_all($re, $post_array['post_content'], $gallery_ids))
        {
            foreach($gallery_ids[1] as $index => $gallery_id) {
                $translated_gallery_id = apply_filters('wpml_object_id', (int)$gallery_id, "displayed_gallery", true, $lang);
            }

            $search[$index] = "preview/id--" . $gallery_id;
            $replace[$index] = "preview/id--" . $translated_gallery_id;
            $post_array['post_content'] = str_replace($search, $replace, $post_array['post_content']);

            $to_save = array(
                'ID' => $id,
                'post_content' => $post_array['post_content']
            );

            add_filter('ngg_cleanup_displayed_galleries', '__return_false', 10, 1);
            wp_update_post($to_save);
            add_filter('ngg_cleanup_displayed_galleries', '__return_true', 11, 1);
        }
    }

    function wpml_set_gallery_language_on_save_post($post_id)
    {
        if (wp_is_post_revision($post_id))
            return;

        if (isset($_POST['icl_ajx_action']) && $_POST['icl_ajx_action'] == 'make_duplicates')
            return;

        $post = get_post($post_id);

        if ($post->post_type == 'displayed_gallery')
            return;

        if (preg_match_all("#<img.*http(s)?://(.*)?" . NGG_ATTACH_TO_POST_SLUG . "(=|/)preview(/|&|&amp;)id(=|--)(\\d+).*?>#mi", $post->post_content, $matches, PREG_SET_ORDER))
        {
            $mapper = C_Displayed_Gallery_Mapper::get_instance();
            foreach ($matches as $match) {
                // Find the displayed gallery
                $displayed_gallery_id = $match[6];
                add_filter('wpml_suppress_filters', '__return_true', 10, 1);
                $displayed_gallery = $mapper->find($displayed_gallery_id, TRUE);
                add_filter('wpml_suppress_filters', '__return_false', 11, 1);
                if ($displayed_gallery)
                {
                    $displayed_gallery_type = apply_filters('wpml_element_type', 'displayed_gallery');

                    // set language of this gallery
                    $displayed_gallery_lang = apply_filters('wpml_post_language_details', null, $displayed_gallery->ID);
                    $post_language = apply_filters('wpml_post_language_details', null, $post_id);

                    if (!$displayed_gallery_lang || $displayed_gallery_lang['language_code'] != $post_language['language_code'])
                    {
                        if ($post_language)
                        {
                            $args = array(
                                'element_id' => $displayed_gallery->ID,
                                'element_type' => $displayed_gallery_type,
                                'language_code' => $post_language['language_code']
                            );
                            do_action('wpml_set_element_language_details', $args);
                        }
                    }

                    // duplicate gallery to other languages
                    $is_translated = apply_filters('wpml_element_has_translations', '', $displayed_gallery->ID, $displayed_gallery_type);
                    if (!$is_translated)
                        do_action('wpml_admin_make_post_duplicates', $displayed_gallery->ID);
                }
            }
        }
    }

    static function translate($in, $name = null)
    {
        if (function_exists('langswitch_filter_langs_with_message'))
            $in = langswitch_filter_langs_with_message($in);

        if (function_exists('polyglot_filter'))
            $in = polyglot_filter($in);

        if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage'))
            $in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);

        if (is_string($name)
        &&  !empty($name)
        &&  function_exists('icl_translate')
        &&  apply_filters('wpml_default_language', NULL) != apply_filters('wpml_current_language', NULL))
            $in = icl_translate('plugin_ngg', $name, $in, TRUE);

        $in = apply_filters('localization', $in);

        return $in;
    }

    static function mb_pathinfo($path, $options=null)
    {
        $ret = array(
            'dirname' => '',
            'basename' => '',
            'extension' => '',
            'filename' => ''
        );
        $pathinfo = array();
        if (preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo))
        {
            if (array_key_exists(1, $pathinfo))
                $ret['dirname'] = $pathinfo[1];
            if (array_key_exists(2, $pathinfo))
                $ret['basename'] = $pathinfo[2];
            if (array_key_exists(5, $pathinfo))
                $ret['extension'] = $pathinfo[5];
            if (array_key_exists(3, $pathinfo))
                $ret['filename'] = $pathinfo[3];
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
            default:
                return $ret;
        }
    }

    static function mb_basename($path)
    {
        $separator = " qq ";
        $path = preg_replace("/[^ ]/u", $separator . "\$0" . $separator, $path);
        $base = basename($path);
        return str_replace($separator, "", $base);
    }

    static public function get_kses_allowed_html()
    {
        global $allowedtags;

        $our_keys = array(
            'a'      => array('href'  => array(),
                              'class' => array(),
                              'title' => array()),
            'br'     => array(),
            'em'     => array(),
            'strong' => array(),
            'u'      => array(),
            'p'      => array('class' => array()),
            'div'    => array('class' => array(), 'id' => array()),
            'span'   => array('class' => array(), 'id' => array())
        );

        return array_merge_recursive($allowedtags, $our_keys);
    }

    function get_type_list()
    {
        return array(
            'A_I18N_Displayed_Gallery_Translation' => 'adapter.i18n_displayed_gallery_translation.php',
            'A_I18N_Image_Translation' => 'adapter.i18n_image_translation.php',
            'A_I18N_Album_Translation' => 'adapter.i18n_album_translation.php',
            'A_I18N_Gallery_Translation' => 'adapter.i18n_gallery_translation.php',
            'A_I18N_Routing_App' => 'adapter.i18n_routing_app.php'
        );
    }
}

new M_I18N();
