<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Class to handle ajax endpoints, specifically used by vue components
 * If possible, keep logic here to a minimum.
 */
class MetaSlider_Api
{
    /**
     * Theme instance
     *
     * @var object
     * @see get_instance()
     */
    protected static $instance = null;

    /**
     * @var \MetaSlider_Slideshows
     */
    public $slideshows;

    /**
     * @var \MetaSlider_Themes|object|null
     */
    public $themes;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Setup
     */
    public function setup()
    {
        $this->slideshows = new MetaSlider_Slideshows();
        $this->themes = MetaSlider_Themes::get_instance();
    }

    /**
     * Used to access the instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register routes for admin ajax. Even if not used these can still be available.
     */
    public function register_admin_ajax_hooks()
    {

        // Slideshows
        add_action('wp_ajax_ms_get_slideshows', array(self::$instance, 'get_slideshows'));
        add_action('wp_ajax_ms_list_slideshows', array(self::$instance, 'list_slideshows'));
        add_action('wp_ajax_ms_get_single_slideshow', array(self::$instance, 'get_single_slideshow'));
        add_action('wp_ajax_ms_get_legacy_slideshows', array(self::$instance, 'get_legacy_slideshows'));
        add_action('wp_ajax_ms_get_preview', array(self::$instance, 'get_preview'));
        add_action('wp_ajax_ms_get_slideshow_usage', array(self::$instance, 'get_slideshow_usage'));
        add_action('wp_ajax_ms_delete_slideshow', array(self::$instance, 'delete_slideshow'));
        add_action('wp_ajax_ms_duplicate_slideshow', array(self::$instance, 'duplicate_slideshow'));
        add_action('wp_ajax_ms_save_slideshow', array(self::$instance, 'save_slideshow'));
        add_action('wp_ajax_ms_search_slideshows', array(self::$instance, 'search_slideshows'));
        add_action('wp_ajax_ms_export_slideshows', array(self::$instance, 'export_slideshows'));
        add_action('wp_ajax_ms_import_slideshows', array(self::$instance, 'import_slideshows'));

        // Themes
        add_action('wp_ajax_ms_get_all_free_themes', array(self::$instance, 'get_all_free_themes'));
        add_action('wp_ajax_ms_get_custom_themes', array(self::$instance, 'get_custom_themes'));
        add_action('wp_ajax_ms_get_theme_customization', array(self::$instance, 'get_theme_customization'));
        add_action('wp_ajax_ms_set_theme', array(self::$instance, 'set_theme'));

        // Slides
        add_action('wp_ajax_ms_import_images', array(self::$instance, 'import_images'));
        add_action('wp_ajax_ms_import_video', array(self::$instance, 'import_video'));
        add_action( 'wp_ajax_ms_import_others', array( self::$instance, 'import_others' ) );

        // Settings
        add_action('wp_ajax_ms_update_user_setting', array(self::$instance, 'save_user_setting'));
        add_action('wp_ajax_ms_campaign_response', array(self::$instance, 'save_campaign_response'));
        add_action('wp_ajax_ms_get_user_details', array(self::$instance, 'get_user_details'));
        add_action('wp_ajax_ms_update_all_slideshow_settings', array(self::$instance, 'save_all_slideshow_settings'));
        add_action('wp_ajax_ms_update_single_slideshow_setting', array(self::$instance, 'save_single_slideshow_setting'));
        add_action('wp_ajax_ms_get_slideshow_default_settings', array(self::$instance, 'get_slideshow_default_settings'));
        add_action('wp_ajax_ms_save_slideshow_default_settings', array(self::$instance, 'save_slideshow_default_settings'));

        // Global settings
        add_action('wp_ajax_ms_get_single_setting', array(self::$instance, 'get_single_setting'));
        add_action('wp_ajax_ms_get_global_settings', array(self::$instance, 'get_global_settings'));
        add_action('wp_ajax_ms_update_global_settings', array(self::$instance, 'save_global_settings'));
        add_action('wp_ajax_ms_update_global_settings_single', array(self::$instance, 'save_global_settings_single'));

        /* Ask MetaSlider help panel
         * @since 3.113.0
         */

        // Other
        add_action('wp_ajax_set_tour_status', array(self::$instance, 'set_tour_status'));
        add_action('wp_ajax_ms_get_image_ids_from_filenames', array(self::$instance, 'get_image_ids_from_file_name'));

        /* Pro settings
         * @since 3.62
         */
        add_action('wp_ajax_ms_get_pro_settings', array(self::$instance, 'get_pro_settings'));
        add_action('wp_ajax_ms_update_pro_settings', array(self::$instance, 'save_pro_settings'));
    }

    /**
     * Helper function to verify access
     *
     * @return boolean
     */
    public function can_access()
    {
        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);

        // Check for the nonce on the server (used by WP REST)
        if (isset($_SERVER['HTTP_X_WP_NONCE']) && wp_verify_nonce(sanitize_key($_SERVER['HTTP_X_WP_NONCE']), 'wp_rest')) {
            return current_user_can($capability);
        }

        // This is for when not using Axios (example: callout.php)
        if (isset($_REQUEST['METASLIDER_NONCE']) && wp_verify_nonce(sanitize_key($_REQUEST['METASLIDER_NONCE']), 'metaslider_request')) {
            return current_user_can($capability);
        }

