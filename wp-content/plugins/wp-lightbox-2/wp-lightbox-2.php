<?php
/**
 * Plugin Name: WP Lightbox 2
 * Plugin URI: http://wpdevart.com/wordpress-lightbox-plugin
 * Description: WP Lightbox 2 is awesome tool for adding responsive lightbox (overlay) effect for images and also create lightbox for photo albums/galleries on your WordPress blog. WordPress Lightbox is one of the most useful plugins for your website.
 * Version: 3.0.6.2
 * Author:Syed Balkhi
 * Author URI: http://syedbalkhi.com
 * License: GNU General Public License, v2 (or newer)
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/*  Copyright 2015 Syed Balkhi 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action( 'plugins_loaded', 'jqlb_init' );
function jqlb_init() {
	if(!defined('ULFBEN_DONATE_URL')){
		define('ULFBEN_DONATE_URL', 'http://onlinewebapplication.com/');
	}

	define('JQLB_SCRIPT', 'wp-lightbox-2.min.js');
	load_plugin_textdomain('jqlb', false, dirname( plugin_basename( __FILE__ ) ) . '/I18n/');	
	add_action('admin_init', 'jqlb_register_settings');
	//add_action('admin_menu', 'jqlb_register_menu_item');
	add_action('wp_enqueue_scripts', 'jqlb_css');	
	add_action('wp_enqueue_scripts', 'jqlb_js');
	//add_filter('plugin_row_meta', 	'jqlb_set_plugin_meta', 2, 10);	
	add_filter('the_content', 'jqlb_autoexpand_rel_wlightbox', 99);
	if(get_option('jqlb_comments') == 1){
		remove_filter('pre_comment_content', 'wp_rel_nofollow');
		add_filter('comment_text', 'jqlb_lightbox_comment', 99);
	}
}

function jqlb_set_plugin_meta( $links, $file ) { // Add a link to this plugin's settings page
	static $this_plugin;
	if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if($file == $this_plugin) {
		$settings_link = '<a href="options-general.php?page=jquery-lightbox-options">'.__('Settings', 'jqlb').'</a>';	
		array_unshift($links, $settings_link);
	}
	return $links; 
}
function jqlb_add_admin_footer(){ //shows some plugin info in the footer of the config screen.
	$plugin_data = get_plugin_data(__FILE__);
	
}	
function jqlb_register_settings(){
	register_setting( 'jqlb-settings-group', 'jqlb_automate', 'jqlb_bool_intval'); 
	register_setting( 'jqlb-settings-group', 'jqlb_comments', 'jqlb_bool_intval'); 
	register_setting( 'jqlb-settings-group', 'jqlb_resize_on_demand', 'jqlb_bool_intval');
	register_setting( 'jqlb-settings-group', 'jqlb_show_download', 'jqlb_bool_intval');
	register_setting( 'jqlb-settings-group', 'jqlb_navbarOnTop', 'jqlb_bool_intval');
	register_setting( 'jqlb-settings-group', 'jqlb_margin_size', 'floatval');
	register_setting( 'jqlb-settings-group', 'jqlb_resize_speed', 'jqlb_pos_intval');
	register_setting( 'jqlb-settings-group', 'jqlb_help_text');

	
	//register_setting( 'jqlb-settings-group', 'jqlb_follow_scroll', 'jqlb_bool_intval');
	add_option('jqlb_help_text', '');

	add_option('jqlb_automate', 1); //default is to auto-lightbox.
	add_option('jqlb_comments', 1);
	add_option('jqlb_resize_on_demand', 0); 
	add_option('jqlb_show_download', 0); 
	add_option('jqlb_navbarOnTop', 0);
	add_option('jqlb_resize_speed', 400); 
	//add_option('jqlb_follow_scroll', 0);  
}
function jqlb_register_menu_item() {		
	add_options_page('WP Lightbox Options', 'WP Lightbox 2', 'manage_options', 'jquery-lightbox-options', 'jqlb_options_panel');
}
function jqlb_get_locale(){
	//$lang_locales and ICL_LANGUAGE_CODE are defined in the WPML plugin (http://wpml.org/)
	global $lang_locales;
	if (defined('ICL_LANGUAGE_CODE') && isset($lang_locales[ICL_LANGUAGE_CODE])){
		$locale = $lang_locales[ICL_LANGUAGE_CODE];
	} else {
		$locale = get_locale();
	}
	return $locale;
}
function jqlb_css(){	
	if(is_admin() || is_feed()){return;}
	$locale = jqlb_get_locale();
	$fileName = "lightbox.min.{$locale}.css";	
	$path = plugin_dir_path(__FILE__)."styles/{$fileName}";

	if(!is_readable($path)){
		$fileName = 'lightbox.min.css';
	}
	wp_enqueue_style('wp-lightbox-2.min.css', plugin_dir_url(__FILE__).'styles/'.$fileName, false, '1.3.4');
	// add custom (dinamic) styles for wp lightbox 2
	?>

	  <?php	
}
function jqlb_js() {			   	
	if(is_admin() || is_feed()){return;}
	wp_enqueue_script('jquery', '', array(), '1.7.1', true);			
	wp_enqueue_script('wp-jquery-lightbox', plugins_url(JQLB_SCRIPT, __FILE__ ),  Array('jquery'), '1.3.4.1', true);
	global $wp_lightbox_2;
	$wp_lightbox_2->parametrs;

	$parametrs_array=array(
		'fitToScreen' => get_option('jqlb_resize_on_demand'),
		'resizeSpeed' => get_option('jqlb_resize_speed'),
		'displayDownloadLink' => get_option('jqlb_show_download'),
		'navbarOnTop' => get_option('jqlb_navbarOnTop'),
		'loopImages' => get_option('jqlb_loopImages'),
		'resizeCenter' => get_option('jqlb_resizeCenter'),
		'marginSize' => get_option('jqlb_margin_size'),
		'linkTarget' => get_option('jqlb_link_target'),
		'help' => __(get_option('jqlb_help_text'), 'jqlb'),
		'prevLinkTitle' => $wp_lightbox_2->parametrs->get_design_settings['jqlb_previous_image_title'],
		'nextLinkTitle' => $wp_lightbox_2->parametrs->get_design_settings['jqlb_next_image_title'],
		'prevLinkText' =>  __('&laquo; Previous', 'jqlb'),
		'nextLinkText' => __('Next &raquo;', 'jqlb'),
		'closeTitle' => $wp_lightbox_2->parametrs->get_design_settings['jqlb_close_image_title'],
		'image' => __('Image ', 'jqlb'),
		'of' => __(' of ', 'jqlb'),
		'download' => __('Download', 'jqlb')
	);

	foreach($wp_lightbox_2->parametrs->get_design_settings as $key =>$value){
		$parametrs_array[$key]	=$value;
	}
	wp_localize_script('wp-jquery-lightbox', 'JQLBSettings', $parametrs_array);
}

function jqlb_lightbox_comment($comment){
	$comment = str_replace('rel=\'external nofollow\'','', $comment);
	$comment = str_replace('rel=\'nofollow\'','', $comment);
	$comment = str_replace('rel="external nofollow"','', $comment);
	$comment = str_replace('rel="nofollow"','', $comment);
	return jqlb_autoexpand_rel_wlightbox($comment);
}

function jqlb_autoexpand_rel_wlightbox($content) {
	if(get_option('jqlb_automate') == 1){
		global $post;	
		$id = ($post->ID) ? $post->ID : -1;
		$content = jqlb_do_regexp($content, $id); //legacy regex function when images don't have rel tags
		$content = wplbtwo_do_regexp($content, $id);
	}			
	return $content;
}
function jqlb_apply_lightbox($content, $id = -1){
	if(!isset($id) || $id === -1){
		$id = time().rand(0, 32768);
	}
	return jqlb_do_regexp($content, $id);
}

/* automatically insert rel="lightbox[nameofpost]" to every image with no manual work.
	if there are already rel="lightbox[something]" attributes, they are not clobbered.
	Michael Tyson, you are a regular expressions god! - http://atastypixel.com */
