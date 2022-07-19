<?php

class CMTT_AMP {

    public static function init() {
        /*
         * Add custom CSS for "AMP for WP" plugin
         */
        add_action('amp_post_template_css', [__CLASS__, 'cminds_amp_custom_css'], 11);
        add_filter('cmtt_pre_item_description_content', array(__CLASS__, 'outputSimpleTooltipContent'), 50, 3);
        add_filter('amp_content_sanitizers', array(__CLASS__, 'disableBlackListSanitizer'), 100);
        add_filter('cmtt_dom_str_replace_before', array(__CLASS__, 'convertBindAttributes'));
        add_filter('cmtt_link_replace', array(__CLASS__, 'convertLinkReplace'), 10, 5);

        add_filter('cmtt_all_letter_additional_attributes', array(__CLASS__, 'allAdditionalAttributes'), 10, 3);
        add_filter('cmtt_index_all_label', array(__CLASS__, 'allLabel'), 10, 3);

        add_filter('cmtt_num_letter_additional_attributes', array(__CLASS__, 'numAdditionalAttributes'), 10, 4);
        add_filter('cmtt_index_num_label', array(__CLASS__, 'numLabel'), 10, 3);

        add_filter('cmtt_letter_letter_additional_attributes', array(__CLASS__, 'letterAdditionalAttributes'), 10, 7);
        add_filter('cmtt_index_letter_label', array(__CLASS__, 'letterLabel'), 10, 3);

        add_filter('cmtt_index_glossary_page_link', array(__CLASS__, 'replaceGlossarylink'));
    }

    public static function replaceGlossarylink($html) {
        if (CMTT_AMP::is_amp_endpoint()) {
            $html = "#";
        }
        return $html;
    }

    public static function allAdditionalAttributes($html, $selectedClass, $postsCount) {
        $html .= self::add_amp_attributes('all', 'ln-all ln-serv-letter' . $selectedClass, $postsCount);
        return $html;
    }

    public static function allLabel($html, $postsCount, $showCounts) {
        /*
         * Prepend count to the current label
         */
        $html = self::add_amp_letter_counts($postsCount, $showCounts) . $html;
        return $html;
    }

    public static function numAdditionalAttributes($html, $disabledClass, $selectedClass, $postsCount) {
        $html .= self::add_amp_attributes('num', 'ln-_ ln-serv-letter' . $disabledClass . $selectedClass, $postsCount);
        return $html;
    }

    public static function numLabel($html, $postsCount, $showCounts) {
        /*
         * Prepend count to the current label
         */
        $html = self::add_amp_letter_counts($postsCount, $showCounts) . $html;
        return $html;
    }

    public static function letterAdditionalAttributes($html, $key, $letter, $lastClass, $disabledClass, $selectedClass, $postsCount) {
        $html .= self::add_amp_attributes($key, 'lnletter-' . $letter . ' ln-serv-letter' . $lastClass . $disabledClass . $selectedClass, $postsCount);
        return $html;
    }

    public static function letterLabel($html, $postsCount, $showCounts) {
        /*
         * Prepend count to the current label
         */
        $html = self::add_amp_letter_counts($postsCount, $showCounts) . $html;
        return $html;
    }

    /**
     * Adds AMP attributes for AMP version of the plugin
     * @param string $letter
     * @param string $prevClasses
     * @param int $count
     * @return string
     */
    public static function add_amp_attributes($letter, $prevClasses, $count = 0) {
        if (!CMTT_AMP::is_amp_endpoint() || $count == 0) {
            return '';
        }

        $ampAttr = ' [class]="cmindsState.visibleLetter==\'ln-' . $letter . ' ' . $prevClasses . '\'? \'ln-selected\':\'' . $prevClasses . '\'"'
                . ' on="tap:AMP.setState({ cmindsState: {visibleLetter: \'ln-' . $letter . '\'} })"';

        return $ampAttr;
    }

    /**
     * Adds letter counts for AMP version of the plugin
     * @param int $num
     * @param boolean $showCounts
     * @return string
     */
    public static function add_amp_letter_counts($num, $showCounts = true) {
        if (!CMTT_AMP::is_amp_endpoint() || !$showCounts) {
            return '';
        }

        $countElement = '<span class="ln-letter-count-amp">' . $num . '</span>';

        return $countElement;
    }

