<?php

include_once('class.pope_cache.php');

define('__EXTOBJ_NO_INIT__', '__NO_INIT__');


/**
 * Provides helper methods for Pope objects
 */
class PopeHelpers
{
    /**
     * Merges two associative arrays
     * @param array $a1
     * @param array $a2
     * @return array
     */
    function array_merge_assoc($a1, $a2, $skip_empty=FALSE)
    {
		if ($a2) {
			foreach ($a2 as $key => $value) {
				if ($skip_empty && $value === '' OR is_null($value)) continue;
				if (isset($a1[$key])) {

					if (is_array($value)) {
						$a1[$key] = $this->array_merge_assoc($a1[$key], $value);

					}
					else {
						$a1[$key] = $value;
					}

				}
				else $a1[$key] = $value;
			}
		}
		return $a1;
    }


    /**
     * Returns TRUE if a property is empty
     * @param string $var
     * @return boolean
     */
    function is_empty($var, $element=FALSE)
    {
       if (is_array($var) && $element) {
           if (isset($var[$element])) $var = $var[$element];
           else $var = FALSE;
       }

       return (is_null($var) OR (is_string($var) AND strlen($var) == 0) OR $var === FALSE);
    }
}


/**
 * An ExtensibleObject can be extended at runtime with methods from another
 * class.
 *
 * - Mixins may be added or removed at any time during runtime
 * - The path to the mixin is cached so that subsequent method calls are
 *   faster
 * - Pre and post hooks can be added or removed at any time during runtime.
 * - Each method call has a list of associated properties that can be modified
 *   by pre/post hooks, such as: return_value, run_pre_hooks, run_post_hooks, etc
 * - Methods can be replaced by other methods at runtime
 * - Objects can implement interfaces, and are constrained to implement all
 *   methods as defined by the interface
 * - All methods are public. There's no added security by having private/protected
 *   members, as monkeypatching can always expose any method. Instead, protect
 *   your methods using obscurity. Conventionally, use an underscore to define
 *   a method that's private to an API
 */
class ExtensibleObject extends PopeHelpers
{
	static $enforce_interfaces=TRUE;

    var  $_mixins 			= array();
    var  $_mixin_priorities = array();
    var  $_method_map_cache = array();
    var  $_disabled_map     = array();
    var  $_interfaces		= array();
    var  $_throw_error 		= TRUE;
    var  $_wrapped_instance = FALSE;
    var  $object            = NULL;

    /**
     * Defines a new ExtensibleObject. Any subclass should call this constructor.
     * Subclasses are expected to provide the following:
     * define_instance() - adds extensions which provide instance methods
     * define_class() - adds extensions which provide static methods
     * initialize() - used to initialize the state of the object
     */
    function __construct()
    {
        // TODO This can be removed in the future. The Photocrati Theme currently requires this.
        $this->object = $this;

		$args = func_get_args();

        // Define the instance
		if (method_exists($this, 'define_instance'))
		{
			$reflection = new ReflectionMethod($this, 'define_instance');
			$reflection->invokeArgs($this, $args);
		}
		elseif (method_exists($this, 'define')) {
			$reflection = new ReflectionMethod($this, 'define');
			$reflection->invokeArgs($this, $args);
		}
		if (self::$enforce_interfaces) $this->_enforce_interface_contracts();

		if (!isset($args[0]) || $args[0] != __EXTOBJ_NO_INIT__) {
			// Initialize the state of the object
			if (method_exists($this, 'initialize')) {
				$reflection = new ReflectionMethod($this, 'initialize');
				$reflection->invokeArgs($this, $args);
			}
		}
	}


    /**
     * Adds an extension class to the object. The extension provides
     * methods for this class to expose as it's own
     * @param string $class
     */
    function add_mixin($class, $instantiate=FALSE)
    {
		$retval = TRUE;

		if (!$this->has_mixin($class)) {
			// We used to instantiate the class, but I figure
			// we might as well wait till the method is called to
			// save memory. Instead, the _call() method calls the
			// _instantiate_mixin() method below.
			$this->_mixins[$class] = NULL; // new $class();
			array_unshift($this->_mixin_priorities, $class);

			// Instantiate the mixin immediately, if requested
			if ($instantiate) $this->_instantiate_mixin($class);
			$this->_flush_cache();

		}
		else $retval = FALSE;

		return $retval;
    }


