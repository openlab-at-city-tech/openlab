<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


class class_breadcrumb_shortcodes  {
	
	
    public function __construct(){

		add_shortcode( 'breadcrumb', array( $this, 'breadcrumb_display' ) );



    }


    public function breadcrumb_display($atts, $content = null ){

        $atts = shortcode_atts(
            array(
                'themes' => '',

            ), $atts);

        $html = '';

        $themes = isset($atts['themes']) ? sanitize_text_field($atts['themes']) : '';
        $breadcrumb_themes = get_option( 'breadcrumb_themes', 'theme5' );

        $breadcrumb_themes = !empty($themes) ? $themes : $breadcrumb_themes;

        include_once( breadcrumb_plugin_dir . 'templates/breadcrumb/breadcrumb-hook.php');


        ob_start();

        ?>
        <div class="breadcrumb-container <?php echo esc_attr($breadcrumb_themes); ?>">
            <?php

            do_action('breadcrumb_main', $atts);

            ?>
        </div>
        <?php
        //wp_enqueue_style( 'font-awesome-5' );


        return ob_get_clean();

    }
	


}


new class_breadcrumb_shortcodes();