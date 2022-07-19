<?php

if (!class_exists('C_Photocrati_Settings_Manager_Base'))
{
	/**
	 * Provides a base abstraction for a Settings Manager
	 * Class C_Settings_Manager_Base
	 */
	abstract class C_Photocrati_Settings_Manager_Base implements ArrayAccess
	{
		static $option_name			= 'pope_settings';
		protected $_options			= array();
		protected $_defaults		= array();
		protected $_option_handlers = array();

		abstract function save();
		abstract function destroy();
		abstract function load();

		protected function __construct()
		{
			$this->load();
		}

		/**
		 * Adds a class to handle dynamic options
		 * @param string $klass
		 * @param array $options
		 */
		function add_option_handler($klass, $options=array())
		{
			if (!is_array($options)) $options = array($options);
			foreach ($options as $option_name) {
				$this->_option_handlers[$option_name] = $klass;
			}
		}

		/**
		 * Gets a handler used to provide a dynamic option
		 * @param string $option_name
		 * @return null|mixed
		 */
		protected function _get_option_handler($option_name, $method='get')
		{
			$retval = NULL;

			if (isset($this->_option_handlers[$option_name])) {
				if (!is_object($this->_option_handlers[$option_name])) {
					$klass = $this->_option_handlers[$option_name];
					$this->_option_handlers[$option_name] = new $klass;
				}
				$retval = $this->_option_handlers[$option_name];
				if (!method_exists($retval, $method)) $retval = NULL;
			}
			return $retval;
		}

		/**
		 * Gets the value of a particular setting
         *
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		function get($key, $default = NULL)
		{
			$retval = $default;

            if (($handler = $this->_get_option_handler($key, 'get'))) {
                $retval = $handler->get($key, $default);
            }
            else if (isset($this->_options[$key])) {
                $retval =  $this->_options[$key];
            }

			// In case a stdObject has been passed in as a value, we want to only return scalar values or arrays
			if (is_object($retval))
			    $retval = (array) $retval;

			return $retval;
		}

		/**
		 * Sets a setting to a particular value
		 * @param string $key
		 * @param mixed $value
		 * @return mixed
		 */
		function set($key, $value=NULL, $skip_handlers=FALSE)
		{
			if (is_object($value)) $value = (array) $value;

			if (is_array($key)) {
				foreach ($key as $k=>$v) $this->set($k, $v);
			}
			elseif (!$skip_handlers && ($handler = $this->_get_option_handler($key, 'set'))) {
				$handler->set($key, $value);
			}
			else $this->_options[$key] = $value;

			return $this;
		}

		/**
		 * Deletes a setting
		 * @param string $key
		 */
		function delete($key)
		{
			if (($handler = $this->_get_option_handler($key, 'delete'))) {
				$handler->delete($key);
			}
			else {
				unset($this->_options[$key]);
			}
		}

		/**
		 * Determines if a setting exists or not
		 * @param $key
		 * @return bool
		 */
		function is_set($key)
		{
			return array_key_exists($key, $this->_options);
		}

		/**
		 * Alias to is_set()
		 * @param $key
		 * @return bool
		 */
		function exists($key)
		{
			return $this->is_set($key);
		}

		function does_not_exist($key)
		{
			return !$this->exists($key);
		}

		function reset()
		{
			$this->_options = array();
            $this->_defaults = array();
		}

		/**
		 * This function does two things:
		 * a) If a value hasn't been set for the specified key, or it's been set to a previously set
		 *    default value, then set this key to the value specified
		 * b) Sets a new default value for this key
		 */
		function set_default_value($key, $default)
		{
			if (!isset($this->_defaults[$key])) $this->_defaults[$key] = $default;
			if (is_null($this->get($key, NULL)) OR $this->get($key) == $this->_defaults[$key]) {
				$this->set($key, $default);
			}
			$this->_defaults[$key] = $default;
			return $this->get($key);
		}

        #[\ReturnTypeWillChange]
		function offsetExists($key)
		{
			return $this->is_set($key);
		}

		#[\ReturnTypeWillChange]
		function offsetGet($key)
		{
			return $this->get($key);
		}

        #[\ReturnTypeWillChange]
		function offsetSet($key, $value)
		{
			return $this->set($key, $value);
		}

        #[\ReturnTypeWillChange]
		function offsetUnset($key)
		{
			return $this->delete($key);
		}

		function __get($key)
		{
			return $this->get($key);
		}

		function __set($key, $value)
		{
			return $this->set($key, $value);
		}

		function __isset($key)
		{
			return $this->is_set($key);
		}

		function __toString()
		{
			return json_encode($this->_options);
		}

		function __toArray()
		{
			return $this->_options;
		}

		function to_array()
		{
			return $this->__toArray();
		}

		function to_json()
		{
			return json_encode($this->_options);
		}

		function from_json($json)
		{
			$this->_options = (array)json_decode($json);
		}
	}
}

if (!class_exists('C_Photocrati_Global_Settings_Manager')) {
	class C_Photocrati_Global_Settings_Manager extends C_Photocrati_Settings_Manager_Base
	{
        static $_instance = NULL;

        /**
         * @return C_Photocrati_Global_Settings_Manager
         */
		public static function get_instance()
		{
            if (is_null(self::$_instance)) {
                $klass = get_class();
                self::$_instance = new $klass();
            }
            return self::$_instance;
		}

		function save()
		{
			return update_site_option(self::$option_name, $this->to_array());
		}

		function load()
		{
			$this->_options = get_site_option(self::$option_name, $this->to_array());
			if (!$this->_options)
			    $this->_options = array();
			else if (is_string($this->_options))
			    $this->_options = C_NextGen_Serializable::unserialize($this->_options);
		}

		function destroy()
		{
			return delete_site_option(self::$option_name);
		}
	}
}


if (!class_exists('C_Photocrati_Settings_Manager'))
{
	class C_Photocrati_Settings_Manager extends C_Photocrati_Settings_Manager_Base
	{
        static $_instance = NULL;

        /**
         * @return C_Photocrati_Settings_Manager
         */
        public static function get_instance()
        {
            if (is_null(self::$_instance)) {
                $klass = get_class();
                self::$_instance = new $klass();
            }
            return self::$_instance;
        }

		function get($key, $default=NULL)
		{
			$retval = parent::get($key, NULL);

			if (is_null($retval)) {
				$retval = C_Photocrati_Global_Settings_Manager::get_instance()->get($key, $default);
			}
			return $retval;
		}

		function save()
		{
			return update_option(self::$option_name, $this->to_array());
		}

		function load()
		{
			$this->_options = get_option(self::$option_name, array());
			if (!$this->_options) $this->_options = array();
			else if (is_string($this->_options)) $this->_options = C_NextGen_Serializable::unserialize($this->_options);
		}

		function destroy()
		{
			delete_option(self::$option_name);
		}
	}
}