	/**
	 * Determines if a mixin has been added to this class
	 * @param string $klass
	 * @return bool
	 */
	function has_mixin($klass)
	{
		return array_key_exists($klass, $this->_mixins);
	}


    /**
     * Stores the instantiated class
     * @param string $class
     * @return mixed
     */
    function &_instantiate_mixin($class)
    {
        $retval = FALSE;
        if (isset($this->_mixins[$class]))
            $retval = $this->_mixins[$class];
        else {
            $obj= new $class();
            $obj->object = $this;
            $retval = $this->_mixins[$class] = &$obj;
            if (method_exists($obj, 'initialize')) $obj->initialize();
			unset($obj->object);
        }

        return $retval;
    }


    /**
     * Deletes an extension from the object. The methods provided by that
     * extension are no longer available for the object
     * @param string $class
     */
    function del_mixin($class)
    {
        unset($this->_mixins[$class]);
        $index = array_search($class, $this->_mixin_priorities);
		unset($this->_mixin_priorities[$index]);
		$this->_flush_cache();
    }


    function remove_mixin($class)
    {
        $this->del_mixin($class);
    }


	/**
	 * Returns the Mixin which provides the specified method
	 * @param string $method
	 */
	function get_mixin_providing($method, $return_obj=FALSE)
	{
		$retval = FALSE;

		// If it's cached, then we've got it easy
		if ($this->is_cached($method)) {
			$klass = $this->_method_map_cache[$method];
			return $return_obj ? $this->_instantiate_mixin($klass) : $klass;
		}

		// Otherwise, we have to look it up
		else {
			foreach ($this->_mixin_priorities as $class_name) {
				if (method_exists($class_name, $method) && !$this->is_mixin_disabled_for($method, $class_name)) {
					$object = $this->_instantiate_mixin($class_name);
					$this->_cache_method($class_name, $method);
					$retval =  $return_obj ? $object : $class_name;
					break;
				}
                elseif (!class_exists($class_name)) {
                    throw new RuntimeException("{$class_name} does not exist.");
                }
            }
		}

		return $retval;
	}

    function is_mixin_disabled_for($method, $mixin_klass)
    {
        $retval = FALSE;

        if (isset($this->_disabled_map[$method])) {
            $retval = in_array($mixin_klass, $this->_disabled_map[$method]);
        }

        return $retval;
    }

    function disable_mixin_for($method, $mixin_klass)
    {
        if (!isset($this->_disabled_map[$method])) {
            $this->_disabled_map[$method] = array($mixin_klass);
        }
        else if (!in_array($mixin_klass, $this->_disabled_map[$method])) {
            array_push($this->_disabled_map[$method], $mixin_klass);
        }

        unset($this->_method_map_cache[$method]);
    }

    function enable_mixin_for($method, $mixin_klass)
    {
        if (isset($this->_disabled_map[$method])) {
            if (($index = array_search($mixin_klass, $this->_disabled_map[$method])) !== FALSE) {
                unset($this->_disabled_map[$method][$index]);
            }
        }
    }

    /**
     * When an ExtensibleObject is instantiated, it checks whether all
     * the registered extensions combined provide the implementation as required
     * by the interfaces registered for this object
     */
    function _enforce_interface_contracts()
    {
        $errors = array();

        foreach ($this->_interfaces as $i) {
            $r = new ReflectionClass($i);
            foreach ($r->getMethods() as $m) {
                if (!$this->has_method($m->name)) {
					$klass = $this->get_class_name($this);
                    $errors[] = "`{$klass}` does not implement `{$m->name}` as required by `{$i}`";
                }
            }
        }

        if ($errors) throw new Exception(implode(". ", $errors));
    }


    /**
     * Implement a defined interface. Does the same as the 'implements' keyword
     * for PHP, except this method takes into account extensions
     * @param string $interface
     */
    function implement($interface)
    {
        $this->_interfaces[] = $interface;
    }

    /**
     * Wraps a class within an ExtensibleObject class.
     * @param string $klass
     * @param array callback, used to tell ExtensibleObject how to instantiate
     * the wrapped class
     */
    function wrap($klass, $callback=FALSE, $args=array())
    {
        if ($callback) {
            $this->_wrapped_instance = call_user_func($callback, $args);
        }
        else {
            $this->_wrapped_instance = new $klass();
        }
    }


