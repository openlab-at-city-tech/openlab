<?php

if ( ! defined( 'ABSPATH' ) )
	exit;
	
class WonderPlugin_Gallery_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config, $imagesize, $thumbnailsize ) {
		
		?>
		
		<?php 
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		$config = str_replace("&quot;", "", $config);
		?>
		
		<h3><?php _e( 'General Options', 'wonderplugin_gallery' ); ?></h3>
		
		<div id="wonderplugin-gallery-id" style="display:none;"><?php echo $id; ?></div>
		<div id="wonderplugin-gallery-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wonderplugin-gallery-pluginfolder" style="display:none;"><?php echo WONDERPLUGIN_GALLERY_URL; ?></div>
		<div id="wonderplugin-gallery-jsfolder" style="display:none;"><?php echo WONDERPLUGIN_GALLERY_URL . 'engine/'; ?></div>
		<div id="wonderplugin-gallery-viewadminurl" style="display:none;"><?php echo admin_url('admin.php?page=wonderplugin_gallery_show_item'); ?></div>		
		<div id="wonderplugin-gallery-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
		<div id="wonderplugin-gallery-imagesize" style="display:none;"><?php echo $imagesize; ?></div>
		<div id="wonderplugin-gallery-thumbnailsize" style="display:none;"><?php echo $thumbnailsize; ?></div>
		<div id="wonderplugin-gallery-ajaxnonce" style="display:none;"><?php echo wp_create_nonce( 'wonderplugin-gallery-ajaxnonce' ); ?></div>
		<div id="wonderplugin-gallery-saveformnonce" style="display:none;"><?php wp_nonce_field('wonderplugin-gallery', 'wonderplugin-gallery-saveform'); ?></div>
		<div id="wonderplugin-gallery-servertime" style="display:none;"><?php echo current_time( 'Y-m-d H:i:s' ); ?></div>

		<?php 
			$cats = get_categories(array(
				'hide_empty' => false,
			));
			$catlist = array();
			foreach ( $cats as $cat )
			{
				$catlist[] = array(
						'ID' => $cat->cat_ID,
						'cat_name' => $cat ->cat_name
				);
			}
		?>
		<div id="wonderplugin-gallery-catlist" style="display:none;"><?php echo json_encode($catlist); ?></div>
		
		<?php 
			$folderlist = wonderplugin_dirtoarray(get_home_path(), false);
		?>
		<div id="wonderplugin-gallery-folderlist" style="display:none;"><?php echo json_encode($folderlist); ?></div>
		<div id="wonderplugin-gallery-folderseperator" style="display:none;"><?php echo DIRECTORY_SEPARATOR; ?></div>
		
		<?php 
			$langlist = array();
			$default_lang = '';
			$currentlang = '';
			if ( get_option( 'wonderplugin_gallery_supportmultilingual', 1 ) == 1 )
			{
				if (class_exists('SitePress'))
				{
					$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc');
					if ( !empty($languages) )
					{
						$default_lang = apply_filters('wpml_default_language', NULL );
						$currentlang = apply_filters('wpml_current_language', NULL );
						foreach($languages as $key => $lang)
						{
							$lang_item = array(
									'code' => $lang['code'],
									'translated_name' => $lang['translated_name']
							);
							if ($key == $default_lang)
								array_unshift($langlist, $lang_item);
							else
								array_push($langlist, $lang_item);
						}				
					}
				}
			}
		?>
		<div id="wonderplugin-gallery-langlist" style="display:none;"><?php echo json_encode($langlist); ?></div>
		<div id="wonderplugin-gallery-defaultlang" style="display:none;"><?php echo $default_lang; ?></div>
		<div id="wonderplugin-gallery-currentlang" style="display:none;"><?php echo $currentlang; ?></div>
		<?php
			$initd_option = 'wonderplugin_gallery_initd';
			$initd = get_option($initd_option);
			if ($initd == false)
			{
				update_option($initd_option, time());
				$initd = time();
			}	
		?>
		<div id="<?php echo $initd_option; ?>" style="display:none;"><?php echo $initd; ?></div>

