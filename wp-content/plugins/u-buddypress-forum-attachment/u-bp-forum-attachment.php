<?php
/*
Plugin Name: U BuddyPress Forum Attachment
Plugin URI: http://urlless.com/u-buddypress-forum-attachment/
Description: This plugin allows members to upload files on BuddyPress forum. Uploader is Ajax-based.
Author: Taehan Lee
Author URI: http://urlless.com
Version: 1.2.1
Network: true
*/

class UBPForumAttachment {
	
var $id = 'ubpfattach';
var $ver = '1.2.1';
var $url, $path, $thumbnail_size, $bb_prefix, $meta_table;

function UBPForumAttachment(){
	$this->url = plugin_dir_url(__FILE__);
	$this->path = plugin_dir_path(__FILE__);
	$this->thumbnail_size = array(100, 100);
	
	register_activation_hook( __FILE__, array(&$this, 'activation') );
	
	load_plugin_textdomain($this->id, false, dirname(plugin_basename(__FILE__)).'/languages/');
	
	add_action( 'bb_init', array(&$this, 'set_db') );
	add_action( 'admin_init', array(&$this, 'set_db') );
	
	add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', array(&$this, 'admin_menu') );
	add_action( 'admin_init', array(&$this, 'admin_init') );
	add_action( 'admin_action_'.$this->id.'_delete_file', array(&$this, 'delete_file_n_update_meta'));
	add_action( 'wp_ajax_'.$this->id.'_ajax', array(&$this, 'ajax') );
	
	add_action( 'bb_init', array(&$this, 'bb_init') );
	add_action( 'init', array(&$this, 'request') );
}

function set_db(){
	global $bp, $bbdb;
	if( is_admin() ){
		if( !empty($bp->forums->bbconfig) AND file_exists($bp->forums->bbconfig) ){
			include $bp->forums->bbconfig;
			$this->bb_prefix = $bb_table_prefix;
			$this->meta_table = $bb_table_prefix.'meta';
		}
		
	} else {
		$this->bb_prefix = $bbdb->prefix;
		$this->meta_table = $bbdb->prefix.'meta';
	}
}

function is_enable(){
	
	$opts = get_option($this->id);
	if( empty($opts['enable']) || empty($opts['upload_dir']) ) 
		return false;
	
	return true;
}

function request(){
	if( ! $this->is_enable() ) return;
	
	if( isset($_POST[$this->id.'_upload']) AND ($_POST[$this->id.'_upload']==='true') ){
		$this->_do_upload();
		exit;
	}
	
	if( isset($_GET[$this->id.'_download']) AND ($_GET[$this->id.'_download']==='true') ){
		$this->_do_download();
		exit;
	}
}

function bb_init(){
	if( ! $this->is_enable() ) return;
	
	$opts = get_option($this->id);
	
	// meta
	add_action( 'groups_new_forum_topic', array(&$this, 'new_topic_meta'), 1, 2);
	add_action( 'groups_new_forum_topic_post', array(&$this, 'new_topic_post_meta'), 1, 2);
	add_action( 'groups_edit_forum_topic', array(&$this, 'edit_topic_meta'), 1);
	add_action( 'groups_edit_forum_post', array(&$this, 'edit_post_meta'), 1);
	
	// display uploader - topic new
	add_action( 'bp_after_group_forum_post_new', array(&$this, 'create_uploader') );
	// display uploader - reply new
	add_action( 'groups_forum_new_reply_after', array(&$this, 'create_uploader') );
	// display uploader - global forum
	add_action( 'groups_forum_new_topic_after', array(&$this, 'create_uploader') );
	
	// display uploader - topic edit
	add_action( 'bp_group_after_edit_forum_topic', array(&$this, 'create_uploader_edit') );
	// display uploader - reply edit
	add_action( 'bp_group_after_edit_forum_post', array(&$this, 'create_uploader_edit') );
	
	// hidden form for file upload
	add_action('wp_footer', array(&$this, 'create_upload_form'));
	
	// filebox on post-entry
	add_action( 'bp_get_the_topic_post_content', array(&$this, 'create_filebox'));
	
	wp_enqueue_style( $this->id.'-style', $this->url.'inc/style.css', '', $this->ver);
	wp_enqueue_script( $this->id.'-script', $this->url.'inc/script.js', array('jquery'), $this->ver);
	wp_localize_script( $this->id.'-script', $this->id.'_vars', array(
		'ajaxurl' 			=> admin_url( 'admin-ajax.php' ), 
		'nonce' 			=> wp_create_nonce( $this->id.'_nonce' ),
		'plugin_id' 		=> $this->id,
		'max_num' 			=> $opts['max_num'],
		'insert_link' 		=> __('Insert into editor', $this->id),
		'delete_link' 		=> __('Delete', $this->id),
		'delete_success' 	=> __('Attachment deleted', $this->id),
		'delete_confirm'	=> __('Are you sure you want to delete?', $this->id),
		'processing' 		=> __('Processing', $this->id),
	));
}


function create_upload_form(){
	?>
<form action="" method="post" enctype="multipart/form-data" target="<?php echo $this->id?>_target" id="<?php echo $this->id?>-form">
	<input type="hidden" name="<?php echo $this->id?>_upload" value="true">
	<?php wp_nonce_field($this->id.'_nonce', $this->id.'_nonce')?>
</form>
<iframe id="<?php echo $this->id?>-target" name="<?php echo $this->id?>_target" frameborder="0" src="about:blank"></iframe>
	<?php
}


function create_uploader( $meta_id='' ){
	global $is_iphone;
	if( $is_iphone ) return false;
		
	$opts = (object) get_option($this->id);
	?>
<div id="<?php echo $this->id?>">
	<span id="<?php echo $this->id?>-button"><span><?php _e('Attach a file', $this->id)?></span></span>
	<span id="<?php echo $this->id?>-process"><strong><?php echo _e('Uploading', $this->id)?> &#8230;</strong> <img src="<?php echo $this->url?>inc/loader.gif"></span>
	<span id="<?php echo $this->id?>-info">
		<?php _e('File types', $this->id)?>: <strong><?php echo $opts->allowed_file_type?></strong>,
		<?php _e('Max size', $this->id)?>: <strong><?php echo $opts->max_size;?>Mbytes</strong>,
		<?php _e('Max count', $this->id)?>: <strong><?php echo $opts->max_num;?></strong>
	</span>
	<div id="<?php echo $this->id?>-message"></div>
	<table id="<?php echo $this->id?>-list" class="<?php echo $this->id?>-filelist"></table>
</div>

<script>jQuery(function(){ window.<?php echo $this->id?> = new U_BP_Forum_Attachment('<?php echo $meta_id?>'); });</script>
	<?php
}


function create_uploader_edit() {
	global $wpdb, $bp, $forum_template;
	
	if( bp_is_edit_topic() ){
		$object_id = $forum_template->topic->topic_id;
		$object_type = 'bb_topic';
	}else{
		$post = bp_forums_get_post( $bp->action_variables[4] );
		$object_id = $post->post_id;
		$object_type = 'bb_post';
	}
	$sql = $wpdb->prepare("SELECT meta_id FROM {$this->meta_table} WHERE meta_key=%s AND object_type=%s AND object_id=%d", $this->id.'_attachments', $object_type, $object_id);
	$meta_id = $wpdb->get_var( $sql );
	$this->create_uploader($meta_id);
}


function create_filebox($content){
	global $topic_template;
	if( $topic_template->current_post==0 ){
		$object_id = $topic_template->topic_id;
		$rows = bb_get_topicmeta( $object_id, $this->id.'_attachments', true);
	}else{
		$object_id = $topic_template->post->post_id;
		$rows = bb_get_postmeta( $object_id, $this->id.'_attachments', true);
	}
	$rows = json_decode($rows);
	$upload_dir_path = $this->get_upload_dir_path();
	$upload_dir_url = $this->get_upload_dir_url();
	
	$ret = '<div class="clear"></div>';
	if( !empty($rows) ){
		$i=0;
		$ret .= '<table class="'.$this->id.'-attachments '.$this->id.'-filelist">';
		foreach($rows as $row){
			$x = explode('.', $row->filename);
			$ext = end($x);
			$is_image = ( $ext=='jpg' || $ext=='jpeg' || $ext=='gif' || $ext=='png' ) ? true : false;
			
			$download_url = add_query_arg(array(
				$this->id.'_download' => 'true',
				'_wpnonce' => wp_create_nonce($this->id.'_nonce'),
				'filename' => urlencode($row->filename),
			), '');
			
			$thumbnail = $row->url;
			if( !empty($row->thumbnail_filename) AND file_exists($upload_dir_path.$row->thumbnail_filename) ){
				$thumbnail = $upload_dir_url.$row->thumbnail_filename;
			}
			
			$even = ($i++%2==0) ? 'even' : '';
			
			$ret .= '<tr class="'.$even.'">';
			if( $is_image ) {
				$ret .= '<td class="thumb"><img src="'.$thumbnail.'" class="thumb">'.$t.'</td>';
			}else{
				$ret .= '<td class="thumb empty"></td>';
			}
			$ret .= '<td class="filename">'.$row->filename.'</td>';
			$ret .= '<td class="links"><a href="'.$download_url.'">'.__('Download', $this->id).'</a>';
			if( $is_image ) {
				$ret .= ' <span class="pipe"> | </span> ';
				$ret .= '<a href="'.$row->url.'" target="_blank" title="'.__('Open Image in New Window', $this->id).'">'.__('View', $this->id).'</a>';
			}
			$ret .= '</td></tr>';
		}
		$ret .= '</table>';
	}
	return $content.$ret;
}

function attachment_validate(){
	$attachments = isset($_POST[$this->id.'-attachments']) ? $_POST[$this->id.'-attachments'] : '';
	if( empty($attachments) || !is_array($attachments) )
		return false;
		
	$clean = array();
	foreach($attachments as $k=>$v)
		$clean[$k] = $v;
	
	return json_encode($clean);
}
	
function new_topic_meta($group_id, $topic) {
	if( $attachment = $this->attachment_validate() )
		bb_update_topicmeta( $topic->topic_id, $this->id.'_attachments', $attachment );
}
function new_topic_post_meta($group_id, $post_id) {
	if( $attachment = $this->attachment_validate() )
		bb_update_postmeta( $post_id, $this->id.'_attachments', $attachment );
}
function edit_topic_meta($topic_id){
	if( $attachment = $this->attachment_validate() )
		bb_update_topicmeta( $topic_id, $this->id.'_attachments', $attachment );
}
function edit_post_meta($post_id){
	if( $attachment = $this->attachment_validate() )
		bb_update_postmeta( $post_id, $this->id.'_attachments', $attachment );
}



function get_all_mime_types(){
	include $this->path.'inc/mimes.php';
	return $mimes;
}

function get_upload_dir_path(){
	$opts = get_option($this->id);
	$wp_upload_dir = wp_upload_dir();
	return $wp_upload_dir['basedir'].'/'.$opts['upload_dir'].'/';
}

function get_upload_dir_url(){
	$opts = get_option($this->id);
	$wp_upload_dir = wp_upload_dir();
	return $wp_upload_dir['baseurl'].'/'.$opts['upload_dir'].'/';
}



function _upload_dir($a){
	$opts = get_option($this->id);
	$subdir = '/'.$opts['upload_dir'];
	$a['path'] = str_replace($a['subdir'], $subdir, $a['path']);
	$a['url'] = str_replace($a['subdir'], $subdir, $a['url']);
	$a['subdir'] = $subdir;
	return $a;
}

function _sanitize_file_name($filename){
	$info = pathinfo($filename);
	$ext = $info['extension'];
	$filename = str_replace('.'.$ext, '', $filename);
	$filename = strtolower($filename);
	$filename = preg_replace('|[^a-z0-9_-]|', '', $filename);
	if( preg_replace('|[^a-z0-9]|', '', $filename)=='' )
		$filename = time();
	$filename = $filename.'.'.$ext;
	return $filename;
}

function _upload_mimes($_mimes=''){
	$mimes = $this->get_all_mime_types();
	$opts = get_option($this->id);
	$exts = explode(',', preg_replace('/,\s*/', ',', $opts['allowed_file_type']));
	$allowed_mimes = array();
	foreach ( $exts as $ext ) {
		foreach ( $mimes as $ext_pattern => $mime ) {
			if ( $ext != '' && strpos( $ext_pattern, $ext ) !== false )
				$allowed_mimes[$ext_pattern] = $mime;
		}
	}
	return $allowed_mimes;
}

function _check_filetype( $filename ) {
	$mimes = $this->_upload_mimes();
	$type = false;
	$ext = false;
	foreach ( $mimes as $ext_preg => $mime_match ) {
		$ext_preg = '!\.(' . $ext_preg . ')$!i';
		if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
			$type = $mime_match;
			$ext = $ext_matches[1];
			break;
		}
	}
	return compact( 'ext', 'type' );
}

