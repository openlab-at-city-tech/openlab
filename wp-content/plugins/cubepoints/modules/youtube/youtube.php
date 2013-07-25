<?php

/** YouTube Module */

cp_module_register(__('YouTube', 'cp') , 'youtube' , '1.0', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Let users earn points for watching YouTube videos.', 'cp'), 1);

function cp_module_youtube_points_install(){
	add_option('cp_module_youtube_points', 10);
}
add_action('cp_module_youtube_activate','cp_module_youtube_points_install');

if(cp_module_activated('youtube')){

	function cp_module_youtube_config(){
	?>
		<br />
		<h3><?php _e('YouTube','cp'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="cp_module_youtube_points"><?php _e('Default number of points for watching a YouTube video', 'cp'); ?>:</label></th>
				<td valign="middle"><input type="text" id="cp_module_youtube_points" name="cp_module_youtube_points" value="<?php echo get_option('cp_module_youtube_points'); ?>" size="30" /></td>
			</tr>
		</table>
	<?php
	}
	add_action('cp_config_form','cp_module_youtube_config');
	
	function cp_module_youtube_config_process(){
		$cp_module_youtube_points = ((int)$_POST['cp_module_youtube_points']<0)?0:(int)$_POST['cp_module_youtube_points'];
		update_option('cp_module_youtube_points', $cp_module_youtube_points);
	}
	add_action('cp_config_process','cp_module_youtube_config_process');
		
	function cp_module_youtube_shortcode($atts){
		
		// return if no video id defined
		if($atts['id']==''){
			return;
		}
		// get points from shortcode or use default
		if(is_numeric($atts['points'])&&(int)$atts['points']>=0){
			$points = (int) $atts['points'];
		}
		else{
			$points = get_option('cp_module_youtube_points');
		}
		
		// process any ajax request
		$_POST['uuid'] = str_replace('__','-',$_POST['uuid']);
		if($_POST['action']=='cp_youtube'&&$_POST['uuid']==$atts['id']){
			global $wpdb;
			$data = $atts['id'];
			$uid = cp_currentUser();
			if( (int) $wpdb->get_var("SELECT COUNT(*) FROM ".CP_DB." WHERE `uid`=$uid AND `data`='$data' AND `type`='youtube'") == 0 ){
				cp_points('youtube', cp_currentUser(), $points, $atts['id']);
			}
			exit();
		}
		
		// get height and width from shortcode or use default
		if(is_numeric($atts['height'])){
			$height = (int) $atts['height'];
		}
		else{
			$height = "315";
		}
		if(is_numeric($atts['width'])){
			$width = (int) $atts['width'];
		}
		else{
			$width = "560";
		}
		
		$uuid = str_replace('-','__',$atts['id']);
		
		$video = '<script type="text/javascript">
					var params = { allowScriptAccess: "always", wmode: "transparent" };
					swfobject.embedSWF("' . htmlentities('http://www.youtube.com/e/'.$atts['id'].'?enablejsapi=1&version=3&playerapiid='.$uuid.'&rel=0&controls=0&showinfo=0') . '", "'.$uuid.'", "'.$width.'", "'.$height.'", "9.0.0", null, null, params);
				</script>
				<div id="'.$uuid.'_container" class="cp_youtube">
					<div id="'.$uuid.'"></div>
				</div>';
				
		$video .= '<script type="text/javascript">
						function cp_youtube_'.$uuid.'_fn(state) {
							cp_youtube_updateState("'.$uuid.'", state);
						}
					</script>';

	return $video;
		
	}
	
	add_shortcode('cp_youtube','cp_module_youtube_shortcode');
	
	function cp_module_youtube_scripts(){
		wp_enqueue_script("swfobject");
		wp_register_script('cp_youtube_common',	WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'cp_youtube.js', array('jquery'));
		wp_enqueue_script('cp_youtube_common');
		wp_localize_script( 'cp_youtube_common', 'cp_youtube', array(
			'ajax_url' =>  (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
		) );
	}
	add_action('init', 'cp_module_youtube_scripts');

	/** YouTube Log Hook */
	add_action('cp_logs_description','cp_admin_logs_desc_youtube', 10, 4);
	function cp_admin_logs_desc_youtube($type,$uid,$points,$data){
		if($type!='youtube') { return; }
		echo 'Watched <a href="http://www.youtube.com/watch?v='.$data.'">YouTube Video</a>';
	}
	
}
?>