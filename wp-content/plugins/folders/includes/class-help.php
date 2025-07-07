<?php
/**
 * Help And Footer Menu Class
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

 if (defined('ABSPATH') === false) {
	exit;
}

// Class for help and footer menu
class FOLDERS_HELP extends WCP_Folders {


    // Allowed pages for showing the help menu
    private static $allowed_pages = ['wcp_folders_settings', 'folders-upgrade-to-pro', 'plugins.php']; 
    
    // constructor
    public function __construct() {  
        
        $current = basename($_SERVER['PHP_SELF'] ?? '');
        $page = $_GET['page'] ?? ''; 
        // Check if we're on one of those pages
        if (in_array($current, self::$allowed_pages, true) || in_array($page, self::$allowed_pages, true)) {
            // register enqueue  css
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); 
            // add need help in footer
            add_action('admin_footer', array($this, 'admin_footer_need_help_content'));
        } 
  
	}//end __construct()

    // load help settings
    public function load_help_settings(){
        $customize_folders = get_option("customize_folders");
        define('WCP_FOLDER_FOOTER_HELP_DATA', array(
            'help_icon' => esc_url(WCP_FOLDER_URL."assets/images/help/help-icon.svg"),
            'close_icon' => esc_url(WCP_FOLDER_URL."assets/images/help/close.svg"), 
            'premio_site_info' => esc_url('https://premio.io/'),
            'help_center_link' => esc_url('https://premio.io/help/folders/?utm_source=pluginspage'),
            'footer_menu' => array( 
                'support' => array(
                    'title' => esc_html("Get Support", "folders"),
                    'link' =>  esc_url("https://wordpress.org/support/plugin/folders/"),
                    'status' => true,
                ),
                'upgrade_to_pro' => array(
                    'title' => esc_html("Upgrade to Pro", "folders"),
                    'link' =>  esc_url($this->getFoldersUpgradeURL()),
                    'status' => true,
                ),
                'recommended_plugins' => array(
                    'title' => esc_html("Recommended Plugins", "folders"),
                    'link' =>  esc_url($this->getFoldersRecommendedPluginsURL()),
                    'status' => get_option("hide_folder_recommended_plugin") || (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") ? false : true,
                ), 
            ),
            'support_widget' => array(
                'upgrade_to_pro' => array(
                    'title' => esc_html("Upgrade to Pro", "folders"),
                    'link' =>  esc_url($this->getFoldersUpgradeURL()),
                    'icon' => esc_url(WCP_FOLDER_URL."assets/images/help/pro.svg"),
                ),
                'get_support' => array(
                    'title' => esc_html("Get Support", "folders"),
                    'link' =>   esc_url("https://wordpress.org/support/plugin/folders/"),
                    'icon' => esc_url(WCP_FOLDER_URL."assets/images/help/help-circle.svg"),
                ),
                'contact' => array(
                    'title' => esc_html("Contact Us", "folders"),
                    'link' =>  false,
                    'icon' => esc_url(WCP_FOLDER_URL."assets/images/help/headphones.svg"),
                ),
            ),
        ));  
    }

    

    // enqueue scripts
    public function admin_enqueue_scripts(){ 
        // enqueue css
        wp_enqueue_style('folders-help-css', WCP_FOLDER_URL . 'assets/css/help.css', array(), WCP_FOLDER_VERSION);   

    } 

    // Need Help Footer Content
    public function admin_footer_need_help_content(){ 
        $this->load_help_settings(); 

        include_once WCP_FOLDERS_PLUGIN_PATH.'/templates/admin/help.php';
    } 
    
}
new FOLDERS_HELP();