function _return_upload_error($error){
	$error = esc_js($error);
	echo "<script>top.{$this->id}.upload_error('{$error}');</script>";
	exit;
}

function _do_upload(){
	$opts = get_option($this->id);
	
	if ( !wp_verify_nonce($_POST[$this->id.'_nonce'], $this->id.'_nonce') )
		$this->_return_upload_error( __('Your nonce did not verify.', $this->id) );
	
	if( empty($_FILES['file']['size']) )
		$this->_return_upload_error( __('Please select a file to upload', $this->id) );
		
	if( $_FILES['file']['size'] > ($opts['max_size'] * 1024 * 1024) )
		$this->_return_upload_error( sprintf(__('For uploading, file size must be less than %s Mbytes', $this->id), $opts['max_size']) );
	
	add_filter( 'upload_dir', array(&$this, '_upload_dir') ); 
	add_filter( 'sanitize_file_name', array(&$this, '_sanitize_file_name') ); 
	add_filter( 'upload_mimes', array(&$this, '_upload_mimes') );
	
	$upload = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($_FILES['file']['tmp_name']));
	if( isset($upload['error']) AND !empty($upload['error']) )
		$this->_return_upload_error($upload['error']);
	
	$url = esc_js($upload['url']);
	$filename = basename($url);
	$message = esc_js(sprintf(__('[%s] is successfully uploaded.', $this->id), $filename));
	
	$thumbnail = $this->_create_thumbnail($upload);
	if( !empty($thumbnail['error']) || empty($thumbnail['url'])){
		$thumbnail_url = '';
		$thumbnail_filename = '';
	}else{
		$thumbnail_url = esc_js($thumbnail['url']);
		$thumbnail_filename = basename($thumbnail_url);
	}
	?>
	<script>
	var ret = {
		url: '<?php echo $url?>',
		filename: '<?php echo $filename?>',
		thumbnail_url: '<?php echo $thumbnail_url?>',
		thumbnail_filename: '<?php echo $thumbnail_filename?>',
		message: '<?php echo $message?>'
	}
	top.<?php echo $this->id?>.upload_complete(ret);
	</script>
	<?php
	exit;
}

