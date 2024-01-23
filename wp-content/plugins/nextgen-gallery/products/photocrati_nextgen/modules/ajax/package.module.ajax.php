<?php
/**
 * Class C_Ajax_Controller
 *
 * @implements I_Ajax_Controller
 */
class C_Ajax_Controller extends C_MVC_Controller
{
    public static $_instances = array();
    /**
     * Returns an instance of this class
     *
     * @param string|bool $context
     * @return C_Ajax_Controller
     */
    public static function get_instance($context = false)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Ajax_Controller($context);
        }
        return self::$_instances[$context];
    }
    public function define($context = false)
    {
        parent::define($context);
        $this->implement('I_Ajax_Controller');
    }
    public function index_action($return = false)
    {
        $retval = null;
        define('DOING_AJAX', true);
        // Inform the MVC framework what type of content we're returning.
        $this->set_content_type('json');
        // Start an output buffer to avoid displaying any PHP warnings/errors.
        ob_start();
        // Get the action requested & find and execute the related method.
        if ($action = $this->param('action')) {
            $method = "{$action}_action";
            if ($this->has_method($method)) {
                $retval = $this->call_method($method);
            } else {
                $retval = ['error' => 'Not a valid AJAX action'];
            }
        } else {
            $retval = ['error' => 'No action specified'];
        }
        // Flush the buffer.
        $buffer_limit = 0;
        $zlib = ini_get('zlib.output_compression');
        if (!is_numeric($zlib) && $zlib == 'On') {
            $buffer_limit = 1;
        } elseif (is_numeric($zlib) && $zlib > 0) {
            $buffer_limit = 1;
        }
        while (ob_get_level() != $buffer_limit) {
            ob_end_clean();
        }
        // Return the JSON to the browser.
        wp_send_json($retval);
    }
    public function validate_ajax_request($action = null, $token = false)
    {
        // Security::verify_nonce() is a wrapper to wp_verify_nonce().
        //
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if (true === $token && (!isset($_REQUEST['nonce']) || !\Imagely\NGG\Util\Security::verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), $action))) {
            return false;
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        return \Imagely\NGG\Util\Security::is_allowed($action);
    }
}