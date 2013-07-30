<?php
/*
Plugin Name: ZooEffect Plugin for Video player, Photo Gallery Slideshow jQuery and audio / music / podcast - HTML5
Plugin URI: http://www.zooeffect.com/
Description: Photo Gallery with slideshow function, video players, music and podcast, many templates (players) and powerfull admin to manage your media assets without any program skills. Delivery using state of the art CDN (Content Delivery Network) included.
Author: ZooEffect
Version: 1.09
*/


function _zooeffect_plugin_ver()
{
	return 'wp1.09';
}

if (strpos($_SERVER['REQUEST_URI'], 'media-upload.php') && strpos($_SERVER['REQUEST_URI'], '&type=zooeffect') && !strpos($_SERVER['REQUEST_URI'], '&wrt='))
{
	header('Location: http://www.zooeffect.com/service.aspx?id='.get_site_option('1pluginjquery_userid').'&type=galleries&ver='._zooeffect_plugin_ver().'&rdt='.urlencode(_zooeffect_selfURL()));
	exit;
}

function _zooeffect_selfURL()
{
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";

	$protocol =  strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol =  substr($protocol, 0, strpos($protocol, "/"));
	$protocol .= $s;

	$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	$ret = $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];

	return $ret;
}

function _zooeffect_pluginURI()
{
	return get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
}

function _zooeffect_WpMedia_init() // constructor
{
	add_action('media_buttons', '_zooeffect_addMediaButton', 20);

	add_action('media_upload_zooeffect', '_zooeffect_media_upload');
	// No longer needed in WP 2.6
	if ( !function_exists('wp_enqueue_style') )
	{
		add_action('admin_head_media_upload_type_zooeffect', 'media_admin_css');
	}
      
	// check auth enabled
	//if(!function_exists('curl_init') && !ini_get('allow_url_fopen')) {}
}

function _zoo_media_menu($tabs) {
    $newtab = array('zooeffect' => __('Insert ZooEffect Photo', 'zooeffect'));
    return array_merge($tabs, $newtab);
}
add_filter('media_upload_tabs', '_zoo_media_menu');


function media_zooeffect_process() {
    media_upload_header();
    ?>
    <iframe src="http://zooeffect.com" width="100%" height="98%"></iframe>
    <?php
}
function zooeffect_menu_handle() {
    
    return wp_iframe( 'media_zooeffect_process');
}

add_action('media_upload_zooeffect', 'zooeffect_menu_handle');


function _zooeffect_addMediaButton($admin = true)
{
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

	$media_upload_iframe_src = get_option('siteurl')."/wp-admin/media-upload.php?post_id=$uploading_iframe_ID";

	$media_zooeffect_iframe_src = apply_filters('media_zooeffect_iframe_src', "$media_upload_iframe_src&amp;type=zooeffect&amp;tab=zooeffect");
	$media_zooeffect_title = __('Add ZooEffect photo', 'wp-media-zooeffect');
if($bloginfo = substr(get_bloginfo('version'), 0, 3)>=3.5):
	echo "<a onClick=\"zooEffect_launch_popup()\" class=\"insert-media \" data-editor=\"content\" title=\"Add Media\"><img src=\""._zooeffect_pluginURI()."/1plugin-icon.gif\" alt=\"$media_zooeffect_title\" /></a>"; 
else: echo "<a class=\"thickbox\" href=\"{$media_zooeffect_iframe_src}&amp;TB_iframe=true&amp;height=500&amp;width=640\" title=\"$media_zooeffect_title\"><img src=\""._zooeffect_pluginURI()."/1plugin-icon.gif\" alt=\"$media_zooeffect_title\" /></a>";endif;
}

function _zooeffect_modifyMediaTab($tabs)
{
	return array(
		'zooeffect' =>  __('ZooEffect photo', 'wp-media-zooeffect'),
	);
}

function _zooeffect_media_upload()
{
	wp_iframe('_zooeffect_media_upload_type');
}


function _zooeffect_media_upload_type()
{
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
	add_filter('media_upload_tabs', '_zooeffect_modifyMediaTab');
?>

<br />
<br />
<h2>&nbsp;&nbsp;Please Wait...</h2>

<script>

	function _zooeffect_stub()
	{
		var i = location.href.indexOf("&wrt=");

		if (i > -1)
		{
			top.send_to_editor(unescape(location.href.substring(i+5)));
		}

		//top.tb_remove();
	}

	window.onload = _zooeffect_stub;

</script>

<?php
}

