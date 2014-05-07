<?php
/*
Plugin Name: Edge Suite
Plugin URI: http://wordpress.org/plugins/edge-suite/
Description: Upload Adobe Edge compositions to your website.
Author: Timm Jansen
Author URI: http://www.timmjansen.com/
Donate link: http://www.timmjansen.com/donate
Version: 0.6
*/

/*  Copyright 2013 Timm Jansen (email: info at timmjansen.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * This is a port of the Drupal Edge Suite module (done by me as well) for wordpress.
*/

require_once('includes/edge-suite-general.php');
require_once('includes/edge-suite-comp.inc');

/**
 * Set all needed constants
 */
function edge_suite_init_constants(){
  // Respect general upload path.
  $upload_dir = get_option('upload_path');
  if (empty($upload_dir)) {
    $upload_dir = 'wp-content/uploads';
  }
  $upload_dir = untrailingslashit($upload_dir);

  define('EDGE_SUITE_PUBLIC_DIR_REL', get_bloginfo('wpurl') . '/' . $upload_dir . '/edge_suite');
  define('EDGE_SUITE_PUBLIC_DIR', untrailingslashit(ABSPATH) . '/' . $upload_dir . '/edge_suite');

  define('EDGE_SUITE_COMP_PROJECT_DIR', EDGE_SUITE_PUBLIC_DIR . '/project');
  define('EDGE_SUITE_COMP_PROJECT_DIR_REL', EDGE_SUITE_PUBLIC_DIR_REL . '/project');


  define('EDGE_SUITE_ALLOWED_ASSET_EXTENSIONS', 'js|png|jpg|gif|svg|css|html|woff|eot|ttf|mp3|ogg|oga|wav|m4a|aac');


  define('REQUEST_TIME', time());

}

/*** UN/INSTALL ***/

function edge_suite_install() {
  // Create DB schema.
  global $wpdb;
  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "
      CREATE TABLE " . $table_name . " (
        definition_id int(11) NOT NULL AUTO_INCREMENT,
        project_name varchar(255) NOT NULL,
        composition_id varchar(255) NOT NULL,
        archive_extension varchar(255) NOT NULL,
        info longtext,
        uid int(11) NOT NULL,
        created int(11) NOT NULL,
        changed int(11) NOT NULL,
        PRIMARY KEY (definition_id)
      );
    ";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  // Default options.
  add_option('edge_suite_max_size', 5);
  add_option('edge_suite_comp_default', -1);
  add_option('edge_suite_comp_homepage', 0);
  add_option('edge_suite_deactivation_delete', 0);
  add_option('edge_suite_widget_shortcode', 0);
  add_option('edge_suite_jquery_noconflict', 0);
  add_option('edge_suite_debug', 0);

}

register_activation_hook(__FILE__, 'edge_suite_install');


function edge_suite_uninstall() {
  edge_suite_init_constants();

  global $wpdb;
  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {
    $wpdb->query('DROP TABLE ' . $table_name);
  }

  // Delete all edge directories
  rmdir_recursive(trailingslashit(EDGE_SUITE_PUBLIC_DIR));

  // Delete options
  delete_option('edge_suite_max_size');
  delete_option('edge_suite_comp_default');
  delete_option('edge_suite_comp_homepage');
  delete_option('edge_suite_deactivation_delete');
  delete_option('edge_suite_widget_shortcode');
  delete_option('edge_suite_jquery_noconflict');
  delete_option('edge_suite_debug');
}
register_uninstall_hook(__FILE__, 'edge_suite_uninstall');


function edge_suite_deactivate() {
  if(get_option('edge_suite_deactivation_delete') == 1){
    edge_suite_uninstall();
  }
}
register_deactivation_hook(__FILE__, 'edge_suite_deactivate');


/**
* Register general options.
*/
function edge_suite_options_init() {
  register_setting('edge_suite_options', 'edge_suite_max_size');
  register_setting('edge_suite_options', 'edge_suite_comp_default');
  register_setting('edge_suite_options', 'edge_suite_comp_homepage');
  register_setting('edge_suite_options', 'edge_suite_deactivation_delete');
  register_setting('edge_suite_options', 'edge_suite_widget_shortcode');
  register_setting('edge_suite_options', 'edge_suite_jquery_noconflict');
  register_setting('edge_suite_options', 'edge_suite_debug');
}

add_action('admin_init', 'edge_suite_options_init');



/** INIT **/
function edge_suite_boot() {
  // Set up basic global edge object.
  global $edge_suite;
  $edge_suite = new stdClass();
  $edge_suite->header = array();
  $edge_suite->stage = "";
  $edge_suite->msg = array();

  edge_suite_init_constants();

}

/**
 * Init function that triggers composition rendering if needed.
 */
