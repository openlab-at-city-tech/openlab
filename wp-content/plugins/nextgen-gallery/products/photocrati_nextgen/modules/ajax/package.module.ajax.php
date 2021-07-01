<?php
/**
 * Class C_Ajax_Controller
 * @implements I_Ajax_Controller
 */
class C_Ajax_Controller extends C_MVC_Controller
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Ajax_Controller');
    }
    function index_action($return = FALSE)
    {
        $retval = NULL;
        define('DOING_AJAX', TRUE);
        // Inform the MVC framework what type of content we're returning
        $this->set_content_type('json');
        // Start an output buffer to avoid displaying any PHP warnings/errors
        ob_start();
        // Get the action requested & find and execute the related method
        if ($action = $this->param('action')) {
            $method = "{$action}_action";
            if ($this->has_method($method)) {
                $retval = $this->call_method($method);
            } else {
                $retval = array('error' => 'Not a valid AJAX action');
            }
        } else {
            $retval = array('error' => 'No action specified');
        }
        // Flush the buffer
        $buffer_limit = 0;
        $zlib = ini_get('zlib.output_compression');
        if (!is_numeric($zlib) && $zlib == 'On') {
            $buffer_limit = 1;
        } else {
            if (is_numeric($zlib) && $zlib > 0) {
                $buffer_limit = 1;
            }
        }
        while (ob_get_level() != $buffer_limit) {
            ob_end_clean();
        }
        // Return the JSON to the browser
        wp_send_json($retval);
    }
    /**
     * Returns an instance of this class
     * @param string|bool $context
     * @return C_Ajax_Controller
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function validate_ajax_request($action = NULL, $token = FALSE)
    {
        if ($token === TRUE && (!isset($_REQUEST['nonce']) || !M_Security::verify_nonce($_REQUEST['nonce'], $action))) {
            return FALSE;
        }
        return M_Security::is_allowed($action);
    }
}