<?php
/*
  Plugin Name: OpenLab GradeBook
  Plugin URI: https://github.com/livinglab/openlab
  Description: A modification of AN Gradebook https://wordpress.org/plugins/an-gradebook/
  Version: 0.0.3
  Author: Joe Unander
  Author URI: http://early-adopter.com/
  License: GPL
 */

define("OPENLAB_GRADEBOOK_VERSION", "0.0.3");
define("OPENLAB_GRADEBOOK_FEATURES_TRACKER", 0.3);
define("OPLB_GRADEBOOK_STORAGE_SLUG", "zzoplb-gradebook-storagezz");

function oplb_verify_buddypress() {

    define("OPLB_BP_AVAILABLE", true);
}

add_action('bp_include', 'oplb_verify_buddypress');

$database_file_list = glob(dirname(__FILE__) . '/database/*.php');
foreach ($database_file_list as $database_file) {
    include($database_file);
}

$oplb_database = new OPLB_DATABASE();
$oplb_gradebook_api = new oplb_gradebook_api();
$oplb_gradebook_course_api = new gradebook_course_API();
$oplb_gradebook_assignment_api = new gradebook_assignment_API();
$oplb_gradebook_cell_api = new gradebook_cell_API();
$oplb_gradebookapi = new OPLB_GradeBookAPI();
$oplb_course_list = new OPLB_COURSE_LIST();
$oplb_gradebook = new OPLB_GRADEBOOK();
$oplb_user = new OPLB_USER();
$oplb_user_list = new OPLB_USER_LIST();
$oplb_statistics = new OPLB_STATISTICS();

function register_oplb_gradebook_menu_page() {
    $roles = wp_get_current_user()->roles;

    //in at least one case a super admin was not properly assigned a role
    if (empty($roles) && is_super_admin()) {
        $roles[0] = 'administrator';
    }

    $my_admin_page = add_menu_page('OpenLab GradeBook', 'OpenLab GradeBook', $roles[0], 'oplb_gradebook', 'init_oplb_gradebook', 'dashicons-book-alt', '6.12');
    $add_submenu_page_settings = in_array($roles[0], array_keys(get_option('oplb_gradebook_settings')));
    if ($add_submenu_page_settings) {
        add_submenu_page('oplb_gradebook', 'Settings', 'Settings', 'administrator', 'oplb_gradebook_settings', 'init_oplb_gradebook_settings');
    }
}

add_action('admin_menu', 'register_oplb_gradebook_menu_page');

function enqueue_oplb_gradebook_scripts($hook) {
    $app_base = plugins_url('js', __FILE__);

    //for media functions (to upload CSV files)
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-datepicker');

    wp_register_script('init_gradebookjs', $app_base . '/init_gradebook.js', array('jquery', 'media-views'), '0.0.0.9', true);
    wp_enqueue_script('init_gradebookjs');
    wp_localize_script('init_gradebookjs', 'oplbGradebook', array(
        'ajaxURL' => admin_url('admin-ajax.php'),
        'depLocations' => oplb_get_dep_locations(),
        'storagePage' => get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG)
    ));
    if ($hook == "toplevel_page_oplb_gradebook" || $hook == 'gradebook_page_oplb_gradebook_settings') {
        $oplb_gradebook_develop = true;

        wp_register_style('jquery_ui_css', $app_base . '/lib/jquery-ui/jquery-ui.css', array(), '0.0.0.2', false);
        wp_register_style('OplbGradeBook_css', plugins_url('GradeBook.css', __File__), array('bootstrap_css', 'jquery_ui_css'), '0.0.0.4', false);
        wp_register_style('bootstrap_css', $app_base . '/lib/bootstrap/css/bootstrap.css', array(), '0.0.0.2', false);
        wp_register_script('jscrollpane-js', $app_base . '/lib/jscrollpane/jscrollpane.dist.js', array('jquery'), '0.0.0.2', true);
        wp_register_script('requirejs', $app_base . '/require.js', array('jquery', 'media-views'), '0.0.0.6', true);
        wp_enqueue_style('OplbGradeBook_css');
        wp_enqueue_script('jscrollpane-js');
        wp_enqueue_script('requirejs');
        wp_localize_script('requirejs', 'require', array(
            'baseUrl' => $app_base,
            'deps' => array($app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js' : '/oplb-gradebook-app-min.js'))
        ));
    } else {
        return;
    }
}

add_action('admin_enqueue_scripts', 'enqueue_oplb_gradebook_scripts');

function init_oplb_gradebook() {
    $template_list = glob(dirname(__FILE__) . '/js/app/templates/*.php');

    foreach ($template_list as $template) {

        //get template name
        $template_explode = explode('/', $template);
        $template_filename = str_replace('.php', '', array_pop($template_explode));
        echo "<script id='{$template_filename}' type='text/template'>";
        include($template);
        echo "</script>";
    }
}

