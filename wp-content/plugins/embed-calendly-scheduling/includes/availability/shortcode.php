<?php

defined('ABSPATH') || exit;

include_once(EMCS_INCLUDES . 'availability/api.php');

class EMCS_Availability
{
    /**
     * Register the [calendly_availability] shortcode.
     */
    public static function init()
    {
        add_shortcode('calendly_availability', array(__CLASS__, 'render_availability'));
    }

    /**
     * Renders the [calendly_availability] shortcode.
     */
    public static function render_availability($atts)
    {
        $atts = self::prepare_attributes($atts);

        $availability = EMCS_Availability_Api::get_availability();

        return self::render_availability_output($availability, $atts);
    }

    /**
     * Render the availability text using the supplied data.
     */
    private static function render_availability_output($availability, $atts)
    {
        $formatted = self::format_availability($availability);

        if (empty($formatted)) {

            $style = 'color:' . esc_attr($atts['text_color']) . ';';
            return '<span style="' . esc_attr($style) . '">' . esc_html__('No availability this week.', 'embed-calendly-scheduling') . '</span>';
        }

        $prefix = apply_filters('emcs_availability_prefix', '', $atts, $availability);
        $style = 'color:' . esc_attr($atts['text_color']) . ';';

        return '<span style="' . esc_attr($style) . '">' . $prefix . $formatted . '</span>';
    }

    /**
     * Format availability into readable "Day(9am-10pm)" strings.
     */
    public static function format_availability($availability)
    {
        if (empty($availability) || !is_array($availability)) {
            return '';
        }

        $days = array_keys($availability);

        if (empty($days)) {
            return '';
        }

        $formatted = array();

        foreach ($days as $day) {

            $slots  = $availability[$day];
            $times  = array();

            foreach ($slots as $slot) {

                if (!is_object($slot)) {
                    continue;
                }

                $start = isset($slot->start) ? $slot->start : '';
                $end   = isset($slot->end) ? $slot->end : '';

                if (!empty($start)) {
                    $times[] = $start;
                }

                if (!empty($end)) {
                    $times[] = $end;
                }
            }

            if (empty($times)) {
                continue;
            }

            sort($times);

            $start_time = $times[0];
            $end_time   = end($times);

            $formatted[] = sprintf(
                '%s(%s-%s)',
                esc_html($day),
                self::format_time($start_time),
                self::format_time($end_time)
            );
        }

        return implode(', ', $formatted);
    }

    /**
     * Format a 24-hour H:i time into 12-hour am/pm format.
     */
    private static function format_time($time)
    {
        if (empty($time)) {
            return '';
        }

        $dt = strtotime($time);

        if (false === $dt) {
            return $time;
        }

        return date_i18n('ga', $dt);
    }

    /**
     * Prepare and sanitize shortcode attributes.
     */
    private static function prepare_attributes(array $atts)
    {
        $defaults = array(
            'theme'    => 'default',
            'text_color' => '#000000',
        );

        // Merge defaults without stripping known keys
        $atts = $atts + $defaults;

        // Sanitize known keys
        $atts['theme']    = self::sanitize_theme($atts['theme']);
        $atts['text_color'] = sanitize_text_field($atts['text_color']);

        return $atts;
    }

    /**
     * Resolve a raw `theme` attribute into a known value.
     */
    public static function sanitize_theme($theme)
    {
        $allowed = array('default');

        if (!in_array($theme, $allowed, true)) {
            return 'default';
        }

        return $theme;
    }
}
