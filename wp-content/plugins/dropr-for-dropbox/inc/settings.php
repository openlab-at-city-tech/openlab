<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; }
$deafult_options = dropr_defaults();
$options         = dropr_getoptions();
$generic_options = dropr_get_generic_options();
?>
<div class="wrap clearfix">
   <h2 class="wpdbx-title"><?php _e( 'Dropr - Dropbox Plugin by AWSM.in', 'dropr' ); ?></h2>
   <div class="wpdbx-left-wrap">
	  <div class="wpdpx-form-item-block">
		 <img class="fl-left" src="<?php echo $this->plugin_url; ?>images/key.jpg">
		 <div class="wpdpx-api-key-main">
			<h3><?php _e( 'Your Dropbox API key', 'dropr' ); ?></h3>
			<form action="options.php" method="post" >
			<?php settings_fields( 'wp-dropbox-api' ); ?>
			   <p class="clearfix">
				  <input type="text" class="fl-left api-key" name="wpdropbox-apikey" value="<?php echo get_option( 'wpdropbox-apikey' ); ?>" placeholder="<?php _e( 'Enter Key', 'dropr' ); ?>" />
				  <input type="submit" value="SUBMIT" class="button fl-left button-primary" id="submit" name="submit">
			   </p>
			   <a href="https://www.dropbox.com/developers/apps/create?app_type_checked=dropins" target="_blank"><?php _e( 'Get my key', 'dropr' ); ?></a>  |  <a href="http://awsm.in/dropr-documentation/#cloudapi" target="_blank"><?php _e( 'How to get it?', 'dropr' ); ?></a>
			</form>
		 </div>
		 <!-- .wpdpx-api-key-main-->
	  </div>
	  <!-- .wpdpx-form-item-block-->
	  <form action="options.php" method="post" class="wpdpx-settings-form">
	  <?php settings_fields( 'wp-dropbox-settings' ); ?>
		  <div class="wpdpx-generic-form-item-block wpdpx-form-item-block">
			<h3><?php esc_html_e( 'General Settings', 'dropr' ); ?></h3>
			<div class="wpdpx-generic-settings-main clearfix">
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Media Library', 'dropr' ); ?></label></th>
							<td>
								<ul class="wpdbx-list-inline wpdbx-form-radio-fields" data-default="dropbox">
									<li>
										<label for="wpdx-media-lbrary-storage-dropbox">
											<input type="radio" name="dropr-generic-settings[media_library_storage]" id="wpdx-media-lbrary-storage-dropbox" value="dropbox"<?php checked( $generic_options['media_library_storage'], 'dropbox' ); ?> /><?php esc_html_e( 'Keep the file in Dropbox', 'dropr' ); ?>
										</label>
									</li>
									<li>
										<label for="wpdx-media-lbrary-storage-local">
											<input type="radio" name="dropr-generic-settings[media_library_storage]" id="wpdx-media-lbrary-storage-local" value="local"<?php checked( $generic_options['media_library_storage'], 'local' ); ?> /><?php esc_html_e( 'Copy to Media Library', 'dropr' ); ?>
										</label>
									</li>
								</ul>
							</td>
						</tr>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Featured Image', 'dropr' ); ?></label></th>
							<td>
								<ul class="wpdbx-list-inline wpdbx-form-radio-fields" data-default="local">
									<li>
										<label for="wpdx-featured-image-storage-dropbox">
											<input type="radio" name="dropr-generic-settings[featured_image_storage]" id="wpdx-featured-image-storage-dropbox" value="dropbox"<?php checked( $generic_options['featured_image_storage'], 'dropbox' ); ?> /><?php esc_html_e( 'Keep the file in Dropbox', 'dropr' ); ?>
										</label>
									</li>
									<li>
										<label for="wpdx-featured-image-storage-local">
											<input type="radio" name="dropr-generic-settings[featured_image_storage]" id="wpdx-featured-image-storage-local" value="local"<?php checked( $generic_options['featured_image_storage'], 'local' ); ?> /><?php esc_html_e( 'Copy to Media Library', 'dropr' ); ?>
										</label>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		  
		 <div class="wpdpx-form-item-block">
			<h3><?php _e( 'Default button style', 'dropr' ); ?></h3>
			<div class="settings-main clearfix">
			   <div class="button-preview">
				  <span id="wpdpx-btn" title="<?php echo $options['btntxt']; ?>"><?php echo $options['btntxt']; ?></span>
			   </div>
			   <!--.button-preview-->
			   <div class="wpdpx-set-values">
				  <ul class="wpdpx-row clearfix">
					 <li class="wpdpx-col-sm-4">
						<div class="inner">
						   <div class="wpdpx-form-item">
							  <label for="wpdx-btn-text"><?php _e( 'Default Text', 'dropr' ); ?></label>
							  <input type="text" name="dropr-settings[btntxt]" class="wpdx-btn-text" id="wpdx-btn-text" data-default="<?php echo $deafult_options['btntxt']; ?>" value="<?php echo $options['btntxt']; ?>" />
						   </div>
						   <div class="wpdpx-form-item">
							  <label for="wpdx-btn-size"><?php _e( 'Font size', 'dropr' ); ?></label>
							  <input type="hidden" name="dropr-settings[fontsize]" data-style="font-size"  data-stylea="px" class="single-slider" data-from="12" data-to="30"  id="wpdx-btn-size"  data-default="<?php echo $deafult_options['fontsize']; ?>" value="<?php echo $options['fontsize']; ?>"/>
						   </div>
						   <div class="wpdpx-form-item clearfix">
							  <label for="wpdx-btn-color"><?php _e( 'Text color', 'dropr' ); ?></label>
							  <div class="color-value">
								 <input type="text" name="dropr-settings[btntxtcolor]" data-style="color" id="wpdx-btn-color" data-default="<?php echo $deafult_options['btntxtcolor']; ?>" value="<?php echo $options['btntxtcolor']; ?>" readonly>
							  </div>
							  <div class="colorSelector" data-color="<?php echo $options['btntxtcolor']; ?>" data-default="<?php echo $deafult_options['btntxtcolor']; ?>" data-txt="wpdx-btn-color">
								 <div style="background-color: <?php echo $options['btntxtcolor']; ?>"></div>
							  </div>
						   </div>
						</div>
					 </li>
					 <li class="wpdpx-col-sm-4">
						<div class="inner">
						   <div class="form-main clearfix">
							  <div class="wpdpx-form-item">
								 <label for="wpdx-btn-padd-v"><?php _e( 'Vertical padding', 'dropr' ); ?></label>
								 <input type="hidden" name="dropr-settings[vpadding]" data-style="padding-top" data-style1="padding-bottom" data-stylea="px" class="single-slider" data-from="0" data-to="30" data-default="<?php echo $deafult_options['vpadding']; ?>" value="<?php echo $options['vpadding']; ?>" />
							  </div>
							  <div class="wpdpx-form-item">
								 <label for="wpdx-btn-padd-h"><?php _e( 'Horizontal padding', 'dropr' ); ?></label>
								 <input type="hidden" name="dropr-settings[hpadding]" data-style="padding-right" data-style1="padding-left" data-stylea="px" class="single-slider" data-from="0" data-to="50"  
								 data-default="<?php echo $deafult_options['hpadding']; ?>"  value="<?php echo $options['hpadding']; ?>"/>
							  </div>
							  <div class="wpdpx-form-item clearfix">
								 <label for="wpdx-btn-bg"><?php _e( 'Background color', 'dropr' ); ?></label>
								 <div class="color-value">
									<input type="text" name="dropr-settings[bgcolor]" data-style="background" id="wpdx-btn-bg" data-default="<?php echo $deafult_options['bgcolor']; ?>" value="<?php echo $options['bgcolor']; ?>" readonly>
								 </div>
								 <div class="colorSelector" data-color="<?php echo $options['bgcolor']; ?>" data-default="<?php echo $deafult_options['bgcolor']; ?>" data-txt="wpdx-btn-bg">
									<div style="background-color: <?php echo $options['bgcolor']; ?>"></div>
								 </div>
							  </div>
						   </div>
						</div>
					 </li>
					 <li class="wpdpx-col-sm-4">
						<div class="inner">
						   <div class="form-main clearfix">
							  <div class="wpdpx-form-item">
								 <label for="wpdx-btn-border-t"><?php _e( 'Thickness', 'dropr' ); ?></label>
								 <input type="hidden" name="dropr-settings[brthick]" data-style="border" class="single-slider" data-from="0" data-to="15"  id="wpdx-btn-padd-t" data-default="<?php echo $deafult_options['brthick']; ?>" value="<?php echo $options['brthick']; ?>"  data-stylea="px solid"/>
							  </div>
							  <div class="wpdpx-form-item">
								 <label for="wpdx-btn-border-r"><?php _e( 'Radius', 'dropr' ); ?></label>
								 <input type="hidden" name="dropr-settings[brradius]" data-style="border-radius" class="single-slider" data-from="0" data-to="50"  id="wpdx-btn-padd-h"  data-stylea="px" data-default="<?php echo $deafult_options['brradius']; ?>" value="<?php echo $options['brradius']; ?>"/>
							  </div>
							  <div class="wpdpx-form-item clearfix">
								 <label for=""><?php _e( 'Border Color', 'dropr' ); ?></label>
								 <div class="color-value">
									<input type="text" name="dropr-settings[brcolor]" data-style="border-color" id="wpdx-btn-br" data-default="<?php echo $deafult_options['brcolor']; ?>" value="<?php echo $options['brcolor']; ?>" readonly>
								 </div>
								 <div class="colorSelector" data-color="<?php echo $options['brcolor']; ?>" data-default="<?php echo $deafult_options['brcolor']; ?>" data-txt="wpdx-btn-br">
									<div style="background-color: <?php echo $options['brcolor']; ?>"></div>
								 </div>
							  </div>
						   </div>
						</div>
					 </li>
				  </ul>
			   </div>
			   <!--.wpdpx-set-values-->
			</div>
			<!-- .settings-main-->
		 </div>
		 <!-- .wpdpx-form-item-block-->
		 <div class="wpdpx-form-submit-section clearfix">
			<a href="#" class="btn-transparent fl-left" id="dropr-reset"><?php _e( 'Reset to default', 'dropr' ); ?></a>
			<?php submit_button(); ?>
		 </div>
		 <!-- .wpdpx-form-item-block-->
	  </form>
	  <!-- .wpdpx-settings-form-->
   </div>
   <!-- .wpdbx-left-wrap -->
   <div class="wpdbx-right-wrap">
	  <div class="wpdbx-right-inner">
		 <a href="http://awsm.in" target="_blank" title="<?php _e( 'AWSM Innovations', 'dropr' ); ?>"><img src="http://awsm.in/innovations/ead/logo.png" alt="AWSM!"></a>
		 <div class="author-info">
			<?php
				/* translators: %1$s: Plugin Author name, %2$s: Author URL */
				printf( __( 'This plugin is developed <br/>by <a href="%2$s" target="_blank" title="%1$s">%1$s.</a>', 'dropr' ), __( 'AWSM Innovations', 'dropr' ), 'https://awsm.in' );
			?>
		 </div>
		 <!-- .author-info -->
		 <ul class="wpdpx-social">
			<li><a href="https://www.facebook.com/awsminnovations" target="_blank" title="<?php _e( 'AWSM Innovations', 'dropr' ); ?>"><span class="wpdpx-icon wpdpx-icon-facebook"><?php _e( 'Facebook', 'dropr' ); ?></span></a></li>
			<li><a href="https://twitter.com/awsmin" target="_blank" title="<?php _e( 'AWSM Innovations', 'dropr' ); ?>"><span class="wpdpx-icon wpdpx-icon-twitter"><?php _e( 'Twitter', 'dropr' ); ?></span></a></li>
			<li><a href="https://github.com/awsmin" target="_blank" title="<?php _e( 'AWSM Innovations', 'dropr' ); ?>"><span class="wpdpx-icon wpdpx-icon-github"><?php _e( 'Github', 'dropr' ); ?></span></a></li>
		 </ul>
	  </div>
	  <!--.wpdbx-right-inner-->
   </div>
   <!-- .wpdbx-right-wrap -->
</div>
<!-- .wrap -->
<?php
extract( $options );
echo '<style type="text/css">
   .wpdropbox-button { 
      background: ' . $bgcolor . ';
      color: ' . $btntxtcolor . ';
      padding: ' . $vpadding . 'px ' . $hpadding . 'px;
      font-size: ' . $fontsize . 'px;
      border: ' . $brthick . 'px solid  ' . $brcolor . ';
      -webkit-border-radius: ' . $brradius . 'px;
      border-radius: ' . $brradius . 'px;
      display: inline-block;
      text-decoration: none;
   }    
</style>';
?>