function _create_thumbnail($upload){
	@ini_set('memory_limit', '256M');
	$return = array();
	$filepath = $upload['file'];
	$path_parts = pathinfo( $filepath );
	$baseurl = str_replace($path_parts['basename'], '', $upload['url']);
	$imagesize = getimagesize($filepath);
	$mime_type = $imagesize['mime'];
	
	switch ( $mime_type ) {
		case 'image/jpeg':
			$img = imagecreatefromjpeg($filepath);
			break;
		case 'image/png':
			$img = imagecreatefrompng($filepath);
			break;
		case 'image/gif':
			$img = imagecreatefromgif($filepath);
			break;
		default:
			$img = false;
			break;
	}
	
	if ( is_resource($img) && function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	} else {
		$return['error'] = __('Unable to create sub-size images.');
		return $return;
	}
	
	$resized = image_make_intermediate_size($filepath, $this->thumbnail_size[0], $this->thumbnail_size[1], true);
	if( empty($resized) ){
		$return['url'] = '';
	}else{
		$return['url'] = $baseurl.$resized['file'];
	}
	
	imagedestroy($img);
	return $return;
}

function _do_download(){
	if ( !wp_verify_nonce($_GET['_wpnonce'], $this->id.'_nonce') )
		wp_die(__('Your nonce did not verify.', $this->id)); 
	
	$filename = basename($_GET['filename']);
	$filepath = $this->get_upload_dir_path().$filename;
	
	if( empty($filename) || !file_exists($filepath) ) {
		wp_die(__('File does not exist', $this->id));
	}else{
		$this->_force_download($filename, file_get_contents($filepath));
	}
}

