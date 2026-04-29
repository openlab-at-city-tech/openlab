<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

include_once(EMCS_DIR . 'includes/embed.php');
include_once(EMCS_DIR . 'includes/dynamic-embedder.php');

class EMCS_Shortcode
{
    /**
     * Renders basic embedder shortcode
     */
    public static function basic_embedder($atts)
    {
        if (!is_array($atts)) {
            $atts = [];
        }

        // Normalize keys to lowercase
        $atts = array_change_key_case($atts, CASE_LOWER);

        // Prepare and sanitize attributes, preserving extra keys (like tracking)
        $atts = self::prepare_attributes($atts);

        wp_enqueue_style('emcs_calendly_css');
        wp_enqueue_script('emcs_calendly_js');

        if (empty($atts['url'])) {
            return esc_html__('Error embedding calendar. Invalid URL.', 'embed-calendly-scheduling');
        }

        $emcs_embed = new EMCS_Embed($atts);
        return $emcs_embed->embed_calendar();
    }

    /**
     * Prepare and sanitize shortcode attributes
     *
     * @param array $atts
     * @return array Sanitized attributes with defaults and extra keys preserved
     */
    private static function prepare_attributes(array $atts)
    {
        // Known default attributes
        $defaults = [
            'url'               => '',
            'type'              => 1,
            'text'              => 'Schedule a call with me',
            'text_color'        => '#000000',
            'text_size'         => '12px',
            'form_height'       => '400px',
            'form_width'        => '600px',
            'button_color'      => '#001F3F',
            'button_style'      => 1,
            'button_size'       => 1,
            'style_class'       => '',
            'branding'          => 0,
            'hide_details'      => 0,
            'hide_cookie_banner' => 0,
            'prefill_fields'    => 0,
        ];

        // Merge defaults without stripping unknown keys
        $atts = $atts + $defaults;

        // Sanitize known keys
        $atts['url']              = !empty($atts['url']) ? esc_url_raw($atts['url']) : '';
        $atts['embed_type']       = intval($atts['type']);
        $atts['text']             = sanitize_text_field($atts['text']);
        $atts['text_color']       = preg_replace('/[^#a-zA-Z0-9]/', '', sanitize_text_field($atts['text_color']));
        $atts['text_size']        = intval($atts['text_size']) . 'px';
        $atts['form_height']      = intval($atts['form_height']) . 'px';
        $atts['form_width']       = intval($atts['form_width']) . 'px';
        $atts['button_color']     = preg_replace('/[^#a-zA-Z0-9]/', '', sanitize_text_field($atts['button_color']));
        $atts['button_style']     = intval($atts['button_style']);
        $atts['button_size']      = intval($atts['button_size']);
        $atts['style_class']      = sanitize_text_field($atts['style_class']);
        $atts['branding']         = intval($atts['branding']);
        $atts['hide_details']     = intval($atts['hide_details']);
        $atts['cookie_banner']    = intval($atts['hide_cookie_banner']);
        $atts['prefill_fields']   = intval($atts['prefill_fields']);

        // Preserve any extra keys (utm_*, custom tracking, etc.) without sanitizing too aggressively
        foreach ($atts as $key => $value) {
            if (!array_key_exists($key, $defaults)) {
                $atts[$key] = is_scalar($value) ? sanitize_text_field($value) : $value;
            }
        }

        return $atts;
    }

    /**
     * Render dynamic embedder shortcode
     */
    public static function dynamic_embedder($atts)
    {
        $dynamic_embed = new EMCS_Dynamic_Embedder($atts);
        return $dynamic_embed->render();
    }
}
