<?php
/*
  Plugin Name: OpenLab GradeBook
  Plugin URI: https://openlab.citytech.cuny.edu/
  Description: Beta version. This basic gradebook allows faculty to confidentially record and share students grades via the WP Dashboard. Some features are still in development, slowness and minor display bugs may occur. A modification of AN GradeBook https://wordpress.org/plugins/an-gradebook/
  Version: 0.1.0
  Author: Joe Unander
  Author URI: http://early-adopter.com/
  License: GPL
 */


//establishing some constants
define("OPENLAB_GRADEBOOK_VERSION", "0.0.3");
define("OPENLAB_GRADEBOOK_FEATURES_TRACKER", 0.3);

/**
 * Legacy: includes database files, where most of the backend functionality lives
 */
$database_file_list = glob(dirname(__FILE__) . '/database/*.php');
foreach ($database_file_list as $database_file) {
    include($database_file);
}

//sidebar widget
include(dirname(__FILE__) . '/components/sidebar-widget.php');

//legacy globals
$oplb_gradebook_api = new oplb_gradebook_api();
$oplb_gradebook_course_api = new gradebook_course_API();
$oplb_gradebook_assignment_api = new gradebook_assignment_API();
$oplb_gradebook_cell_api = new gradebook_cell_API();
$oplb_course_list = new OPLB_COURSE_LIST();
$oplb_gradebook = new OPLB_GRADEBOOK();
$oplb_user = new OPLB_USER();
$oplb_user_list = new OPLB_USER_LIST();
$oplb_statistics = new OPLB_STATISTICS();
$oplb_database = new OPLB_DATABASE();

/**
 * Legacy: setup OpenLab GradeBook admin
 */
function register_oplb_gradebook_menu_page() {
    $roles = wp_get_current_user()->roles;
    
    //in at least one case a super admin was not properly assigned a role
    if (empty($roles) && is_super_admin()) {
        $roles[0] = 'administrator';
    }

    $my_admin_page = add_menu_page('OpenLab GradeBook', 'OpenLab GradeBook', $roles[0], 'oplb_gradebook', 'init_oplb_gradebook', 'dashicons-book-alt', '6.12');
    add_submenu_page('oplb_gradebook', 'OpenLab GradeBook', 'My GradeBook', $roles[0], 'oplb_gradebook', 'init_oplb_gradebook');
    $add_submenu_page_settings = in_array($roles[0], array_keys(get_option('oplb_gradebook_settings')));
    
    if ($add_submenu_page_settings) {
        add_submenu_page('oplb_gradebook', 'Settings', 'Settings', 'administrator', 'oplb_gradebook_settings', 'init_oplb_gradebook_settings');
    }
}

add_action('admin_menu', 'register_oplb_gradebook_menu_page', 10);

/**
 * Updating admin menu to appends "#courses" to the GradeBook URL
 * That hash initiates the client-side app functionality
 */
function oplb_gradebook_admin_menu_custom(){
    global $menu, $submenu, $plugin_page;

    if (!isset($submenu['oplb_gradebook'])) {
        foreach ($menu as &$menu_item) {

            if (in_array('oplb_gradebook', $menu_item)) {

                $menu_item[2] = 'admin.php?page=oplb_gradebook#courses';

            }

        }
        return false;
    }

    foreach ($submenu['oplb_gradebook'] as &$submenu_item){
        
        if(!is_array($submenu_item)){
            break;
        }

        foreach($submenu_item as &$item){
            
            if($item === 'oplb_gradebook'){
                $item = 'admin.php?page=oplb_gradebook#courses';
            }

        }

    }

}

add_action('admin_menu', 'oplb_gradebook_admin_menu_custom', 100);

/**
 * Legacy: setup OpenLab admin enqueues
 * @param type $hook
 * @return type
 */