function jqlb_do_regexp($content, $id){
	$id = esc_attr($id);
	$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)(?![^>]*?rel=.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)\?{0,1}\S{0,}['\"][^\>]*)>/i";
	$replacement = '$1 rel="lightbox['.$id.']">';
	return preg_replace($pattern, $replacement, $content);
}

/**
 * Automatically includes 'lightbox[$id]' into rel tag of images.
 *
 * @param $content
 * @param $id
 *
 * @return mixed
 *
 * @since 3.0.6.2
 */
function wplbtwo_do_regexp($content, $id){
	$id = esc_attr($id);
	$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)\?{0,1}\S{0,}['\"][^\>]*)(rel=['\"])(.*?)>/i";
	$replacement = '$1 $2lightbox['.$id.'] $3>';
	return preg_replace($pattern, $replacement, $content);
}

function jqlb_bool_intval($v){
	return $v == 1 ? '1' : '0';
}

function jqlb_pos_intval($v){
	return abs(intval($v));
}
function jqlb_options_panel(){
	if(!function_exists('current_user_can') || !current_user_can('manage_options')){
			die(__('Cheatin&#8217; uh?', 'jqlb'));
	} 
	add_action('in_admin_footer', 'jqlb_add_admin_footer');
	?>
	
	<div class="wrap">
	<h2>WP Lightbox 2</h2>	
	<?php include_once(plugin_dir_path(__FILE__).'about.php'); ?>
	<form method="post" action="options.php">
		<table>
		<?php settings_fields('jqlb-settings-group'); ?>
			<tr valign="baseline" colspan="2">
				<td colspan="">
					<?php $check = get_option('jqlb_automate') ? ' checked="yes" ' : ''; ?>
					<input type="checkbox" id="jqlb_automate" name="jqlb_automate" value="1" <?php echo $check; ?>/>
					<label for="jqlb_automate" title="<?php _e('Let the plugin add necessary html to image links', 'jqlb') ?>"> <?php _e('Auto-lightbox image links', 'jqlb') ?></label>
				</td>
			</tr>
			<tr valign="baseline" colspan="2">
				<td colspan="2">
					<?php $check = get_option('jqlb_comments') ? ' checked="yes" ' : ''; ?>
					<input type="checkbox" id="jqlb_comments" name="jqlb_comments" value="1" <?php echo $check; ?>/>
					<label for="jqlb_comments" title="<?php _e('Note: this will disable the nofollow-attribute of comment links, that otherwise interfere with the lightbox.', 'jqlb') ?>"> <?php _e('Enable lightbox in comments (disables <a href="http://codex.wordpress.org/Nofollow">the nofollow attribute!</a>)', 'jqlb') ?></label>
				</td>
			</tr>
			<tr valign="baseline" colspan="2">
				<td>
					<?php $check = get_option('jqlb_show_download') ? ' checked="yes" ' : ''; ?>
					<input type="checkbox" id="jqlb_show_download" name="jqlb_show_download" value="1" <?php echo $check; ?> />
					<label for="jqlb_show_download"> <?php _e('Show download link', 'jqlb') ?> </label>
			</td>
				<td>
				<?php $target = get_option('jqlb_link_target'); ?>
				<label for="jqlb_link_target" title="<?php _e('_blank: open the image in a new window or tab
_self: open the image in the same frame as it was clicked (default)
_parent: open the image in the parent frameset
_top: open the image in the full body of the window', 'jqlb') ?>"><?php _e('Target for download link:', 'jqlb'); ?></label> 
				<select id="jqlb_link_target" name="jqlb_link_target">
					<option <?php if ('_blank' == $target)echo 'selected="selected"'; ?>>_blank</option>
					<option <?php if ('_self' == $target)echo 'selected="selected"'; ?>>_self</option>
					<option <?php if ('_top' == $target)echo 'selected="selected"'; ?>>_top</option>
					<option <?php if ('_parent' == $target)echo 'selected="selected"'; ?>>_parent</option>
				</select>							
				</td>
			</tr>
      <tr valign="baseline" colspan="2">
        <td colspan="2"> 
          <?php $check = get_option('jqlb_navbarOnTop') ? ' checked="yes" ' : ''; ?>
          <input type="checkbox" id="jqlb_navbarOnTop" name="jqlb_navbarOnTop" value="1" <?php echo $check; ?> />
          <label for="jqlb_navbarOnTop">
            <?php _e('Show image info on top', 'jqlb') ?>
          </label>
        </td>
      </tr>
      <tr valign="baseline" colspan="2">
			<td>
				<?php $check = get_option('jqlb_resize_on_demand') ? ' checked="yes" ' : ''; ?>
				<input type="checkbox" id="jqlb_resize_on_demand" name="jqlb_resize_on_demand" value="1" <?php echo $check; ?> />
				<label for="jqlb_resize_on_demand"><?php _e('Shrink large images to fit smaller screens', 'jqlb') ?></label> 
			</td>
			<?php IF($check != ''): ?>
			<td>					
				<input type="text" id="jqlb_margin_size" name="jqlb_margin_size" value="<?php echo floatval(get_option('jqlb_margin_size')) ?>" size="3" />
				<label for="jqlb_margin_size" title="<?php _e('Keep a distance between the image and the screen edges.', 'jqlb') ?>"><?php _e('Minimum margin to screen edge (default: 0)', 'jqlb') ?></label>			
			</td>
			<?php ENDIF; ?>
		</tr>					
		<tr valign="baseline" colspan="2">
			<td colspan="2">					
				<input type="text" id="jqlb_resize_speed" name="jqlb_resize_speed" value="<?php echo intval(get_option('jqlb_resize_speed')) ?>" size="3" />
				<label for="jqlb_resize_speed"><?php _e('Animation duration (in milliseconds) ', 'jqlb') ?></label>			
			</td>
		</tr>
		<tr valign="baseline" colspan="2">			
			<td>
				<input type="text" id="jqlb_help_text" name="jqlb_help_text" value="<?php echo get_option('jqlb_help_text'); ?>" size="30" />		
				<label for="jqlb_help_text"><?php _e('Help text (default: none) ', 'jqlb'); ?></label>						
			</td>			
		</tr>			
		 </table>
		<p style="font-size:xx-small;font-style:italic;"><?php _e('Browse images with your keyboard: Arrows or P(revious)/N(ext) and X/C/ESC for close.', 'jqlb'); ?></p>
		<p class="submit">
		  <input type="submit" name="Submit" value="<?php _e('Save Changes', 'jqlb') ?>" />
		</p>
	</form>
	<?php
		$locale = jqlb_get_locale();
		$diskfile = plugin_dir_path(__FILE__)."I18n/howtouse-{$locale}.html";
		if (!file_exists($diskfile)){
			$diskfile = plugin_dir_path(__FILE__).'I18n/howtouse.html';
		}
		$text = false;
		if(function_exists('file_get_contents')){
			$text = @file_get_contents($diskfile);
		} else {
			$text = @file($diskfile);
			if($text !== false){
				$text = implode("", $text);
		      }
		}
		if($text === false){
			$text = '<p>The documentation files are missing! Try <a href="http://wordpress.org/extend/plugins/wp-lightbox-2/">downloading</a> and <a href="http://wordpress.org/extend/plugins/wp-lightbox-2/installation/">re-installing</a> this lightbox plugin.</p>';
		}
		echo $text;
	?>
	</div>	
<?php }
function jqlb_hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';
	if(empty($color))
          return $default; 

        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        $rgb =  array_map('hexdec', $hex);

        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }
        return $output;
}
class wp_lightbox_2{
	// required variables
	
