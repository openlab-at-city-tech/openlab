<?php
/*	
Plugin Name: Digress.it
Plugin URI: http://digress.it
Description:  Digress.it allows readers to comment paragraph by paragraph in the margins of a text. You can use it to comment, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 3.1.2
Author URI: http://eddietejeda.com
License: GPLv2 (http://creativecommons.org/licenses/GPL/2.0/)

Special thanks to:	
Matteo Bicocchi @ www.open-lab.com
The developers of JQuery @ www.jquery.com
Mark James, for the famfamfam iconset @ http://www.famfamfam.com/lab/icons/silk/
Joss Winn and Tony Hirst @ writetoreply.com
Jesse Wilbur, Ben Vershbow, Dan Visel and Bob Stein @ futureofthebook.org
*/

global $commentbrowser, $blog_id, $current_user, $current_user_comments, $development_mode, $testing_mode, $production_mode;
global $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function,$is_commentbrowser, $browser;

$browser = current_browser();
$is_commentbrowser= false;

//get_currentuserinfo();

$plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 

load_plugin_textdomain('digressit', 'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/languages');

define("DIGRESSIT_VERSION", '3.1');
define("DIGRESSIT_COMMUNITY", 'digress.it');
define("DIGRESSIT_COMMUNITY_HOSTNAME", 'digress.it');
define("DIGRESSIT_REVISION", 229);
define("DIGRESSIT_DIR", WP_PLUGIN_DIR ."/". $plugin_name);
define("DIGRESSIT_CORE_DIR", DIGRESSIT_DIR . '/core');
define("DIGRESSIT_EXTENSIONS_DIR", DIGRESSIT_DIR . '/extensions');
define("DIGRESSIT_CORE_JS_DIR", DIGRESSIT_CORE_DIR . '/js');
define("DIGRESSIT_CORE_IMG_DIR", DIGRESSIT_CORE_DIR . '/images');
define("DIGRESSIT_CORE_CSS_DIR", DIGRESSIT_CORE_DIR . '/css');
define("DIGRESSIT_THEMES_DIR", DIGRESSIT_DIR . '/themes');


define("DIGRESSIT_URL", WP_PLUGIN_URL ."/". $plugin_name);
define("DIGRESSIT_CORE_URL", DIGRESSIT_URL . '/core');
define("DIGRESSIT_CORE_JS_URL", DIGRESSIT_CORE_URL . '/js');
define("DIGRESSIT_CORE_IMG_URL", DIGRESSIT_CORE_URL . '/images');
define("DIGRESSIT_CORE_CSS_URL", DIGRESSIT_CORE_URL . '/css');
define("DIGRESSIT_THEMES_URL", DIGRESSIT_URL . '/themes');



register_activation_hook(__FILE__,  'activate_digressit');
register_deactivation_hook(__FILE__, 'deactivate_digressit' );


$plugin_dir = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/';
$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes/';


register_theme_directory( $plugin_theme_link );


register_digressit_content_function('standard_digressit_content_parser');
register_digressit_content_function('discrete_digressit_content_parser');
//register_digressit_content_function('regexp_digressit_content_parser');


register_digressit_comments_function('standard_digressit_comment_parser');


register_digressit_commentbox_js('grouping_digressit_commentbox_parser');
register_digressit_commentbox_js('nogrouping_digressit_commentbox_parser');	



add_action('admin_menu', 'digressit_add_admin_menu');
//add_action('init', 'digressit_localization' );
add_action('init', 'digressit_init');




//load core files
if ($handle = opendir(DIGRESSIT_CORE_DIR)) {
	while (false !== ($file = readdir($handle))) {
		if (!@is_dir($file) && strstr($file, '-functions.php')) {
			require_once(DIGRESSIT_CORE_DIR . '/' . $file);
		}
	}
	closedir($handle);
}


//load extensions
if ($handle = opendir(DIGRESSIT_EXTENSIONS_DIR)) {
	while (false !== ($file = readdir($handle))) {
		if (@is_dir(DIGRESSIT_EXTENSIONS_DIR . "/". $file) && file_exists(DIGRESSIT_EXTENSIONS_DIR . '/' . $file  . '/' . $file.".php")) {
			require_once(DIGRESSIT_EXTENSIONS_DIR . '/' . $file  . '/' . $file.".php");
		}
	}
	closedir($handle);
}



