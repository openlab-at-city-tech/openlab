<?php
/*
Plugin Name: Resize Image After Upload
Plugin URI: https://wordpress.org/plugins/resize-image-after-upload/
Description: Automatically resize uploaded images to within specified maximum width and height. Also has option to force recompression of JPEGs. Configuration options found under <a href="options-general.php?page=resize-after-upload">Settings > Resize Image Upload</a>
Author: ShortPixel
Version: 1.8.6
Author URI: https://shortpixel.com

Copyright (C) 2017 ShortPixel

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

$PLUGIN_VERSION = '1.8.6';
$DEBUG_LOGGER = false;


// Default plugin values
if(get_option('jr_resizeupload_version') != $PLUGIN_VERSION) {

  add_option('jr_resizeupload_version', 			$PLUGIN_VERSION, '','yes');
  add_option('jr_resizeupload_width', 				'1200', '', 'yes');
  add_option('jr_resizeupload_height',				'1200', '', 'yes');
  add_option('jr_resizeupload_quality',				'90', '', 'yes');
  add_option('jr_resizeupload_resize_yesno', 		'yes', '','yes');
  add_option('jr_resizeupload_recompress_yesno', 	'no', '','yes');
  add_option('jr_resizeupload_convertbmp_yesno', 	'no', '', 'yes');
  add_option('jr_resizeupload_convertpng_yesno', 	'no', '', 'yes');
  add_option('jr_resizeupload_convertgif_yesno', 	'no', '', 'yes');
}



// Hook in the options page
add_action('admin_menu', 'jr_uploadresize_options_page');

// Hook the function to the upload handler
add_action('wp_handle_upload', 'jr_uploadresize_resize');

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'jr_generate_plugin_links');//for plugin settings page

//add_action('admin_notices', 'jr_display_notices');
//add_action('wp_ajax_jr_dismiss_notices', 'jr_dismiss_notices');

/**
 * Add ths link to Settings in Plugins Page
 */
function jr_generate_plugin_links($links) {
  $settings_link = '<a href="options-general.php?page=resize-after-upload">Settings</a>';
  array_unshift( $links, $settings_link );
  return $links;
}

function jr_display_notices() {
  if(get_option( 'jr_resizeupload_news') != 1 ) {
    global $jr_settings_page;
    $screen = get_current_screen();
    if ( $screen->id != $jr_settings_page ) { ?>
      <div class='notice notice-warning' id='jr-resizeupload-news' style="padding-top: 7px">
        <div style="float:right;"><a href="javascript:jrResizeuploadDismissNews()" class="button" style="margin-top:10px;">Dismiss</a></div>
        <strong>Resize Image After Upload</strong>
        <p>Check out the <a href="options-general.php?page=resize-after-upload">Plugin settings</a> for new features that can make your site load faster.</p>
      </div>
      <script>
        function jrResizeuploadDismissNews() {
          jQuery("#jr-resizeupload-news").hide();
          var data = { action  : 'jr_dismiss_notices'};
          jQuery.get('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
            data = JSON.parse(response);
            if(data["Status"] == 0) {
              console.log("dismissed");
            }
          });
        }
      </script>
    <?php }
  }
}

function jr_dismiss_notices() {
  update_option( 'jr_resizeupload_news', 1);
  die(json_encode(array("Status" => 0)));
}

