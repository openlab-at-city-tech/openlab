<?php

if (!defined('POPE_VERSION')) { die('Use autoload.php'); }

/**
 *  A registry of registered products, modules, adapters, and utilities.
 *
 *
 *  How the registry gets initialized:
 * 1) Each product tells the registry where to find products and modules
 * 2) We load all products
 */
class C_Component_Registry
{
    static  $_instance = NULL;
	var     $_searched_paths = array();
	var     $_blacklist = array();
	var     $_meta_info = array();
    var     $_default_path = NULL;
    var     $_modules = array();
    var     $_products = array();
    var     $_adapters = array();
    var     $_utilities = array();
    var     $_module_type_cache = array();
    var     $_module_type_cache_count = 0;

    /**
     * This is a singleton object
     */
    private function __construct()
    {
        // Create an autoloader
        spl_autoload_register(array($this, '_module_autoload'), TRUE);
    }


    /**
     * Returns a singleton
     * @return C_Component_Registry()
     */
    static function &get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new C_Component_Registry();
        }
        return self::$_instance;
    }

	function require_module_file($module_file_abspath)
	{
		// We don't include (require) module files that have the same name. This
		// avoids loading module.autoupdate.php from two products
		static $already_required = array();
		$relpath = basename($module_file_abspath);
		if (!in_array($relpath, $already_required)) {
            ini_set('track_errors', true);
            @require_once($module_file_abspath);
            if (isset($php_errormsg) && defined('NGG_DEBUG') && constant('NGG_DEBUG')) error_log($php_errormsg);
			$already_required[] = $relpath;
		}
	}

	function has_searched_path_before($abspath)
	{
		return in_array($abspath, $this->_searched_paths);
	}

	function mark_as_searched_path($abspath)
	{
		$this->_searched_paths[] = $abspath;
	}


    /**
     * Adds a path in the search paths for loading modules
     * @param string $path
     * @param bool $recurse - TRUE, FALSE, or the number of levels to recurse
     * @param bool $load_all - loads all modules found in the path
     */
    function add_module_path($path, $recurse = false, $load_all = false)
    {
	    if (!$recurse || (!$this->has_searched_path_before($path))) {

		    // If no default module path has been set, then set one now
		    if ($this->get_default_module_path() == null)  {
			    $this->set_default_module_path($path);
		    }

		    // We we've been passed a module file, then include it
		    if (@file_exists($path) && is_file($path)) {
			    $this->require_module_file($path);
		    }

		    // Recursively find product and module files in this path
		    else foreach ($this->find_product_and_module_files($path, $recurse) as $file_abspath) {
			    $this->require_module_file($file_abspath);
		    }

		    $this->mark_as_searched_path($path);
	    }

	    if ($load_all) $this->load_all_modules(NULL, $path);
    }


    /**
     * Retrieves the default module path (Note: this is just the generic root container path for modules)
     * @return string
     */
    function get_default_module_path()
    {
        return $this->_default_path;
    }


    /**
     * Sets the default module path (Note: this is just the generic root container path for modules)
     * @param string $path
     */
    function set_default_module_path($path)
    {
        $this->_default_path = $path;
    }


    /**
     * Retrieves the module path
     * @param string $module_id
     * @return string
     */
    function get_module_path($module_id)
    {
        if (isset($this->_meta_info[$module_id])) {
            $info = $this->_meta_info[$module_id];

            if (isset($info['path'])) {
                return $info['path'];
            }
        }

        return null;
    }


    /**
     * Retrieves the module installation directory
     * @param string $module_id
     * @return string
     */
    function get_module_dir($module_id)
    {
        $path = $this->get_module_path($module_id);

        if ($path != null) {
            return dirname($path);
        }

        return null;
    }


	function is_module_loaded($module_id)
	{
		return (isset($this->_meta_info[$module_id]) && isset($this->_meta_info[$module_id]['loaded']) && $this->_meta_info[$module_id]['loaded']);
	}

    /**
     * Loads a module's code according to its dependency list
     * @param string $module_id
     */
    function load_module($module_id)
    {
        $retval = FALSE;

	    if (($module = $this->get_module($module_id)) && !$this->is_module_loaded($module_id) && !$this->is_blacklisted($module_id)) {
			$module->load();
		    $retval = $this->_meta_info[$module_id]['loaded'] = TRUE;

	    }

	    return $retval;
    }

    function load_all_modules($type=NULL, $dir=NULL)
    {
        $modules = $this->get_known_module_list();
        $ret = true;

        foreach ($modules as $module_id)
        {
            if ($type == null || $this->get_module_meta($module_id, 'type') == $type) {
                if ($dir == NULL || strpos($this->get_module_dir($module_id), $dir) !== FALSE)
	                $ret = $this->load_module($module_id) && $ret;
            }
        }

        return $ret;
    }


    /**
     * Initializes a previously loaded module
     * @param string $module_id
     */
    function initialize_module($module_id)
    {
        $retval = FALSE;

        if (isset($this->_modules[$module_id])) {
            $module = $this->_modules[$module_id];

            if ($this->is_module_loaded($module_id) && !$module->initialized) {
                if (method_exists($module, 'initialize'))
                    $module->initialize();

                $module->initialized = true;
            }
            $retval = TRUE;
        }
        return $retval;
    }


    /**
     * Initializes an already loaded product
     * @param string $product_id
     * @return bool
     */
    function initialize_product($product_id)
    {
        return $this->initialize_module($product_id);
    }


    /**
     * Initializes all previously loaded modules
     */
    function initialize_all_modules()
    {
        $module_list = $this->get_loaded_module_list();

        foreach ($module_list as $module_id)
        {
            $this->initialize_module($module_id);
        }
    }


    /**
     * Adds an already loaded module to the registry
     * @param string $module_id
     * @param C_Base_Module $module_object
     */
    function add_module($module_id, $module_object)
    {
        if (!isset($this->_modules[$module_id])) {
            $this->_modules[$module_id] = $module_object;
        }

	    if (!isset($this->_meta_info[$module_id])) {
		    $klass = new ReflectionClass($module_object);

		    $this->_meta_info[$module_id] = array(
			    'path'      =>  $klass->getFileName(),
			    'type'      =>  $klass->isSubclassOf('C_Base_Product') ? 'product' : 'module',
			    'loaded'    =>  FALSE
		    );
	    }
    }


    /**
     * Deletes an already loaded module from the registry
     * @param string $module_id
     */
    function del_module($module_id)
    {
        if (isset($this->_modules[$module_id])) {
            unset($this->_modules[$module_id]);
        }
    }


    /**
     * Retrieves the instance of the registered module. Note: it's the instance of the module object, so the module needs to be loaded or this function won't return anything. For module info returned by scanning (with add_module_path), look at get_module_meta
     * @param string $module_id
     * @return C_Base_Module
     */
    function get_module($module_id)
    {
        if (isset($this->_modules[$module_id])) {
            return $this->_modules[$module_id];
        }

        return null;
    }

    function get_module_meta($module_id, $meta_name)
    {
        $meta = $this->get_module_meta_list($module_id);

        if (isset($meta[$meta_name])) {
            return $meta[$meta_name];
        }

        return null;
    }

    function get_module_meta_list($module_id)
    {
        if (isset($this->_meta_info[$module_id])) {
            return $this->_meta_info[$module_id];
        }

        return null;
    }

    /**
     * Retrieves a list of instantiated module ids, in their "loaded" order as defined by a product
     *
     * @return array
     */
	function get_module_list($for_product_id=FALSE)
	{
		$retval = $module_list = array();
		// As of May 1, 2015, there's a new standard. A product will provide get_provided_modules() and get_modules_to_load().

		// As of Feb 10, 2015, there's no standard way across Pope products to an "ordered" list of modules
		// that the product provides.
		//
		// The "standard" going forward will insist that all Product classes will provide either:
		// A) a static property called "modules"
		// B) an instance method called "define_modules", which returns a list of modules, and as well, sets
		//    a static property called "modules'.
		//
		// IMPORTANT!
		// The Photocrati Theme, as of version 4.1.8, doesn't follow this standard. But both NextGEN Pro and Plus do.

		// Following the standard above, collect all modules provided by a product
		$problematic_product_id = FALSE;
		foreach ($this->get_product_list() as $product_id) {
			$modules = array();

			// Try getting the list of modules using the "standard" described above
			$obj = $this->get_product($product_id);
			try{
				$klass = new ReflectionClass($obj);
				if ($klass->hasMethod('get_modules_to_load')) {
					$modules = $obj->get_modules_provided();
				}
				elseif ($klass->hasProperty('modules')) {
					$modules = $klass->getStaticPropertyValue('modules');
				}

				if (!$modules && $klass->hasMethod('define_modules')) {
					$modules = $obj->define_modules();
					if ($klass->hasProperty('modules')) {
						$modules = $klass->getStaticPropertyValue('modules');
					}
				}
			}

				// We've encountered a product that doesn't follow the standard. For these exceptions, we'll have to
				// make an educated guess - if the module path is in the product's default module path, we know that
				// it belongs to the product
			catch (ReflectionException $ex) {
				$modules = array();
			}

			if (!$modules) {
				$product_path = $this->get_product_module_path($product_id);
				foreach ($this->_modules as $module_id => $module) {
					if (strpos($this->get_module_path($module_id), $product_path) !== FALSE) {
						$modules[] = $module_id;
					}
				}
				if (!$modules) $problematic_product_id = $product_id;
			}

			$module_list[$product_id] = $modules;
		}

		// If we have a problematic product, that is, one that we can't find it's ordered list of modules
		// that it provides, then we have one last fallback: get a list of modules that Pope is aware of, but hasn't
		// added to $module_list[$product_id] yet
		if ($problematic_product_id) {
			$modules = array();
			foreach (array_keys($this->_modules) as $module_id) {
				$assigned = FALSE;
				foreach (array_keys($module_list) as $product_id) {
					if (in_array($module_id, $module_list[$product_id])) {
						$assigned =TRUE;
						break;
					}
				}
				if (!$assigned) $modules[] = $module_id;
			}
			$module_list[$problematic_product_id] = $modules;
		}

		// Now that we know which products provide which modules, we can serve the request.
		if (!$for_product_id) {
			foreach (array_values($module_list) as $modules) {
				$retval = array_merge($retval, $modules);
			}
		}
		else $retval = $module_list[$for_product_id];

		// Final fallback...if all else fails, just return the list of all modules
		// that Pope is aware of
		if (!$retval) $retval = array_keys($this->_modules);


		return $retval;
	}

	function get_loaded_module_list()
	{
		$retval = array();

		foreach ($this->get_module_list() as $module_id) {
			if ($this->is_module_loaded($module_id)) $retval[] = $module_id;
		}

		return $retval;
	}

    /**
     * Retrieves a list of registered module ids, including those that aren't loaded (i.e. get_module() call with those unloaded ids will fail)
     * @return array
     */
    function get_known_module_list()
    {
        return array_keys($this->_meta_info);
    }


    function load_product($product_id)
    {
        return $this->load_module($product_id);
    }

    function load_all_products()
    {
        return $this->load_all_modules('product');
    }

    /**
     * Adds an already loaded product in the registry
     * @param string $product_id
     * @param C_Base_Module $product_object
     */
    function add_product($product_id, $product_object)
    {
        if (!isset($this->_products[$product_id])) {
            $this->_products[$product_id] = $product_object;
        }
    }


    /**
     * Deletes an already loaded product from the registry
     * @param string $product_id
     */
    function del_product($product_id)
    {
        if (isset($this->_products[$product_id])) {
            unset($this->_products[$product_id]);
        }
    }


    /**
     * Retrieves the instance of the registered product
     * @param string $product_id
     * @return C_Base_Module
     */
    function get_product($product_id)
    {
        if (isset($this->_products[$product_id])) {
            return $this->_products[$product_id];
        }

        return null;
    }

    function get_product_meta($product_id, $meta_name)
    {
        $meta = $this->get_product_meta_list($product_id);

        if (isset($meta[$meta_name])) {
            return $meta[$meta_name];
        }

        return null;
    }

    function get_product_meta_list($product_id)
    {
        if (isset($this->_meta_info[$product_id]) && $this->_meta_info[$product_id]['type'] == 'product') {
            return $this->_meta_info[$product_id];
        }

        return null;
    }


    /**
     * Retrieves the module installation path for a specific product (Note: this is just the generic root container path for modules of this product)
     * @param string $product_id
     * @return string
     */
    function get_product_module_path($product_id)
    {
        if (isset($this->_meta_info[$product_id])) {
            $info = $this->_meta_info[$product_id];

            if (isset($info['product-module-path'])) {
                return $info['product-module-path'];
            }
        }

        return null;
    }

	function blacklist_module_file($relpath)
	{
		if (!in_array($relpath, $this->_blacklist)) $this->_blacklist[] = $relpath;
	}

	function is_blacklisted($filename)
	{
		return in_array($filename, $this->_blacklist);
	}

    /**
     * Sets the module installation path for a specific product (Note: this is just the generic root container path for modules of this product)
     * @param string $product_id
     * @param string $module_path
     */
    function set_product_module_path($product_id, $module_path)
    {
        if (isset($this->_meta_info[$product_id])) {
            $this->_meta_info[$product_id]['product-module-path'] = $module_path;
        }
    }


    /**
     * Retrieves a list of instantiated product ids
     * @return array
     */
    function get_product_list()
    {
        return array_keys($this->_products);
    }

    /**
     * Retrieves a list of registered product ids, including those that aren't loaded (i.e. get_product() call with those unloaded ids will fail)
     * @return array
     */
    function get_known_product_list()
    {
        $list = array_keys($this->_meta_info);
        $return = array();

        foreach ($list as $module_id)
        {
            if ($this->get_product_meta_list($module_id) != null)
            {
                $return[] = $module_id;
            }
        }

        return $return;
    }


    /**
     * Registers an adapter for an interface with specific contexts
     * @param string $interface
     * @param string $class
     * @param array $contexts
     */
    function add_adapter($interface, $class, $contexts=FALSE)
    {
        // If no specific contexts are given, then we assume
        // that the adapter is to be applied in ALL contexts
        if (!$contexts) $contexts = array('all');
        if (!is_array($contexts)) $contexts = array($contexts);

        if (!isset($this->_adapters[$interface])) {
            $this->_adapters[$interface] = array();
        }

        // Iterate through each specific context
        foreach ($contexts as $context) {
            if (!isset($this->_adapters[$interface][$context])) {
                $this->_adapters[$interface][$context] = array();
            }
            $this->_adapters[$interface][$context][] = $class;
        }
    }


    /**
     * Removes an adapter for an interface. May optionally specifify what
     * contexts to remove the adapter from, leaving the rest intact
     * @param string $interface
     * @param string $class
     * @param array $contexts
     */
    function del_adapter($interface, $class, $contexts=FALSE)
    {
        // Ensure that contexts is an array of contexts
        if (!$contexts) $contexts = array('all');
        if (!is_array($contexts)) $contexts = array($contexts);

        // Iterate through each context for an adapter
        foreach ($this->_adapters[$interface] as $context => $classes) {
            if (!$context OR in_array($context, $contexts)) {
                $index = array_search($class, $classes);
                unset($this->_adapters[$interface][$context][$index]);
            }
        }


    }


    /**
     * Apply adapters registered for the component
     * @param C_Component $component
     * @return C_Component
     */
    function &apply_adapters(C_Component &$component)
    {
        // Iterate through each adapted interface. If the component implements
        // the interface, then apply the adapters
        foreach ($this->_adapters as $interface => $contexts) {
            if ($component->implements_interface($interface)) {


                // Determine what context apply to the current component
                $applied_contexts = array('all');
                if ($component->context) {
                    $applied_contexts[] = $component->context;
                    $applied_contexts = $this->_flatten_array($applied_contexts);
                }

                // Iterate through each of the components contexts and apply the
                // registered adapters
                foreach ($applied_contexts as $context) {
                    if (isset($contexts[$context])) {
                        foreach ($contexts[$context] as $adapter) {
                            $component->add_mixin($adapter, FALSE);
                        }
                    }

                }
            }
        }

        return $component;
    }


    /**
     * Adds a utility for an interface, to be used in particular contexts
     * @param string $interface
     * @param string $class
     * @param array $contexts
     */
    function add_utility($interface, $class, $contexts=FALSE)
    {
        // If no specific contexts are given, then we assume
        // that the utility is for ALL contexts
        if (!$contexts) $contexts = array('all');
        if (!is_array($contexts)) $contexts = array($contexts);

        if (!isset($this->_utilities[$interface])) {
            $this->_utilities[$interface] = array();
        }

        // Add the utility for each appropriate context
        foreach ($contexts as $context) {
            $this->_utilities[$interface][$context] = $class;
        }
    }


    /**
     * Deletes a registered utility for a particular interface.
     * @param string $interface
     * @param array $contexts
     */
    function del_utility($interface, $contexts=FALSE)
    {
        if (!$contexts) $contexts = array('all');
        if (!is_array($contexts)) $contexts = array($contexts);

        // Iterate through each context for an interface
        foreach ($this->_utilities[$interface] as $context => $class) {
            if (!$context OR in_array($context, $contexts)) {
                unset($this->_utilities[$interface][$context]);
            }
        }
    }

    /**
     * Gets the class name of the component providing a utility implementation
     * @param string $interface
     * @param string|array $context
     * @return string
     */
    function get_utility_class_name($interface, $context=FALSE)
    {
        return $this->_retrieve_utility_class($interface, $context);
    }


    /**
     * Retrieves an instantiates the registered utility for the provided instance.
     * The instance is a singleton and must provide the get_instance() method
     * @param string $interface
     * @param string $context
     * @return C_Component
     */
    function get_utility($interface, $context=FALSE)
    {
        if (!$context) $context='all';
        $class = $this->_retrieve_utility_class($interface, $context);
        return call_user_func("{$class}::get_instance", $context);
    }


    /**
     * Flattens an array of arrays to a single array
     * @param array $array
     * @param array $parent (optional)
     * @param bool $exclude_duplicates (optional - defaults to TRUE)
     * @return array
     */
    function _flatten_array($array, $parent=NULL, $exclude_duplicates=TRUE)
    {
        if (is_array($array)) {

            // We're to add each element to the parent array
            if ($parent) {
                foreach ($array as $index => $element) {
                    foreach ($this->_flatten_array($array) as $sub_element) {
                        if ($exclude_duplicates) {
                            if (!in_array($sub_element, $parent)) {
                                $parent[] = $sub_element;
                            }
                        }
                        else $parent[] = $sub_element;
                    }
                }
                $array = $parent;
            }

            // We're starting the process..
            else {
                $index = 0;
                while (isset($array[$index])) {
                    $element = $array[$index];
                    if (is_array($element)) {
                        $array = $this->_flatten_array($element, $array);
                        unset($array[$index]);
                    }
                    $index += 1;
                }
                $array = array_values($array);
            }
        }
        else {
            $array = array($array);
        }

        return $array;
    }

	function find_product_and_module_files($abspath, $recursive=FALSE)
	{
		$retval = array();
		static $recursive_level = 0;
        static $exclusions = array('..', '.', 'error_log', 'README', 'CHANGELOG', 'readme.txt', 'changelog.txt', 'LICENSE', 'node_modules', 'vendor');
		$recursive_level++;

		$abspath = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $abspath);
		if (!in_array($abspath, $exclusions)) {
            $contents = @scandir($abspath);
            if ($contents) foreach ($contents as $filename) {
                if (in_array($filename, $exclusions)) continue;
                $filename_abspath = $abspath.DIRECTORY_SEPARATOR.$filename;

                // Is this a subdirectory?
                // We don't use is_dir(), as it's less efficient than just checking for a 'dot' in the filename.
                // The problem is that we're assuming that our directories won't contain a 'dot'.
                if ($recursive && strpos($filename, '.') === FALSE) {

                    // The recursive parameter can either be set to TRUE or the number of levels to navigate
                    // If we reach the max number of recursive levels we're supported to navigate, then we try
                    // to guess if there's a module or product file under the directory with the same name as
                    // the directory
                    if ($recursive === TRUE || (is_int($recursive) && $recursive_level <= $recursive)) {
                        $retval = array_merge($retval, $this->find_product_and_module_files($filename_abspath, $recursive));
                    }

                    elseif (@file_exists(($module_abspath = $filename_abspath.DIRECTORY_SEPARATOR.'module.'.$filename.'.php'))) {
                        $filename = 'module.'.$filename.'.php';
                        $filename_abspath = $module_abspath;
                    }
                    elseif (@file_exists(($product_abspath = $filename_abspath.DIRECTORY_SEPARATOR.'product.'.$filename.'.php'))) {
                        $filename = 'product.'.$filename.'.php';
                        $filename_abspath = $module_abspath;
                    }

                }

                if ((strpos($filename, 'module.') === 0 OR strpos($filename, 'product.') === 0) AND !$this->is_blacklisted($filename)) {
                    $retval[] = $filename_abspath;
                }
            }
        }

		$this->mark_as_searched_path($abspath);

		$recursive_level--;

		return $retval;
	}


    /**
     * Private API method. Retrieves the class which currently provides the utility
     * @param string $interface
     * @param string $context
     */
    function _retrieve_utility_class($interface, $context='all')
    {
        $class = FALSE;

        if (!$context) $context = 'all';
        if (isset($this->_utilities[$interface])) {
            if (isset($this->_utilities[$interface][$context])) {
                $class = $this->_utilities[$interface][$context];
            }

            // No utility defined for the specified interface
            else {
                if ($context == 'all') $context = 'default';
                $class = $this->_retrieve_utility_class($interface, FALSE);
                if (!$class)
                    throw new Exception("No utility registered for `{$interface}` with the `{$context}` context.");

            }
        }
        else throw new Exception("No utilities registered for `{$interface}`");

        return $class;
    }
    /**
     * Autoloads any classes, interfaces, or adapters needed by this module
     */
    function _module_autoload($name)
    {
	    // Pope classes are always prefixed
	    if (strpos($name, 'C_') !== 0 && strpos($name, 'A_') !== 0 && strpos($name, 'Mixin_') !== 0) {
		    return;
	    }

        if ($this->_module_type_cache == null || count($this->_modules) > $this->_module_type_cache_count)
        {
            $this->_module_type_cache_count = count($this->_modules);
            $modules = $this->_modules;

            $keys = array();
            foreach ($modules as $mod => $properties) $keys[$mod] = $properties->module_version;
            if (!($this->_module_type_cache = C_Pope_Cache::get($keys, array()))) {
                foreach ($modules as $module_id => $module)
                {
                    $dir = $this->get_module_dir($module_id);
                    $type_list = $module->get_type_list();

                    foreach ($type_list as $type => $filename)
                    {
                        $this->_module_type_cache[strtolower($type)] = $dir . DIRECTORY_SEPARATOR . $filename;
                    }
                }
                C_Pope_Cache::set($keys, $this->_module_type_cache);
            }
            elseif (is_object($this->_module_type_cache)) $this->_module_type_cache = get_object_vars($this->_module_type_cache);
        }

        $name = strtolower($name);

        if (isset($this->_module_type_cache[$name]))
        {
            $module_filename = $this->_module_type_cache[$name];

            if (file_exists($module_filename))
            {
                require_once($module_filename);
            }
        }
    }
}
