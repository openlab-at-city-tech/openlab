<?php
/*
Plugin Name: 1 Plugin video player, Photo Gallery Slideshow jQuery and audio / music / podcast - HTML5
Plugin URI: http://1pluginjquery.com/
Description: Photo Gallery with slideshow function, video players, music and podcast, many templates (players) and powerfull admin to manage your media assets without any program skills. Delivery using state of the art CDN (Content Delivery Network) included.
Author: 1pluginjquery
Version: 1.03
*/


function _1pluginjquery_plugin_ver()
{
	return 'wp1.03';
}

function _1pluginjquery_url()
{
	return 'http://app.1pluginjquery.com';
}

if (strpos($_SERVER['REQUEST_URI'], 'media-upload.php') && strpos($_SERVER['REQUEST_URI'], '&type=1pluginjquery') && !strpos($_SERVER['REQUEST_URI'], '&wrt='))
{
	header('Location: '._1pluginjquery_url().'/service.aspx?id='.get_site_option('1pluginjquery_userid').'&type=galleries&ver='._1pluginjquery_plugin_ver().'&rdt='.urlencode(_1pluginjquery_selfURL()));
	exit;
}

function _1pluginjquery_selfURL()
{
	$s = empty ( $_SERVER ["HTTPS"] ) ? '' : ($_SERVER ["HTTPS"] == "on") ? "s" : "";

	$protocol =  strtolower ( $_SERVER ["SERVER_PROTOCOL"] );
	$protocol =  substr($protocol, 0, strpos($protocol, "/"));
	$protocol .= $s;

	$port = ($_SERVER ["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER ["SERVER_PORT"]);
	$ret = $protocol . "://" . $_SERVER ['SERVER_NAME'] . $port . $_SERVER ['REQUEST_URI'];

	return $ret;
}

function _1pluginjquery_pluginURI()
{
	return get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
}

function _1pluginjquery_WpMedia_init() // constructor
{
	add_action('media_buttons', '_1pluginjquery_addMediaButton', 20);

	add_action('media_upload_1pluginjquery', '_1pluginjquery_media_upload');
	// No longer needed in WP 2.6
	if ( !function_exists('wp_enqueue_style') )
	{
		add_action('admin_head_media_upload_type_1pluginjquery', 'media_admin_css');
	}
      
	// check auth enabled
	//if(!function_exists('curl_init') && !ini_get('allow_url_fopen')) {}
}

function _1pluginjquery_addMediaButton($admin = true)
{
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

	$media_upload_iframe_src = get_option('siteurl').'/wp-admin/media-upload.php?post_id=$uploading_iframe_ID';

	$media_1pluginjquery_iframe_src = apply_filters('media_1pluginjquery_iframe_src', "$media_upload_iframe_src&amp;type=1pluginjquery&amp;tab=1pluginjquery");
	$media_1pluginjquery_title = __('Add 1pluginjquery photo', 'wp-media-1pluginjquery');

	echo "<a class=\"thickbox\" href=\"{$media_1pluginjquery_iframe_src}&amp;TB_iframe=true&amp;height=500&amp;width=640\" title=\"$media_1pluginjquery_title\"><img src=\""._1pluginjquery_pluginURI()."/1plugin-icon.gif\" alt=\"$media_1pluginjquery_title\" /></a>";
}

function _1pluginjquery_modifyMediaTab($tabs)
{
	return array(
		'1pluginjquery' =>  __('1pluginjquery photo', 'wp-media-1pluginjquery'),
	);
}

function _1pluginjquery_media_upload()
{
	wp_iframe('_1pluginjquery_media_upload_type');
}


function _1pluginjquery_media_upload_type()
{
	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
	add_filter('media_upload_tabs', '_1pluginjquery_modifyMediaTab');
?>

<br />
<br />
<h2>&nbsp;&nbsp;Please Wait...</h2>

<script>

	function _1pluginjquery_stub()
	{
		var i = location.href.indexOf("&wrt=");

		if (i > -1)
		{
			top.send_to_editor(unescape(location.href.substring(i+5)));
		}

		top.tb_remove();
	}

	window.onload = _1pluginjquery_stub;

</script>

<?php
}

_1pluginjquery_WpMedia_init();


// this new regex should resolve the problem of having unicode chars in the tag
define("PLUGINJQUERY_REGEXP", "/\[1pjq([^\]]*)\]/");


function _1pluginjquery_tag($fid)
{
	return _1pluginjquery_async_plugin_callback(array($fid));
}


function _1pluginjquery_async_plugin_callback($match)
{
	$uni = uniqid('');
	$ret = '
<!-- 1pluginjquery WordPress plugin '._1pluginjquery_plugin_ver().' (async engine): http://1pluginjquery.com/ -->

<div id="pj_widget_'.$uni.'"><img src="http://app.1pluginjquery.com/runtime/loading.gif" style="border:0;" alt="1pluginjquery WordPress plugin" /></div>

<script type="text/javascript">
/* PLEASE CHANGE DEFAULT EXCERPT HANDLING TO CLEAN OR FULL (go to your Wordpress Dashboard/Settings/1pluginjquery Options ... */

var zeo = [];
zeo["_object"] ="pj_widget_'.$uni.'";
zeo["_gid"] = "'.urlencode($match[0]).'";

var _zel = _zel || [];
_zel.push(zeo);

(function() {
	var cp = document.createElement("script"); cp.type = "text/javascript";
	cp.async = true; cp.src = "http://app.1pluginjquery.com/runtime/loader.js";
	var c = document.getElementsByTagName("script")[0];
	c.parentNode.insertBefore(cp, c);
})();

</script>

';

	return $ret;
}

function _1pluginjquery_feed_plugin_callback($match)
{
	$ret = '<img style="border:0;" src="http://app.1pluginjquery.com/runtime/thumb.aspx?fid='.urlencode($match[1]).'&size=large" />';

	return $ret;
}

function _1pluginjquery_plugin($content)
{
	$pluginjquery_excerpt_rt = get_site_option('1pluginjquery_excerpt_rt');
	if ($pluginjquery_excerpt_rt == 'remove' && (is_search() || is_category() || is_archive() || is_home()))
		return preg_replace(PLUGINJQUERY_REGEXP, '', $content);
	else if ( is_feed() )
		return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_1pluginjquery_feed_plugin_callback', $content));
	else
		return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_1pluginjquery_async_plugin_callback', $content));
}

function _1pluginjquery_plugin_rss($content)
{
	return (preg_replace_callback(PLUGINJQUERY_REGEXP, '_1pluginjquery_feed_plugin_callback', $content));
}

//add_shortcode('1pluginjquery', '1pluginjquery_plugin_shortcode');
add_filter('the_content', '_1pluginjquery_plugin');
add_filter('the_content_rss', '_1pluginjquery_plugin_rss');
add_filter('the_excerpt_rss', '_1pluginjquery_plugin_rss');
add_filter('comment_text', '_1pluginjquery_plugin'); 

add_action ( 'bp_get_activity_content_body', '_1pluginjquery_plugin' );
add_action ( 'bp_get_the_topic_post_content', '_1pluginjquery_plugin' );

add_action('wp_dashboard_setup', '_1pluginjquery_dashboard'); 

// Hook for adding admin menus
// http://codex.wordpress.org/Adding_Administration_Menus
add_action('admin_menu', '_1pluginjquery_mt_add_pages');

// register pluginjqueryWidget widget
add_action('widgets_init', create_function('', 'return register_widget("pluginjqueryWidget");'));


/////////////////////////////////
// dashboard widget
//////////////////////////////////
function _1pluginjquery_dashboard()
{
	if(function_exists('wp_add_dashboard_widget'))
		wp_add_dashboard_widget('1pluginjquery', '1pluginjquery', '_1pluginjquery_dashboard_content');
}

function _1pluginjquery_dashboard_content()
{

	echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&continue=http%3a%2f%2fapp.1pluginjquery.com%2fmanage%2fwordpress-dashboard.aspx&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='100%' height='370px' scrolling='no'></iframe>";

}



// action function for above hook
function _1pluginjquery_mt_add_pages() {

	// Add a new submenu under Options:
	// http://codex.wordpress.org/Roles_and_Capabilities
	
	add_options_page('1pluginjquery Options', '1pluginjquery Options', 'install_plugins', '1pluginjqueryoptions', '_1pluginjquery_mt_options_page');

	$pluginjquery_permission_level = get_site_option('1pluginjquery_permission_level');

	if(function_exists('add_menu_page'))
	{
		add_menu_page('1pluginjquery', '1pluginjquery', $pluginjquery_permission_level, __FILE__, '_1pluginjquery_mt_toplevel_page');

		// kill the first menu item that is usually the identical to the menu itself
		add_submenu_page(__FILE__, '', '', $pluginjquery_permission_level, __FILE__);

		add_submenu_page(__FILE__, 'Manage Galleries', 'Manage Galleries', $pluginjquery_permission_level, 'sub-page', '_1pluginjquery_mt_sublevel_monitor');
//		add_submenu_page(__FILE__, 'Media Library', 'Media Library', $pluginjquery_permission_level, 'sub-page1', '_1pluginjquery_mt_sublevel_library');
//		add_submenu_page(__FILE__, 'Create Gallery', 'Create Gallery', $pluginjquery_permission_level, 'sub-page2', '_1pluginjquery_mt_sublevel_create');
//		add_submenu_page(__FILE__, 'My Account', 'My Account', $pluginjquery_permission_level, 'sub-page3', '_1pluginjquery_mt_sublevel_myaccount');
//		add_submenu_page(__FILE__, 'Support Forum', 'Support Forum', $pluginjquery_permission_level, 'sub-page4', '_1pluginjquery_mt_sublevel_forum');
	}
}

function _1pluginjquery_isAdmin()
{
	return !function_exists('is_site_admin') || is_site_admin() == true;
}

function _1pluginjquery_mt_options_page() {

//	if( is_site_admin() == false ) {
//		wp_die( __('You do not have permission to access this page.') );
//	}

	if (strpos($_SERVER['QUERY_STRING'], 'hide_note=welcome_notice'))
	{
		update_site_option('1pluginjquery_welcome_notice', _1pluginjquery_plugin_ver());
		echo "<script type=\"text/javascript\">	document.location.href = '".$_SERVER['HTTP_REFERER']."'; </script>";
		exit;
	}

	if (strpos($_SERVER['QUERY_STRING'], 'hide_note=premiumpress_notice'))
	{
		update_site_option('premiumpress_notice', _1pluginjquery_plugin_ver());
	}

	$pluginjquery_userid = get_site_option('1pluginjquery_userid');
	$pluginjquery_permission_level = get_site_option('1pluginjquery_permission_level');
	$pluginjquery_excerpt = get_site_option('1pluginjquery_excerpt');

	if ( isset($_POST['submit']) )
	{
		if (_1pluginjquery_isAdmin())
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
		
		echo "<div id=\"updatemessage\" class=\"updated fade\"><p>1pluginjquery settings updated.</p></div>\n";
		echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";	
	}

	$disp_excerpt2 = $pluginjquery_excerpt == 'clean' ? 'checked="checked"' : '';
	$disp_excerpt3 = $pluginjquery_excerpt == 'full' ? 'checked="checked"' : '';
	$disp_excerpt4 = $pluginjquery_excerpt == 'remove' ? 'checked="checked"' : '';
	$disp_excerpt1 = $pluginjquery_excerpt == '' || $pluginjquery_excerpt == 'nothing' ? 'checked="checked"' : '';


?>
	<div class="wrap">
		<h2>1pluginjquery Configuration</h2>
		<div class="postbox-container">
			<div class="metabox-holder">
				<div class="meta-box-sortables">
					<form action="" method="post" id="1pluginjquery-conf">
						<div id="1pluginjquery_settings" class="postbox">
							<div class="handlediv" title="Click to toggle">
								<br />
							</div>
							<h3 class="hndle">
								<span>1pluginjquery Settings</span>
							</h3>
							<div class="inside" style="width:600px;">
								<table class="form-table">




									<?php

if (_1pluginjquery_isAdmin())
{
?>


									<tr style="width:100%;">
										<th valign="top" scrope="row">
											<label for="1pluginjquery_userid">
												1pluginjquery Userid (<a target="_blank" href="http://1pluginjquery.com/">what?</a>):
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
												Excerpt Handling (<a target="_blank" href="http://1pluginjquery.com/">what?</a>):
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
<li>Use this PHP code to add a gallery directly to your template : <br>&nbsp;&nbsp;&nbsp; <i>&lt;?php echo _1pluginjquery_tag("GALLERY ID"); ?&gt;</i></li>
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
// _1pluginjquery_mt_manage_page() displays the page content for the Test Manage submenu
function _1pluginjquery_mt_manage_page() {
    echo "<h2>Test Manage</h2>";
}
*/

function _1pluginjquery_mt_toplevel_page() {

    echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='98%' height='2000px'></iframe>";
}

function _1pluginjquery_mt_sublevel_create() {
    echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='98%' height='2000px'></iframe>";
}

function _1pluginjquery_mt_sublevel_monitor() {
    echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='98%' height='2000px'></iframe>";
}

function _1pluginjquery_mt_sublevel_library() {
    echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='98%' height='2000px'></iframe>";
}

function _1pluginjquery_mt_sublevel_myaccount() {
    echo "<iframe src='http://app.1pluginjquery.com/service.aspx?id=".get_site_option('1pluginjquery_userid')."&type=galleries&ver="._1pluginjquery_plugin_ver()."&src=".urlencode(_1pluginjquery_selfURL())."' width='98%' height='2000px'></iframe>";
}

function _1pluginjquery_mt_sublevel_forum() {
//    echo "<iframe src='http://app.1pluginjquery.com/redirect_to_help.aspx' width='98%' height='2000px'></iframe>";
}



if (!class_exists('pluginjqueryWidget')) {

	/**
	 * pluginjqueryWidget Class
	 */
	class pluginjqueryWidget extends WP_Widget {
			/** constructor */
			function pluginjqueryWidget() {
					parent::WP_Widget(false, $name = '1pluginjquery Gallery Widget');	
			}

			/** @see WP_Widget::widget */
			function widget($args, $instance) {		
					extract( $args );

					if (strpos($instance['galleryid'], '1pluginjquery'))
						$gallery = _1pluginjquery_plugin($instance['galleryid']);
					else
						$gallery = _1pluginjquery_plugin('[1pjq '.$instance['galleryid'].']');

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
			<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Gallery ID:'); ?> <a target="_blank" href="http://1pluginjquery.com/">what?</a> <input class="widefat" id=""<?php echo $this->get_field_id('galleryid'); ?>" name="<?php echo $this->get_field_name('galleryid'); ?>" type="text" value="<?php echo $galleryid; ?>" />
		</label>
	</p>
	<?php 
			}

	} // class pluginjqueryWidget

}

// http://www.aaronrussell.co.uk/blog/improving-wordpress-the_excerpt/

function _1pluginjquery_improved_trim_excerpt($text)
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
	add_filter('get_the_excerpt', '_1pluginjquery_improved_trim_excerpt');
}

function _1pluginjquery_activation_notice()
			{ ?>
			<div id="message" class="updated fade">
				<p style="line-height: 150%">
					<strong>Welcome to 1pluginjquery Rich Media Plugin</strong> - The best way to manage and display photo galleries and slideshows on your site.
				</p>
				<p>
					On every post page (above the text box) you'll find this  <img src="<?php echo _1pluginjquery_pluginURI() ?>/1plugin-icon.gif"  />  icon, click on it to start or use sidebar Widgets (Appearance menu).
				</p>
				<p>
		
<input type="button" class="button" value="1pluginjquery Options Page" onclick="document.location.href = 'options-general.php?page=1pluginjqueryoptions';" />

<input type="button" class="button" value="Hide this message" onclick="document.location.href = 'options-general.php?page=1pluginjqueryoptions&amp;hide_note=welcome_notice';" />


				</p>

			</div>


			<?php

	if (get_site_option('1pluginjquery_installed') != 'true')
	{
		update_site_option('1pluginjquery_installed', 'true');
		echo "<img src='http://goo.gl/hXevd' width=0 height=0 />";
	}
}



if (get_site_option('1pluginjquery_welcome_notice') != _1pluginjquery_plugin_ver())
	add_action( 'admin_notices', '_1pluginjquery_activation_notice' );


if (get_site_option('1pluginjquery_userid') == "")
{
	$uni = uniqid('');
	update_site_option('1pluginjquery_userid', $uni);
}

if (get_site_option('1pluginjquery_permission_level') == "")
{
	update_site_option('1pluginjquery_permission_level', 'edit_posts');
}


?>