function _force_download($filename = '', $data = ''){
	if ($filename == '' OR $data == '')	
		return false;
	
	if (FALSE === strpos($filename, '.')) 
		return false;
	
	$rs = $this->_check_filetype($filename);
	$mime_type = $rs['type'];
	
	if( empty($mime_type) ){
		wp_die(__('Invalid file type'));
	
	}else{
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE){
			header('Content-Type: "'.$mime_type.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}else{
			header('Content-Type: "'.$mime_type.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}
		exit($data);
	}
}


function delete_file_n_update_meta(){
	if( defined('DOING_AJAX') ){
		check_ajax_referer( $this->id.'_nonce' );
	}else{
		check_admin_referer($this->id.'_nonce'); 
	}
	
	$meta_ids = $_REQUEST['meta_id'];
	if( is_string($meta_ids) )
		$meta_ids = array($meta_ids);
	
	foreach( $meta_ids as $meta_id ){
		$tmp = explode('|', $meta_id);
		if( count($tmp)!= 2 )
			continue;
		
		$meta_id = absint($tmp[0]);
		$file_index = stripcslashes($tmp[1]);
		
		$files = $this->get_meta( $meta_id );
		$file = isset($files[$file_index]) ? $files[$file_index] : '';
		unset($files[$file_index]);
		
		if( $file ){
			if( isset($file->url) )
				$this->unlink_file($file->url);
			
			if( isset($file->thumbnail_url) )
				$this->unlink_file($file->thumbnail_url);
		}
		
		$this->update_meta($meta_id, $files);
	}
	
	if( !defined('DOING_AJAX') ){
		$goback = wp_get_referer();
		wp_redirect( $goback );
	}
	exit;
}

function get_meta($meta_id){
	global $wpdb;
	if( empty($meta_id) )
		return false;
	$r = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$this->meta_table} WHERE meta_id=%d LIMIT 1", $meta_id));
	return (array) json_decode($r);
}

function get_meta_total(){
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$this->meta_table} WHERE meta_key=%s", $this->id.'_attachments'));
}

function update_meta($meta_id, $files){
	global $wpdb;
	
	if( empty($meta_id) )
		return false;
	
	$files = !empty($files) ? (array) $files : array();
	
	$clean = array();
	foreach($files as $k=>$v) 
		$clean[$k] = $v;
	$clean = json_encode( $clean );
	$wpdb->update($this->meta_table, array('meta_value'=>$clean), array('meta_id'=>$meta_id));
}

