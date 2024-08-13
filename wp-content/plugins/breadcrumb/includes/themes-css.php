<?php



if ( ! defined('ABSPATH')) exit;  // if direct access 


	
	
	
function breadcrumb_themes_css($theme){

    $breadcrumb_themes_css = array();
    $breadcrumb_bg_color = get_option('breadcrumb_bg_color','#278df4');

    ob_start();
    ?>
    <style type="text/css">
        .breadcrumb-container.theme1 li {
            margin: 0;
            padding: 0;
        }
        .breadcrumb-container.theme1 a {
            background: <?php echo esc_attr($breadcrumb_bg_color); ?>;
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
        }
    </style>
    <?php

    $breadcrumb_themes_css['theme1'] = ob_get_clean();


    ob_start();

    ?>
    <style type="text/css">
        .breadcrumb-container.theme2 li {
            margin: 0;
            padding: 0;
        }
        .breadcrumb-container.theme2 a {
            background: <?php echo esc_attr($breadcrumb_bg_color); ?>;
            border-bottom: 1px solid rgb(139, 139, 139);
            border-top: 1px solid rgba(255, 255, 255, 0);
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
        }
    </style>
    <?php

    $breadcrumb_themes_css['theme2'] = ob_get_clean();



    ob_start();

    ?>
    <style type="text/css">

        .breadcrumb-container.theme3 li {
            margin: 0;
            padding: 0;
        }


        .breadcrumb-container.theme3 a {
            background: <?php echo esc_attr($breadcrumb_bg_color); ?>;
            border-top: 1px solid rgb(139, 139, 139);
            border-bottom: 1px solid rgba(355, 355, 355, 0);
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
        }
    </style>
    <?php

    $breadcrumb_themes_css['theme3'] = ob_get_clean();


    ob_start();

    ?>
    <style type="text/css">

        .breadcrumb-container.theme4 li {
            display: inline-block;
            margin: 0 14px;
            padding: 0;
        }

        .breadcrumb-container.theme4 a {
            background: <?php echo esc_attr($breadcrumb_bg_color); ?>;
            color: rgb(102, 102, 102);
            display: inline-block;
            font-size: 14px;
            height: 16px;
            margin: 0;
            padding: 5px 10px;
            text-decoration: none;
            position:relative;
        }


        .breadcrumb-container.theme4 a::after {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            border-color: rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) <?php echo esc_attr($breadcrumb_bg_color); ?>;
            border-image: none;
            border-style: solid;
            border-width: 13px;
            content: " ";
            display: inline-block;
            height: 0;
            line-height: 0;
            position: absolute;
            right: -26px;
            top: 0;
            width: 0;
        }

        .breadcrumb-container.theme4 .separator {
            display: none;
        }
    </style>
    <?php

    $breadcrumb_themes_css['theme4'] = ob_get_clean();



    ob_start();

    ?>
    <style type="text/css">
        .breadcrumb-container.theme5 li {
            display: inline-block;
            margin: 0 14px;
            padding: 0;
        }

        .breadcrumb-container.theme5 a {
            background: <?php echo esc_attr($breadcrumb_bg_color); ?>;
            color: rgb(102, 102, 102);
            display: inline-block;
            font-size: 14px;
            height: 16px;
            margin: 0;
            padding: 5px 10px;
            text-decoration: none;
            position:relative;
        }

        .breadcrumb-container.theme5 a::before {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            border-color: <?php echo esc_attr($breadcrumb_bg_color); ?> <?php echo esc_attr($breadcrumb_bg_color); ?> <?php echo esc_attr($breadcrumb_bg_color); ?> rgba(0, 0, 0, 0);
            border-image: none;
            border-style: solid;
            border-width: 13px;
            content: " ";
            display: block;
            height: 0;
            left: -18px;
            position: absolute;
            top: 0;
            width: 0;
        }
        .breadcrumb-container.theme5 a::after {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            border-color: rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) <?php echo esc_attr($breadcrumb_bg_color); ?>;
            border-image: none;
            border-style: solid;
            border-width: 13px;
            content: " ";
            display: inline-block;
            height: 0;
            line-height: 0;
            position: absolute;
            right: -26px;
            top: 0;
            width: 0;
        }

        .breadcrumb-container.theme5 .separator {
            display: none;
        }

    </style>
    <?php

    $breadcrumb_themes_css['theme5'] = ob_get_clean();

    $breadcrumb_themes_css = apply_filters('breadcrumb_themes_css', $breadcrumb_themes_css);


    return isset($breadcrumb_themes_css[$theme]) ? $breadcrumb_themes_css[$theme] : '';
						
				
				

}
	
	
	
	