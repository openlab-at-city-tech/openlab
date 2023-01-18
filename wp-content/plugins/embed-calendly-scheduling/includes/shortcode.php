<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

include_once(EMCS_DIR . 'includes/embed.php');

class EMCS_Shortcode
{
    public static function register_shortcode($atts) {
        return self::load_view($atts);
    }

    public static function load_view($atts)
    {
    
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        if (empty($atts) || empty($atts['url'])) {
            return 'Error embedding calendar. Invalid URL';
        }

        // enqueue style and script on demand
        wp_enqueue_style('emcs_calendly_css');
        wp_enqueue_script('emcs_calendly_js');
        
        $atts = self::prepare_attributes($atts);
        $emcs_embed = new EMCS_Embed($atts);

        return $emcs_embed->embed_calendar();
    }

    private static function prepare_attributes($atts)
    {
        $embed_type = (!empty($atts['type'])) ? sanitize_text_field($atts['type']) : '1';
        $text = (!empty($atts['text'])) ? sanitize_text_field($atts['text']) : 'Schedule a call with me';
        $text_color = (!empty($atts['text_color'])) ? sanitize_text_field($atts['text_color']) : '#000000';
        $text_size = (!empty($atts['text_size'])) ? sanitize_text_field($atts['text_size']) . 'px' : '12px';
        $form_height = (!empty($atts['form_height'])) ? sanitize_text_field($atts['form_height']) . 'px' : '400px';
        $form_width = (!empty($atts['form_width'])) ? sanitize_text_field($atts['form_width']) . 'px' : '600px';
        $button_color = (!empty($atts['button_color'])) ? sanitize_text_field($atts['button_color']) : '#00a2ff';
        $button_style = (!empty($atts['button_style'])) ? sanitize_text_field($atts['button_style']) : '1';
        $button_size = (!empty($atts['button_size'])) ? sanitize_text_field($atts['button_size']) : '1';
        $class = (!empty($atts['style_class'])) ? sanitize_text_field($atts['style_class']) : '';
        $branding = (!empty($atts['branding'])) ? sanitize_text_field($atts['branding']) : 'false';
        $hide_details = (!empty($atts['hide_details'])) ? sanitize_text_field($atts['hide_details']) : '0';
        $cookie_banner = (!empty($atts['hide_cookie_banner'])) ? sanitize_text_field($atts['hide_cookie_banner']) : '0';
        $url = esc_url_raw($atts['url']);

        return [
            'url'           => $url, 
            'embed_type'    => $embed_type, 
            'text'          => $text, 
            'text_color'    => $text_color, 
            'text_size'     => $text_size, 
            'form_height'   => $form_height,
            'form_width'    => $form_width,
            'button_color'  => $button_color, 
            'button_style'  => $button_style,
            'button_size'   => $button_size,  
            'style_class'   => $class, 
            'branding'      => $branding,
            'hide_details'  => $hide_details,
            'cookie_banner' => (int) $cookie_banner
        ];
    }
}