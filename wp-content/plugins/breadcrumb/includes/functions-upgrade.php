<?php
if (!defined('ABSPATH')) exit;  // if direct access


add_shortcode('breadcrumb_update_elements', 'breadcrumb_update_elements');

function breadcrumb_update_elements()
{

    $breadcrumb_options = get_option('breadcrumb_options');


    $permalinks = isset($breadcrumb_options['permalinks']) ? $breadcrumb_options['permalinks'] : array();

    $permalinksX = [];

    foreach ($permalinks as $postType => $postTypeData) {

        $i = 0;
        foreach ($postTypeData as $elementSlug => $elementData) {

            $elementData['elementId'] = $elementSlug;


            $permalinksX[$postType][$i] = $elementData;

            $i++;
        }
    }


    // echo '<pre>' . var_export($permalinksX, true) . '</pre>';

    $breadcrumb_options['permalinks'] = $permalinksX;

    update_option('breadcrumb_options', $breadcrumb_options);
}
