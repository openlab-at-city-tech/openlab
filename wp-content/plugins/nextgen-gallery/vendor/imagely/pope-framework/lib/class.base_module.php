<?php

if (!defined('POPE_VERSION')) { die('Use autoload.php'); }

/**
 * A Module will register utilities and adapters to provide it's functionality,
 * and usually provide some classes for business logic.
 *
 * Registered an adapter for the I_Component_Factory interface to add new
 * factory methods is the most common use of an adapter.
 */
abstract class C_Base_Module
{
    var $module_id;
    var $module_name;
    var $module_description;
    var $module_version;
    var $module_uri;
    var $module_author;
    var $module_author_uri;
    var $module_type_list = null;
    var $initialized = FALSE;

    public $object;

    function __construct()
    {
        // TODO: This is here to be compatible with the theme. Once the theme doesn't make use of $this->object
        // when it doesn't have to, we can remove this circular reference
        $this->object = $this;

    	@$this->define();
    }

	function initialize()
	{

	}

	function get_registry()
	{
		return C_Component_Registry::get_instance();
	}

	function _get_registry()
	{
		return C_Component_Registry::get_instance();
	}

    /**
     * Defines the module
     */
    function define($id='pope-module', $name='Pope Module', $description='', $version='', $uri='', $author='', $author_uri='', $context=FALSE)
    {
		$this->module_id = $id;
		$this->module_name = $name;
		$this->module_description = $description;
		$this->module_version = $version;
		$this->module_uri = $uri;
		$this->module_author = $author;
		$this->module_author_uri = $author_uri;

		$this->get_registry()->add_module($this->module_id, $this);
    }

	function load()
	{
	    // Package files may not exist until releases are built; do not use require_once() here
        $path = $this->get_package_abspath();
        ini_set('track_errors', true);
        if (@file_exists($path))
            include_once($path);

        if (isset($php_errormsg) && defined('NGG_DEBUG') && constant('NGG_DEBUG'))
            error_log($php_errormsg);

        $this->_register_utilities();
        $this->_register_adapters();
        $this->_register_hooks();
	}

	function get_package_abspath()
	{
		$module_abspath = $this->get_registry()->get_module_path($this->module_id);
		return str_replace('module.', 'package.module.', $module_abspath);
	}

    /**
     * I/O can be expensive to run repeatedly, so when a module is created we cache a listing of every file provided
     *
     * @return array List of types => files belonging to this module
     */
    function get_type_list()
    {
    	// XXX small hack to skip photocrati theme modules scans
    	$except_modules = array(
    	'photocrati-gallery_legacy' => array(), 
    	'photocrati-theme_bulk' => array(), 
    	'photocrati-theme_admin' => array(), 
    	'photocrati-auto_update' => array(
        'A_Autoupdate_Settings' => 'adapter.autoupdate_settings.php'
      ),
    	'photocrati-auto_update-admin' => array(
        'A_Autoupdate_Admin_Ajax' => 'adapter.autoupdate_admin_ajax.php',
        'A_Autoupdate_Admin_Factory' => 'adapter.autoupdate_admin_factory.php',
        'C_Autoupdate_Admin_Ajax' => 'class.autoupdate_admin_ajax.php',
        'C_Autoupdate_Admin_Controller' => 'class.autoupdate_admin_controller.php'
      ));
      
      if (isset($except_modules[$this->module_id]))
      {
      	return $except_modules[$this->module_id];
      }
      
    	if ($this->module_type_list === null)
    	{    		
				$map = array(
					'C_'		=> 'class',
					'A_'		=> 'adapter',
					'I_'		=> 'interface',
					'Mixin_'	=> 'mixin',
					'M_'		=> 'module',
					'Hook_'		=> 'hook',
				);
		      
		  	$type_list = array();
		    $dir = $this->get_registry()->get_module_dir($this->module_id) . DIRECTORY_SEPARATOR;
		    $iterator = new RecursiveIteratorIterator(
		        new RecursiveDirectoryIterator($dir)
		    );
		    foreach ($iterator as $filename) {
		        if (in_array(basename($filename->getPathname()), array('.', '..')))
		            continue;
		      
		      $filename = str_replace($dir, '', $filename->getPathname());
		    	$file_parts = explode('.', $filename);
		    	$prefix = $file_parts[0];
		    	$name = (!empty($file_parts[1]) ? $file_parts[1] : '');
		    	$name_prefix = array_search($prefix, $map);
		    	
		    	if ($name_prefix)
		    	{
				  	$type_name = $name_prefix . $name;
				  	
				  	$type_list[$type_name] = $filename;
		    	}
		    }
		    
		    $this->module_type_list = $type_list;
    	}
      
      return $this->module_type_list;
    }
   
    /**
     * Provides a reliable means of determining if the current request is in the
     * wp-admin panel
     * @return boolean
     */
    function is_admin()
    {
        return (is_admin() OR preg_match('/wp-admin/', $_SERVER['REQUEST_URI']));
    }

    /**
     * Join two filesystem paths together (e.g. 'give me $path relative to $base').
     *
     * If the $path is absolute, then the full path is returned.
     * Taken from wordpress 3.4.1
     *
     * @param string $base
     * @param string $path
     * @return string The path with the base or absolute path
     */
    function _path_join($base, $path)
    {
        if ($this->_path_is_absolute($path))
        {
            return $path;
        }
        return trim($base, "/\\") . DIRECTORY_SEPARATOR . ltrim($path, "/\\");
    }

    /**
     * Test if a give filesystem path is absolute ('/foo/bar', 'c:\windows').
     *
     * Taken from wordpress 3.4.1
     * @param string $path File path
     * @return bool True if path is absolute, false is not absolute.
     */
    function _path_is_absolute($path)
    {
        // this is definitive if true but fails if $path does not exist or contains a symbolic link
        if (realpath($path) == $path)
        {
            return true;
        }

        if (strlen($path) == 0 || $path[0] == '.')
        {
            return false;
        }

        // windows allows absolute paths like this
        if (preg_match('#^[a-zA-Z]:\\\\#', $path))
        {
            return true;
        }

        // a path starting with / or \ is absolute; anything else is relative
        return ($path[0] == '/' || $path[0] == '\\');
    }

    function _register_hooks() {}
    function _register_adapters() {}
    function _register_utilities() {}
}
