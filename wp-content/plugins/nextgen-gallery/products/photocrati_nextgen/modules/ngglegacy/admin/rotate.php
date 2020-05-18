<?php
/**

Custom thumbnail for NGG
Author : Simone Fumagalli | simone@iliveinperego.com
More info and update : http://www.iliveinperego.com/rotate_for_ngg/

Credits:
 NextGen Gallery : Alex Rabe | http://alexrabe.boelinger.com/wordpress-plugins/nextgen-gallery/
 
**/

require_once( dirname( dirname(__FILE__) ) . '/ngg-config.php');
require_once( NGGALLERY_ABSPATH . '/lib/image.php' );

if ( !is_user_logged_in() )
	die(__('Cheatin&#8217; uh?'));
	
if ( !current_user_can('NextGEN Manage gallery') ) 
	die(__('Cheatin&#8217; uh?'));

global $wpdb;

$id = (int) $_GET['id'];

// let's get the image data
$picture = nggdb::find_image($id);

include_once( nggGallery::graphic_library() );

// Generate a url to a preview image
$storage       = C_Gallery_Storage::get_instance();
$preview_image = $storage->get_image_url($id, 'full');
?>

<script type='text/javascript'>
	var selectedImage = "thumb<?php echo $id ?>";
	
	function rotateImage() {
		
		var rotate_angle = jQuery('input[name=ra]:checked').val();
		
		jQuery.ajax({
		  url: ajaxurl,
		  type : "POST",
		  data:  {action: 'rotateImage', id: <?php echo $id ?>, ra: rotate_angle},
		  cache: false,
		  success: function (msg) { 
				var d = new Date();
				newUrl = jQuery("#"+selectedImage).attr("src") + "?" + d.getTime();
				jQuery("#"+selectedImage).attr("src" , newUrl);
					
		  	showMessage('<?php _e('Image rotated', 'nggallery'); ?>') 
		  },
		  error: function (msg, status, errorThrown) { showMessage('<?php _e('Error rotating thumbnail', 'nggallery'); ?>') }
		});

	}
	
	function showMessage(message) {
		jQuery('#thumbMsg').html(message);
		jQuery('#thumbMsg').css({'display':'block'});
		setTimeout(function(){ jQuery('#thumbMsg').fadeOut('slow'); }, 1500);
		
		var d = new Date();
		newUrl = jQuery("#imageToEdit").attr("src") + "?" + d.getTime();

		jQuery("#imageToEdit").attr("src" , newUrl);
							
	}
</script>

<table align="center">
	<tr>
		<td valign="middle" align="center" id="ngg-overlay-dialog-main">
			<img src="<?php echo nextgen_esc_url( $preview_image ); ?>"
                 alt=""
                 id="imageToEdit"
                 style="max-width: 450px;
                        max-height: 350px;"/>
		</td>
		<td>
			<input type="radio" name="ra" value="cw" /><?php esc_html_e('90&deg; clockwise', 'nggallery'); ?><br />
			<input type="radio" name="ra" value="ccw" /><?php esc_html_e('90&deg; counter-clockwise', 'nggallery'); ?><br />
			<input type="radio" name="ra" value="fv" /><?php esc_html_e('Flip vertically', 'nggallery'); ?><br />
			<input type="radio" name="ra" value="fh" /><?php esc_html_e('Flip horizontally', 'nggallery'); ?>
		</td>		
	</tr>
</table>
<div id="ngg-overlay-dialog-bottom">
	<input type="button" name="update" value="<?php esc_attr_e('Update', 'nggallery'); ?>" onclick="rotateImage()" class="button-primary" />
	<div id="thumbMsg"></div>
</div>