    /**
     * Determines if the ExtensibleObject is a wrapper for an existing class
     */
    function is_wrapper()
    {
        return $this->_wrapped_instance ? TRUE : FALSE;
    }


    /**
     * Returns the name of the class which this ExtensibleObject wraps
     * @return string
     */
    function &get_wrapped_instance()
    {
        return $this->_wrapped_instance;
    }


    /**
     * Returns TRUE if the wrapped class provides the specified method
     */
    function wrapped_class_provides($method)
    {
        $retval = FALSE;

        // Determine if the wrapped class is another ExtensibleObject
        if (method_exists($this->_wrapped_instance, 'has_method')) {
			$retval = $this->_wrapped_instance->has_method($method);
        }
        elseif (method_exists($this->_wrapped_instance, $method)){
            $retval = TRUE;
        }

        return $retval;
    }


    /**
     * Provides a means of calling static methods, provided by extensions
     * @param string $method
     * @return mixed
     */
    static function get_class()
    {
		// Note: this function is static so $this is not defined
        $klass = self::get_class_name();
        $obj = new $klass(__EXTOBJ_STATIC__);
        return $obj;
    }


	/**
	 * Gets the name of the ExtensibleObject
	 * @return string
	 */
	static function get_class_name($obj = null)
	{
		if ($obj)
			return get_class($obj);
		elseif (function_exists('get_called_class'))
			return get_called_class();
		else
			return get_class();
	}

	/**
     * Gets a property from a wrapped object
     * @param string $property
     * @return mixed
     */
    function __get($property)
    {
		$retval = NULL;

		if ($property == 'object') return $this;
		else if ($this->is_wrapper()) {
			try {
				$reflected_prop = new ReflectionProperty($this->_wrapped_instance, $property);

				// setAccessible method is only available for PHP 5.3 and above
				if (method_exists($reflected_prop, 'setAccessible')) {
					$reflected_prop->setAccessible(TRUE);
				}

				$retval = $reflected_prop->getValue($this->_wrapped_instance);
			}
			catch (ReflectionException $ex)
			{
				$retval = $this->_wrapped_instance->$property;
			}
        }

		return $retval;
    }

	/**
	 * Determines if a property (dynamic or not) exists for the object
	 * @param string $property
	 * @return boolean
	 */
	function __isset($property)
	{
		$retval = FALSE;

		if (property_exists($this, $property)) {
			$retval = isset($this->$property);
		}
		elseif ($this->is_wrapper() && property_exists($this->_wrapped_instance, $property)) {
			$retval = isset($this->$property);
		}

		return $retval;
	}


    /**
     * Sets a property on a wrapped object
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    function __set($property, $value)
    {
		$retval = NULL;

        if ($this->is_wrapper()) {
			try {
				$reflected_prop = new ReflectionProperty($this->_wrapped_instance, $property);

				// The property must be accessible, but this is only available
				// on PHP 5.3 and above
				if (method_exists($reflected_prop, 'setAccessible')) {
					$reflected_prop->setAccessible(TRUE);
				}

				$retval = $reflected_prop->setValue($this->_wrapped_instance, $value);
			}

			// Sometimes reflection can fail. In that case, we need
			// some ingenuity as a failback
			catch (ReflectionException $ex) {
				$this->_wrapped_instance->$property = $value;
				$retval = $this->_wrapped_instance->$property;
			}

        }
		else {
			$this->$property = $value;
			$retval = $this->$property;
		}
        return $retval;
    }


    /**
     * Finds a method defined by an extension and calls it. However, execution
     * is a little more in-depth:
     * 1) Execute all global pre-hooks and any pre-hooks specific to the requested
     *    method. Each method call has instance properties that can be set by
     *    other hooks to modify the execution. For example, a pre hook can
     *    change the 'run_pre_hooks' property to be false, which will ensure that
     *    all other pre hooks will NOT be executed.
     * 2) Runs the method. Checks whether the path to the method has been cached
     * 3) Execute all global post-hooks and any post-hooks specific to the
     *    requested method. Post hooks can access method properties as well. A
     *    common usecase is to return the value of a post hook instead of the
     *    actual method call. To do this, set the 'return_value' property.
     * @param string $method
     * @param array $args
     * @return mixed
     */
    function __call($method, $args)
    {
		$retval = NULL;

		if (($this->get_mixin_providing($method))) {
			$retval = $this->_exec_cached_method($method, $args);
		}

		// This is NOT a wrapped class, and no extensions provide the method
		else {
			// Perhaps this is a wrapper and the wrapped object
			// provides this method
			if ($this->is_wrapper() && $this->wrapped_class_provides($method))
			{
				$object = $this->add_wrapped_instance_method($method);
				$retval = call_user_func_array(
					array(&$object, $method),
					$args
				);
			}
			elseif ($this->_throw_error) {
                if (defined('POPE_DEBUG') && POPE_DEBUG)
                    print_r(debug_backtrace());
				throw new Exception("`{$method}` not defined for " . get_class());
			}
		}

        return $retval;
    }