function get_unattached_files(){
	global $wpdb;
	
	$unattached = array();
	$attached = array();
	
	$upload_dir_path = $this->get_upload_dir_path();
	$upload_dir_url = $this->get_upload_dir_url();
	
	$handler = opendir($upload_dir_path);
	while($file = readdir($handler)){
		if($file != '.' AND $file != '..'){
			$unattached[] = $file;
		}
	}
	closedir($handler);
	
	if( empty($unattached) )
		return null;
	
	$metas = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM {$this->meta_table} WHERE meta_key=%s", $this->id.'_attachments'));
	foreach($metas as $meta){
		if( empty($meta->meta_value) ) continue;
		$files = json_decode($meta->meta_value);
		foreach( $files as $file ){
			if( !empty($file->filename) )
				$attached[] = $file->filename;
			if( !empty($file->thumbnail_filename) )
				$attached[] = $file->thumbnail_filename;
		}
	}
	
	$rs = array();
	foreach( $unattached as $filename ){
		if( !in_array($filename, $attached) )
			$rs[] = $filename;
	}
	
	return $rs;
}

function unlink_file($filename){
	if( empty($filename) )
		return false;
	$upload_dir_path = $this->get_upload_dir_path();
	$filename = basename($filename);
	$filepath = $upload_dir_path.$filename;
	if( !empty($filename) AND file_exists($filepath) )
		@unlink($filepath);
}

function ajax(){
	if( !defined('DOING_AJAX') ) die('-1');
	check_ajax_referer( $this->id.'_nonce' );
	
	switch( $_REQUEST['action_scope'] ){
		case 'get_meta':
			$r =$this->get_meta( $_REQUEST['meta_id'] );
			echo json_encode($r);
			break;
		
		case 'update_meta':
			$this->update_meta($_REQUEST['meta_id'], $_POST['files']);
			break;
			
		case 'delete_file_n_update_meta':
			$this->delete_file_n_update_meta();
			break;
		
		case 'delete_unattached_files':
			if( isset($_POST['filename']))
				$this->unlink_file($_POST['filename']);
			if( isset($_POST['thumbnail_filename']))
				$this->unlink_file($_POST['thumbnail_filename']);
			break;
		
		case 'get_unattached_files':
			$r = $this->get_unattached_files();
			echo json_encode($r);
			break;
	}
	die();
}
	
	
	
	
/* Back-end
--------------------------------------------------------------------------------------- */

function activation() {
	global $wp_version;
	
	if (version_compare($wp_version, "3.1", "<")) 
		wp_die("This plugin requires WordPress version 3.1 or higher.");
	
	register_uninstall_hook( __FILE__, 'ubpfattach_uninstall' );
	
	$opts = array (
		'enable' => '',
		'max_size' => 1,
		'max_num' => 3,
		'allowed_file_type' => 'jpg, png, gif, zip',
		'upload_dir' => $this->id,
		'fm_number' => 20,
	);
	
	$saved = get_option($this->id);
	if ( !empty($saved) ) 
		foreach ($saved as $key=>$val) 
			$opts[$key] = $val;
	
	update_option($this->id, $opts);
}

function admin_init(){
	register_setting($this->id.'_options', $this->id, array( &$this, 'admin_options_vailidate'));
}

function admin_menu(){
	if( !is_super_admin() ) 
		return false;
	
	add_submenu_page( 
		'bp-general-settings', 
		'U '.__('Forum Attachment', $this->id), 
		'U '.__('Forum Attachment', $this->id), 
		'manage_options', 
		$this->id, 
		array(&$this, 'admin_options_page') 
	);
	
	add_submenu_page( 
		'bp-general-settings', 
		'U '.__('Forum Attachment', $this->id).' - '.__('File Manager', $this->id), 
		'U '.__('Forum Attachment', $this->id).' - '.__('File Manager', $this->id), 
		'manage_options', 
		$this->id.'-files', 
		array(&$this, 'admin_file_manager') 
	);
}