_zooeffect_WpMedia_init();


// this new regex should resolve the problem of having unicode chars in the tag
define("PLUGINJQUERY_REGEXP", "/\[(?:(?:1pjq)|(?:zooeffect)) ([^\]]*)\]/");


function _zooeffect_tag($fid)
{
	return _zooeffect_async_plugin_callback(array($fid));
}

function _1pluginjquery_tag($fid)
{
	return _zooeffect_async_plugin_callback(array($fid));
}

function _zooeffect_async_plugin_callback($match)
{
	$uni = uniqid('');
	$ret = '
<!-- ZooEffect WordPress plugin '._zooeffect_plugin_ver().' (async engine): http://www.zooeffect.com/ -->

<div id="pj_widget_'.$uni.'"><img src="http://www.zooeffect.com/runtime/loading.gif" style="border:0;" alt="ZooEffect WordPress plugin" /></div>

<script type="text/javascript">
/* PLEASE CHANGE DEFAULT EXCERPT HANDLING TO CLEAN OR FULL (go to your Wordpress Dashboard/Settings/ZooEffect Options ... */

var zeo = [];
zeo["_object"] ="pj_widget_'.$uni.'";
zeo["_gid"] = "'.urlencode($match[1]).'";

var _zel = _zel || [];
_zel.push(zeo);

(function() {
	var cp = document.createElement("script"); cp.type = "text/javascript";
	cp.async = true; cp.src = "http://www.zooeffect.com/runtime/loader.js";
	var c = document.getElementsByTagName("script")[0];
	c.parentNode.insertBefore(cp, c);
})();

</script>

';

	return $ret;
}

function _zooeffect_feed_plugin_callback($match)
{
	$ret = '<img style="border:0;" src="http://www.zooeffect.com/runtime/thumb.aspx?fid='.urlencode($match[1]).'&size=large" />';

	return $ret;
}

function _zooeffect_plugin($content)
{
	$pluginjquery_excerpt_rt = get_site_option('1pluginjquery_excerpt_rt');
	if ($pluginjquery_excerpt_rt == 'remove' && (is_search() || is_category() || is_archive() || is_home()))
		return preg_replace(PLUGINJQUERY_REGEXP, '', $content);
	else if ( is_feed() )
		return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_zooeffect_feed_plugin_callback', $content));
	else
		return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_zooeffect_async_plugin_callback', $content));
}

function _zooeffect_plugin_rss($content)
{
	return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_zooeffect_feed_plugin_callback', $content));
}

//add_shortcode('zooeffect', 'zooeffect_plugin_shortcode');
add_filter('the_content', '_zooeffect_plugin');
add_filter('the_content_rss', '_zooeffect_plugin_rss');
add_filter('the_excerpt_rss', '_zooeffect_plugin_rss');
add_filter('comment_text', '_zooeffect_plugin'); 

add_action ( 'bp_get_activity_content_body', '_zooeffect_plugin' );
add_action ( 'bp_get_the_topic_post_content', '_zooeffect_plugin' );

add_action('wp_dashboard_setup', '_zooeffect_dashboard'); 

// Hook for adding admin menus
// http://codex.wordpress.org/Adding_Administration_Menus
add_action('admin_menu', '_zooeffect_mt_add_pages');

// register zooeffectWidget widget
add_action('widgets_init', create_function('', 'return register_widget("zooeffectWidget");'));


/////////////////////////////////
// dashboard widget
//////////////////////////////////
function _zooeffect_dashboard()
{
	if(function_exists('wp_add_dashboard_widget'))
		wp_add_dashboard_widget('zooeffect', 'zooeffect', '_zooeffect_dashboard_content');
}

function _zooeffect_dashboard_content()
{

	echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&continue=http%3a%2f%2fwww.zooeffect.com%2fmanage%2fwordpress-dashboard.aspx&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='100%' height='370px' scrolling='no'></iframe>";

}



