<?php
/*
Plugin Name: GradeBook
Plugin URI: http://www.aorinevo.com/
Description: A simple GradeBook plugin
Version: 5.0.1
Author: Aori Nevo
Author URI: http://www.aorinevo.com
License: GPL
*/

define( "AN_GRADEBOOK_VERSION", "4.0.11");

$database_file_list = glob(dirname( __FILE__ ).'/database/*.php');
foreach($database_file_list as $database_file){
	include($database_file);
}

$angb_database = new ANGB_DATABASE();
$an_gradebook_api = new an_gradebook_api();
$an_gradebook_course_api = new gradebook_course_API();
$an_gradebook_assignment_api = new gradebook_assignment_API();
$an_gradebook_cell_api = new gradebook_cell_API();
$an_gradebookapi = new AN_GradeBookAPI();
$angb_course_list = new ANGB_COURSE_LIST();
$angb_gradebook = new ANGB_GRADEBOOK();
$angb_user = new ANGB_USER();
$angb_user_list = new ANGB_USER_LIST();
$angb_statistics = new ANGB_STATISTICS();

function register_an_gradebook_menu_page(){
		$roles = wp_get_current_user()->roles;
		$my_admin_page = add_menu_page( 'GradeBook', 'GradeBook', $roles[0], 'an_gradebook', 'init_an_gradebook', 'dashicons-book-alt', '6.12' );
		$add_submenu_page_settings = in_array($roles[0], array_keys(get_option('an_gradebook_settings')));
		if ($add_submenu_page_settings) {
 			add_submenu_page( 'an_gradebook', 'Settings', 'Settings', 'administrator', 'an_gradebook_settings', 'init_an_gradebook_settings' );
 		}
}
add_action( 'admin_menu', 'register_an_gradebook_menu_page' );


function enqueue_an_gradebook_scripts($hook){
	$app_base = plugins_url('js',__FILE__);
	wp_register_script( 'init_gradebookjs', $app_base.'/init_gradebook.js', array('jquery'), null, true);
	wp_enqueue_script('init_gradebookjs');
	if( $hook == "toplevel_page_an_gradebook" || $hook=='gradebook_page_an_gradebook_settings'){
		$an_gradebook_develop = false;
		wp_register_style( 'jquery_ui_css', $app_base.'/lib/jquery-ui/jquery-ui.css', array(), null, false );
		wp_register_style( 'GradeBook_css', plugins_url('GradeBook.css',__File__), array('bootstrap_css','jquery_ui_css'), null, false );
		wp_register_style( 'bootstrap_css', $app_base.'/lib/bootstrap/css/bootstrap.css', array(), null, false);
		wp_register_script( 'requirejs', $app_base.'/require.js', array(), null, true);
		wp_enqueue_style('GradeBook_css');
		wp_enqueue_script('requirejs');
		wp_localize_script( 'requirejs', 'require', array(
			'baseUrl' => $app_base,
			'deps'    => array( $app_base . ($an_gradebook_develop ? '/an-gradebook-app.js' : '/an-gradebook-app-min.js')
		)));
	} else {
		return;
	}

}
add_action( 'admin_enqueue_scripts', 'enqueue_an_gradebook_scripts');

function init_an_gradebook(){
		$template_list = glob(dirname( __FILE__ ).'/js/app/templates/*.php');

		foreach($template_list as $template){
			include($template);
		}
}

function init_an_gradebook_settings(){
	ob_start();
	include( dirname( __FILE__ ) . '/js/app/templates/settings-template.php' );
	include( dirname( __FILE__ ) . '/js/app/templates/ajax-template.php' );
	echo ob_get_clean();
}

function an_gradebook_my_delete_user( $user_id ) {
	global $wpdb;
	$results1 = $wpdb->delete('an_gradebook_users',array('uid'=>$user_id));
	$results2 = $wpdb->delete('an_gradebook_cells',array('uid'=>$user_id));
}
add_action( 'delete_user', 'an_gradebook_my_delete_user' );

function an_gradebook_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}
add_action('wp_head','an_gradebook_ajaxurl');

function an_gradebook_shortcode (){
	init_an_gradebook();
	$an_gradebook_develop = false;
	$app_base = plugins_url('js',__FILE__);
	wp_register_script( 'init_front_end_gradebookjs', $app_base.'/init_front_end_gradebook.js', array('jquery'), null, true);
	wp_enqueue_script('init_front_end_gradebookjs');
	if( 1==1){
		wp_register_style( 'jquery_ui_css', $app_base.'/lib/jquery-ui/jquery-ui.css', array(), null, false );
		wp_register_style( 'GradeBook_css', plugins_url('GradeBook.css',__File__), array('bootstrap_css','jquery_ui_css'), null, false );
		wp_register_style( 'bootstrap_css', $app_base.'/lib/bootstrap/css/bootstrap.css', array(), null, false);
		wp_register_script( 'requirejs', $app_base.'/require.js', array(), null, true);
		wp_enqueue_style('GradeBook_css');
		wp_enqueue_script('requirejs');
		wp_localize_script( 'requirejs', 'require', array(
			'baseUrl' => $app_base,
			'deps'    => array( $app_base . ($an_gradebook_develop ? '/an-gradebook-app.js' : '/an-gradebook-app-min.js')
		)));
	} else {
		return;
	}
	return '<div id="wpbody-content"></div>';
}
add_shortcode('an_gradebook', 'an_gradebook_shortcode');

?>
