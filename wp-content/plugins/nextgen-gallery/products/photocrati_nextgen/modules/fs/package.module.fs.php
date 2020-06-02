<?php
/**
 * Class C_Fs
 * @mixin Mixin_Fs_Instance_Methods
 * @implements I_Fs
 */
class C_Fs extends C_Component
{
    static $_instances = array();
    var $_document_root;
    /**
     * Gets an instance of the FS utility
     * @param mixed $context
     * @return C_Fs
     */
    static function &get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the instance of the FS utility
     * @param mixed $context	the context in this case is the product
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Fs_Instance_Methods');
        $this->implement('I_Fs');
    }
    function initialize()
    {
        parent::initialize();
        $this->_document_root = $this->set_document_root(ABSPATH);
    }
}
class Mixin_Fs_Instance_Methods extends Mixin
{
    function add_trailing_slash($path)
    {
        return rtrim($path, "/\\") . DIRECTORY_SEPARATOR;
    }
    /**
     * Returns a calculated path to a file
     * @param string $path
     * @param string|false $module (optional)
     * @return string
     */
    function get_abspath($path, $module = FALSE)
    {
        return M_Static_Assets::get_static_abspath($path, $module);
    }
    /**
     * Removes a path segment from a url or filesystem path
     * @param string $path
     * @param string $segment
     * @return string
     */
    function remove_path_segment($path, $segment)
    {
        $parts = explode($segment, rtrim($path, "/\\"));
        return $this->join_paths($parts);
    }
    /**
     * Gets the absolute path to a file/directory for a specific Pope product
     *
     * If the path doesn't exist, then NULL is returned
     * @param string $path
     * @param string|false $module (optional)
     * @param bool $relpath (optional)
     * @param array $search_paths (optional)
     * @return string|NULL
     */
    function find_abspath($path, $module = FALSE)
    {
        if (strpos($path, '#') !== FALSE) {
            $parts = explode("#", $path);
            if (count($parts) === 2) {
                $path = $parts[1];
                $module = $parts[0];
            } else {
                $path = $parts[0];
            }
        }
        if (!$module) {
            die(sprintf("find_abspath requires a path and module. Received %s and %s", $path, strval($module)));
        }
        $module_dir = C_Component_Registry::get_instance()->get_module_dir($module);
        $path = preg_replace("#^/{1,2}#", "", $path, 1);
        $retval = path_join($module_dir, $path);
        // Adjust for windows paths
        return wp_normalize_path($retval);
    }
    /**
     * Returns a list of directories to search for a particular filename
     * @param string $path
     * @param string|false $module (optional)
     * @return array
     */
    function get_search_paths($path, $module = FALSE)
    {
        $append_module = FALSE;
        // Ensure that we weren't passed a module id in the path
        if (!$module) {
            list($path, $module) = $this->object->parse_formatted_path($path);
        }
        // Directories to search
        $directories = array();
        // If a name of a module has been provided, then we need to search within
        // that directory first
        if ($module) {
            // Were we given a module id?
            if ($module_dir = $this->get_registry()->get_module_dir($module)) {
                $directories[] = $module_dir;
            } else {
                $append_module = TRUE;
            }
        }
        // Add product's module directories
        foreach ($this->get_registry()->get_product_list() as $product_id) {
            $product_dir = $this->get_registry()->get_product_module_path($product_id);
            if ($append_module) {
                $directories[] = $this->join_paths($product_dir, $module);
            }
            $directories[] = $product_dir;
        }
        // If all else fails, we search from the document root
        $directories[] = $this->get_document_root();
        return $directories;
    }
    /**
     * Searches for a file recursively
     *
     * @param string $base_path
     * @param string $file
     * @return null|string
     */
    function _rglob($base_path, $file)
    {
        $retval = NULL;
        $results = @file_exists($this->join_paths($base_path, $file));
        // Must be located in a sub-directory
        if (!$results) {
            // the modules cache a list of all their files when they are initialized. Ask POPE for our current
            // modules and inspect their file listing to determine which module provides what we need
            $modules = $this->object->get_registry()->get_module_list();
            foreach ($modules as $module) {
                $module_file_list = array_values($this->object->get_registry()->get_module($module)->get_type_list());
                $module_dir = $this->object->get_registry()->get_module_dir($module);
                $variations = array($file, ltrim($file, DIRECTORY_SEPARATOR));
                foreach ($variations as $variant) {
                    if (in_array($variant, $module_file_list)) {
                        $retval = $this->join_paths($module_dir, $variant);
                        break 2;
                    }
                }
            }
        } else {
            $retval = $this->join_paths($base_path, $file);
        }
        return $retval;
    }
    /**
     * Gets the relative path to a file/directory for a specific Pope product. If the path doesn't exist, then NULL is returned
     * @param string $path
     * @param string|false $module (optional)
     * @return string|null
     */
    function find_relpath($path, $module = FALSE)
    {
        return $this->object->find_abspath($path, $module, TRUE);
    }
    /**
     * @param string $abspath
     * @return bool
     */
    function delete($abspath)
    {
        $retval = FALSE;
        if (file_exists($abspath)) {
            // Delete single file
            if (is_file($abspath)) {
                @unlink($abspath);
            } else {
                foreach (scandir($abspath) as $relpath) {
                    if (in_array($relpath, array('.', '..'))) {
                        continue;
                    }
                    $sub_abspath = $this->join_paths($abspath, $relpath);
                    $this->object->delete($sub_abspath);
                }
            }
            $retval = !file_exists($abspath);
        }
        return $retval;
    }
    /**
     * Joins multiple path segments together
     * @deprecated use path_join() instead when you can
     * @return string
     */
    function join_paths()
    {
        $segments = array();
        $retval = array();
        $protocol = NULL;
        $params = func_get_args();
        $this->_flatten_array($params, $segments);
        foreach ($segments as $segment) {
            $segment = trim($segment, "/\\");
            $pieces = array_values(preg_split('#[/\\\\]#', $segment));
            $segment = join(DIRECTORY_SEPARATOR, $pieces);
            if (!$retval) {
                $retval = $segment;
            } else {
                if (strpos($segment, $retval) !== FALSE) {
                    $retval = $segment;
                } else {
                    $retval = $retval . DIRECTORY_SEPARATOR . $segment;
                }
            }
        }
        if (strpos($retval, $this->get_document_root()) !== 0 && strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
            $retval = DIRECTORY_SEPARATOR . trim($retval, "/\\");
        }
        // Check for and adjust Windows UNC paths (\\server\share\) for network mounted sites
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && substr($this->get_document_root(), 0, 2) === '\\\\') {
            $retval = '\\\\' . $retval;
        }
        return $retval;
    }
    function _flatten_array($obj, &$arr)
    {
        if (is_array($obj)) {
            foreach ($obj as $inner_obj) {
                $this->_flatten_array($inner_obj, $arr);
            }
        } elseif ($obj) {
            $arr[] = $obj;
        }
    }
    /**
     * Parses the path for a module and filename
     * @param string $str
     * @return array [path => module]
     */
    function parse_formatted_path($str)
    {
        $module = FALSE;
        $path = $str;
        $parts = explode('#', $path);
        if (count($parts) > 1) {
            $module = array_shift($parts);
            $path = array_shift($parts);
        }
        return array($path, $module);
    }
    /**
     * Gets the document root for this application
     * @param string $type Must be one of plugins, plugins_mu, templates, styles, content, gallery, or root
     * @return string
     */
    function get_document_root($type = 'root')
    {
        $retval = NULL;
        switch ($type) {
            case 'plugins':
            case 'plugin':
                $retval = WP_PLUGIN_DIR;
                break;
            case 'plugins_mu':
            case 'plugin_mu':
                $retval = WPMU_PLUGIN_DIR;
                break;
            case 'templates':
            case 'template':
            case 'themes':
            case 'theme':
                $retval = get_template_directory();
                break;
            case 'styles':
            case 'style':
            case 'stylesheets':
            case 'stylesheet':
                $retval = get_stylesheet_directory();
                break;
            case 'content':
                $retval = WP_CONTENT_DIR;
                break;
            case 'gallery':
            case 'galleries':
                $root_type = NGG_GALLERY_ROOT_TYPE;
                if ($root_type == 'content') {
                    $retval = WP_CONTENT_DIR;
                } else {
                    $retval = $this->_document_root;
                }
                break;
            default:
                $retval = $this->_document_root;
        }
        return wp_normalize_path($retval);
    }
    function get_absolute_path($path)
    {
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return wp_normalize_path(implode(DIRECTORY_SEPARATOR, $absolutes));
    }
    /**
     * Sets the document root for this application
     * @param string $value
     * @return string
     */
    function set_document_root($value)
    {
        // some web servers like home.pl and PhpStorm put the document root in "/" or (even weirder) "//"
        if ($value == DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) {
            $value = DIRECTORY_SEPARATOR;
        }
        if ($value !== DIRECTORY_SEPARATOR) {
            $value = rtrim($value, "/\\");
        }
        return $this->_document_root = $value;
    }
}