    public static function convertLinkReplace($link_replace, $titleAttr, $glossary_item, $additionalClass, $titlePlaceholder) {
        if (CMTT_AMP::is_amp_endpoint()) {
            $id = rand(0, 100);
            $titleAttr .= ' on="tap:AMP.setState({ cmindsState: {visibleTooltip: \'tooltip-' . $glossary_item->ID . $id . '\'} })"';
            $link_replace = '<span role="button" tabindex="0" aria-describedby="tt" class="glossaryLink ampGlossaryLink' . $additionalClass . '" '
                    . ' ' . $titleAttr . '>'
                    . apply_filters('cmtt_pre_item_description_content', $titlePlaceholder, $glossary_item, $id, '')
                    . '</span>';
        }
        return $link_replace;
    }

    public static function convertBindAttributes($html) {
        // As saveHTML() method removes attributes in brackets, like [class] and [hidden], we need to convert them to appropriate format
        if (CMTT_AMP::is_amp_endpoint()) {
            $html = self::convert_amp_bind_attributes($html);
        }
        return $html;
    }

    // Function from AMP plugin v1.4.4 AMP_DOM_Utils::convert_amp_bind_attributes()
    public static function convert_amp_bind_attributes($html) {

        // Pattern for HTML attribute accounting for binding attr name, boolean attribute, single/double-quoted attribute value, and unquoted attribute values.
        $attr_regex = '#^\s+(?P<name>\[?[a-zA-Z0-9_\-]+\]?)(?P<value>=(?:"[^"]*+"|\'[^\']*+\'|[^\'"\s]+))?#';

        /**
         * Replace callback.
         *
         * @param array $tag_matches Tag matches.
         * @return string Replacement.
         */
        $replace_callback = static function( $tag_matches ) use ( $attr_regex ) {

            // Strip the self-closing slash as long as it is not an attribute value, like for the href attribute (<a href=/>).
            $old_attrs = preg_replace('#(?<!=)/$#', '', $tag_matches['attrs']);

            $old_attrs = rtrim($old_attrs);

            $new_attrs = '';
            $offset = 0;
            while (preg_match($attr_regex, substr($old_attrs, $offset), $attr_matches)) {
                $offset += strlen($attr_matches[0]);

                if ('[' === $attr_matches['name'][0]) {
                    $new_attrs .= ' ' . 'data-amp-bind-' . trim($attr_matches['name'], '[]');
                    if (isset($attr_matches['value'])) {
                        $new_attrs .= $attr_matches['value'];
                    }
                } else {
                    $new_attrs .= $attr_matches[0];
                }
            }

            // Bail on parse error which occurs when the regex isn't able to consume the entire $new_attrs string.
            if (strlen($old_attrs) !== $offset) {
                return $tag_matches[0];
            }

            return '<' . $tag_matches['name'] . $new_attrs . '>';
        };

        // Match all start tags that contain a binding attribute.
        $pattern = implode(
                '',
                [
                    '#<',
                    '(?P<name>[a-zA-Z0-9_\-]+)', // Tag name.
                    '(?P<attrs>\s', // Attributes.
                    '(?:[^>"\'\[\]]+|"[^"]*+"|\'[^\']*+\')*+', // Non-binding attributes tokens.
                    '\[[a-zA-Z0-9_\-]+\]', // One binding attribute key.
                    '(?:[^>"\']+|"[^"]*+"|\'[^\']*+\')*+', // Any attribute tokens, including binding ones.
                    ')>#s',
                ]
        );
        $converted = preg_replace_callback(
                $pattern,
                $replace_callback,
                $html
        );

        /**
         * If the regex engine incurred an error during processing, for example exceeding the backtrack
         * limit, $converted will be null. In this case we return the originally passed document to allow
         * DOMDocument to attempt to load it.  If the AMP HTML doesn't make use of amp-bind or similar
         * attributes, then everything should still work.
         *
         * See https://github.com/ampproject/amp-wp/issues/993 for additional context on this issue.
         * See http://php.net/manual/en/pcre.constants.php for additional info on PCRE errors.
         */
        return ( null !== $converted ) ? $converted : $html;
    }