	/**
	 * Adds the implementation of a wrapped instance method to the ExtensibleObject
	 * @param string $method
	 * @return Mixin
	 */
	function add_wrapped_instance_method($method)
	{
		$retval = $this->get_wrapped_instance();

		// If the wrapped instance is an ExtensibleObject, then we don't need
		// to use reflection
		if (!is_subclass_of($this->get_wrapped_instance(), 'ExtensibleObject')) {
			$func	= new ReflectionMethod($this->get_wrapped_instance(), $method);

			// Get the entire method definition
			$filename = $func->getFileName();
			$start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
			$end_line = $func->getEndLine();
			$length = $end_line - $start_line;
			$source = file($filename);
			$body = implode("", array_slice($source, $start_line, $length));
            $body = preg_replace("/^\s{0,}private|protected\s{0,}/", '', $body);

			// Change the context
			$body = str_replace('$this', '$this->object', $body);
			$body = str_replace('$this->object->object', '$this->object', $body);
			$body = str_replace('$this->object->$', '$this->object->', $body);

			// Define method for mixin
			$mixin_klass = "Mixin_AutoGen_{$method}";
			if (!class_exists($mixin_klass)) {
				eval("class {$mixin_klass} extends Mixin{
					{$body}
				}");
			}
			$this->add_mixin($mixin_klass);
			$retval = $this->_instantiate_mixin($mixin_klass);
			$this->_cache_method($mixin_klass, $method);

		}

		return $retval;
	}


    /**
     * Provides an alternative way to call methods
     */
    function call_method($method, $args=array())
    {
        if (method_exists($this, $method))
        {
            $reflection = new ReflectionMethod($this, $method);
            return $reflection->invokeArgs($this, array($args));
        }
        else {
            return $this->__call($method, $args);
        }
    }


    /**
     * Returns TRUE if the method in particular has been cached
     * @param string $method
     * @return type
     */
    function is_cached($method)
    {
        return isset($this->_method_map_cache[$method]);
    }


    /**
     * Caches the path to the extension which provides a particular method
     * @param string $klass
     * @param string $method
     */
    function _cache_method($klass, $method)
    {
        $this->_method_map_cache[$method] = $klass;
    }

    /**
     * Flushes the method cache
     */
    function _flush_cache()
    {
        $this->_method_map_cache = array();
    }


    /**
     * Returns TRUE if the object provides the particular method
     * @param string $method
     * @return boolean
     */
    function has_method($method)
    {
        $retval = FALSE;

        // Have we looked up this method before successfully?
        if ($this->is_cached($method)) {
            $retval = TRUE;
        }

        // Is this a local PHP method?
        elseif (method_exists($this, $method)) {
            $retval = TRUE;
        }

        // Is a mixin providing this method
        elseif ($this->get_mixin_providing($method)) {
            $retval = TRUE;
        }

        elseif ($this->is_wrapper() && $this->wrapped_class_provides($method)) {
            $retval = TRUE;
        }

        return $retval;
    }

    /**
     * Executes a cached method
     * @param string $method
     * @param array $args
     * @return mixed
     */
    function _exec_cached_method($method, $args=array())
    {
        $klass = $this->_method_map_cache[$method];
		$object = $this->_instantiate_mixin($klass);
        $object->object = $this;
        $reflection = new ReflectionMethod($object, $method);
        return $reflection->invokeArgs($object, $args);
    }

    /**
     * Returns TRUE if the ExtensibleObject has decided to implement a
     * particular interface
     * @param string $interface
     * @return boolean
     */
    function implements_interface($interface)
    {
        return in_array($interface, $this->_interfaces);
    }

    function get_class_definition_dir($parent=FALSE)
    {
        return dirname($this->get_class_definition_file($parent));
    }