        return false;
    }

    /**
     * Helper function to return an access denied response
     */
    public function deny_access()
    {
        wp_send_json_error(array(
            'message' => __('Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider')
        ), 401);
    }

    /**
     * Helper function to get data from the request
     * (supports rest & admin-ajax)
     * Does not handle any validation
     *
     * @param object $request    The request
     * @param array  $parameters The wanted parameters
     * @return array
     */
    public function get_request_data($request, $parameters)
    {
        $results = array();
        foreach ($parameters as $param) {
            if (method_exists($request, 'get_param')) {
                $results[$param] = $request->get_param($param);
            } else {
                $results[$param] = isset($_REQUEST[$param]) ? stripslashes_deep(sanitize_text_field($_REQUEST[$param])) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            }
        }

        return $results;
    }

    /**
     * Returns all slideshows
     *
     * @param object|null $request The request
     */
    public function get_slideshows($request = null)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('page', 'count'));
        $page = isset($data['page']) ? intval($data['page']) : 1;
        $count = isset($data['count']) ? intval($data['count']) : 25;

        $slideshows = $this->slideshows->get($count, $page);

        $slideshows = array_map(array($this, 'get_slide_data'), $slideshows);

        if (is_wp_error($slideshows)) {
            wp_send_json_error(array(
                'message' => $slideshows->get_error_message()
            ), 400);
        }

        wp_send_json_success($slideshows, 200);
    }

    /**
     * Returns all slideshows
     *
     * @param object|null $request The request
     */
    public function search_slideshows($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('term', 'count'));

        $slideshows = $this->slideshows->search((string) $data['term'], (string) $data['count']);

        $slideshows = array_map(array($this, 'get_slide_data'), $slideshows);

        if (is_wp_error($slideshows)) {
            wp_send_json_error(array(
                'message' => $slideshows->get_error_message()
            ), 400);
        }

        wp_send_json_success($slideshows, 200);
    }

    /**
     * Returns a list of all slideshows
     *
     * @param object|null $request The request
     */
    public function list_slideshows($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $slideshows = $this->slideshows->get_all_slideshows();

        if (is_wp_error($slideshows)) {
            wp_send_json_error(array(
                'message' => $slideshows->get_error_message()
            ), 400);
        }

        wp_send_json_success($slideshows, 200);
    }

    /**
     * Returns the content that uses a slideshow, for the "View Usage" window
     *
     * @since 3.114.1
     *
     * @param object|null $request The request
     */
    public function get_slideshow_usage($request = null)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id'));
        $html = MetaSlider_Admin_Table::get_posts_using_slideshow(absint($data['slideshow_id']));

        if (esc_html__('Not found.', 'ml-slider') === $html) {
            $html = esc_html__('This slideshow is not used in any published content.', 'ml-slider');
        }

        wp_send_json_success(array(
            'html' => $html
        ), 200);
    }

    /**
     * Returns a single slideshow
     *
     * @param object $request The request
     */
    public function get_single_slideshow($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('id'));

        $slideshow = $this->slideshows->get_single($data['id']);

        $slideshow = array_map(array($this, 'get_slide_data'), $slideshow);

        if (is_wp_error($slideshow)) {
            wp_send_json_error(array(
                'message' => $slideshow->get_error_message()
            ), 400);
        }

        wp_send_json_success($slideshow, 200);
    }

    /**
     * Returns Nivo, Coin, Responsive Slideshows
     *
     * @param object|null $request The request
     */
    public function get_legacy_slideshows($request = null)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $count_sliders = $this->slideshows->get_legacy_slideshows();
        wp_send_json_success($count_sliders, 200);
    }

    /**
     * Maps some slide data to the slideshow
     *
     * @param array $slideshow - a slideshow
     *
     * @return array
     */
    private function get_slide_data($slideshow)
    {
        if (isset($slideshow['slides'])) {
            foreach ($slideshow['slides'] as $order => $slide_id) {
                $thumbnail_id = 'attachment' === get_post_type($slide_id) ? $slide_id : get_post_thumbnail_id($slide_id);
                $thumbnail_data = wp_get_attachment_image_src($thumbnail_id);

                unset($slideshow['slides'][$order]);
                $slideshow['slides'][$order] = array(
                    'id' => $slide_id,
                    'thumbnail' => isset($thumbnail_data[0]) ? $thumbnail_data['0'] : '',
                    'post_excerpt' => get_post_field('post_excerpt', $slide_id),
                    'order' => $order,
                    'meta' =>  array()
                );
                foreach (get_post_meta($slide_id) as $metakey => $value) {
                    $slideshow['slides'][$order]['meta'][$metakey] = $value[0];
                }
            }
            $slideshow['slides'] = array_values($slideshow['slides']);
        }

        return $slideshow;
    }

    /**
     * Returns all custom themes
     */
    public function get_custom_themes()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $themes = $this->themes->get_custom_themes();

        if (is_wp_error($themes)) {
            wp_send_json_error(array(
                'message' => $themes->get_error_message()
            ), 400);
        }

        wp_send_json_success($themes, 200);
    }

    /**
     * Returns theme customizations for a given slideshow
     * 
     * @since 3.91
     */
    public function get_theme_customization($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $manifest       = array();
        $data           = $this->get_request_data($request, array('slideshow_id', 'theme', 'type'));
        $slideshow_id   = absint($data['slideshow_id']);
        $folder         = sanitize_key($data['theme']);
        $type           = sanitize_text_field($data['type']);
        $settings       = get_post_meta($slideshow_id, 'ml-slider_settings', true);

        // If is not a free theme, maybe override $manifest path
        if ($type !== 'free') {
            /**
             * Check if we have extra themes/ folders added from external sources,
             * including MetaSlider Slideshow Pro 
             * 
             * e.g. 
             * array(
             *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
             *  '/path/to/wp-content/themes/my-theme/ms-themes/'
             * )
             */
            $extra_themes = apply_filters('metaslider_extra_themes', array());

            foreach ($extra_themes as $location) {
                // Check if customize.php file that belongs to $folder as theme name (lowercase) exists
                if (file_exists($customize_file = trailingslashit($location) . trailingslashit($folder) . 'customize.php')) {
                    // Get the data from customize.php files
                    $manifest = $this->themes->add_base_customize_settings_single(
                        $folder, $customize_file
                    );
                    break;
                }
            }
        } else {
            // Get the data from customize.php files
            $manifest = $this->themes->add_base_customize_settings_single($folder);
        }
        
        $data = array(
            'saved_settings' => isset($settings['theme_customize']) 
                                && is_array($settings['theme_customize']) 
                                && count($settings['theme_customize']) > 0 
                                ? $settings['theme_customize'] : false,
            'manifest' => $manifest
        );

        if (is_wp_error($data)) {
            wp_send_json_error(array(
                'message' => $data->get_error_message()
            ), 400);
        }

        wp_send_json_success($data, 200);
    }

    /**
     * Returns all themes
     */
    public function get_all_free_themes()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $themes = $this->themes->get_all_free_themes();

        if (is_wp_error($themes)) {
            wp_send_json_error(array(
                'message' => $themes->get_error_message()
            ), 400);
        }

        wp_send_json_success($themes, 200);
    }

    /**
     * Sets a specific theme
     *
     * @param object $request The request
     */
    public function set_theme($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'theme'));

        $response = $this->themes->set(absint($data['slideshow_id']), (array) $data['theme']);

        if (!$response) {
            wp_send_json_error(array(
                'message' => 'There was an issue while attempting to save the theme. Please refresh and try again.'
            ), 400);
        }

        // If we made it this far, return the theme data
        wp_send_json_success((array) $data['theme'], 200);
    }

    /**
     * Returns the preview HTML
     *
     * @param object $request The request
     */
    public function get_preview($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'theme_id'));

        // The theme id can be either a string or null, exit if it's something else
        if (!is_null($data['theme_id']) && !is_string($data['theme_id'])) {
            wp_send_json_error(array(
                'message' => __('The request format was not valid.', 'ml-slider')
            ), 415);
        }

        // If the slideshow was deleted
        $slideshow = get_post($data['slideshow_id']);
        if ('publish' !== $slideshow->post_status) {
            wp_send_json_error(array(
                'message' => __('This slideshow is no longer available.', 'ml-slider')
            ), 410);
        }

        $html = $this->slideshows->preview(
            absint($data['slideshow_id']),
            $data['theme_id']
        );

        if (is_wp_error($html)) {
            wp_send_json_error(array(
                'message' => $html->get_error_message()
            ), 400);
        }

        wp_send_json_success($html, 200);
    }

    /**
     * Duplicate a slideshow
     *
     * @param object $request The request
     */
    public function duplicate_slideshow($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id'));

        $new_slideshow = $this->slideshows->duplicate(absint($data['slideshow_id']));

        if (is_wp_error($new_slideshow)) {
            wp_send_json_error(array(
                'message' => $new_slideshow->get_error_message()
            ), 400);
        }

        wp_send_json_success($new_slideshow, 200);
    }

    /**
     * Export slideshows
     *
     * @param object $request The request
     */
    public function export_slideshows($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_ids'));

        $export = $this->slideshows->export((array) json_decode($data['slideshow_ids'], true));

        if (is_wp_error($export)) {
            wp_send_json_error(array(
                'message' => $export->get_error_message()
            ), 400);
        }

        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            // @codingStandardsIgnoreLine
            echo wp_json_encode($export, JSON_PRETTY_PRINT);
            die;
        }

        wp_send_json($export, 200);
    }

    /**
     * Import slideshows
     *
     * @param object $request The request
     */
    public function import_slideshows($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshows'));

        if (!$data['slideshows']) {
            wp_send_json_error(array(
                'message' => __('Nothing to import.', 'ml-slider')
            ), 400);
        }

        $status = $this->slideshows->import((array) json_decode($data['slideshows'], true));

        if (is_wp_error($status)) {
            wp_send_json_error(array(
                'message' => $status->get_error_message()
            ), 400);
        }

        wp_send_json_success($status, 200);
    }

    /**
     * Delete a slideshow
     *
     * @param object $request The request
     */
    public function delete_slideshow($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'slider_id', 'nonce'));

        // Validate nonce
        if (! isset($data['nonce']) 
            || empty($data['nonce']) 
            || ! wp_verify_nonce(sanitize_key($data['nonce']), 'metaslider_delete_slider')
        ) {
            wp_send_json_error(array(
                'message' => 'There was an error. Invalid nonce token.'
            ), 400);
        }

        // Backwards compatability for slider_id param
        $slideshow_id = is_null($data['slideshow_id']) ? $data['slider_id'] : $data['slideshow_id'];

        // If the slideshow was deleted
        $slideshow = get_post($slideshow_id);
        if ('publish' !== $slideshow->post_status) {
            wp_send_json_error(array(
                'message' => __('This slideshow is no longer available.', 'ml-slider')
            ), 410);
        }

        // Confirm it's one of ours
        if ('ml-slider' !== get_post_type($slideshow_id)) {
            wp_send_json_error(array(
                'message' => __('This was not a slideshow, so we cannot delete it.', 'ml-slider')
            ), 409);
        }

        $next_slideshow = $this->slideshows->delete(absint($slideshow_id));

        if (is_wp_error($next_slideshow)) {
            wp_send_json_error(array(
                'message' => 'There was an issue while attempting delete the slideshow. Please refresh and try again.'
            ), 400);
        }

        wp_send_json_success($next_slideshow, 200);
    }

    /**
     * Save a slideshow
     *
     * @since 3.114.0 Only saves slides the user can edit, in a slideshow they can edit
     *
     * @param object $request The request
     */
    public function save_slideshow($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'attachment', 'count'));

        metaslider_verify_slideshow_for_ajax(absint($data['slideshow_id']));

        // Check every slide before saving any, so a refused slide doesn't leave the slideshow half saved
        if (isset($data['attachment']) && is_array($data['attachment'])) {
            foreach (array_keys($data['attachment']) as $slide_id) {
                metaslider_verify_slide_for_ajax((int) $slide_id);
            }
        }

        // If we are missing the count, then it's likely the payload was truncated
        if (!$data['count']) {
            // If the input vars count is close to the max allowed, assume that the data was truncated and inform the user to increase the value
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $current_vars = count($_POST, COUNT_RECURSIVE);
            if (($current_vars + 50) > ini_get('max_input_vars')) { // phpcs:ignore PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
                wp_send_json_error(array(
                    'current_input_vars' => $current_vars
                ), 400);
            }
        }

        try {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (isset($data['attachment'])) {
                foreach ($data['attachment'] as $slide_id => $fields) {
                    $fields = $this->sanitize_files_array($fields);

                    $slide_id = (int)$slide_id;

                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    do_action("metaslider_save_{$fields['type']}_slide", $slide_id, $data['slideshow_id'], $fields);
                }
            }
        } catch (Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()), 400);
        }

        wp_send_json_success('OK', 200);
    }

    private function sanitize_files_array($fields)
    {
        if (isset($fields['caption_source'])) {
            $fields['caption_source'] = sanitize_text_field($fields['caption_source']);
        }

        if (isset($fields['post_excerpt'])) {
            $fields['post_excerpt'] = $this->cleanup_content_kses( $fields['post_excerpt'] );
        }

        if (isset($fields['url'])) {
            $fields['url'] = sanitize_url($fields['url']);
        }

        if (isset($fields['inherit_image_title'])) {
            $fields['inherit_image_title'] = sanitize_text_field($fields['inherit_image_title']);
        }

        if (isset($fields['title'])) {
            $fields['title'] = sanitize_text_field($fields['title']);
        }

        if (isset($fields['inherit_image_alt'])) {
            $fields['inherit_image_alt'] = sanitize_text_field($fields['inherit_image_alt']);
        }

        if (isset($fields['alt'])) {
            $fields['alt'] = sanitize_text_field($fields['alt']);
        }

        if (isset($fields['link-alt'])) {
            $fields['link-alt'] = sanitize_text_field($fields['link-alt']);
        }

        if (isset($fields['crop_position'])) {
            $fields['crop_position'] = sanitize_text_field($fields['crop_position']);
        }

        if (isset($fields['type'])) {
            $fields['type'] = sanitize_text_field($fields['type']);
        }

        if (isset($fields['menu_order'])) {
            $fields['menu_order'] = sanitize_text_field($fields['menu_order']);
        }

        if (isset($fields['new_window'])) {
            $fields['new_window'] = sanitize_text_field($fields['new_window']);
        }

        if (isset($fields['hide_slide_smartphone'])) {
            $fields['hide_slide_smartphone'] = sanitize_text_field($fields['hide_slide_smartphone']);
        }

        if (isset($fields['hide_slide_tablet'])) {
            $fields['hide_slide_tablet'] = sanitize_text_field($fields['hide_slide_tablet']);
        }

        if (isset($fields['hide_slide_laptop'])) {
            $fields['hide_slide_laptop'] = sanitize_text_field($fields['hide_slide_laptop']);
        }

        if (isset($fields['hide_slide_desktop'])) {
            $fields['hide_slide_desktop'] = sanitize_text_field($fields['hide_slide_desktop']);
        }

        if (isset($fields['hide_caption_smartphone'])) {
            $fields['hide_caption_smartphone'] = sanitize_text_field($fields['hide_caption_smartphone']);
        }

        if (isset($fields['hide_caption_tablet'])) {
            $fields['hide_caption_tablet'] = sanitize_text_field($fields['hide_caption_tablet']);
        }

        if (isset($fields['hide_caption_laptop'])) {
            $fields['hide_caption_laptop'] = sanitize_text_field($fields['hide_caption_laptop']);
        }

        if (isset($fields['hide_caption_desktop'])) {
            $fields['hide_caption_desktop'] = sanitize_text_field($fields['hide_caption_desktop']);
        }
        
        return $fields;
    }

    /**
     * Sanitize HTML and avoid rgb() color being stripped by wp_filter_post_kses
     * 
     * @since 3.62
     * 
     * @param html $content
     * 
     * @return html
     */
    public function cleanup_content_kses( $content ) {

        // Remove any script tag instance
        $content = preg_replace( '/<script[^>]*>.*?<\/script>/', '', $content );
    
        return $content;
    }

    /**
     * Update tour status
     *
     * @param object $request The request
     */
    public function set_tour_status($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('current_step'));

        // This wont provide a useful return
        update_option('metaslider_tour_cancelled_on', $data['current_step']);

        wp_send_json_success('OK', 200);
    }

    /**
     * Update legacy notification status
     *
     * @param object $request The request
     */
    public function set_legacy_notification($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('notif_status'));
        update_option('metaslider_legacy_notification', $data['notif_status']);

        wp_send_json_success('OK', 200);
    }

    /**
     * Update a single seting specific to a slideshow
     *
     * @param object $request The request
     */
    public function save_single_slideshow_setting($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'setting_key', 'setting_value'));

        // Confirm it's one of ours
        if ('ml-slider' !== get_post_type($data['slideshow_id'])) {
            wp_send_json_error(array(
                'message' => __('This was not a slideshow, so we cannot update the setting.', 'ml-slider')
            ), 409);
        }

        // If it's the title, process
        if ('title' === $data['setting_key']) {
            wp_update_post(array(
                'ID' => absint($data['slideshow_id']),
                'post_title'  => sanitize_text_field( $data['setting_value'] )
            ));
            return wp_send_json_success('OK', 200);
        }

        // To save other data different to 'title' add the cases below...

        wp_send_json_success('OK', 200);
    }

    /**
     * Update all settings on a slideshow
     *
     * @param object $request The request
     */
    public function save_all_slideshow_settings($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'settings'));

        $result = $this->slideshows->save(
            absint($data['slideshow_id']),
            (array) $data['settings']
        );

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => 'There was an issue while attempting delete the slideshow. Please refresh and try again.'
            ), 400);
        }

        wp_send_json_success($result, 200);
    }

    /**
     * Update a global user setting
     *
     * @param object $request The request
     */
    public function save_user_setting($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('setting_key', 'setting_value'));

        // Ensure the key is prefixed
        $key = $data['setting_key'];
        $key = (0 === strpos($key, 'metaslider_')) ? $key : 'metaslider_' . $key;

        // This will not provide a useful return (reminder, key is prefixed)
        update_user_option(get_current_user_id(), $key, $data['setting_value']);

        wp_send_json_success('OK', 200);
    }

    /**
     * Record the site's answer to a campaign popup
     *
     * @since 3.114.1
     *
     * @param object $request The request
     */
    public function save_campaign_response($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        // Matches MetaSlider_Campaigns::for_editor(): only users who see the popup can answer it
        if (!current_user_can(MetaSlider_Campaigns::CAPABILITY)) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('campaign', 'response'));

        $response = is_scalar($data['response']) ? sanitize_key((string) $data['response']) : '';

        if (!is_scalar($data['campaign']) || !MetaSlider_Campaigns::respond((string) $data['campaign'], $response)) {
            wp_send_json_error(array('message' => __('This answer could not be saved.', 'ml-slider')), 400);
        }

        wp_send_json_success('OK', 200);
    }

    /**
     * Get current user's email for optin email
     */
    public function get_user_details()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $current_user = wp_get_current_user();
        wp_send_json_success($current_user->user_email, 200);
    }

    /**
     * Get default settings
     */
    public function get_slideshow_default_settings()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }
        wp_send_json_success(MetaSlider_Slideshow_Settings::defaults(), 200);
    }

    /**
     * Save setting defaults
     *
     * @param object $request The request
     */
    public function save_slideshow_default_settings($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('settings'));
        $settings = json_decode($data['settings'], true);

        // These settings should include a title and not be empty
        if (!isset($settings['title'])) {
            wp_send_json_error(array('message' => __('The title cannot be empty.', 'ml-slider')), 422);
        }
        foreach ($settings as $key => $value) {
            if ($value === '') {
                wp_send_json_error(array('message' => sprintf(__('The field (%s) cannot be empty', 'ml-slider'), $key)), 422);
            }
            //convert mobile setting to integer
            if ($key == 'smartphone' || $key == 'tablet' || $key == 'laptop' || $key == 'desktop') {
                $settings[$key] = (int)$value;
            }
        }

        // This will not provide a useful return
        update_option('metaslider_default_settings', $settings);

        wp_send_json_success($settings, 200);
    }

    /**
     * Get a single setting
     *
     * @param object $request The request
     */
    public function get_single_setting($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }
        $data = $this->get_request_data($request, array('setting'));

        $key = (0 === strpos($data['setting'], 'metaslider_')) ? $data['setting'] : 'metaslider_' . $data['setting'];
        if ($setting = get_option($key)) {
            return wp_send_json_success($setting, 200);
        }

        wp_send_json_success(array(), 200);
    }

    /**
     * Get global settings whether multisite or not
     */
    public function get_global_settings()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        if (is_multisite() && $settings = get_site_option('metaslider_global_settings')) {
            return wp_send_json_success($settings, 200);
        }

        if ($settings = get_option('metaslider_global_settings')) {
            return wp_send_json_success($settings, 200);
        }

        wp_send_json_success(array(), 200);
    }

    /**
     * Save global settings whether multisite or other
     *
     * @param object $request The request
     *
     * @since 3.112.0-beta.4 Added unsplashImageQuality/pixabayImageQuality/pixabayVideoQuality (#2365)
     * @since 3.114.1 Added dashboardUsageOnClick (#2613)
     */
    public function save_global_settings($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('settings'));
        $raw  = json_decode($data['settings'], true);

        if (!is_array($raw)) {
            wp_send_json_error('Invalid settings format.', 400);
            return;
        }

        $settings = array(
            'license'         => isset($raw['license'])         ? sanitize_text_field($raw['license'])  : '',
            // Keep the stored value when optIn isn't sent - only the sign-up popup sends it (#2541)
            'optIn'           => isset($raw['optIn'])            ? (bool) $raw['optIn']                  : MetaSlider_Email_Collection::site_is_optin(),
            'adminBar'        => isset($raw['adminBar'])         ? (bool) $raw['adminBar']               : true,
            'editLink'        => isset($raw['editLink'])         ? (bool) $raw['editLink']               : false,
            'legacy'          => isset($raw['legacy'])           ? (bool) $raw['legacy']                 : true,
            'newSlideOrder'   => isset($raw['newSlideOrder'])    ? sanitize_key($raw['newSlideOrder'])    : 'last',
            'mobileSettings'  => isset($raw['mobileSettings'])  ? (bool) $raw['mobileSettings']         : true,
            'legacyWidget'    => isset($raw['legacyWidget'])     ? (bool) $raw['legacyWidget']           : true,
            'tinyMce'         => isset($raw['tinyMce'])          ? (bool) $raw['tinyMce']                : true,
            'fixTouchSwipe'   => isset($raw['fixTouchSwipe'])    ? (bool) $raw['fixTouchSwipe']          : false,
            'autoThemeConfig' => isset($raw['autoThemeConfig'])  ? (bool) $raw['autoThemeConfig']        : true,
            'dashboardUsageOnClick' => isset($raw['dashboardUsageOnClick']) ? (bool) $raw['dashboardUsageOnClick'] : false,
            // sanitize_key() would lowercase 'ID' to 'id', breaking both the
            // select's display and WP_Query's case-sensitive orderby match.
            'dashboardSort'   => isset($raw['dashboardSort']) && in_array($raw['dashboardSort'], array('ID', 'post_title', 'post_date'), true)
                ? $raw['dashboardSort']
                : 'ID',
            'dashboardOrder'  => isset($raw['dashboardOrder'])   ? sanitize_key($raw['dashboardOrder'])   : 'asc',
            'dashboardItems'  => isset($raw['dashboardItems'])   ? absint($raw['dashboardItems'])         : 10,
            // Quality/size tiers picked when importing an image/video from these external
            // services - each list is that service's own real, finite vocabulary, so an
            // allowlist (rather than sanitize_key()) is used to reject anything else outright.
            // 'raw'/'full' are deliberately excluded from Unsplash's list - both can exceed what
            // WordPress can actually process (up to ~49MP), and WordPress would just scale them
            // back down to 2560px anyway (big_image_size_threshold, #2365).
            'unsplashImageQuality' => isset($raw['unsplashImageQuality']) && in_array($raw['unsplashImageQuality'], array('optimized', 'regular'), true)
                ? $raw['unsplashImageQuality']
                : 'optimized',
            // fullHDURL/imageURL deliberately excluded here (unlike the per-photo picker) - both
            // are only sometimes present depending on the photo/API key, so they're not safe to
            // rely on as an always-available global default.
            'pixabayImageQuality' => isset($raw['pixabayImageQuality']) && in_array($raw['pixabayImageQuality'], array('webformatURL', 'largeImageURL'), true)
                ? $raw['pixabayImageQuality']
                : 'largeImageURL',
            'pixabayVideoQuality' => isset($raw['pixabayVideoQuality']) && in_array($raw['pixabayVideoQuality'], array('tiny', 'small', 'medium', 'large'), true)
                ? $raw['pixabayVideoQuality']
                : 'small',
        );

        // TODO: validate the license if not ''

        $was_optin = MetaSlider_Email_Collection::site_is_optin();

        update_option('metaslider_global_settings', $settings, true);

        // for legacy library
        update_option('metaslider_new_user', 'old');

        // Hand the collected email to the connect service. This no-ops when the
        // same address was already sent, so it's safe to call on every save.
        $response = $settings;

        if ($settings['optIn']) {
            // Reported back so the opt-in modal can tell the user whether to go
            // and look for their confirmation email, and so the whole exchange
            // with the connect service is visible to the browser
            $optin = MetaSlider_Email_Collection::optin();
            $response['optinEmailSent'] = $optin['status'];

            // A failed sign-up must not opt the site in, or the popup never asks again (#2529)
            if (!$was_optin && !in_array($optin['status'], array('subscribed', 'duplicate'), true)) {
                $settings['optIn'] = false;
                $response['optIn'] = false;
                update_option('metaslider_global_settings', $settings, true);
            }

            // The full exchange with the connect service, shown in the opt-in
            // modal for debugging. Off by default - it names internal
            // endpoints and is only useful while developing. Force it on the
            // same way as metaslider_always_show_optin_notice, e.g. from
            // wp-config.php: add_filter('metaslider_always_show_connect_report', '__return_true');
            if (apply_filters('metaslider_always_show_connect_report', false)) {
                $response['optinReport'] = $optin;
            }
        }

        wp_send_json_success($response, 200);
    }

    /**
     * Update a global single setting
     *
     * @param object $request The request
     */
    public function save_global_settings_single($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('setting_key', 'setting_value'));

        $allowed = array(
            'optin_via'   => 'sanitize_key',
            'optin_email' => 'sanitize_email',
        );

        $key = $data['setting_key'];

        if (!array_key_exists($key, $allowed)) {
            wp_send_json_error('Invalid setting key.', 400);
            return;
        }

        $value = call_user_func($allowed[$key], $data['setting_value']);

        // sanitize_email() silently reduces garbage input to '', which would
        // otherwise get stored and fail validation downstream. Reject it here
        // instead, unless they're intentionally clearing the field.
        if ('optin_email' === $key && '' !== $data['setting_value'] && ! is_email($value)) {
            wp_send_json_error('Please enter a valid email address.', 400);
            return;
        }

        update_option('metaslider_' . $key, $value, true);

        // Clearing the address while still opted in would otherwise leave
        // optIn true with nothing to send - the next unrelated settings save
        // would find no valid email and report 'invalid' with nothing to show
        // for it. Opting out here as well keeps that combination from existing.
        if ('optin_email' === $key && '' === $value) {
            $settings = get_option('metaslider_global_settings');
            if (is_array($settings) && !empty($settings['optIn'])) {
                $settings['optIn'] = false;
                update_option('metaslider_global_settings', $settings, true);
            }
        }

        // Submitting the opt-in form is a deliberate request to subscribe, so
        // clear the "already handed off" marker and let the save that follows
        // send a fresh confirmation. Without this, anyone re-opting in after
        // losing the email (or being removed at the Mailjet end) would be told
        // to check an inbox that never receives anything.
        if ('optin_email' === $key) {
            delete_option('metaslider_optin_email_sent');
            // A deliberate retry from the popup shouldn't wait out the failure pause (#2536)
            delete_transient(MetaSlider_Email_Collection::retry_cooldown_key($value));
        }

        wp_send_json_success('OK', 200);
    }

    /**
     * Get ids of images based on filename. Used during import
     *
     * @deprecated 3.103 - We no longer use filenames to identify images during import
     *
     * @param object $request The request
     */
    public function get_image_ids_from_file_name($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('filenames'));
        $images = MetaSlider_Image::get_image_ids_from_file_name(json_decode($data['filenames'], true));
        wp_send_json_success($images, 200);
    }

    /**
     * Get pro settings whether multisite or not
     * 
     * @since 3.62
     */
    public function get_pro_settings()
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        if (is_multisite() && $settings = get_site_option('metaslider_pro_settings')) {
            return wp_send_json_success($settings, 200);
        }

        if ($settings = get_option('metaslider_pro_settings')) {
            return wp_send_json_success($settings, 200);
        }

        wp_send_json_success(array(), 200);
    }

    /**
     * Save pro settings whether multisite or other
     *
     * @since 3.62
     * 
     * @param object $request The request
     */
    public function save_pro_settings($request)
    {
        if ( ! $this->can_access() ) {
            $this->deny_access();
        }

        $data = $this->get_request_data( $request, array( 'settings' ) );
        $settings = json_decode( $data['settings'], true );

        // Supported types: 'number' (int), 'text' (string)
        $fields_label = array(
            'postFeedFields' => __( 'Maximum Number of Custom Field in Post Feed Sliders', 'ml-slider' )
        );

        // These settings should not be empty and be the type defined in $fields_check
        foreach ($settings as $key => $value) {

            if ( $value === '' ) {
                wp_send_json_error(
                    array(
                        'message' => sprintf(
                        __( 'The field (%s) cannot be empty', 'ml-slider' ), 
                        $fields_label[$key]  
                    )
                ), 422 );
            }
        }

        update_option('metaslider_pro_settings', $settings, true);

        wp_send_json_success($settings, 200);
    }

    /**
     * Import a downloaded video file (e.g. a picked Pixabay video) and create a media
     * library attachment for it. Unlike import_images(), this never attaches the result to
     * any slide - the client (the Local Video module) wires the returned attachment ID into
     * its own postmeta/AJAX actions afterwards, since a video's postmeta model
     * (repeatable ml-slider_video_id) is slide-type specific and not something this
     * generic endpoint should know about.
     *
     * @since 3.112
     * 
     * @param object $request The request
     */
    public function import_video($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('image_data'));

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- REST route access is checked by can_access() above.
        if (empty($_FILES['files']['tmp_name'])) {
            wp_send_json_error(array('message' => __('No video file was received by the server.', 'ml-slider')), 400);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- REST route access is checked by can_access(), and uploaded files are validated by process_uploads().
        $videos = $this->process_uploads($_FILES['files'], $data['image_data'], 'video/');

        if (empty($videos)) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- REST route access is checked by can_access(); read-only, used to build a diagnostic message, never persisted or output unescaped.
            $filename = isset($_FILES['files']['name']) ? reset($_FILES['files']['name']) : '';
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- REST route access is checked by can_access(); this is a PHP-generated temp file path, validated by wp_check_filetype_and_ext() below, not user text to sanitize.
            $tmp_name = isset($_FILES['files']['tmp_name']) ? reset($_FILES['files']['tmp_name']) : '';
            $filetype = $tmp_name ? wp_check_filetype_and_ext($tmp_name, $filename) : array('type' => false);

            wp_send_json_error(array(
                'message' => sprintf(
                    /* translators: 1: uploaded filename, 2: mime type WordPress detected, or "none" */
                    __('The video "%1$s" was rejected before upload (detected type: %2$s). It may be an unsupported format or exceed this server\'s upload limit.', 'ml-slider'),
                    sanitize_text_field($filename),
                    $filetype['type'] ? $filetype['type'] : __('none', 'ml-slider')
                )
            ), 400);
        }

        $attachment_ids = MetaSlider_Image::instance()->import($videos, null, array(), 'video/');
        if (is_wp_error($attachment_ids) || empty($attachment_ids)) {
            wp_send_json_error(array(
                'message' => is_wp_error($attachment_ids) ? $attachment_ids->get_error_message() : __('The video could not be imported.', 'ml-slider')
            ), 400);
        }

        wp_send_json_success(array('attachment_id' => $attachment_ids[0]), 200);
    }

    /**
     * Import theme images
     *
     * @since 3.113.0 Each row now reports the attachment_id it imported
     * @since 3.113.0 A slide_type other than 'image' only imports the attachment and hands it back
     *
     * @param object $request The request
     */
    public function import_images($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'theme_id', 'slide_id', 'slide_type', 'image_data', 'extra'));

        // Sanitize extra data
        if ( isset( $data['extra'] ) && is_array( $data['extra'] ) ) {
            $data['extra'] = array_map( 'sanitize_text_field', $data['extra'] );
        }

        // Create a slideshow if one doesn't exist
        if (is_null($data['slideshow_id']) || !absint($data['slideshow_id'])) {
            $data['slideshow_id'] = MetaSlider_Slideshows::create();
            if (is_wp_error($data['slideshow_id'])) {
                wp_send_json_error(array('message' => $data['slideshow_id']->getMessage()), 400);
            }
        }

        // If there are files here, then we need to prepare them
        // Dont use get_file_params() as it's WP4.4
        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- REST route access is checked by can_access(), and uploaded files are validated by process_uploads().
        $images = isset($_FILES['files']) ? $this->process_uploads($_FILES['files'], $data['image_data']) : array();

        // $images should be an array of image data at this point
        $image_ids = MetaSlider_Image::instance()->import($images, $data['theme_id'], $data['extra']);
        if (is_wp_error($image_ids)) {
            wp_send_json_error(array(
                'message' => $image_ids->get_error_message()
            ), 400);
        }

        // Only image slides are built here - any other new slide type (e.g. Pro's Layer Slide) is created by its own module from the attachment
        $slide_type = empty($data['slide_type']) ? 'image' : sanitize_key($data['slide_type']);
        if (is_null($data['slide_id']) && 'image' !== $slide_type) {
            $attachments = array();
            foreach ($image_ids as $image_id) {
                $attachments[] = array(
                    'attachment_id' => absint($image_id),
                    'slideshow_id' => absint($data['slideshow_id'])
                );
            }
            wp_send_json($attachments);
        }

        $errors = array();
        $html_rows = array();
        $method = is_null($data['slide_id']) ? 'create_slide' : 'update';
        foreach ($image_ids as $image_id) {
            $slide = new MetaSlider_Slide(absint($data['slideshow_id']), $data['slide_id']);
            $slide->add_image($image_id)->$method();

            if (is_wp_error($slide->error)) {
                array_push($errors, $slide->error);
            } else {
                $imageSlide = new MetaImageSlide();
                $imageSlide->set_slide( $slide->slide_id );

                $row = array(
                    'slide_id' => $slide->slide_id,
                    'slideshow_id' => absint( $data['slideshow_id'] ),
                    // Callers replacing media on an existing slide need this to keep their own
                    // "currently selected" state in sync - e.g. the Local Video cover button
                    'attachment_id' => absint( $image_id ),
                    'thumbnail_url_small' => $imageSlide->get_intermediate_image_src( 240 ),
                    'thumbnail_url_medium' => $imageSlide->get_intermediate_image_src( 768 ),
                    'thumbnail_url_large' => $imageSlide->get_intermediate_image_src( 1024 )
                );

                // Only the "add new slide" flow needs the full rendered row - the "replace existing
                // slide" path, and the other import/images callers, always full-page-reload and never
                // read this field, so skip the extra query + render cost for them.
                if ( 'create_slide' === $method ) {
                    $imageSlide->set_slider( absint( $data['slideshow_id'] ) );

                    // Match MetaImageSlide::add_slide()'s render for a brand new slide (used by the Media Library "Add Slide" flow too)
                    $imageSlide->settings['width'] = 0;
                    $imageSlide->settings['height'] = 0;

                    $row['html'] = $imageSlide->get_admin_slide();
                }

                $html_rows[] = $row;
            }
        }

        // Send back the first error, if any
        if (isset($errors[0])) {
            wp_send_json_error(array(
                'message' => $errors[0]->get_error_message()
            ), 400);
        }

        wp_send_json( $html_rows );
    }

    /**
     * Import others (aka Local videos, YouTube videos, Vimeo videos, etc.)
     *
     * @since 3.70
     * @deprecated 3.106 - Use MetaSlider_Slideshows->import() instead
     * 
     * @param object $request The request
     */
    public function import_others( $request )
    {
        if ( ! $this->can_access() ) {
            $this->deny_access();
        }

        $data = $this->get_request_data(
            $request, 
            array( 
                'slideshow_id',
                'slug'
            )
        );

        if ( empty( $data['slug'] ) || empty( $data['slideshow_id'] ) ) {
            wp_send_json_error( 
                array( 'message' => __('Import slug not found', 'ml-slider') ), 
                404 
            );
        }

        $slider_id  = intval( $data['slideshow_id'] );
        $slug       = (string) $data['slug'];
        $new_slides = array();

        if ( class_exists( 'MetaSliderPro_Quickstart' ) ) {
            $quickstart = new MetaSliderPro_Quickstart();
            $new_slides = $quickstart->import_slides( $slug, $slider_id );
        }

        if ( ! count( $new_slides ) ) {
            wp_send_json_error( 
                array( 'message' => __('Import data could not be processed', 'ml-slider') ), 
                404 
            );
        }

        wp_send_json( $new_slides );
    }

    /**
     * Verify uploads are useful and return an array with metadata
     * For now only handles images.
     *
     * @param array $files An array of the images
     * @param array $data  Data for the image, keys should match
     *
     * @return array An array with image data
     */
    public function process_uploads($files, $data = null, $mime_prefix = 'image/')
    {
        $images = array();
        foreach ($files['tmp_name'] as $index => $tmp_name) {
            // If there was an error, skip this file
            // TODO: consider reporting an error back to the user, but skipping might be best
            if (!empty($files['error'][$index])) {
                continue;
            }

            // If the name is empty or isn't an uploaded file, skip it
            if (empty($tmp_name) || !is_uploaded_file($tmp_name)) {
                continue;
            }

            // Verify the file matches the expected type. SVG is never allowed here regardless of
            // $mime_prefix - WP core already blocks it by default, this is defense-in-depth.
            $filetype = wp_check_filetype_and_ext( $tmp_name, $files['name'][$index] );
            if ( ! $filetype['type'] || strpos( $filetype['type'], $mime_prefix) !== 0 || 'image/svg+xml' === $filetype['type'] ) {
                continue;
            }

            // getimagesize() only applies to images - a video file would always fail it
            if ('image/' === $mime_prefix) {
                $image_info = @getimagesize( $tmp_name );
                if ( $image_info === false ) {
                    continue;
                }
            }

            // Ignore images too large for the server (According to WP)
            // The server probably handles this already
            // TODO: possibly provide user feedback, but skipping moves forward
            $max_upload_size = wp_max_upload_size();
            if (!$max_upload_size) {
                $max_upload_size = 0;
            }
            $file_size = $files['size'][$index];
            if ($file_size > $max_upload_size) {
                continue;
            }

            // Tests were passed, so move forward with this image
            $filename = $files['name'][$index];
            $images[$filename] = array(
                'source' => sanitize_text_field($tmp_name),
                'caption' => isset($data[$filename]['caption']) ? sanitize_text_field($data[$filename]['caption']) : '',
                'title' => isset($data[$filename]['title']) ? sanitize_text_field($data[$filename]['title']) : '',
                'description' => isset($data[$filename]['description']) ? sanitize_text_field($data[$filename]['description']) : '',
                'alt' => isset($data[$filename]['alt']) ? sanitize_text_field($data[$filename]['alt']) : ''
            );
        }
        return $images;
    }
}