function admin_options_page(){
	
	
	$opts = (object) get_option($this->id);
	
	if( is_multisite() AND defined('BP_ROOT_BLOG') AND BP_ROOT_BLOG!=1){
		switch_to_blog(BP_ROOT_BLOG);
		$wp_upload_dir = wp_upload_dir();
		restore_current_blog();
	}else{
		$wp_upload_dir = wp_upload_dir();
	}
	
	$all_mimes = array_keys($this->get_all_mime_types());
	foreach($all_mimes as $i=>$mime) 
		$all_mimes[$i] = '['.str_replace('|', ',', $mime).']';
	$all_mimes = implode(', ', $all_mimes);
	?>
	
	<div class="wrap">
		<?php screen_icon("options-general"); ?>
		
		<h2>U <?php _e('BuddyPress Forum Attachment', $this->id);?></h2>
		
		<?php settings_errors( $this->id ) ?>
		
		<form action="<?php echo admin_url('options.php')?>" method="post">
			<?php settings_fields($this->id.'_options'); ?>
			<table class="form-table">
			<tr>
				<th><strong><?php _e('Enable', $this->id)?></strong></th>
				<td>
					<label><input type="checkbox" name="<?php echo $this->id?>[enable]" value="1" <?php checked($opts->enable, '1')?>> 
					<strong><?php _e('Enable', $this->id)?></strong></label>
					<p>&nbsp;</p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Upload directory', $this->id)?>*</th>
				<td>
					<?php echo $wp_upload_dir['basedir']?>/
					<input type="text" name="<?php echo $this->id?>[upload_dir]" value="<?php echo $opts->upload_dir;?>">
				</td>
			</tr>
			<tr>
				<th><?php _e('Max file size per file', $this->id)?>*</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[max_size]" value="<?php echo $opts->max_size;?>" size="1"> Mbytes
				</td>
			</tr>
			<tr>
				<th><?php _e('Max file count per post', $this->id)?>*</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[max_num]" value="<?php echo $opts->max_num;?>" size="1">
					<span class="description"><?php _e('How many files can be attached per post.', $this->id)?></span>
				</td>
			</tr>
			<tr>
				<th><?php _e('Upload file types', $this->id)?>*</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[allowed_file_type]" value="<?php echo $opts->allowed_file_type;?>" class="regular-text">
					<p class="description"><?php _e('Separate file extentions with commas.', $this->id)?></p>
					<br>
					<p><strong>Available file types</strong></p>
					<p class="description"><?php _e("Extention(s) in the square brackets is same type each other. so, for example, if you inputted 'jpg', you don't need to input 'jpeg' or 'jpe'.", $this->id)?></p>
					<p><code style="font-size:10px;"><?php echo $all_mimes?></code></p>
				</td>
			</tr>
			
			<tr>
				<th><?php _e('Number of posts to show in File Manager', $this->id)?></th>
				<td>
					<input type="text" name="<?php echo $this->id?>[fm_number]" value="<?php echo $opts->fm_number;?>" size="1">
				</td>
			</tr>
			
			</table>
			
			<p class="submit">
				<input name="submit" type="submit" class="button-primary" value="<?php esc_attr_e(__('Save Changes', $this->id)); ?>" />
			</p>
		</form>
	</div>
	<?php
}

function admin_options_vailidate($input){
	$r = array();
	$r['enable'] = $input['enable'];
	$r['upload_dir'] = preg_replace( '/[^a-z0-9_\-\.\/]/', '', untrailingslashit($input['upload_dir']) );
	$r['max_size'] = floatval($input['max_size']);
	$r['max_num'] = absint($input['max_num']);
	$r['allowed_file_type'] = trim($input['allowed_file_type']);
	$r['fm_number'] = absint($input['fm_number']) ? absint($input['fm_number']) : 20;
		
	if( !$r['upload_dir'] || !$r['max_size'] || !$r['max_num'] || !$r['allowed_file_type'] ){
		add_settings_error($this->id, 'settings_error', __('Error: please fill the required fields.', $this->id), 'error');
		$r = get_option($this->id);
	} else {
		add_settings_error($this->id, 'settings_updated', __('Settings saved.'), 'updated');
	}
	return $r;
}