/**
 * Check to see if this revision requires an reset of options. 
 * @todo This function should be a bit smarter
 *
 */
function digressit_init(){
	$options = get_option('digressit');
	
	if(!isset($options['revision']) || (int)$options['revision'] < 198 ){
		activate_digressit();
		
		echo "<meta http-equiv=\"refresh\" content=\"1\" >";
		
	}	
}



/**
 * Loads default settings into 	add_option('digressit', $options), initializes theme 
 */
function activate_digressit(){
	global $wpdb;
	$options = get_option('digressit');

	//PRE-3.0
	$commentpress_upgraded_to_digress_it = get_option('commentpress_upgraded_to_digress_it');
	$digressit_community_hostname = get_option('digressit_community_hostname');
	$digressit_client_password = get_option('digressit_client_password');
	$digressit_installation_key = get_option('digressit_installation_key');


	$plugin_name = str_replace("/", "", str_replace(basename( __FILE__),"",plugin_basename(__FILE__))); 
	$plugin_url = WP_PLUGIN_URL .'/' . $plugin_name . '/';		
	$plugin_file = $plugin_url. plugin_basename(__FILE__); 


	$digressit_server = 'http://'. DIGRESSIT_COMMUNITY_HOSTNAME . '/';


	$is_multiuser = is_mu_or_network_mode();			


	$theme_url = $plugin_url. 'themes/'; 

	$js_path = $plugin_url. 'js/'; 
	$jquery_path = $js_path . 'jquery/'; 
	$jquery_extensions_path =  $jquery_path. 'external/'; 
	$jquery_theme_path = $jquery_path . 'themes/'; 
	$jquery_elements_path = $jquery_path . 'elements/'; 
	$jquery_css_path = $jquery_path . 'css/'; 

	$style_path = $plugin_url . 'style/'; 
	$image_path = $plugin_url . 'themes/images/'; 
	$punctuations = null;


	$url = $_SERVER["SERVER_NAME"] ;
	preg_match("/^(http:\/\/)?([^\/]+)/i" , $url, $found);
	preg_match("/[^\.\/]+\.[^\.\/]+$/" , $found[2], $found);



	$hostname = $found[0];
	$default_skin = 'skin1';
	$default_stylesheet  = 'default';

	
	$installation_key  = null;
	$installation_key = strlen($options['installation_key']) == 32 ? $options['installation_key'] : null;

	//$options['wp_path'] = $wp_path;
	$options['debug_mode'] = 0;
	$options['allow_text_selection'] = 0;
	$options['default_skin'] = $default_skin;
	$options['stylesheet'] = $default_stylesheet;
	$options['default_left_position'] = '400px';
	$options['default_top_position'] = '175px';
	$options['allow_users_to_minimize'] = 0;
	$options['allow_users_to_resize'] = 0;
	//$options['server_sync_interval'] = $monthly;
	$options['allow_users_to_drag'] = 1;
	$options['highlight_color'] = '#FFFC00';
	$options['parse_list_items'] = 0;
	$options['enable_chrome_frame']	= 1;
	$options['front_page_post_type'] = 'post';
	$options['front_page_numberposts'] = 10;
	$options['frontpage_sidebar'] = 0;
	$options['front_page_content'] = '';
	$options['front_page_order'] = 'ASC';
	$options['front_page_order_by'] = 'date';
	$options['allow_general_comments'] = 1;
	$options['allow_comments_search'] = 0;
	$options['enable_sidebar'] = 1;
	$options['enable_instant_content_search'] = 'false';
	$options['enable_instant_comment_search'] = 'false';
	$options['show_pages_in_menu'] = 0;
	$options['table_of_contents_label'] = 'Table of Contents';
	$options['comments_by_section_label'] = 'Comments by Section';
	$options['comments_by_users_label'] = 'Comments by Users';
	$options['general_comments_label'] = 'General Comments';
	$options['sidebar_position'] = 'sidebar-widget-position-left';
	$options['auto_hide_sidebar'] = 'sidebar-widget-auto-hide';
	$options['show_comment_count_in_sidebar'] = 1;
	$options['revision'] = DIGRESSIT_REVISION;
	$options['version'] = DIGRESSIT_VERSION;
	$options['custom_style_sheet'] = '';
	$options['custom_header_image'] = '';
	$options['use_cdn'] = 0;
	$options['cdn'] = 'http://c0006125.cdn2.cloudfiles.rackspacecloud.com';
	$options['frontpage_list_style'] = 'list-style-decimal';
	$options['commentpress_upgraded_to_digress_it'] = $digressit_installation_key;
	$options['digressit_community_hostname'] = $digressit_community_hostname;
	$options['digressit_client_password'] = $digressit_client_password;
	$options['digressit_installation_key'] = $digressit_installation_key;
	$options['content_parser'] = 'standard_digressit_content_parser';
	$options['comments_parser'] = 'standard_digressit_comment_parser';
	$options['commentbox_parser'] = 'grouping_digressit_commentbox_parser';
	
	
	delete_option('digressit');
	add_option('digressit', $options);	
	

	update_option('thread_comments_depth', 2); //we default to just 2 threads.

	
	$sql = "SHOW COLUMNS FROM $wpdb->comments";	
	$columns = $wpdb->get_results($sql);

	$comment_text_signature_exists = false;
	foreach($columns as $col){
		if($col->Field == 'comment_text_signature'){
			$comment_text_signature_exists = true;
		}
	}

	

	if($comment_text_signature_exists == false){
		$sql = "ALTER TABLE `$wpdb->comments` ADD `comment_text_signature` VARCHAR( 255 ) NULL;";	
		$wpdb->query($sql);
	}



	$themes_dir = WP_CONTENT_DIR . '/themes/';
	$plugin_theme_link = WP_CONTENT_DIR . '/plugins/'. $plugin_name.'/themes/';



	$options = get_option('digressit');
	
	
	$theme_link = $themes_dir . $plugin_name;
	
	if(is_link($theme_link)){
		unlink($theme_link);
	}
	/* Since: 2.9.0 */
	if(!function_exists( 'register_theme_directory')){
		if(is_writable( $themes_dir)){

			//echo "is_writable";
			$theme_link = $themes_dir . $plugin_name;
		
			//CREATE THE THEME DIRECTORY
			if(is_link($theme_link)){
				//i think we're good
				//die( "already link");
			}
			elseif(!file_exists($theme_link)){
				if(symlink($plugin_theme_link,$theme_link)){
					//we're good
					//update_option($options['theme_mode'], 'stylesheet');
					//die( "Created link");
				}
				else{
					//die( "There was an error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. If the server doesn't have write permission try creating it manually");
				}
			}
			else{
				//die( "unknown error");
				//probably a windows person
				//die( "There was a error creating the symlink of <b>$plugin_theme_link</b> in <b>$theme_link</b>. Maybe a theme named DigressIt already exists?");					
			}
		
		
		}
		else{
			die(__('No write permission on: ').$themes_dir.__('. Please give the server write permission on this directory'));
		}
	}
	switch_theme('digressit-default', 'digressit-default');	
}


