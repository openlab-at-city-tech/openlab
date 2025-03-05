<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

/**
 * View Class
 */
class WPCAView
{

    /**
     * Path to view template
     *
     * @var string
     */
    private $_path;

    /**
     * Parameters for view template
     *
     * @var array
     */
    private $_params;

    /**
     * Template content
     *
     * @var string
     */
    private $_content;

    /**
     * Constructor
     *
     * @since 1.0
     * @param   string    $path
     * @param   array     $params
     */
    private function __construct($path, $params = [])
    {
        $this->_path = $path;
        $this->_params = $params;
    }

    /**
     * Create instance of view
     *
     * @since 1.0
     * @param   string    $name
     * @param   array     $params
     * @return  WPCAView
     */
    public static function make($name, $params = [])
    {
        return new self($name, $params);
    }

    /**
     * Add possibility to set params dynamically
     *
     * @since 1.0
     * @param   string    $method
     * @param   array     $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'set' && count($args) == 1) {
            $this->_params[strtolower(substr($method, 2))] = $args[0];
            return $this;
        }
        return call_user_func_array($method, $args);
    }

    /**
     * Get path to file
     *
     * @since 1.0
     * @return  string
     */
    private function resolve_path()
    {
        if (stripos(strrev($this->_path), 'php.') === 0) {
            return $this->_path;
        }
        return WPCA_PATH.'view/'.str_replace('.', '/', $this->_path).'.php';
    }

    /**
     * Get content
     *
     * @since 1.0
     * @return  string
     */
    private function get_content()
    {
        if (!$this->_content) {
            ob_start();
            if ($this->_params) {
                extract($this->_params);
            }
            require($this->resolve_path());
            $this->_content = ob_get_clean();
        }
        return $this->_content;
    }

    /**
     * Render content
     *
     * @since 1.0
     * @return  void
     */
    public function render()
    {
        echo $this->get_content();
    }
}
