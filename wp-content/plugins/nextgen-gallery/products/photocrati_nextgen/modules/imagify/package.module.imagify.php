<?php
/**
 * Class A_Imagify_Admin_Page
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 */
class A_Imagify_Admin_Page extends Mixin
{
    function setup()
    {
        // This hides the imagify page from the menu while still allowing it to display
        if (defined('IMAGIFY_VERSION')) {
            $parent = NULL;
        } elseif (is_multisite()) {
            $parent = NULL;
        } else {
            $parent = NGGFOLDER;
        }
        $this->object->add('ngg_imagify', array('adapter' => 'A_Imagify_Admin_Page_Controller', 'parent' => $parent));
        return $this->call_parent('setup');
    }
}
class A_Imagify_Admin_Page_Controller extends Mixin
{
    function enqueue_backend_resources()
    {
        $this->call_parent('enqueue_backend_resources');
        wp_enqueue_style('imagify_upgrade_page', $this->get_static_url('photocrati-imagify#style.css'), array(), NGG_SCRIPT_VERSION);
    }
    function get_page_title()
    {
        return __('Image Optimization', 'nggallery');
    }
    function get_required_permission()
    {
        // TODO: must be able to install plugins
        return 'NextGEN Change options';
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->title = __('Image Optimization');
        $i18n->message = __('NextGEN Gallery partners with Imagify for best-in-class image optimization. Compress images to make galleries faster, all while maintaining image quality.', 'nggallery');
        $i18n->confirmation = '';
        $i18n->button = '';
        $i18n->third_party_message = __('Note: Imagify is a third party plugin. It is not built or supported by NextGEN Gallery.', 'nggallery');
        // $i18n->review_message = __( 'For more on why we recommend compressing images and why we recommend Imagify, check out our ', 'nggallery' );
        $i18n->more_message = __('More on Imagify:', 'nggallery');
        $i18n->review_message = __('More on why we recommend Imagify:', 'nggallery');
        $i18n->imagify_plugin_link = __('Imagify Plugin Page', 'nggallery');
        $i18n->imagify_website_link = __('Imagify Website', 'nggallery');
        $i18n->imagify_review_link = __('Our Review of Image Compression Plugins', 'nggallery');
        if (Imagify_Partner::is_imagify_activated()) {
            if (Imagify_Partner::is_success()) {
                $i18n->confirmation = __('Imagify has been successfully activated', 'nggallery');
            } else {
                $i18n->confirmation = __('Imagify is already activated', 'nggallery');
            }
        } else {
            if (Imagify_Partner::is_imagify_installed()) {
                $i18n->button = __('Activate Imagify', 'nggallery');
            } else {
                $i18n->button = __('Install and activate Imagify', 'nggallery');
            }
        }
        return $i18n;
    }
    function index_action()
    {
        $this->object->enqueue_backend_resources();
        $key = C_Photocrati_Transient_Manager::create_key('ngg_imagify_page', 'html');
        if ($html = C_Photocrati_Transient_Manager::fetch($key, FALSE)) {
            echo $html;
        } else {
            $imagify_install_url = NULL;
            if ($client = M_Imagify::get_imagify_client()) {
                $imagify_install_url = $client->get_post_install_url();
            }
            print $this->render_view('photocrati-imagify#admin_page', array('i18n' => $this->get_i18n_strings(), 'is_imagify_activated' => Imagify_Partner::is_imagify_activated(), 'imagify_install_url' => $imagify_install_url, 'imagify_plugin_url' => 'https://wordpress.org/plugins/imagify/', 'imagify_website_url' => 'https://imagify.io/?utm_source=nextgen-gallery&utm_campaign=plugin_partner&utm_medium=partnership', 'imagify_review_url' => 'https://www.imagely.com/image-optimization-plugin-comparison/'), TRUE);
            C_Photocrati_Transient_Manager::update($key, $html);
        }
    }
}