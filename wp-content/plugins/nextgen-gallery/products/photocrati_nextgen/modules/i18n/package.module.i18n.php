<?php
/**
 * Class A_I18N_Routing_App
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_I18N_Routing_App extends Mixin
{
    function execute_route_handler($handler)
    {
        if (!empty($GLOBALS['q_config']) && defined('QTRANS_INIT')) {
            global $q_config;
            $q_config['hide_untranslated'] = 0;
        }
        return $this->call_parent('execute_route_handler', $handler);
    }
}