<?php
/**
 * Class A_NextGen_Pro_Plus_Upgrade_Page
 * @mixin C_NextGen_Admin_Page_Manager
 * @adapts I_Page_Manager
 * @todo merge with A_NextGen_Pro_Upgrade_Page class
 */
class A_NextGen_Pro_Plus_Upgrade_Page extends Mixin
{
    function setup()
    {
        // Using include() to retrieve the is_plugin_active() is apparently The WordPress Way(tm)..
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        // We shouldn't show the upgrade page if they already have the plugin and it's active
        $found = false;
        if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME')) {
            $found = 'NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME';
        }
        if (defined('NGG_PRO_PLUGIN_BASENAME')) {
            $found = 'NGG_PRO_PLUGIN_BASENAME';
        }
        if (!($found && is_plugin_active(constant($found)))) {
            $this->object->add('ngg_pro_upgrade', array('adapter' => 'A_NextGen_Pro_Upgrade_Controller', 'parent' => NGGFOLDER));
        }
        return $this->call_parent('setup');
    }
}
/**
 * Class A_NextGen_Pro_Upgrade_Controller
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page_Controller using "ngg_pro_upgrade" context
 */
class A_NextGen_Pro_Upgrade_Controller extends Mixin
{
    function enqueue_backend_resources()
    {
        $this->call_parent('enqueue_backend_resources');
        wp_enqueue_style('nextgen_pro_upgrade_page', $this->get_static_url('photocrati-nextgen_pro_upgrade#style.css'), array(), NGG_SCRIPT_VERSION);
    }
    function get_page_title()
    {
        return __('Upgrade to Pro', 'nggallery');
    }
    function get_required_permission()
    {
        return 'NextGEN Change options';
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->plus_title = __('Create Stunning Galleries with NextGEN Pro', 'nggallery');
        $i18n->pro_title = __('Sell Photos + Adobe Lightroom', 'nggallery');
        $i18n->plus_desc_first = __('Introducing the most powerful gallery system ever made for WordPress. Watch our 30 second video, or click below to learn more about NextGEN premium extensions and support.', 'nggallery');
        $i18n->pro_desc = __('You\'re awesome! You\'ve already got NextGEN Plus. But why not go all the way? With NextGEN Pro, you can sell print and digital downloads, provide proofing galleries for clients, manage galleries directly from Adobe Lightroom, and more.', 'nggallery');
        $i18n->video = __('Psst...watch the video ->', 'nggallery');
        $i18n->plus_button = __('Get Premium Extensions', 'nggallery');
        $i18n->pro_button = __('Learn More', 'nggallery');
        return $i18n;
    }
    function index_action()
    {
        $this->object->enqueue_backend_resources();
        $key = C_Photocrati_Transient_Manager::create_key('nextgen_pro_upgrade_page', 'html');
        if ($html = C_Photocrati_Transient_Manager::fetch($key, FALSE)) {
            echo $html;
        } else {
            // Get template and page content
            $template = 'photocrati-nextgen_pro_upgrade#plus';
            if (defined('NGG_PLUS_PLUGIN_BASENAME')) {
                $template = 'photocrati-nextgen_pro_upgrade#pro';
            }
            $html = $this->render_view($template, array('i18n' => $this->get_i18n_strings()), TRUE);
            // Cache it
            C_Photocrati_Transient_Manager::update($key, $html);
            // Render it
            echo $html;
        }
    }
}
/**
 * Class A_NextGen_Pro_Upgrade_Page
 * @mixin C_NextGen_Admin_Page_Controller
 * @adapts I_NextGen_Admin_Page_Controller
 * @todo merge with A_NextGen_Pro_Plus_Upgrade_Page class
 */
class A_NextGen_Pro_Upgrade_Page extends Mixin
{
    function setup()
    {
        // Using include() to retrieve the is_plugin_active() is apparently The WordPress Way(tm)..
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        // We shouldn't show the upgrade page if they already have the plugin and it's active
        $found = false;
        if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME')) {
            $found = 'NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME';
        }
        if (defined('NGG_PRO_PLUGIN_BASENAME')) {
            $found = 'NGG_PRO_PLUGIN_BASENAME';
        }
        if (!($found && is_plugin_active(constant($found)))) {
            $this->object->add('ngg_pro_upgrade', array('adapter' => 'A_NextGen_Pro_Upgrade_Controller', 'parent' => NGGFOLDER));
        }
        return $this->call_parent('setup');
    }
}