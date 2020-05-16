<?php
/**
 * Class C_Frame_Event_Publisher
 * @mixin Mixin_Frame_Event_Publisher
 * @implements I_Frame_Event_Publisher
 */
class C_Frame_Event_Publisher extends C_Component
{
    static $_instances = array();
    var $setting_name = NULL;
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Frame_Event_Publisher');
        $this->implement('I_Frame_Event_Publisher');
    }
    function initialize()
    {
        parent::initialize();
        $this->setting_name = C_NextGen_Settings::get_instance()->frame_event_cookie_name;
    }
    /**
     * Gets an instance of the publisher
     * @param string|bool $context
     * @return C_Frame_Event_Publisher
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
}
class Mixin_Frame_Event_Publisher extends Mixin
{
    /**
     * Encodes data for a setting
     * @param array $data
     * @return string
     */
    function _encode($data)
    {
        return rawurlencode(json_encode($data));
    }
    /**
     * Decodes data from a setting
     * @param string $data
     * @return array
     */
    function _decode($data)
    {
        return (array) json_decode(rawurldecode($data));
    }
    /**
     * Adds a setting to the frame events
     * @param array $data
     * @return array
     */
    function add_event($data)
    {
        $id = md5(serialize($data));
        $data['context'] = $this->object->context;
        $write_cookie = TRUE;
        if (defined('XMLRPC_REQUEST')) {
            $write_cookie = XMLRPC_REQUEST == FALSE;
        }
        if ($write_cookie) {
            setrawcookie($this->object->setting_name . '_' . $id, $this->object->_encode($data), time() + 10800, '/', parse_url(site_url(), PHP_URL_HOST));
        }
        return $data;
    }
}