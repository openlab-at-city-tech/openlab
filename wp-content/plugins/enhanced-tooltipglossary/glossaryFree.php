<?php

class GlossaryTooltipException extends Exception {
    
}

class CMTT_Free {

    protected static $filePath = '';
    protected static $cssPath = '';
    protected static $jsPath = '';
    protected static $messages = '';
    public static $lastQueryDetails = array();
    public static $calledClassName;

    public static function init() {
        self::setupConstants();

        self::includeFiles();

        self::initFiles();

        if (empty(self::$calledClassName)) {
            self::$calledClassName = __CLASS__;
        }

        $file = basename(__FILE__);
        $folder = basename(dirname(__FILE__));
        $hook = "in_plugin_update_message-{$folder}/{$file}";
        add_action($hook, array(self::$calledClassName, 'cmtt_warn_on_upgrade'));

        self::$filePath = plugin_dir_url(__FILE__);
        self::$cssPath = self::$filePath . 'assets/css/';
        self::$jsPath = self::$filePath . 'assets/js/';

        add_action('plugins_loaded', array(self::$calledClassName, 'loadPluginTextDomain'));
        add_action('init', array(self::$calledClassName, 'cmtt_create_post_types'));
        add_action('cmtt_flush_rewrite_rules', array(self::$calledClassName, 'cmtt_create_post_types'));

        add_action('admin_menu', array(self::$calledClassName, 'cmtt_admin_menu'));
        add_action('admin_head', array(self::$calledClassName, 'addRicheditorButtons'));

        add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmtt_glossary_admin_settings_scripts'));
        add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmtt_glossary_admin_edit_scripts'));

        add_action('restrict_manage_posts', array(self::$calledClassName, 'cmtt_restrict_manage_posts'));

        add_action('admin_notices', array(self::$calledClassName, 'cmtt_glossary_admin_notice_wp33'));
        add_action('admin_notices', array(self::$calledClassName, 'cmtt_glossary_admin_notice_mbstring'));
        add_action('admin_notices', array(self::$calledClassName, 'cmtt_glossary_admin_notice_client_pagination'));
        add_action('admin_print_footer_scripts', array(self::$calledClassName, 'cmtt_quicktags'));
        add_action('add_meta_boxes', array(self::$calledClassName, 'cmtt_RegisterBoxes'));

        add_filter('manage_edit-glossary_columns', array(self::$calledClassName, 'cmtt_customColumns'));
        add_filter('manage_glossary_posts_custom_column', array(
            self::$calledClassName,
            'cmtt_customColumnsContent'
                ), 10, 2);
        add_action('quick_edit_custom_box', array(self::$calledClassName, 'cmtt_quickEdit'), 10, 2);
        add_action('save_post', array(self::$calledClassName, 'cmtt_save_postdata'));
        add_action('update_post', array(self::$calledClassName, 'cmtt_save_postdata'));
        /*
         * Invalidate transients on updating/deleting terms
         */
        add_action('save_post', array(self::$calledClassName, 'cmtt_unset_transients'));
        add_action('delete_post', array(self::$calledClassName, 'cmtt_unset_transients'));

        add_filter('cmtt_settings_tooltip_tab_content_after', array(self::$calledClassName, 'cmtt_settings_tooltip_tab_content_after'));

        /*
         * FILTERS
         */

        if (\CM\CMTT_Settings::get('cmtt_glossaryRemoveExcerptParsing', 0) == 1) {
            add_filter('get_the_excerpt', array(self::$calledClassName, 'remove_excerpt_parsing'), 1);
            add_filter('wp_trim_excerpt', array(self::$calledClassName, 'add_parsing_after_excerpt'), 1);
        } else {
            add_filter('get_the_excerpt', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            add_filter('the_excerpt', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        }

        add_filter('get_the_excerpt', array(self::$calledClassName, 'cmtt_disable_parsing'), 1);
        add_filter('wpseo_opengraph_desc', array(self::$calledClassName, 'cmtt_reenable_parsing'), 1);
        /*
         * Make sure parser runs before the post or page content is outputted
         */
        add_filter('the_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        add_filter('the_content', array(self::$calledClassName, 'removeGlossaryExclude'), 25000);
        add_filter('the_content', array('CMTT_Glossary_Index', 'lookForShortcode'), 1);
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 21000);

        /**
         * Avada (fusion) Builder Fix
         */
        add_action('fusion_pause_live_editor_filter', array(self::$calledClassName, 'fusionBuilderFix'));

        /*
         * Highlight terms in the archive description
         */
        if (!empty(\CM\CMTT_Settings::get('cmtt_glossaryHighlightInArchive', 1))) {
            add_filter(
                    'get_the_archive_description', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            add_filter(
                    'category_description', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        }

        /*
         * It's a custom filter which can be applied to create the tooltips
         */
        add_filter('cm_tooltip_parse', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000), 2);
        add_filter('the_title', array(self::$calledClassName, 'cmtt_glossary_addTitlePrefix'), 22000, 2);

        add_filter('cmtt_tooltip_content_add', array(self::$calledClassName, 'addTitleToTooltip'), 10, 2);
        add_filter('cmtt_tooltip_content_add', array(self::$calledClassName, 'addLinkToTooltip'), 10, 2);
        add_filter('cmtt_tooltip_content_add', array(self::$calledClassName, 'addEditlinkToTooltip'), 10, 2);
        add_filter('cmtt_tooltip_content_add', array(
            self::$calledClassName,
            'fixQuotes'
                ), PHP_INT_MAX - 100, 2);
        add_filter('cmtt_tooltip_content_add', array(
            self::$calledClassName,
            'maybeHashContent'
                ), PHP_INT_MAX - 90, 2);

        /*
         * Filter for the BuddyPress record
         */
        add_filter('bp_blogs_record_comment_post_types', array(
            self::$calledClassName,
            'cmtt_bp_record_my_custom_post_type_comments'
        ));

        add_filter('bp_replace_the_content', array(self::$calledClassName, 'cmtt_bp_turn_off_parsing'));

        add_filter('cmtt_is_tooltip_clickable', array(self::$calledClassName, 'isTooltipClickable'));

        /*
         * Tooltip Content ADD
         */
        add_filter('cmtt_tooltip_content_add', array(
            self::$calledClassName,
            'cmtt_glossary_parse_strip_shortcodes'
                ), 4, 2);

        /*
         * "Normal" Tooltip Content
         */
        add_filter('cmtt_term_tooltip_content', array(
            self::$calledClassName,
            'getTheTooltipContentBase'
                ), 10, 2);
        add_filter('cmtt_term_tooltip_content', array(self::$calledClassName, 'addCodeBeforeAfter'), 15, 2);
        add_filter('cmtt_term_tooltip_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse_strip_shortcodes'
                ), 20, 2);
        add_filter('cmtt_term_tooltip_content', array(
            self::$calledClassName,
            'cmtt_glossary_filterTooltipContent'
                ), 30, 2);

        add_filter('cmtt_parse_with_simple_function', array(self::$calledClassName, 'allowSimpleParsing'));

        // acf/load_value - filter for every value load
        add_filter('acf/load_value', array(self::$calledClassName, 'parseACFFields'), 10, 3);
        add_filter('bbp_get_reply_content', array(self::$calledClassName, 'parseBBPressFields'));
//			add_filter( 'bbp_get_breadcrumb', array( self::$calledClassName, 'outputGlossaryExcludeWrap' ) );

        add_filter('cmtt_tooltip_script_args', array(__CLASS__, 'addTooltipScriptArgs'));

        /*
         * Tooltips in Woocommerce short description
         */
        add_filter('woocommerce_short_description', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        add_filter('woocommerce_attribute', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        add_filter('woocommerce_attribute_label', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));

        /*
         * Tooltips in WordPress Text Widget
         */
        if (\CM\CMTT_Settings::get('cmtt_glossaryParseTextWidget', 0) == 1) {
            add_filter('widget_text', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        }

        /*
         * Tooltips in WPBakery
         */
        if (\CM\CMTT_Settings::get('cmtt_glossaryParseWPBakery', 0) == 1) {
            add_filter('vc_shortcode_output', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        }

        /*
         * Tooltips in Goodlayers Page Builder
         */
        add_filter('gdlr_core_the_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));

        /*
         * Tooltips in Ninja tables
         */
        if (\CM\CMTT_Settings::get('cmtt_glossaryParseNinjaTables', 0) == 1) {
            add_filter('ninja_tables_get_public_data', array(
                self::$calledClassName,
                'cmtt_parse_ninja_tables'
                    ), 10, 2);
        }

        /*
         * Tooltips in Oxygen builder
         */
        if (\CM\CMTT_Settings::get('cmtt_glossaryParseOxygenBuilder', 0) == 1) {
            add_action('ct_builder_end', [__CLASS__, 'oxygenParse']);
        }

        add_filter('comments_open', array(self::$calledClassName, 'cmtt_comments_open'), 10, 2);
        add_filter('get_comments_number', array(self::$calledClassName, 'cmtt_comments_number'), 10, 2);

        /*
         * SHORTCODES
         */
        add_shortcode('cm_tooltip_link_to_term', array(self::$calledClassName, 'cmtt_link_to_term'));
        add_shortcode('cm_tooltip_parse', array(self::$calledClassName, 'cm_tooltip_parse'));
        add_shortcode('cmtgend', array(self::$calledClassName, 'cm_tooltip_custom_content'));

        /*
         * Custom tooltip shortcode
         */
        add_shortcode('glossary_tooltip', array(self::$calledClassName, 'cmtt_custom_tooltip_shortcode'));

        add_action('bp_before_create_group', array(self::$calledClassName, 'outputGlossaryExcludeStart'));
        add_action('bp_before_group_admin_content', array(
            self::$calledClassName,
            'outputGlossaryExcludeStart'
                ), 50);
        add_action('bp_attachments_avatar_check_template', array(
            self::$calledClassName,
            'outputGlossaryExcludeStart'
                ), 50);
        add_action('bp_before_profile_avatar_upload_content', array(
            self::$calledClassName,
            'outputGlossaryExcludeStart'
                ), 50);
        add_action('bp_before_profile_edit_cover_image', array(
            self::$calledClassName,
            'outputGlossaryExcludeStart'
                ), 50);

        add_action('bp_after_create_group', array(self::$calledClassName, 'outputGlossaryExcludeEnd'));
        add_action('bp_after_group_admin_content', array(
            self::$calledClassName,
            'outputGlossaryExcludeEnd'
                ), 50);
        add_action('bp_attachments_avatar_main_template', array(
            self::$calledClassName,
            'outputGlossaryExcludeEnd'
                ), 50);
        add_action('bp_after_profile_avatar_upload_content', array(
            self::$calledClassName,
            'outputGlossaryExcludeEnd'
                ), 50);
        add_action('bp_after_profile_edit_cover_image', array(
            self::$calledClassName,
            'outputGlossaryExcludeEnd'
                ), 50);

        add_filter('cmtt_dynamic_css_before', array(__CLASS__, 'addDynamicCSS'));

        add_action('ava_after_main_title', array(__CLASS__, 'enfoldFix'));
        add_filter('cmtt_glossary_parse_post', array(__CLASS__, 'customizerFix'), 10000, 3);

        add_filter('the_content', array(__CLASS__, 'themifyFix'), 21000);

        add_filter('cmtt_tooltip_script_data', array(__CLASS__, 'addTooltipDefinitions'));

        add_filter('cmtt_glossary_content_before', array(__CLASS__, 'formatAdditionalContent'), 1000);
        add_filter('cmtt_glossary_content_after', array(__CLASS__, 'formatAdditionalContent'), 1000);

        add_filter('the_content', array(__CLASS__, 'onBeforeShortcodes'), 9);
        add_filter('cmtt_runParser', array(__CLASS__, 'onBeforeParsing'), PHP_INT_MAX);
        add_filter('cmtt_parsed_content', array(__CLASS__, 'onAfterParsing'));

        add_filter('cmtt_get_all_glossary_items_single', array(__CLASS__, 'maybeUseOtherTitleOnIndex'), 10, 2);
        add_filter('cmtt_glossary_index_listnav_content_inside', array(__CLASS__, 'outputListnav'), 10, 3);
    }

    public static function oxygenParse() {

        // only for frontend
        if (defined("SHOW_CT_BUILDER")) {
            return false;
        }

        global $template_content;

        if ($template_content !== false) {
            $template_content = self::cmtt_glossary_parse($template_content);
        }
    }

    public static function fusionBuilderFix() {
        remove_filter('the_content', array('CMTT_Free', 'cmtt_glossary_addBacklink'), 21000);
        add_filter('fusion_component_fusion_tb_content_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 21000);
    }

    public static function onBeforeShortcodes($content) {
        if (function_exists('wpcodex_hide_email_shortcode')) {
            remove_shortcode('email');
        }

        return $content;
    }

    public static function onBeforeParsing($runParser) {
        if ($runParser) {
            
        } else {
            
        }

        return $runParser;
    }

    public static function onAfterParsing($content) {
        if (function_exists('wpcodex_hide_email_shortcode')) {
            add_shortcode('email', 'wpcodex_hide_email_shortcode');
            $content = do_shortcode($content);
        }

        return $content;
    }

    public static function maybeHashContent($glossaryItemContent, $glossary_item) {
        $hashTooltipContent = \CM\CMTT_Settings::get('cmtt_glossaryTooltipHashContent', '0');
        /*
         * Disabled on AJAX requests
         */
        if (!defined('DOING_AJAX') && $hashTooltipContent) {
            /*
             * Store the TooltipContent in the JS, after decoding the entities, and return the hash
             */
            $glossaryItemContent = self::addTooltipDefinition(html_entity_decode($glossaryItemContent));
        } else {
            $glossaryItemContent = htmlentities($glossaryItemContent); //possible fix for Elementor? (added 4.0.4)
        }

        return $glossaryItemContent;
    }

    public static function addTooltipDefinition($tooltipData = null, $returnAll = false) {
        static $tooltipDefinitons = array();
        if (!empty($tooltipData)) {
            $tooltipDataHash = md5($tooltipData);
            $tooltipDefinitons[$tooltipDataHash] = $tooltipData;

            return $tooltipDataHash;
        } else {
            if ($returnAll) {
                return $tooltipDefinitons;
            } else {
                return '';
            }
        }
    }

    public static function addTooltipDefinitions($tooltipData) {
        $tooltipData['cmtooltip_definitions'] = self::addTooltipDefinition(false, true);

        return $tooltipData;
    }

    public static function themifyFix($content) {
        static $builder_loaded = false;
        $output = '';
        if (class_exists('Themify_Builder_Model') && !$builder_loaded && !Themify_Builder_Model::is_front_builder_activate() && strpos($content, 'themify_builder_row') !== false) { // check if builder has any content
            $builder_loaded = true;
            $output = '';
            $link_tag = "<link id='builder-styles' rel='stylesheet' href='" . themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-style.css') . '?ver=' . THEMIFY_VERSION . "' type='text/css' />";
            $output .= '<script type="text/javascript">
					if( document.getElementById( "builder-styles-css" ) ) document.getElementById( "builder-styles-css" ).insertAdjacentHTML( "beforebegin", "' . $link_tag . '" );
					</script>';
        }
        $content = $content . $output;

        return $content;
    }

    public static function fixQuotes($glossaryItemContent, $glossary_item) {
        $glossaryItemContent = str_replace(['"', '“', '„'], '&quot;', $glossaryItemContent);

        return $glossaryItemContent;
    }

    public static function customizerFix($post, $content, $force) {
        /*
         *  Example customizer URL:
         *  /about/?customize_changeset_uuid=66aadb18-9696-4d4c-b47a-8ade53a4d0cc&customize_theme=mesmerize-pro&customize_messenger_channel=preview-0
         */
        if (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'customize_changeset') !== false) {
            return null;
        }

        return true;
    }

    public static function enfoldFix() {
        global $post;
        if (empty($post)) {
            return;
        }

        $aviaAvailable = function_exists('Avia_Builder');
        $aviaStatus = Avia_Builder()->get_alb_builder_status($post->ID);
        /*
         * Fix for new Enfold, tested with 4.5.6
         */
        if ($aviaAvailable && ( $aviaStatus || 'active' == $aviaStatus )) {
            remove_filter('the_content', array(
                'CMTT_Free',
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            remove_filter('the_content', array('CMTT_Free', 'removeGlossaryExclude'), 25000);
            add_filter('av_complete_content', array(
                'CMTT_Free',
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            add_filter('avf_the_content', array(
                'CMTT_Free',
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            add_action('ava_before_content_templatebuilder_page', array('CMTT_Free', 'enfold441fix_start'));
            add_action('ava_after_content_templatebuilder_page', array('CMTT_Free', 'enfold441fix_end'));
            add_filter('avf_sc_video_output', array('CMTT_Free', 'aviaVideoFix'));
        }
    }

    public static function aviaVideoFix($output) {
        $output = '[glossary_exclude]' . $output . '[/glossary_exclude]';

        return $output;
    }

    public static function enfold441fix_start() {
        /*
         * Start the output buffering
         */
        ob_start();
    }

    public static function enfold441fix_end() {
        /*
         * End the output buffering and pass through our filter
         */
        $content = ob_get_clean();
        echo self::cmtt_glossary_parse($content);
    }

    /**
     * Adds more dynamic styles
     *
     * @param string $dynamicCss
     * @return string
     */
    public static function addDynamicCSS($dynamicCss) {
        $mobileTermLink = \CM\CMTT_Settings::get('cmtt_glossaryMobileSupportLabel', 'Term link: ');
        $fontName = \CM\CMTT_Settings::get('cmtt_tooltipFontStyle', 'default (disables Google Fonts)');
        $fontSize = \CM\CMTT_Settings::get('cmtt_tooltipFontSize', null);
        $titleFontSize = \CM\CMTT_Settings::get('cmtt_tooltipTitleFontSize', null);
        $fontFamily = ( $fontName !== 'default (disables Google Fonts)' ) ? 'font-family: "' . $fontName . '", sans-serif;' : '';
        $glossaryTermTitleColorText = str_replace('#', '', \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_text'));
        $zIndex = \CM\CMTT_Settings::get('cmtt_tooltipZIndex', 1500);
        /* ML */
        $glossaryTermTitleColorBackground = str_replace('#', '', \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_background'));
        $paddingContent = \CM\CMTT_Settings::get('cmtt_tooltipPaddingContent', '0');
        $paddingTitle = \CM\CMTT_Settings::get('cmtt_tooltipPaddingTitle', '0');
        $stemColor = \CM\CMTT_Settings::get('cmtt_tooltipStemColor', '#ffffff');
        $showStem = \CM\CMTT_Settings::get('cmtt_tooltipShowStem', '0');

        ob_start();
        ?>
        #tt {
        <?php echo $fontFamily; ?>
        z-index: <?php echo $zIndex; ?>;
        }

        <?php if (!empty($glossaryTermTitleColorText)) : ?>
            #tt #ttcont div.glossaryItemTitle {
            color: #<?php echo $glossaryTermTitleColorText; ?> !important;
            }
        <?php endif; ?>

        <?php if (!empty($glossaryTermTitleColorBackground)) : ?>
            #tt #ttcont div.glossaryItemTitle {
            background-color: #<?php echo $glossaryTermTitleColorBackground; ?> !important;
            padding: <?php echo $paddingTitle; ?> !important;
            margin: 0px !important;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            }
        <?php endif; ?>

        <?php if (!empty($titleFontSize)) : ?>
            #tt #ttcont div.glossaryItemTitle {
            font-size: <?php echo $titleFontSize; ?>px !important;
            }
        <?php endif; ?>

        #tt #ttcont div.glossaryItemBody {
        padding: <?php echo $paddingContent; ?> !important;
        <?php if (!empty($fontSize)) : ?>
            font-size: <?php echo $fontSize; ?>px !important;
        <?php endif; ?>
        }
        #tt #ttcont .mobile-link{
        <?php if (!empty($fontSize)) : ?>
            font-size: <?php echo $fontSize; ?>px;
        <?php endif; ?>
        }

        .mobile-link a.glossaryLink {
        color: #fff !important;
        }
        .mobile-link:before{content: "<?php echo $mobileTermLink; ?> "}

        <?php if (!empty($showStem)): ?>
            #tt.vertical_top:after {
            border-bottom: 9px solid <?php echo $stemColor; ?> !important;
            }
            #tt.vertical_bottom:after{
            border-top: 9px solid <?php echo $stemColor; ?> !important;
            }
        <?php endif; ?>

        <?php
        $dynamicCss .= ob_get_clean();

        return $dynamicCss;
    }

    /**
     * Check whether the highlight only once should be enabled for the post/page
     *
     * @param type $post
     *
     * @return bool
     * @global type $post
     */
    public static function isHighlightOnlyOnceEnabled($post = null) {
        if (empty($post)) {
            global $post;
        }
        $highlightFirstOccuranceOnly = ( \CM\CMTT_Settings::get('cmtt_glossaryFirstOnly') == 1 );

        if (!empty($post)) {
            /*
             * The post based checkbox can override the general setting, regardless of what it is so:
             * - if the option is enabled globally - it can disable for post
             * - if the option is disabled globally - it can enable for post
             */
            $postHighlightFirstOccurenceOverride = (bool) get_post_meta($post->ID, '_cmtt_highlightFirstOnly', true);
            $highlightFirstOccuranceOnly = $highlightFirstOccuranceOnly == !$postHighlightFirstOccurenceOverride;
        }

        return apply_filters('cmtt_highlight_first_only', $highlightFirstOccuranceOnly, $post);
    }

    /**
     * Function removing the comments functionality from the Term Page pt1.
     */
    public static function cmtt_comments_number($count, $post_id) {
        $removeComments = \CM\CMTT_Settings::get('cmtt_glossaryRemoveCommentsTermPage', 0);
        $_post = get_post($post_id);
        if ($removeComments && !empty($_post) && 'glossary' === $_post->post_type) {
            $count = 0;
        }

        return $count;
    }

    /**
     * Function removing the comments functionality from the Term Page pt2.
     */
    public static function cmtt_comments_open($open, $post_id) {
        if ($open) {
            $removeComments = \CM\CMTT_Settings::get('cmtt_glossaryRemoveCommentsTermPage', 0);
            $_post = get_post($post_id);
            if ('glossary' === $_post->post_type) {
                $open = !$removeComments;
            }
        }

        return $open;
    }

    /**
     * Load plugin's textdomain
     */
    public static function loadPluginTextDomain() {
        load_plugin_textdomain('cm-tooltip-glossary', false, basename(dirname(__FILE__)) . '/languages/');
    }

    /**
     * Add tooltip script args
     *
     * @param array $tooltipArgs
     *
     * @return type
     */
    public static function addTooltipScriptArgs($tooltipArgs) {
        $tooltipArgs['close_button'] = (bool) \CM\CMTT_Settings::get('cmtt_tooltipShowCloseIcon', 1);
        $tooltipArgs['close_button_mobile'] = (bool) \CM\CMTT_Settings::get('cmtt_tooltipShowCloseIconMobile', 1);
        $tooltipArgs['close_symbol'] = \CM\CMTT_Settings::get('cmtt_tooltipCloseSymbol', 'dashicons-no');

        return $tooltipArgs;
    }

    /**
     * Function adds the term highlighting to Advanced Custom Fields
     *
     * @param type $value
     * @param type $post_id
     * @param type $field
     *
     * @return type
     */
    public static function parseACFFields($value, $post_id, $field) {

        if (is_admin()) {
            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        /*
         * Showing the tooltips on page is disabled
         */
        $parsingDisabled = self::is_parsing_disabled($post_id);
        /*
         * Just the ACF parsing is disabled
         */
        $parsingACFDisabled = get_post_meta($post_id, '_cmtt_disable_acf_for_page', true) == 1;

        $disabledACFFields = \CM\CMTT_Settings::get('cmtt_disableACFfields');
        if (!empty($disabledACFFields)) {
            $disabledACFFieldsArr = explode(',', $disabledACFFields);
            if (!empty($disabledACFFieldsArr)) {
                $disabledACFFieldsArr = array_map('trim', $disabledACFFieldsArr);
            }
            $isFieldDisabledName = in_array($field['_name'], $disabledACFFieldsArr);
            $isFieldDisabledKey = in_array($field['key'], $disabledACFFieldsArr);
            $isFieldDisabled = $isFieldDisabledKey || $isFieldDisabledName;
        } else {
            $isFieldDisabled = false;
        }

        $parseACFFields = \CM\CMTT_Settings::get('cmtt_glossaryParseACFFields');

        if ($parseACFFields && !$isFieldDisabled && !$parsingACFDisabled) {

            /*
             * Limit the scope
             * 3.4.0 - added the option to change the fields being parsed
             */
            if (!in_array($field['type'], (array) apply_filters('cmtt_acf_parsed_field_types', \CM\CMTT_Settings::get('cmtt_acf_parsed_field_types', array(
                                        'text',
                                        'wysiwyg'
                    ))))) {
                return $value;
            }

            /*
             * Unwanted in some cases
             */
            if (in_array($field['type'], (array) apply_filters('cmtt_acf_remove_filters_for_type', \CM\CMTT_Settings::get('cmtt_acf_remove_filters_for_type', array('text'))))) {
                remove_filter('acf_the_content', 'wpautop');
            }

            /*
             * Creates problems in some cases
             */
            remove_filter('acf_the_content', 'wptexturize');

            /*
             * Fixes the issues with additional <p> wrappers
             */
            if (!empty($value) && in_array($field['type'], (array) apply_filters('cmtt_acf_wrap_in_span_for_type', \CM\CMTT_Settings::get('cmtt_acf_wrap_in_span_for_type', array('text', 'checkbox'))))) {
                $value = '<span>' . $value . '</span>';
            }
            $value = self::cmtt_glossary_parse(do_shortcode($value), !$parsingDisabled);
        }

        return $value;
    }

    /**
     * Function adds the term highlighting to bbPress fields
     *
     * @param type $value
     * @param type $post_id
     * @param type $field
     *
     * @return type
     */
    public static function parseWPDatatablesFields($value) {
        if (defined('DOING_AJAX') && doing_action('wp_ajax_nopriv_get_wdtable')) {
            add_filter('cmtt_glossary_parse_post', array(__CLASS__, 'parseWPDatatablesFieldsPostOverwrite'), 10, 3);
            $cmWrapItUp = true;
            $result = apply_filters('cm_tooltip_parse', $value, true);
            $cmWrapItUp = false;
            remove_filter('cmtt_glossary_parse_post', array(
                __CLASS__,
                'parseWPDatatablesFieldsPostOverwrite'
                    ), 10, 3);
        } else {
            $result = $value;
        }

        return $result;
    }

    public static function parseWPDatatablesFieldsPostOverwrite($post, $content, $force) {
        global $post;

        if (empty($post)) {
            $args = array(
                'post_type'      => 'page',
                'posts_per_page' => 1,
                'orderby'        => 'rand',
            );
            $rand = new WP_Query($args);
            $posts = $rand->get_posts();
            $post = is_array($posts) ? reset($posts) : null;
        }

        return $post;
    }

    /**
     * Function adds the term highlighting to bbPress fields
     *
     * @param type $value
     * @param type $post_id
     * @param type $field
     *
     * @return type
     */
    public static function parseBBPressFields($value) {
        if (!is_string($value)) {
            return $value;
        }

        $parseBBPressFields = \CM\CMTT_Settings::get('cmtt_glossaryParseBBPressFields');
        if ($parseBBPressFields) {
            $value = apply_filters('cm_tooltip_parse', $value);
        }

        return $value;
    }

    /**
     * Include the files
     */
    public static function includeFiles() {
        do_action('cmtt_include_files_before');

        include_once CMTT_PLUGIN_DIR . "settings/CMTT_Settings.php";
        include_once CMTT_PLUGIN_DIR . "glossaryIndex.php";
        include_once CMTT_PLUGIN_DIR . "amp.php";
        include_once CMTT_PLUGIN_DIR . "functions.php";
        include_once CMTT_PLUGIN_DIR . "package/cminds-free.php";

        do_action('cmtt_include_files_after');
    }

    /**
     * Initialize the files
     */
    public static function initFiles() {
        do_action('cmtt_init_files_before');

        \CM\CMTT_Settings::init();
        CMTT_Glossary_Index::init();
        CMTT_AMP::init();

        do_action('cmtt_init_files_after');
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @return void
     * @since 1.1
     */
    public static function setupConstants() {
        /**
         * Define Plugin Directory
         *
         * @since 1.0
         */
        if (!defined('CMTT_PLUGIN_DIR')) {
            define('CMTT_PLUGIN_DIR', plugin_dir_path(__FILE__));
        }

        /**
         * Define Plugin URL
         *
         * @since 1.0
         */
        if (!defined('CMTT_PLUGIN_URL')) {
            define('CMTT_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        /**
         * Define Plugin Slug name
         *
         * @since 1.0
         */
        if (!defined('CMTT_SLUG_NAME')) {
            define('CMTT_SLUG_NAME', 'cm-tooltip-glossary');
        }

        /**
         * Define Plugin basename
         *
         * @since 1.0
         */
        if (!defined('CMTT_PLUGIN')) {
            define('CMTT_PLUGIN', plugin_basename(__FILE__));
        }

        if (!defined('CMTT_MENU_OPTION')) {
            define('CMTT_MENU_OPTION', 'cmtt_menu_options');
        }

        define('CMTT_ABOUT_OPTION', 'cmtt_about');
        define('CMTT_EXTENSIONS_OPTION', 'cmtt_extensions');
        define('CMTT_SETTINGS_OPTION', 'cmtt_settings');

        do_action('cmtt_setup_constants_after');
    }

    /**
     * Create custom post type
     */
    public static function cmtt_create_post_types() {
        $createGlossaryTermPages = (bool) \CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', true);
        $glossaryPermalink = \CM\CMTT_Settings::get('cmtt_glossaryPermalink', 'glossary');
        $comments = \CM\CMTT_Settings::get('cmtt_glossaryRemoveCommentsTermPage', 1);
        /*
         * Decide whether to add RSS feeds for custom post type or not (for fixing problems with missing links in Google Webdeveloper Tools)
         */
        $addFeeds = \CM\CMTT_Settings::get('cmtt_glossaryAddFeeds', true);

        $singularName = \CM\CMTT_Settings::get('cmtt_glossaryItemSingularName', 'Glossary Item');
        $excludeFromSearch = (bool) \CM\CMTT_Settings::get('cmtt_excludeGlossaryTermPagesFromSearch', '0');
        $name = \CM\CMTT_Settings::get('cmtt_glossaryBreadcrumbs', CMTT_NAME);

        $args = array(
            'label'               => __('Glossary', 'cm-tooltip-glossary'),
            'labels'              => array(
                'add_new_item'  => __('Add New Glossary Item', 'cm-tooltip-glossary'),
                'add_new'       => __('Add Glossary Item', 'cm-tooltip-glossary'),
                'edit_item'     => __('Edit Glossary Item', 'cm-tooltip-glossary'),
                'view_item'     => __('View Glossary Item', 'cm-tooltip-glossary'),
                'singular_name' => __($singularName, 'cm-tooltip-glossary'),
                'name'          => __($name, 'cm-tooltip-glossary'),
                'menu_name'     => __('Glossary', 'cm-tooltip-glossary')
            ),
            'description'         => '',
            'map_meta_cap'        => true,
            'publicly_queryable'  => $createGlossaryTermPages,
            'exclude_from_search' => $excludeFromSearch,
            'public'              => $createGlossaryTermPages,
            'show_ui'             => true,
            'show_in_admin_bar'   => true,
            'show_in_menu'        => CMTT_MENU_OPTION,
            '_builtin'            => false,
            'capability_type'     => 'post',
            'capabilities'        => array(
                'edit_posts'   => 'manage_glossary',
                'create_posts' => 'manage_glossary',
            ),
            'hierarchical'        => false,
            'has_archive'         => false,
            'rewrite'             => array(
                'slug'       => $glossaryPermalink,
                'with_front' => false,
                'feeds'      => true,
                'feed'       => true
            ),
            'query_var'           => true,
            'supports'            => array(
                'title',
                'editor',
                'author',
                'excerpt',
                'revisions',
                'custom-fields',
                'page-attributes',
                'post-thumbnails',
                'thumbnail'
            ),
            'show_in_rest'        => true,
        );

        if (!$comments) {
            $args['supports'][] = 'comments';
        }

        register_post_type('glossary', apply_filters('cmtt_post_type_args', $args));

        if ($addFeeds) {
            global $wp_rewrite;
            $wp_rewrite->extra_permastructs['glossary'] = array();
            $args = (object) $args;

            $post_type = 'glossary';
            $archive_slug = $args->rewrite['slug'];
            if ($args->rewrite['with_front']) {
                $archive_slug = substr($wp_rewrite->front, 1) . $archive_slug;
            } else {
                $archive_slug = $wp_rewrite->root . $archive_slug;
            }
            if ($args->rewrite['feeds'] && $wp_rewrite->feeds) {
                $feeds = '(' . trim(implode('|', $wp_rewrite->feeds)) . ')';
                add_rewrite_rule("{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top');
                add_rewrite_rule("{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top');
            }

            $permastruct_args = $args->rewrite;
            $permastruct_args['feed'] = $permastruct_args['feeds'];
            add_permastruct($post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args);
        } else {
//			add_filter( 'feed_links_show_posts_feed', array( __CLASS__, 'remove_feeds' ), PHP_INT_MAX );
//			add_filter( 'feed_links_show_comments_feed', array( __CLASS__, 'remove_feeds' ), PHP_INT_MAX );
        }
    }

    public static function remove_feeds($feed) {
        global $post;
        if (!empty($post) && in_array($post->post_type, array('glossary'))) {
            return false;
        }

        return $feed;
    }

    public static function cmtt_admin_menu() {
        global $submenu;
        $current_user = wp_get_current_user();

        add_menu_page('Glossary', CMTT_NAME, 'manage_glossary', CMTT_MENU_OPTION, 'edit.php?post_type=glossary', CMTT_PLUGIN_URL . 'assets/css/images/cm-glossary-tooltip-icon.png');

        add_submenu_page(CMTT_MENU_OPTION, 'Add New', 'Add New', 'manage_glossary', 'post-new.php?post_type=glossary');
        do_action('cmtt_add_admin_menu_after_new');

        do_action('cmtt_add_submenu_pages');

        $glossaryItemsPerPage = get_user_meta(get_current_user_id(), 'edit_glossary_per_page', true);
        if ($glossaryItemsPerPage && intval($glossaryItemsPerPage) > 100) {
            update_user_meta(get_current_user_id(), 'edit_glossary_per_page', 100);
        }

        add_filter('views_edit-glossary', array(self::$calledClassName, 'cmtt_filter_admin_nav'), 10, 1);
    }

    public static function displayAdminPage($content) {
        include 'views/backend/admin_template.php';
    }

    public static function cmtt_about() {
        ob_start();
        require 'views/backend/admin_about.php';
        $content = ob_get_clean();
        self::displayAdminPage($content);
    }

    /**
     * Shows extensions page
     */
    public static function cmtt_extensions() {
        ob_start();
        include 'views/backend/admin_extensions.php';
        $content = ob_get_clean();
        self::displayAdminPage($content);
    }

    /**
     * Function enqueues the scripts and styles for the admin Settings view
     * @return type
     * @global type $parent_file
     */
    public static function cmtt_glossary_admin_settings_scripts() {
        global $parent_file, $pagenow;
        if (( CMTT_MENU_OPTION !== $parent_file && 'edit.php?post_type=glossary' !== $parent_file ) || $pagenow === 'edit-tags.php') {
            return;
        }

        wp_enqueue_script('cmtt-select2-js', self::$filePath . 'settings/assets/js/select2.min.js', ['jquery']);
        wp_enqueue_style('cmtt-select2-css', self::$filePath . 'settings/assets/css/select2.min.css');
        wp_enqueue_style('cm-tooltip-admin-css', self::$cssPath . 'tooltip-admin.css');
        wp_enqueue_script('tooltip-admin-js', self::$jsPath . 'cm-tooltip.js', array(
            'jquery',
            'wp-color-picker',
            'inline-edit-post',
            'cmtt-select2-js'
        ));

        $tooltipData['ajaxurl'] = admin_url('admin-ajax.php');
        $tooltipData['nonce'] = wp_create_nonce('cmtt-admin-script-nonce');
        $tooltipData['find_term_nonce'] = wp_create_nonce('cmtt-find-term-nonce');
        $tooltipData['find_woo_product_nonce'] = wp_create_nonce('cmtt-find-woo-product-nonce');
        wp_localize_script('tooltip-admin-js', 'cmtt_data', $tooltipData);
    }

    /**
     * Function outputs the scripts and styles for the edit views
     * @global string $typenow
     */
    public static function cmtt_glossary_admin_edit_scripts() {
        global $typenow;

        $defaultPostTypes = \CM\CMTT_Settings::get('cmtt_allowed_terms_metabox_all_post_types') ? get_post_types() : array(
            'post',
            'page'
        );
        $allowedTermsBoxPostTypes = apply_filters('cmtt_allowed_terms_metabox_posttypes', $defaultPostTypes);

        if (!in_array($typenow, $allowedTermsBoxPostTypes)) {
            return;
        }

        wp_enqueue_script('cmtt-select2-js', self::$filePath . 'settings/assets/js/select2.min.js', ['jquery']);
        wp_enqueue_style('cmtt-select2-css', self::$filePath . 'settings/assets/css/select2.min.css');
        wp_enqueue_style('cm-tooltip-admin-css', self::$cssPath . 'tooltip-admin.css');
        wp_enqueue_script('tooltip-admin-js', self::$jsPath . 'cm-tooltip.js', array(
            'jquery',
            'wp-color-picker',
            'inline-edit-post',
            'cmtt-select2-js'
        ));
    }

    /**
     * Filters admin navigation menus to show horizontal link bar
     *
     * @param type $views
     *
     * @return string
     * @global string $submenu
     * @global type $plugin_page
     */
    public static function cmtt_filter_admin_nav($views) {
        global $submenu, $plugin_page;
        $scheme = is_ssl() ? 'https://' : 'http://';
        $adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
        $currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);
        $submenus = array();
        if (isset($submenu[CMTT_MENU_OPTION])) {
            $thisMenu = $submenu[CMTT_MENU_OPTION];

            $firstMenuItem = $thisMenu[0];
            unset($thisMenu[0]);

            $secondMenuItem = array(
                'Trash',
                'manage_glossary',
                'edit.php?post_status=trash&post_type=glossary',
                'Trash'
            );

            array_unshift($thisMenu, $firstMenuItem, $secondMenuItem);

            foreach ($thisMenu as $item) {
                $slug = $item[2];
                $isCurrent = ( $slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php') );
                $isExternalPage = strpos($item[2], 'http') !== false;
                $isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== false;
                $url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
                $target = $isExternalPage ? '_blank' : '';
                $submenus[$item[0]] = '<a href="' . $url . '" target="' . $target . '" class="' . ( $isCurrent ? 'current' : '' ) . '">' . $item[0] . '</a>';
            }
        }

        return $submenus;
    }

    public static function cmtt_restrict_manage_posts() {
        global $typenow, $wp_query;
        if ($typenow == 'glossary') {
            $status = get_query_var('post_status');
            $options = apply_filters('cmtt_glossary_restrict_manage_posts', array(
                'published' => 'Published',
                'draft'     => 'Draft',
                'trash'     => 'Trash'
            ));

            echo '<select name="post_status">';
            foreach ($options as $key => $label) {
                echo '<option value="' . $key . '" ' . selected($key, $status) . '>' . __($label, 'cm-tooltip-glossary') . '</option>';
            }
            echo '</select>';

            /*
             * create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
             */
            $filters = get_object_taxonomies('glossary');

            foreach ($filters as $tax_slug) {
                // retrieve the taxonomy object
                $tax_obj = get_taxonomy($tax_slug);
                $tax_name = $tax_obj->labels->name;
                // retrieve array of term objects per taxonomy
                $terms = get_terms($tax_slug);

                $currentValue = get_query_var($tax_slug);

                // output html for taxonomy dropdown filter
                echo '<select name="' . $tax_slug . '" id="' . $tax_slug . '" class="postform">';
                echo '<option value="">Show All ' . $tax_name . '</option>';
                foreach ($terms as $term) {
                    echo '<option value="' . $term->slug . '" ' . selected($term->slug, $currentValue) . '>' . $term->name . ' (' . $term->count . ')</option>';
                }
                echo '</select>';
            }
        }
    }

    /**
     * Displays the horizontal navigation bar
     * @global string $submenu
     * @global type $plugin_page
     */
    public static function cmtt_showNav() {
        global $submenu, $plugin_page;
        $submenus = array();
        $scheme = is_ssl() ? 'https://' : 'http://';
        $adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
        $currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);

        if (isset($submenu[CMTT_MENU_OPTION])) {
            $thisMenu = $submenu[CMTT_MENU_OPTION];
            foreach ($thisMenu as $item) {
                $slug = $item[2];
                $isCurrent = ( $slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php') );
                $isExternalPage = strpos($item[2], 'http') !== false;
                $isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== false;
                $url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
                $submenus[] = array(
                    'link'    => $url,
                    'title'   => $item[0],
                    'current' => $isCurrent,
                    'target'  => $isExternalPage ? '_blank' : ''
                );
            }
            require( 'views/backend/admin_nav.php' );
        }
    }

    /**
     * Returns TRUE if the tooltip should be clickable
     */
    public static function isTooltipClickable($isClickable) {
        $isClickableArr['is_clickable'] = (bool) \CM\CMTT_Settings::get('cmtt_tooltipIsClickable', FALSE);
        $isClickableArr['edit_link'] = (bool) \CM\CMTT_Settings::get('cmtt_glossaryAddTermEditlink', FALSE) && current_user_can('manage_glossary');

        $isClickable = in_array(true, $isClickableArr);

        return $isClickable;
    }

    /**
     * Adds a notice about wp version lower than required 3.3
     * @global type $wp_version
     */
    public static function cmtt_glossary_admin_notice_wp33() {
        global $wp_version;

        if (version_compare($wp_version, '3.3', '<')) {
            $message = sprintf(__('%s requires Wordpress version 3.3 or higher to work properly.', 'cm-tooltip-glossary'), CMTT_NAME);
            cminds_show_message($message, true);
        }
    }

    /**
     * Adds a notice about mbstring not being installed
     * @global type $wp_version
     */
    public static function cmtt_glossary_admin_notice_mbstring() {
        $mb_support = function_exists('mb_strtolower');

        if (!$mb_support) {
            $message = sprintf(__('%s since version 2.6.0 requires "mbstring" PHP extension to work! ', 'cm-tooltip-glossary'), CMTT_NAME);
            $message .= '<a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">(' . __('Installation instructions.', 'cm-tooltip-glossary') . '</a>';
            cminds_show_message($message, true);
        }
    }

    /**
     * Adds a notice about too many glossary items for client pagination
     * @global type $wp_version
     */
    public static function cmtt_glossary_admin_notice_client_pagination() {
        $serverSide = \CM\CMTT_Settings::get('cmtt_glossaryServerSidePagination', 0);
        if (!$serverSide) {
            $glossaryItemsCount = wp_count_posts('glossary');
            if ((int) $glossaryItemsCount->publish > 2000) {
                $message = sprintf(__('%s has detected that your glossary has more than 2000 terms and the "Client-side" pagination has been selected.', 'cm-tooltip-glossary'), CMTT_NAME);
                $message .= '<br/>';
                $message .= __('Please switch to the "Server-side" pagination to avoid slowness and problems with the server memory on the Glossary Index Page. (Pro version only)', 'cm-tooltip-glossary');
                cminds_show_message($message, true);
            }
        }
    }

    /**
     * Filters the tooltip content
     *
     * @param type $glossaryItemContent
     * @param type $glossaryItemPermalink
     *
     * @return type
     */
    public static function cmtt_glossary_filterTooltipContent($glossaryItemContent, $glossaryItem) {
        $glossaryItemPermalink = get_permalink($glossaryItem);
        $glossaryItemContent = str_replace('[glossary_exclude]', '', $glossaryItemContent);
        $glossaryItemContent = str_replace('[/glossary_exclude]', '', $glossaryItemContent);

        if (\CM\CMTT_Settings::get('cmtt_glossaryNoFilters') != 1) {

            if (\CM\CMTT_Settings::get('cmtt_glossaryFilterTooltipImg') != 1) {
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<img>');
            }

            if (\CM\CMTT_Settings::get('cmtt_glossaryFilterTooltipA') != 1) {
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<a>');
            }

            if (\CM\CMTT_Settings::get('cmtt_glossaryFilterTooltip') == 1) {
                // remove paragraph, bad chars from tooltip text
                $glossaryItemContent = str_replace(array(chr(10), chr(13)), array(
                    '',
                    ''
                        ), $glossaryItemContent);
                $glossaryItemContent = str_replace(array('</p>', '</ul>', '</li>'), array(
                    '<br/>',
                    '<br/>',
                    '<br/>'
                        ), $glossaryItemContent);
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<li>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<ul>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<p>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h1>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h2>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h3>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h4>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h5>');
                $glossaryItemContent = self::cmtt_strip_only($glossaryItemContent, '<h6>');
                $glossaryItemContent = htmlspecialchars($glossaryItemContent);
                $glossaryItemContent = esc_attr($glossaryItemContent);
                $glossaryItemContent = str_replace("color:#000000", "color:#ffffff", $glossaryItemContent);
                $glossaryItemContent = str_replace('\\[glossary_exclude\\]', '', $glossaryItemContent);
            } else {
                $glossaryItemContent = strtr($glossaryItemContent, array(
                    "\r\n\r\n" => '<br />',
                    "\r\r"     => '<br />',
                    "\n\n"     => '<br />'
                ));
            }
        }

        /*
         * 10.06.2015 added check for (\CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', TRUE)
         */
        if (( \CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', true) && \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip') >= 30 ) && ( strlen($glossaryItemContent) > \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip') )) {
            $glossaryItemContent = cminds_truncate(
                    preg_replace('/<!--(.|\s)*?-->/', '', html_entity_decode($glossaryItemContent)), \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip'), \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltipSymbol', '(...)'), false, true);
        }

        return esc_attr($glossaryItemContent);
    }

    /**
     * Strips just one tag
     *
     * @param type $str
     * @param type $tags
     * @param type $stripContent
     *
     * @return type
     */
    public static function cmtt_strip_only($str, $tags, $stripContent = false) {
        $content = '';
        if (!is_array($tags)) {
            $tags = ( strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags) );
            if (end($tags) == '') {
                array_pop($tags);
            }
        }
        foreach ($tags as $tag) {
            if ($stripContent) {
                $content = '(.+</' . $tag . '[^>]*>|)';
            }
            $str = preg_replace('#</?' . $tag . '[^>]*>' . $content . '#is', '', $str);
        }

        return $str;
    }

    /**
     * Disable the parsing for some reason
     *
     * @param type $smth
     *
     * @return type
     * @global type $wp_query
     */
    public static function remove_excerpt_parsing($smth) {
        remove_filter('the_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));

        return $smth;
    }

    /**
     * Reenable the parsing for some reason
     *
     * @param type $smth
     *
     * @return type
     * @global type $wp_query
     */
    public static function add_parsing_after_excerpt($smth) {
        add_filter('the_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));

        return $smth;
    }

    /**
     * Disable the parsing for some reason
     *
     * @param type $smth
     *
     * @return type
     * @global type $wp_query
     */
    public static function cmtt_disable_parsing($smth) {
        global $wp_query;
        if ($wp_query->is_main_query() && !$wp_query->is_singular) {  // to prevent conflict with Yoast SEO
            remove_filter('the_content', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
            remove_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 21000);
            do_action('cmtt_disable_parsing');
        }

        return $smth;
    }

    /**
     * Reenable the parsing for some reason
     *
     * @param type $smth
     *
     * @return type
     * @global type $wp_query
     */
    public static function cmtt_reenable_parsing($smth) {
        add_filter('the_content', array(
            self::$calledClassName,
            'cmtt_glossary_parse'
                ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 21000);
        do_action('cmtt_reenable_parsing');

        return $smth;
    }

    /**
     * Function strips the shortcodes if the option is set
     *
     * @param type $content
     *
     * @return type
     */
    public static function cmtt_glossary_parse_strip_shortcodes($content, $glossaryItem) {
        if (\CM\CMTT_Settings::get('cmtt_glossaryTooltipStripShortcode') == 1) {
            $content = strip_shortcodes($content);
        } else {
            $content = do_shortcode($content);
        }

        return $content;
    }

    /**
     * Function returns TRUE if the given post should be parsed
     *
     * @param type $post
     * @param type $force
     *
     * @return boolean
     */
    public static function cmtt_isParsingRequired($post, $force = false, $from_cache = false) {
        static $requiredAtLeastOnce = false;
        if ($from_cache) {
            /*
             * Could be used to load JS/CSS in footer only when needed
             */
            return $requiredAtLeastOnce;
        }

        if ($force) {
            return true;
        }

        if (!is_object($post)) {
            return false;
        }

        /*
         *  Skip parsing for excluded pages and posts (except glossary pages?! - Marcin)
         */
        $parsingDisabled = self::is_parsing_disabled($post->ID);
        if ($parsingDisabled) {
            return false;
        }

        $showOnHomepageAuthorpageEtc = (!is_page($post->ID) && !is_single($post->ID) && !is_singular($post->post_type) && \CM\CMTT_Settings::get('cmtt_glossaryOnlySingle') == 0 );
        $onMainQueryOnly = ( \CM\CMTT_Settings::get('cmtt_glossaryOnMainQuery') == 1 ) ? is_main_query() : true;
        $noHomepage = ( \CM\CMTT_Settings::get('cmtt_glossaryOnlySingle') == 1 && is_front_page() );

        $showOnSingleCustom = is_singular($post);

        $condition = ( $showOnHomepageAuthorpageEtc || ( $showOnSingleCustom && !$noHomepage ) );

        $result = $onMainQueryOnly && $condition;
        if ($result) {
            $requiredAtLeastOnce = true;
        }
        $result = apply_filters('cmtt_isParsingRequiredResult', $result, $post, $force, $from_cache);

        return $result;
    }

    public static function prepare_parser_string_arr($glossary_index) {
        /*
         * Initialize $glossarySearchStringArr as empty array
         */
        global $glossaryIndexArr;

        $glossarySearchStringArr = array();
        $glossarySearchStringArrays = array();

        //the tag:[glossary_exclude]+[/glossary_exclude] can be used to mark text will not be taken into account by the glossary
        if ($glossary_index) {

            $caseSensitive = \CM\CMTT_Settings::get('cmtt_glossaryCaseSensitive', 0);

            /*
             * The loops prepares the search query for the replacement
             */
            foreach ($glossary_index as $glossary_item) {
                $dontParseTerm = (bool) CMTT_Free::_get_meta('_cmtt_exclude_parsing', $glossary_item->ID);
                if ($dontParseTerm) {
                    continue;
                }
                $glossary_title = self::normalizeTitle($glossary_item->post_title);
                $addition = '';

                $additionFiltered = apply_filters('cmtt_parse_addition_add', $addition, $glossary_item);
                $additionBeforeTitle = apply_filters('cmtt_parse_addition_before_title', '', $glossary_item);

                $glossaryIndexArrKey = $additionBeforeTitle . $glossary_title . $additionFiltered;

                if (!$caseSensitive) {
                    $glossaryIndexArrKey = mb_strtolower($glossaryIndexArrKey);
                }
                $glossarySearchStringArr[$glossary_item->ID] = $additionBeforeTitle . $glossary_title . $additionFiltered;
                if (!empty($glossary_item->parseSeparately)) {
                    $glossarySearchStringArrays[] = $glossarySearchStringArr;
                    $glossarySearchStringArr = array();
                }

                /*
                 * New in 4.0.9 support for duplicates (especially private duplicates)
                 */
                if (isset($glossaryIndexArr[$glossaryIndexArrKey])) {
                    $current_user_id = get_current_user_id();
                    if (!empty($current_user_id) && !empty($glossary_item->post_author) && $current_user_id == $glossary_item->post_author) {
                $glossaryIndexArr[$glossaryIndexArrKey] = $glossary_item->ID;
            }
                } else {
                    $glossaryIndexArr[$glossaryIndexArrKey] = $glossary_item->ID;
        }
            }
        }


        $glossarySearchStringArrays[] = $glossarySearchStringArr;

        return $glossarySearchStringArrays;
    }

    public static function cmtt_glossary_parse($content, $force = false) {
        global $post, $wp_query, $replacedTerms;

        if (apply_filters('cmtt_glossary_parse_post', $post, $content, $force) === null) {
            return $content;
        }

        $seo = doing_action('wpseo_head');
        /*
         * Added && !$force to solve a conflict with WordPress SEO - ACF Content Analysis
         */
        if ($seo && !$force) {
            return $content;
        }

        static $initializeReplacedTerms = true;

        if (!is_object($post)) {
            $post = $wp_query->post;
            if (empty($post))
                return $content;
        }

        $runParser = apply_filters('cmtt_runParser', self::cmtt_isParsingRequired($post, $force), $post, $content, $force);
        if (!$runParser) {
            return $content;
        }

        /*
         * If there's more than one query and the "Only highlight once"
         */
        if ((( \CM\CMTT_Settings::get('cmtt_glossaryOnMainQuery') != 1 ) && self::isHighlightOnlyOnceEnabled($post))) {
            $initializeReplacedTerms = true;
        }
        /*
         * Run the glossary parser
         */
        $contentHash = md5('cmtt_' . $post->ID . $content);
        $contentHashKey = 'cmtt_content_hash_for_' . $post->ID;

        if (!$force) {
            if (!\CM\CMTT_Settings::get('cmtt_glossaryEnableCaching', false) && \CM\CMTT_Settings::get('cmtt_glossaryClearCaches', false)) {
                wp_cache_delete($contentHash);
                $oldContentHash = (string) get_transient($contentHashKey);
                delete_transient($contentHash);
                if (!empty($oldContentHash)) {
                    delete_transient($oldContentHash);
                }
                delete_transient($contentHashKey);
            }
            $result = get_transient($contentHash);
            if ($result !== false) {
                /*
                 * Need to fake that
                 */
                $replacedTerms = true;

                return $result;
            }
        }

        $args = apply_filters('cmtt_parser_query_args', array(
            'nopaging'    => true,
            'numberposts' => - 1,
        ));
        $glossary_index = CMTT_Free::getGlossaryItemsSorted($args);

        /*
         * New feature introduced in 4.0.4 - it should improve performance for big glossaries and large pages
         * what it does - is filtering the terms which can appear on the page, but without checking context and
         * without doing any replacements
         */
        $enableQuickScan = \CM\CMTT_Settings::get('cmtt_glossaryEnableQuickScan', false);
        if ($enableQuickScan) {
            $glossary_index = self::quickScan($glossary_index, $content);
        }

        $glossarySearchStringArrays = self::prepare_parser_string_arr($glossary_index);

        $highlightTermOwnPage = \CM\CMTT_Settings::get('cmtt_highlightTermOnItsOwnPage', 1);
        $caseSensitive = \CM\CMTT_Settings::get('cmtt_glossaryCaseSensitive', 0);
        /*
         * No replace required if there's no glossary items
         */
        if (!empty($glossarySearchStringArrays) && is_array($glossarySearchStringArrays)) {
            foreach ($glossarySearchStringArrays as $glossarySearchStringArr) {
                $excludeGlossaryStrs = array();
                $excludeGlossaryTagsStrs = array();
                $excludeGlossaryScripts = array();
                /*
                 * Don't highlight glossary term on it's own page
                 */
                if (!$highlightTermOwnPage) {
                    unset($glossarySearchStringArr[$post->ID]);
                }

                $excludeGlossary_regex = '\\['   // Opening bracket
                        . '(\\[?)'   // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
                        . "(glossary_exclude)"   // 2: Shortcode name
                        . '\\b'   // Word boundary
                        . '('  // 3: Unroll the loop: Inside the opening shortcode tag
                        . '[^\\]\\/]*' // Not a closing bracket or forward slash
                        . '(?:'
                        . '\\/(?!\\])'   // A forward slash not followed by a closing bracket
                        . '[^\\]\\/]*'   // Not a closing bracket or forward slash
                        . ')*?'
                        . ')'
                        . '(?:'
                        . '(\\/)'   // 4: Self closing tag ...
                        . '\\]'  // ... and closing bracket
                        . '|'
                        . '\\]'  // Closing bracket
                        . '(?:'
                        . '('   // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                        . '[^\\[]*+' // Not an opening bracket
                        . '(?:'
                        . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                        . '[^\\[]*+'   // Not an opening bracket
                        . ')*+'
                        . ')'
                        . '\\[\\/\\2\\]' // Closing shortcode tag
                        . ')?'
                        . ')'
                        . '(\\]?)';

                $exclude_hyphenated = \CM\CMTT_Settings::get('cmtt_glossaryExcludeHyphenatedWords', 0);
                $exclude_in_double_quotes = \CM\CMTT_Settings::get('cmtt_glossaryInDoubleQuotes', 0);

                if ($exclude_hyphenated || $exclude_in_double_quotes) {
                    $excludeGlossary_regex = '(' . $excludeGlossary_regex . ')';
                }

                if ($exclude_hyphenated) {
                    $excludeGlossary_regex .= '|((?:\w+-)+\w+)';
                }

                if ($exclude_in_double_quotes) {
                    $excludeGlossary_regex .= '|(&#8220;.*?&#8221;)';
                    $excludeGlossary_regex .= '|(&#34;.*?&#34;)';
                    $excludeGlossary_regex .= '|(&“;.*?&”;)';
                    $excludeGlossary_regex .= '|(&quot;.*?&quot;)';
                }

                $excludeGlossary_regex = '/' . $excludeGlossary_regex . '/s';

                /*
                 * Fix for the &amp; character and the AMP term
                 */
                if (CMTT_AMP::is_amp_endpoint()) {
                    $content = str_replace('&#038;', '[glossary_exclude]&#038;[/glossary_exclude]', $content);
                }

                /*
                 * Replace exclude tags and content between them in purpose to save the original text as is
                 * before glossary plug go over the content and add its code
                 * (later will be returned to the marked places in content)
                 */
                preg_match_all($excludeGlossary_regex, $content, $excludeGlossaryStrs, PREG_PATTERN_ORDER);
                $i = 0;

                if (!empty($excludeGlossaryStrs)) {
                    foreach ($excludeGlossaryStrs[0] as $excludeStr) {
                        $content = preg_replace($excludeGlossary_regex, '#' . $i . 'excludeGlossary', $content, 1);
                        $i++;
                    }
                }

                /*
                 * Only for simple parser, doesn't work with DOM
                 */
                $j = 0;
                if (strlen(\CM\CMTT_Settings::get('cmtt_glossaryParseExcludedTags')) > 0 && self::is_DOM_parser_disabled($post)) {
                    $excludedTags = explode(',', \CM\CMTT_Settings::get('cmtt_glossaryParseExcludedTags'));
                    $excludeGlossaryTags_regex = '#(';
                    foreach ($excludedTags as $tag) {
                        $excludeGlossaryTags_regex .= '<' . trim($tag) . '[^>]*>([^>]*)</' . trim($tag) . '>|';
                    }
                    $excludeGlossaryTags_regex = substr($excludeGlossaryTags_regex, 0, strlen($excludeGlossaryTags_regex) - 1);
                    $excludeGlossaryTags_regex .= ')#sU';
                    preg_match_all($excludeGlossaryTags_regex, $content, $excludeGlossaryTagsStrs);
                    if (count($excludeGlossaryTagsStrs) > 0) {
                        foreach ($excludeGlossaryTagsStrs[0] as $excludeStr) {
                            $content = preg_replace($excludeGlossaryTags_regex, '#' . $j . 'excludeTagsGlossary', $content, 1);
                            ++$j;
                        }
                    }
                }

                /*
                 * Since 4.0.5
                 * Always exclude scripts from inline content as DOM parser breaks them often (eg. LearnDash)
                 */
                $excludeScript_regex = '#(<script[^>]*>([^>]*)</script>)#sU';
                preg_match_all($excludeScript_regex, $content, $excludeGlossaryScripts);
                if (!empty($excludeGlossaryScripts)) {
                    $j = 0;
                    foreach ($excludeGlossaryScripts[0] as $excludeStr) {
                        $content = preg_replace($excludeScript_regex, '#' . $j . 'excludeScriptsGlossary', $content, 1);
                        ++$j;
                    }
                }

                $glossaryArrayChunk = apply_filters('cmtt_parse_array_chunk_size', 75);
                $spaceSeparated = \CM\CMTT_Settings::get('cmtt_glossaryOnlySpaceSeparated', 1);

                /*
                 * Initialize the array just once to make the "Highlight only the first occurrence" work regardless of the filter parsing was attached to
                 */
                if ($initializeReplacedTerms) {
                    $replacedTerms = array();
                    $initializeReplacedTerms = false;
                }
                if (count($glossarySearchStringArr) > $glossaryArrayChunk) {
                    $chunkedGlossarySearchStringArr = array_chunk($glossarySearchStringArr, $glossaryArrayChunk, true);
                    $glossarySearchStringArr = null;

                    foreach ($chunkedGlossarySearchStringArr as $glossarySearchStringArrChunk) {
                        $glossarySearchString = '/' . ( ( $spaceSeparated ) ? '(?<=\P{L}|^)(?<!(\p{N}))' : '' ) . '(?!(<|&lt;))(' . (!$caseSensitive ? '(?i)' : '' ) . implode('|', $glossarySearchStringArrChunk) . ')(?!(>|&gt;))' . ( ( $spaceSeparated ) ? '(?=\P{L}|$)(?!(\p{N}))' : '' ) . '/u';
                        $content = self::cmtt_str_replace($content, $glossarySearchString);
                    }
                } else {
                    $glossarySearchString = '/' . ( ( $spaceSeparated ) ? '(?<=\P{L}|^)(?<!(\p{N}))' : '' ) . '(?!(<|&lt;))(' . (!$caseSensitive ? '(?i)' : '' ) . implode('|', $glossarySearchStringArr) . ')(?!(>|&gt;))' . ( ( $spaceSeparated ) ? '(?=\P{L}|$)(?!(\p{N}))' : '' ) . '/u';
                    $content = self::cmtt_str_replace($content, $glossarySearchString);
                }

                if (!empty($excludeGlossaryStrs)) {
                    $i = 0;
                    foreach ($excludeGlossaryStrs[0] as $excludeStr) {
                        $content = str_replace('#' . $i . 'excludeGlossary', $excludeStr, $content);
                        $i++;
                    }
                    //remove all the exclude signs
                    $content = str_replace(array('[glossary_exclude]', '[/glossary_exclude]'), array(
                        '',
                        ''
                            ), $content);
                }

                /*
                 * Only with Simple parser (doesn't work with DOM)
                 */
                if (!empty($excludeGlossaryTagsStrs)) {
                    $j = 0;
                    foreach ($excludeGlossaryTagsStrs[0] as $excludeStr) {
                        $content = str_replace('#' . $j . 'excludeTagsGlossary', $excludeStr, $content);
                        ++$j;
                    }
                }

                /*
                 * Since 4.0.5
                 */
                if (!empty($excludeGlossaryScripts)) {
                    $j = 0;
                    foreach ($excludeGlossaryScripts[0] as $excludeStr) {
                        $content = str_replace('#' . $j++ . 'excludeScriptsGlossary', $excludeStr, $content);
                    }
                }
            }
        }

        $content = apply_filters('cmtt_parsed_content', $content);

        do_action('cmtt_after_parsed_content', $post->ID, $content);

        if (\CM\CMTT_Settings::get('cmtt_glossaryEnableCaching', false)) {
            /*
             * Cache for a month - in case invalidator function doesn't work
             * Save the content hash, so we can invalidate after content change
             */
            set_transient($contentHashKey, $contentHash, 60 * 60 * 24 * 30);
            $result = set_transient($contentHash, $content, 60 * 60 * 24 * 30);
        }

        return $content;
    }

    /**
     * Link some text/phrase to existing tooltip
     * [cm_tooltip_link_to_term term="WordPress"]Sidebars[/cm_tooltip_link_to_term]
     *
     * @param type $atts
     * @param type $content
     *
     * @return type
     * @global type $cmWrapItUp
     */
    public static function cmtt_link_to_term($atts, $content = '') {
        $args = shortcode_atts(
                array(
                    'term' => null
                ), $atts);

        if (empty($args['term'])) {
            return $content;
        } else {
            $term = get_page_by_title($args['term'], OBJECT, 'glossary');

            if (!empty($term)) {
                global $cmtt_temporaryAdditions;

                $cmtt_temporaryAdditions[$term->ID][] = $content;
                add_filter('cmtt_parse_addition_add', array(__CLASS__, 'addTemporaryAdditions'), 10, 2);
                add_filter('cmtt_glossary_index_item_additions', array(__CLASS__, 'addTemporaryToAdditions'), 10, 2);

                add_filter('cmtt_highlight_first_only', array(__CLASS__, 'overrideFirstOnly'), 10);
                $cmWrapItUp = true;
                $result = apply_filters('cm_tooltip_parse', $content, true);
                $cmWrapItUp = false;
                remove_filter('cmtt_highlight_first_only', array(__CLASS__, 'overrideFirstOnly'), 10);
            }
        }

        return $content;
    }

    /**
     * The only reason for this function is to disable the "Highlight Only First Occurrence" for "cm_tooltip_link_to_term" shortcode
     *
     * @param type $highlightFirstOnly
     *
     * @return boolean
     */
    public static function overrideFirstOnly($highlightFirstOnly) {
        return false;
    }

    /**
     * Adds abbreviations to parsing
     *
     * @param type $addition
     * @param type $glossary_item
     *
     * @return type
     */
    public static function addTemporaryAdditions($addition, $glossary_item) {
        global $cmtt_temporaryAdditions;

        $termId = $glossary_item->ID;
        $temporaryAddition = !empty($cmtt_temporaryAdditions[$termId]) ? implode('|', $cmtt_temporaryAdditions[$termId]) : '';

        if (!empty($temporaryAddition)) {
            $addition .= '|' . preg_quote(str_replace('\'', '&#39;', htmlspecialchars(trim($temporaryAddition), ENT_QUOTES, 'UTF-8')), '/');
        }

        return $addition;
    }

    /**
     * Adds temporary terms to parsing
     *
     * @param type $additions
     * @param type $glossary_item
     *
     * @return type
     */
    public static function addTemporaryToAdditions($additions, $glossary_item) {
        global $cmtt_temporaryAdditions;

        $termId = $glossary_item->ID;
        $temporaryAddition = !empty($cmtt_temporaryAdditions[$termId]) ? $cmtt_temporaryAdditions[$termId] : [];

        if (!empty($temporaryAddition)) {
            $additions = array_merge($additions, [preg_quote(str_replace('\'', '&#39;', htmlspecialchars(trim($temporaryAddition), ENT_QUOTES, 'UTF-8')), '/')]);
        }

        return $additions;
    }

    /**
     * [cm_tooltip_parse]content[/cm_tooltip_parse]
     *
     * @param type $atts
     * @param type $content
     *
     * @return type
     */
    public static function cm_tooltip_parse($atts, $content = '') {
        global $cmWrapItUp;
        $atts = $atts;

        $cmWrapItUp = true;
        $result = apply_filters('cm_tooltip_parse', $content, true);
        $cmWrapItUp = false;

        return $result;
    }

    /**
     * Replaces the matches
     *
     * @param type $match
     *
     * @return type
     */
    public static function cmtt_replace_matches($match) {
        $replacementText = '';

        if (!empty($match[0])) {  // Standard tooltips matching
            $replacementText = CMTT_Free::cmtt_prepareReplaceTemplate(htmlspecialchars_decode($match[0], ENT_COMPAT));
        }
        return $replacementText;
    }

    public static function maybeRemoveLinkToGlossaryTerm($post) {
        /*
         *  Checks whether to show links to glossary pages or not
         */
        $linksDisabled = CMTT_Free::_get_meta('_glossary_disable_links_for_page', $post->ID) == 1;

        $mainPageId = CMTT_Glossary_Index::getGlossaryIndexPageId();
        $onGlossaryIndex = !empty($post) && $post->ID == $mainPageId;
        $removeLinksToTermsOnIndex = $onGlossaryIndex && \CM\CMTT_Settings::get('cmtt_glossaryOnlyTitleLinksToTerm', 0);

        /*
         * If TRUE then the links to glossary pages are exchanged with spans
         */
        $removeLinksToTerms = ( \CM\CMTT_Settings::get('cmtt_glossaryTermLink') == 1 || $linksDisabled || !\CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', true) || $removeLinksToTermsOnIndex );

        return $removeLinksToTerms;
    }

    /**
     * Function which prepares the templates for the glossary words found in text
     *
     * @param string $title replacement text
     *
     * @return array|string
     */
    public static function cmtt_prepareReplaceTemplate($title) {
        /*
         * Placeholder for the title
         */
        $titlePlaceholder = '##TITLE_GOES_HERE##';

        /*
         * Array of glossary items, settings
         */
        global $glossaryIndexArr, $templatesArr, $removeLinksToTerms, $replacedTerms, $post;

        /*
         *  Checks whether to show tooltips on this page or not
         */
        $tooltipsDisabled = CMTT_Glossary_Index::disableTooltips(false, $post);

        /*
         * If TRUE then the links to glossary pages are exchanged with spans
         */
        $removeLinksToTerms = CMTT_Free::maybeRemoveLinkToGlossaryTerm($post);

        /*
         * If "Highlight first occurrence only" option is set
         */
        $highlightFirstOccuranceOnly = CMTT_Free::isHighlightOnlyOnceEnabled($post);

        /*
         * Check the case sensitive setting
         */
        $caseSensitive = \CM\CMTT_Settings::get('cmtt_glossaryCaseSensitive', 0);

        /*
         * If it's case insensitive, then the term keys are stored as lowercased
         */
        $glossary_title = preg_quote(htmlspecialchars(trim($title), ENT_QUOTES, 'UTF-8'), '/');

        $normalizedTitle = str_replace(array('&\#039;', '&\#096;', '&\#146;', '’', "'", '&#039;', '&#096;', '&#146;', '&rsquo;'),
                '.',
                $glossary_title
        );
        $titleIndex = (!$caseSensitive ) ? mb_strtolower($normalizedTitle) : $normalizedTitle;

        try {
            do_action('cmtt_replace_template_before_synonyms', $titleIndex, $title);
        } catch (GlossaryTooltipException $ex) {
            /*
             * Trick to stop the execution
             */
            $message = $ex->getMessage();

            return $message;
        }

        /*
         * Upgrade to make it work with synonyms
         */
        $glossary_item_id = null;
        if ($glossaryIndexArr) {
            /*
             * First - look for exact keys
             */
            if (array_key_exists($titleIndex, $glossaryIndexArr)) {
                $glossary_item_id = $glossaryIndexArr[$titleIndex];
            } else {
                /*
                 * If not found - try the synonyms
                 */
                foreach ($glossaryIndexArr as $key => $value) {
                    /*
                     * If we find the term we make sure it's a synonym and not a part of some other term
                     */
                    if (strstr($key, '|') && strstr($key, $titleIndex)) {
                        $synonymsArray = explode('|', $key);
                        if (in_array($titleIndex, $synonymsArray)) {
                            /*
                             * $replace = Glossary Post
                             */
                            $glossary_item_id = $value;
                            break;
                        }
                    }
                }
            }
        }

        if (null === $glossary_item_id) {
            return $title;
        }
        $glossary_item = get_post($glossary_item_id);

        try {
            do_action('cmtt_replace_template_after_synonyms', $glossary_item, $titleIndex, $title);
        } catch (GlossaryTooltipException $ex) {
            /*
             * Trick to stop the execution
             */
            $message = $ex->getMessage();

            return $message;
        }

        /*
         * Error checking
         */
        if (!is_object($glossary_item)) {
            return 'Error! Post not found for word:' . $titleIndex;
        }

        $id = $glossary_item->ID;

        if (!is_array($replacedTerms)) {
            return $title;
        }

        /**
         *  If "Highlight first occurrence only" option is set, we check if the post has already been highlighted
         */
        if (($highlightFirstOccuranceOnly) && is_array($replacedTerms) && !empty($replacedTerms)) {
            foreach ($replacedTerms as $replacedTermTitle => $replacedTerm) {
                /*
                 * If the post has already been highlighted
                 * a) $firstOnlyIncludingVariations = TRUE - regardless of the case,synonym,variant etc
                 * b) $firstOnlyIncludingVariations = FALSE - show each variant once
                 */
                $firstOnlyIncludingVariations = \CM\CMTT_Settings::get('cmtt_firstOnlyIncludingVariations', false);

                if ($firstOnlyIncludingVariations) {
                    if ($replacedTerm['postID'] == $id) {
                        return $title;
                    }
                } else {
                    if ($replacedTermTitle == $title) {
                        return $title;
                    }
                }
            }
        }

        /*
         * Save the post item to the global array so it can be used to generate "Related Terms" list
         */
        $replacedTerms[$title]['post'] = $glossary_item;

        /*
         * Save the post item ID to the global array so it's easy to find out if it has been highlighted in text or not
         */
        $replacedTerms[$title]['postID'] = $id;

        /*
         * Replacement is already cached - use it
         */
        if (!empty($templatesArr[$id])) {

            $replaceEveryNth = \CM\CMTT_Settings::get('cmtt_tooltipReplaceEveryNth', 1);
            if($replaceEveryNth <= 0){ //sanity check
                $replaceEveryNth = 1;
            }
            $shouldReplace = $templatesArr[$id]['count'] && $templatesArr[$id]['count'] % $replaceEveryNth == 0;
            /*
             * Increment the occurrence count
             */
            $templatesArr[$id]['count']++;
            
            if (!$shouldReplace) {
                /*
                 * Don't replace unless it's every n-th
                 */
                return $title;
            } else {
            if (!empty(\CM\CMTT_Settings::get('cmtt_tooltipLinkIconInContent', 0))) {
                $title = apply_filters('cmtt_glossaryItemTitle', $title, $glossary_item, 0);
            }
                $templateReplaced = str_replace($titlePlaceholder, $title, $templatesArr[$id]['template']);

            return $templateReplaced;
        }
        }

        $class = '';

        $titleAttrPrefix = __(\CM\CMTT_Settings::get('cmtt_titleAttributeLabelPrefix', 'Glossary:'), 'cm-tooltip-glossary');
        $titleAttr = ( \CM\CMTT_Settings::get('cmtt_showTitleAttribute') == 1 ) ? ['title' => $titleAttrPrefix . ' ' . esc_attr($glossary_item->post_title)] : [];
        $attributes = array_merge([], $titleAttr);

        /*
         * Conditions to show tooltips
         */
        $excludeTT = CMTT_Free::_get_meta('_cmtt_exclude_tooltip', $glossary_item->ID) || $tooltipsDisabled;
        $disable_by_category = apply_filters('cmtt_term_disable_tooltip_by_category', false, $glossary_item);
        $showTooltipsPrefiltered = $excludeTT != 1 && !$disable_by_category;
        /*
         * This filter can be used to disable tooltips and/or enable replacement eg. footnotes
         */
        $showTooltips = apply_filters('cmtt_show_tooltips', $showTooltipsPrefiltered, $glossary_item, $post);

        if ($showTooltips) {
            $class = 'glossaryLink';
            $tooltipContent = self::getTooltipContent($glossary_item);
            $attributes = array_merge($attributes,
                    [
                        'aria-describedby' => 'tt',
                        'data-cmtooltip'   => $tooltipContent
            ]);
        }

        $tag = 'span';
        if (!$removeLinksToTerms) {
            $tag = 'a';
            $class = 'glossaryLink';
            $permalink = apply_filters('cmtt_term_tooltip_permalink', get_permalink($glossary_item), $glossary_item->ID);
            $attributes = array_merge($attributes, ['href' => $permalink]);
            /*
             * Open in new window
             */
            $newPage = \CM\CMTT_Settings::get('cmtt_glossaryInNewPage') || CMTT_Free::_get_meta('_cmtt_new_page_exception', $post->ID);
            $windowTarget = $newPage ? ['target' => '_blank'] : [];
            $attributes = array_merge($attributes, $windowTarget);
        }

        $additionalClass = apply_filters('cmtt_term_tooltip_additional_class', $class, $glossary_item);

        /*
         * The array of the additional attributes
         */
        $additionalAttributesArr = apply_filters('cmtt_term_tooltip_additional_attibutes', $attributes, $glossary_item);
        $additionalAttributes = '';
        /*
         * Change the additional attributes array to HTML attributes
         */
        if (!empty($additionalAttributesArr)) {
            foreach ($additionalAttributesArr as $key => $value) {
                $additionalAttributes .= sprintf(' %s="%s" ', $key, $value);
            }
        }

        $additionalAttributes .= " data-gt-translate-attributes='[{\"attribute\":\"data-cmtooltip\", \"format\":\"html\"}]'";

        /*
         * Build the span/link with/without tooltips for replacement
         */
        $link_replace = sprintf('<%s class="%s" %s>%s</%s>', $tag, $additionalClass, $additionalAttributes, $titlePlaceholder, $tag);

        /*
         * Save with $titlePlaceholder - for the synonyms
         */
        $templatesArr[$id]['template'] = apply_filters('cmtt_link_replace', $link_replace, $titleAttr, $glossary_item, $additionalClass, $titlePlaceholder, $title);

        if (!empty(\CM\CMTT_Settings::get('cmtt_tooltipLinkIconInContent', 0))) {
            $title = apply_filters('cmtt_glossaryItemTitle', $title, $glossary_item, 0);
        }
        /*
         * Replace it with title to show correctly for the first time
         */
        $link_replace = str_replace($titlePlaceholder, $title, $templatesArr[$id]['template']);
        $templatesArr[$id]['count'] = 1;

        return $link_replace;
    }

    public static function getTooltipContent($glossary_item) {
        global $post;
        /*
         * Change glossary item for tooltip (E-commerce version)
         */
        $glossary_item = apply_filters('cmtt_glossary_item_for_tooltip', $glossary_item, $post, array());

        $tooltipContent = apply_filters('cmtt_term_tooltip_content', '', $glossary_item);
        /*
         * Apply filters for 3rd party widgets additions
         */
        $tooltipContent = apply_filters('cmtt_3rdparty_tooltip_content', $tooltipContent, $glossary_item, false);
        /*
         * Add filter to change the glossary item content on the glossary list
         */
        $tooltipContent = apply_filters('cmtt_tooltip_content_add', $tooltipContent, $glossary_item);

        return $tooltipContent;
    }

    /**
     * Get the base of the Tooltip Content on Glossary Index Page
     *
     * @param type $content
     * @param type $glossary_item
     *
     * @return type
     */
    public static function getTheTooltipContentBase($content, $glossary_item) {
        $content = ( \CM\CMTT_Settings::get('cmtt_glossaryExcerptHover') && $glossary_item->post_excerpt ) ? $glossary_item->post_excerpt : $glossary_item->post_content;
        if (has_shortcode($content, 'cmtgend')) {
            $content = preg_match('/\[cmtgend\](.*?)\[\/cmtgend\]/s', $content, $match);
            $content = $match[1];
        }
        return $content;
    }

    /**
     * Get the base of the Tooltip Content on Glossary Index Page
     *
     * @param type $content
     * @param type $glossary_item
     *
     * @return type
     */
    public static function addCodeBeforeAfter($content, $glossary_item) {
        $before = apply_filters('cmtt_glossary_content_before', \CM\CMTT_Settings::get('cmtt_glossaryTooltipContentBefore', ''));
        $after = apply_filters('cmtt_glossary_content_after', \CM\CMTT_Settings::get('cmtt_glossaryTooltipContentAfter', ''));
        $content = $before . $content . $after;

        return $content;
    }

    /**
     * Function adds the title to the tooltip
     *
     * @param string $where
     *
     * @return string
     * @global type $wpdb
     */
    public static function addTitleToTooltip($glossaryItemContent, $glossary_item) {
        $showTitle = \CM\CMTT_Settings::get('cmtt_glossaryAddTermTitle');

        if ($showTitle == 1) {
            $glossaryItemTitle = '<div class=glossaryItemTitle>' . get_the_title($glossary_item) . '</div>';
            $glossaryItemBody = '<div class=glossaryItemBody>' . $glossaryItemContent . '</div>';
            /*
             * Add the title
             */
            $glossaryItemContent = $glossaryItemTitle . $glossaryItemBody;
        }

        return $glossaryItemContent;
    }

    /**
     * Function adds the editlink
     * @return string
     */
    public static function addEditlinkToTooltip($glossaryItemContent, $glossary_item) {
        $showTitle = \CM\CMTT_Settings::get('cmtt_glossaryAddTermEditlink');

        if ($showTitle == 1 && current_user_can('manage_glossary')) {
            $link = '<a href="' . get_edit_post_link($glossary_item) . '">Edit term</a>';
            $glossaryItemEditlink = '<div class=glossaryItemEditlink>' . $link . '</div>';
            /*
             * Add the editlink
             */
            $glossaryItemContent = $glossaryItemEditlink . $glossaryItemContent;
        }

        return $glossaryItemContent;
    }

    /**
     * Displays the options screen
     */
    public static function outputOptions() {
        $content = \CM\CMTT_Settings::render();
        self::displayAdminPage($content);
    }

    public static function cmtt_quicktags() {
        global $post;
        ?>
        <script type="text/javascript">
            if (typeof QTags !== "undefined") {
                QTags.addButton('cmtt_parse', 'Glossary Parse', '[cm_tooltip_parse]', '[/cm_tooltip_parse]');
                QTags.addButton('cmtt_exclude', 'Glossary Exclude', '[glossary_exclude]', '[/glossary_exclude]');
                QTags.addButton('cmtt_translate', 'Glossary Translate', '[glossary_translate term=""]');
                QTags.addButton('cmtt_dictionary', 'Glossary Dictionary', '[glossary_dictionary term=""]');
                QTags.addButton('cmtt_thesaurus', 'Glossary Thesaurus', '[glossary_thesaurus term=""]');
            }
        </script>
        <?php
    }

    /**
     * Add the prefix before the title on the Glossary Term page
     *
     * @param string $title
     * @param type $id
     *
     * @return string
     * @global type $wp_query
     */
    public static function cmtt_glossary_addTitlePrefix($title = '', $id = null) {
        global $wp_query;

        if ($id) {
            $glossaryItem = get_post($id);
            if ($glossaryItem && 'glossary' == $glossaryItem->post_type && $wp_query->is_single && isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'glossary') {
                $prefix = \CM\CMTT_Settings::get('cmtt_glossaryBeforeTitle');
                if (!empty($prefix)) {
                    $title = '<span class=cmtt-glossary-item-title-prefix>' . __($prefix, 'cm-tooltip-glossary') . '</span>' . $title;
                }
            }
        }

        return $title;
    }

    public static function formatAdditionalContent($content) {
        $contentFormatted = do_shortcode(html_entity_decode($content));

        return $contentFormatted;
    }

    /**
     * Add the backlink on the Glossary Term page
     *
     * @param type $content
     *
     * @return type
     * @global type $wp_query
     */
    public static function cmtt_glossary_addBacklink($content = '') {
        static $doOnce = true;
        global $wp_query;

        if (!$doOnce && \CM\CMTT_Settings::get('cmtt_addBacklinksOnce', 0)) {
            return $content;
        }

        $doOnce = false;

        if (!isset($wp_query->post)) {
            return $content;
        }
        $post = $wp_query->post;
        $id = $post->ID;

        $onMainQueryOnly = ( \CM\CMTT_Settings::get('cmtt_glossaryOnMainQuery') == 1 ) ? is_main_query() : true;
        $is_single = is_single();
        $addBacklink = apply_filters('cmtt_add_backlink', $onMainQueryOnly && $is_single && $post->post_type == 'glossary');

        if ($addBacklink) {
            $mainPageId = CMTT_Glossary_Index::getGlossaryIndexPageId();
            $backlinkHref = apply_filters('cmtt_glossary_backlink_href', get_permalink($mainPageId), $post);
            $backlink = ( \CM\CMTT_Settings::get('cmtt_glossary_addBackLink') == 1 && $mainPageId > 0 ) ? '<a href="' . $backlinkHref . '" class="cmtt-backlink cmtt-backlink-top">' . __(\CM\CMTT_Settings::get('cmtt_glossary_backLinkText'), 'cm-tooltip-glossary') . '</a>' : '';
            $backlinkBottom = ( \CM\CMTT_Settings::get('cmtt_glossary_addBackLinkBottom') == 1 && $mainPageId > 0 ) ? '<a href="' . $backlinkHref . '" class="cmtt-backlink cmtt-backlink-bottom">' . __(\CM\CMTT_Settings::get('cmtt_glossary_backLinkBottomText'), 'cm-tooltip-glossary') . '</a>' : '';

            $referralSnippet = ( \CM\CMTT_Settings::get('cmtt_glossaryReferral') == 1 && \CM\CMTT_Settings::get('cmtt_glossaryAffiliateCode') ) ? self::cmtt_getReferralSnippet() : '';

            $contentBefore = apply_filters('cmtt_glossary_content_before', \CM\CMTT_Settings::get('cmtt_glossaryContentBefore', ''));
            $contentAfter = apply_filters('cmtt_glossary_content_after', \CM\CMTT_Settings::get('cmtt_glossaryContentAfter', ''));
            if (!\CM\CMTT_Settings::get('cmtt_glossarySynonymsBeforeContent', 0)) {
                $contentWithoutBacklink = $contentBefore . $content . $contentAfter;
            } else {
                $contentWithoutBacklink = $contentBefore . $content . $contentAfter;
            }

            $filteredContent = apply_filters('cmtt_add_backlink_content', $contentWithoutBacklink, $post);

            /*
             * If the filteredContent is not empty - we add a second backlink
             */
            if (!empty($filteredContent)) {
                $filteredContent = $filteredContent . $backlinkBottom;
            }

            /*
             * In the end add the backlink at the beginning and the referral snippet at the end
             */
            $contentWithBacklink = $backlink . $filteredContent . $referralSnippet;

            $contentWithBacklink = apply_filters('cmtt_glossary_term_after_content', $contentWithBacklink, $post);

            return $contentWithBacklink;
        }

        return $content;
    }

    /**
     * Outputs the Affiliate Referral Snippet
     * @return string
     */
    public static function cmtt_getReferralSnippet() {
        ob_start();
        ?>
        <span class="glossary_referral_link">
            <a target="_blank"
               href="https://www.cminds.com/store/tooltipglossary/?af=<?php echo \CM\CMTT_Settings::get('cmtt_glossaryAffiliateCode') ?>">
                <img src="https://www.cminds.com/wp-content/uploads/download_tooltip.png" width=122 height=22
                     alt="Download Tooltip Pro" title="Download Tooltip Pro"/>
            </a>
        </span>
        <?php
        $referralSnippet = ob_get_clean();

        return $referralSnippet;
    }

    /**
     * Attaches the hooks adding the custom buttons to TinyMCE and CKeditor
     */
    public static function addRicheditorButtons() {
        /*
         *  check user permissions
         */
        if (!current_user_can('manage_glossary') && !current_user_can('edit_pages')) {
            return;
        }

        // check if WYSIWYG is enabled
        if ('true' == get_user_option('rich_editing') && \CM\CMTT_Settings::get('cmtt_add_richedit_buttons', 1)) {
            add_filter('mce_external_plugins', array(self::$calledClassName, 'cmtt_mcePlugin'));
            add_filter('mce_buttons', array(self::$calledClassName, 'cmtt_mceButtons'));

            add_filter('ckeditor_external_plugins', array(self::$calledClassName, 'cmtt_ckeditorPlugin'));
            add_filter('ckeditor_buttons', array(self::$calledClassName, 'cmtt_ckeditorButtons'));
        }
    }

    public static function cmtt_mcePlugin($plugins) {
        $plugins = (array) $plugins;
        $plugins['cmtt_glossary'] = self::$jsPath . 'editor/glossary-mce.js';

        return $plugins;
    }

    public static function cmtt_mceButtons($buttons) {
        array_push($buttons, '|', 'cmtt_exclude', 'cmtt_parse');

        return $buttons;
    }

    public static function cmtt_ckeditorPlugin($plugins) {
        $plugins = (array) $plugins;
        $plugins['cmtt_glossary'] = self::$jsPath . '/editor/ckeditor/plugin.js';

        return $plugins;
    }

    public static function cmtt_ckeditorButtons($buttons) {
        array_push($buttons, 'cmtt_exclude', 'cmtt_parse');

        return $buttons;
    }

    public static function cmtt_settings_tooltip_tab_content_after($content) {
        ob_start();
        ?>
        <div class="block onlyinpro">
            <h3>Tooltip - Styling</h3>
            <table class="floated-form-table form-table">
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Is clickable?</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
                        <strong>TRUE</strong> - the tooltip should be stationary and clickable<br/>
                        <strong>FALSE</strong> - the tooltip should be floating and unclickable(like in Tooltip Free)<br/>
                    </td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Show "Close" icon</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
                        <strong>TRUE</strong> - the close icon will be displayed<br/>
                        <strong>FALSE</strong> - there won't be the close icon<br/>
                    </td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Show "Close" icon only on mobile devices</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">With this option you can choose:<br/>
                        <strong>TRUE</strong> - the close icon will be displayed only on mobile devices<br/>
                        <strong>FALSE</strong> - the close icon will be displayed on all devices<br/>
                        <strong>Note:</strong> to use this option you need to enable "Show Close icon"
                    </td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Close icon color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set color of tooltip close icon</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Close icon size</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the size of the tooltip close icon</td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row">Tooltip background color</th>
                    <td><input type="text" class="colorpicker" name="cmtt_tooltipBackground" value="<?php echo \CM\CMTT_Settings::get('cmtt_tooltipBackground'); ?>" /></td>
                    <td colspan="2" class="cm_field_help_container">Set color of tooltip background</td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row">Tooltip text color</th>
                    <td><input type="text" class="colorpicker" name="cmtt_tooltipForeground" value="<?php echo \CM\CMTT_Settings::get('cmtt_tooltipForeground'); ?>" /></td>
                    <td colspan="2" class="cm_field_help_container">Set color of tooltip text color</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip title's font size</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set font-size of term title in the tooltip. (Works only if the option "Add term
                        title to the tooltip content?" is set)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip title's text color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set color of term title in the tooltip. (Works only if the option "Add term title to the tooltip content?" is set)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip title's background color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set color of the title's background in the tooltip. (Works only if the option "Add term title to the tooltip content?" is set)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip border</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set border styling (style, width, color)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip rounded corners radius</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set rounded corners radius</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip opacity</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set opacity of tooltip (100=fully opaque, 0=transparent)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip z-index</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set tooltip z-index</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip sizing</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the minimal size of the tooltip in pixels. </td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip positioning</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set distance of tooltip's bottom left corner from cursor pointer</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip font size</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set size of font inside tooltip</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip padding</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set internal padding: top, right, bottom, left</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip shadow</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Select this option if you like to show the shadow for the tooltip</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip shadow color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the color of the shadow of the tooltip</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip display delay</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>

                    <td colspan="2" class="cm_field_help_container">Set the delay before the tooltip appears (seconds)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip hide delay</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the delay before the tooltip fades out (seconds)</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip internal link color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the color of the links inside the tooltip.</td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip edit link color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set the color of the edit links in the tooltip. (Added only when the "Add term editlink to the tooltip content?" is enabled) </td>
                </tr>
                <tr valign="top" class="onlyinpro">
                    <th scope="row">Tooltip mobile link color</th>
                    <td><?php echo \CM\CMTT_Settings::renderOnlyin(); ?></td>
                    <td colspan="2" class="cm_field_help_container">Set color of link to the term page in the tooltip. (Added only when the mobile support is enabled and on mobile device)</td>
                </tr>
            </table>
        </div>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public static function cmtt_warn_on_upgrade() {
        ?>
        <div style="margin-top: 1em"><span style="color: red; font-size: larger">STOP!</span> Do <em>not</em> click
            &quot;update automatically&quot; as you will be <em>downgraded</em> to the free version of Tooltip Glossary.
            Instead, download the Pro update directly from <a
                href="http://www.cminds.com/downloads/cm-enhanced-tooltip-glossary-premium-version/">http://www.cminds.com/downloads/cm-enhanced-tooltip-glossary-premium-version/</a>.
        </div>
        <div style="font-size: smaller">Tooltip Glossary Pro does not use WordPress's standard update mechanism. We
            apologize for the inconvenience!
        </div>
        <?php
    }

    /**
     * Registers the metaboxes
     */
    public static function cmtt_RegisterBoxes() {
        add_meta_box('glossary-exclude-box', 'CM Tooltip - Term Properties', array(
            self::$calledClassName,
            'cmtt_render_my_meta_box'
                ), 'glossary', 'side', 'high');

        $defaultPostTypes = \CM\CMTT_Settings::get('cmtt_disable_metabox_all_post_types') ? get_post_types() : array(
            'glossary',
            'post',
            'page'
        );
        $disableBoxPostTypes = apply_filters('cmtt_disable_metabox_posttypes', $defaultPostTypes);
        if (!empty($disableBoxPostTypes)) {
            foreach ($disableBoxPostTypes as $postType) {
                add_meta_box('glossary-disable-box', 'CM Tooltip - Disables', array(
                    self::$calledClassName,
                    'cmtt_render_disable_for_page'
                        ), $postType, 'side', 'high');
            }
        }

        do_action('cmtt_register_boxes');
    }

    public static function cmtt_render_disable_for_page($post) {
        $dTTpage = get_post_meta($post->ID, '_glossary_disable_tooltip_for_page', true);
        $disableTooltipForPage = (int) $dTTpage;

        $dLpage = get_post_meta($post->ID, '_glossary_disable_links_for_page', true);
        $disableLinkForPage = (int) (!empty($dLpage) && $dLpage == 1 );

        $disableParsingForPage = get_post_meta($post->ID, '_glossary_disable_for_page', true);
        $selected_post_types = \CM\CMTT_Settings::get('cmtt_glossaryOnPosttypes');
        if (!is_array($selected_post_types) || empty($selected_post_types)) {
            $disableParsingGlobal = 'Don\'t search';
        } else {
            $disableParsingGlobal = in_array($post->post_type, $selected_post_types) ? 'Search' : 'Don\'t search';
        }

        $highlightFirst = get_post_meta($post->ID, '_cmtt_highlightFirstOnly', true);
        $highlightFirstForPage = (int) (!empty($highlightFirst) && $highlightFirst == 1 );

        $disableACF = get_post_meta($post->ID, '_cmtt_disable_acf_for_page', true);
        $disableACFforPage = (int) (!empty($disableACF) && $disableACF == 1 );

        $newPageException = get_post_meta($post->ID, '_cmtt_new_page_exception', true);
        $newPageExceptionForPage = (int) (!empty($newPageException) && $newPageException == 1 );

        $DOMParser = get_post_meta($post->ID, '_glossary_disable_dom_parser_global_settings_for_page', true);
        $DOMParserForPage = (int) (!empty($DOMParser) && $DOMParser == 1 );

        $show_tooltips_global = \CM\CMTT_Settings::get('cmtt_glossaryTooltip') ? 'Show' : 'Don\'t show';

        echo '<div class="cmtt_disable_tooltip_for_page_field cmtt-metabox-field">';
        echo '<label for="glossary_disable_tooltip_for_page" class="blocklabel">Tooltips:</label>';
        echo '<select name="glossary_disable_tooltip_for_page" id="glossary_disable_tooltip_for_page">';
        echo '<option value="0" ' . selected($disableTooltipForPage, 0) . '>Use global settings (' . $show_tooltips_global . ')</option>';
        echo '<option value="1" ' . selected($disableTooltipForPage, 1) . '>Don\'t show</option>';
        echo '<option value="2" ' . selected($disableTooltipForPage, 2) . '>Show</option>';
        echo '</select>';
//        echo '<input type="checkbox" name="glossary_disable_tooltip_for_page" id="glossary_disable_tooltip_for_page" value="1" ' . checked(1, $disableTooltipForPage, false) . '>';
//        echo '&nbsp;&nbsp;&nbsp;Don\'t show the Tooltips on this post/page</label>';
        echo '</div>';

        echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
        echo '<label for="glossary_disable_for_page" class="blocklabel">Search for glossary items on this post/page:</label>';
//        echo '<input type="checkbox" name="glossary_disable_for_page" id="glossary_disable_for_page" value="1" ' . checked(1, $disableParsingForPage, false) . '>';
//        echo '&nbsp;&nbsp;&nbsp;Don\'t search for glossary items on this post/page</label>';
        echo '<select name="glossary_disable_for_page" id="glossary_disable_for_page">';
        echo '<option value="0" ' . selected($disableParsingForPage, 0) . '>Use global settings (' . $disableParsingGlobal . ')</option>';
        echo '<option value="1" ' . selected($disableParsingForPage, 1) . '>Don\'t search</option>';
        echo '<option value="2" ' . selected($disableParsingForPage, 2) . '>Search</option>';
        echo '</select>';
        echo '</div>';

        echo '<div class="cmtt_disable_link_for_page_field cmtt-metabox-field">';
        echo '<label for="glossary_disable_link_for_page" class="blocklabel">';
        echo '<input type="checkbox" name="glossary_disable_link_for_page" id="glossary_disable_link_for_page" value="1" ' . checked(1, $disableLinkForPage, false) . '>';
        echo '&nbsp;&nbsp;&nbsp;Don\'t show links to glossary terms on this post/page</label>';
        echo '</div>';

        echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
        echo '<label for="cmtt_highlightFirstOnly" class="blocklabel">';
        echo '<input type="checkbox" name="cmtt_highlightFirstOnly" id="cmtt_highlightFirstOnly" value="1" ' . checked(1, $highlightFirstForPage, false) . '>';
        echo '&nbsp;&nbsp;&nbsp;Overwrite the "Highlight Only First Occurence" setting on this page.</label>';
        echo '</div>';

        echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
        echo '<label for="cmtt_disable_acf_for_page" class="blocklabel">';
        echo '<input type="checkbox" name="cmtt_disable_acf_for_page" id="cmtt_disable_acf_for_page" value="1" ' . checked(1, $disableACFforPage, false) . '>';
        echo '&nbsp;&nbsp;&nbsp;Don\'t search for glossary items in ACF fields on this page.</label>';
        echo '</div>';

        echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
        echo '<label for="cmtt_new_page_exception" class="blocklabel">';
        echo '<input type="checkbox" name="cmtt_new_page_exception" id="cmtt_new_page_exception" value="1" ' . checked(1, $newPageExceptionForPage, false) . '>';
        echo '&nbsp;&nbsp;&nbsp;Overwrite the "Open glossary term page in a new windows/tab?" setting on this page.</label>';
        echo '</div>';

        echo '<div class="cmtt_disable_for_page_field cmtt-metabox-field">';
        echo '<label for="glossary_disable_dom_parser_global_settings_for_page" class="blocklabel">';
        echo '<input type="checkbox" name="glossary_disable_dom_parser_global_settings_for_page" id="glossary_disable_dom_parser_global_settings_for_page" value="1" ' . checked(1, $DOMParserForPage, false) . '>';
        echo '&nbsp;&nbsp;&nbsp;Overwrite the "Don\'t use the DOM parser for the content" setting on this page.</label>';
        echo '</div>';

        do_action('cmtt_add_disables_metabox', $post);
    }

    public static function cmtt_glossary_meta_box_fields() {
        $metaBoxFields = apply_filters('cmtt_add_properties_metabox', array());

        return $metaBoxFields;
    }

    public static function cmtt_render_my_meta_box($post) {
        $result = array();

        foreach (self::cmtt_glossary_meta_box_fields() as $key => $value) {
            $optionContent = '<div><label for="' . $key . '" class="blocklabel">';
            $fieldValue = get_post_meta($post->ID, '_' . $key, true);

            if (is_string($value)) {
                $optionContent .= '<input type="checkbox" name="' . $key . '" id="' . $key . '" value="1" ' . ( (bool) $fieldValue ? ' checked ' : '' ) . '>';
                $optionContent .= '&nbsp;&nbsp;&nbsp;' . $value;
            } elseif (is_array($value)) {
                $label = isset($value['label']) ? $value['label'] : __('No label', 'cm-tooltip-glossary');
                if (isset($value['options'])) {
                    $options = isset($value['options']) ? $value['options'] : array('' => __('-no options-', 'cm-tooltip-glossary'));
                    $optionContent .= '<select name="' . $key . '" id="' . $key . '">';
                    foreach ($options as $optionKey => $optionLabel) {
                        $optionContent .= '<option value="' . $optionKey . '" ' . selected($optionKey, $fieldValue, false) . '>' . $optionLabel . '</option>';
                    }
                    $optionContent .= '</select>';
                } else {
                    $placeholder = isset($value['placeholder']) ? $value['placeholder'] : '';
                    $fieldValue = !empty($fieldValue) ? $fieldValue : $placeholder;
                    $optionContent .= '<input type="text" name="' . $key . '" id="' . $key . '" value=" ' . $fieldValue . '" placeholder="' . $placeholder . '">';
                }
                $optionContent .= '&nbsp;&nbsp;&nbsp;' . $label;
            }
            $optionContent .= '</label></div>';
            $result[] = $optionContent;
        }

        $result = apply_filters('cmtt_edit_properties_metabox_array', $result, $post);

        if (empty($result)) {
            $result[] = 'Only in Pro+/Ecommerce';
        }

        echo implode('', $result);
    }

    public static function cmtt_customColumns($columns) {
        $new_columns = array();

        foreach ($columns as $key => $val) {
            if ($key == 'title' && \CM\CMTT_Settings::get('cmtt_show_desc_inlist_table', 0)) {
                $new_columns['title'] = $val;
                $new_columns['cmtt_desc'] = 'Description';
            } else {
                $new_columns[$key] = $val;
            }
        }
        $new_columns['cmtt_meta'] = 'Meta values';

        return $new_columns;
    }

    public static function cmtt_customColumnsContent($column_name, $post_id) {
        $html = '';
        //output post featured checkbox
        if ('cmtt_meta' === $column_name) {
            ob_start();
            do_action('cmtt_meta_column_content', $post_id);
            $html = ob_get_clean();
        } elseif ('cmtt_desc' == $column_name) {
            $glossary_item = get_post($post_id);
//            if (\CM\CMTT_Settings::get('cmtt_glossaryShowExcerpt', 0)) {
//                $glossaryItemDesc = $glossary_item->post_excerpt;
//            } else {
            $glossaryItemDesc = $glossary_item->post_content;
//            }
            $glossaryItemDesc = do_shortcode($glossaryItemDesc);
            $html = cminds_truncate(preg_replace(
                            '/<!--(.|\s)*?-->/',
                            '',
                            html_entity_decode($glossaryItemDesc)),
                    \CM\CMTT_Settings::get('cmtt_show_desc_inlist_table', 0),
                    \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltipSymbol', '(...)'), false, true);
        }
        echo $html;
    }

    public static function cmtt_quickEdit($column_name, $post_type) {
        $html = '';

        //output post featured checkbox
        if ('cmtt_meta' === $column_name) {
            wp_nonce_field('post_metadata', 'post_metadata_field');

            ob_start();
            ?>
            <fieldset class="inline-edit-col-left clear">
                <div class="inline-edit-group wp-clearfix">
                    <?php do_action('cmtt_quick_edit_content'); ?>
                </div>
            </fieldset>
            <?php
            $html = ob_get_clean();
        }

        echo $html;
    }

    public static function cmtt_unset_transients($post_id) {
        $postType = get_post_type($post_id);
        if ('glossary' != $postType) {
            return;
        }

        /*
         * Invalidate transients
         */
        $contentHashKey = 'cmtt_content_hash_for_' . $post_id;
        $contentHash = get_transient($contentHashKey);
        delete_transient($contentHashKey);
        if (!empty($contentHash)) {
            delete_transient($contentHash);
        }

        $allArgsKey = 'cmtt_all_args_keys';
        $allArgsKeys = get_transient($allArgsKey);
        if (is_array($allArgsKeys)) {
            foreach ($allArgsKeys as $argsKey) {
                if (is_string($argsKey)) {
                    delete_transient($argsKey);
                }
            }
            if (!empty($allArgsKey)) {
                delete_transient($allArgsKeys);
            }
        }
    }

    public static function cmtt_save_postdata($post_id) {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $postType = isset($post['post_type']) ? $post['post_type'] : '';

        do_action('cmtt_on_glossary_item_save_before', $post_id, $post);

        $disableBoxPostTypes = apply_filters('cmtt_disable_metabox_posttypes', array('glossary', 'post', 'page'));
        if (in_array($postType, $disableBoxPostTypes)) {
            /*
             * Disables the parsing of the given page
             */
            $disableParsingForPage = 0; // 0 - use global settings; 1 - disable; 2 - enable (since 3.9.8)
            if (isset($post["glossary_disable_for_page"])) {
                $disableParsingForPage = $post["glossary_disable_for_page"];
            }
            update_post_meta($post_id, '_glossary_disable_for_page', $disableParsingForPage);

            /*
             * Disables the showing of tooltip on given page
             */
            $disableTooltipForPage = 0;
            if (isset($post["glossary_disable_tooltip_for_page"])) {
                $disableTooltipForPage = $post["glossary_disable_tooltip_for_page"];
            }
            update_post_meta($post_id, '_glossary_disable_tooltip_for_page', $disableTooltipForPage);

            /*
             * Disables the showing of links to tooltip pages on given page
             */
            $disableLinksForPage = 0;
            if (isset($post["glossary_disable_link_for_page"]) && $post["glossary_disable_link_for_page"] == 1) {
                $disableLinksForPage = 1;
            }
            update_post_meta($post_id, '_glossary_disable_links_for_page', $disableLinksForPage);

            /*
             * Overwrite the hightlight first only setting for page
             */
            $highlightFirstOnly = 0;
            if (isset($post["cmtt_highlightFirstOnly"]) && $post["cmtt_highlightFirstOnly"] == 1) {
                $highlightFirstOnly = 1;
            }
            update_post_meta($post_id, '_cmtt_highlightFirstOnly', $highlightFirstOnly);

            /*
             * Overwrite the DOM Parser global Settings
             */
            $DOMParserForPage = 0;
            if (isset($post["glossary_disable_dom_parser_global_settings_for_page"]) && $post["glossary_disable_dom_parser_global_settings_for_page"] == 1) {
                $DOMParserForPage = 1;
            }
            update_post_meta($post_id, '_glossary_disable_dom_parser_global_settings_for_page', $DOMParserForPage);

            /*
             * Overwrite the disable acf
             */
            $disableACFForPage = 0;
            if (isset($post["cmtt_disable_acf_for_page"]) && $post["cmtt_disable_acf_for_page"] == 1) {
                $disableACFForPage = 1;
            }
            update_post_meta($post_id, '_cmtt_disable_acf_for_page', $disableACFForPage);

            /*
             * Overwrite the disable acf
             */
            $newPageException = 0;
            if (isset($post["cmtt_new_page_exception"]) && $post["cmtt_new_page_exception"] == 1) {
                $newPageException = 1;
            }
            update_post_meta($post_id, '_cmtt_new_page_exception', $newPageException);
        }

        if ('glossary' != $postType) {
            return;
        }

        do_action('cmtt_on_glossary_item_save', $post_id, $post);

        /*
         * Part for "glossary" items only starts here
         */
        foreach (array_keys(self::cmtt_glossary_meta_box_fields()) as $value) {
            $exclude_value = ( isset($post[$value]) ) ? $post[$value] : 0;
            update_post_meta($post_id, '_' . $value, $exclude_value);
        }
    }

    /**
     * Allows to choose which parser should be used
     *
     * @param string $html
     * @param string $glossarySearchString
     *
     * @return string
     */
    public static function cmtt_str_replace($html, $glossarySearchString) {
        global $post;
        $filter = current_filter();
        $parseWithSimpleFunctionFilters = apply_filters('cmtt_parse_with_simple_function', array());

        $runThroughSimpleFunction = in_array($filter, $parseWithSimpleFunctionFilters);
        $runThroughSimpleFunctionAll = \CM\CMTT_Settings::get('cmtt_disableDOMParser', false);

        $disable_DOM_parser_for_page = get_post_meta($post->ID, '_glossary_disable_dom_parser_global_settings_for_page', TRUE);

        if (1 == $disable_DOM_parser_for_page) {
            $runThroughSimpleFunctionAll = !$runThroughSimpleFunctionAll;
        }

        if ($runThroughSimpleFunction || $runThroughSimpleFunctionAll) {
            return self::cmtt_simple_str_replace($html, $glossarySearchString);
        } else {
            return self::cmtt_dom_str_replace($html, $glossarySearchString);
        }
    }

    /**
     * Setups the filters which should use the simple parsing instead of DOM parser
     *
     * @param array $simpleParsingList
     *
     * @return array
     */
    public static function allowSimpleParsing($simpleParsingList) {
        if (\CM\CMTT_Settings::get('cmtt_disableDOMParserForACF', false)) {
            $simpleParsingList[] = 'acf/load_value';
        }

        return $simpleParsingList;
    }

    public static function outputGlossaryExcludeWrap($output = '') {
        return '[glossary_exclude]' . $output . '[/glossary_exclude]';
    }

    public static function outputGlossaryExcludeStart() {
        echo '[glossary_exclude]';
    }

    public static function outputGlossaryExcludeEnd() {
        echo '[/glossary_exclude]';
    }

    public static function removeGlossaryExclude($content) {
        $content = str_replace(array('[glossary_exclude]', '[/glossary_exclude]'), array('', ''), $content);

        return $content;
    }

    /**
     * New function to search the terms in the content
     *
     * @param string $html
     * @param string $glossarySearchString
     *
     * @return string
     * @since 2.3.1
     */
    public static function cmtt_dom_str_replace($html, $glossarySearchString) {
        global $cmWrapItUp;

        if (!empty($html) && is_string($html)) {

            $html = apply_filters('cmtt_dom_str_replace_before', $html);

            if ($cmWrapItUp) {
                $html = '<span>' . $html . '</span>';
            }
            $dom = new DOMDocument();
            /*
             * loadXml needs properly formatted documents, so it's better to use loadHtml, but it needs a hack to properly handle UTF-8 encoding
             */
            libxml_use_internal_errors(true);
            if (!$dom->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"))) {
                libxml_clear_errors();
            }
            $xpath = new DOMXPath($dom);

            /*
             * Base query NEVER parse in scripts
             */
            $query = '//text()[not(ancestor::script)][not(ancestor::style)]';

            $excludedTags = \CM\CMTT_Settings::get('cmtt_glossaryProtectedTags', array('all_h', 'h1', 'a', 'other'));
            if ($excludedTags == 1)
                $excludedTags = array('all_h', 'h1', 'a', 'other');

            if (is_array($excludedTags)) {
                foreach ($excludedTags as $tag) {
                    switch ($tag) {
                        case 'all_h':
                            $query .= '[not(ancestor::h1)][not(ancestor::h2)][not(ancestor::h3)][not(ancestor::h4)][not(ancestor::h5)][not(ancestor::h6)]';
                            break;

                        case 'h1':
                            $query .= '[not(ancestor::h1)]';
                            break;

                        case 'a':
                            $query .= '[not(ancestor::a)]';
                            break;

                        case 'other':
                            $query .= '[not(ancestor::header)][not(ancestor::pre)][not(ancestor::object)][not(ancestor::textarea)]';
                    }
                }
            }

            /*
             * Parsing of the Glossary Index Page
             */
            if (\CM\CMTT_Settings::get('cmtt_glossary_index_dont_parse', 1) == 1) {
                $query .= '[not(ancestor::div[@class=\'cm-tooltip\'])]';
            }

            /*
             * Exclude specific tags
             */
            if (strlen(\CM\CMTT_Settings::get('cmtt_glossaryParseExcludedTags')) > 0) {
                $excludedTagsArr = explode(',', \CM\CMTT_Settings::get('cmtt_glossaryParseExcludedTags'));
                $excludeGlossaryTagsStr = '';
                if (!empty($excludedTagsArr)) {
                    foreach ($excludedTagsArr as $tag) {
                        $excludeGlossaryTagsStr .= '[not(ancestor::' . trim($tag) . ')]';
                    }
                }
                $query .= $excludeGlossaryTagsStr;
            }

            /*
             * Parsing of the already-parsed items
             */
            $query .= '[not(ancestor::span[contains(concat(\' \', @class, \' \'), \' glossaryLink \')])]';

            /*
             * Parsing of the already-parsed items
             */
            $query .= '[not(ancestor::a[contains(concat(\' \', @class, \' \'), \' glossaryLink \')])]';

            /*
             * Parsing of the term title in the Language Table view
             */
            $query .= '[not(ancestor::span[contains(concat(\' \', @class, \' \'), \' cmtt-related-term-title \')])]';

            /*
             * Parsing of the term title in the Language Table view
             */
            $query .= '[not(ancestor::a[contains(concat(\' \', @class, \' \'), \' cmtt-related-term-title \')])]';

            /*
             * Parsing of the AMP tooltip content
             */
            $query .= '[not(ancestor::span[contains(concat(\' \', @class, \' \'), \' amp-tooltip \')])]';

            /*
             * Parsing of the Categories dropdown and category links
             */
            $query .= '[not(ancestor::select[contains(concat(\' \', @class, \' \'), \' glossary-categories \')])]';
            $query .= '[not(ancestor::div[contains(concat(\' \', @class, \' \'), \' cmtt-categories-filter \')])]';

            /*
             * Parsing of the wistia videos
             */
            $query .= '[not(ancestor::div[contains(concat(\' \', @class, \' \'), \' avia_codeblock \')])]';

            /*
             * Parsing of the wp-captions
             */
            if (\CM\CMTT_Settings::get('cmtt_glossaryCaptions', 1) == 1) {
                $query .= '[not(ancestor::*[contains(concat(\' \', @class, \' \'), \' wp-caption \')])]';
            }

            /*
             * Parsing of the layerslider
             */
            $query .= '[not(ancestor::*[contains(concat(\' \', @class, \' \'), \' ls-wp-container \')])]';

            /*
             * Parsing of the custom classes
             */
            $parsingExcludedClassArr = explode(',', \CM\CMTT_Settings::get('cmtt_glossaryParseExcludedClasses', ''));
            $parsingExcludedClassArr = array_filter($parsingExcludedClassArr);
            if (!empty($parsingExcludedClassArr) && is_array($parsingExcludedClassArr)) {
                foreach ($parsingExcludedClassArr as $class) {
                    $query .= '[not(ancestor::*[contains(concat(\' \', @class, \' \'), \' ' . trim($class) . ' \')])]';
                }
            }

            $nodeList = $xpath->query(apply_filters('cmtt_glossary_xpath_query', $query));
            if (!empty($nodeList) && $nodeList->length > 0) {
                foreach ($nodeList as $node) {
                    /* @var $node DOMText */

                    $replaced = preg_replace_callback($glossarySearchString, array(
                        self::$calledClassName,
                        'cmtt_replace_matches'
                            ), htmlspecialchars($node->wholeText, ENT_COMPAT));
                    if (!empty($replaced)) {
                        $newNode = $dom->createDocumentFragment();
                        $replacedShortcodes = strip_shortcodes($replaced);
                        $result = $newNode->appendXML('<![CDATA[' . $replacedShortcodes . ']]>');

                        if ($result !== false) {
                            $node->parentNode->replaceChild($newNode, $node);
                        }
                    }
                }

                do_action('cmtt_xpath_main_query_after', $xpath, $glossarySearchString, $dom);

                /*
                 *  get only the body tag with its contents, then trim the body tag itself to get only the original content
                 */
                $bodyNode = $xpath->query('//body')->item(0);
                if ($bodyNode !== null) {
                    $newDom = new DOMDocument();
                    $newDom->appendChild($newDom->importNode($bodyNode, true));

                    $intermalHtml = $newDom->saveHTML();
                    $html = mb_substr(trim($intermalHtml), 6, ( mb_strlen($intermalHtml) - 14));
                    /*
                     * Fixing the self-closing which is lost due to a bug in DOMDocument->saveHtml() (caused a conflict with NextGen)
                     */
                    $html = preg_replace('#(<img[^>]*[^/])>#Ui', '$1/>', $html);
                    if (\CM\CMTT_Settings::get('cmtt_convert_to_initial_encoding', 0)) {
                        $html = mb_convert_encoding($html, "UTF-8", 'HTML-ENTITIES');
                    }
                }
            }
        }

        if ($cmWrapItUp) {
            $html = mb_substr(trim($html), 6, ( mb_strlen($html) - 14), "UTF-8");
        }

        return $html;
    }

    /**
     * Simple function to search the terms in the content
     *
     * @param string $html
     * @param string $glossarySearchString
     *
     * @return string
     * @since 2.3.1
     */
    public static function cmtt_simple_str_replace($html, $glossarySearchString) {
        if (!empty($html) && is_string($html)) {
            $replaced = preg_replace_callback($glossarySearchString, array(
                self::$calledClassName,
                'cmtt_replace_matches'
                    ), $html);
            $html = $replaced;
        }

        return $html;
    }

    /**
     * BuddyPress record custom post type comments
     *
     * @param array $post_types
     *
     * @return string
     */
    public static function cmtt_bp_turn_off_parsing($content) {
        if (!\CM\CMTT_Settings::get('cmtt_glossaryParseBuddyPressPages', 1)) {
            remove_filter('the_content', array(
                self::$calledClassName,
                'cmtt_glossary_parse'
                    ), \CM\CMTT_Settings::get('cmtt_tooltipParsingPriority', 20000));
        }

        return $content;
    }

    /**
     * BuddyPress record custom post type comments
     *
     * @param array $post_types
     *
     * @return string
     */
    public static function cmtt_bp_record_my_custom_post_type_comments($post_types) {
        $post_types[] = 'glossary';

        return $post_types;
    }

    /**
     * Adds the support for the custom tooltips
     * [glossary_tooltip content="text" dashicon="dashicons-editor-help" color="#c0c0c0" size="16px"]term[/glossary_tooltip]
     */
    public static function cmtt_custom_tooltip_shortcode($atts, $text = '') {
        $content = __('Use the &quot;content&quot; attribute on the shortcode to change this text', 'cm-tooltip-glossary');
        $dashicon = '';
        $additionalClass = '';

        $atts = shortcode_atts(array(
            'content'   => $content,
            'dashicon'  => '',
            'size'      => '',
            'color'     => '',
            'underline' => '',
            'link'      => '',
            'term_id'   => ''
                ), $atts);

        if (!empty($atts['dashicon'])) {
            $style = '';
            if (!empty($atts['size'])) {
                $style .= 'font-size:' . esc_attr($atts['size']) . ';';
            }
            if (!empty($atts['color'])) {
                $style .= 'color:' . esc_attr($atts['color']) . ';';
            }
            $dashicon = '<span class="dashicons ' . esc_attr($atts['dashicon']) . '" style="' . $style . 'display:inline;"></span>';

            /*
             * Don't underline unless underline="1"
             */
            if (empty($atts['underline'])) {
                $additionalClass = ' hasDashicon';
            }
        }

        $wrappedContent = '';
        if (!empty($atts['term_id'])) {
            $glossary_item = get_post($atts['term_id']);
            if (!empty($glossary_item)) {
                $wrappedContent = self::getTooltipContent($glossary_item);
            }
        }
        if (empty($wrappedContent)) {
            $wrappedContent = '<div class=glossaryItemBody>' . htmlentities($atts['content']) . '</div>';
        }

        if (!empty($atts['link'])) {
            $tooltip = '<a href="' . esc_url($atts['link']) . '" data-cmtooltip="' . $wrappedContent . '" class="glossaryLink' . $additionalClass . '">' . $dashicon .
                    $text . '</a>';
        } else {
            $tooltip = '<span data-cmtooltip="' . $wrappedContent . '" class="glossaryLink' . $additionalClass . '">' . $dashicon . $text . '</span>';
        }

        CMTT_Glossary_Index::$shortcodeDisplayed = 1;

        return $tooltip;
    }

    /**
     * Returns the list of sorted glossary items
     * @staticvar array $glossary_index_full_sorted
     *
     * @param type $args
     *
     * @return type
     */
    public static function getGlossaryItemsSorted($args = array()) {
        static $glossary_index_full_sorted = array();

        if ($glossary_index_full_sorted === array()) {
            /*
             * 1) Get list of all Glossary Terms from database
             */
            $glossary_index = self::getGlossaryItems($args);

            /*
             * 2) Add all the synonyms/variants/abbreviations - everything what parser can find for given term
             */
            $glossary_index_full = self::glossaryFillAdditions($glossary_index);
            $glossary_index_full_sorted = $glossary_index_full;
            /*
             * 3) Sort the items in such way, that longest title/synonyms/variants are before shorter ones
             * (currently only works by sorting titles, need to find way to sort $glossary_item->additions too)
             */
            uasort($glossary_index_full_sorted, array(self::$calledClassName, '_sortByWPQueryObjectTitleLength'));
        }

        return apply_filters('cmtt_glossary_index_sorted', $glossary_index_full_sorted, $args);
    }

    /**
     * Function responsible for storing all of additional strings which should trigger parsing 
     * eg. synonyms,variations,abbreviations
     * @param type $glossary_index
     * @return type
     */
    public static function glossaryFillAdditions($glossary_index) {
        if (!empty($glossary_index)) {
            foreach ($glossary_index as $id => $glossary_term) {
                $glossary_term->additions = apply_filters('cmtt_glossary_index_item_additions', [], $glossary_term);
            }
        }
        return $glossary_index;
    }

    /**
     * Returns the cachable array of all Glossary Terms, either sorted by title, or by title length
     *
     * @staticvar array $glossary_index
     * @staticvar array $glossary_index_sorted
     *
     * @param array $args
     *
     * @return array
     */
    public static function getGlossaryItems($args = array()) {
        global $wpdb;
        static $cache_clearing_loop_break = false;

        $glossary_index = array();

        $encodedArgs = json_encode($args);
        $argsKey = 'cmtt_' . md5('args' . $encodedArgs);
        $allArgsKey = 'cmtt_all_args_keys';

        if (!\CM\CMTT_Settings::get('cmtt_glossaryEnableCaching', false)) {
            delete_transient($argsKey);
            $glossaryItems = false;
        } else {
            $glossaryItems = get_transient($argsKey);
        }
        if (false === $glossaryItems || !isset($glossaryItems['args']) || !isset($glossaryItems['args']['fields'])) {
            $defaultArgs = array(
                'post_type'              => 'glossary',
                'post_status'            => 'publish',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'suppress_filters'       => false,
                'fields'                 => 'ids',
            );

            $queryArgs = array_merge($defaultArgs, $args);

            $nopaging_args = $queryArgs;
            $nopaging_args['nopaging'] = true;
            $nopaging_args['numberposts'] = - 1;

            if ($args === array()) {
                $queryArgs = $nopaging_args;
            }

            $glossaryItems['args'] = $queryArgs;
            $glossaryItems['nopaging_args'] = $nopaging_args;
            if ($queryCacheKey = apply_filters('cmtt_glossary_items_query_cache_key', '')) {
                $glossaryItems['query'] = wp_cache_get($queryCacheKey);
                if (false === $glossaryItems['query']) {
                    $glossaryItems['query'] = new WP_Query($glossaryItems['args']);
                    wp_cache_set($queryCacheKey, $glossaryItems['query'], '', 3600);
                }
            } else {
                $glossaryItems['query'] = new WP_Query($glossaryItems['args']);
            }

            if (\CM\CMTT_Settings::get('cmtt_glossaryEnableCaching', false)) {
                $allArgsKeys = get_transient($allArgsKey);
                if (!is_array($allArgsKeys)) {
                    $allArgsKeys = array();
                }
                set_transient($argsKey, $glossaryItems, 5 * MINUTE_IN_SECONDS);

                $allArgsKeys[] = $argsKey;
                set_transient($allArgsKey, $allArgsKeys, 60 * MINUTE_IN_SECONDS);
            }
        }

        /*
         * We need to be 100% sure that the query returns just the ids
         */
        if (!is_object($glossaryItems['query']) || !isset($glossaryItems['args']['fields']) || 'ids' !== $glossaryItems['args']['fields']) {
            $glossaryItems['args']['fields'] = 'ids';
            $glossaryItems['query'] = new WP_Query($glossaryItems['args']);
        }
        if ($indexIdsCacheKey = apply_filters('cmtt_index_ids_cache_key', '')) {
            $glossaryIndexIds = wp_cache_get($indexIdsCacheKey);
            if (false === $glossaryIndexIds) {
                $glossaryIndexIds = $glossaryItems['query']->get_posts();
                wp_cache_set($indexIdsCacheKey, $glossaryIndexIds, '', 3600);
            }
        } else {
            $glossaryIndexIds = $glossaryItems['query']->posts;
        }

        /*
         * Check the result - should contain only ids since 3.3.8
         */
        if (!$cache_clearing_loop_break && !empty($glossaryIndexIds) && is_array($glossaryIndexIds)) {
            /*
             * Check the first item in the table - if it's object (WP_Post) then we need to clear the cache
             */
            $testGlossaryTerm = reset($glossaryIndexIds);
            if (is_object($testGlossaryTerm) && !empty($testGlossaryTerm->ID)) {
                $cache_clearing_loop_break = true;
                self::cmtt_unset_transients($testGlossaryTerm->ID);

                return self::getGlossaryItems($args);
            }
        }

        if (!empty($args['return_just_ids'])) {
            return $glossaryIndexIds;
        }

        $glossaryIndexIdsWhere = !empty($glossaryIndexIds) ? implode(',', $glossaryIndexIds) : - 1;
        $sql = $wpdb->prepare(
                'SELECT ID,post_title,post_name,post_content,post_excerpt,post_date,post_type,post_author FROM ' . $wpdb->posts .
                ' WHERE ID IN (' . $glossaryIndexIdsWhere .
                ') LIMIT %d', '999999');
        if ($glossaryIndexCacheKey = apply_filters('cmtt_glossary_index_cache_key', '')) {
            $glossaryIndex = wp_cache_get($glossaryIndexCacheKey);
            if (false === $glossaryIndex) {
                $glossaryIndex = $wpdb->get_results($sql, ARRAY_A);
                wp_cache_set($glossaryIndexCacheKey, $glossaryIndex, '', 3600);
            }
        } else {
            $glossaryIndex = $wpdb->get_results($sql, ARRAY_A);
        }

        foreach ($glossaryIndex as $key => $post) {
            $obj = (object) $post;
            $newObj = apply_filters('cmtt_get_all_glossary_items_single', $obj, $post);
            $glossary_index[] = $newObj;
            unset($glossaryIndex[$key]);
        }

        /*
         * Save statically
         */
        self::$lastQueryDetails = $glossaryItems;

        return $glossary_index;
    }

    public static function maybeUseOtherTitleOnIndex($obj, $post) {
        $glossaryIndexTitle = self::_get_meta('_cmtt_term_index_title', $obj->ID);
        if (!empty($glossaryIndexTitle)) {
            $obj->post_title = $glossaryIndexTitle;
        }
        return $obj;
    }

    /**
     * 
     * @param int $output 0 - multicheckbox, 1 - select
     * @return string
     */
    public static function outputCustomPostTypesList($option_name, $output = 0) {
        $content = '';
        $args = array(
            'public' => true,
        );

        $output_type = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types($args, $output_type, $operator);
        $selected = \CM\CMTT_Settings::get($option_name);

        if (!is_array($selected)) {
            $selected = array();
        }

        $options = [];
        foreach ($post_types as $post_type) {
            $label = $post_type->labels->singular_name . ' (' . $post_type->name . ')';
            $name = $post_type->name;
            $options[$name] = $label;
        }
        $post_types = null;
        $content = CMTT_Free::_outputMultipleValues($option_name, $options, $selected, $output);

        return $content;
    }

    /**
     * 
     * @param type $option_name
     * @param type $default_values
     * @param type $guest
     * @param int $output 0 - multicheckbox, 1 - select
     * @return string
     */
    public static function outputRolesList($option_name, $default_values, $guest = false, $output = 0) {
        $content = '';

        $roles = get_editable_roles();
        $selected = \CM\CMTT_Settings::get($option_name, $default_values);

        if (!is_array($selected)) {
            $selected = array();
        }

        // Adding Guest to the roles list
        if ($guest) {
            $roles = array_merge($roles, array('Guest' => array('name' => 'Not logged in user')));
        }

        foreach ($roles as $role => $role_info) {
            $roles[$role] = $role . ' (' . $role_info['name'] . ')';
        }

        $content = CMTT_Free::_outputMultipleValues($option_name, $roles, $selected, $output);

        return $content;
    }

    /**
     * 
     * @param type $option_name
     * @param type $defaultSelected
     * @param int $output 0 - multicheckbox, 1 - select
     * @return string
     */
    public static function outputACFTypesList(
            $option_name = 'cmtt_acf_parsed_field_types', $defaultSelected = array(
                'text',
                'wysiwyg'
            )
            , $output = 0) {
        $content = '';

        $options = array('text' => 'Text', 'textarea' => 'Textarea', 'wysiwyg' => 'WYSIWYG', 'message' => 'Message', 'checkbox' => 'Checkbox');
        $selected = \CM\CMTT_Settings::get($option_name, $defaultSelected);

        if (!is_array($selected)) {
            $selected = array();
        }

        $content = CMTT_Free::_outputMultipleValues($option_name, $options, $selected, $output);

        return $content;
    }

    public static function _outputMultipleValues($option_name, $options, $selected = [], $output = 0) {
        $content = '';

        if (1 === $output) {
            $content .= '<div><select name="' . $option_name . '[]" multiple="multiple">';
        }

        foreach ($options as $name => $label) {
            switch ($output) {
                case 0:
                default:
                    $content .= '<div><label><input type="checkbox" name="' . $option_name . '[]" ' . checked(true, in_array($name, $selected), false) . ' value="' . $name . '" />' . $label . '</label></div>';
                    break;
                case 1:
                    $content .= '<option value="' . $name . '" ' . selected(true, in_array($name, $selected), false) . '>' . $label . '</option>';
                    break;
            }
        }

        if (1 === $output) {
            $content .= '</select></div>';
        }

        return $content;
    }

    /*
     *  Sort longer titles first, so if there is collision between terms
     * (e.g., "essential fatty acid" and "fatty acid") the longer one gets created first.
     */

    public static function _sortByWPQueryObjectTitleLength($a, $b) {
        $sortVal = 0;
        if (property_exists($a, 'post_title') && property_exists($b, 'post_title')) {
            $sortVal = strlen($b->post_title) - strlen($a->post_title);
        }

        return $sortVal;
    }

    /**
     * Plugin activation
     */
    protected static function _activate() {
        CMTT_Glossary_Index::tryGenerateGlossaryIndexPage();
        do_action('cmtt_do_activate');
    }

    /**
     * Plugin installation
     *
     * @param type $networkwide
     *
     * @return type
     * @global type $wpdb
     */
    public static function _install($networkwide) {
        global $wpdb;

        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs}", array()));
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::_activate();
                    self::_add_caps();
                }
                switch_to_blog($old_blog);

                return;
            }
        }

        self::_activate();
        self::_add_caps();
    }

    /**
     * Flushes the caps for the roles
     *
     * @global type $wp_rewrite
     */
    public static function _add_caps($roles = array()) {
        global $wp_roles;

        if (class_exists('WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }
        }

        /*
         * First reset the caps
         */
        $allRoles = get_editable_roles();
        foreach ($allRoles as $role => $role_info) {
            $wp_roles->remove_cap($role, 'manage_glossary');
        }

        $roles = !empty($roles) ? $roles : \CM\CMTT_Settings::get('cmtt_glossaryRoles', array('administrator', 'editor'));

        if (is_object($wp_roles)) {

            foreach ($roles as $role) {
                $wp_roles->add_cap($role, 'manage_glossary');
            }
        }
    }

    public static function _get_meta($meta_key, $id = null) {
        global $wpdb;
        static $_cache = array();

        if (!isset($_cache[$meta_key])) {
            if ($metaCacheKey = apply_filters('cmtt_meta_cache_key', '', $meta_key, $id)) {
                $_cache[$meta_key] = wp_cache_get($metaCacheKey);
                if (false === $_cache[$meta_key]) {
                    $_cache[$meta_key] = array_column(
                            $wpdb->get_results(
                                    $wpdb->prepare(
                                            'SELECT post_id,meta_value FROM ' . $wpdb->postmeta .
                                            ' WHERE meta_key="%s" LIMIT %d', $meta_key, PHP_INT_MAX),
                                    ARRAY_A),
                            'meta_value', 'post_id');
                    wp_cache_set($metaCacheKey, $_cache[$meta_key], '', 3600);
                }
            } else {
                $arr = $wpdb->get_results(
                        $wpdb->prepare(
                                'SELECT post_id,meta_value FROM ' . $wpdb->postmeta .
                                ' WHERE meta_key="%s" LIMIT %d', $meta_key, PHP_INT_MAX), ARRAY_A);
                $_cache[$meta_key] = array_column($arr, 'meta_value', 'post_id');
            }
        }

        if (null !== $id) {
            $result = (isset($_cache[$meta_key]) && isset($_cache[$meta_key][$id])) ? $_cache[$meta_key][$id] : false;
        } else {
            $result = $_cache[$meta_key];
        }

        return maybe_unserialize($result);
    }

    public static function _get_term($taxonomies, $id = null) {
        global $wpdb;
        static $_cache = array();

        if (!isset($_cache[$taxonomies])) {

            $taxonomies_query = "'" . $taxonomies . "'";
            $query = "SELECT t.*, tt.*, tr.object_id as post_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies_query) LIMIT %d";
            $results = $wpdb->get_results($wpdb->prepare($query, PHP_INT_MAX), ARRAY_A);
            foreach ($results as $value) {
                $_cache[$taxonomies][$value['post_id']][] = $value['name'];
            }
        }

        if (null !== $id) {
            $result = isset($_cache[$taxonomies][$id]) ? $_cache[$taxonomies][$id] : false;
        } else {
            $result = $_cache[$taxonomies];
        }

        return $result;
    }

    /**
     * Get AJAX URL
     *
     * @return string URL to the AJAX file to call during AJAX requests.
     * @since 1.3
     */
    function _get_ajax_url() {
        $scheme = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'https' : 'admin';

        $current_url = edd_get_current_page_url();
        $ajax_url = admin_url('admin-ajax.php', $scheme);

        if (preg_match('/^https/', $current_url) && !preg_match('/^https/', $ajax_url)) {
            $ajax_url = preg_replace('/^http/', 'https', $ajax_url);
        }

        return apply_filters('cmtt_ajax_url', $ajax_url);
    }

    public static function is_DOM_parser_disabled($post) {
        $runThroughSimpleFunctionAll = \CM\CMTT_Settings::get('cmtt_disableDOMParser', false);
        if (1 == CMTT_Free::_get_meta('_glossary_disable_dom_parser_global_settings_for_page', $post->ID)) {
            $runThroughSimpleFunctionAll = !$runThroughSimpleFunctionAll;
        }

        return $runThroughSimpleFunctionAll;
    }

    public static function addLinkToTooltip($glossaryItemContent, $glossary_item) {
        $showLink = \CM\CMTT_Settings::get('cmtt_glossaryTooltipShowLink');
        $limitArticles = \CM\CMTT_Settings::get('cmtt_glossaryTooltipAmountLinks');
        $limitCustomArticles = \CM\CMTT_Settings::get('cmtt_glossaryTooltipAmountCustomLinks');
        if ($showLink == 1) {
            if (!$limitArticles) {
                $limitArticles = 5;
            }
            $glossaryItemContent = $glossaryItemContent;
        }
        return $glossaryItemContent;
    }

    public static function getFirstLetter($title, $permalink, $sortByTitle = true) {
        if ($sortByTitle) {
            $what = $title;
        } else {
            $what = $permalink;
        }

        mb_internal_encoding("UTF-8");
        $what = urldecode($what);

        $letter = mb_strtolower(mb_substr($what, 0, 1));
        return $letter;
    }

    /**
     * Returns the numbers of posts for server-side pagination
     * @param array $shortcodeAtts
     * @return array
     */
    public static function getListnavCounts($shortcodeAtts) {
        $counts = array();
        $nonLatinLetters = (bool) \CM\CMTT_Settings::get('cmtt_index_nonLatinLetters');
        $sortByTitle = (bool) \CM\CMTT_Settings::get('cmtt_index_sortby_title');

        $args = array();

        do_action('cmtt_glossary_doing_search', $args, $shortcodeAtts);
        $hideTerms = !empty($shortcodeAtts['hide_terms']);

        $glossaryIndex = CMTT_Free::getGlossaryItems($args);
        $counts['all'] = !$hideTerms ? count($glossaryIndex) : 0;

        foreach ($glossaryIndex as $term) {
            if (!$hideTerms) {
                $firstLetterOriginal = self::getFirstLetter($term->post_title, $term->post_name, $sortByTitle);
                if (!$nonLatinLetters) {
                    $firstLetterOriginal = remove_accents($firstLetterOriginal);
                }
                $firstLetter = mb_strtolower($firstLetterOriginal);

                if (preg_match('/\d/', $firstLetter)) {
                    $firstLetter = 'al-num';
                }

                if (!isset($counts[$firstLetter])) {
                    $counts[$firstLetter] = 0;
                }
                if (\CM\CMTT_Settings::get('cmtt_limitNum') > 0) {
                    if ($counts[$firstLetter] > \CM\CMTT_Settings::get('cmtt_limitNum') - 1)
                        continue;
                    $counts['all'] = '';
                }
                $counts[$firstLetter]++;
            }

            $counts = apply_filters('cmtt_modify_listnav_counts_term', $counts, $term, $shortcodeAtts);
        }

        return apply_filters('cmtt_modify_listnav_counts_all', $counts);
    }

    /**
     * Function outputs the ListNav for server-side pagination
     * @param string $listnavOutput
     * @param array $shortcodeAtts
     * @return string
     */
    public static function outputListnav($listNavInsideContent, $shortcodeAtts, $glossaryQuery) {
        if (!CMTT_Glossary_Index::isServerSide() || (isset($shortcodeAtts['disable_listnav']) && $shortcodeAtts['disable_listnav'])) {
            if (!CMTT_AMP::is_amp_endpoint())
                return $listNavInsideContent;
        }

        $initLetter = \CM\CMTT_Settings::get('cmtt_index_initLetter', '');
        $currentlySelectedLetter = (!empty($shortcodeAtts["letter"]) && empty($shortcodeAtts["search_term"])) ? $shortcodeAtts["letter"] : (!empty($initLetter) ? $initLetter : 'all');
        $currentlySelectedLetter = (!empty($shortcodeAtts['search_term']) && \CM\CMTT_Settings::get('cmtt_glossary_ignore_letter_on_search', 0)) ? 'all' : $currentlySelectedLetter;
        $letters = (array) \CM\CMTT_Settings::get('cmtt_index_letters');
        $letters = apply_filters('cmtt_index_letters', $letters, $shortcodeAtts);
        $includeAll = (bool) \CM\CMTT_Settings::get('cmtt_index_includeAll');
        $includeNum = (bool) \CM\CMTT_Settings::get('cmtt_index_includeNum');
        $round = (bool) \CM\CMTT_Settings::get('cmtt_index_showRound');
        $allLabel = \CM\CMTT_Settings::get('cmtt_index_allLabel', 'ALL');
        $showCounts = \CM\CMTT_Settings::get('cmtt_index_showCounts', '1');
        $glossaryPageLink = apply_filters('cmtt_index_glossary_page_link', $shortcodeAtts['glossary_page_link']);

        $postCounts = self::getListnavCounts($shortcodeAtts);

        $listNavInsideContent .= '<div class="ln-letters ' . ($round ? 'round' : '') . '">';
        $listNavInsideContent .= apply_filters('cmtt_after_ln_letters_content', '');

        if ($includeAll) {
            $postsCount = $postCounts['all'];
            $selectedClass = $currentlySelectedLetter == 'all' ? ' ln-selected' : '';
            $listNavInsideContent .= '<a class="ln-all ln-serv-letter' . $selectedClass
                    . '" data-letter="all" data-letter-count="' . $postsCount . '" href="#" '
                    . apply_filters('cmtt_all_letter_additional_attributes', '', $selectedClass, $postsCount) . '>'
                    . apply_filters('cmtt_index_all_label', $allLabel, $postsCount, $showCounts) . '</a>';
        }

        if ($includeNum) {
            $postsCount = isset($postCounts['al-num']) ? $postCounts['al-num'] : 0;
            $disabledClass = $postsCount == 0 ? ' ln-disabled' : '';
            $selectedClass = $currentlySelectedLetter == 'al-num' ? ' ln-selected' : '';
            $listNavInsideContent .= '<a class="ln-_ ln-serv-letter' . $disabledClass . $selectedClass
                    . '" data-letter-count="' . $postsCount . '" data-letter="al-num" href="' . $glossaryPageLink
                    . '" ' . apply_filters('cmtt_num_letter_additional_attributes', '', $disabledClass, $selectedClass, $postsCount) . '>'
                    . apply_filters('cmtt_index_num_label', '0-9', $postsCount, $showCounts) . '</a>';
        }

        foreach ($letters as $key => $letter) {
            $postsCount = isset($postCounts[$letter]) ? $postCounts[$letter] : 0;
            $isLast = ($key == count($letters) - 1 );
            $lastClass = $isLast ? ' ln-last' : '';
            $disabledClass = $postsCount == 0 ? ' ln-disabled' : '';
            $selectedClass = $currentlySelectedLetter == $letter ? ' ln-selected' : '';

            $listNavInsideContent .= '<a class="lnletter-' . $letter . ' ln-serv-letter' . $lastClass . $disabledClass . $selectedClass
                    . '" data-letter-count="' . $postsCount
                    . '" data-letter="' . $letter
                    . '" href="' . $glossaryPageLink . '" '
                    . apply_filters('cmtt_letter_letter_additional_attributes', '', $key, $letter, $lastClass, $disabledClass, $selectedClass, $postsCount) . '>'
                    . apply_filters('cmtt_index_letter_label', mb_strtoupper(str_replace('ı', 'İ', $letter)), $postsCount, $showCounts) . '</a>';
        }

        if ($showCounts) {
            $resultsLabel = \CM\CMTT_Settings::get('cmtt_index_resultsLabel', 'Results:');
            $listNavInsideContent .= '<div class="ln-letter-count" style="position: absolute; top: 0px; left: 88px; width: 20px; display: none;"></div>';
            $listNavInsideContent .= '<div class="query-results-count">'.sprintf('%s %d',$resultsLabel, $glossaryQuery->found_posts).'</div>';
        }
        $listNavInsideContent .= '</div>';

        return $listNavInsideContent;
    }

    public static function cm_tooltip_custom_content($atts = array(), $content = null) {
        return $content;
    }

    /*
     * Checks whether the parsing is disabled or enabled on current page
     * 0 - using global settings;
     * 1 - don't search glossary items on this page despite global settings;
     * 2 - search glossary items on this page despite global settings.
     */

    static function is_parsing_disabled($post_id) {
        $post_meta = get_post_meta($post_id, '_glossary_disable_for_page', TRUE);
        $post_meta = !empty($post_meta) ? $post_meta : 0;
        $disabled = FALSE;

        switch ($post_meta) {
            case 0:
                $post = get_post($post_id);
                if (is_object($post)) {
                    $selected_post_types = \CM\CMTT_Settings::get('cmtt_glossaryOnPosttypes');

                    if (is_array($selected_post_types) && !empty($selected_post_types)) {
                        $disabled = in_array($post->post_type, $selected_post_types) ? FALSE : TRUE;
                    } else {
                        $disabled = TRUE;
                    }
                }
                break;
            case 1:
                $disabled = TRUE;
                break;
            case 2:
                $disabled = FALSE;
                break;
        }

        return $disabled;
    }

    public static function normalizeTitle($title) {
        $normalizedTitle = preg_quote(htmlspecialchars(trim($title), ENT_QUOTES, 'UTF-8'), '/');
        $normalizedTitle = str_replace(array('&\#039;', '&\#096;', '&\#146;', '’', "'", '&#039;', '&#096;', '&#146;', '&rsquo;'),
                '.',
                $normalizedTitle
        );
        return $normalizedTitle;
    }

    public static function cmtt_parse_ninja_tables($table, $tableId) {
        global $post;
        $post = new stdClass();

        foreach ($table as $key => $row) {
            foreach ($row as $rowkey => $cell) {
                if ($rowkey == '___id___') {
                    continue;
                }
                $cmWrapItUp = true;
                $table[$key][$rowkey] = apply_filters('cm_tooltip_parse', $cell, true);
                $cmWrapItUp = false;
            }
        }

        return $table;
    }

    /**
     * Function should quickly scan the content looking for potential terms
     * without looking for replacements. It's meant to be used for huge glossaries
     * to limit the amount of terms that needs to be searched and replaced using 
     * DOM parser
     * 
     * @param type $content
     * @return array
     */
    public static function quickScan($potentialTerms, $content) {
        $foundTerms = [];

        $caseSensitive = \CM\CMTT_Settings::get('cmtt_glossaryCaseSensitive', 0);

        foreach ($potentialTerms as $key => $glossary_item) {
            /*
             * Simply try to find the term in the content
             */
            if (!$caseSensitive) {
                $found = FALSE !== stripos($content, $glossary_item->post_title);
            } else {
                $found = FALSE !== strpos($content, $glossary_item->post_title);
            }
            if ($found) {
                $foundTerms[$key] = $glossary_item;
                continue;
            } elseif (!empty($glossary_item->additions)) {
                /*
                 * Try to find any of the additions in content
                 */
                foreach ($glossary_item->additions as $key => $addition) {
                    if (!$caseSensitive) {
                        if (FALSE !== stripos($content, $addition)) {
                            $foundTerms[$key] = $glossary_item;
                            continue;
                        }
                    } else {
                    if (FALSE !== strpos($content, $addition)) {
                        $foundTerms[$key] = $glossary_item;
                        continue;
                    }
                }
            }
        }
        }
        return $foundTerms;
    }

}
