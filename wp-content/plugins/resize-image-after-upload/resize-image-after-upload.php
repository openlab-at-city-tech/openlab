<?php
/*
Plugin Name: Resize Image After Upload
Plugin URI: http://www.jepsonrae.com/?utm_campaign=plugins&utm_source=wp-resize-image-after-upload&utm_medium=plugin-url
Description: This plugin resizes uploaded images to a given width or height (whichever is the largest) after uploading, discarding the original uploaded file in the process.
Author: Jepson Rae
Version: 1.4.2
Author URI: http://www.jepsonrae.com/?utm_campaign=plugins&utm_source=wp-resize-image-after-upload&utm_medium=author-url



Copyright (C) 2008 A. Huizinga (original Resize at Upload plugin)
Copyright (C) 2013 Jepson Rae Ltd



Includes hints and code by:
	Huiz.net (www.huiz.net)
  	Jacob Wyke (www.redvodkajelly.com)
  	Paolo Tresso / Pixline (http://pixline.net)  



This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$PLUGIN_VERSION = '1.4.2';


// Default plugin values
if(get_option('jr_resizeupload_version') != $PLUGIN_VERSION) {

  add_option('jr_resizeupload_version', 			$PLUGIN_VERSION, '','yes');
  add_option('jr_resizeupload_width', 				'1200', '', 'yes');
  add_option('jr_resizeupload_height',				'1200', '', 'yes');
  add_option('jr_resizeupload_quality',				'90', '', 'yes');
  add_option('jr_resizeupload_resize_yesno', 		'yes', '','yes');
  add_option('jr_resizeupload_convertbmp_yesno', 	'no', '', 'no');
}



// Hook in the options page
add_action('admin_menu', 'jr_uploadresize_options_page');  

// Hook the function to the upload handler
if (get_option('jr_resizeupload_resize_yesno') == 'yes') {
	add_action('wp_handle_upload', 'jr_uploadresize_resize'); // apply our modifications
} 





  
/**
* Add the options page
*/
function jr_uploadresize_options_page(){
	if(function_exists('add_options_page')){
		add_options_page(
			'Resize Image After Upload', 
			'Resize Image Upload', 
			'manage_options', 
			'resize-after-upload', 
			'jr_uploadresize_options'
		);
	} 
} // function jr_uploadresize_options_page(){ 



