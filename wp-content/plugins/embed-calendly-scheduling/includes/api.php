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

    public function emcs_get_events() {
        
        if($this->api_version == 'v2') {
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

    protected function get_current_user() {
        $calendly  = EMCS_API::connect('/users/me', $this->api_key);
        return $calendly;
    }

    protected function connect($endpoint, $api_key, $user = '')
    {
        $headers = array();

        $ch = curl_init();

        if($this->api_version == 'v2') {
            $url = 'https://api.calendly.com' . $endpoint;
            $headers[] = 'Authorization: Bearer ' . $api_key;

            if(!empty($user)) {

                curl_setopt($ch, CURLOPT_POSTFIELDS, ['user' => $user]);
            }

        } else {
            $url = 'https://calendly.com/api/v1' . $endpoint;
            $headers[] = 'X-Token: ' . $api_key;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }
}
