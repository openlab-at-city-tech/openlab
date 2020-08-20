<?php

if (!defined('POPE_VERSION')) { die('Use autoload.php'); }

/**
 * A factory for hatching (instantiating) components
 */
class C_Component_Factory extends C_Component
{
    static $_instances = array();

    function define($context=FALSE)
    {
		parent::define($context);
        $this->implement('I_Component_Factory');
    }

    function create($method, $args=array())
    {
        // Format the arguments for the method call
        $args = func_get_args();
        array_shift($args);

        // Create the component and apply the adapters
        $component = $this->call_method($method, $args);

        return $component;
    }

    static function &get_instance($context = False)
    {
		if (!isset(self::$_instances[$context])) {
			self::$_instances[$context] = new C_Component_Factory($context);
		}
		return self::$_instances[$context];
    }
}