    /**
     * Outputs the tooltip content
     * @param
     * @return string $tooltip
     */
    public static function outputSimpleTooltipContent($postItemTitleContent, $glossaryItem, $id) {
        $tooltip = '';

        if (CMTT_AMP::is_amp_endpoint()) {
            $ttcont_styles = '';
            $tt_styles = '';
            $title_styles = '';
            $body_styles = '';
            $addLink = \CM\CMTT_Settings::get('cmtt_glossaryAddTermPagelink');
            $createGlossaryTermPages = (bool) \CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', true);
            $target = \CM\CMTT_Settings::get('cmtt_glossaryTermPageLinkTargetBlank', false) ? 'target=&quot; _blank &quot;' : '';
            $text = __(\CM\CMTT_Settings::get('cmtt_glossaryTermDetailsLink'), 'cm-tooltip-glossary');
            $permalink = apply_filters('cmtt_term_tooltip_permalink', get_permalink($glossaryItem), $glossaryItem->ID);

            $tt_styles .= 'width: 300px;';
            $tt_styles .= 'opacity:' . (int) \CM\CMTT_Settings::get('cmtt_tooltipOpacity', 100) / 100 . ';';

            $ttcont_styles .= 'border:' . \CM\CMTT_Settings::get('cmtt_tooltipBorderWidth') . 'px '
                    . \CM\CMTT_Settings::get('cmtt_tooltipBorderStyle')
                    . \CM\CMTT_Settings::get('cmtt_tooltipBorderColor') . ';';
            $ttcont_styles .= 'color:' . \CM\CMTT_Settings::get('cmtt_tooltipForeground') . ';';
            $ttcont_styles .= 'padding:' . \CM\CMTT_Settings::get('cmtt_tooltipPadding') . ';';
            $ttcont_styles .= 'border-radius:' . \CM\CMTT_Settings::get('cmtt_tooltipBorderRadius') . 'px;';
            $ttcont_styles .= 'background-color:' . \CM\CMTT_Settings::get('cmtt_tooltipBackground', '#000000 ') . ';';
            $ttcont_styles .= 'font-size:' . \CM\CMTT_Settings::get('cmtt_tooltipFontSize', null) . 'px;';

            $title_styles .= 'font-size:' . \CM\CMTT_Settings::get('cmtt_tooltipTitleFontSize', '14') . 'px;';
            $title_styles .= 'background-color:' . \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_background') . ';';
            $title_styles .= 'padding:' . \CM\CMTT_Settings::get('cmtt_tooltipPaddingTitle', '0') . ';';
            $title_styles .= 'color:' . \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_text', '#000000 ') . ';';

            $body_styles .= 'font-size:' . \CM\CMTT_Settings::get('cmtt_tooltipFontSize', '14') . 'px;';
            $body_styles .= 'padding:' . \CM\CMTT_Settings::get('cmtt_tooltipPaddingContent', '0') . ';';

            $glossaryItemContent = strip_tags(CMTT_Free::getTheTooltipContentBase('', $glossaryItem));
            if (( \CM\CMTT_Settings::get('cmtt_createGlossaryTermPages', true) && \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip') >= 30 ) && ( strlen($glossaryItemContent) > \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip') )) {
                $glossaryItemContent = cminds_truncate(
                        preg_replace('/<!--(.|\s)*?-->/', '', html_entity_decode($glossaryItemContent)), \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltip'), \CM\CMTT_Settings::get('cmtt_glossaryLimitTooltipSymbol', '(...)'), false, true);
            }
            ob_start();
            ?>
            <span id="tt"
                  role="tooltip"
                  class="amp-tooltip tooltip-<?php echo $glossaryItem->ID . $id; ?>"
                  aria-hidden="true"
                  hidden [hidden]="cmindsState.visibleTooltip!='tooltip-<?php echo $glossaryItem->ID . $id; ?>'"
                  style="<?php echo $tt_styles; ?>">
                  <span id="tttop">
                    <span id="tt-btn-close"
                          class="dashicons dashicons-no"
                          aria-label="Close the tooltip"
                          on="tap:AMP.setState({ cmindsState: {visibleTooltip: ''}})">
            <?php echo (function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint()) ? "&#215;" : ""; ?>
                    </span>
                </span>
                <span id="ttcont" style="<?php echo $ttcont_styles; ?>">
                    <?php
                    $showTitle = \CM\CMTT_Settings::get('cmtt_glossaryAddTermTitle');
                    if ($showTitle == 1):
                        ?>
                        <span class="glossaryItemTitle" style="<?php echo $title_styles; ?>">
                        <?php echo $glossaryItem->post_title; ?>
                        </span><br>
                        <?php endif; ?>
                    <span class="glossaryItemBody" style="<?php echo $body_styles; ?>">
                    <?php echo $glossaryItemContent; ?>
                    </span><br>
            <?php if ($addLink && $createGlossaryTermPages): ?>
                        <span class="glossaryTooltipMoreLinkWrapper">
                            <a class="glossaryTooltipMoreLink" href="<?php echo $permalink; ?>" <?php echo $target; ?>>
                <?php echo $text; ?>
                            </a>
                        </span>
            <?php endif; ?>
                </span>
            </span>
            <?php
            $tooltip .= ob_get_clean();
        }

        $postItemTitleContent .= $tooltip;
        return $postItemTitleContent;
    }

    public static function disableBlackListSanitizer($data) {
        if (isset($data['AMP_Blacklist_Sanitizer'])) {
            unset($data['AMP_Blacklist_Sanitizer']);
        }
        return $data;
    }