		<div style="margin:0 12px;">
		<table class="wonderplugin-form-table">
			<tr>
				<td>
					<label><?php _e( 'Width', 'wonderplugin_gallery' ); ?> / <?php _e( 'Height', 'wonderplugin_gallery' ); ?> (px) : </label>
				</td>
				<td>
					<input name="wonderplugin-gallery-width" type="number" id="wonderplugin-gallery-width" value="960" style="width:80px;" /> / <input name="wonderplugin-gallery-height" type="number" id="wonderplugin-gallery-height" value="540" style="width:80px;" />
				</td>
				<td>
					<label><?php _e( 'Name', 'wonderplugin_gallery' ); ?> : </label>
					<input name="wonderplugin-gallery-name" type="text" id="wonderplugin-gallery-name" value="My Gallery" class="regular-text" />
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Categories', 'wonderplugin_gallery' ); ?>
					<?php
						if (!empty($langlist))
						{
					?>
							<div id="wonderplugin-gallery-category-langs" style="display:none;"></div>
							<p style="font-weight:normal;"><input type="button" class="button button-primary" id="wonderplugin-gallery-category-multilingual" value="Multilingual Translation"></p>
					<?php
						}
					?>
				</td>
				<td colspan="2">				
				<div id="wonderplugin-gallery-categorylist"></div>
				<p><i>* You can drag and drop a category to change the order. </i></p></td>
			</tr>
		</table>
		</div>
		
