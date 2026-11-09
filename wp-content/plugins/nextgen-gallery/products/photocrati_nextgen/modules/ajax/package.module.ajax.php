<?php
/**
 * Class C_Ajax_Controller
 *
 * @implements I_Ajax_Controller
 */
class C_Ajax_Controller extends C_MVC_Controller
{
    /**
     * Instances cache.
     *
     * @var array
     */
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
    /**
     * Methods that are entry points of this controller rather than AJAX actions.
     *
     * Dispatching to one of these would re-enter the dispatcher with identical request
     * state and no exit condition, so they can never be dispatch targets -- not even if
     * a third party adds them through the 'ngg_ajax_actions' filter.
     *
     * Compared case-insensitively, because PHP resolves method names that way: POPE's
     * has_method()/call_method() both go through method_exists(), so 'Index' would
     * otherwise survive the filter and still reach index_action().
     *
     * @var string[]
     */
    const RESERVED_ACTIONS = array('index');
    /**
     * Returns the action names this endpoint is allowed to dispatch to.
     *
     * The endpoint is served on 'init' and so answers before authentication; it must
     * therefore resolve only names a module has explicitly registered, rather than any
     * method whose name happens to end in '_action'.
     *
     * @return string[]
     */
    public function get_dispatchable_actions()
    {
        /**
         * Filters the legacy AJAX actions this endpoint will dispatch to.
         *
         * Add-ons that mix an action into C_Ajax_Controller must also declare it here.
         * Registering a name here only makes it reachable -- it grants nothing. Each
         * action is still responsible for its own nonce and capability checks, and an
         * action that omits them is reachable unauthenticated, because this endpoint
         * answers on 'init'. (Of the defaults below, get_watermark_preview_url checks
         * the capability but not a nonce; see the follow-up on issue #1087.)
         *
         * @param string[] $actions Registered action names, without the '_action' suffix.
         */
        $actions = (array) apply_filters('ngg_ajax_actions', ['create_new_gallery', 'upload_image', 'browse_folder', 'import_folder', 'import_media_library', 'get_watermark_preview_url', 'get_legacy_featured_images_count', 'update_legacy_featured_images']);
        $reserved = array_map('strtolower', self::RESERVED_ACTIONS);
        $dispatchable = [];
        foreach ($actions as $action) {
            if (!is_string($action) || in_array(strtolower($action), $reserved, true)) {
                continue;
            }
            $dispatchable[] = $action;
        }
        return array_values(array_unique($dispatchable));
    }
    public function index_action($return_output = false)
    {
        $retval = null;
        if (!defined('DOING_AJAX')) {
            define('DOING_AJAX', true);
        }
        // Inform the MVC framework what type of content we're returning.
        $this->set_content_type('json');
        // Start an output buffer to avoid displaying any PHP warnings/errors.
        ob_start();
        // Get the action requested & find and execute the related method.
        $action = $this->param('action');
        if (!$action) {
            $retval = ['error' => 'No action specified'];
        } elseif (!is_string($action) || !in_array($action, $this->get_dispatchable_actions(), true)) {
            // The is_string() test rejects 'action[]=...' before it can be cast, which
            // would otherwise raise an "Array to string conversion" warning. The
            // in_array() comparison is strict and so case-sensitive by design: POPE
            // resolves method names case-insensitively, so anything but an exact match
            // here could dispatch a method this list never named.
            $retval = ['error' => 'Not a valid AJAX action'];
        } else {
            $method = "{$action}_action";
            $retval = $this->has_method($method) ? $this->call_method($method) : ['error' => 'Not a valid AJAX action'];
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