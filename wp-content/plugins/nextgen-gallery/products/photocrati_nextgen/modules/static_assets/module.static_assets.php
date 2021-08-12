<?php
class M_Static_Assets extends C_Base_Module
{
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-static_assets',
            'Static Assets',
            'Provides a means of finding static assets',
            '3.1.8',
            'https://www.imagely.com',
            'Imagely',
            'https://www.imagely.com'
        );
    }

    static function get_static_url($filename, $module=FALSE)
    {
        $retval = self::get_static_abspath($filename, $module);

        // Allow for overrides from WP_CONTENT/ngg/
        if (strpos($retval, path_join(WP_CONTENT_DIR, 'ngg')) !== FALSE)
            $retval = str_replace(wp_normalize_path(WP_CONTENT_DIR), WP_CONTENT_URL, $retval);

        // Normal plugin distributed files
        $retval = str_replace(wp_normalize_path(WP_PLUGIN_DIR), WP_PLUGIN_URL, $retval);

        $retval = is_ssl() ? str_replace('http:', 'https:', $retval) : $retval;

        return $retval;
    }

    static function get_static_abspath($filename, $module=FALSE)
    {
        static $cache = array();
        $key = $filename.strval($module);
        if (!isset($cache[$key])) {
            $cache[$key] = self::get_computed_static_abspath($filename, $module);
        }
        return $cache[$key];
    }

    static function get_computed_static_abspath($filename, $module=FALSE)
    {
        if (strpos($filename, '#') !== FALSE) {
            $parts = explode("#", $filename);
            if (count($parts) === 2) {
                $filename   = $parts[1];
                $module     = $parts[0];    
            }
            else $filename = $parts[0];
        }
        $filename = self::trim_preceding_slash($filename);

        if (!$module) die(sprintf(
            "get_static_abspath requires a path and module. Received %s and %s",
            $filename,
            strval($module))
        );

        $module_dir = wp_normalize_path(C_Component_Registry::get_instance()->get_module_dir($module));

        // In case NextGen is in a symlink we make $mod_dir relative to the NGG parent root and then rebuild it
        // using WP_PLUGIN_DIR; without this NGG-in-symlink creates URL that reference the file abspath
        if (is_link(path_join(WP_PLUGIN_DIR, basename(NGG_PLUGIN_DIR))))
        {
            $module_dir = ltrim(str_replace(dirname(NGG_PLUGIN_DIR), '', $module_dir), DIRECTORY_SEPARATOR);
            $module_dir = path_join(WP_PLUGIN_DIR, $module_dir);
        }

        $static_dir = self::trim_preceding_slash(C_NextGen_Settings::get_instance()->mvc_static_dir);

        $override_dir = wp_normalize_path(self::get_static_override_dir($module));
        $retval = $override = path_join(
            $override_dir,
            $filename
        );

        if (!@stream_resolve_include_path($override)) {
            $retval = path_join(
                path_join($module_dir, $static_dir),
                $filename
            );
        }

        // Adjust for windows paths
        return wp_normalize_path($retval);
    }

    static function trim_preceding_slash($str)
    {
        return preg_replace("#^/{1,2}#", "", $str, 1);
    }

    /**
     * @param string $module_id
     *
     * @return string $dir
     */
    static function get_static_override_dir($module_id = NULL)
    {
        $root = trailingslashit(path_join(WP_CONTENT_DIR, 'ngg'));
        if (!@file_exists($root) && is_writable(trailingslashit(WP_CONTENT_DIR)))
            wp_mkdir_p($root);

        $modules = trailingslashit(path_join($root, 'modules'));

        if (!@file_exists($modules) && is_writable($root))
            wp_mkdir_p($modules);

        if ($module_id)
        {
            $module_dir = trailingslashit(path_join($modules, $module_id));
            if (!@file_exists($module_dir) && is_writable($modules))
                wp_mkdir_p($module_dir);

            $static_dir = trailingslashit(path_join($module_dir, 'static'));
            if (!@file_exists($static_dir) && is_writable($module_dir))
                wp_mkdir_p($static_dir);

            return $static_dir;
        }

        return $modules;
    }
}

new M_Static_Assets;