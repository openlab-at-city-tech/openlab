<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(isset($_POST['slider-id'])) {
	if (!isset( $_POST['recover_slider_nonce'] ) || ! wp_verify_nonce( $_POST['recover_slider_nonce'], 'recover_slider' ) ) {
		print 'Sorry, your nonce did not verify.';
		exit;
	} else {
		$slider_id = sanitize_text_field($_POST['slider-id']);
		
		$uris_updated_sliders = get_option('uris_updated_slider_ids',  array());
		if(is_array($uris_updated_sliders)){
			if(count($uris_updated_sliders)) {
				if(in_array($slider_id, $uris_updated_sliders)) {
					echo "<p style='text-align:center; color:red; font-weight:bolder;'>ERROR!!! Entered Slider ID '$slider_id' is already recovered.</p>";
					$slider_id = 0;
				}
			}
		}
		
		if($slider_id) {
			
			// ### Part - 1: Update Slides Details Start ###
			global $wpdb;
			$post_table_name = $wpdb->prefix. "posts";
			$slides_id_array = array();
			
			// get the setting of the slider by id
			$URIS_Slides_Settings = get_post_meta( $slider_id, 'ris_all_photos_details', true);
			$URIS_Slides_Settings_Array = unserialize(base64_decode(get_post_meta( $slider_id, 'ris_all_photos_details', true)));
			$URIS_Slider_Slide_Count_Array = count(unserialize(base64_decode(get_post_meta( $slider_id, 'ris_all_photos_details', true ))));
			
			/* echo "<pre>";
			print_r($URIS_Slides_Settings_Array);
			echo "</pre>"; */
			
			if(is_array($URIS_Slides_Settings_Array)){
				if(count($URIS_Slides_Settings_Array)){
				
					foreach($URIS_Slides_Settings_Array as $URIS_Slide_Setting){
						// get each slide attachment id by slide URL
						$slide_url = $URIS_Slide_Setting['rpgp_image_url'];
						//echo "<br />";
						if(count($attachment_id = $wpdb->get_col($wpdb->prepare("SELECT `id` FROM `$post_table_name` WHERE `guid` LIKE '%s'", $slide_url)))) {
							$slide_id = $attachment_id[0];
							//echo "<br />";
							// set old slide details
							$attachment = get_post( $slide_id ); // get all slide details
							$slide_title = $URIS_Slide_Setting['rpgp_image_label']; // attachment title
							$slide_desc = $URIS_Slide_Setting['rpgp_image_desc'];
							$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
							$slides_id_array[] = array('rpgp_image_id' => $slide_id);
							
							// update attachment image title and description
							$attachment_details = array(
								'ID' => sanitize_text_field($slide_id),
								'post_title' => sanitize_text_field($slide_title),
								'post_content' => sanitize_text_field($slide_desc)
							);
							wp_update_post( $attachment_details );
							
							// update attachment alt text
							update_post_meta( $slide_id, '_wp_attachment_image_alt', sanitize_text_field( $slide_alt ) );
						}
						update_post_meta($slider_id, 'ris_all_photos_details', $slides_id_array);
					}
					
					
					// ### Part - 2: Update Slider Configuration Settings Start ###
						
						// get old saved setting
						$WRIS_Gallery_Settings_Key = "WRIS_Gallery_Settings_".$slider_id;
						$WRIS_Settings_Array = unserialize(get_post_meta( $slider_id, $WRIS_Gallery_Settings_Key, true));
						//update old settings
						update_post_meta($slider_id, $WRIS_Gallery_Settings_Key, $WRIS_Settings_Array);
					
					// ### Part -2: Update Slider Configuration Settings End ###
					
					
					// ### Part - 3: Save A Flag For This Slider Start ###
						// get previously updated slider
						$uris_updated_sliders = array();
						$uris_updated_sliders = get_option('uris_updated_slider_ids', 0);
						if(is_array($uris_updated_sliders)){
							if(count($uris_updated_sliders)) {
								array_push($uris_updated_sliders, $slider_id);
							}
						} else {
							$uris_updated_sliders = array($slider_id);
						}
						update_option('uris_updated_slider_ids', $uris_updated_sliders);
						
					// ### Part - 3: Save A Flag For This Slider End ###
				} // is count
			} // is array
			
			// ### Part - 1: Update Slides Details End ###
		}
	}
}
?>
<div style="border: 3px dashed #23282D; padding: 50px; margin:20px;">
	<h2 style="color:blue;"> Recover Old Slider Post</h2><hr />
	<p>To recover old slider post you need to provide <strong>Slider ID</strong> in below field and hit the <strong>Recover Slider</strong> button.</p>
	<p>If your slider shortcode is <strong>[URIS id=101]</strong> then your <strong>Slider ID</strong> is <strong>101</strong>.</p>
	<form action="" method="post" action="recover-slider">
		<?php wp_nonce_field( 'recover_slider', 'recover_slider_nonce' ); ?>
		<p><input type="number" value="" id="slider-id" name="slider-id" placeholder="Enter Slider ID" required></p>
		<p>
			<input type="submit" id="recover-slider" name="recover-slider" value="Recover Slider" class="button button-primary button-hero">
		</p>
	</form>
	<?php
	$uris_updated_sliders = get_option('uris_updated_slider_ids',  array());
	if(is_array($uris_updated_sliders)){
		if(count($uris_updated_sliders)) {
			echo "<div style='text-align:left;padding-top:10px;'>";
			echo "<h3 style='color:blue;'><strong>Previously Recovered Slider IDs are:</strong></h3><hr /><p>";
			sort($uris_updated_sliders);
			foreach($uris_updated_sliders as $id){
				echo $id;
				echo ", ";
			}
			echo '</p>';
			echo "</div>";
		}
	}
	//print_r($uris_updated_sliders);
	echo "<div style='text-align:left;padding-top:10px;'>";
	echo "<h3 style='color:blue;'>Important Notes</h3><hr />";
	echo "<p>1. <strong>Reupdate:</strong> Once you updated a Slider ID, don't re-update, It will break the slider or show errors.</p>";
	echo "<p>2. <strong>Recoverable:</strong> Slider created in older versions of the plugin can be recoverable. Like Slider created in Version 3.3.9 or later.</p>";
	echo "<p>3. <strong>Non Recoverable:</strong> Slider created after major update version 3.3.10 of the plugin can't be recoverable. Like Slider created in Version 3.3.10 or latest.</p>";
	echo "<p>4. <strong>Non Recoverable:</strong> If you already updated slider by hitting update post button in latest plugin version. <a href='http://prntscr.com/qikp8q' target='_blank'>http://prntscr.com/qikp8q</a></p>";
	echo "</div>";
	?>
</div>