function edge_suite_init() {
  edge_suite_boot();

  if(!is_admin()){
    // Get default composition.
    $definition_id = get_option('edge_suite_comp_default');

    // Get homepage composition.
    if (is_home()) {
      if (get_option('edge_suite_comp_homepage') != 0) {
        $definition_id = get_option('edge_suite_comp_homepage');
      }
    }
    //Get post composition
    else {
      global $post;
      if(isset($post->ID)){
        $post_id = $post->ID;
        $post_reference_id = get_post_meta($post_id, '_edge_composition', TRUE);
        if (!empty($post_reference_id)) {
          $definition_id = $post_reference_id;
        }
      }
    }

    // Render composition.
    global $edge_suite;
    $definition_res = edge_suite_comp_render($definition_id);
    // Split scripts and stage so they can be used by the respective functions.
    $edge_suite->scripts = isset($definition_res['scripts']) ? $definition_res['scripts'] : '';
    $edge_suite->stage = isset($definition_res['stage']) ? $definition_res['stage'] : '';
  }
}

add_action('wp', 'edge_suite_init');


/** COMPOSITION **/

/**
 * Add needed scripts to the header that were located during composition
 * rendering in the init phase.
 */
function edge_suite_header() {
  global $edge_suite;
  //TODO: use wp_enqueue_script()
  if(isset($edge_suite->scripts) && is_array($edge_suite->scripts)){
    print "\n" . implode("\n", $edge_suite->scripts) . "\n";
  }
}

add_action("wp_head", 'edge_suite_header');


/**
 * Theme callback to retrieve the rendered stage. The composition gets rendered
 * in the init phase. Scripts will be placed through edge_suite_header.
 * @return string
 *   Returns the rendered stage.
 */
function edge_suite_view() {
  global $edge_suite;
  return $edge_suite->stage;
}

/** MENU **/

function edge_suite_menu() {
  add_menu_page('Edge Suite', 'Edge Suite', 'edge_suite_administer', __FILE__, 'edge_suite_menu_main');
  add_submenu_page(__FILE__, 'Manage', 'Manage', 'edge_suite_administer', __FILE__, 'edge_suite_menu_main');
  add_submenu_page(__FILE__, 'Settings', 'Settings', 'edge_suite_administer', 'edge_suite_menu_settings', 'edge_suite_menu_settings');
  add_submenu_page(__FILE__, 'Usage', 'Usage', 'edge_suite_administer', 'edge_suite_menu_usage', 'edge_suite_menu_usage');
}

add_action('admin_menu', 'edge_suite_menu');

function edge_suite_menu_main() {
  include('admin/manage.php');
}

function edge_suite_menu_settings() {
  include('admin/options.php');
}

function edge_suite_menu_usage() {
  include('admin/usage.php');
}

/** CAPABILITIES **/
function edge_suite_map_meta_cap($caps, $cap, $user_id, $args) {
  $meta_caps = array(
    'edge_suite_administer' => 'manage_options',
    'edge_suite_select_composition' => 'publish_pages',
  );

  $caps = array_diff($caps, array_keys($meta_caps));

  if (isset($meta_caps[$cap])) {
    $caps[] = $meta_caps[$cap];
  }

  return $caps;
}

add_filter('map_meta_cap', 'edge_suite_map_meta_cap', 10, 4);

/** COMPOSITION BY PAGE/POST **/

/**
 * Adds a select box to posts/pages to be able to choose a composition that will
 * appear on the page.
 */
function edge_suite_add_box() {
  if (current_user_can('edge_suite_select_composition')) {
    add_meta_box('edge_suite_composition_selection', 'Edge Suite', 'edge_suite_reference_form', 'post', 'advanced', 'high');
    add_meta_box('edge_suite_composition_selection', 'Edge Suite', 'edge_suite_reference_form', 'page', 'advanced', 'high');
  }
}

add_action('admin_menu', 'edge_suite_add_box');


/**
 * Callback for post_save. It's being checked if a composition was selected for
 * the corresponding page/post within the edge_suite_box and the id of the
 * composition will be saved with it.
 * @param $id
 *   Id of the post/page
 */
function edge_suite_save_post_reference($id) {
  if (current_user_can('edge_suite_select_composition') && isset($_POST['edge_suite_composition'])) {
    $definition_id = intval($_POST['edge_suite_composition']);
    if ($definition_id != 0) {
      //    add_post_meta($id, '_edge_composition', $definition_id, true) ||
      update_post_meta($id, '_edge_composition', $definition_id);
    }
    else {
      delete_post_meta($id, '_edge_composition');
    }
  }
}

add_action('save_post', 'edge_suite_save_post_reference');


/**
 * Meta box callback
 */
