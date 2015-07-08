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
    $doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
    $domx = new DOMXPath( $doc );
    $list_items = $domx->evaluate( '//li' );
    $list_items_html = array();
    foreach ( $list_items as $list_item ) {
	    $list_items_html[] = $list_item->ownerDocument->saveHTML( $list_item );
    }

    if ( count( $list_items_html ) > 0 ) {
        $index = 1;
        $final_content = '<div class="callout-list">';
        foreach ( $list_items_html as $this_item ) {
	    preg_match( '|<li[^>]*>(.*)</li>|s', $this_item, $matches );
	    $item_content = $matches[1];
            $final_content .= <<<HTML
                    <div class="row">
                        <div class="col-xs-2 callout-list-number">
                            <span class="semibold">{$index}</span>
                        </div>
                        <div class="col-xs-22 callout-list-content">
                            <div class="item-wrapper">
                                {$item_content}
                            </div>
                        </div>
                    </div>
HTML;
            $index++;
        }
        $final_content .= '</div>';
    } else {
        $final_content = $content;
    }

    return $final_content;
}

add_shortcode('callout', 'openlab_callout_list_shortcode');