function init_oplb_gradebook_settings() {
    ob_start();
    include( dirname(__FILE__) . '/js/app/templates/settings-template.php' );
    include( dirname(__FILE__) . '/js/app/templates/ajax-template.php' );
    echo ob_get_clean();
}

function oplb_gradebook_my_delete_user($user_id) {
    global $wpdb;
    $results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $user_id));
    $results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('uid' => $user_id));
}

add_action('delete_user', 'oplb_gradebook_my_delete_user');

function oplb_gradebook_ajaxurl() {
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}

add_action('wp_head', 'oplb_gradebook_ajaxurl');

function oplb_gradebook_admin_notices() {
    global $wp_filter;
    $screen = get_current_screen();

    //if this is not OpenLab Gradebook, we're not doing anything here
    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base !== "toplevel_page_oplb_gradebook" && $screen->base !== 'gradebook_page_oplb_gradebook_settings') {
            return false;
        }
    }

    if (isset($wp_filter['admin_notices'])
            && isset($wp_filter['admin_notices']->callbacks)
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

function oplb_gradebook_shortcode() {
    init_oplb_gradebook();
    $oplb_gradebook_develop = false;
    $app_base = plugins_url('js', __FILE__);
    wp_register_script('init_front_end_gradebookjs', $app_base . '/init_front_end_gradebook.js', array('jquery'), null, true);
    wp_enqueue_script('init_front_end_gradebookjs');
    if (1 == 1) {
        wp_register_style('jquery_ui_css', $app_base . '/lib/jquery-ui/jquery-ui.css', array(), null, false);
        wp_register_style('OplbGradeBook_css', plugins_url('GradeBook.css', __File__), array('bootstrap_css', 'jquery_ui_css'), null, false);
        wp_register_style('bootstrap_css', $app_base . '/lib/bootstrap/css/bootstrap.css', array(), null, false);
        wp_register_script('requirejs', $app_base . '/require.js', array(), null, true);
        wp_enqueue_style('OplbGradeBook_css');
        wp_enqueue_script('requirejs');
        wp_localize_script('requirejs', 'require', array(
            'baseUrl' => $app_base,
            'deps' => array($app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js' : '/oplb-gradebook-app-min.js')
        )));
    } else {
        return;
    }
    return '<div id="wpbody-content"></div>';
}

//add_shortcode('oplb_gradebook', 'oplb_gradebook_shortcode');

/**
 * Grab dependencies already stored in WP (to avoid conflicts)
 */
function oplb_get_dep_locations() {

    $include_dir = includes_url() . 'js/';

    $deps = array(
        'jquery' => $include_dir . 'jquery/jquery',
        'jqueryui' => $include_dir . 'jquery-ui/jquery-ui.min',
        'backbone' => $include_dir . 'backbone.min',
        'underscore' => $include_dir . 'underscore.min',
    );

    return $deps;
}

function oplb_gradebook_current_screen_callback($screen) {

    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base === "toplevel_page_oplb_gradebook" || $screen->base === 'gradebook_page_oplb_gradebook_settings') {
            add_filter('gettext', 'oplb_gradebook_gettext', 99, 3);
        }
    }
}

add_action('current_screen', 'oplb_gradebook_current_screen_callback');

function oplb_gradebook_gettext($translated_text, $untranslated_text, $domain) {

    switch ($untranslated_text) {
        case 'Drop files anywhere to upload':
            $translated_text = 'Drop CSV anywhere to upload';
            break;
        case 'Select Files':
            $translated_text = 'Select CSV';
            break;
        case 'No items found.':
            $translated_text = 'Upload CSV';
            break;
    }

    return $translated_text;
}

//activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_oplb_gradebook');
register_deactivation_hook(__FILE__, 'deactivate_oplb_gradebook');

/**
 * Openlab Gradebook activation actions
 */
function activate_oplb_gradebook() {

    //add custom page for csv storage - make the slug something very unlikely to be used
    oplb_gradebook_custom_page(OPLB_GRADEBOOK_STORAGE_SLUG, 'OpenLab Gradebook Storage');
    update_option('oplb_gradebook_features_tracker', OPENLAB_GRADEBOOK_FEATURES_TRACKER);
}

/**
 * OpenLab Gradebook deactivation actions
 * @todo: remove storage page
 */
function deactivate_oplb_gradebook() {
    
}

/**
 * Hook into wp_handle_upload to run our specific CSV uploads
 * 1) Check to make sure file is a CSV
 * @todo Check to make sure uploader is a faculty member
 * @param type $file
 * @return type
 */
