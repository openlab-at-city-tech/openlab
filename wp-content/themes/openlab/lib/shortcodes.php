<?php

/**
 * Custom Shortcodes
 */
function openlab_callout_list_shortcode($atts, $content) {
    global $post;
    $result = array();

    // Attributes
    extract(shortcode_atts(array(), $atts));

    $index = 0;

    $doc = new DOMDocument();
    $doc->loadHTML($content);
    $sxml = simplexml_import_dom($doc);
    $list_items = $sxml->xpath('//li');

    if (count($list_items) > 0) {
        $index = 1;
        $final_content = '<div class="callout-list">';
        foreach ($list_items as $item) {
            $this_item = strip_tags($item, '<b><i><strong><em><a>');
            $final_content .= <<<HTML
                    <div class="row">
                        <div class="col-xs-2 callout-list-number">
                            <span class="semibold">{$index}</span>
                        </div>
                        <div class="col-xs-22 callout-list-content">
                            {$this_item}
                        </div>
                    </div>
HTML;
            $index++;
        }
        $final_count .= '</div>';
    } else {
        $final_content = $content;
    }

    return $final_content;
}

add_shortcode('callout', 'openlab_callout_list_shortcode');