if (class_exists('WP_REST_Controller')) :
    /**
     * Class to handle REST route api endpoints.
     */
    class MetaSlider_REST_Controller extends WP_REST_Controller
    {
        /**
         * Namespace and version for the API
         *
         * @var string
         */
        protected $namespace = 'metaslider/v1';

        /**
         * Constructor
         */
        public function __construct()
        {
            add_action('rest_api_init', array($this, 'register_routes'));
            $this->api = MetaSlider_Api::get_instance();
            $this->api->setup();
        }

        /**
         * Register routes
         */
        public function register_routes()
        {
            register_rest_route($this->namespace, '/slideshow/all', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/list', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'list_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/single', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_single_slideshow'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/legacy', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_legacy_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/preview', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_preview'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_slideshow'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/delete', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'delete_slideshow'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/duplicate', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'duplicate_slideshow'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/search', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'search_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/export', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'export_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/slideshow/import', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'import_slideshows'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/themes/all', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_all_free_themes'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/themes/custom', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_custom_themes'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/theme/customization', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_theme_customization'),
                'permission_callback' => array($this->api, 'can_access')
            )));
            register_rest_route($this->namespace, '/themes/set', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'set_theme'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/import/images', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'import_images'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/import/video', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'import_video'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/import/others', array(
                array(
                    'methods' => 'POST',
                    'callback' => array($this->api, 'import_others'),
                    'permission_callback' => array($this->api, 'can_access')
                )
            ));

            register_rest_route($this->namespace, '/tour/status', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'set_tour_status'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/user/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_user_setting'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/campaigns/response', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_campaign_response'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/user', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_user_details'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/global/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_global_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/global/single/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_global_settings_single'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/single', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_single_setting'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/global', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_global_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/slideshow/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_all_slideshow_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/slideshow/save-single', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_single_slideshow_setting'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/slideshow/defaults', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_slideshow_default_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/slideshow/defaults/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_slideshow_default_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/images/ids-from-filenames', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'get_image_ids_from_file_name'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            // Pro
            register_rest_route($this->namespace, '/settings/pro', array(array(
                'methods' => 'GET',
                'callback' => array($this->api, 'get_pro_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));

            register_rest_route($this->namespace, '/settings/pro/save', array(array(
                'methods' => 'POST',
                'callback' => array($this->api, 'save_pro_settings'),
                'permission_callback' => array($this->api, 'can_access')
            )));
        }
    }
endif;