    function get_class_definition_file($parent=FALSE)
    {
		$klass = $this->get_class_name($this);
        $r = new ReflectionClass($klass);
        if ($parent) {
            $parent = $r->getParentClass();
            return $parent->getFileName();
        }
        return $r->getFileName();
    }

    /**
     * Returns get_class_methods() optionally limited by Mixin
     *
     * @param string (optional) Only show functions provided by a mixin
     * @return array Results from get_class_methods()
     */
    public function get_instance_methods($name = null)
    {
        if (is_string($name))
        {
            $methods = array();
            foreach ($this->_method_map_cache as $method => $mixin) {
                if ($name == get_class($mixin))
                {
                    $methods[] = $method;
                }
            }
            return $methods;
        } else {
            $methods = get_class_methods($this);
            foreach ($this->_mixins as $mixin) {
                $methods = array_unique(array_merge($methods, get_class_methods($mixin)));
                sort($methods);
            }

            return $methods;
        }
    }

    function get_parent_mixin_providing($method, $return_obj=FALSE, $levels=1)
    {
        $disabled_mixins = array();

        for ($i=0; $i<$levels; $i++) {
            if (($klass = $this->get_mixin_providing($method))) {
                $this->disable_mixin_for($method, $klass);
                $disabled_mixins[] = $klass;

                // Get the method map cache
                $orig_method_map = $this->_method_map_cache;
                $this->_method_map_cache = (array)C_Pope_Cache::get(
                    array($this->context, $this->_mixin_priorities, $this->_disabled_map),
                    $this->_method_map_cache
                );
            }
        }

        $retval = $this->get_mixin_providing($method, $return_obj);

        // Re-enable mixins
        foreach ($disabled_mixins as $klass) {
            $this->enable_mixin_for($method, $klass);
        }

        return $retval;
    }
}


/**
 * An mixin provides methods for an ExtensibleObject to use
 */
class Mixin extends PopeHelpers
{
    /**
     * The ExtensibleObject which called the extension's method
     * @var ExtensibleObject
     */
    var $object;

    /**
     * The name of the method called on the ExtensibleObject
     * @var type
     */
    var $method_called;

    /**
     * There really isn't any concept of 'parent' method. An ExtensibleObject
     * instance contains an ordered array of extension classes, which provides
     * the method implementations for the instance to use. Suppose that an
     * ExtensibleObject has two extension, and both have the same methods.The
     * last extension appears to 'override' the first extension. So, instead of calling
     * a 'parent' method, we're actually just calling an extension that was added sooner than
     * the one that is providing the current method implementation.
     */
    function call_parent($method)
    {
        $retval = NULL;

        // To simulate a 'parent' call, we remove the current mixin providing the
		// implementation.
        $klass = $this->object->get_mixin_providing($method);

		// Perform the routine described above...
        $this->object->disable_mixin_for($method, $klass);

		// Get the method map cache
		$orig_method_map = $this->object->_method_map_cache;
		$this->object->_method_map_cache = (array)C_Pope_Cache::get(
			array($this->object->context, $this->object->_mixin_priorities, $this->object->_disabled_map),
			$this->object->_method_map_cache
		);

        // Call anchor
        $args = func_get_args();

        // Remove $method parameter
        array_shift($args);

        // Execute the method
        $retval = $this->object->call_method($method, $args);

		// Cache the method map for this configuration of mixins
		C_Pope_Cache::set(
            array($this->object->context, $this->object->_mixin_priorities, $this->object->_disabled_map),
			$this->object->_method_map_cache
		);

		// Re-enable mixins;
//		$this->object->add_mixin($klass);
        $this->object->enable_mixin_for($method, $klass);

		// Restore the original method map
		$this->object->_method_map_cache = $orig_method_map;

        return $retval;
    }

    /**
     * Although is is preferrable to call $this->object->method(), sometimes
     * it's nice to use $this->method() instead.
     * @param string $method
     * @param array $args
     * @return mixed
     */
    function __call($method, $args)
    {
        if ($this->object->has_method($method)) {
            return call_user_func_array(array(&$this->object, $method), $args);
        }
    }

    /**
     * Although extensions can have state, it's probably more desirable to maintain
     * the state in the parent object to keep a sane environment
     * @param string $property
     * @return mixed
     */
    function __get($property)
    {
        return $this->object->$property;
    }
}