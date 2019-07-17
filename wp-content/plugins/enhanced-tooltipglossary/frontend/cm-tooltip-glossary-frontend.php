<?php

class CMTooltipGlossaryFrontend {

    public static $calledClassName;
    protected static $instance = NULL;
    protected static $cssPath = NULL;
    protected static $jsPath = NULL;
    protected static $viewsPath = NULL;

    public static function instance() {
        $class = __CLASS__;
        if (!isset(self::$instance) && !( self::$instance instanceof $class )) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct() {
        if (empty(self::$calledClassName)) {
            self::$calledClassName = __CLASS__;
        }

        self::$cssPath = CMTT_PLUGIN_URL . 'frontend/assets/css/';
        self::$jsPath = CMTT_PLUGIN_URL . 'frontend/assets/js/';
        self::$viewsPath = CMTT_PLUGIN_DIR . 'frontend/views/';

        add_action('wp_print_styles', array(self::$calledClassName, 'cmtt_glossary_css'));
        add_action('wp_enqueue_scripts', array(self::$calledClassName, 'cmtt_glossary_js'));

        /*
         * FILTERS
         */
        add_filter('get_the_excerpt', array(self::$calledClassName, 'cmtt_disable_parsing'), 1);
        add_filter('wpseo_opengraph_desc', array(self::$calledClassName, 'cmtt_reenable_parsing'), 1);

        /*
         * Make sure parser runs before the post or page content is outputted
         */
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_parse'), 9999);
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_createList'), 9998);
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 10000);

        /*
         *  We need to redirect the queries to the archive
         */
        add_action('template_redirect', array(self::$calledClassName, 'cmtt_templateRedirect'), 1);

        add_action('bp_before_create_group', array(self::$calledClassName, 'outputGlossaryExcludeStart'));
        add_action('bp_before_group_admin_content', array(self::$calledClassName, 'outputGlossaryExcludeStart'), 50);
        add_action('bp_attachments_avatar_check_template', array(self::$calledClassName, 'outputGlossaryExcludeStart'), 50);
        add_action('bp_before_profile_avatar_upload_content', array(self::$calledClassName, 'outputGlossaryExcludeStart'), 50);
        add_action('bp_before_profile_edit_cover_image', array(self::$calledClassName, 'outputGlossaryExcludeStart'), 50);

        add_action('bp_after_create_group', array(self::$calledClassName, 'outputGlossaryExcludeEnd'));
        add_action('bp_after_group_admin_content', array(self::$calledClassName, 'outputGlossaryExcludeEnd'), 50);
        add_action('bp_attachments_avatar_main_template', array(self::$calledClassName, 'outputGlossaryExcludeEnd'), 50);
        add_action('bp_after_profile_avatar_upload_content', array(self::$calledClassName, 'outputGlossaryExcludeEnd'), 50);
        add_action('bp_after_profile_edit_cover_image', array(self::$calledClassName, 'outputGlossaryExcludeEnd'), 50);
    }

    /**
     * Adds tooltip javascript
     */
    public static function cmtt_glossary_js() {
        wp_enqueue_script('tooltip-js', self::$jsPath . 'tooltip.js', array('jquery'), false);

        $tooltipData = array();

        $tooltipArgs = array(
            'clickable' => 0, //(int) get_option('cmtt_tooltipIsClickable', 0),
            'top' => 3, //(int) get_option('cmtt_tooltipPositionTop', 3),
            'left' => 23, //(int) get_option('cmtt_tooltipPositionLeft', 23),
            'endalpha' => 95, //(int) get_option('cmtt_tooltipOpacity', 95),
            'borderStyle' => 'none', //get_option('cmtt_tooltipBorderStyle', 'none'),
            'borderWidth' => '0px', //get_option('cmtt_tooltipBorderWidth', 0) . 'px',
            'borderColor' => '#000', //get_option('cmtt_tooltipBorderColor', '#000'),
            'fontSize' => '13px', //get_option('cmtt_tooltipFontSize', 13) . 'px',
            'padding' => '2px 12px 3px 7px', //get_option('cmtt_tooltipPadding', '2px 12px 3px 7px'),
            'borderRadius' => '6px', //get_option('cmtt_tooltipBorderRadius', 6) . 'px'
        );

        $tooltipData['tooltip'] = $tooltipArgs;
        $tooltipData['ajaxurl'] = admin_url('admin-ajax.php');

        wp_localize_script('tooltip-js', 'cmtt_data', $tooltipData);

        self::cmtt_glossary_createList_scripts();
    }

