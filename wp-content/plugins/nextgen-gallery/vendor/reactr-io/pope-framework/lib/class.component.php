<?php

if (!defined('POPE_VERSION')) { die('Use autoload.php'); }

/**
 * Pope is a component-based framework. All classes should inherit this class.
 */
class C_Component extends ExtensibleObject
{
    /**
     * @var string
     */
    var $context;
	var $adapted = FALSE;

	/**
	 * Many components will execute parent::define()
	 */
	function define($context=FALSE)
	{
		$this->context = is_null($context) ? FALSE : $context;
		$this->implement('I_Component');
	}

    // Initializes the state of the object
    function initialize()
    {
		$this->get_registry()->apply_adapters($this);
		$this->adapted = TRUE;
		register_shutdown_function(array(&$this, 'update_cache'));
		$this->_method_map_cache = (array)C_Pope_Cache::get(
            array($this->context, $this->_mixin_priorities, $this->_disabled_map),
			$this->_method_map_cache
		);
    }

	// Updates the cache for this component
	function update_cache()
	{
		C_Pope_Cache::set(array($this->context, $this->_mixin_priorities, $this->_disabled_map), $this->_method_map_cache);
	}

	/**
	 * Determines if the component has one or more particular contexts assigned
	 * @param string|array $context
	 * @return boolean
	 */
	function has_context($context)
	{
		$retval = TRUE;
		$current_context = is_array($this->context) ? $this->context : array($this->context);
		if (!is_array($context)) $context = array($context);
		foreach ($context as $c) {
			if (!in_array($c, $current_context)) {
				$retval = FALSE;
				break;
			}
		}
		return $retval;
	}

	/**
	 * Assigns a particular context to the component
	 * @param type $context
	 */
	function add_context($context)
	{
		if (!is_array($context)) $context = array($context);
		if (!is_array($this->context)) $this->context = array($this->context);
		foreach ($context as $c) {
			if (in_array($c, $this->context)) continue;
			else $context[] = $c;
		}
	}

	/**
	 * Assigns one or more contexts to the component
	 * @param type $context
	 */
	function assign_context($context)
	{
		$this->add_context($context);
	}

	/**
	 * Un-assigns one or more contexts from the component
	 * @param type $context
	 */
	function remove_context($context)
	{
		if (!is_array($context)) $context = array($context);
		if (!is_array($this->context)) $this->context = array($this->context);
		foreach ($context as $c) {
			if (($index = array_search($c, $this->context)) !== FALSE) {
				unset($this->context[$index]);
			}
		}
	}

	/**
	 * Assigns one or more contexts to the component
	 * @param type $context
	 */
	function unassign_context($context)
	{
		$this->remove_context($context);
	}

	/**
	 * Gets the component registry
	 * @return 	C_Component_Registry
	 */
	function get_registry()
	{
		return C_Component_Registry::get_instance();
	}

	/**
	 * Gets the component registry -- backward compatibility
	 * @return 	C_Component_Registry
	 */
	function _get_registry()
	{
		return C_Component_Registry::get_instance();
	}
}
