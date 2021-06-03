<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }
$options = dropr_getoptions();
?> 
<div id="wpdrop-popup-wrap">
   <div id="wpdrop-popup">
	  <div id="wpdrop-popup-settings">
	  <div id="wpdpx-popup-header">
		 <h1><?php _e( 'Add from Dropbox', 'dropr' ); ?></h1>
	  </div>
	  <div class="wpdpx-section">
		 <div class="wpdpx-upload-success">
			<div class="wpdpx-inner">
			   <div class="wpdpx-wpdpx-wpdpx-advanced-options">
				  <!--- if image -->
				  <div class="wpdpx-img-placeholder" style="display: none;">
					 <img id="awsmdrop_img" src=""/>
				  </div>
				  <!-- else-->
				  <img id="awsmdrop_icon" style="display: none;" src=""/>
				  <p id="awsmdrop_filename"></p>
				  <span id="awsmdrop_filesize"></span>
			   </div>
			   <!--.wpdpx-wpdpx-wpdpx-advanced-options-->
			   <span class="v-align-dummy"></span>
			   <div class="clear"></div>
			</div>
			<!--.wpdpx-inner-->
			<div class="wpdpx-advanced-options">
			   <div class="wpdx-insert-opt">
				  <h4><?php _e( 'Insert Options', 'dropr' ); ?></h4>
				  <img src="<?php echo $this->plugin_url; ?>images/icon-dropbox.png"/>
				  <div class="clear"></div>
			   </div><!--.wpdx-insert-opt-->
			   <ul id="form-tabs">
				  <li class="active first-item" id="embeditem"><input type="radio" name="insert-type" id="insert-type-embed"  value="embed" data-item="#wpdpx-embed" checked/><label for="insert-type-embed" ><?php _e( 'Embed this', 'dropr' ); ?></label></li>
				  <li id="linkitem"><input type="radio" name="insert-type" value="link" id="insert-type-link" data-item="#wpdpx-embed-link"/><label for="insert-type-link"><?php _e( 'Insert as a link', 'dropr' ); ?></label></li>
			   </ul>
			   <div class="wpdpx-tab-item" id="wpdpx-embed" class="hidden">
				  <div id="wpdpx-embed-image" class="awsm_media hidden">
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li>
							  <label for="awsm-title"><?php _e( 'Title', 'dropr' ); ?></label>
							  <input type="text" class="input-item droprest" data-setting="title"/>
						   </li>
						   <li>
							  <label for="awsm-desc"><?php _e( 'Caption', 'dropr' ); ?></label>
							  <textarea class="input-item droprest" id="dropr_caption"></textarea>
						   </li>
						   <li>
							  <label for="awsm-alt-text"><?php _e( 'Alt text', 'dropr' ); ?></label>
							  <input type="text" class="input-item droprest" data-setting="alt"/>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li>
							  <label for="awsmdrop_alignment"><?php _e( 'Alignment', 'dropr' ); ?></label>
							  <select name="awsmdrop_alignment" class="droprest" data-setting="align">
								 <option value="alignleft"><?php _e( 'Left', 'dropr' ); ?></option>
								 <option value="aligncenter"><?php _e( 'Center', 'dropr' ); ?></option>
								 <option value="alignright"><?php _e( 'Right', 'dropr' ); ?></option>
								 <option selected="" value="alignnone"><?php _e( 'None', 'dropr' ); ?></option>
							  </select>
						   </li>
						   <li>
							  <label for="awsmdrop_link"><?php _e( 'Link', 'dropr' ); ?></label>
							  <select name="awsmdrop_link" class="awsmdrop_link">
								 <option selected="" value="file"><?php _e( 'Media File', 'dropr' ); ?></option>
								 <option value="custom"><?php _e( 'Custom URL', 'dropr' ); ?></option>
								 <option value="none"><?php _e( 'None', 'dropr' ); ?></option>
							  </select>
							  <div class="clear clear-margin"></div>
							  <label for="awsmdrop_linkUrl" class="drop-url">&nbsp;</label>
							  <input type="text" class="drop-url  link-to-custom input-item"/>
						   </li>
						   <li class="hidden">
							  <label for="awsm-size"><?php _e( 'Size', 'dropr' ); ?></label>
							 <span>W <input type="text" class="small-input awsmdrop_width droprest" data-setting="width"/></span>
							 <span>H <input type="text" class="small-input awsmdrop_height droprest" data-setting="height"/></span>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <input type="hidden" class="wp-dropbox"/>
				  </div>
				  <!--#wpdpx-embed-image-->
				  <div id="wpdpx-embed-document" class="awsm_media hidden">
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li>
							  <label for="awsm-title"><?php _e( 'Select Viewer', 'dropr' ); ?></label>
							  <?php
								 $providers = array(
									 'dropbox'   => __( 'Dropbox', 'dropr' ),
									 'google'    => __( 'Google Docs Viewer', 'dropr' ),
									 'microsoft' => __( 'Microsoft Office Online', 'dropr' ),
								 );
								 dropr_selectbuilder( 'awsmdrop_provider', $providers, 'dropbox', 'awsmdrop_provider', 'viewer' );
									?>
								  
						   </li>
						   <li>
							  <label for="awsm-size"><?php _e( 'Size', 'dropr' ); ?></label>
							  <span>W <input type="text" class="small-input" data-setting="width" value="100%"/></span>
							  <span>H <input type="text" class="small-input" data-setting="height" value="500px"/></span>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li>
							  <label for="awsmdrop_alignment"><?php _e( 'Download Link', 'dropr' ); ?></label>
							  <?php
								$downoptions = array(
									'all'    => __( 'For all users', 'dropr' ),
									'logged' => __( 'For Logged-in users', 'dropr' ),
									'none'   => __( 'No Download', 'dropr' ),
								);
								dropr_selectbuilder( 'awsmdrop_download', $downoptions, 'all', 'awsmdrop_download', 'download' );
								?>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <input type="hidden" class="wp-dropbox"/>
				  </div>
				  <!--#wpdpx-embed-doument-->
				  <div id="wpdpx-embed-audio" class="awsm_media hidden">
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li class="full-width-form-item">
							  <label id="audio_title"></label>
							  <input type="text"  id="main_audio" data-setting="" readonly/>
						   </li>
						   <li class="new_media full-width-form-item"></li>
						   <li>
							  <span class="newmediamsg"><?php _e( 'Add alternate sources for maximum HTML5 playback:', 'dropr' ); ?></span>
							  <div class="button-large dropr-support">
								 <button class="button add-media-source" data-ext=".ogg">ogg</button>
								 <button class="button add-media-source" data-ext=".mp3">mp3</button>
							  </div>
							  <div class="droprmedialist hidden">
								 <button class="button add-media-source" data-ext=".ogv">ogv</button>
								 <button class="button add-media-source" data-ext=".mp4">mp4</button>
								 <button class="button add-media-source" data-ext=".webm">webm</button>
							  </div>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li>
							  <label><?php _e( 'Playback', 'dropr' ); ?></label>
							  <div class="wpdpx-inner-label"><input type="checkbox" id="audio_playback" data-setting="autoplay" value="true"/><label for="audio_playback"><?php _e( 'Autoplay', 'dropr' ); ?></label></div>
							   <label>&nbsp;</label>
							  <div class="wpdpx-inner-label"><input type="checkbox" id="audio_loop" data-setting="loop" value="true"/><label for="audio_loop"><?php _e( 'Loop', 'dropr' ); ?></label></div>
						   </li>

						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <input type="hidden" class="wp-dropbox"/>
				  </div>
				  <!--#wpdpx-embed-audio-->
				  <div id="wpdpx-embed-video" class="awsm_media hidden">
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li class="full-width-form-item">
							  <label id="video_title"></label>
							  <input type="text" class="reset" id="main_video" data-setting="" readonly/>
						   </li>
						   <li class="new_media full-width-form-item"></li>
						   <li>
							  <span class="newmediamsg"><?php _e( 'Add alternate sources for maximum HTML5 playback:', 'dropr' ); ?></span>
							  <div class="button-large dropr-support">
								 <button class="button add-media-source" data-ext=".ogv">ogv</button>
								 <button class="button add-media-source" data-ext=".mp4">mp4</button>
								 <button class="button add-media-source" data-ext=".webm">webm</button>
							  </div>
							  <div class="droprmedialist hidden">
								 <button class="button add-media-source" data-ext=".ogv">ogv</button>
								 <button class="button add-media-source" data-ext=".mp4">mp4</button>
								 <button class="button add-media-source" data-ext=".webm">webm</button>
							  </div>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <div class="wpdpx-col-50">
						<ul class="list-unstyled">
						   <li class="hidden">
							  <label for="awsm-size"><?php _e( 'Size', 'dropr' ); ?></label>
							  <span>W <input type="text" class="small-input awsmdrop_width droprest"  data-setting="width"/></span>
							  <span>H <input type="text" class="small-input awsmdrop_height droprest" data-setting="height"/></span>
						   </li>
						   <li>
							  <label><?php _e( 'Playback', 'dropr' ); ?></label>
							  <div class="wpdpx-inner-label"><input type="checkbox" id="video_playback" data-setting="autoplay" value="true"/><label for="video_playback"><?php _e( 'Autoplay', 'dropr' ); ?></label></div>
							  <label>&nbsp;</label>
							  <div class="wpdpx-inner-label"><input type="checkbox" id="video_loop" data-setting="loop" value="true"/><label for="video_loop"><?php _e( 'Loop', 'dropr' ); ?></label></div>
						   </li>
						</ul>
					 </div>
					 <!--.wpdpx-col-50-->
					 <input type="hidden" class="wp-dropbox"/>
				  </div>
				  <!--#wpdpx-embed-video-->
			   </div>
			   <!--#wpdpx-tab-item-->
			   <div class="wpdpx-tab-item hidden" id="wpdpx-embed-link">
				  <div class="wpdpx-col-50">
					 <ul class="list-unstyled">
						<li>
						   <label for="awsmdrop_link"><?php _e( 'Link', 'dropr' ); ?></label>
						   <select class="link-url">
							  <option selected="" value="direct" data-suffix="raw=1"><?php _e( 'Direct', 'dropr' ); ?></option>
							  <option value="download" data-suffix="dl=1"><?php _e( 'Force Download', 'dropr' ); ?></option>
							  <option value="preview" data-suffix="dl=0"><?php _e( 'Dropbox Preview', 'dropr' ); ?></option>
						   </select>
						   <div class="clear clear-margin"></div>
						   <label for="awsm-linkUrl">&nbsp;</label>
						   <input type="checkbox" data-setting='target' id="awsm-new-tab" value="_blank"><label for="awsm-new-tab" class="no-style"/><?php _e( 'Open in new tab', 'dropr' ); ?></label>
						</li>
					 </ul>
				  </div>
				  <!--.wpdpx-col-50-->
				  <div class="wpdpx-col-50">
					 <ul class="list-unstyled">
						<li>
						   <label for="awsm-link-text" ><?php _e( 'Link text', 'dropr' ); ?></label>
						   <input type="text" class="input-item awsm-link" value="<?php echo $options['btntxt']; ?>" />
						</li>
						<li>
						   <label for="awsm-link-style"><?php _e( 'Link Style', 'dropr' ); ?></label>
						   <select name="awsm-link-style" data-setting="class">
							  <option value="plain"><?php _e( 'Plain Link', 'dropr' ); ?></option>
							  <option value="wpdropbox-button"><?php _e( 'Button', 'dropr' ); ?></option>
						   </select>
						</li>
					 </ul>
				  </div>
				  <!--.wpdpx-col-50-->
				  <input type="hidden" class="wp-dropbox"/>
			   </div>
			   <!--#wpdpx-embed-link-->
			</div>
			<!--.wpdpx-advanced-options-->
			<div class="wpdx-download-opt">
			   <div class="wpdx-download-opt-inner">
				  <h3><?php _e( 'Where do you want to store the file?', 'dropr' ); ?></h3>
				  <ul>
					 <li>
						<a href="#">
						   <img src="<?php echo $this->plugin_url; ?>images/icon-dropbox.png"/>
						   <p><?php _e( 'Keep in Dropbox', 'dropr' ); ?></p>
						</a>
					 </li>
					 <li>
						<a href="#">
						   <img src="<?php echo $this->plugin_url; ?>images/icon-media.png"/>
						   <p><?php _e( 'Copy to Media Library', 'dropr' ); ?></p>
						</a>
					 </li>
				  </ul>
				  <p>You can choose your default storage option in <a href="#">setttings page</a></p>
			   </div><!--.wpdx-download-opt-inner-->
			   <span class="v-align-dummy"></span>
			</div><!--.wpdx-download-opt-->
		 </div>
		 <!--.wpdpx-upload-success-->
		 <div id="wpdpx-drop-preload"></div>
		 <!--#wpdpx-drop-preload-->
	  </div>
	  <!--.section-->
	  <div class="wpdpx-action-panel">
		 <div style="float: right">
			<input type="button" id="wpdpx-drop-insert" data-embedtype=""  name="insert" data-txt="<?php _e( 'Insert', 'dropr' ); ?>" data-loading="<?php _e( 'Loading...', 'dropr' ); ?>" class="wpdpx-drop-btn button button-primary button-medium" value="<?php _e( 'Insert', 'dropr' ); ?>"/>
		 </div>
		 <div style="float: left">
			<input type="button" name="cancel"  class="wpdpx-drop-btn button cancel_embed button-medium" value="<?php _e( 'Cancel', 'dropr' ); ?>" />
		 </div>
		 <div class="clear"></div>
		   </div><!--.wpdpx-action-panel-->
	  </div><!-- wpdrop-popup-settings-->
	  <div class="wpdpx-api-key-main hidden" id="wpdrop-nokey">
			<img class="pull-left" src="<?php echo $this->plugin_url; ?>images/key.jpg">
			<div class="wpdpx-api-key-content">
			   <h3><?php _e( 'API Key not found!', 'dropr' ); ?></h3>
			   <p><?php _e( 'The plugin needs you to submit your Dropbox API key. Just only once! ', 'dropr' ); ?><a href="https://www.dropbox.com/developers/apps/create?app_type_checked=dropins" target="_blank"><?php _e( 'Get your key', 'dropr' ); ?></a> <?php _e( 'and paste it in', 'dropr' ); ?> <a href="options-general.php?page=<?php echo $this->settings_slug; ?>"><?php _e( 'settings page', 'dropr' ); ?></a></p>
			</div><!-- .wpdpx-api-key-content-->
		 
	  </div>
	  <!-- .wpdpx-api-key-main--> 
	  <div class="overlay-loading">
		 <div class="overlay-main">
			<img src="<?php echo $this->plugin_url; ?>images/loading-spin.svg"/>
			<p><?php _e( 'Copying file to website', 'dropr' ); ?></p>
		 </div>
		 <span class="dummy"></span>
	  </div><!--.overlay-loading-->
   </div>
   <!--#wpdrop-popup-->
</div>
<!--#wpdrop-popup-wrap-->