    /**
     * Outputs the frontend CSS
     */
    public static function cmtt_glossary_css() {
        wp_enqueue_style('tooltip', self::$cssPath . 'tooltip.css');
    }

    /**
     * Add the dynamic CSS to reflect the styles set by the options
     * @return type
     */
    public static function cmtt_dynamic_css() {
        ob_start();
        echo apply_filters('cmtt_dynamic_css_before', '');
        ?>

        .tiles ul a { min-width: <?php echo get_option('cmtt_glossarySmallTileWidth', '85px'); ?>; width:<?php echo get_option('cmtt_glossarySmallTileWidth', '85px'); ?>;  }
        .tiles ul span { min-width:<?php echo get_option('cmtt_glossarySmallTileWidth', '85px'); ?>; width:<?php echo get_option('cmtt_glossarySmallTileWidth', '85px'); ?>;  }

        <?php
        echo apply_filters('cmtt_dynamic_css_after', '');
        $content = ob_get_clean();

        /*
         * One can use this filter to change/remove the standard styling
         */
        $dynamicCSScontent = apply_filters('cmtt_dynamic_css', $content);
        return trim($dynamicCSScontent);
    }

    /**
     * Adds the scripts which has to be included on the main glossary index page only
     */
    public static function cmtt_glossary_createList_scripts() {
        $glossaryPageID = get_option('cmtt_glossaryID');
        if (is_numeric($glossaryPageID) && is_page($glossaryPageID)) {
            wp_enqueue_script('jquery-listnav', self::$jsPath . 'jquery.listnav.glossary.js', array('jquery'));
            wp_enqueue_script('listnav-js', self::$jsPath . 'listnav.js', array('jquery', 'jquery-listnav'));

            wp_enqueue_style('jquery-listnav-style', self::$cssPath . 'jquery.listnav.css');
            /*
             * It's WP 3.3+ function
             */
            if (function_exists('wp_add_inline_style')) {
                wp_add_inline_style('jquery-listnav-style', self::cmtt_dynamic_css());
            }

            $listnavArgs = array(
                'includeAll' => true,
                'allLabel' => __('ALL', 'cm-tooltip-glossary'),
                'includeNums' => true,
            );
            $tooltipData['listnav'] = $listnavArgs;
            $tooltipData['list_id'] = 'glossaryList' . (isset($_POST['isshortcode']) && isset($_POST["gcat_id"]) ? '_' . $_POST["gcat_id"] : '');

            wp_localize_script('listnav-js', 'cmtt_listnav_data', $tooltipData);
        }
    }

