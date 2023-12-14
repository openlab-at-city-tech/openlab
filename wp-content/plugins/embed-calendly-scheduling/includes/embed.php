<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Embed
{
    private $atts;
    private $url;

    public function __construct($atts)
    {
        $this->atts = $atts;
        $this->url = $atts['url'];
        $url_parts = [];

        if (isset($atts['url'])) {

            if (isset($atts['cookie_banner'])) {

                if (!empty($atts['cookie_banner'])) {

                    $url_parts[] = 'hide_gdpr_banner=1';
                }
            }

            if (isset($atts['hide_details'])) {

                if (!empty($atts['hide_details'])) {

                    $url_parts[] = 'hide_event_type_details=1';
                }
            }
        }

        if (!empty($url_parts)) {
            $this->url = $this->prepare_embed_url($this->url, $url_parts);
        }

        if (!defined('EMCS_BUTTON_EMBED_TYPE')) {
            define('EMCS_BUTTON_EMBED_TYPE', 2);
        }

        if (!defined('EMCS_POPUP_TEXT_EMBED_TYPE')) {
            define('EMCS_POPUP_TEXT_EMBED_TYPE', 3);
        }
    }

    public function embed_calendar()
    {

        if (!empty($this->atts)) {

            do_action('emcp_before_calendar_embed', $this->url);

            $sanitized_atts = $this->clean_shortcode_atts($this->atts);

            if ($sanitized_atts) {

                switch ($sanitized_atts['embed_type']) {
                    case EMCS_BUTTON_EMBED_TYPE:

                        if ($sanitized_atts['button_style'] == 1) {
                            return $this->embed_inline_button_widget($sanitized_atts);
                        } else {
                            return $this->embed_popup_button_widget($sanitized_atts);
                        }

                    case EMCS_POPUP_TEXT_EMBED_TYPE:
                        return $this->embed_popup_text_widget($sanitized_atts);

                    default:
                        return $this->embed_inline_widget($sanitized_atts);
                }
            }
        }
    }

    /**
     * Clean shortcode attributes and properly escape them
     * 
     * @param array atts Array of shortcode options
     * @return array Cleaned attributes
     */
    private function clean_shortcode_atts($atts)
    {
        $sanitized_atts = [];

        if ($atts) {
            foreach ($atts as $att_key => $att_value) {
                $sanitized_atts[$att_key] = esc_html($att_value);
            }
        }

        return $sanitized_atts;
    }

    /**
     * Embeds calendly inline widget
     * 
     * @param array atts Array of shortcode options
     * @return string HTML code for inline embed widget
     */
    private function embed_inline_widget($atts = array())
    {
        return '<div id="calendly-inline-widget" data-url="' . esc_attr($this->url) . '" class="calendly-inline-widget ' . esc_attr($atts['style_class']) . '" data-url="' . esc_url($this->url) . '"
                     style="height:' . esc_attr($atts['form_height']) . '; min-width:' . esc_attr($atts['form_width']) . '"></div>';
    }

    /**
     * Embeds calendly popup text widget
     * 
     * @param array atts Array of shortcode options
     * @return string HTML code for popup text embed widget
     */
    private function embed_popup_text_widget($atts = array())
    {
        return '<a id="calendly-popup-text-widget" data-url="' . esc_attr($this->url) . '" class="' . esc_attr($atts['style_class']) . '" href="" onclick="Calendly.initPopupWidget({url: \'' . esc_url($this->url) . '\'});return false;"
                   style="font-size:' . esc_attr($atts['text_size']) . '; color:' . esc_attr($atts['text_color']) . '">' . esc_attr($atts['text']) . '</a>';
    }

    /**
     * Embeds calendly inline button widget
     * 
     * @param array atts Array of shortcode options
     * @return string HTML code for inline button embed widget
     */
    private function embed_inline_button_widget($atts = array())
    {
        $button_padding = '';

        switch ($atts['button_size']) {
            case 1:
                // small size inline button
                $button_padding = apply_filters('emcs_small_inline_button', '10px');
                break;
            case 2:
                // medium size inline button
                $button_padding = apply_filters('emcs_medium_inline_button', '15px');
                break;
            default:
                // large size inline button
                $button_padding = apply_filters('emcs_large_inline_button', '20px');
        }

        return '<a id="calendly-inline-button-widget" data-url="' . esc_attr($this->url) . '" class="' . esc_attr($atts['style_class']) . '" href="" onclick="Calendly.initPopupWidget({url: \'' . esc_url($this->url) . '\'});return false;"
                   style="background-color: ' . $atts['button_color'] . '; padding: ' . $button_padding . '; font-size:' . esc_attr($atts['text_size']) . '; 
                   color:' . esc_attr($atts['text_color']) . ';">' . esc_attr($atts['text']) . '</a>';
    }

    /**
     * Embeds calendly popup button widget
     * 
     * @param array atts Array of shortcode options
     * @return string Script for popup button embed widget
     */
    private function embed_popup_button_widget($atts = array())
    {
        return $this->popup_script($atts);
    }

    private function popup_script($atts)
    {
        return '<div id="calendly-popup-button-widget" data-url="' . esc_attr($this->url) . '" style="display: none"><script>window.onload = function() { Calendly.initBadgeWidget({ url: \'' . $this->url . '\', text: \'' . $atts['text'] . '\', 
                color: \'' . $atts['button_color'] . '\', textColor: \'' . $atts['text_color'] . '\', 
                branding: ' . $atts['branding'] . ' });}</script></div>';
    }

    private function prepare_embed_url($url, $url_parts = [])
    {

        if (empty($url) || empty($url_parts)) return;

        $url .= '?' . implode('&', $url_parts);

        return $url;
    }
}