function oplb_gradebook_wp_handle_upload_prefilter($file_info) {

    $storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

    if (isset($_REQUEST['post_id']) && intval($_REQUEST['post_id']) === intval($storage_page->ID)) {

        if ($file_info['type'] !== 'text/csv') {
            $file_info['error'] = 'This file does not appear to be a CSV.';
            return $file_info;
        }

        if (!isset($_REQUEST['name']) || !isset($_REQUEST['gbid'])) {
            $file_info['error'] - 'There was a problem with the upload; please try again.';
        }

        $name = 'temp.csv';

        $name = sanitize_file_name($_REQUEST['name']);
        $file_info['gbid'] = intval(sanitize_text_field($_REQUEST['gbid']));

        $oplb_upload_csv = new gradebook_upload_csv_API();
        $result = $oplb_upload_csv->upload_csv($file_info, $name);

        if ($result['response'] === 'oplb-gradebook-error') {
            $file_info['error'] = $result['content'];
            return $file_info;
        }
    }

    return $file_info;
}

add_filter('wp_handle_upload', 'oplb_gradebook_wp_handle_upload_prefilter');

/**
 * Use wp_prepare_attachment_for_js to clean up CSV and send cleaned confirmation data back to the upload modal
 * @param type $response
 * @param type $attachment
 * @param type $meta
 * @return type
 */
function oplb_gradebook_wp_prepare_attachment_for_js($response, $attachment, $meta) {

    $storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

    if (isset($response['uploadedTo']) && $response['uploadedTo'] === $storage_page->ID) {
        wp_delete_attachment($response['id'], true);
    }

    return $response;
}

add_filter('wp_prepare_attachment_for_js', 'oplb_gradebook_wp_prepare_attachment_for_js', 10, 3);

/**
 * Create custom page
 * @param type $slug
 * @param type $title
 * @return type
 */
function oplb_gradebook_custom_page($slug, $title) {

    $post_id = -1;
    $author_id = 1;

    if (null == get_page_by_path($slug)) {

        $post_id = wp_insert_post(
                array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => $author_id,
                    'post_name' => $slug,
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_type' => 'page'
                )
        );
    } else {
        $post_id = -2;
    }

    return $post_id;
}

/**
 * Exclude custom pages from admin (so nobody messes with 'em)
 * @global type $pagenow
 * @global type $post_type
 * @param type $query
 * @return type
 */
function oplb_gradebook_exclude_pages_from_admin($query) {

    if (!is_admin())
        return $query;

    global $pagenow, $post_type;

    if ($pagenow == 'edit.php' && $post_type == 'page') {

        $csv_storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

        $query->query_vars['post__not_in'] = array($csv_storage_page->ID);
    }
}

add_filter('parse_query', 'oplb_gradebook_exclude_pages_from_admin');

/**
 * Custom pages: remove link from admin bar
 * @global type $post
 * @global type $wp_admin_bar
 */
function oplb_gradebook_remove_admin_bar_edit_link() {
    global $post;

    $exclusions = array(OPLB_GRADEBOOK_STORAGE_SLUG);

    if ($post && in_array($post->post_name, $exclusions)) {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('edit');
    }
}

add_action('wp_before_admin_bar_render', 'oplb_gradebook_remove_admin_bar_edit_link');

function oplb_gradebook_dynamic_sidebar_before($index) {

    $target_indices = array('sidebar-1', 'Primary Sidebar', 'sidebar_home', 'sidebar_posts', 'sidebar_pages', 'primary-widget-area');

    if (in_array($index, $target_indices)) {

        //@todo: move this to separate template file
        if (is_user_logged_in()) {

            $url = admin_url('admin.php?page=oplb_gradebook#courses');
            ?>
            <aside id="oplbGradebookLink" class="widget widget_categories">
                <h3 class="widget-title"><a href="<?php echo $url; ?>">OpenLab Gradebook</a></h3>
            </aside>
            <?php
        }
    }
}

add_action('dynamic_sidebar_before', 'oplb_gradebook_dynamic_sidebar_before');

function oplb_gradebook_plupload_default_params($params) {
    $screen = new stdClass();

    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
    }

    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base === "toplevel_page_oplb_gradebook" || $screen->base === 'gradebook_page_oplb_gradebook_settings') {
            $params['oplb_gb_upload_type'] = 'oplb_gb_csv';
        }
    }

    return $params;
}

add_filter('plupload_default_params', 'oplb_gradebook_plupload_default_params');

//legacy update
$option = get_option('oplb_gradebook_features_tracker');

if (!$option || floatval($option) < 0.3) {

    oplb_gradebook_custom_page(OPLB_GRADEBOOK_STORAGE_SLUG, 'OpenLab Gradebook Storage');
    update_option('oplb_gradebook_features_tracker', OPENLAB_GRADEBOOK_FEATURES_TRACKER);
}