    /**
     * Whether this is an AMP endpoint.
     *
     * @see https://github.com/Automattic/amp-wp/blob/e4472bfa5c304b6c1b968e533819e3fa96579ad4/includes/amp-helper-functions.php#L248
     * @return boolean
     */
    public static function is_amp_endpoint() {
        $turn_on_amp = (bool) \CM\CMTT_Settings::get('cmtt_glossaryTurnOnAmp', 0);
        $amp1 = function_exists('is_amp_endpoint') && is_amp_endpoint();
        $amp2 = function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint();
        return $turn_on_amp && ($amp1 || $amp2);
    }

    public static function amp_custom_css() {
        echo file_get_contents(CMTT_PLUGIN_DIR . 'assets/css/tooltip.min.css');
        echo file_get_contents(CMTT_PLUGIN_DIR . 'assets/css/jquery.listnav.min.css');

        $tt_styles = '';
        $tt_styles .= 'min-width:' . (int) \CM\CMTT_Settings::get('cmtt_tooltipWidthMin', 200) . 'px;';
        $tt_styles .= 'max-width:' . (int) \CM\CMTT_Settings::get('cmtt_tooltipWidthMax', 400) . 'px;';
        $tt_styles .= 'opacity:' . (int) \CM\CMTT_Settings::get('cmtt_tooltipOpacity', 100) / 100 . ';';

        $ttcont_styles = '';
        $ttcont_styles .= 'border:' . \CM\CMTT_Settings::get('cmtt_tooltipBorderWidth') . 'px ' . \CM\CMTT_Settings::get('cmtt_tooltipBorderStyle') .
                \CM\CMTT_Settings::get('cmtt_tooltipBorderColor') . ';';
        $ttcont_styles .= 'color:' . \CM\CMTT_Settings::get('cmtt_tooltipForeground') . ';';
        $ttcont_styles .= 'padding:' . \CM\CMTT_Settings::get('cmtt_tooltipPadding') . ';';
        $ttcont_styles .= 'border-radius:' . \CM\CMTT_Settings::get('cmtt_tooltipBorderRadius') . 'px;';
        $ttcont_styles .= 'background-color:' . \CM\CMTT_Settings::get('cmtt_tooltipBackground', '#000000 ') . ';';
        $ttcont_styles .= 'font-size:' . \CM\CMTT_Settings::get('cmtt_tooltipFontSize', null) . 'px;';
        ?>
        #ttcont {
        <?php echo $ttcont_styles; ?>
        }
        #tt.amp-tooltip {
        <?php echo $tt_styles; ?>
        }

        <?php
        $titleFontSize = \CM\CMTT_Settings::get('cmtt_tooltipTitleFontSize', null);
        $titleBGColor = \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_background');
        $titlePadding = \CM\CMTT_Settings::get('cmtt_tooltipPaddingTitle', '0');
        $titleColor = \CM\CMTT_Settings::get('cmtt_tooltipTitleColor_text', '#000000 ');

        $iconSize = \CM\CMTT_Settings::get('cmtt_tooltipCloseSize', 14);
        $iconColor = \CM\CMTT_Settings::get('cmtt_tooltipCloseColor', '#222');

        $fontSize = \CM\CMTT_Settings::get('cmtt_tooltipFontSize', null);
        $bodyPadding = \CM\CMTT_Settings::get('cmtt_tooltipPaddingContent', '0');
        ?>
        #tt .glossaryItemTitle {
        margin: 10px 0;
        <?php if (!empty($titleColor)) : ?>
            color: <?php echo $titleColor; ?>;
        <?php endif; ?>
        <?php if (!empty($titlePadding)) : ?>
            padding: <?php echo $titlePadding; ?>;
        <?php endif; ?>
        <?php if (!empty($titleBGColor)): ?>
            background-color: <?php echo $titleBGColor; ?>;
        <?php endif; ?>
        <?php if (!empty($titleFontSize)): ?>
            font-size: <?php echo $titleFontSize; ?>px;
        <?php endif; ?>
        }

        #tt #tt-btn-close {
        <?php if (!empty($iconSize)): ?>
            font-size: <?php echo $iconSize; ?>px;
        <?php endif; ?>
        <?php if (!empty($iconColor)): ?>
            color: <?php echo $iconColor; ?>;
        <?php endif; ?>
        }

        #tt #ttcont .glossaryItemBody {
        <?php if (!empty($bodyPadding)) : ?>
            padding: <?php echo $bodyPadding; ?>;
        <?php endif; ?>
        <?php if (!empty($fontSize)) : ?>
            font-size: <?php echo $fontSize; ?>px;
        <?php endif; ?>
        }
        <?php
    }

}
