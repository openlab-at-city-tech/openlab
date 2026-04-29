<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Embed
{
    private $atts;
    private $url;
    private $redirection_url;

    public function __construct($atts)
    {
        $this->atts = $atts;
        $this->url = isset($atts['url']) ? esc_url_raw($atts['url']) : '';
        $this->redirection_url = isset($atts['redirection_url']) ? esc_url_raw($atts['redirection_url']) : '';

        // prefill user fields always
        if (!empty($atts['prefill_fields'])) {
            $this->url = $this->prefill_fields($this->url);
        }

        $this->url = $this->sanitize_calendar_url($this->url);

        // handle GDPR & hide details
        $url_parts = [];

        if (!empty($atts['cookie_banner'])) {
            $url_parts[] = 'hide_gdpr_banner=1';
        }

        if (!empty($atts['hide_details'])) {
            $url_parts[] = 'hide_event_type_details=1';
        }

        if (!empty($url_parts)) {
            $this->url = $this->prepare_embed_url($this->url, $url_parts);
        }

        // allow Pro version to add extra params via hook
        $this->url = apply_filters('emcs_embed_final_url', $this->url, $atts);

        // define embed type constants if not defined
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

            do_action('emcs_before_calendar_embed', $this->url);

            $sanitized_atts = $this->atts;

            if ($sanitized_atts) {
                switch ($sanitized_atts['embed_type']) {
                    case EMCS_BUTTON_EMBED_TYPE:
                        return ($sanitized_atts['button_style'] == 1)
                            ? $this->embed_inline_button_widget($sanitized_atts)
                            : $this->embed_popup_button_widget($sanitized_atts);
                    case EMCS_POPUP_TEXT_EMBED_TYPE:
                        return $this->embed_popup_text_widget($sanitized_atts);
                    default:
                        return $this->embed_inline_widget($sanitized_atts);
                }
            }
        }
    }

    private function sanitize_calendar_url($url)
    {
        if (empty($url)) return $url;

        $parsed = wp_parse_url($url);

        if (!empty($parsed['query'])) {

            parse_str($parsed['query'], $query_array);

            // keep only allowed query params
            $allowed = ['name', 'email'];
            $query_array = array_intersect_key($query_array, array_flip($allowed));

            $url = (isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '') .
                (isset($parsed['host']) ? $parsed['host'] : '') .
                (isset($parsed['path']) ? $parsed['path'] : '');

            if (!empty($query_array)) {
                $url .= '?' . http_build_query($query_array);
            }
        }

        return $url;
    }

    private function prefill_fields($url)
    {
        $current_user = wp_get_current_user();

        if (!$current_user || empty($url)) return $url;

        $query = [];

        if (!empty($current_user->user_email)) {
            $query['email'] = $current_user->user_email;
        }

        $name = !empty($current_user->first_name) || !empty($current_user->last_name)
            ? trim($current_user->first_name . ' ' . $current_user->last_name)
            : '';

        if (!empty($name)) {
            $query['name'] = $name;
        }

        if (!empty($query)) {
            $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . http_build_query($query);
        }

        return $url;
    }

    private function embed_inline_widget($atts = [])
    {
        return '<div id="calendly-inline-widget" class="calendly-inline-widget ' . esc_attr($atts['style_class']) . '" 
                    data-url="' . esc_url($this->url) . '" data-redirection="' . esc_url($this->redirection_url) . '" 
                    style="height:' . esc_attr($atts['form_height']) . '; min-width:' . esc_attr($atts['form_width']) . '"></div>';
    }

    private function embed_popup_text_widget($atts = [])
    {
        return '<a id="calendly-popup-text-widget" data-url="' . esc_url($this->url) . '" data-redirection="' . esc_url($this->redirection_url) . '" class="' . esc_attr($atts['style_class']) . '" href="#" onclick="Calendly.initPopupWidget({url:\'' . esc_js($this->url) . '\'});return false;"
                    style="font-size:' . esc_attr($atts['text_size']) . '; color:' . esc_attr($atts['text_color']) . '">' . esc_html($atts['text']) . '</a>';
    }

    private function embed_inline_button_widget($atts = [])
    {
        $button_size = isset($atts['button_size']) ? (int) $atts['button_size'] : 0;

        switch ($button_size) {
            case 1:
                $padding = apply_filters('emcs_small_inline_button', '10px');
                break;

            case 2:
                $padding = apply_filters('emcs_medium_inline_button', '15px');
                break;

            default:
                $padding = apply_filters('emcs_large_inline_button', '20px');
                break;
        }

        return '<a id="calendly-inline-button-widget" data-url="' . esc_url($this->url) . '" data-redirection="' . esc_url($this->redirection_url) . '" class="' . esc_attr($atts['style_class']) . '" href="#" onclick="Calendly.initPopupWidget({url:\'' . esc_js($this->url) . '\'});return false;"
                    style="background-color:' . esc_attr($atts['button_color']) . '; padding:' . esc_attr($padding) . '; font-size:' . esc_attr($atts['text_size']) . ';
                    color:' . esc_attr($atts['text_color']) . ';">' . esc_html($atts['text']) . '</a>';
    }

    private function embed_popup_button_widget($atts = [])
    {
        $prefill_js = '';

        if (!empty($atts['prefill_fields'])) {

            $current_user = wp_get_current_user();

            $name = !empty($current_user->first_name) || !empty($current_user->last_name)
                ? trim($current_user->first_name . ' ' . $current_user->last_name)
                : '';

            $email = !empty($current_user->user_email) ? $current_user->user_email : '';

            $prefill_js = ",
                prefill: {
                    name: '" . esc_js($name) . "',
                    email: '" . esc_js($email) . "'
                }";
        }

        return "<div id='calendly-popup-button-widget' data-url='" . esc_url($this->url) . "' data-redirection='" . esc_url($this->redirection_url) . "' style='display:none'>
            <script>
                window.onload = function() {
                    Calendly.initBadgeWidget({
                        url: " . wp_json_encode($this->url, JSON_UNESCAPED_SLASHES) . ",
                        text: '" . esc_js($atts['text']) . "',
                        color: '" . esc_js($atts['button_color']) . "',
                        textColor: '" . esc_js($atts['text_color']) . "',
                        branding: " . (!empty($atts['branding']) ? 'true' : 'false') . "
                        $prefill_js
                    });
                };
            </script>
        </div>";
    }

    private function prepare_embed_url($url, $url_parts = [])
    {
        if (empty($url) || empty($url_parts)) return $url;

        $delimiter = parse_url($url, PHP_URL_QUERY) ? '&' : '?';

        return $url . $delimiter . implode('&', $url_parts);
    }
}