// action function for above hook
function _zooeffect_mt_add_pages() {

	// Add a new submenu under Options:
	// http://codex.wordpress.org/Roles_and_Capabilities
	
	add_options_page('ZooEffect Options', '<b>ZooEffect Options</b> (1PluginjQuery)', 'install_plugins', 'zooeffectoptions', '_zooeffect_mt_options_page');

	$pluginjquery_permission_level = get_site_option('1pluginjquery_permission_level');

	if(function_exists('add_menu_page'))
	{
		add_menu_page('zooeffect', 'ZooEffect (1PluginjQuery)', $pluginjquery_permission_level, __FILE__, '_zooeffect_mt_toplevel_page');

		// kill the first menu item that is usually the identical to the menu itself
		add_submenu_page(__FILE__, '', '', $pluginjquery_permission_level, __FILE__);

		add_submenu_page(__FILE__, 'Manage Galleries', 'Manage My Galleries', $pluginjquery_permission_level, 'zoo-sub-page', '_zooeffect_mt_sublevel_monitor');
//		add_submenu_page(__FILE__, 'Media Library', 'Media Library', $pluginjquery_permission_level, 'zoo-sub-page1', '_zooeffect_mt_sublevel_library');
		add_submenu_page(__FILE__, 'Create Gallery', 'Create Gallery', $pluginjquery_permission_level, 'zoo-sub-page2', '_zooeffect_mt_sublevel_create');
//		add_submenu_page(__FILE__, 'My Account', 'My Account', $pluginjquery_permission_level, 'zoo-sub-page3', '_zooeffect_mt_sublevel_myaccount');
		add_submenu_page(__FILE__, 'Support Forum', 'Support Forum', $pluginjquery_permission_level, 'zoo-sub-page4', '_zooeffect_mt_sublevel_forum');
	}
}

function _zooeffect_isAdmin()
{
	return !function_exists('is_site_admin') || is_site_admin() == true;
}

function _zooeffect_mt_options_page() {

//	if( is_site_admin() == false ) {
//		wp_die( __('You do not have permission to access this page.') );
//	}
	
	/*
	if (strpos($_SERVER['QUERY_STRING'], 'hide_note=welcome_notice'))
	{
		update_site_option('zooeffect_welcome_notice', _zooeffect_plugin_ver());
		echo '<script type="text/javascript">jQuery(function() {jQuery("#message").hide();});</script>';
	}
	*/
	
	$pluginjquery_userid = get_site_option('1pluginjquery_userid');
	$pluginjquery_permission_level = get_site_option('1pluginjquery_permission_level');
	$pluginjquery_excerpt = get_site_option('1pluginjquery_excerpt');

	if ( isset($_POST['submit']) )
	{
		if (_zooeffect_isAdmin())
		{
			if (isset($_POST['1pluginjquery_userid']))
			{
				$pluginjquery_userid = $_POST['1pluginjquery_userid'];
				update_site_option('1pluginjquery_userid', $pluginjquery_userid);
			}
			
			if (isset($_POST['1pluginjquery_permission_level']))
			{
				$pluginjquery_permission_level = $_POST['1pluginjquery_permission_level'];
				update_site_option('1pluginjquery_permission_level', $pluginjquery_permission_level);
			}
		}

		if (isset($_POST['embedRel']))
		{
			$pluginjquery_excerpt = $_POST['embedRel'];
			update_site_option('1pluginjquery_excerpt', $pluginjquery_excerpt);			
		}
		
		echo "<div id=\"updatemessage\" class=\"updated fade\"><p>ZooEffect settings updated.</p></div>\n";
		echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";	
	}

	$disp_excerpt2 = $pluginjquery_excerpt == 'clean' ? 'checked="checked"' : '';
	$disp_excerpt3 = $pluginjquery_excerpt == 'full' ? 'checked="checked"' : '';
	$disp_excerpt4 = $pluginjquery_excerpt == 'remove' ? 'checked="checked"' : '';
	$disp_excerpt1 = $pluginjquery_excerpt == '' || $pluginjquery_excerpt == 'nothing' ? 'checked="checked"' : '';


?>
	<div class="wrap">
		<h2>ZooEffect Configuration</h2>
		<div class="postbox-container">
			<div class="metabox-holder">
				<div class="meta-box-sortables">
					<form action="" method="post" id="zooeffect-conf">
						<div id="zooeffect_settings" class="postbox">
							<div class="handlediv" title="Click to toggle">
								<br />
							</div>
							<h3 class="hndle">
								<span>ZooEffect Settings</span>
							</h3>
							<div class="inside" style="width:600px;">
								<table class="form-table">




									<?php

if (_zooeffect_isAdmin())
{
?>


									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="1pluginjquery_userid">
												ZooEffect Userid (<a target="_blank" href="http://www.zooeffect.com/">what?</a>):
											</label>
										</th>
										<td valign="top">
											<input id="1pluginjquery_userid" name="1pluginjquery_userid" type="text" size="20" value="<?php echo $pluginjquery_userid; ?>"/>
										</td>
									</tr>





									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="1pluginjquery_permission_level">
												Menu Permission (<a target="_blank" href="http://codex.wordpress.org/Roles_and_Capabilities">what?</a>):
											</label>
										</th>
										<td valign="top">
											<input id="1pluginjquery_permission_level" name="1pluginjquery_permission_level" type="text" size="20" value="<?php echo $pluginjquery_permission_level; ?>"/>
										</td>
									</tr>



									<?php
}

?>





									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="">
												Excerpt Handling (<a target="_blank" href="http://www.zooeffect.com/">what?</a>):
											</label>
										</th>
										<td valign="top">

											<input type="radio" <?php echo $disp_excerpt1; ?> id="embedCustomization0" name="embedRel" value="nothing"/>
											<label for="embedCustomization0">Do nothing (default Wordpress behavior)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt2; ?> id="embedCustomization1" name="embedRel" value="clean"/>
											<label for="embedCustomization1">Clean excerpt (do not show gallery)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt4; ?> id="embedCustomization3" name="embedRel" value="remove"/>
											<label for="embedCustomization3">Remove gallery (do not show gallery in all non post pages)</label>
											<br/>
											<input type="radio" <?php echo $disp_excerpt3; ?> id="embedCustomization2" name="embedRel" value="full"/>
											<label for="embedCustomization2">Full excerpt (show gallery)</label>
											<br/>

										</td>
									</tr>

									
									
									

									<tr style="width:100%;">
										<th valign="top" scrope="row" colspan=2>
											Note:
<ol>
<li>Use this PHP code to add a gallery directly to your template : <br>&nbsp;&nbsp;&nbsp; <i>&lt;?php echo _zooeffect_tag("GALLERY ID"); ?&gt;</i></li>
</ol>
										</th>
									</tr>


								</table>
							</div>
						</div>
						<div class="submit">
							<input type="submit" class="button-primary" name="submit" value="Update &raquo;" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
    
    
}
/*
// _zooeffect_mt_manage_page() displays the page content for the Test Manage submenu
function _zooeffect_mt_manage_page() {
    echo "<h2>Test Manage</h2>";
}
*/