/**
 * Switches back to default theme
 */
function deactivate_digressit(){
	switch_theme('default', 'default');	
}


/**
 * Creates menu in the admin page. Also detects permalink status
 */
function digressit_add_admin_menu() {
	global $wp_rewrite;
	add_submenu_page( 'themes.php', 'Digress.it', 'Digress.it', 'administrator', 'digressit.php', 'digressit_theme_options_page');

	if(!$wp_rewrite->permalink_structure){
		add_action( 'admin_notices', 'permalink_required_notice' );
	}
}


function permalink_required_notice(){
		echo "<div id='permalink-required-notice' class='updated fade'><p>Warning: Digress.it requires permalinks to be enabled. Please go to <a href='".get_bloginfo('url')."/wp-admin/options-permalink.php'>Permalink Settings</a> and make sure that <b>Default</b> is not selected</p></div>";	
}

/**
 * Creates the theme options page. Prints out HTML
 * @todo secure forms
 */

function digressit_theme_options_page() {
	global $wpdb, $digressit_content_function, $digressit_comments_function, $digressit_commentbox_function, $blog_id;

	//var_dump($digressit_content_function);
	if($_GET['page'] == 'digressit.php' && isset($_POST['reset']) && $_POST['reset'] == 'Reset Options'){
		delete_option('digressit');
		activate_digressit();
		//echo "resetting";
		
	}
	elseif(isset($_POST['update-digressit-options'])){
		$options = get_option('digressit');
		
		foreach($_POST as $key => $value){
			$options[$key] = $value;
		}
		
		delete_option('digressit');
		add_option('digressit', $options);
		//echo "updating";
	}

	$options = get_option('digressit');
	?>

	<style>
		#wpcontent input[type=text],#wpcontent select {
		border:1px solid #DDDDDD;
		font-size:14px;
		margin:2px;
		width:auto;
		}
 		.form-table tr{
			border-bottom: 1px solid #eee;
		}	
	</style>

  	<div class="wrap" style="position: relative; font-size: 110%;">
	
		<form method="post" action="<?php $PHP_SELF; ?>">

		<h2><?php _e('Digress.it Options');  ?></h2>

		<table class="form-table" style="vertical-align: top; width: 800px; padding: 0; margin: 0" >
	



		<?php   
	
			$pages = null;
			foreach(get_pages() as $page){
				$pages[$page->post_title] = $page->ID;			
			}
		
		?>
		<tr>
			<td colspan="2"><h2>Presentation</h2></td>
		</tr>
		
		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front page content');  ?></b></td>
			<td>
			
				<?php print_dropdown('front_page_content', $pages, $options['front_page_content']); ?>
				<p>The content of this page will be the first thing a visitor to your website will see.</p>
			</td>
		</tr>
		

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Table of Contents Label');  ?></b></td>
			<td><?php print_input_text('table_of_contents_label', $options['table_of_contents_label']); ?></td>
		</tr>




		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front Page Order');  ?></b></td>
			<td><?php print_dropdown('front_page_order_by', array('id' => 'id', 'date' => 'date'), $options['front_page_order_by']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Front Page Order by');  ?></b></td>
			<td><?php print_dropdown('front_page_order', array('ASC' => 'ASC', 'DESC' => 'DESC'), $options['front_page_order']); ?></td>
		</tr>



		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Comments by Section Label');  ?></b></td>
			<td><?php print_input_text('comments_by_section_label', $options['comments_by_section_label']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Comments by Users Label');  ?></b></td>
			<td><?php print_input_text('comments_by_users_label', $options['comments_by_users_label']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('General Comments Label');  ?></b></td>
			<td><?php print_input_text('general_comments_label', $options['general_comments_label']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Frontpage List Style');  ?></b></td>
			<td><?php print_dropdown('frontpage_list_style', array(
																	'Numbers' => 'list-style-decimal', 
																	'None' => 'list-style-none',
																	'Lower Alphabet' => 'list-style-lower-alpha',
																	'Upper Alphabet' => 'list-style-upper-alpha',
																	'Lower Roman' => 'list-style-lower-roman',
																	'Upper Roman' => 'list-style-upper-roman',
																	'Square' => 'list-style-square',
																	'Circle' => 'list-style-circle'


																), $options['frontpage_list_style']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Allow General Comments');  ?></b></td>
			<td><?php print_dropdown('allow_general_comments', array('No' => 0, 'Yes' => '1'), $options['allow_general_comments']); ?></td>
		</tr>

		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Instant Content Search');  ?></b></td>
			<td><?php print_dropdown('enable_instant_content_search', array('No' => 'false', 'Yes' => 'true'), $options['enable_instant_content_search']); ?></td>
		</tr>



		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Parse List Items');  ?></b></td>
			<td><?php print_dropdown('parse_list_items', array('No' => 0, 'Yes' => 1), $options['parse_list_items']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Show Pages in Menu');  ?></b></td>
			<td><?php print_dropdown('show_pages_in_menu', array('No' => 0, 'Yes' => 1), $options['show_pages_in_menu']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Enable Sidebar');  ?></b></td>
			<td><?php print_dropdown('enable_sidebar', array('No' => 0, 'Yes' => 1), $options['enable_sidebar']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Sidebar Position');  ?></b></td>
			<td><?php print_dropdown('sidebar_position', array('Left' => 'sidebar-widget-position-left', 'Right' => 'sidebar-widget-position-right'), $options['sidebar_position']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Auto-hide Sidebar');  ?></b></td>
			<td><?php print_dropdown('auto_hide_sidebar', array('No' => 'sidebar-widget-no-auto-hide', 'Yes' => 'sidebar-widget-auto-hide'), $options['auto_hide_sidebar']); ?></td>
		</tr>
		
		<tr valign="top">
			<td style="width: 200px"><b><?php _e('In Sidebar Show');  ?></b></td>
			<td><?php print_dropdown('show_comment_count_in_sidebar', array('Comment Count' => '1', 'Section Number' => 0), $options['show_comment_count_in_sidebar']); ?></td>
		</tr>


		<tr valign="top">
			<td style="width: 200px"><b><?php _e('Custom Header Image URL');  ?></b></td>
			<td>
				
				<?php print_input_text('custom_header_image', $options['custom_header_image']); ?>
				<p>This image will override the current header and will become the logo to your site. 
					Be sure to get copy the entire URL in this field. You can also 
					<a href="<?php bloginfo('url') ?>/wp-admin/media-new.php">upload your logo</a> and get the URL from there.
					<b>Note:</b> The image needs to be a maximum of 60px tall.
			</td>
		</tr>

		<tr>
			<td style="width: 200px"><b><?php _e('Custom Style Sheet');  ?></b></td>
			<td>
				<?php print_input_text('custom_style_sheet', $options['custom_style_sheet']); ?>
				<p>If you would like to customize the theme, you can upload a stylesheet that can be be loaded after the required stylesheets. 
					For heavy customizations you should use the "Digress.it Wireframe" theme provided.
					For more information on this feature follow the instructions provided at <a href="http://digress.it/help">http://digress.it/help</a>. </p>
			</td>
		</tr>

		<tr>
			<td colspan="2"><h2>Advanced</h2></td>
		</tr>

		
		
		<?php if(is_super_admin()): ?>
		<tr>
			<td style="width: 200px"><b><?php _e('Debug Mode');  ?></b></td>
			<td><?php print_dropdown('debug_mode', array('No' => 0, 'Yes' => '1'), $options['debug_mode']); ?></td>
		</tr>
		
		<tr>
			<td style="width: 200px"><b><?php _e('Use CDN');  ?></b></td>
			<td>
			<?php print_dropdown('use_cdn', array('Yes' => '1', 'No' => 0), $options['use_cdn']); ?>
			<p>This is an experimental feature. The idea is that you can host the media files on a really fast file server. Enabling this now
				has the risk of downloading files that are out of date. Use at your own discretion.</p>				
				
			</td>
		</tr>
		
		<tr>
			<td style="width: 200px"><b><?php _e('CDN');  ?></b></td>
			<td><?php print_input_text('cdn', $options['cdn'], 'disabled'); ?>

			</td>
		</tr>
		
		<?php endif; ?>
		
		
		<tr>
			<td style="width: 200px"><b><?php _e('Content Parsing Function');  ?></b></td>
			<td><?php print_dropdown('content_parser', $digressit_content_function, $options['content_parser']); ?></td>
		</tr>

		<tr>
			<td style="width: 200px"><b><?php _e('Comments Parsing Function');  ?></b></td>
			<td><?php print_dropdown('comments_parser', $digressit_comments_function, $options['comments_parser']); ?></td>
		</tr>
	
		<tr>
			<td style="width: 200px"><b><?php _e('Comment Box Parsing Function');  ?></b></td>
			<td><?php print_dropdown('commentbox_parser', $digressit_commentbox_function, $options['commentbox_parser']); ?></td>
		</tr>
		
<!--
		<tr>
			<td style="width: 200px"><b><?php _e('Allow Comments Search');  ?></b></td>
			<td><?php print_dropdown('allow_comments_search', array('No' => 0, 'Yes' => '1'), $options['allow_comments_search']); ?></td>
			<td>Help</td>
		</tr>
-->	
	

		</table>

		<input type="hidden" name="update-digressit-options" value="1" />

		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		<input type="submit" name="reset" class="button-primary" value="<?php _e('Reset Options') ?>" />
		</p>

		</form>
	</div>
	

	

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="XYBB4WEBLRHMN">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>

	<?php 
	//restore_current_blog();
}





/**
 * This function is to future-proof how media is handled. If we are using CDN it bybasses local media assets
 */
function get_digressit_media_uri($filepath){
	$options = get_option('digressit');
	
	if((int)$options['use_cdn']){
		return $options['cdn'] ."/". basename($filepath);
	}
	else{
		return DIGRESSIT_CORE_URL ."/".$filepath;
	}
}


/**
 * Returns the system path where Digress.it is installed
 */
function get_digressit_theme_path(){
	return DIGRESSIT_THEMES_DIR."/".basename(get_template_directory());;
}

/**
 * Returns the URL path where Digress.it is installed
 */
function get_digressit_theme_uri(){
	return DIGRESSIT_THEMES_DIR . get_current_theme();
}






function register_digressit_content_function($function_name){
	global $digressit_content_function;
	$digressit_content_function[$function_name] = $function_name;
}
function register_digressit_comments_function($function_name){
	global $digressit_comments_function;
	$digressit_comments_function[$function_name] = $function_name;
}

function register_digressit_commentbox_js($function_name){
	global $digressit_commentbox_function;
	$digressit_commentbox_function[$function_name] = $function_name;
}


function print_input_text($name, $value, $attrs =null){
	echo "<input $attrs style='width: 50%' type='text' name='$name' value='$value'>";
}


function print_dropdown($name, $options = array(), $selected, $id=''){
	if($id){
		$id = " id='$id' ";
	}
	
	echo "<select $id name='$name'>";
	foreach($options as $name => $value) {
		$selected_html = ($value == $selected) ? " selected='selected' " : '';
		echo "<option $selected_html value='$value'>$name</option>";
	}
	echo "</select>";
}


/**
 * Checks to see if this is Wordpress MU (pre WP 3.0) or WP 3.0+
 */
function is_mu_or_network_mode(){

	$is_multiuser = false;

	if(function_exists('wpmu_create_blog') || (function_exists('is_multisite') && is_multisite()) ){
		$is_multiuser = true;
	}
	
	return 	$is_multiuser;
}

/**
 * Checks to see if this page is the table of contents
 */
function is_frontpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(!function_exists('is_multisite')){
		return false;
	}

	if(is_multisite() && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home() || is_front_page()){
			if($blog_id == 1){		
				return true;
			}
		}
	}
	
	return false;
}


/**
 * Checks to see if this page is the table of contents
 */
function is_mainpage(){
	global $is_frontpage, $is_mainpage, $blog_id;
	
	if(is_multisite() && file_exists(get_template_directory(). '/frontpage.php')){
		if(is_home() || is_front_page()){
			if($blog_id == 1):			
				return false;
			else:
				return true;
			endif;
		}
	}
	else{
		return false;
	}
}


/**
 * A somewhat crude way to detect user browser
 */
function current_browser() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
    if (preg_match('/opera/', $userAgent)) {
        $name = 'opera';
    }
    elseif (preg_match('/webkit/', $userAgent)) {
        $name = 'safari';
    }
    elseif (preg_match('/msie/', $userAgent)) {
        $name = 'msie';
    }
    elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) {
        $name = 'mozilla';
    }
    else {
        $name = 'unrecognized';
    }

    // What version?
    if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) {
        $version = $matches[1];
    }
    else {
        $version = 'unknown';
    }

    // Running on what platform?
    if (preg_match('/linux/', $userAgent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/', $userAgent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/', $userAgent)) {
        $platform = 'windows';
    }
    else {
        $platform = 'unrecognized';
    }

    return array(
        'name'      => $name,
        'version'   => $version,
        'platform'  => $platform,
        'userAgent' => $userAgent
    );
}

/**
 * Checks to see if we are in the comment-browser section
 */
function is_commentbrowser(){
	global $is_commentbrowser;
	return $is_commentbrowser;
}




?>