function admin_file_manager(){
	global $wpdb, $bp;
	$opts = get_option($this->id);
	
	$paged = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
	$items_per_page = absint($opts['fm_number']) ? absint($opts['fm_number']) : 20;
	$total_item = $this->get_meta_total();
	$total_page = ceil($total_item/$items_per_page);
	$offset =  ($paged * $items_per_page) - $items_per_page;
	
	$pager_args = array(
		'base' => @add_query_arg('paged','%#%'),
		'format' => '',
		'total' => $total_page,
		'current' => $paged,
	);
	$page_links = paginate_links( $pager_args );
	
	$upload_dir_path = $this->get_upload_dir_path();
	$upload_dir_url = $this->get_upload_dir_url();
	$base_url = add_query_arg(array('page'=>$_GET['page'], 'paged'=>$paged), network_admin_url('admin.php'));
	
	$sql = $wpdb->prepare("SELECT * FROM {$this->meta_table} WHERE meta_key=%s ORDER BY meta_id DESC LIMIT %d, %d", $this->id.'_attachments', $offset, $items_per_page);
	$meta_rows = $wpdb->get_results($sql);
	$meta_index = 0;
	$rows = array();
	
	foreach( $meta_rows as $meta_row){
		if( empty($meta_row->meta_value) ) continue;
		$files = (array) json_decode($meta_row->meta_value);
		if( !count($files) ) continue;
				
		$post = $userdata = $author = $title = $date = $post_id = $post_status = '';
				
		if( $meta_row->object_type=='bb_topic' ){
			$sql = "SELECT t.*, f.forum_slug FROM {$this->bb_prefix}topics AS t INNER JOIN {$this->bb_prefix}forums AS f ON t.forum_id = f.forum_id WHERE t.topic_id=%d";
			$post = $wpdb->get_row( $wpdb->prepare($sql, $meta_row->object_id));
			if( $post ){
				$post_id = $post->topic_id;
				$post_status = $post->topic_status;
				$title = $post->topic_title;
				if( $post_status==0 )
					$title = '<a href="'.$bp->root_domain.'/'.BP_GROUPS_SLUG.'/'.$post->forum_slug.'/forum/topic/'.$post->topic_slug.'">'.$title.'</a>';
				$userdata = get_userdata($post->topic_poster);
				$author = $userdata->display_name;
				$date = $post->topic_start_time;
				$date = date('Y/m/d', strtotime($date));
			}
		}else{
			$sql = "SELECT t.*, p.*, f.forum_slug FROM {$this->bb_prefix}topics AS t INNER JOIN {$this->bb_prefix}posts AS p ON t.topic_id = p.topic_id INNER JOIN {$this->bb_prefix}forums AS f ON t.forum_id = f.forum_id WHERE post_id=%d";
			$post = $wpdb->get_row( $wpdb->prepare($sql, $meta_row->object_id));
			if( $post ){
				$post_id = $post->post_id;
				$post_status = $post->post_status;
				$title = '@'.$post->topic_title;
				if( $post_status==0 )
					$title = '<a href="'.$bp->root_domain.'/'.BP_GROUPS_SLUG.'/'.$post->forum_slug.'/forum/topic/'.$post->topic_slug.'/#post-'.$post->post_id.'">'.$title.'</a>';
				$userdata = get_userdata($post->poster_id);
				$author = $userdata->display_name;
				$date = $post->post_time;
				$date = date('Y/m/d', strtotime($date));
			}
		}
		switch($post_status){
			case '': $post_status = __('(Deleted)', $this->id); break;
			case 1: $post_status = __('(Trash)', $this->id); break;
			case 0: $post_status = ''; break;
		}
		
		$row = array();
		foreach( $files as $file_index=>$file ){
			$file_index = (string) $file_index;
			
			// for under version 1.2
			if( $file_index=='0' || absint($file_index)>0 ){
				$file_index = $file->filename;
				$_files = array();
				foreach($files as $_file)
					$_files[$_file->filename] = $_file;
				$this->update_meta($meta_row->meta_id, $_files);
			}
			$x = explode('.', $file->filename);
			$ext = end($x);
			$is_image = ( $ext=='jpg' || $ext=='jpeg' || $ext=='gif' || $ext=='png' ) ? true : false;
			$thumbnail = '';
			if( $is_image ){
				$thumbnail = $file->url;
				if( !empty($file->thumbnail_filename) AND file_exists($upload_dir_path.$file->thumbnail_filename) )
					$thumbnail = $upload_dir_url.$file->thumbnail_filename;
			}else{
				$ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $file->filename);
				if ( !empty($ext) AND $mime=wp_ext2type($ext) ) 
					$thumbnail = wp_mime_type_icon($mime);
			}
			$thumbnail = "<img src='$thumbnail' width='40'>";
					
			$delete_url = add_query_arg(array(
				'action' => $this->id.'_delete_file',
				'meta_id' => $meta_row->meta_id.'|'.$file_index,
				'_wpnonce' => wp_create_nonce($this->id.'_nonce'), 
				'_wp_http_referer' => urlencode($base_url),
			), '');
			
			$row[] = (object) array(
				'meta_id' => $meta_row->meta_id,
				'file_index' => $file_index,
				'file' => $file,
				'thumbnail' => $thumbnail,
				'title' => $title,
				'delete_url' => $delete_url,
				'post_status' => $post_status,
				'author' => $author,
				'date' => $date,
			);
		}
		$rows[] = $row;
	}
	?>
	
	<div class="wrap">
		<?php screen_icon("options-general"); ?>
		
		<h2>U <?php _e('BuddyPress Forum Attachment', $this->id);?> : <?php _e('File Manager', $this->id)?></h2>
		<br>
		
		<form action="" method="get" id="files-form">
		<?php wp_nonce_field($this->id.'_nonce')?>
	
		<div class="tablenav top">
			<div class="alignleft">
				<select class="action" name="action">
					<option value="-1" selected="selected"><?php _e('Bulk Actions')?></option>
					<option value="<?php echo $this->id?>_delete_file"><?php _e('Delete Permanently')?></option>
					<input type="submit" value="<?php _e('Apply')?>" class="button">
				</select>
			</div>
			<div class="tablenav-pages">
				<div class="pagination-link"><?php echo $page_links?></div>
			</div>
		</div>
		
		<table class="widefat fixed" id="files-table">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox"></th>
					<th class="thumb-column"></th>
					<th class="file-column"><?php _e('File', $this->id)?></th>
					<th class="attached-to-column"><?php _e('Attached to', $this->id)?></th>
					<th class="author-column"><?php _e('Author', $this->id)?></th>
					<th class="date-column"><?php _e('Date', $this->id)?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$i = 0;
			foreach( $rows as $row){
				$alternate = (($i++)%2==0) ? 'alternate' : '';
				$row_count = count($row);
				$j = 0;
				foreach( $row as $r ){ ?>
				<tr class="<?php echo $alternate?>">
					<td class="check-column">
						<input type="checkbox" name="meta_id[]" value="<?php echo $r->meta_id?>|<?php echo $r->file_index?>">
					</td>
					<td class="thumb-column">
						<?php echo $r->thumbnail?>
					</td>
					<td class="file-column">
						<strong><?php echo $r->file->filename?></strong>
						<div class="row-actions">
							<span class="delete"><a href="<?php echo $r->delete_url?>" class="submitdelete"><?php _e('Delete Permanently')?></a></span> |
							<span class="view"><a href="<?php echo $r->file->url?>" target="_blank"><?php _e('View')?></a>
						</div>
					</td>
					<?php if( ($j++)==0 ): ?>
					<td class="attached-to-column rowspan" rowspan="<?php echo $row_count?>"><?php echo $r->post_status?> <?php echo $r->title?></td>
					<td class="author-column rowspan" rowspan="<?php echo $row_count?>"><?php echo $r->author?></td>
					<td class="date-column rowspan" rowspan="<?php echo $row_count?>"><?php echo $r->date?></td>
					<?php endif; ?>
				</tr>
			<?php }} ?>
			</tbody>
		</table>
		
		<div class="tablenav bottom"></div>
		
		</form>
		
		
		<p><br><a href="#" id="sho-unattached-files">Show Unattached Files</a></p>
		<div id="unattached-files">
			<h3>Unattached Files</h3>
			<table class="widefat">
				<thead>
					<tr>
						<th>Filename</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="2"><img class="status" src="<?php echo $this->url?>inc/loader.gif"></td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>
	
	<style>
	#files-table td { vertical-align: middle; }
	#files-table .check-column input { margin-left: 8px;}
	#files-table .thumb-column {width: 65px;}
	#files-table .author-column {width: 120px;}
	#files-table .date-column {width: 100px;}
	#files-table td.rowspan {border-left: 1px dashed #ddd;}
	#files-form .tablenav.bottom .tablenav-pages { float: none;}
	#unattached-files {display: none;}
	#unattached-files table{width: auto;}
	#unattached-files h3 { margin-top: 0; }
	</style>
	
	<script>
	(function($) { $(function(){
	
	$('<tfoot/>').html($('#files-table thead').html()).insertAfter($('#files-table thead'));
	
	$('#files-table th input[type=checkbox]').change(function(){ 
		$('#files-table tb input[type=checkbox]').attr('checked', this.checked);
	});
	
	$('#files-form .tablenav-pages').clone().appendTo('#files-form .tablenav.bottom');
	
	$('#files-form').submit(function(){ 
		if( $(this).find('select.action').val()=='-1' ) 
			return false; 
	});
	
	$('a#sho-unattached-files').click( function(){
		$(this).hide();
		$('#unattached-files').show();
		
		var args = {
			action: 'ubpfattach_ajax',
			action_scope: 'get_unattached_files',
			_ajax_nonce: '<?php echo wp_create_nonce( $this->id.'_nonce' )?>'
		}
		
		$.post('<?php echo admin_url('admin-ajax.php')?>', args, function(res){
			var t = $('#unattached-files');
			t.find('img.status').hide();
			var tbody = t.find('table tbody');
			
			if( !res || res.length==0 ){
				tbody.find('td').append('<?php _e('No unattached files', $this->id)?>');
				return;
			}
			
			tbody.find('tr').remove();
			for( var i in res ){
				var filename = res[i];
				var file_type = filename.substr(filename.lastIndexOf('.')+1);
				var is_image = (file_type=='jpg'||file_type=='jpeg'||file_type=='gif'||file_type=='png') ? true : false;
				var view_link = is_image ? '<a href="<?php echo $upload_dir_url?>'+filename+'" target="_blank"><?php _e('View')?></a> | ' : '';
			
				var delete_link = $('<a href="#">Delete</a>');
				$.data(delete_link[0], 'filename', filename);
				
				delete_link.click( function(){
					var t = $(this).hide();
					var args = {
						action: 'ubpfattach_ajax',
						action_scope: 'delete_unattached_files',
						_ajax_nonce: '<?php echo wp_create_nonce( $this->id.'_nonce' )?>',
						filename: $.data(this, 'filename')
					}
					$.post('<?php echo admin_url('admin-ajax.php')?>', args, function(r){
						t.parents('tr:eq(0)').fadeOut();
					});
					return false;
				} );
					
				var tr = $('<tr><td class="filename">'+filename+'</td><td class="actions"></td></tr>');
				tr.find('td.actions').append( view_link, delete_link );
				tr.appendTo( tbody );
			}
		}, 'json');
		
		return false;
	});
	
	}); })(jQuery);
	</script>
	
	<?php
}


} // end of class

$ubpfattach = new UBPForumAttachment;


function ubpfattach_uninstall(){
	delete_option('ubpfattach');
}