		<h3><?php _e( 'Gallery Editor', 'wonderplugin_gallery' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wonderplugin-tab-buttons" id="wonderplugin-gallery-toolbar">
			<li class="wonderplugin-tab-button step1 wonderplugin-tab-buttons-selected"><span class="wonderplugin-icon">1</span><?php _e( 'Images & Videos', 'wonderplugin_gallery' ); ?></li>
			<li class="wonderplugin-tab-button step2"><span class="wonderplugin-icon">2</span><?php _e( 'Skins', 'wonderplugin_gallery' ); ?></li>
			<li class="wonderplugin-tab-button step3"><span class="wonderplugin-icon">3</span><?php _e( 'Options', 'wonderplugin_gallery' ); ?></li>
			<li class="wonderplugin-tab-button step4"><span class="wonderplugin-icon">4</span><?php _e( 'Preview', 'wonderplugin_gallery' ); ?></li>
			<li class="laststep"><input class="button button-primary button-hero" type="button" value="<?php _e( 'Save & Publish', 'wonderplugin_gallery' ); ?>"></input></li>
		</ul>
				
		<ul class="wonderplugin-tabs" id="wonderplugin-gallery-tabs">
			<li class="wonderplugin-tab wonderplugin-tab-selected">	
			
				<div class="wonderplugin-toolbar">	
					<div class="wonderplugin-toolbar-buttons">
					<input type="button" class="button" id="wonderplugin-add-image" value="<?php _e( 'Add Image', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-video" value="<?php _e( 'Add Video', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-youtube" value="<?php _e( 'Add YouTube', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-youtube-playlist" value="<?php _e( 'Add YouTube Playlist', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-vimeo" value="<?php _e( 'Add Vimeo', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-dailymotion" value="<?php _e( 'Add Dailymotion', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-iframevideo" value="<?php _e( 'Add Iframe Video', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-posts" value="<?php _e( 'Add WordPress Posts', 'wonderplugin_gallery' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-folder" value="<?php _e( 'Import Folder', 'wonderplugin_gallery' ); ?>" />
					</div>
					<div class="wonderplugin-toolbar-options">
					<label style="margin:4px;vertical-align:bottom;">Category: <select name='wonderplugin-gallery-selectcategorylist' id='wonderplugin-gallery-selectcategorylist'>
						<option value="all">Show All</option>
					</select></label>
					<label style="margin:4px;vertical-align:bottom;"><input type="button" class="button" id="wonderplugin-deleteall" value="<?php _e( 'Delete All', 'wonderplugin_gallery' ); ?>" /></label>
					<label style="margin:4px;vertical-align:bottom;"><input type="button" class="button" id="wonderplugin-reverselist" value="<?php _e( 'Reverse List', 'wonderplugin_gallery' ); ?>" /></label>
					<label style="margin:4px;vertical-align:bottom;"><input type="button" class="button" id="wonderplugin-updatevimeothumb" value="<?php _e( 'Update Vimeo Thumbs', 'wonderplugin_gallery' ); ?>" /></label>
					<label style="margin:4px;vertical-align:bottom;"><input type='checkbox' id='wonderplugin-newestfirst' value='' /> Add new item to the beginning</label>
					</div>
				</div>
        		
        		<ul class="wonderplugin-table" id="wonderplugin-gallery-media-table">
				</ul>
				<div class="wonderplugin-gallery-media-table-help"><span class="dashicons dashicons-editor-help"></span>Click Above Buttons to Add Images, Videos or Posts</div>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wonderplugin-tab">
				<form>
					<fieldset>
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="gallery" selected> Gallery <br /><img class="selected" style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-gallery.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="mediapage"> Media page <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-mediapage.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="light"> Light <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-light.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="horizontal"> Horizontal <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-horizontal.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="gallerywithtext"> Gallery With Text <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-gallerywithtext.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="gallerywithtopthumbs"> Gallery With Top Thumbnails <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-gallerywithtopthumbs.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="gallerywithtextbottom"> Gallery With Bottom Text <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-gallerywithtextbottom.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="vertical"> Vertical <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-vertical.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="verticallight"> Vertical Light<br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-verticallight.jpg" /></label>
						</div>

						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="verticalwithleft"> Vertical with Left Carousel <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-verticalwithleft.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="verticallightwithleft"> Vertical Light with Left Carousel<br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-verticallightwithleft.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="showcase"> Showcase <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-showcase.jpg" /></label>
						</div>
						
						<div class="wonderplugin-tab-skin">
						<label><input type="radio" name="wonderplugin-gallery-skin" value="darkness"> Darkness <br /><img style="width:300px;" src="<?php echo WONDERPLUGIN_GALLERY_URL; ?>images/skin-darkness.jpg" /></label>
						</div>
						
					</fieldset>
				</form>
			</li>
			<li class="wonderplugin-tab">
			
				<div class="wonderplugin-gallery-options">
					<div class="wonderplugin-gallery-options-menu" id="wonderplugin-gallery-options-menu">
						<div class="wonderplugin-gallery-options-menu-item wonderplugin-gallery-options-menu-item-selected"><?php _e( 'Gallery options', 'wonderplugin_gallery' ); ?></div>
						<div class="wonderplugin-gallery-options-menu-item"><?php _e( 'Skin options', 'wonderplugin_gallery' ); ?></div>
						<div class="wonderplugin-gallery-options-menu-item"><?php _e( 'Categories', 'wonderplugin_gallery' ); ?></div>
						<div class="wonderplugin-gallery-options-menu-item"><?php _e( 'Lightbox options', 'wonderplugin_gallery' ); ?></div>
						<div class="wonderplugin-gallery-options-menu-item"><?php _e( 'Social Media options', 'wonderplugin_gallery' ); ?></div>
						<div class="wonderplugin-gallery-options-menu-item"><?php _e( 'Advanced options', 'wonderplugin_gallery' ); ?></div>
					</div>
					
					<div class="wonderplugin-gallery-options-tabs" id="wonderplugin-gallery-options-tabs">
						<div class="wonderplugin-gallery-options-tab wonderplugin-gallery-options-tab-selected">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Slideshow</th>
									<td><label><input name='wonderplugin-gallery-autoslide' type='checkbox' id='wonderplugin-gallery-autoslide' value='' /> Auto slide</label>
									<p><label><input name='wonderplugin-gallery-random' type='checkbox' id='wonderplugin-gallery-random' value='' /> Random</label></p>
									<p>Loop count: <input name='wonderplugin-gallery-loop' type='number' id='wonderplugin-gallery-loop' value='0' min='0' class="small-text" /> (0 will make the slideshow loop endlessly, 1 will make it not loop)</p>
									<p><label><input name='wonderplugin-gallery-switchonmouseover' type='checkbox' id='wonderplugin-gallery-switchonmouseover' value='' /> Switch slideshow on mouse over thumbnails</label></p>
									</td>
								</tr>
								<tr>
									<th>Slideshow interval (ms)</th>
									<td><label><input name='wonderplugin-gallery-slideshowinterval' type='number' id='wonderplugin-gallery-slideshowinterval' value='' /></label></td>
								</tr>
								<tr>
									<th>Video</th>
									<td><label><input name='wonderplugin-gallery-autoplayvideo' type='checkbox' id='wonderplugin-gallery-autoplayvideo' value='' /> Automatically play video </label>
									<p style="font-style:italic;"> * Video autoplay on page load will not work unless the video is muted; On iOS, the playsinline attribute also needs to be enabled.</p>
									<p><label><input name='wonderplugin-gallery-mutevideo' type='checkbox' id='wonderplugin-gallery-mutevideo' value='' /> Mute video</label></p>
									<p><label><input name='wonderplugin-gallery-playsinline' type='checkbox' id='wonderplugin-gallery-playsinline' value='' /> Add playsinline attribute to HTML5 video</label></p>
									<p><label><input name='wonderplugin-gallery-autoslideandplayafterfirstplayed' type='checkbox' id='wonderplugin-gallery-autoslideandplayafterfirstplayed' value='' /> Auto slide and play videos after the first video is played</label></p>
									<p><label><input name='wonderplugin-gallery-html5player' type='checkbox' id='wonderplugin-gallery-html5player' value='' /> Use HTML5 as default video player</label></p>
									<p><label><input name='wonderplugin-gallery-schemamarkup' type='checkbox' id='wonderplugin-gallery-schemamarkup' value='' /> Create Schema.org markup for Videos (Dynamic YouTube Playlist not supported)</label></p>
									<p><label><input name='wonderplugin-gallery-hidetitlewhenvideoisplaying' type='checkbox' id='wonderplugin-gallery-hidetitlewhenvideoisplaying' value='' /> Hide title when the video is playing</label></p>
									<p><label><input name='wonderplugin-gallery-stopallplaying' type='checkbox' id='wonderplugin-gallery-stopallplaying' value='' /> Stop other HTML5 audio and video players on the same webpage when the video starts playing</label></p>
									<p><label><input name='wonderplugin-gallery-reloadonvideoend' type='checkbox' id='wonderplugin-gallery-reloadonvideoend' value='' /> Reload and display the poster image when the current video ends</label></p>
									<p><label><input name='wonderplugin-gallery-loadnextonvideoend' type='checkbox' id='wonderplugin-gallery-loadnextonvideoend' value='' /> Load the next video and stop when the current video ends</label></p>
									<p><label><input name='wonderplugin-gallery-donotuseposter' type='checkbox' id='wonderplugin-gallery-donotuseposter' value='' /> Do not use poster image for video slideshow</label></p>
									<p><label>Default HTML5 video volume (0 to 1): <input name='wonderplugin-gallery-defaultvideovolume' type='number' id='wonderplugin-gallery-defaultvideovolume' min="0" max="1" step="0.1" value="1" class="small-text" /></label></p>
									</td>
								</tr>
								<tr>
									<th>Responsive</th>
									<td><label><input name='wonderplugin-gallery-responsive' type='checkbox' id='wonderplugin-gallery-responsive' value='' /> Create a responsive gallery</label>
									<p><label><input name='wonderplugin-gallery-fullwidth' type='checkbox' id='wonderplugin-gallery-fullwidth' value='' /> Create a full width gallery</label></p>
									<p><label><input name='wonderplugin-gallery-disablehovereventontouch' type='checkbox' id='wonderplugin-gallery-disablehovereventontouch' value='' /> Disable hover over effect on touch screen</label></p>
									</td>
								</tr>
								<tr>
									<th>Resize mode</th>
									<td><label>
										<select name='wonderplugin-gallery-resizemode' id='wonderplugin-gallery-resizemode'>
										  <option value="fit">Resize to fit</option>
										  <option value="fill">Resize to fill</option>
										</select>
									</label></td>
								</tr>
								<tr>
									<th>Keyboard Accessibility</th>
									<td><label><input name='wonderplugin-gallery-enabletabindex' type='checkbox' id='wonderplugin-gallery-enabletabindex' value='' /> Enable the tabindex attribute for thumbnails</label><br />
									</td>
								</tr>
								<tr>
									<th>Button display mode</th>
									<td><label>
										<select name='wonderplugin-gallery-imagetoolboxmode' id='wonderplugin-gallery-imagetoolboxmode'>
										  <option value="mouseover">Show on mouseover</option>
										  <option value="show">Always show</option>
										  <option value="hide">Hide</option>
										</select>
									</label></td>
								</tr>
								<tr>
									<th>Buttons</th>
									<td>
										<label><input name='wonderplugin-gallery-showplaybutton' type='checkbox' id='wonderplugin-gallery-showplaybutton' value='' /> Show play/pause button</label>
										<p><label><input name='wonderplugin-gallery-showfullscreenbutton' type='checkbox' id='wonderplugin-gallery-showfullscreenbutton' value='' /> Show lightbox button</label></p>
									</td>
								</tr>
								<tr>
									<th>Transition effect</th>
									<td>
										<label><input name='wonderplugin-gallery-effect-fade' type='checkbox' id='wonderplugin-gallery-effect-fade' value='fade' /> Fade</label> &nbsp;
										<label><input name='wonderplugin-gallery-effect-fadeinout' type='checkbox' id='wonderplugin-gallery-effect-fadeinout' value='fadeinout' /> Fade In Fade Out</label> &nbsp;
										<label><input name='wonderplugin-gallery-effect-crossfade' type='checkbox' id='wonderplugin-gallery-effect-crossfade' value='crossfade' /> Crossfade</label>
										<label><input name='wonderplugin-gallery-effect-slide' type='checkbox' id='wonderplugin-gallery-effect-slide' value='slide' /> Slide</label> &nbsp;
									</td>
								</tr>
								<tr>
									<th>Fade effect duration (ms)</th>
									<td><label><input name='wonderplugin-gallery-duration' type='number' id='wonderplugin-gallery-duration' value='' /></label></td>
								</tr>
								<tr>
									<th>Slide effect duration (ms)</th>
									<td><label><input name='wonderplugin-gallery-slideduration' type='number' id='wonderplugin-gallery-slideduration' value='' /></label></td>
								</tr>
								<tr>
									<th>Timer</th>
									<td><label><input name='wonderplugin-gallery-showtimer' type='checkbox' id='wonderplugin-gallery-showtimer' value='' /> Show a line timer at the bottom of the image when slideshow playing</label></td>
								</tr>
								<tr>
									<th>Carousel</th>
									<td><label><input name='wonderplugin-gallery-showcarousel' type='checkbox' id='wonderplugin-gallery-showcarousel' value='' /> Show thumbnail carousel</label></td>
								</tr>
								<tr>
									<th>Google Analytics Tracking ID:</th>
									<td><label><input name="wonderplugin-gallery-googleanalyticsaccount" type="text" id="wonderplugin-gallery-googleanalyticsaccount" value="" class="medium-text" /></label></td>
								</tr>
								<tr>
									<th>&lt;img&gt; tags</th>
									<td><label><input name='wonderplugin-gallery-showimgtitle' type='checkbox' id='wonderplugin-gallery-showimgtitle' value='' /> Add the following text as &lt;img&gt; tag title attribute: </label>
									<select name='wonderplugin-gallery-imgtitle' id='wonderplugin-gallery-imgtitle'>
										  <option value="title">Title</option>
										  <option value="description">Description</option>
										  <option value="alt">Alt</option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div class="wonderplugin-gallery-options-tab">
							<p class="wonderplugin-gallery-options-tab-title"><?php _e( 'Skin option will be restored to its default value if you switch to a new skin in the Skins tab.', 'wonderplugin_gallery' ); ?></p>
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Text</th>
									<td><label><input name='wonderplugin-gallery-showtitle' type='checkbox' id='wonderplugin-gallery-showtitle' value='' /> Show title</label>
									<p><label><input name='wonderplugin-gallery-showdescription' type='checkbox' id='wonderplugin-gallery-showdescription' value='' /> Show description</label></p>
									</td>
								</tr>
								
								<tr>
									<th>Text area height (px)</th>
									<td>
									<input name="wonderplugin-gallery-titleheight" type="number" id="wonderplugin-gallery-titleheight" value="72" class="small-text" />
									<br><label><input name='wonderplugin-gallery-titlesmallscreen' type='checkbox' id='wonderplugin-gallery-titlesmallscreen' value='' /> Specify a different text height (px) : </label>
									<input name="wonderplugin-gallery-titleheightsmallscreen" type="number" id="wonderplugin-gallery-titleheightsmallscreen" value="148" class="small-text" />
									when the screen width is less than (px) :<input name="wonderplugin-gallery-titlesmallscreenwidth" type="number" id="wonderplugin-gallery-titlesmallscreenwidth" value="640" class="small-text" />
									</td>
								</tr>

								<tr>
									<th>Title CSS</th>
									<td><label><textarea name="wonderplugin-gallery-titlecss" id="wonderplugin-gallery-titlecss" rows="3" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr>
									<th>Description CSS</th>
									<td><label><textarea name="wonderplugin-gallery-descriptioncss" id="wonderplugin-gallery-descriptioncss" rows="3" class="large-text code"></textarea></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Padding', 'wonderplugin_gallery' ); ?> </th>
									<td><input name="wonderplugin-gallery-padding" type="text" id="wonderplugin-gallery-padding" value="12" class="small-text" /></td>
								</tr>
								<tr>
									<th>Shadow</th>
									<td><label><input name='wonderplugin-gallery-galleryshadow' type='checkbox' id='wonderplugin-gallery-galleryshadow'  /> Show gallery shadow</label>
									<p><label><input name='wonderplugin-gallery-slideshadow' type='checkbox' id='wonderplugin-gallery-slideshadow' /> Show slide shadow</label></p>
									<p><label><input name='wonderplugin-gallery-thumbshadow' type='checkbox' id='wonderplugin-gallery-thumbshadow' /> Show thumbnail shadow</label></p>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Background color', 'wonderplugin_gallery' ); ?> </th>
									<td><input name="wonderplugin-gallery-bgcolor" type="text" id="wonderplugin-gallery-bgcolor" value="" class="text" /></td>
								</tr>
								<tr>
									<th><?php _e( 'Background image', 'wonderplugin_gallery' ); ?> </th>
									<td><input name="wonderplugin-gallery-bgimage" type="text" id="wonderplugin-gallery-bgimage" value="" class="large-text" /></td>
								</tr>
								<tr>
									<th>Thumbnail title and image</th>
									<td>
									<label><input name='wonderplugin-gallery-thumbshowimage' type='checkbox' id='wonderplugin-gallery-thumbshowimage'  /> Show image in thumbnail area</label>
									<p><label><input name='wonderplugin-gallery-thumbshowtitle' type='checkbox' id='wonderplugin-gallery-thumbshowtitle'  /> Show title in thumbnail area - title height (px): </label> - <label><input name="wonderplugin-gallery-thumbtitleheight" type="number" id="wonderplugin-gallery-thumbtitleheight" value="24" class="small-text" /></label></p>
									</td>
								</tr>
								<tr>
								<th><?php _e( 'Thumbnail width/ height', 'wonderplugin_gallery' ); ?></th>
									<td><input name="wonderplugin-gallery-thumbwidth" type="number" id="wonderplugin-gallery-thumbwidth" value="64" class="small-text" /> / <input name="wonderplugin-gallery-thumbheight" type="number" id="wonderplugin-gallery-thumbheight" value="48" class="small-text" />
									<p><label><input name='wonderplugin-gallery-thumbcolumnsresponsive' type='checkbox' id='wonderplugin-gallery-thumbcolumnsresponsive'  /> Specify different size for small screens (horizontal skins only)</label></p>
									<ul style="list-style-type:square;margin-left:24px;"><li>Thumbnail size when the screen width is less than <input name="wonderplugin-gallery-thumbmediumsize" type="number" id="wonderplugin-gallery-thumbmediumsize" value="800" class="small-text" /> px:  <input name="wonderplugin-gallery-thumbmediumwidth" type="number" id="wonderplugin-gallery-thumbmediumwidth" value="64" class="small-text" /> / <input name="wonderplugin-gallery-thumbmediumheight" type="number" id="wonderplugin-gallery-thumbmediumheight" value="48" class="small-text" /> <label>title height (px): <input name="wonderplugin-gallery-thumbmediumtitleheight" type="number" id="wonderplugin-gallery-thumbmediumtitleheight" value="24" class="small-text" /></label></li>
									<li>Thumbnail size when the screen width is less than <input name="wonderplugin-gallery-thumbsmallsize" type="number" id="wonderplugin-gallery-thumbsmallsize" value="480" class="small-text" /> px:  <input name="wonderplugin-gallery-thumbsmallwidth" type="number" id="wonderplugin-gallery-thumbsmallwidth" value="64" class="small-text" /> / <input name="wonderplugin-gallery-thumbsmallheight" type="number" id="wonderplugin-gallery-thumbsmallheight" value="48" class="small-text" /> <label>title height (px): <input name="wonderplugin-gallery-thumbsmalltitleheight" type="number" id="wonderplugin-gallery-thumbsmalltitleheight" value="24" class="small-text" /></label></li></ul>
									</td>
								</tr>
								<tr>
									<th></th>
									<td><label><input name='wonderplugin-gallery-thumbverticalmiddle' type='checkbox' id='wonderplugin-gallery-thumbverticalmiddle'  /> Vertically align thumbnails in middle</label></td>
								</tr>
								<tr>
									<th>Thumbnail gap</th>
									<td><input name="wonderplugin-gallery-thumbgap" type="text" id="wonderplugin-gallery-thumbgap" value="64" class="small-text" /> </td>
								</tr>
								<tr>
									<th>Thumbnail gap between row</th>
									<td><input name="wonderplugin-gallery-thumbrowgap" type="text" id="wonderplugin-gallery-thumbrowgap" value="64" class="small-text" /> </td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-gallery-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Category Options</th>
									<td><label><input name="wonderplugin-gallery-showcategory" type="checkbox" id="wonderplugin-gallery-showcategory"/>Show categorey selection when categories are defined</label>
									<p><label>Category area height: <input name="wonderplugin-gallery-categoryheight" type="number" id="wonderplugin-gallery-categoryheight" value="48" class="small-text" /></label></p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Default category</th>
									<td>
									<select name='wonderplugin-gallery-categorydefault' id='wonderplugin-gallery-categorydefault'>
										<option value="all">Show All</option>
									</select>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">CSS style</th>
									<td>
										<select name='wonderplugin-gallery-categorycssstyle' id='wonderplugin-gallery-categorycssstyle'>
											<option value="wonder-category-dropdown">wonder-category-dropdown</option>
											<option value="wonder-category-dropdown-fullwidth">wonder-category-dropdown-fullwidth</option>
											<option value="wonder-category-textlink">wonder-category-textlink</option>
											<option value="wonder-category-textlink-lightcolor">wonder-category-textlink-lightcolor</option>
											<option value="wonder-category-greybutton">wonder-category-greybutton</option>
										</select>
									<p><textarea name='wonderplugin-gallery-categorycss' id='wonderplugin-gallery-categorycss' value='' class='large-text' rows="20"></textarea></p>
									</td>
								</tr>

							</table>
						</div>

						<div class="wonderplugin-gallery-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Maximum text bar height</th>
									<td><label><input name="wonderplugin-gallery-lightboxtextheight" type="text" id="wonderplugin-gallery-lightboxtextheight" value="64" class="small-text" /></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Title</th>
									<td><label><input name="wonderplugin-gallery-lightboxshowtitle" type="checkbox" id="wonderplugin-gallery-lightboxshowtitle" /> Show title</label></td>
								</tr>
								
								<tr>
									<th>Title CSS</th>
									<td><label><textarea name="wonderplugin-gallery-lightboxtitlecss" id="wonderplugin-gallery-lightboxtitlecss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Description</th>
									<td><label><input name="wonderplugin-gallery-lightboxshowdescription" type="checkbox" id="wonderplugin-gallery-lightboxshowdescription" /> Show description</label></td>
								</tr>
								
								<tr>
									<th>Description CSS</th>
									<td><label><textarea name="wonderplugin-gallery-lightboxdescriptioncss" id="wonderplugin-gallery-lightboxdescriptioncss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
							</table>
						</div>
						
						<div class="wonderplugin-gallery-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr valign="top">
								<th scope="row">Social Media</th>
								<td><label for="wonderplugin-gallery-initsocial"><input name="wonderplugin-gallery-initsocial" type="checkbox" id="wonderplugin-gallery-initsocial" /> Initialise social media CSS</label>
								<p><label for="wonderplugin-gallery-showsocial"><input name="wonderplugin-gallery-showsocial" type="checkbox" id="wonderplugin-gallery-showsocial" /> Enable social media</label></p>
								<p><label for="wonderplugin-gallery-showemail"><input name="wonderplugin-gallery-showemail" type="checkbox" id="wonderplugin-gallery-showemail" /> Show email button</label></p>
								<p><label for="wonderplugin-gallery-showfacebook"><input name="wonderplugin-gallery-showfacebook" type="checkbox" id="wonderplugin-gallery-showfacebook" /> Show Facebook button</label></p>
								<p><label for="wonderplugin-gallery-showtwitter"><input name="wonderplugin-gallery-showtwitter" type="checkbox" id="wonderplugin-gallery-showtwitter" /> Show Twitter button</label></p>
								<p><label for="wonderplugin-gallery-showpinterest"><input name="wonderplugin-gallery-showpinterest" type="checkbox" id="wonderplugin-gallery-showpinterest" /> Show Pinterest button</label></p>
								</td>
							</tr>
				        	
				        	<tr valign="top">
								<th scope="row">Position and Size</th>
								<td>
								Display mode on the gallery:
								<select name="wonderplugin-gallery-socialmode" id="wonderplugin-gallery-socialmode">
								  <option value="mouseover" selected="selected">On mouse over</option>
								  <option value="always">Always</option>
								</select>
								<p>Position CSS: <input name="wonderplugin-gallery-socialposition" type="text" id="wonderplugin-gallery-socialposition" value="" class="regular-text" /></p>
                				<p>Position CSS on lightbox popup: <input name="wonderplugin-gallery-socialpositionlightbox" type="text" id="wonderplugin-gallery-socialpositionlightbox" value="" class="regular-text" /></p>
								<p>Button size: <input name="wonderplugin-gallery-socialbuttonsize" type="number" id="wonderplugin-gallery-socialbuttonsize" value="32" class="small-text" />
								Button font size: <input name="wonderplugin-gallery-socialbuttonfontsize" type="number" id="wonderplugin-gallery-socialbuttonfontsize" value="18" class="small-text" />
								Buttons direction:
								<select name="wonderplugin-gallery-socialdirection" id="wonderplugin-gallery-socialdirection">
								  <option value="horizontal" selected="selected">horizontal</option>
								  <option value="vertical">vertical</option>
								</select>
								</p>
								<p><label for="wonderplugin-gallery-socialrotateeffect"><input name="wonderplugin-gallery-socialrotateeffect" type="checkbox" id="wonderplugin-gallery-socialrotateeffect" /> Enable button rotating effect on mouse hover</label></p>	
								</td>
							</tr>
							</table>
						</div>
						
						<div class="wonderplugin-gallery-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th></th>
									<td><p><label><input name='wonderplugin-gallery-donotinit' type='checkbox' id='wonderplugin-gallery-donotinit'  /> Do not init the gallery when the page is loaded. Check this option if you would like to manually init the gallery with JavaScript API.</label></p>
									<p><label><input name='wonderplugin-gallery-addinitscript' type='checkbox' id='wonderplugin-gallery-addinitscript'  /> Add init scripts together with gallery HTML code. Check this option if your WordPress site uses Ajax to load pages and posts.</label></p>
									<p><label><input name='wonderplugin-gallery-doshortcodeontext' type='checkbox' id='wonderplugin-gallery-doshortcodeontext'  /> Support shortcode in title and description</label></p>
									<p><label><input name='wonderplugin-gallery-triggerresize' type='checkbox' id='wonderplugin-gallery-triggerresize'  /> Trigger window resize event after (ms): </label><input name="wonderplugin-gallery-triggerresizedelay" type="number" min=0 id="wonderplugin-gallery-triggerresizedelay" value="0" class="small-text" /></p>
									<p><label><input name='wonderplugin-gallery-specifyid' type='checkbox' id='wonderplugin-gallery-specifyid' value='' /> Use gallery id in CSS class name</label></p>
									<p><label><input name='wonderplugin-gallery-removeinlinecss' type='checkbox' id='wonderplugin-gallery-removeinlinecss'  /> Do not add CSS to HTML source code</label></p>
									</td>
								</tr>
								<tr>
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wonderplugin-gallery-custom-css' id='wonderplugin-gallery-custom-css' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Data Options</th>
									<td><textarea name='wonderplugin-gallery-data-options' id='wonderplugin-gallery-data-options' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Custom JavaScript</th>
									<td><textarea name='wonderplugin-gallery-customjs' id='wonderplugin-gallery-customjs' value='' class='large-text' rows="10"></textarea><br />
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-gallery-preview-tab">
					<div id="wonderplugin-gallery-preview-message"></div>
					<div id="wonderplugin-gallery-preview-container">
					</div>
				</div>
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-gallery-publish-loading"></div>
				<div id="wonderplugin-gallery-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}