<?php
class A_NextGen_Block_Ajax extends Mixin
{
    function get_image_action()
    {
        $retval = array('success' => FALSE);
        // TODO: Should this method check for a valid nonce? Should it require authentication?
        if ($image = $this->param('image_id')) {
            if ($image = C_Image_Mapper::get_instance()->find($image)) {
                $storage = C_Gallery_Storage::get_instance();
                $image->thumbnail_url = $storage->get_image_url($image, 'thumb');
                $image->image_url = $storage->get_image_url($image, 'full');
                $retval['image'] = $image;
                $retval['success'] = TRUE;
            }
        }
        return $retval;
    }
}
/**
 * Adds support to Gutenberg / Block Editor for NGG Post Thumbnails
 */
class C_Ngg_Post_Thumbnails
{
    static $_instance = NULL;
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    protected function __construct()
    {
    }
    public function register_hooks()
    {
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_post_thumbnails'], 1);
        // Expose a field for posts/pages to set the ngg_post_thumbnail via REST API
        add_action('init', function () {
            array_map(function ($post_type) {
                add_post_type_support($post_type, 'custom-fields');
                register_meta($post_type, 'ngg_post_thumbnail', ['type' => 'integer', 'single' => TRUE, 'show_in_rest' => TRUE]);
                add_action('rest_insert_' . $post_type, [$this, 'set_or_remove_ngg_post_thumbnail'], PHP_INT_MAX - 1, 2);
            }, get_post_types_by_support('thumbnail'));
        }, 11);
    }
    function register_adapters()
    {
        C_Component_Registry::get_instance()->add_adapter('I_Ajax_Controller', 'A_NextGen_Block_Ajax');
    }
    function set_or_remove_ngg_post_thumbnail($post, $request)
    {
        $json = @json_decode($request->get_body());
        $target = NULL;
        if (!is_object($json)) {
            return;
        }
        // WordPress 5.3 changed how the featured-image metadata was submitted to the server
        if (isset($json->meta) && property_exists($json->meta, 'ngg_post_thumbnail')) {
            $target = $json->meta;
        } elseif (property_exists($json, 'ngg_post_thumbnail')) {
            $target = $json;
        }
        if (!$target) {
            return;
        }
        $storage = C_Gallery_Storage::get_instance();
        // Was the post thumbnail removed?
        if (!$target->ngg_post_thumbnail) {
            delete_post_thumbnail($post->ID);
            $storage->delete_from_media_library($target->ngg_post_thumbnail);
        } else {
            // Was it added?
            $storage->set_post_thumbnail($post->ID, $target->ngg_post_thumbnail);
        }
    }
    function enqueue_post_thumbnails()
    {
        add_thickbox();
        wp_enqueue_script('ngg-post-thumbnails', C_Router::get_instance()->get_static_url(NEXTGEN_BLOCK . '#build/post-thumbnail.min.js'), ['lodash', 'wp-element', 'wp-data', 'wp-editor', 'wp-components', 'wp-i18n', 'photocrati_ajax'], NGG_PLUGIN_VERSION);
        wp_localize_script('ngg-post-thumbnails', 'ngg_featured_image', ['modal_url' => admin_url("/media-upload.php?post_id=%post_id%&type=image&tab=nextgen&from=block-editor&TB_iframe=true")]);
        if (preg_match("/media-upload\\.php/", $_SERVER['REQUEST_URI']) && $_GET['tab'] == 'nextgen') {
            wp_add_inline_style('wp-admin', "#media-upload-header {display: none; }");
            if (isset($_GET['from']) && $_GET['from'] == 'block-editor') {
                add_action('admin_enqueue_scripts', [$this, 'media_upload_footer']);
            }
        }
    }
    function media_upload_footer()
    {
        wp_add_inline_script('image-edit', 'window.NGGSetAsThumbnail = top.set_ngg_post_thumbnail');
    }
}