function enqueue_oplb_gradebook_scripts() {

    $app_base = plugins_url('js', __FILE__);

	wp_enqueue_script('jquery-ui-datepicker');

	$oplb_gradebook_develop = false;

	if (WP_DEBUG) {
		$oplb_gradebook_develop = true;
	}

	$dep_ver = '0.0.1.1';
    $app_ver = filemtime(plugin_dir_path(__FILE__).'oplb-gradebook-app-min.js');
    $style_ver = filemtime(plugin_dir_path(__File__).'GradeBook.css');

	wp_register_style('jquery_ui_css', $app_base . '/lib/jquery-ui/jquery-ui.css', array(), $dep_ver, false);
	wp_register_style('OplbGradeBook_css', plugins_url('GradeBook.css', __File__), array('bootstrap_css', 'jquery_ui_css'), $style_ver, false);
	wp_register_style('bootstrap_css', $app_base . '/lib/bootstrap/css/bootstrap.css', array(), $dep_ver, false);
    wp_register_script('jscrollpane-js', $app_base . '/lib/jscrollpane/jscrollpane.dist.js', array('jquery'), $dep_ver, true);
    wp_register_script('bootstrap-fileinput-js', $app_base . '/lib/waypoints/noframework.waypoints.min.js', array('jquery'), $dep_ver, true);
    wp_register_script('waypoints-js', $app_base . '/lib/bootstrap-fileinput/bootstrap-fileinput.dist.js', array('jquery'), $dep_ver, true);
	wp_register_script('css-element-queries-js', $app_base . '/lib/css-element-queries/css.element.queries.dist.js', array('jquery'), $dep_ver, true);
	wp_register_script('requirejs', $app_base . '/require.js', array('jquery', 'media-views'), $app_ver, true);
	wp_enqueue_style('OplbGradeBook_css');
    wp_enqueue_script('jscrollpane-js');
    wp_enqueue_script('bootstrap-fileinput-js');
    wp_enqueue_script('waypoints-js');
	wp_enqueue_script('css-element-queries-js');
	wp_enqueue_script('requirejs');

	wp_localize_script('requirejs', 'oplbGradebook', array(
		'ajaxURL' => admin_url('admin-ajax.php'),
		'depLocations' => oplb_gradebook_get_dep_locations(),
		'nonce' => wp_create_nonce('oplb_gradebook'),
		'currentYear' => date('Y'),
		'initName' => oplb_gradebook_gradebook_init_placeholder(),
	));

	wp_localize_script('requirejs', 'require', array(
		'baseUrl' => $app_base,
		'deps' => array($app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js?ver='.$app_ver : '/oplb-gradebook-app-min.js?ver='.$app_ver))
	));
}
add_action( 'admin_print_footer_scripts-toplevel_page_oplb_gradebook', 'enqueue_oplb_gradebook_scripts', 1 );
add_action( 'admin_print_footer_scripts-openlab-gradebook_page_oplb_gradebook_settings', 'enqueue_oplb_gradebook_scripts', 1 );

/**
 * Legacy: callback for OpenLab GradeBook instantiation
 * Adds template files to page so that BackBone JS client-side app can access them
 */
function init_oplb_gradebook() {

    $template_list = glob(dirname(__FILE__) . '/js/app/templates/*.php');

    foreach ($template_list as $template) {

        //get template name
        $template_explode = explode('/', $template);
        $template_filename = esc_html(str_replace('.php', '', array_pop($template_explode)));
        echo "<script id='{$template_filename}' type='text/template'>";
        include($template);
        echo "</script>";
    }
}

/**
 * Legacy: callback for OpenLab GradeBook settings instantiation
 * Setups up templates for Backbone JS client-side app responsible for settings
 */
function init_oplb_gradebook_settings() {
    ob_start();
    include( dirname(__FILE__) . '/components/parts/pages/settings-template.php' );
    echo ob_get_clean();
}

/**
 * Legacy: delete user hooks
 * @todo: determine if this is necessary; actions may already be completed in database/User.php
 * @global type $wpdb
 * @param type $user_id
 */
function oplb_gradebook_my_delete_user($user_id) {
    global $wpdb;
    $results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $user_id));
    $results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('uid' => $user_id));
}

add_action('delete_user', 'oplb_gradebook_my_delete_user');

/**
 * Legacy: makes ajaxurl accessible to client-side app
 * @todo: move this to wp_localize_script
 */
function oplb_gradebook_ajaxurl() {
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}

add_action('wp_head', 'oplb_gradebook_ajaxurl');

/**
 * Prevent notices from other plugins from appearing on OpenLab GradeBook pages
 * These notices can sometimes interfere with client-side functionality
 * @global type $wp_filter
 * @return boolean
 */
function oplb_gradebook_admin_notices() {
    global $wp_filter;
    $screen = get_current_screen();

    //if this is not OpenLab GradeBook, we're not doing anything here
    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base !== "toplevel_page_oplb_gradebook" && $screen->base !== 'openlab-gradebook_page_oplb_gradebook_settings') {
            return false;
        }
    }

    if (isset($wp_filter['admin_notices'])
            && !empty($wp_filter['admin_notices']->callbacks)) {

        foreach ($wp_filter['admin_notices']->callbacks as $priority => $callback) {

            foreach ($callback as $hookname => $hook) {

                if (strpos($hookname, 'oplb_gradebook') === false) {
                    $result = remove_action('admin_notices', $hookname, $priority);
                }
            }
        }
    }
}

add_action('admin_notices', 'oplb_gradebook_admin_notices', 1);

/**
 * Grab dependencies already stored in WP (to avoid conflicts)
 */
function oplb_gradebook_get_dep_locations() {

    $include_dir = includes_url() . 'js/';

    $deps = array(
        'jquery' => $include_dir . 'jquery/jquery',
        'jqueryui' => $include_dir . 'jquery/ui/core.min',
        'backbone' => $include_dir . 'backbone.min',
        'underscore' => $include_dir . 'underscore.min',
    );

    return $deps;
}

//activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_oplb_gradebook');
register_deactivation_hook(__FILE__, 'deactivate_oplb_gradebook');

//
function oplb_gradebook_gradebook_init_placeholder(){

    return apply_filters('oplb_gradebook_gradebook_init_placeholder', 'Please provide a Name');

}

/**
 * Openlab GradeBook activation actions
 */
function activate_oplb_gradebook() {
    global $wpdb;

    //initialize databases
    $oplb_database = new OPLB_DATABASE();
    $oplb_database->database_init();
    $oplb_database->database_alter();

    //create the instructor user so the instructor has permissions to create a GradeBook
    $user = wp_get_current_user();

    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}oplb_gradebook_courses WHERE gbid = %d AND role = %s AND uid = %d", 0, 'instructor', $user->ID);
    $init_instructor = $wpdb->get_results($query);

    if (empty($init_instructor)) {
        $result = $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
            'uid' => $user->ID,
            'gbid' => 0,
            'role' => 'instructor',
            'current_grade_average' => 0.00,
                ), array(
            '%d',
            '%d',
            '%s',
            '%f',
                )
        );
    }

    update_option('oplb_gradebook_features_tracker', OPENLAB_GRADEBOOK_FEATURES_TRACKER);
}

/**
 * OpenLab GradeBook deactivation actions
 * @todo: remove storage page
 */
function deactivate_oplb_gradebook() {

    delete_option('oplb_gradebook_features_tracker');
    delete_option('oplb_gradebook_db_version');
    delete_option('oplb_gradebook_settings');
    delete_option('oplb_gradebook_db_version');
}