    /**
     * Disable the parsing for some reason
     * @global type $wp_query
     * @param type $smth
     * @return type
     */
    public static function cmtt_disable_parsing($smth) {
        global $wp_query;
        if ($wp_query->is_main_query() && !$wp_query->is_singular) {  // to prevent conflict with Yost SEO
            remove_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_parse'), 9999);
            remove_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_createList'), 9998);
            remove_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 10000);
        }
        return $smth;
    }

    /**
     * Reenable the parsing for some reason
     * @global type $wp_query
     * @param type $smth
     * @return type
     */
    public static function cmtt_reenable_parsing($smth) {
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_parse'), 9999);
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_createList'), 9998);
        add_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_addBacklink'), 10000);

        return $smth;
    }

    /**
     * Parses the pages/posts and adding the tooltips to the terms
     *
     * @global type $glossary_index
     * @global type $post
     * @global type $glossaryIndexArr
     * @global array $onlySynonyms
     * @global type $caseSensitive
     * @param type $content
     * @param type $force
     * @return type
     */
    public static function cmtt_glossary_parse($content, $force = false) {
        global $glossary_index, $post;

        $seo = doing_action('wpseo_head');
        if ($seo) {
            return $content;
        }

        /*
         * Initialize $glossarySearchStringArr as empty array
         */
        $glossarySearchStringArr = array();
        $onlySynonyms = array();

        /*
         * New! 03/01/2014 - these rules simplify the condition whether to parse for tooltip or not
         */
        $showOnSinglePost = (is_single() && get_option('cmtt_glossaryOnPosts') == 1);
        $noHomepage = (get_option('cmtt_glossaryOnlySingle') == 1 && is_front_page());
        $showOnSinglePage = (is_page() && !$noHomepage && get_option('cmtt_glossaryOnPages') == 1);
        $showOnHomepageAuthorpageEtc = (!is_page() && !is_single() && get_option('cmtt_glossaryOnlySingle') == 0);
        $onMainQueryOnly = (get_option('cmtt_glossaryOnMainQuery') == 1 ) ? is_main_query() : TRUE;

        /*
         * Run the glossary parser
         */
        if ($force || ($onMainQueryOnly && ($showOnHomepageAuthorpageEtc || $showOnSinglePage || $showOnSinglePost))) {
            $contentHash = sha1($content);
            if (!$force) {
                $result = wp_cache_get($contentHash, 'cachedParsedGlossaryPages');
                if ($result !== false) {
                    return $result;
                }
            }

            if (empty($glossary_index)) {
                $glossary_index = get_posts(array(
                    'post_type' => 'glossary',
                    'post_status' => 'publish',
                    'order' => 'DESC',
                    'orderby' => 'title',
                    'numberposts' => -1,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false
                        ));
                /*
                 *  Sort by title length (functions.php)
                 */
                uasort($glossary_index, 'cminds_sort_WP_posts_by_title_length');
            } elseif (is_array($glossary_index)) {
                reset($glossary_index);
            }

            //the tag:[glossary_exclude]+[/glossary_exclude] can be used to mark text will not be taken into account by the glossary
            if ($glossary_index) {
                $excludeGlossary_regex = '/\\['   // Opening bracket
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
                        . '(\\]?)/s';

                $excludeGlossaryStrs = array();

                /*
                 * Fix for the &amp; character and the AMP term
                 */
                $content = str_replace('&#038;', '[glossary_exclude]&#038;[/glossary_exclude]', $content);
                /*
                 * Fix 2 for the &amp; character and the AMP term
                 */
                $content = preg_replace('/(\s&\s)/', '[glossary_exclude]$0[/glossary_exclude]', $content);

                /*
                 * Replace exclude tags and content between them in purpose to save the original text as is
                 * before glossary plug go over the content and add its code
                 * (later will be returned to the marked places in content)
                 */
                $excludeTagsCount = preg_match_all($excludeGlossary_regex, $content, $excludeGlossaryStrs, PREG_PATTERN_ORDER);
                $i = 0;

                if ($excludeTagsCount > 0) {
                    foreach ($excludeGlossaryStrs[0] as $excludeStr) {
                        $content = preg_replace($excludeGlossary_regex, '#' . $i . 'excludeGlossary', $content, 1);
                        $i++;
                    }
                }

                global $glossaryIndexArr, $onlySynonyms, $caseSensitive;

                $caseSensitive = get_option('cmtt_glossaryCaseSensitive', 0);

                /*
                 * The loops prepares the search query for the replacement
                 */
                foreach ($glossary_index as $glossary_item) {
                    if ($post->post_type == 'glossary' && ($post->ID === $glossary_item->ID )) {
                        continue;
                    }
//					$glossary_title	 = str_replace( '&#039;', '’', preg_quote( htmlspecialchars( trim( $glossary_item->post_title ), ENT_QUOTES, 'UTF-8' ), '/' ) ); //changed in 3.3.8
                    $glossary_title = preg_quote(htmlspecialchars(trim($glossary_item->post_title), ENT_COMPAT, 'UTF-8'), '/');
                    $addition = '';

                    $glossaryIndexArrKey = $glossary_title . $addition;
                    if (!$caseSensitive) {
                        $glossaryIndexArrKey = mb_strtolower($glossaryIndexArrKey);
                    }
                    $glossarySearchStringArr[] = $glossary_title . $addition;
                    $glossaryIndexArr[$glossaryIndexArrKey] = $glossary_item;
                }

                /*
                 * No replace required if there's no glossary items
                 */
                if (!empty($glossarySearchStringArr) && is_array($glossarySearchStringArr)) {
                    $glossaryArrayChunk = 150;
                    $spaceSeparated = TRUE;

                    if (count($glossarySearchStringArr) > $glossaryArrayChunk) {
                        $chunkedGlossarySearchStringArr = array_chunk($glossarySearchStringArr, $glossaryArrayChunk, TRUE);

                        foreach ($chunkedGlossarySearchStringArr as $glossarySearchStringArrChunk) {
                            $glossarySearchString = '/' . (($spaceSeparated) ? '(?<=\P{L}|^)(?<!(\p{N}))' : '') . '(?!(<|&lt;))(' . (!$caseSensitive ? '(?i)' : '') . implode('|', $glossarySearchStringArrChunk) . ')(?!(>|&gt;))' . (($spaceSeparated) ? '(?=\P{L}|$)(?!(\p{N}))' : '') . '/u';
                            $content = self::cmtt_dom_str_replace($content, $glossarySearchString);
                        }
                    } else {
                        $glossarySearchString = '/' . (($spaceSeparated) ? '(?<=\P{L}|^)(?<!(\p{N}))' : '') . '(?!(<|&lt;))(' . (!$caseSensitive ? '(?i)' : '') . implode('|', $glossarySearchStringArr) . ')(?!(>|&gt;))' . (($spaceSeparated) ? '(?=\P{L}|$)(?!(\p{N}))' : '') . '/u';
                        $content = self::cmtt_dom_str_replace($content, $glossarySearchString);
                    }
                }

                if ($excludeTagsCount > 0) {
                    $i = 0;
                    foreach ($excludeGlossaryStrs[0] as $excludeStr) {
                        $content = str_replace('#' . $i . 'excludeGlossary', $excludeStr, $content);
                        $i++;
                    }

                    /*
                     * remove all the exclude signs
                     */
                    $content = str_replace(array('[glossary_exclude]', '[/glossary_exclude]'), array('', ''), $content);
                }
            }
            $result = wp_cache_set($contentHash, $content, 'cachedParsedGlossaryPages', 120);
        }

        return $content;
    }

    public static function outputGlossaryExcludeStart() {
        echo '[glossary_exclude]';
    }

    public static function outputGlossaryExcludeEnd() {
        echo '[/glossary_exclude]';
    }

    /**
     * New function to search the terms in the content
     *
     * @global array $replacedTerms
     * @param strin $html
     * @param string $glossarySearchString
     * @since 2.3.1
     * @return type
     */
    public static function cmtt_dom_str_replace($html, $glossarySearchString) {
        global $replacedTerms;
        $replacedTerms = is_array($replacedTerms) ? $replacedTerms : array();

        if (!empty($html) && is_string($html)) {
            $dom = new DOMDocument();
            /*
             * loadXml needs properly formatted documents, so it's better to use loadHtml, but it needs a hack to properly handle UTF-8 encoding
             */
            libxml_use_internal_errors(true);
            if (!$dom->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"))) {
//            foreach(libxml_get_errors() as $error)
//            {}
                libxml_clear_errors();
            }
            $xpath = new DOMXPath($dom);

            /*
             * Base query NEVER parse in scripts
             */
            $query = '//text()[not(ancestor::script)][not(ancestor::style)]';
            if (get_option('cmtt_glossaryProtectedTags') == 1) {
                $query .= '[not(ancestor::header)][not(ancestor::a)][not(ancestor::pre)][not(ancestor::object)][not(ancestor::h1)][not(ancestor::h2)][not(ancestor::h3)][not(ancestor::h4)][not(ancestor::h5)][not(ancestor::h6)][not(ancestor::textarea)]';
            }

            /*
             * Parsing of the wp-captions
             */
            $query .= '[not(ancestor::*[contains(concat(\' \', @class, \' \'), \' wp-caption \')])]';

            foreach ($xpath->query($query) as $node) {
                /* @var $node DOMText */
                $replaced = preg_replace_callback($glossarySearchString, array(self::$calledClassName, 'cmtt_replace_matches'), htmlspecialchars($node->wholeText, ENT_COMPAT));
                if (!empty($replaced)) {
                    $newNode = $dom->createDocumentFragment();
                    $replacedShortcodes = strip_shortcodes($replaced);
                    $result = $newNode->appendXML('<![CDATA[' . $replacedShortcodes . ']]>');

                    if ($result !== false) {
                        $node->parentNode->replaceChild($newNode, $node);
                    }
                }
            }

            /*
             *  get only the body tag with its contents, then trim the body tag itself to get only the original content
             */
            $bodyNode = $xpath->query('//body')->item(0);

            if ($bodyNode !== NULL) {
                $newDom = new DOMDocument();
                $newDom->appendChild($newDom->importNode($bodyNode, TRUE));

                $intermalHtml = $newDom->saveHTML();
                $html = mb_substr(trim($intermalHtml), 6, (mb_strlen($intermalHtml) - 14), "UTF-8");
                /*
                 * Fixing the self-closing which is lost due to a bug in DOMDocument->saveHtml() (caused a conflict with NextGen)
                 */
                $html = preg_replace('#(<img[^>]*[^/])>#Ui', '$1/>', $html);
            }
        }

        return $html;
    }

    /**
     * Replaces the matches
     * @global array $replacedTerms
     * @param type $match
     * @return type
     */
    public static function cmtt_replace_matches($match) {
        if (!empty($match[0])) {
            $replacementText = self::cmtt_prepareReplaceTemplate(htmlspecialchars_decode($match[0], ENT_COMPAT));
            return $replacementText;
        }
    }

    /**
     * Function which prepares the templates for the glossary words found in text
     *
     * @param string $title replacement text
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
        global $glossaryIndexArr, $caseSensitive, $templatesArr, $removeLinksToTerms, $replacedTerms;

        /*
         * If TRUE then the links to glossary pages are exchanged with spans
         */
        $removeLinksToTerms = (get_option('cmtt_glossaryTermLink') == 1);

        /*
         * If "Highlight first occurance only" option is set
         */
        $highlightFirstOccuranceOnly = (get_option('cmtt_glossaryFirstOnly') == 1);

        /*
         * If it's case insensitive, then the term keys are stored as lowercased
         */
