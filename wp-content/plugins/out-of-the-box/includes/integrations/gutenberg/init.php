<?php

namespace TheLion\OutoftheBox\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gutenberg block with live preview.
 */
class Gutenberg
{
    public function __construct()
    {
        if ($this->has_gutenberg()) {
            $this->hooks();
        }
    }

    /**
     * Check if Gutenberg is enabled.
     */
    public function has_gutenberg()
    {
        return function_exists('register_block_type');
    }

    /**
     * Load Gutenberg block assets for in editor.
     */
    public function enqueue_block_editor_assets()
    {
    }

    /**
     *  Register Gutenberg block, enqueue styles and set i18n.
     */
    public function register_block()
    {// phpcs:ignore
        wp_register_script(
            'wpcp-outofthebox-block-js',
            plugins_url('blocks.build.js', __FILE__),
            ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'],
            OUTOFTHEBOX_VERSION,
            true
        );

        // Register block editor styles for backend.
        wp_register_style(
            'wpcp-outofthebox-block-editor-css',
            plugins_url('blocks.editor.build.css', __FILE__),
            ['wp-edit-blocks'],
            OUTOFTHEBOX_VERSION
        );

        $i18n = [
            'title' => 'Dropbox',
            'description' => sprintf(\esc_html__('Insert your %s content', 'wpcloudplugins'), 'Dropbox'),
            'form_keywords' => [
                'cloud',
                'dropbox',
                'documents',
                'files',
                'upload',
                'video',
                'audio',
                'media',
            ],
            'create_shortcode' => \esc_html__('Build your shortcode', 'wpcloudplugins'),
            'open_shortcode_builder' => \esc_html__('Edit via Shortcode Builder', 'wpcloudplugins'),
            'edit_shortcode' => \esc_html__('Edit this shortcode via the Shortcode Builder or manually via the raw code', 'wpcloudplugins'),
            'updated_shortcode' => \esc_html__('Shortcode is succesfully updated!', 'wpcloudplugins'),
            'block_settings' => \esc_html__('Shortcode', 'wpcloudplugins'),
            'edit' => \esc_html__('Edit', 'wpcloudplugins'),
            'open_preview' => \esc_html__('Preview', 'wpcloudplugins'),
            'close_preview' => \esc_html__('Close Preview', 'wpcloudplugins'),
            'panel_notice_head' => \esc_html__('Heads up!', 'wpcloudplugins'),
            'panel_notice_text' => \esc_html__('Do not forget to test your shortcode on your site.', 'wpcloudplugins'),
        ];

        // WP Localized globals. Use dynamic PHP stuff in JavaScript via `wpcpOutoftheBoxGlobal` object.
        wp_localize_script(
            'wpcp-outofthebox-block-js',
            'wpcpOutoftheBoxGlobal',
            [
                'pluginDirPath' => plugin_dir_path(__DIR__),
                'pluginDirUrl' => plugin_dir_url(__DIR__),
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'i18n' => $i18n,
                'wpnonce' => \wp_create_nonce('wpcp-outofthebox-block'),
            ]
        );

        /*
         * Register Gutenberg block on server-side.
         *
         * Register the block on server-side to ensure that the block
         * scripts and styles for both frontend and backend are
         * enqueued when the editor loads.
         *
         */

        $attributes = [
            'shortcode' => [
                'type' => 'string',
            ],
            'className' => [
                'type' => 'string',
            ],
        ];

        register_block_type(
            'wpcp/outofthebox-block',
            [
                'attributes' => $attributes,
                // Enqueue blocks.style.build.css on both frontend & backend.
                //'style' => 'wpcp-outofthebox-block-style-css',
                // Enqueue blocks.build.js in the editor only.
                'editor_script' => 'wpcp-outofthebox-block-js',
                // Enqueue blocks.editor.build.css in the editor only.
                'editor_style' => 'wpcp-outofthebox-block-editor-css',
                'render_callback' => [$this, 'get_render_html'],
            ]
        );
    }

    /**
     * Get form HTML to display in a WPForms Gutenberg block.
     *
     * @param array $attr attributes passed by WPForms Gutenberg block
     *
     * @return string
     */
    public function get_render_html($attr)
    {
        $shortcode = !empty($attr['shortcode']) ? $attr['shortcode'] : false;

        if (empty($shortcode)) {
            return __('Please create your shortcode first', 'wpcloudplugins');
        }

        \ob_start();

        echo do_shortcode($shortcode);

        $content = \ob_get_clean();

        if (empty($content)) {
            return '';
        }

        return $content;
    }

    /**
     * Checking if is Gutenberg REST API call.
     *
     * @return bool true if is Gutenberg REST API call
     */
    public function is_gb_editor()
    {
        // TODO: Find a better way to check if is GB editor API call.
        return \defined('REST_REQUEST') && REST_REQUEST && !empty($_REQUEST['context']) && 'edit' === $_REQUEST['context']; // phpcs:ignore
    }

    /**
     * Add WP Cloud Plugins category to blocks.
     *
     * @param mixed $categories
     * @param mixed $post
     */
    public function create_block_category($categories, $post)
    {
        $category_slugs = wp_list_pluck($categories, 'slug');

        // Only add the category once
        return in_array('wpcp-blocks', $category_slugs, true) ? $categories : array_merge(
            $categories,
            [
                [
                    'slug' => 'wpcp-blocks',
                    'title' => 'WP Cloud Plugins',
                    'icon' => null,
                ],
            ]
        );
    }

    /**
     * Integration hooks.
     */
    protected function hooks()
    {
        \add_action('init', [$this, 'register_block']);
        \add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
        \add_filter('block_categories', [$this, 'create_block_category'], 10, 2);
    }
}
new Gutenberg();
