<?php
/**
 * Plugin Name: WP Lightbox 2
 * Plugin URI: http://yepinol.com/lightbox-2-plugin-wordpress/
 * Description: This plugin used to add the lightbox (overlay) effect to the current page images on your WordPress blog.
 * Version:       2.28.8.1
 * Author:        Pankaj Jha
 * Author URI:    http://onlinewebapplication.com/
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/*  Copyright 2011 Pankaj Jha (onlinewebapplication.com)

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

/*2.21 - Image Map, Shrink large images to fit smaller screens*/
/*2.22 - Fixed one s, that caused a fatal error*/
/*2.23 - Updated jQuery calls for faster load*/
/*2.24 - Compatible with wordpress 3.4.2*/
/*2.25 - Fixed PHP 5 bug*/
/*2.26 - Compatible with wordpress 3.5*/
/*2.27 - Compatible with wordpress 3.5.1*/
/*
2.28 - wp_print_scripts and wp_print_styles should not be used to enqueue styles or scripts on the front page. Use wp_enqueue_scripts instead.
*/
/*2.28.1 - Fixed PHP 5 comment bug that got reintroduced into plugin*/
/*2.28.2 - Compatible with wordpress HTML5 Themes */
/*2.28.3 - Fixed HTML5 Themes support issues. */
/*2.28.4 - Compatible with wordpress 3.6*/
/*2.28.5 - Compatible with wordpress 3.6.1*/
/*2.28.6.1 - Fixed navigation issue (minor release)*/
/*2.28.7 - Compatible with wordpress 3.7.1*/
/*2.28.8 - Compatible with wordpress 3.8*/
/*2.28.8.1 - Fixed navigation issue*/
add_action( 'plugins_loaded', 'jqlb_init' );
function jqlb_init() {
	if(!defined('ULFBEN_DONATE_URL')){
		define('ULFBEN_DONATE_URL', 'http://onlinewebapplication.com/');
	}
	
	//JQLB_PLUGIN_DIR == plugin_dir_path(__FILE__); 
	//JQLB_URL = plugin_dir_url(__FILE__);
	//JQLB_STYLES_URL = plugin_dir_url(__FILE__).'styles/'
	//JQLB_LANGUAGES_DIR = plugin_dir_path(__FILE__) . 'I18n/'
	define('JQLB_SCRIPT', 'wp-lightbox-2.min.js');
	load_plugin_textdomain('jqlb', false, dirname( plugin_basename( __FILE__ ) ) . '/I18n/');	
	//load_plugin_textdomain('jqlb', false, plugin_dir_path(__FILE__).'I18n/');
	add_action('admin_init', 'jqlb_register_settings');
	add_action('admin_menu', 'jqlb_register_menu_item');
	add_action('wp_enqueue_scripts', 'jqlb_css');	
	add_action('wp_enqueue_scripts', 'jqlb_js');
	add_filter('plugin_row_meta', 	'jqlb_set_plugin_meta', 2, 10);	
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
	register_setting( 'jqlb-settings-group', 'jqlb_link_target');
	
	//register_setting( 'jqlb-settings-group', 'jqlb_follow_scroll', 'jqlb_bool_intval');
	add_option('jqlb_help_text', '');
	add_option('jqlb_link_target', '_self');
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
}
function jqlb_js() {			   	
	if(is_admin() || is_feed()){return;}
	wp_enqueue_script('jquery', '', array(), '1.7.1', true);			
	wp_enqueue_script('wp-jquery-lightbox', plugins_url(JQLB_SCRIPT, __FILE__ ),  Array('jquery'), '1.3.4.1', true);
	wp_localize_script('wp-jquery-lightbox', 'JQLBSettings', array(
		'fitToScreen' => get_option('jqlb_resize_on_demand'),
		'resizeSpeed' => get_option('jqlb_resize_speed'),
		'displayDownloadLink' => get_option('jqlb_show_download'),
		'navbarOnTop' => get_option('jqlb_navbarOnTop'),
		'loopImages' => get_option('jqlb_loopImages'),
		'resizeCenter' => get_option('jqlb_resizeCenter'),
		'marginSize' => get_option('jqlb_margin_size'),
		'linkTarget' => get_option('jqlb_link_target'),
		//'followScroll' => get_option('jqlb_follow_scroll'),
		/* translation */
		'help' => __(get_option('jqlb_help_text'), 'jqlb'),
		'prevLinkTitle' => __('previous image', 'jqlb'),
		'nextLinkTitle' => __('next image', 'jqlb'),
		'prevLinkText' =>  __('&laquo; Previous', 'jqlb'),
		'nextLinkText' => __('Next &raquo;', 'jqlb'),
		'closeTitle' => __('close image gallery', 'jqlb'),
		'image' => __('Image ', 'jqlb'),
		'of' => __(' of ', 'jqlb'),
		'download' => __('Download', 'jqlb')
	));
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
		$content = jqlb_do_regexp($content, $id);
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
	$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)\?{0,1}\S{0,}['\"][^\>]*)>/i";
	$replacement = '$1 rel="lightbox['.$id.']">';
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
     <div id="sideblock" style="float:right;width:270px;margin-left:10px;"> 
		 <iframe width=270 height=500 frameborder="0" src="http://demos.onlinewebapplication.com/wp-internal-links/SEOIinternalLinks.html"></iframe>
 	</div>
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
<?php }?>
