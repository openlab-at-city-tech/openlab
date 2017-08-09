<?php
/*
Plugin Name: OpenLab GradeBook
Plugin URI: https://github.com/livinglab/openlab
Description: A modification of AN Gradebook https://wordpress.org/plugins/an-gradebook/
Version: 0.0.1
Author: Joe Unander
Author URI: http://early-adopter.com/
License: GPL
*/

define( "OPENLAB_GRADEBOOK_VERSION", "0.0.1");

$database_file_list = glob(dirname( __FILE__ ).'/database/*.php');
foreach($database_file_list as $database_file){
	include($database_file);
}	

$angb_database = new ANGB_DATABASE();
$oplb_gradebook_api = new oplb_gradebook_api();  
$oplb_gradebook_course_api = new gradebook_course_API();
$oplb_gradebook_assignment_api = new gradebook_assignment_API();
$oplb_gradebook_cell_api = new gradebook_cell_API();
$oplb_gradebookapi = new OPLB_GradeBookAPI();
$angb_course_list = new ANGB_COURSE_LIST();
$angb_gradebook = new ANGB_GRADEBOOK();
$angb_user = new ANGB_USER();
$angb_user_list = new ANGB_USER_LIST();
$angb_statistics = new ANGB_STATISTICS();

function register_oplb_gradebook_menu_page(){	
		$roles = wp_get_current_user()->roles;
                
                //in at least one case a super admin was not properly assigned a role
                if(empty($roles) && is_super_admin()){
                    $roles[0] = 'administrator';
                }
                
		$my_admin_page = add_menu_page( 'OpenLab GradeBook', 'OpenLab GradeBook', $roles[0], 'oplb_gradebook', 'init_oplb_gradebook', 'dashicons-book-alt', '6.12' ); 			
		$add_submenu_page_settings = in_array($roles[0], array_keys(get_option('oplb_gradebook_settings')));
		if ($add_submenu_page_settings) {		
 			add_submenu_page( 'oplb_gradebook', 'Settings', 'Settings', 'administrator', 'oplb_gradebook_settings', 'init_oplb_gradebook_settings' );		
 		}
} 	
add_action( 'admin_menu', 'register_oplb_gradebook_menu_page' );	

	
function enqueue_oplb_gradebook_scripts($hook){
	$app_base = plugins_url('js',__FILE__);		
	wp_register_script( 'init_gradebookjs', $app_base.'/init_gradebook.js', array('jquery'), null, true);	
	wp_enqueue_script('init_gradebookjs');		
	if( $hook == "toplevel_page_oplb_gradebook" || $hook=='gradebook_page_oplb_gradebook_settings'){
		$oplb_gradebook_develop = true;
		wp_register_style( 'jquery_ui_css', $app_base.'/lib/jquery-ui/jquery-ui.css', array(), null, false );	
		wp_register_style( 'OplbGradeBook_css', plugins_url('GradeBook.css',__File__), array('bootstrap_css','jquery_ui_css'), null, false );				
		wp_register_style( 'bootstrap_css', $app_base.'/lib/bootstrap/css/bootstrap.css', array(), null, false);	
		wp_register_script( 'requirejs', $app_base.'/require.js', array(), null, true);		
		wp_enqueue_style('OplbGradeBook_css');								
		wp_enqueue_script('requirejs');					
		wp_localize_script( 'requirejs', 'require', array(
			'baseUrl' => $app_base,				
			'deps'    => array( $app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js' : '/oplb-gradebook-app-min.js')
		)));
	} else {
		return;
	}
			
}
add_action( 'admin_enqueue_scripts', 'enqueue_oplb_gradebook_scripts');

function init_oplb_gradebook(){	
		$template_list = glob(dirname( __FILE__ ).'/js/app/templates/*.php');
                
		foreach($template_list as $template){
                    
                        //get template name
                        $template_explode = explode('/', $template);    
                        $template_filename = str_replace('.php','',array_pop($template_explode));
                        echo "<script id='{$template_filename}' type='text/template'>";
                            include($template);
                        echo "</script>";
		}	
}

function init_oplb_gradebook_settings(){
	ob_start();	
	include( dirname( __FILE__ ) . '/js/app/templates/settings-template.php' );	
	include( dirname( __FILE__ ) . '/js/app/templates/ajax-template.php' );		
	echo ob_get_clean();
}

function oplb_gradebook_my_delete_user( $user_id ) {
	global $wpdb;
	$results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users",array('uid'=>$user_id));
	$results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells",array('uid'=>$user_id));	
}
add_action( 'delete_user', 'oplb_gradebook_my_delete_user' );

function oplb_gradebook_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}
add_action('wp_head','oplb_gradebook_ajaxurl');

function oplb_gradebook_shortcode (){
	init_oplb_gradebook();
	$oplb_gradebook_develop = false;	
	$app_base = plugins_url('js',__FILE__);		
	wp_register_script( 'init_front_end_gradebookjs', $app_base.'/init_front_end_gradebook.js', array('jquery'), null, true);	
	wp_enqueue_script('init_front_end_gradebookjs');		
	if( 1==1){
		wp_register_style( 'jquery_ui_css', $app_base.'/lib/jquery-ui/jquery-ui.css', array(), null, false );	
		wp_register_style( 'OplbGradeBook_css', plugins_url('GradeBook.css',__File__), array('bootstrap_css','jquery_ui_css'), null, false );				
		wp_register_style( 'bootstrap_css', $app_base.'/lib/bootstrap/css/bootstrap.css', array(), null, false);	
		wp_register_script( 'requirejs', $app_base.'/require.js', array(), null, true);		
		wp_enqueue_style('OplbGradeBook_css');							
		wp_enqueue_script('requirejs');					
		wp_localize_script( 'requirejs', 'require', array(
			'baseUrl' => $app_base,				
			'deps'    => array( $app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js' : '/oplb-gradebook-app-min.js')
		)));
	} else {
		return;
	}
	return '<div id="wpbody-content"></div>';
}
add_shortcode('oplb_gradebook', 'oplb_gradebook_shortcode');

?>