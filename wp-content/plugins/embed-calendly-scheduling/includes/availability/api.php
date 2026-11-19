<?php

defined('ABSPATH') || exit;

include_once(EMCS_INCLUDES . 'api.php');

class EMCS_Availability_Api
{
    /**
     * Get the current Calendly user's availability, grouped by day name.
     */
    public static function get_availability($api_key = '')
    {
        if (empty($api_key)) {

            $options = get_option('emcs_settings');
            $api_key = self::resolve_api_key($options);

            if (empty($api_key)) {
                return array();
            }
        }

        $decrypted_key = self::decrypt_key($api_key);

        if (empty($decrypted_key)) {
            return false;
        }

        $schedule = self::list_current_user_schedule($decrypted_key);

        if (!is_array($schedule) || empty($schedule['rules'])) {
            return false;
        }

        $rules = $schedule['rules'];

        // Build the day => slots map.
        $availability = array();

        foreach ($rules as $rule) {

            if (!is_array($rule) || empty($rule['intervals'])) {
                continue;
            }

            if (empty($rule['type']) || 'wday' !== $rule['type']) {
                continue;
            }

            $day = !empty($rule['wday']) ? self::day_name($rule['wday']) : '';

            if (empty($day)) {
                continue;
            }

            $slots = array();

            foreach ($rule['intervals'] as $interval) {

                if (!is_array($interval) || empty($interval['from']) || empty($interval['to'])) {
                    continue;
                }

                $slots[] = (object) array(
                    'start' => $interval['from'],
                    'end'   => $interval['to'],
                );
            }

            if (empty($slots)) {
                continue;
            }

            $availability[$day] = $slots;
        }

        // Sort by day name (Monday => Sunday).
        $day_order = array(
            'Monday'    => 1,
            'Tuesday'   => 2,
            'Wednesday' => 3,
            'Thursday'  => 4,
            'Friday'    => 5,
            'Saturday'  => 6,
            'Sunday'    => 7,
        );

        uksort($availability, function ($a, $b) use ($day_order) {
            $a = isset($day_order[$a]) ? $day_order[$a] : 99;
            $b = isset($day_order[$b]) ? $day_order[$b] : 99;

            return $a - $b;
        });

        return $availability;
    }

    /**
     * Resolve the availability schedule URI for the current Calendly user.
     */
    private static function list_current_user_schedule($api_key)
    {
        $response = self::request(EMCS_API::calendly_v2_base_url . '/users/me', $api_key);

        if (is_wp_error($response)) {
            return false;
        }

        $body   = wp_remote_retrieve_body($response);
        $code   = wp_remote_retrieve_response_code($response);

        if ($code >= 400 || empty($body)) {
            return false;
        }

        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            return false;
        }

        $resource = !empty($decoded['resource']) ? $decoded['resource'] : array();
        $user_uri = !empty($resource['uri']) ? $resource['uri'] : '';

        if (empty($user_uri)) {
            return false;
        }

        return self::get_schedule('?' . http_build_query(array('user' => $user_uri)), $api_key);
    }

    /**
     * Fetch a single availability schedule by its URI.
     */
    private static function get_schedule($schedule_uri, $api_key)
    {
        $url = EMCS_API::calendly_v2_base_url . '/user_availability_schedules' . rtrim($schedule_uri, '/');
        $response = self::request($url, $api_key);

        if (is_wp_error($response)) {
            return false;
        }

        $body   = wp_remote_retrieve_body($response);
        $code   = wp_remote_retrieve_response_code($response);

        if ($code >= 400 || empty($body)) {
            return false;
        }

        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            return false;
        }

        $collection = !empty($decoded['collection']) ? $decoded['collection'] : array();

        if (empty($collection)) {
            return false;
        }

        $first = $collection[0];
        $resource = ! empty($first['resource']) ? $first['resource'] : $first;

        return array(
            'uri'        => ! empty($resource['uri']) ? $resource['uri'] : $url,
            'timezone'   => ! empty($resource['timezone']) ? $resource['timezone'] : '',
            'rules'      => ! empty($resource['rules']) ? $resource['rules'] : array(),
        );
    }

    /**
     * Make a single HTTP request, returning a WP_Error on failure.
     */
    private static function request($url, $api_key)
    {
        $args = array(
            'method'     => 'GET',
            'timeout'    => 20,
            'headers'    => array('Authorization' => 'Bearer ' . $api_key),
            'sslverify'  => true,
            'reject_unsafe_urls' => false,
        );

        return wp_remote_request($url, $args);
    }

    /**
     * Convert a Calendly day name into a friendly day name.
     */
    private static function day_name($day)
    {
        $map = array(
            'monday'    => 'Monday',
            'tuesday'   => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday'  => 'Thursday',
            'friday'    => 'Friday',
            'saturday'  => 'Saturday',
            'sunday'    => 'Sunday',
        );

        return isset($map[strtolower($day)]) ? $map[strtolower($day)] : ucfirst($day);
    }

    /**
     * Resolve an API key from the settings array, handling empty strings.
     */
    private static function resolve_api_key($options)
    {
        if (is_array($options)) {

            if (!empty($options['emcs_v2api_key'])) {
                return $options['emcs_v2api_key'];
            }

            if (!empty($options['emcs_v1api_key'])) {
                return $options['emcs_v1api_key'];
            }
        }
        return '';
    }

    /**
     * Decrypt an encrypted API key.
     */
    private static function decrypt_key($key)
    {
        if (function_exists('emcs_decrypt_key')) {
            return emcs_decrypt_key($key);
        }

        return $key;
    }
}