function _zooeffect_mt_toplevel_page() {

    echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='98%' height='1000px'></iframe>";
}

function _zooeffect_mt_sublevel_create() {
    echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=creategallery&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='98%' height='1000px'></iframe>";
}

function _zooeffect_mt_sublevel_monitor() {
    echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='98%' height='1000px'></iframe>";
}

function _zooeffect_mt_sublevel_library() {
    echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='98%' height='1000px'></iframe>";
}

function _zooeffect_mt_sublevel_myaccount() {
    echo "<iframe src='http://www.zooeffect.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._zooeffect_plugin_ver()."&src=".urlencode(_zooeffect_selfURL())."' width='98%' height='1000px'></iframe>";
}

function _zooeffect_mt_sublevel_forum() {
    echo "<iframe src='http://www.zooeffect.com/support.aspx' width='98%' height='1000px'></iframe>";
}



if (!class_exists('zooeffectWidget')) {

	/**
	 * zooeffectWidget Class
	 */
	class zooeffectWidget extends WP_Widget {
			/** constructor */
			function zooeffectWidget() {
					parent::WP_Widget(false, $name = 'ZooEffect Gallery Widget');	
			}

			/** @see WP_Widget::widget */
			function widget($args, $instance) {		
					extract( $args );

					if (strpos($instance['galleryid'], '1pjq'))
						$gallery = _zooeffect_plugin($instance['galleryid']);
					else if (strpos($instance['galleryid'], 'zooeffect'))
						$gallery = _zooeffect_plugin($instance['galleryid']);
					else
						$gallery = _zooeffect_plugin('[zooeffect '.$instance['galleryid'].']');

					echo $gallery;
			}

			/** @see WP_Widget::update */
			function update($new_instance, $old_instance) {				
					return $new_instance;
			}

			/** @see WP_Widget::form */
			function form($instance) {				
					$galleryid = esc_attr($instance['galleryid']);
					?>
	<p>
		<label for=""
			<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Gallery ID:'); ?> <a target="_blank" href="http://www.zooeffect.com/">what?</a> <input class="widefat" id=""<?php echo $this->get_field_id('galleryid'); ?>" name="<?php echo $this->get_field_name('galleryid'); ?>" type="text" value="<?php echo $galleryid; ?>" />
		</label>
	</p>
	<?php 
			}

	} // class zooeffectWidget

}

