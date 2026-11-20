<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

include_once(EMCS_INCLUDES . 'event-types/event-type.php');

class EMCS_API
{
    private $api_version;
    private $api_key;

    public const calendly_v1_base_url = 'https://calendly.com/api/v1';
    public const calendly_v2_base_url = 'https://api.calendly.com';

    public function __construct($api_version, $api_key)
    {
        $this->api_version = $api_version;
        $this->api_key = $api_key;
    }

    public function emcs_get_events()
    {

        if ($this->api_version == 'v2') {
            return $this->emcs_get_events_v2();
        } else {
            return $this->emcs_get_events_v1();
        }
    }

    protected function emcs_get_events_v1()
    {
        $calendly_events  = EMCS_API::connect('/users/me/event_types', $this->api_key);
        $events_data = array();

        if (is_wp_error($calendly_events)) {
            return $calendly_events;
        }

        if (empty($calendly_events->data)) {
            return false;
        }

        foreach ($calendly_events->data as $events) {

            $event = new EMCS_Event_Type(
                $events->attributes->name,
                $events->attributes->description,
                !empty($events->attributes->active) ? $events->attributes->active : '0',
                $events->attributes->url,
                $events->attributes->slug
            );

            $events_data[] = $event;
        }

        return $events_data;
    }

    protected function emcs_get_events_v2()
    {
        $current_user = $this->get_current_user();

        if (is_wp_error($current_user)) {
            return $current_user;
        }

        $user = (isset($current_user->resource)) ? $current_user->resource->uri : '';
        $calendly_events  = EMCS_API::connect('/event_types', $this->api_key, $user);
        $events_data = array();

        if (is_wp_error($calendly_events)) {
            return $calendly_events;
        }

        if (empty($calendly_events->collection)) {
            return false;
        }

        foreach ($calendly_events->collection as $events) {

            $event = new EMCS_Event_Type(
                $events->name,
                $events->description_plain,
                !empty($events->active) ? $events->active : '0',
                $events->scheduling_url,
                $events->slug
            );

            $events_data[] = $event;
        }

        return $events_data;
    }

    protected function get_current_user()
    {
        $calendly  = EMCS_API::connect('/users/me', $this->api_key);
        return $calendly;
    }

    protected function connect($endpoint, $api_key, $user = '')
    {

        if ($this->api_version === 'v2') {
            $url     = EMCS_API::calendly_v2_base_url . $endpoint;
            $headers = array(
                'Authorization' => 'Bearer ' . $api_key,
            );
        } else {
            $url     = EMCS_API::calendly_v1_base_url . $endpoint;
            $headers = array(
                'X-Token' => $api_key,
            );
        }

        $args = array(
            'method'  => 'GET',
            'headers' => $headers,
            'timeout' => 20,
        );

        // If v2 and user is provided, append as query param
        if ($this->api_version === 'v2' && ! empty($user)) {
            $url = add_query_arg('user', $user, $url);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $status_code = wp_remote_retrieve_response_code($response);
        $decoded = json_decode($body);

        // If HTTP status indicates an error, return a WP_Error with the
        // response body so callers can display a friendly message.
        if ($status_code >= 400) {
            $message = $body;
            if (isset($decoded->error)) {
                $message = $decoded->error;
            }
            if (isset($decoded->title)) {
                $message = $decoded->title;
            }
            return new WP_Error(
                'emcs_calendly_api_error',
                $message,
                array('http_code' => $status_code)
            );
        }

        return $decoded;
    }
}