/** 
* Define the Options page for the plugin
*/
function jr_uploadresize_options(){

  if (isset($_POST['jr_options_update'])) {
  
    $maxwidth = trim(mysql_real_escape_string($_POST['maxwidth']));
    $maxheight = trim(mysql_real_escape_string($_POST['maxheight']));
    $quality = trim(mysql_real_escape_string($_POST['quality']));
    $yesno = $_POST['yesno'];
    $convert_bmp = $_POST['convertbmp'];
    
    // if input is empty or not an integer, use previous setting
    if ($maxwidth == '' || ctype_digit(strval($maxwidth)) == FALSE) {
    	$maxwidth = get_option('jr_resizeupload_width');
    } 
    
    if ($maxheight == '' || ctype_digit(strval($maxheight)) == FALSE) {
    	$maxheight = get_option('jr_resizeupload_height');
    } 
    
    if ($quality == '' || ctype_digit(strval($quality)) == FALSE) {
		$quality = get_option('jr_resizeupload_quality');
    }
    
    if($quality<0) {
    	$quality=0;
    }
    else if($quality>100) {
    	$quality=100;
    }
    
    
    update_option('jr_resizeupload_width',$maxwidth);
    update_option('jr_resizeupload_height',$maxheight);
    update_option('jr_resizeupload_quality',$quality);
    
    if ($yesno == 'yes') {
      update_option('jr_resizeupload_resize_yesno','yes');
    } // if
    else {
      update_option('jr_resizeupload_resize_yesno','no');
    } // else
    
    
    if ($convert_bmp == 'yes') {
      update_option('jr_resizeupload_convertbmp_yesno','yes');
    } // if
    else {
      update_option('jr_resizeupload_convertbmp_yesno','no');
    } // else
    
    

    echo('<div id="message" class="updated fade"><p><strong>Options have been updated.</strong></p></div>');
  } // if



  // get options and show settings form
  $maxwidth = get_option('jr_resizeupload_width');
  $maxheight = get_option('jr_resizeupload_height');
  $quality = intval(get_option('jr_resizeupload_quality'));
  $yesno = get_option('jr_resizeupload_resize_yesno');
  $convert_bmp = get_option('jr_resizeupload_convertbmp_yesno');
?>

<div class="wrap">
	<form method="post" accept-charset="utf-8">

		<h2>Resize Image After Upload Options</h2>
		<p>This plugin resizes uploaded images to given maximum width and/or height after uploading, discarding the original uploaded file in the process.
	You can set the max width and max height, and images (JPEG, PNG or GIF) will be resized automatically after they are uploaded.</p>
		
		<!-- <p>If 'Convert BMPs to JPEGs' is enabled, then BMP files will also be resized.</p> -->

		<p>Your file will be resized, there will not be a copy or backup with the original size.</p>

		<p>Set the option 'Resize' to no if you want to disable resizing, this way you shouldn&#8217;t need to deactivate the plugin if you don&#8217;t want to resize for a while.</p>

		<h3 style="margin-top:20px;border-top:1px solid #eee;padding-top:20px;">Settings</h3>
		<table class="form-table">
			<tr>
				<td valign="top">Resize images:&nbsp;</td>
				<td valign="top">
					<select name="yesno" id="yesno">  
						<option value="no" label="no" <?php echo ($yesno == 'no') ? 'selected="selected"' : ''; ?>>No</option>
						<option value="yes" label="yes" <?php echo ($yesno == 'yes') ? 'selected="selected"' : ''; ?>>Yes</option>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">Maximum width and height (pixels):&nbsp;</td>
				<td valign="top">
					<input type="text" name="maxwidth" size="7" id="maxwidth" value="<?php echo $maxwidth; ?>" /> px-wide
					<br /><input type="text" name="maxheight" size="7" id="maxheight" value="<?php echo $maxheight; ?>" /> px-high
					<br /><small>Integer pixel value (e.g. 1200). </small>
					<br /><small>Set to zero (0) to prevent resizing in that dimension.</small>
					<br /><small>Recommended value: 1200</small>
				</td>
			</tr>
			
			<tr>
				<td valign="top">Compression quality (for JPEGs):&nbsp;</td>
				<td valign="top">
					<select id="quality" name="quality">
					<?php for($i=1; $i<=100; $i++) : ?>
						<option value="<?php echo $i; ?>" <?php if($quality == $i) : ?>selected<?php endif; ?>><?php echo $i; ?></option>
					<?php endfor; ?>
					</select>
					<br /><small>Integer between 0 (low quality, smallest files) and 100 (best quality, largest files)
					<br />Default value: 90</small>
				</td>
			</tr>
			
<!-- 
			<tr>
				<td valign="top">Convert BMPs to JPEGs and resize:&nbsp;</td>
				<td valign="top">
					<select id="convert-bmp" name="convertbmp">
						<option value="no" <?php if($convert_bmp == 'no') : ?>selected<?php endif; ?>>No</option>
						<option value="yes" <?php if($convert_bmp == 'yes') : ?>selected<?php endif; ?>>Yes</option>
					</select>
					<br /><small>When a BMP is uploaded, it will automatically be converted to a JPEG
					<br />Selecting 'No' will prevent BMPs from being resized</small>
				</td>
			</tr>
 -->
		</table>

		<p class="submit" style="margin-top:20px;border-top:1px solid #eee;padding-top:20px;">
		  <input type="hidden" id="convert-bmp" name="convertbmp" value="no" />
		  <input type="hidden" name="action" value="update" />  
		  <input id="submit" name="jr_options_update" class="button button-primary" type="submit" value="Update Options">
		</p>
	</form>

</div>
<?php
} // function jr_uploadresize_options(){



/**
* This function will apply changes to the uploaded file 
* @param $array - contains file, url, type
*/
function jr_uploadresize_resize($array){ 

  if(
  	$array['type'] == 'image/jpeg' || 
  	$array['type'] == 'image/gif' || 
  	$array['type'] == 'image/png' || 
   	$array['type'] == 'image/bmp'
  )	
  {

    // Include the file to carry out the resizing
    require_once('class.resize.php');

	// Get resizing limits
    $max_width = get_option('jr_resizeupload_width');
    $max_height = get_option('jr_resizeupload_height');
    
    $quality = get_option('jr_resizeupload_quality');
    
    $convert_bmp = get_option('jr_resizeupload_convertbmp_yesno');
	$convert_bmp = ($convert_bmp=='yes') ? true : false;


	// Get original image sizes
    $original_info = getimagesize($array['file']);
    $original_width = $original_info[0];
    $original_height = $original_info[1];
    $is_bitmap = ($array['type'] == 'image/bmp') ? true : false;

	
	// Perform the resize only if required, i.e. the image is larger than the max sizes
	if( $original_width > $max_width || 
		$original_height > $max_height || 
		($is_bitmap && $convert_bmp)
	) {
	
		//Resize by width
		if($original_width > $original_height && $max_width != 0) {
			$objResize = new RVJ_ImageResize($array['file'], $array['file'], 'W', $max_width, true, $quality);
		
		} 
	
		//Resize by height
		else if($max_height != 0) {
			$objResize = new RVJ_ImageResize($array['file'], $array['file'], 'H', $max_height, true, $quality);
		}
	}
  } // if(...) 
  
  return $array;
} // function jr_uploadresize_resize($array){ 