function edge_suite_reference_form() {
  global $post;
  $selected = get_post_meta($post->ID, '_edge_composition', TRUE);
  $select_form = edge_suite_comp_select_form('edge_suite_composition', $selected);

  $form = $select_form;
  $form .= '<p class="description">Choose an Edge composition for the page.
  Compositions can be uploaded through the <a href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">Edge Suite Management page</a>.
  Check the <a href="/wp-admin/admin.php?page=edge_suite_menu_usage">usage page</a> for further instructions.</p>';

  echo $form;
}


/*** FORM HELPER FUNCTIONS ***/

/**
 * Returns a select form element with all available compositions keyed by composition id.
 *
 * @param $select_form_id
 *  Form name and id
 * @param string $selected
 *   Key that gets selected.
 * @param bool $default_option
 *   If set to true the option 'default' will be added.
 * @param bool $none_option
 *   If set to true the option 'none' will be added.
 *
 * @return string
 */
function edge_suite_comp_select_form($select_form_id, $selected, $default_option = TRUE, $none_option = TRUE) {
  global $wpdb;

  if (empty($selected)) {
    $selected = 0;
  }

  // Get all compositions.
  $table_name = $wpdb->prefix . "edge_suite_composition_definition";
  $definitions = $wpdb->get_results('SELECT * FROM ' . $table_name . ' ORDER BY definition_id');
  $options = array();
  foreach ($definitions as $definition) {
    $options[$definition->definition_id] = $definition->definition_id . ' - ' . $definition->project_name . ' ' . $definition->composition_id;
  }

  $form = '';
  $form .= '<select name="' . $select_form_id . '" id="' . $select_form_id . '">' . "\n";

  $options_default = array();
  if ($none_option) {
    $options_default['-1'] = 'None';
  }
  if ($default_option) {
    $options_default['0'] = 'Default';
  }

  $options_default += $options;
  foreach ($options_default as $key => $value) {
    $form .= '<option value="' . $key . '" ' . ($selected == $key ? 'selected' : '') . '>' . $value . '</option>' . "\n";
  }
  $form .= '</select>' . "\n";

  return $form;
}

function edge_suite_comp_view_iframe($definition_id, $css_style = ''){
  return edge_suite_comp_iframe($definition_id, $css_style);
}

function edge_suite_comp_view_inline($definition_id, $css_style = '', $data = array()){

  $definition_res = edge_suite_comp_render($definition_id, $css_style, $data);

  $stage = $scripts = '';

  if($definition_res != NULL){
    $scripts = implode("\n", $definition_res['scripts']);
    $stage = $definition_res['stage'];
  }

  return "\n" . $scripts . "\n" . $stage ."\n";
}


/**
 * Shortcode implementation for 'edge_animation'
 */
function edge_suite_shortcode_edge_animation( $atts ) {
  $id = -1;
  extract( shortcode_atts( array(
    'id' => '-1',
    'left' => NULL,
    'top' => NULL,
    'iframe' => FALSE,
  ), $atts ) );

  // The styles that will be added to the stage div inline or to the iframe
  $styles = '';

  // Add left position offset to style.
  if(isset($left)){
    if($left == 'auto'){
      $styles .= 'margin: 0px auto;';
    }
    else if(intval($left) != 0){
      $styles .= 'margin-left:' . $left . 'px;';
    }
  }

  //Add top position offset to style.
  if(isset($top) && intval($top) != 0){
    $styles .= 'margin-top:' . $top . 'px;';
  }

  $definition_id = $id;

  // iframe rendering
  if(isset($iframe) && $iframe){
    return edge_suite_comp_iframe($definition_id, $styles);
  }
  // Inline rendering
  else{
    return edge_suite_comp_view_inline($definition_id, $styles);
  }

}
add_shortcode('edge_animation', 'edge_suite_shortcode_edge_animation');

// Enable widget shortcut support if configured
if(get_option('edge_suite_widget_shortcode') == 1){
  add_filter('widget_text', 'do_shortcode');
}

/**
 * Callback to check and deliver a plain composition to view within an iframe
 */
function edge_suite_iframe_callback(){
  // Todo: make configurable?
  $check_referer = TRUE;

  // Check if compoistion id GET parameter is set
  // Todo: allow for admins so composition can be tested?
  if(isset($_GET['edge_suite_iframe']) && intval($_GET['edge_suite_iframe']) > 0){
    if($check_referer){
      $site_url = get_bloginfo('wpurl');
      if (!isset($_SERVER['HTTP_REFERER']) || substr($_SERVER['HTTP_REFERER'], 0, strlen($site_url)) != $site_url) {
        exit;
      }
    }

    // Todo: check permissions for the user to view the compositions?
    $definition_id = intval($_REQUEST['edge_suite_iframe']);
    // Get composition
    $content = edge_suite_comp_full_page($definition_id);
    if(!empty($content)){
      // Deliver composition content and exit
      header('Content-Type: text/html; charset=utf-8');
      print $content;
      exit;
    }
  };
}
add_action( 'template_redirect', 'edge_suite_iframe_callback' );