	private $plugin_url;
	
	private $plugin_path;
	
	private $version;
	
	public $options;
	
	public $parametrs;	
	
	function __construct(){
		
		$this->plugin_url  = trailingslashit( plugins_url('', __FILE__ ) );
		$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->version     = 1.0;
		require_once($this->plugin_path.'includes/install_database.php');		
		$this->parametrs = new wp_lightbox2_database_params();	
		$this->call_base_filters();
		$this->create_admin_menu();	

	}
	
	private function create_admin_menu(){
		
		require_once($this->plugin_path.'admin/admin_menu.php');
		
		$admin_menu = new wp_lightbox_admin_menu(array('plugin_url' => $this->plugin_url,'plugin_path' => $this->plugin_path,'databese_parametrs' =>$this->parametrs));
		
		add_action('admin_menu', array($admin_menu,'create_menu'));
		
	}	
	public function registr_requeried_scripts(){		
		wp_register_script('angularejs',$this->plugin_url.'admin/scripts/angular.min.js');
		//wp_register_script('youtube_front_end_api_js',$this->plugin_url.'fornt_end/scripts/youtube_plus_front_end.js',array('jquery'));
		//wp_register_script('youtube_api_js',"https://www.youtube.com/iframe_api",array('youtube_front_end_api_js'));
		wp_register_style('admin_style_wp_lightbox',$this->plugin_url.'admin/styles/admin_themplate.css');
		wp_register_style('jquery-ui-style',$this->plugin_url.'admin/styles/jquery-ui.css');		
	}
	public function enqueue_requeried_scripts(){	
		wp_enqueue_style("jquery-ui-style");
		wp_enqueue_script("jquery-ui-slider");
	}
	public function call_base_filters(){
		add_action( 'init',  array($this,'registr_requeried_scripts') );
		add_action( 'admin_head',  array($this,'enqueue_requeried_scripts') );
	}
  	

}
$wp_lightbox_2 = new wp_lightbox_2();