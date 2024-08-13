<?php
if (!defined('ABSPATH')) exit; // if direct access 


$breadcrumb_info = get_option('breadcrumb_info');
//$v1_5_39 = isset($breadcrumb_info['v1_5_39']) ? $breadcrumb_info['v1_5_39'] : 'no';





?>





<div class="wrap">

    <div id="icon-tools" class="icon32"><br></div><?php echo "<h2>" . sprintf(__('%s Data - Update'), breadcrumb_plugin_name) . "</h2>"; ?>

    <?php

    //var_dump($breadcrumb_info);

    $breadcrumb_options = get_option('breadcrumb_options');
    update_option('breadcrumb_options_old', $breadcrumb_options);


    $permalinks = isset($breadcrumb_options['permalinks']) ? $breadcrumb_options['permalinks'] : array();

    $permalinksX = [];

    foreach ($permalinks as $postType => $postTypeData) {

    ?>
        <p>
            <?php
            echo 'Settings updated for ' . $postType;
            ?>
        </p>
    <?php

        $i = 0;
        foreach ($postTypeData as $elementSlug => $elementData) {

            $elementData['elementId'] = $elementSlug;


            $permalinksX[$postType][$i] = $elementData;

            $i++;
        }
    }

    ?>
    <h4>Settings updated successfully, you can close this page now.</h4>
    <?php
    // echo '<pre>' . var_export($permalinksX, true) . '</pre>';

    $breadcrumb_options['permalinks'] = $permalinksX;

    update_option('breadcrumb_options', $breadcrumb_options);


    $breadcrumb_info = [];
    $breadcrumb_info['v1_5_39'] = 'yes';



    update_option('breadcrumb_info', $breadcrumb_info);



    ?>

</div> <!-- end wrap -->