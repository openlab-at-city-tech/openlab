<?php

class M_Marketing extends C_Base_Module
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
            'photocrati-marketing',
            'Marketing',
            'Provides resources for encouraging users to upgrade to NextGen Plus/Pro',
            '3.3.10',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    public static $big_hitters_block_two_cache = [];

    protected static $display_setting_blocks = ['tile', 'mosaic', 'masonry'];

    public static function is_plus_or_pro_enabled()
    {
        return defined('NGG_PRO_PLUGIN_BASENAME') || defined('NGG_PLUS_PLUGIN_BASENAME') || is_multisite();
    }

    /**
     * @return stdClass
     */
    static function get_i18n()
    {
        $i18n = new stdClass;
        $i18n->lite_coupon           = __('NextGEN Basic users get a discount of 30% off regular price', 'nggallery');
        $i18n->bonus                 = __('Bonus', 'nggallery');
        $i18n->feature_not_available = __("We're sorry, but %s is not available in the lite version of NextGEN Gallery. Please upgrade to NextGEN Pro to unlock these awesome features.", 'nggallery');

        return $i18n;
    }

    /**
     * @return string
     */
    static function get_i18n_fragment($msg)
    {
        $params = func_get_args();
        array_shift($params);

        $i18n = self::get_i18n();

        switch($msg) {
            case 'lite_coupon':
                $params = [
                    "<strong>%s</strong> %s",
                    $i18n->bonus,
                    $i18n->lite_coupon
                ];
                break;
            case 'feature_not_available':
                array_unshift($params, $i18n->feature_not_available);
                break;
        }

        return call_user_func_array('sprintf', $params);
    }

    function _register_hooks()
    {
        if (self::is_plus_or_pro_enabled() || !is_admin())
            return;

        add_action('ngg_manage_albums_marketing_block', function() {
            self::enqueue_blocks_style();
            print self::get_big_hitters_block_albums();
        });

        add_action('ngg_manage_galleries_marketing_block', function() {
            self::enqueue_blocks_style();
            print self::get_big_hitters_block_two('managegalleries');
        });

        add_action('ngg_manage_images_marketing_block', function() {
            self::enqueue_blocks_style();
            print self::get_big_hitters_block_two('manageimages');
        });

        add_action('ngg_sort_images_marketing_block', function() {
            self::enqueue_blocks_style();
            print self::get_big_hitters_block_two('sortgallery');
        });

        add_action('ngg_manage_galleries_above_table', function() { 
            $title    = __('Want to sell your images online?', 'nggallery');
            $block    = new C_Marketing_Block_Single_Line($title, 'managegalleries', 'wanttosell');
            print $block->render();
        });

        add_action('admin_init', function() {
            $forms = C_Form_Manager::get_instance();
            foreach (self::$display_setting_blocks as $block) {
                $forms->add_form(NGG_DISPLAY_SETTINGS_SLUG, "photocrati-marketing_display_settings_{$block}");
            }

            $forms->add_form(NGG_OTHER_OPTIONS_SLUG, 'marketing_image_protection');
        });
    }

    function _register_utilities()
    {
    }

    function _register_adapters()
    {
        if (!self::is_plus_or_pro_enabled() && is_admin())
        {
            $registry = $this->get_registry();

            // Add display type upsells in the IGW
            $registry->add_adapter('I_Attach_To_Post_Controller', 'A_Marketing_IGW_Display_Type_Upsells');

            // Add upsell blocks to NGG pages
            $registry->add_adapter('I_MVC_View', 'A_Marketing_Lightbox_Options_MVC', 'lightbox_effects');
            $registry->add_adapter('I_MVC_View', 'A_Marketing_AddGallery_MVC',       'ngg_addgallery');
            $registry->add_adapter('I_Form',     'A_Marketing_Other_Options_Form',   'marketing_image_protection');

            // If we call find_all() before init/admin_init an exception is thrown due to is_user_logged_in() being
            // called too early. Don't remove this action hook.
            add_action('init', function() {
                foreach (C_Display_type_Mapper::get_instance()->find_all() as $display_type) {
                    $registry = $this->get_registry();
                    $registry->add_adapter('I_Form', 'A_Marketing_Display_Type_Settings_Form', $display_type->name);
                }

                wp_register_style(
                    'ngg_marketing_blocks_style',
                    C_Router::get_instance()->get_static_url('photocrati-marketing#blocks.css'),
                    ['wp-block-library'],
                    NGG_SCRIPT_VERSION
                );

                wp_register_script(
                    'jquery-modal',
                    'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js',
                    array('jquery'),
                    '0.9.1'
                );
        
                wp_register_style(
                    'jquery-modal',
                    'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css',
                    [],
                    '0.9.1'
                );
            });

            foreach (self::$display_setting_blocks as $block) {
                $registry->add_adapter(
                    'I_Form',
                    'A_Marketing_Display_Settings_Form',
                    "photocrati-marketing_display_settings_{$block}"
                );
            }
        }
    }

    function initialize()
    {
        
    }

    /**
     * @param string $path
     * @param string $medium
     * @param string $campaign
     * @param string $hash
     * @param string $src
     * @return string
     */
    public static function get_big_hitter_link_url($path, $medium, $campaign, $hash = '', $src = 'ngg')
    {
        if (!empty($hash))
            $hash = '#' . $hash;
        return 'https://www.imagely.com' . $path . '?utm_source=' . $src . '&utm_medium=' . $medium . '&utm_campaign=' . $campaign . $hash;
    }

    /**
     * The same links are used by both of the two blocks
     * @return array
     */
    public static function get_big_hitters_links($medium)
    {
        return [[
            ['title' => __('Ecommerce',                   'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/pro-ecommerce-demo/', $medium, 'ecommerce')],
            ['title' => __('Automated Print Fulfillment', 'nggallery'), 'href' => self::get_big_hitter_link_url('/sell-photos-wordpress/',                       $medium, 'printfulfillment')],
            ['title' => __('Automated Tax Calculation',   'nggallery'), 'href' => self::get_big_hitter_link_url('/sell-photos-wordpress/',                       $medium, 'autotaxcalculations')],
            ['title' => __('Additional Gallery Displays', 'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/nextgen-pro/',        $medium, 'additionalgallerydisplays', 'features')],
            ['title' => __('Additional Album Displays',   'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/nextgen-pro/',        $medium, 'additionalalbumdisplays', 'features')],
        ], [
            ['title' => __('Image Proofing',    'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/pro-proofing-demo/',     $medium, 'proofing')],
            ['title' => __('Image Protection',  'nggallery'), 'href' => self::get_big_hitter_link_url('/docs/turn-image-protection/',                     $medium, 'imageprotection')],
            ['title' => __('Pro Lightbox',      'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/pro-lightbox-demo',      $medium, 'prolightbox')],
            ['title' => __('Digital Downloads', 'nggallery'), 'href' => self::get_big_hitter_link_url('/wordpress-gallery-plugin/digital-download-demo/', $medium, 'digitaldownloads')],
            __('Dedicated customer support and so much more!', 'nggallery')
        ]];
    }

    public static function get_big_hitters_block_base($medium)
    {
        return [
            'title'       => __('Want to make your gallery workflow and presentation even better?', 'nggallery'),
            'description' => __('By upgrading to NextGEN Pro, you can get access to numerous other features, including:', 'nggallery'),
            'links'       => self::get_big_hitters_links($medium),
            'footer'      => __('<strong>Bonus:</strong> NextGEN Gallery users get a discount of 30% off regular price.', 'nggallery'),
            'campaign'    => 'clickheretoupgrade',
            'medium'      => $medium
        ];
    }

    public static function get_big_hitters_block_albums()
    {
        $base = self::get_big_hitters_block_base('managealbums');

        $base['title'] = __('Want to do even more with your albums?', 'nggallery');

        $block = new C_Marketing_Block_Two_Columns(
            $base['title'],
            $base['description'],
            $base['links'],
            $base['footer'],
            'managealbums',
            $base['campaign']
        );

        return $block->render();
    }

    /**
     * @param string $medium
     * @return string
     */
    public static function get_big_hitters_block_two($medium)
    {
        if (!empty(self::$big_hitters_block_two_cache[$medium]))
            return self::$big_hitters_block_two_cache[$medium];

        $base = self::get_big_hitters_block_base($medium);

        $base['title']       = __('Want to do even more with your gallery display?', 'nggallery');
        $base['description'] = [
            __('We know that you will truly love NextGEN Pro. It has 2,600+ five star ratings and is active on over 900,000 websites.', 'nggallery'),
            __('By upgrading to NextGEN Pro, you can get access to numerous other features, including:', 'nggallery')
        ];

        $block = new C_Marketing_Block_Two_Columns(
            $base['title'],
            $base['description'],
            $base['links'],
            $base['footer'],
            $base['medium'],
            $base['campaign']
        );

        self::$big_hitters_block_two_cache[$medium] = $block->render();

        return self::$big_hitters_block_two_cache[$medium];
    }

    public static function enqueue_blocks_style()
    {
        wp_enqueue_style('ngg_marketing_blocks_style');
    }

    /**
     * @return array
     */
    function get_type_list()
    {
        return [
            'A_Marketing_AddGallery_MVC'             => 'adapter.addgallery_mvc.php',
            'A_Marketing_Display_Settings_Form'      => 'adapter.display_settings_form.php',
            'A_Marketing_Display_Type_Settings_Form' => 'adapter.display_type_settings_form.php',
            'A_Marketing_IGW_Display_Type_Upsells'   => 'adapter.igw_display_type_upsells.php',
            'A_Marketing_Lightbox_Options_MVC'       => 'adapter.lightbox_options_mvc.php',
            'A_Marketing_Other_Options_Form'         => 'adapter.other_options_form.php',
            'C_Marketing_Block_Base'                 => 'class.block_base.php',
            'C_Marketing_Block_Card'                 => 'class.block_card.php',
            'C_Marketing_Block_Large'                => 'class.block_large.php',
            'C_Marketing_Block_Popup'                => 'class.block_popup.php',
            'C_Marketing_Block_Single_Line'          => 'class.block_single_line.php',
            'C_Marketing_Block_Two_Columns'          => 'class.block_two_columns.php'
        ];
    }
}

new M_Marketing;