// http://www.aaronrussell.co.uk/blog/improving-wordpress-the_excerpt/

function _zooeffect_improved_trim_excerpt($text)
{
	global $post;
	if ( '' == $text ) {
		$text = get_the_content('');

		$pluginjquery_excerpt_rt = get_site_option('1pluginjquery_excerpt');

		if ($pluginjquery_excerpt_rt == 'clean')
			$text = preg_replace(PLUGINJQUERY_REGEXP, '', $text);

		$text = apply_filters('the_content', $text);

		if ($pluginjquery_excerpt_rt == 'full')
			return $text;

		$text = str_replace(']]>', ']]&gt;', $text);
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);

		$text = strip_tags($text, '<'.'p'.'>');
		$excerpt_length = 80;
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words)> $excerpt_length) 
		{
			array_pop($words);
			array_push($words, '[...]');
			$text = implode(' ', $words);
		}
	}
			
	return $text;
}

$pluginjquery_excerpt_rt = get_site_option('1pluginjquery_excerpt');
if ($pluginjquery_excerpt_rt == 'full' || $pluginjquery_excerpt_rt == 'clean')
{
	remove_filter('get_the_excerpt', 'wp_trim_excerpt');
	//	remove_all_filters('get_the_excerpt');
	add_filter('get_the_excerpt', '_zooeffect_improved_trim_excerpt');
}

function _zooeffect_activation_notice() { 
	?>
			<div id="message" class="updated fade">
				<p style="line-height: 150%">
					<strong>Welcome to ZooEffect (1PluginjQuery) Rich Media Plugin</strong> - The best way to manage and display photo galleries and slideshows on your site.
				</p>
				<p>
					On every post page (above the text box) you'll find this  <img src="<?php echo _zooeffect_pluginURI() ?>/1plugin-icon.gif"  />  icon, click on it to start or use sidebar Widgets (Appearance menu).
				</p>
				<p>
		
				<input type="button" class="button" value="ZooEffect Options Page" 
					onclick="document.location.href = 'options-general.php?page=zooeffectoptions';" />
				<input type="button" id="zoo_welcome_hide" class="button" value="Hide this message" />


				</p>

			</div>
			
			<script type="text/javascript">
				function zoo_hide_welcome_message() {

		            jQuery.post(ajaxurl, { action: 'zoo_hide_welcome' }, 
				            function(response) {
		            			jQuery('#message').fadeOut();			            
		            		}
		            );
		            
		            return false;
				}
				
				jQuery(function () {
					jQuery('#zoo_welcome_hide').click(zoo_hide_welcome_message);	
				});
			</script>

			<?php

	if (get_site_option('1pluginjquery_installed') != 'true')
	{
		update_site_option('1pluginjquery_installed', 'true');
		echo "<img src='http://goo.gl/hXevd' width=0 height=0 />";
	}
}

add_action('wp_ajax_zoo_hide_welcome', 'zoo_hide_welcome_callback');

function zoo_hide_welcome_callback() {
	update_site_option('zooeffect_welcome_notice', _zooeffect_plugin_ver());
}

if (get_site_option('zooeffect_welcome_notice') != _zooeffect_plugin_ver())
	add_action( 'admin_notices', '_zooeffect_activation_notice' );


if (get_site_option('1pluginjquery_userid') == "")
{
	$uni = uniqid('');
	update_site_option('1pluginjquery_userid', $uni);
}

if (get_site_option('1pluginjquery_permission_level') == "")
{
	update_site_option('1pluginjquery_permission_level', 'edit_posts');
}


add_action( 'admin_footer-post-new.php', 'zooEffect_mediaDefault_script' );
add_action( 'admin_footer-post.php', 'zooEffect_mediaDefault_script' );
add_action( 'admin_footer-index.php', 'zooEffect_mediaDefault_script' );
function zooEffect_mediaDefault_script()
{
	?>
		<script type="text/javascript">
			var zooEffect_popup_timer = null;
			function zooEffect_launch_popup()
			{
				zooEffect_popup_timer = setInterval(zooEffect_check_popup, 200);
			}

			function zooEffect_check_popup()
			{
				if (jQuery(".media-menu-item:contains('ZooEffect')").length > 0)
				{
					jQuery(".media-menu-item:contains('ZooEffect')")[0].click();
					clearInterval(zooEffect_popup_timer);
					zooEffect_popup_timer = null;
				}
			}

		</script>
	<?php
} 


?>