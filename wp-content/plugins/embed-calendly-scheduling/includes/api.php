<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

include_once(EMCS_INCLUDES . 'event-types/event-type.php');

class EMCS_API
{
    private $api_version;
    private $api_key;

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
        $user = (isset($this->get_current_user()->resource)) ? $this->get_current_user()->resource->uri : '';
        $calendly_events  = EMCS_API::connect('/event_types', $this->api_key, $user);
        $events_data = array();

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
            $url     = 'https://api.calendly.com' . $endpoint;
            $headers = array(
                'Authorization' => 'Bearer ' . $api_key,
            );
        } else {
            $url     = 'https://calendly.com/api/v1' . $endpoint;
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
            $url = add_query_arg('user', rawurlencode($user), $url);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        return json_decode($body);
    }
}