/**
* Add the options page
*/
function jr_uploadresize_options_page(){
    global $jr_settings_page;
	if(function_exists('add_options_page')){
      $jr_settings_page = add_options_page(
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

  if(isset($_POST['jr_options_update'])) {

      if(!(current_user_can('manage_options') &&
          wp_verify_nonce($_POST['_wpnonce'], 'jr-options-update'))) {
          wp_die("Not authorized");
      }

    $resizing_enabled = ($_POST['yesno'] == 'yes' ? 'yes' : 'no');
    $force_jpeg_recompression   = ($_POST['recompress_yesno'] == 'yes' ? 'yes' : 'no');

    $max_width   = intval($_POST['maxwidth']);
    $max_height  = intval($_POST['maxheight']);
    $compression_level    = intval($_POST['quality']);

    $convert_png_to_jpg = (isset($_POST['convertpng']) && $_POST['convertpng'] == 'yes' ? 'yes' : 'no');
    $convert_gif_to_jpg = (isset($_POST['convertgif']) && $_POST['convertgif'] == 'yes' ? 'yes' : 'no');
    $convert_bmp_to_jpg = (isset($_POST['convertbmp']) && $_POST['convertbmp'] == 'yes' ? 'yes' : 'no');


    // If input is not an integer, use previous setting
    $max_width = ($max_width == '') ? 0 : $max_width;
    $max_width = (ctype_digit(strval($max_width)) == false) ? get_option('jr_resizeupload_width') : $max_width;
    update_option('jr_resizeupload_width',$max_width);


    $max_height = ($max_height == '') ? 0 : $max_height;
    $max_height = (ctype_digit(strval($max_height)) == false) ? get_option('jr_resizeupload_height') : $max_height;
    update_option('jr_resizeupload_height',$max_height);


    $compression_level = ($compression_level == '') ? 1 : $compression_level;
    $compression_level = (ctype_digit(strval($compression_level)) == false) ? get_option('jr_resizeupload_quality') : $compression_level;

    if($compression_level < 1) {
    	$compression_level = 1;
    }
    else if($compression_level > 100) {
    	$compression_level = 100;
    }

    update_option('jr_resizeupload_quality',$compression_level);




    if ($resizing_enabled == 'yes') {
      update_option('jr_resizeupload_resize_yesno','yes'); }
    else {
      update_option('jr_resizeupload_resize_yesno','no'); }


    if ($force_jpeg_recompression == 'yes') {
      update_option('jr_resizeupload_recompress_yesno','yes'); }
    else {
      update_option('jr_resizeupload_recompress_yesno','no'); }


    if ($convert_png_to_jpg == 'yes') {
      update_option('jr_resizeupload_convertpng_yesno','yes'); }
    else {
      update_option('jr_resizeupload_convertpng_yesno','no'); }

    if ($convert_gif_to_jpg == 'yes') {
      update_option('jr_resizeupload_convertgif_yesno','yes'); }
    else {
      update_option('jr_resizeupload_convertgif_yesno','no'); }

    if ($convert_bmp_to_jpg == 'yes') {
      update_option('jr_resizeupload_convertbmp_yesno','yes'); }
    else {
      update_option('jr_resizeupload_convertbmp_yesno','no'); }



    echo('<div id="message" class="updated fade"><p><strong>Options have been updated.</strong></p></div>');
  } // if



  // get options and show settings form
  $resizing_enabled = get_option('jr_resizeupload_resize_yesno');
  $force_jpeg_recompression = get_option('jr_resizeupload_recompress_yesno');
  $compression_level  = intval(get_option('jr_resizeupload_quality'));

  $max_width     = get_option('jr_resizeupload_width');
  $max_height    = get_option('jr_resizeupload_height');

  $convert_png_to_jpg = get_option('jr_resizeupload_convertpng_yesno');
  $convert_gif_to_jpg = get_option('jr_resizeupload_convertgif_yesno');
  $convert_bmp_to_jpg = get_option('jr_resizeupload_convertbmp_yesno');
?>
<style type="text/css">
.resizeimage-button {
  color: #FFF;
  background: none repeat scroll 0% 0% #FC9A24;
  border-radius: 3px;
  display: inline-block;
  border-bottom: 4px solid #EC8A14;
  margin-right:5px;
  line-height:1.05em;
  text-align: center;
  text-decoration: none;
  padding: 9px 20px 8px;
  font-size: 15px;
  font-weight: bold;
  text-shadow: 0 -1px 1px rgba(0,0,0,0.2);
}

.resizeimage-button:active,
.resizeimage-button:hover,
.resizeimage-button:focus {
  background-color: #EC8A14;
  color: #FFF;
}

.media-upload-form div.error, .wrap div.error, .wrap div.updated {
  margin: 25px 0px 25px;
}

</style>

<div class="wrap">
	<form method="post" accept-charset="utf-8">

		<h2><img src="<?php echo plugins_url('icon-128x128.png', __FILE__ ); ?>" style="float:right; border:1px solid #ddd;margin:0 0 15px 15px;width:100px; height:100px;" />Resize Image After Upload</h2>

		<div style="max-width:700px">
  		<p>This plugin automatically resizes uploaded images (JPEG, GIF, and PNG) to within a given maximum width and/or height to reduce server space usage. This may be necessary due to the fact that images from digital cameras and smartphones can now be over 10MB each due to higher megapixel counts.</p>

  		<p>In addition, the plugin can force re-compression of uploaded JPEG images, regardless of whether they are resized or not; and convert uploaded GIF and PNG images into JPEG format.</p>

  		<p><strong>Note:</strong> the resizing/recompression process will discard the original uploaded file including EXIF data.</p>

  		<p>This plugin is not intended to replace the WordPress <em>add_image_size()</em> function, but rather complement it. Use this plugin to ensure that no excessively large images are stored on your server, then use <em>add_image_size()</em> to create versions of the images suitable for positioning in your website theme.</p>

  		<p>This plugin uses standard PHP image resizing functions and will require a high amount of memory (RAM) to be allocated to PHP in your php.ini file (e.g 512MB).</p>

  		<h4 style="font-size: 15px;font-weight: bold;margin: 2em 0 0;">Like the plugin?</h4>

  		<p>This plugin was written for free (as in free beer). If you find it useful please consider donating some small change to my beer fund because beer is very seldom free. Thanks!</p>

  		<p style="padding-bottom:2em;" class="resizeimage-button-wrapper">
  		  <a class="resizeimage-button" href="https://www.paypal.me/resizeImage" target="_blank">Donate cash</a>
  		</p>
 		</div>

		<hr style="margin-top:20px; margin-bottom:0;">
		<hr style="margin-top:1px; margin-bottom:40px;">

		<h3>Re-sizing options</h3>
		<table class="form-table">
			<tr>
				<th scope="row">Enable re-sizing</th>
				<td valign="top">
					<select name="yesno" id="yesno">
						<option value="no" label="no" <?php echo ($resizing_enabled == 'no') ? 'selected="selected"' : ''; ?>>NO - do not resize images</option>
						<option value="yes" label="yes" <?php echo ($resizing_enabled == 'yes') ? 'selected="selected"' : ''; ?>>YES - resize large images</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">Max image dimensions</th>

				<td>
					<fieldset><legend class="screen-reader-text"><span>Maximum width and height</span></legend>
						<label for="maxwidth">Max width</label>
						<input name="maxwidth" step="1" min="0" id="maxwidth" class="small-text" type="number" value="<?php echo $max_width; ?>">
						&nbsp;&nbsp;&nbsp;<label for="maxheight">Max height</label>
						<input name="maxheight" step="1" min="0" id="maxheight" class="small-text" type="number" value="<?php echo $max_height; ?>">
						<p class="description">Set to zero or very high value to prevent resizing in that dimension.
						<br />Recommended values: <code>1200</code></p>
					</fieldset>
				</td>


			</tr>

		</table>

		<hr style="margin-top:20px; margin-bottom:30px;">

		<h3>Compression options</h3>
		<p style="max-width:700px">The following settings will only apply to uploaded JPEG images and images converted to JPEG format.</p>

		<table class="form-table">

			<tr>
				<th scope="row">JPEG compression level</th>
				<td valign="top">
					<select id="quality" name="quality">
					<?php for($i=1; $i<=100; $i++) : ?>
						<option value="<?php echo $i; ?>" <?php if($compression_level == $i) : ?>selected<?php endif; ?>><?php echo $i; ?></option>
					<?php endfor; ?>
					</select>
					<p class="description"><code>1</code> = low quality (smallest files)
					<br><code>100</code> = best quality (largest files)
					<br>Recommended value: <code>90</code></p>
				</td>
			</tr>

			<tr>
				<th scope="row">Force JPEG re-compression</th>
				<td>
					<select name="recompress_yesno" id="yesno">
						<option value="no" label="no" <?php echo ($force_jpeg_recompression == 'no') ? 'selected="selected"' : ''; ?>>NO - only re-compress resized jpeg images</option>
						<option value="yes" label="yes" <?php echo ($force_jpeg_recompression == 'yes') ? 'selected="selected"' : ''; ?>>YES - re-compress all uploaded jpeg images</option>
					</select>
				</td>
			</tr>

		</table>

        <p class="description"><strong>
            Note that any changes you make will only affect new images uploaded to your site. A specialized plugin can optimize all your present images and will also optimize new ones as they are added.
        </strong></p>
        <p class="description" style="font-size:1.1em;margin: 15px 0;"><strong>
            <a href="https://shortpixel.com/riau/af/WVCLIKV28044?autoreferrer=1" target="_blank">
                Test your website with  ShortPixel for free to see how much you could gain by optimizing your images
            </a>
        </strong></p>
        <a href="https://shortpixel.com/riau/af/WVCLIKV28044?autoreferrer=1" target="_blank"><img src="<?php echo plugins_url(); ?>/resize-image-after-upload/img/sp.png" style="float:left;margin-right:20px;"/></a>
        <p class="description">
            ShortPixel is an easy to use, comprehensive, stable and frequently updated image optimization plugin supported by the friendly team that created it. Using a powerful set of specially tuned algorithms, it squeezes the most of each image striking the best balance between image size and quality. Current images can be all optimized with a single click. Newly added images are automatically resized/rescaled and optimized on the fly, in the background.
        </p>
        <p class="description-link">
            <a href="https://shortpixel.com/riau/af/WVCLIKV28044?autoreferrer=1" target="_blank">&gt;&gt; <?php _e( 'More info', 'sb-pack' ); ?></a>
        </p>

		<hr style="margin-top:20px; margin-bottom:20px;">

		<h3>Image conversion options</h3>
		<p style="max-width:700px">Photos saved as PNG <?php //and GIF ?> images can be extremely large in file size due to their compression methods not being suited for photos. Enable these options below to automatically convert <?php //GIF and/or ?>PNG images to JPEG <strong>only if they don't have transparency</strong></strong>.</p>

		<p>When enabled, conversion will happen to all suitable uploaded PNG images, not just ones that require resizing.</p>

		<table class="form-table">

          <tr>
            <th scope="row">Convert PNG to JPEG</th>
            <td>
              <select id="convert-png" name="convertpng">
                <option value="no" <?php if($convert_png_to_jpg == 'no') : ?>selected<?php endif; ?>>NO - just resize uploaded png images as normal</option>
                <option value="yes" <?php if($convert_png_to_jpg == 'yes') : ?>selected<?php endif; ?>>YES - convert all uploaded png images not having a transparency layer to jpeg</option>
              </select>
            </td>
          </tr>

          <?php /* DEFINED HERE FOR FUTURE RELEASE - does not do anything if uncommented
			<tr>
				<th scope="row">Convert GIF to JPEG</th>
				<td>
					<select id="convert-gif" name="convertgif">
						<option value="no" <?php if($convert_gif_to_jpg == 'no') : ?>selected<?php endif; ?>>NO - just resize uploaded gif images as normal</option>
						<option value="yes" <?php if($convert_gif_to_jpg == 'yes') : ?>selected<?php endif; ?>>YES - convert all uploaded gif images to jpeg</option>
					</select>
				</td>
			</tr>

		*/ ?>

		</table>

		<hr style="margin-top:30px;">

		<p class="submit" style="margin-top:10px;border-top:1px solid #eee;padding-top:20px;">
		  <input type="hidden" id="convert-bmp" name="convertbmp" value="no" />
          <input type="hidden" name="action" value="update" />
          <?php wp_nonce_field('jr-options-update'); ?>
		  <input id="submit" name="jr_options_update" class="button button-primary" type="submit" value="Update Options">
		</p>
	</form>

</div>
<?php
} // function jr_uploadresize_options(){





/**
* This function will apply changes to the uploaded file
* @param $image_data - contains file, url, type
*/
function jr_uploadresize_resize($image_data){


  jr_error_log("**-start--resize-image-upload");


  $resizing_enabled = get_option('jr_resizeupload_resize_yesno');
  $resizing_enabled = ($resizing_enabled=='yes') ? true : false;

  $force_jpeg_recompression = get_option('jr_resizeupload_recompress_yesno');
  $force_jpeg_recompression = ($force_jpeg_recompression=='yes') ? true : false;

  $compression_level = get_option('jr_resizeupload_quality');

  $max_width  = get_option('jr_resizeupload_width')==0 ? false : get_option('jr_resizeupload_width');

  $max_height = get_option('jr_resizeupload_height')==0 ? false : get_option('jr_resizeupload_height');


  $convert_png_to_jpg = get_option('jr_resizeupload_convertpng_yesno');
	$convert_png_to_jpg = ($convert_png_to_jpg=='yes') ? true : false;

  $convert_gif_to_jpg = get_option('jr_resizeupload_convertgif_yesno');
	$convert_gif_to_jpg = ($convert_gif_to_jpg=='yes') ? true : false;

  $convert_bmp_to_jpg = get_option('jr_resizeupload_convertbmp_yesno');
	$convert_bmp_to_jpg = ($convert_bmp_to_jpg=='yes') ? true : false;


  if($convert_png_to_jpg && $image_data['type'] == 'image/png' ) {
    $image_data = jr_uploadresize_convert_image( $image_data, $compression_level );
  }

  if($image_data['type'] == 'image/gif' && is_ani($image_data['file'])) {
    //animated gif, don't resize
    jr_error_log("--animated-gif-not-resized");
    return $image_data;
  }

  //---------- In with the old v1.6.2, new v1.7 (WP_Image_Editor) ------------

  if($resizing_enabled || $force_jpeg_recompression) {

		$fatal_error_reported = false;
		$valid_types = array('image/gif','image/png','image/jpeg','image/jpg');

    if(empty($image_data['file']) || empty($image_data['type'])) {
    	jr_error_log("--non-data-in-file-( ".print_r($image_data, true)." )");	
		  $fatal_error_reported = true;
    }
    else if(!in_array($image_data['type'], $valid_types)) {
    	jr_error_log("--non-image-type-uploaded-( ".$image_data['type']." )");
		  $fatal_error_reported = true;
    }

    jr_error_log("--filename-( ".$image_data['file']." )");
    $image_editor = wp_get_image_editor($image_data['file']);
    $image_type = $image_data['type'];


    if($fatal_error_reported || is_wp_error($image_editor)) {
      jr_error_log("--wp-error-reported");
    }
    else {

      $to_save = false;
      $resized = false;


      // Perform resizing if required
      if($resizing_enabled) {

        jr_error_log("--resizing-enabled");
        $sizes = $image_editor->get_size();

        if((isset($sizes['width']) && $sizes['width'] > $max_width)
          || (isset($sizes['height']) && $sizes['height'] > $max_height)) {

          $image_editor->resize($max_width, $max_height, false);
          $resized = true;
          $to_save = true;

          $sizes = $image_editor->get_size();
          jr_error_log("--new-size--".$sizes['width']."x".$sizes['height']);
        }
        else {
          jr_error_log("--no-resizing-needed");
        }
      }
      else {
        jr_error_log("--no-resizing-requested");
      }


      // Regardless of resizing, image must be saved if recompressing
      if($force_jpeg_recompression && ($image_type=='image/jpg' || $image_type=='image/jpeg')) {

        $to_save = true;
        jr_error_log("--compression-level--q-".$compression_level);
      }
      elseif(!$resized) {
        jr_error_log("--no-forced-recompression");
      }


      // Only save image if it has been resized or need recompressing
      if($to_save) {

        $image_editor->set_quality($compression_level);
        $saved_image = $image_editor->save($image_data['file']);
        jr_error_log("--image-saved");
      }
      else {
        jr_error_log("--no-changes-to-save");
      }
    }
  } // if($resizing_enabled || $force_jpeg_recompression)

  else {
    jr_error_log("--no-action-required");
  }

  jr_error_log("**-end--resize-image-upload\n");


  return $image_data;
} // function jr_uploadresize_resize($image_data){

function jr_uploadresize_convert_image( $params, $compression_level ){
  $transparent = 0;
  $image = $params['file'];

  $contents = file_get_contents( $image );
  if ( ord ( file_get_contents( $image, false, null, 25, 1 ) ) & 4 ) $transparent = 1;
  if ( stripos( $contents, 'PLTE' ) !== false && stripos( $contents, 'tRNS' ) !== false ) $transparent = 1;

  $transparent_pixel = $img = $bg = false;
  if($transparent) {
    $img = imagecreatefrompng($params['file']);
    $w = imagesx($img); // Get the width of the image
    $h = imagesy($img); // Get the height of the image
    //run through pixels until transparent pixel is found:
    for($i = 0; $i<$w; $i++) {
      for($j = 0; $j < $h; $j++) {
        $rgba = imagecolorat($img, $i, $j);
        if(($rgba & 0x7F000000) >> 24) {
          $transparent_pixel = true;
          break;
        }
      }
    }
  }

  if( !$transparent || !$transparent_pixel) {
    if(!$img) $img = imagecreatefrompng($params['file']);
    $bg = imagecreatetruecolor(imagesx($img), imagesy($img));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, 1);
    imagecopy($bg, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
    $newPath = preg_replace("/\.png$/", ".jpg", $params['file']);
    $newUrl = preg_replace("/\.png$/", ".jpg", $params['url']);
    for($i = 1; file_exists($newPath); $i++) {
      $newPath = preg_replace("/\.png$/", "-".$i.".jpg", $params['file']);
    }
    if ( imagejpeg( $bg, $newPath, $compression_level ) ){
      unlink($params['file']);
      $params['file'] = $newPath;
      $params['url'] = $newUrl;
      $params['type'] = 'image/jpeg';
    }
  }

  return $params;
}

function is_ani($filename) {
  if(!($fh = @fopen($filename, 'rb')))
    return false;
  $count = 0;
  //an animated gif contains multiple "frames", with each frame having a
  //header made up of:
  // * a static 4-byte sequence (\x00\x21\xF9\x04)
  // * 4 variable bytes
  // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

  // We read through the file til we reach the end of the file, or we've found
  // at least 2 frame headers
  $chunk = false;
  while(!feof($fh) && $count < 2) {
    //add the last 20 characters from the previous string, to make sure the searched pattern is not split.
    $chunk = ($chunk ? substr($chunk, -20) : "") . fread($fh, 1024 * 100); //read 100kb at a time
    $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
  }

  fclose($fh);
  return $count > 1;
}

/**
* Simple debug logging function. Will only output to the log file
* if 'debugging' is turned on.
*/
function jr_error_log($message) {
  global $DEBUG_LOGGER;

  if($DEBUG_LOGGER) {
    error_log(print_r($message, true));
  }
}