//		$normalizedTitle = str_replace( '&#039;', "’", preg_quote( htmlspecialchars( trim( $title ), ENT_QUOTES, 'UTF-8' ), '/' ) ); //changed in 3.3.8
        $normalizedTitle = preg_quote(htmlspecialchars(trim($title), ENT_COMPAT, 'UTF-8'), '/');
        $titleIndex = (!$caseSensitive) ? mb_strtolower($normalizedTitle) : $normalizedTitle;

        /*
         * Upgrade to make it work with synonyms
         */
        if ($glossaryIndexArr) {
            /*
             * First - look for exact keys
             */
            if (array_key_exists($titleIndex, $glossaryIndexArr)) {
                $glossary_item = $glossaryIndexArr[$titleIndex];
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
                            $glossary_item = $value;
                            break;
                        }
                    }
                }
            }
        }

        /*
         * Error checking
         */
        if (!is_object($glossary_item)) {
            return 'Error! Post not found for word:' . $titleIndex;
        }

        $id = $glossary_item->ID;

        /**
         *  If "Highlight first occurance only" option is set, we check if the post has already been highlighted
         */
        if ($highlightFirstOccuranceOnly && is_array($replacedTerms) && !empty($replacedTerms)) {
            foreach ($replacedTerms as $replacedTerm) {
                if ($replacedTerm['postID'] == $id) {
                    /*
                     * If the post has already been highlighted
                     */
                    return $title;
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
            $templateReplaced = str_replace($titlePlaceholder, $title, $templatesArr[$id]);
            return $templateReplaced;
        }

        $additionalClass = '';
        $permalink = get_permalink($glossary_item);

        /*
         * Open in new window
         */
        $showTitleAttribute = get_option('cmtt_showTitleAttribute');
        $windowTarget = (get_option('cmtt_glossaryInNewPage') == 1) ? ' target="_blank" ' : '';
        $titleAttr = (get_option('cmtt_showTitleAttribute') == 1) ? ' title="Glossary: ' . esc_attr($glossary_item->post_title) . '" ' : '';

        if (get_option('cmtt_glossaryTooltip') == 1) {
            $glossaryItemContentBase = (get_option('cmtt_glossaryExcerptHover') && $glossary_item->post_excerpt) ? $glossary_item->post_excerpt : $glossary_item->post_content;
            $glossaryItemContent = self::cmtt_glossary_filterTooltipContent($glossaryItemContentBase, get_permalink($glossary_item));

            /*
             * Add filter to change the tooltip content on both Glossary Index and Post/Pages
             */
            $glossaryItemContent = apply_filters('cmtt_tooltip_content_add', $glossaryItemContent, $glossary_item);

            /*
             * Add filter to change the tooltip content on Post/Pages only
             */
            $glossaryItemContent = apply_filters('cmtt_postpages_tooltip_content_add', $glossaryItemContent, $glossary_item);

            if ($removeLinksToTerms) {
                $link_replace = '<span ' . $titleAttr . ' data-cmtooltip="' . $glossaryItemContent . '" class="glossaryLink ' . $additionalClass . '">' . $titlePlaceholder . '</span>';
            } else {
                $link_replace = '<a href="' . $permalink . '"' . $titleAttr . ' data-cmtooltip="' . $glossaryItemContent . '"  class="glossaryLink ' . $additionalClass . '"' . $windowTarget . '>' . $titlePlaceholder . '</a>';
            }
        } else {
            if ($removeLinksToTerms) {
                $link_replace = '<span  ' . $titleAttr . ' class="glossaryLink">' . $titlePlaceholder . '</span>';
            } else {
                $link_replace = '<a href="' . $permalink . '"' . $titleAttr . ' class="glossaryLink"' . $windowTarget . '>' . $titlePlaceholder . '</a>';
            }
        }

        /*
         * Save with $titlePlaceholder - for the synonyms
         */
        $templatesArr[$id] = $link_replace;

        /*
         * Replace it with title to show correctly for the first time
         */
        $link_replace = str_replace($titlePlaceholder, $title, $link_replace);
        return $link_replace;
    }

    /**
     * Filters the tooltip content
     * @param type $glossaryItemContent
     * @param type $glossaryItemPermalink
     * @return type
     */
    public static function cmtt_glossary_filterTooltipContent($glossaryItemContent, $glossaryItemPermalink) {
        $glossaryItemContent = str_replace('[glossary_exclude]', '', $glossaryItemContent);
        $glossaryItemContent = str_replace('[/glossary_exclude]', '', $glossaryItemContent);

        if (get_option('cmtt_glossaryFilterTooltip') == 1) {
            // remove paragraph, bad chars from tooltip text
            $glossaryItemContent = str_replace(array(chr(10), chr(13)), array('', ''), $glossaryItemContent);
            $glossaryItemContent = str_replace(array('</p>', '</ul>', '</li>'), array('<br/>', '<br/>', '<br/>'), $glossaryItemContent);
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<li>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<ul>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<p>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<img>');

            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h1>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h2>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h3>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h4>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h5>');
            $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<h6>');
            if (get_option('cmtt_glossaryFilterTooltipA') != 1) {
                $glossaryItemContent = cminds_strip_only($glossaryItemContent, '<a>');
            }
            $glossaryItemContent = htmlspecialchars($glossaryItemContent);
            $glossaryItemContent = esc_attr($glossaryItemContent);
            $glossaryItemContent = str_replace("color:#000000", "color:#ffffff", $glossaryItemContent);
            $glossaryItemContent = str_replace('\\[glossary_exclude\\]', '', $glossaryItemContent);
        } else {
            $glossaryItemContent = strtr($glossaryItemContent, array("\r\n\r\n" => '<br />', "\r\r" => '<br />', "\n\n" => '<br />'));
        }

        $tooltipLengthLimit = get_option('cmtt_glossaryLimitTooltip');

        if (($tooltipLengthLimit >= 30) && (strlen($glossaryItemContent) > $tooltipLengthLimit)) {
            $glossaryItemContent = cminds_truncate(html_entity_decode($glossaryItemContent), $tooltipLengthLimit, '(...)');
        }

        return esc_attr($glossaryItemContent);
    }

    /**
     * Create the actual glossary
     * @param type $content
     * @return string
     */
    public static function cmtt_glossary_createList($content) {
        $currentPost = get_post();
        $glossaryPageID = get_option('cmtt_glossaryID');
        if (is_numeric($glossaryPageID) && is_page($glossaryPageID) && $glossaryPageID > 0 && $currentPost && $currentPost->ID == $glossaryPageID) {
            $content = self::cmtt_glossaryShowList($content);
            $removeGlossaryIndexFilterAfterOutput = (get_option('cmtt_removeGlossaryCreateListFilter', 0) == 1);
            if ($removeGlossaryIndexFilterAfterOutput) {
                remove_filter('the_content', array(self::$calledClassName, 'cmtt_glossary_createList'), 9998);
            }
        }
        return $content;
    }

    /**
     * Displays the main glossary index
     *
     * @global type $removeLinksToTerms
     * @global boolean $isMainGlossaryPage
     * @global type $glossary_RequiredJSData
     * @param type $content
     * @param type $shortcode
     * @return string $content
     */
    public static function cmtt_glossaryShowList($content = '', $shortcode = false) {
        global $removeLinksToTerms, $isMainGlossaryPage, $glossary_RequiredJSData, $post;

        /*
         * Store the value if it's the main Glossary Index
         */
        $isMainGlossaryPage = true;

        $glossary_list_id = 'glossaryList' . ($shortcode || isset($_POST['isshortcode']) ? '_' . $_POST["gcat_id"] : '');

        $content .= '<div id="' . $glossary_list_id . '-nav" class="listNav" role="tablist"></div>';
        $content .= '<div class="glossary-container">';

        $args = array(
            'post_type' => 'glossary',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => 500
        );

        ob_start();
        ?>
        <div class="glossary_top_filter">
            <?php echo apply_filters('cmtt_glossary_index_additional_filters_html', ''); ?>
        </div>
        <?php
        $content .= ob_get_clean();

        $glossary_query = new WP_Query($args);
        $glossary_index = $glossary_query->get_posts();

        if (empty($glossary_index)) {
            $content .= '<span class="error">Nothing found. Please change search parameters.</span>';
        }

        if ($glossary_index) {
            $glossary_RequiredJSData['list_id'] = $glossary_list_id;

            $content .= '<ul class="glossaryList" role="tabpanel" id="' . $glossary_list_id . '">';

            /*
             * Style links based on option
             */
            $glossary_style = (get_option('cmtt_glossaryDiffLinkClass') == 1) ? 'glossaryLinkMain' : 'glossaryLink';
            if ($removeLinksToTerms) {
                $tag = 'span';
            } else {
                $tag = 'a';
            }

            foreach ($glossary_index as $glossary_item) {
                $glossaryItemContentBase = (get_option('cmtt_glossaryExcerptHover') && $glossary_item->post_excerpt) ? $glossary_item->post_excerpt : $glossary_item->post_content;
                $glossaryItemContent = self::cmtt_glossary_filterTooltipContent($glossaryItemContentBase, get_permalink($glossary_item));

                $glossaryItemDesc = '';

                if ($removeLinksToTerms) {
                    $href = '';
                } else {
                    $href = 'href="' . get_permalink($glossary_item) . '"';
                }

                /*
                 * Add filter to change the tooltip content on both Glossary Index and Post/Pages
                 */
                $glossaryItemContent = apply_filters('cmtt_tooltip_content_add', $glossaryItemContent, $glossary_item);

                /*
                 * Add filter to change the tooltip content only on the Glossary Index page
                 */
                $glossaryItemContent = apply_filters('cmtt_glossary_index_tooltip_content_add', $glossaryItemContent, $glossary_item);

                $preItemTitleContent = '';
                $postItemTitleContent = '';

                $preItemTitleContent .= '<li>';

                /*
                 * Start the internal tag: span or a
                 */
                $preItemTitleContent .= '<' . $tag . ' class="' . $glossary_style . '" ' . $href . ' ';

                /*
                 * Add tooltip if needed (general setting enabled and page not excluded from plugin)
                 */
                $preItemTitleContent .= (get_option('cmtt_glossaryTooltip') == 1) ? 'data-cmtooltip="' . $glossaryItemContent . '"' : '';
                $preItemTitleContent .= '>';

                /*
                 * Add filter to change the content of what's before the glossary item title on the list
                 */
                $preItemTitleContent = apply_filters('cmtt_glossaryPreItemTitleContent_add', $preItemTitleContent, $glossary_item);

                /*
                 * Insert post title here later on
                 */
                $postItemTitleContent .= '</' . $tag . '>';
                /*
                 * Add description if needed
                 */
                $postItemTitleContent .= $glossaryItemDesc;
                $postItemTitleContent .= '</li>';

                $content .= $preItemTitleContent . $glossary_item->post_title . $postItemTitleContent;
            }
            $content .= '</ul>';
        }

        if (get_option('cmtt_glossaryListTiles') == 1) {
            $content = '<div class="tiles">' . $content . '<p class="clear"></p></div>';
        }

        $content .= '</div>';

        $content = apply_filters('cmtt_glossary_index_after_content', $content);

        $authorUrl = do_shortcode('[cminds_free_author id="cmtt"]');
        $content .= (get_option('cmtt_glossaryReferral') == 1 && get_option('cmtt_glossaryAffiliateCode')) ? self::cmtt_getReferralSnippet() : $authorUrl;
        return $content;
    }

    /**
     * Adds the backlink to the term pages
     * @global type $wp_query
     * @global type $post
     * @param type $content
     * @return string
     */
    public static function cmtt_glossary_addBacklink($content = '') {
        global $wp_query;
        $post = $wp_query->post;

        $onMainQueryOnly = (get_option('cmtt_glossaryOnMainQuery') == 1 ) ? is_main_query() : TRUE;

        if (is_single() && get_query_var('post_type') == 'glossary' && $onMainQueryOnly && 'glossary' == get_post_type()) {
            global $post;

            $mainPageId = get_option('cmtt_glossaryID');
            $addBacklink = 0;
            $addBacklinkBottom = get_option('cmtt_glossary_addBackLinkBottom', 0);

            $backlink = ($addBacklink == 1 && $mainPageId > 0) ? '<a href="' . get_permalink($mainPageId) . '" style="display:block;margin:10px 0;">' . __('&laquo; Back to Glossary Index', 'cm-tooltip-glossary') . '</a>' : '';
            $backlinkBottom = ($addBacklinkBottom == 1 && $mainPageId > 0) ? '<a href="' . get_permalink($mainPageId) . '" style="margin:10px 0;">' . __('&laquo; Back to Glossary Index', 'cm-tooltip-glossary') . '</a>' : '';

            $referralSnippet = (get_option('cmtt_glossaryReferral') == 1 && get_option('cmtt_glossaryAffiliateCode')) ? self::cmtt_getReferralSnippet() : '';

            $contentWithoutBacklink = $content;
            $filteredContent = apply_filters('cmtt_add_backlink_content', $contentWithoutBacklink);

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

            $contentWithBacklink = apply_filters('cmtt_glossary_term_after_content', $contentWithBacklink);

            return $contentWithBacklink;
        }

        return $content;
    }

    /**
     * We redirect the query for "glossary" archive to the glossary index page or homepage if it doesn't exist
     * @global type $wp_query
     * @global type $post
     */
    public static function cmtt_templateRedirect() {
        global $wp_query, $post, $wp_the_query;

        $glossaryPageID = get_option('cmtt_glossaryID');
        $glossaryPermalink = get_option('cmtt_glossaryPermalink');

        $otherPageWithSamePermalink = isset($wp_query->queried_object_id) && $wp_query->queried_object_id != $glossaryPageID && isset($wp_query->query['pagename']) && $wp_query->query['pagename'] === $glossaryPermalink;
        $archiveOrHome = (is_archive() || is_home()) && !is_feed() && isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'glossary';

        if ($otherPageWithSamePermalink || $archiveOrHome) {
            $glossaryPageLink = get_page_link($glossaryPageID);
            $glossaryArchivePermalink = trailingslashit(home_url($glossaryPermalink));

            if (is_numeric($glossaryPageID) && $glossaryPageID > 0) {
                if ($glossaryArchivePermalink !== $glossaryPageLink) {
                    wp_redirect($glossaryPageLink);
                    exit();
                }

                query_posts(array(
                    'pagename' => get_option('cmtt_glossaryPermalink'),
                    'page' => ''
                ));

                $wp_query->is_page = true;

                $post = $wp_query->post;
                $wp_the_query = $wp_query;

                add_filter('template_include', 'glossary_index_page_template', 99);

                function glossary_index_page_template($template) {
                    /*
                     * Make 100% sure it only loads once
                     */
                    remove_filter('template_include', 'glossary_index_page_template', 99);

                    global $wp_query;
                    $glossaryPageID = get_option('cmtt_glossaryID');
                    if (is_page() && $wp_query->post->ID == $glossaryPageID) {
                        $new_template = locate_template(array('page.php', 'single.php'));
                        if ('' != $new_template) {
                            return $new_template;
                        }
                    }

                    return $template;
                }

            } else {
                wp_redirect(home_url());
                exit();
            }
        }
    }

    /**
     * Outputs the Affiliate Referral Snippet
     * @return type
     */
    public static function cmtt_getReferralSnippet() {
        ob_start();
        ?>
        <span class="glossary_referral_link">
            <a target="_blank" href="https://www.cminds.com/store/tooltipglossary/?af=<?php echo get_option('cmtt_glossaryAffiliateCode') ?>">
                <img src="https://www.cminds.com/wp-content/uploads/download_tooltip.png" width=122 height=22 alt="Download Tooltip Pro" title="Download Tooltip Pro" />
            </a>
        </span>
        <?php
        $referralSnippet = ob_get_clean();
        return $referralSnippet;
    }

}
