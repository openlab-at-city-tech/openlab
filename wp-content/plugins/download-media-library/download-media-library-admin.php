<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

//Add subpage
add_action('admin_menu', 'mtdml_menu');
function mtdml_menu() {
    add_submenu_page('upload.php',__( 'Download Media Library', 'mtdml' ),	__( 'Download Media Library', 'mtdml' ), 'manage_options', 'mtdml', 'mtdml_setting');
}

//Get slug by id. Used for folder post name
/*function mtdml_the_slug($id) {
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug; 
}*/

//ZIP
function mtdml_zip($files){
	
	// create new zip opbject
	$zip = new ZipArchive();

	// create a temp file & open it
	$tmp_file = tempnam('.','');
	$zip->open($tmp_file, ZipArchive::CREATE);
	
	// loop through each file
	foreach($files as $file_id => $file){

		// Get file parent id
		$parent_id = get_post($file_id)->post_parent;
		
		// get file parent post type
		$posttype_folder_name = get_post_type($parent_id);
		$obj_post_type = get_post_type_object( $posttype_folder_name );
		$posttype_folder_name = $obj_post_type->labels->name.'/';
		// get file parent slug
		$post_folder_name = ($parent_id != 0) ? get_the_title($parent_id).'/' : '';
		// get file mime type
		$mimetype_folder_name = get_post_mime_type($file_id).'/';
	
		// download file
		$download_file = file_get_contents($file);

		//add it to the zip
		//$zip->addFromString(iconv("UTF-8","CP852", $posttype_folder_name.$post_folder_name.$mimetype_folder_name.basename($file)),$download_file);
		$zip->addFromString($posttype_folder_name.$post_folder_name.$mimetype_folder_name.basename($file),$download_file);

	}

	// close zip
	$zip->close();

	// send the file to the browser as a download
	$zip_name = 'dml'.sanitize_title(get_bloginfo('name')).date('Ymdhi');
	header('Content-disposition: attachment; filename='.$zip_name.'.zip');
	header('Content-type: application/zip');
	readfile($tmp_file);
}

// if button download, execute function for zip
if(isset($_POST['mtdml_download'])){
	// Get the attachments
	$selected_mime_types = isset( $_POST['mime_type'] ) ? $_POST['mime_type'] : '';

	$attachments = get_posts(array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any', 'post_mime_type' => $selected_mime_types, 'post_parent' => null ));
	if ( $attachments ) {
		
		// Get post types selected by user
		$selected_post_types = isset( $_POST['post_type'] ) ? $_POST['post_type'] : '';
		
		//Insert in array 'files', the files for zip
		foreach ( $attachments as $post ) {
			if(!empty($selected_post_types)){
				if(in_array(get_post_type($post->post_parent), $selected_post_types)){
					$files[$post->ID] = get_attached_file( $post->ID );
				}
			}else{
				$files[$post->ID] = get_attached_file( $post->ID );
			}
		}
		wp_reset_postdata();
		
	}
	if(empty($files)){
		header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&error=no-files");
		exit;
	}else{
		mtdml_zip($files);
	}
}

//function for setting
function mtdml_setting(){
	$curr_page = (isset($_GET['page'])) ? $_GET['page'] : '';
	global $wp_version;
	//Check WordPress Version
	if ( $wp_version < 2.6 && $curr_page == 'mtdml') {
		echo '<div class="error"><p><strong>';
		echo __( 'This plugin not is supported in current WordPress version. <a href="./update-core.php">Please update the WordPress for version 3.6 or above.</a>', 'MTDML' );
		echo '</strong></p></div><style type="text/css">p.submit{display:none}</style>';
	}else{
	global $wpdb;
	
	$error = (isset($_GET['error'])) ? $_GET['error'] : '';
	
	$post_types = get_post_types(array('public' => true));
	$mime_types = $wpdb->get_results( "SELECT post_mime_type FROM $wpdb->posts WHERE post_type = 'attachment' GROUP BY post_mime_type", ARRAY_N );

	$html = '<div id="wrap">';
	$html .= '<h3>'. __('Download Media Library', 'mtdml').'</h3>';
	if($error == 'no-files'){
		$html .= '<div class="error"><p><strong>'.__( 'No files found for this criteria.', 'mtdml' ).'</strong></p></div>';
	}
	$html .= '<form id="mtdml_form" action="'.$_SERVER['REQUEST_URI'].'" method="post">';
	$html .= '<p>
				<strong>'. __('Choose the post types:', 'mtdml').'</strong><br />';
				// show post types in checkboxes
				foreach($post_types as $post_type){
					$obj_post_type = get_post_type_object( $post_type );
					$html .= '<label><input checked type="checkbox" id="post_type[]" name="post_type[]" value="'.$post_type.'"/>'.$obj_post_type->labels->singular_name.'</label><br />';
				}
	$html .= '</p>';
	$html .= '<p>
				<strong>'. __('...and/or choose the mime types:', 'mtdml').'</strong><br />';
				// show post types in checkboxes
				foreach($mime_types as $mime_type){
					$html .= '<label><input type="checkbox" id="mime_type[]" name="mime_type[]" value="'.$mime_type[0].'"/>'.$mime_type[0].'</label><br />';
				}
	$html .= '</p>';
	$html .= '<p>
				<input type="submit" class="button button-primary button-large" name="mtdml_download" id="mtdml_download" value="'. __('Download .zip', 'mtdml').'" />
				<img style="display:none" class="loading" src="'.get_admin_url().'/images/spinner.gif" alt="" />
			</p>
			<div class="notice-info notice">
			<p>
			'.__( 'Please consider making a donation you prefer, so still animated to develop more plugins and maintain support to existing plugins ;)', 'mtdml' ).'
			<br /><br />
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G85Z9XFXWWHCY" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" alt="PayPal - The safer, easier way to pay online!" border="0"></a>
			</p>
			</div>
			</form>';
	// Notice success
	$html .= '</div>';
	echo $html;
	}
}