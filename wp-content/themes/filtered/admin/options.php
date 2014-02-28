<?php

//////////////////////////////////////////////////////////////
// ThemeTrust Custom Options Page
/////////////////////////////////////////////////////////////

$ttrust_theme_name = "Filtered";
$ttrust_theme_version = "1.1.0";

require_once( TEMPLATEPATH . '/admin/admin-setup.php');
require_once( TEMPLATEPATH . '/admin/admin-functions.php');

add_action( 'admin_init', 'ttrust_register_options' );

function ttrust_register_options() {
	register_setting( 'ttrust_options_group', 'ttrust_options' );
}



function ttrust_options_page() {
	
global $ttrust_theme_name, $ttrust_theme_version, $ttrust_default_slideshow_speed, $ttrust_options, $ttrust_suggested_img_size;

	
?>

<div class="wrap">
	<div id="optionsWrap">			
		<div id="optionsHeader" class="clearfix">
			<a id="themetrustLogo" href="#"><img src="<?php echo ADMIN_PATH . '/images/themetrust_logo.png'; ?>" alt="ThemeTrust" /></a>
			<span id="themeVersion">
				<?php echo("<strong>".$ttrust_theme_name . "</strong> v" . $ttrust_theme_version ); ?> | <a href="http://themetrust.com/support-forums" target="_blank"><?php _e('Support', 'themetrust'); ?></a>
			</span>
			<ul id="optionsNav" class="tabs">
				<li id="tab1"><a href="#option1"><?php _e('General', 'themetrust'); ?></a></li>
				<li id="tab2"><a href="#option2"><?php _e('Appearance', 'themetrust'); ?></a></li>
				<li id="tab3"><a href="#option3"><?php _e('Integration', 'themetrust'); ?></a></li>				
				<li id="tab4"><a href="#option4"><?php _e('Home Page', 'themetrust'); ?></a></li>
			</ul>
			
		</div>		
		
		<form id="optionsForm" method="post" action="options.php">			
		
		    <?php
			settings_fields( 'ttrust_options_group' ); 
		    $ttrust_options = of_get_option('all'); 
			$ttrust_logo = get_option('ttrust_logo');							
			?>
		    
		    <div class="optionsContainer clearfix">	
			
				<div id="statusBar" class="clearfix">
					<?php if(isset($_REQUEST['updated']) || isset($_REQUEST['reset'])) echo '<div id="message">'.$ttrust_theme_name.' '. 'Settings updated'.'</div>'; ?>
					<input type="submit" class="button" value="Save Changes" />
				</div>	
			
		    	<div id="option1" class="optionContent">	    		
					
					<!-- Logo -->					
					<div class="subOption">
						<h3 class="logoHeading"><?php _e('Logo', 'themetrust'); ?></h3>	    			
				    	
						<div class="logoContainer itemRow">
							<div id="status_ttrust_logo"></div>
							<?php if(isset($ttrust_logo) && $ttrust_logo != "" ){ ?>	
								<img id="img_ttrust_logo" src="<?php echo($ttrust_logo); ?>" />
							<?php } ?>
						</div>	    											
						
						<div class="itemRow clearfix">		
							<input name="ttrust_logo" id="ttrust_logo_upload" type="text" size="50" value="<?php if(isset($ttrust_logo)) echo $ttrust_logo;  ?>" />	<input type="button" class="button imageUploadBtn" id="ttrust_logo" value="Upload Image"/><input type="button" <?php if(!$ttrust_logo) echo 'style="display: none;"'; ?> class="button imageResetButton" title="ttrust_logo" id="reset_ttrust_logo" value="Remove">
						</div>
						
						<p class="instructions"><?php _e('Enter a URL or upload a custom logo.', 'themetrust'); ?></p>												
		 										
					</div>					
					
					<!-- CSS -->
					<div class="subOption">
						<h3 class="cssHeading"><?php _e('Custom CSS', 'themetrust'); ?></h3>
						<textarea name="ttrust_options[ttrust_custom_css]" cols=70 rows=6><?php if (isset($ttrust_options['ttrust_custom_css'])) echo $ttrust_options['ttrust_custom_css']; ?></textarea>
						<p class="instructions"><?php _e('Enter custom CSS here.', 'themetrust'); ?> </p>
					</div>
					
					<!-- Footer Text -->
					<div class="subOption">
						<h3 class="cssHeading"><?php _e('Footer Text', 'themetrust'); ?></h3>
						
						<h4><?php _e('Left side:', 'themetrust'); ?></h4>
						<textarea name="ttrust_options[ttrust_footer_left]" cols=70 rows=6><?php if (isset($ttrust_options['ttrust_footer_left'])) echo $ttrust_options['ttrust_footer_left']; ?></textarea>
						<p class="instructions"><?php _e('This will appear on the left side of the footer.', 'themetrust'); ?></p>
						
						<h4><?php _e('Right side:', 'themetrust'); ?></h4>
						<textarea name="ttrust_options[ttrust_footer_right]" cols=70 rows=6><?php if (isset($ttrust_options['ttrust_footer_right'])) echo $ttrust_options['ttrust_footer_right']; ?></textarea>
						<p class="instructions"><?php _e('This will appear on the right side of the footer.', 'themetrust'); ?></p>
					</div>
									
				</div>
				
				<div id="option2" class="optionContent">
					<!-- Color -->
					<div class="subOption">
						<h3 class="colorHeading"><?php _e('Color & Background', 'themetrust'); ?></h3>
						
						<div class="itemRow clearfix divided">		
						<label class="singleLine"><?php _e('Background:', 'themetrust'); ?></label> 
						<select name="ttrust_options[ttrust_background]" id="themeBackground" class="ttrustSelect inlineItem">
							<option<?php if($ttrust_options['ttrust_background']=='bkgNone') echo ' selected'; ?> value="bkgNone"><?php _e('none', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_background']=='bkgGrid') echo ' selected'; ?> value="bkgGrid"><?php _e('Grid', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_background']=='bkgScratches') echo ' selected'; ?> value="bkgScratches"><?php _e('Scratches', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_background']=='bkgNoise') echo ' selected'; ?> value="bkgNoise"><?php _e('Noise', 'themetrust'); ?></option>	
						</select>
						</div>
						
						<div class="itemRow clearfix divided">		
						<label class="singleLine"><?php _e('Footer Color:', 'themetrust'); ?></label> 
						<select name="ttrust_options[ttrust_footer_color]" id="footerColor" class="ttrustSelect inlineItem">
							<option<?php if($ttrust_options['ttrust_footer_color']=='dark') echo ' selected'; ?> value="dark"><?php _e('Dark', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_footer_color']=='light') echo ' selected'; ?> value="light"><?php _e('Light', 'themetrust'); ?></option>							
						</select>
						</div>													
					
						<div class="itemRow clearfix divided">
							<span class="inlineItem clearfix"><label class="medium"><strong><?php _e('Button:', 'themetrust'); ?></strong></label> <input class="colorField" name="ttrust_options[ttrust_color_btn]" id="colorBtn"  type="text" size=7 value="<?php if(isset($ttrust_options['ttrust_color_btn'])) echo $ttrust_options['ttrust_color_btn']; ?>" /> </span>
							<span class="inlineItem clearfix"><label class="small">hover:</label> <input class="colorField" name="ttrust_options[ttrust_color_btn_hover]" id="colorBtnHover"  type="text" size=7 value="<?php if(isset($ttrust_options['ttrust_color_btn_hover'])) echo $ttrust_options['ttrust_color_btn_hover']; ?>" /> </span>
						</div>						
						<div class="itemRow clearfix divided">
							<span class="inlineItem clearfix"><label class="medium"><strong><?php _e('Links:', 'themetrust'); ?></strong></label> <input class="colorField" name="ttrust_options[ttrust_color_link]" id="colorLink"  type="text" size=7 value="<?php if(isset($ttrust_options['ttrust_color_link'])) echo $ttrust_options['ttrust_color_link']; ?>" /> </span>
							<span class="inlineItem clearfix"><label class="small"><?php _e('hover:', 'themetrust'); ?></label> <input class="colorField" name="ttrust_options[ttrust_color_link_hover]" id="colorLinkHover"  type="text" size=7 value="<?php if(isset($ttrust_options['ttrust_color_link_hover'])) echo $ttrust_options['ttrust_color_link_hover']; ?>" /> </span>
						</div>
						<p class="instructions"><?php _e('Use these fields to set custom colors. If the fields are left blank, the default colors will be used.', 'themetrust'); ?></p>
					</div>					
				</div>
			
			
				<div id="option3" class="optionContent">		
					
					
					<!-- Analytics -->
					<div class="subOption">
						<h3 class="analyticsHeading"><?php _e('Analytics', 'themetrust'); ?></h3>
						<textarea name="ttrust_options[ttrust_analytics]" cols=40 rows=5><?php if(isset($ttrust_options['ttrust_analytics'])) echo $ttrust_options['ttrust_analytics']; ?></textarea>
						<p class="instructions"><?php _e('Enter your custom analytics code. (e.g. Google Analytics).', 'themetrust'); ?></p>
					</div>		
				</div>				
			
			
			<div id="option4" class="optionContent">			
				
				<!-- Slideshow -->						
				<div class="subOption clearfix">
					<h3 class="slideshowHeading"><?php _e('Slideshow', 'themetrust'); ?></h3>					
					<div class="itemRow clearfix divided">						
						<label class="sliderLabel singleLine"><?php _e('Enable Slideshow:', 'themetrust'); ?></label>
						<input id="enableSlideshow" name="ttrust_options[ttrust_slideshow_enabled]" type="checkbox" <?php if($ttrust_options['ttrust_slideshow_enabled']) echo("checked"); ?>/>
					</div>
					
					<div class="itemRow clearfix divided">						
						<label class="sliderLabel singleLine">Deactivate Links:</label>
						<input id="deactivateLinks" name="ttrust_options[ttrust_slide_deactivate_links]" type="checkbox" <?php if($ttrust_options['ttrust_slide_deactivate_links']) echo("checked"); ?>/>
					
						<p class="instructions clear"><?php _e('Check this box to prevent slides from linking to corresponding pages.', 'themetrust'); ?></p>
						
					</div>
					
					<div class="itemRow clearfix divided">	
						<label class="singleLine"><?php _e('Transition Effect:', 'themetrust'); ?></label>
						<select name="ttrust_options[ttrust_slideshow_effect]" id="slideshowEffect" class="ttrustSelect inlineItem">							
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceDown') echo ' selected'; ?> value="sliceDown"><?php _e('Slice Down', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceDownLeft') echo ' selected'; ?> value="sliceDownLeft"><?php _e('Slice Down Left', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceUp') echo ' selected'; ?> value="sliceUp"><?php _e('Slice Up', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceUpLeft') echo ' selected'; ?> value="sliceUpLeft"><?php _e('Slice Up Left', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceUpDown') echo ' selected'; ?> value="sliceUpDown"><?php _e('Slice Up Down', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='sliceUpDownLeft') echo ' selected'; ?> value="sliceUpDownLeft"><?php _e('Slice Up Down Left', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='fold') echo ' selected'; ?> value="fold"><?php _e('Fold', 'themetrust'); ?></option>	
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='fade') echo ' selected'; ?> value="fade"><?php _e('Fade', 'themetrust'); ?></option>
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='slideInRight') echo ' selected'; ?> value="slideInRight"><?php _e('Slide In Right', 'themetrust'); ?></option>	
							<option<?php if($ttrust_options['ttrust_slideshow_effect']=='slideInLeft') echo ' selected'; ?> value="slideInLeft"><?php _e('Slide In Left', 'themetrust'); ?></option>													
						</select>
					</div>
					
					<div class="itemRow clearfix">						
					<input name="ttrust_options[ttrust_slideshow_speed]" id="slideShowSpeed" type="hidden" value="<?php echo($ttrust_options['ttrust_slideshow_speed']); ?>"/>
					<label class="sliderLabel singleLine"><?php _e('Speed:', 'themetrust'); ?></label>					
					<div class="sliderHolder">
						<div id="speedSlider"></div>
					</div>
					<div id="speedSliderValue" class="sliderValue"><?php $v = ($ttrust_options['ttrust_slideshow_speed'] == "0") ? "automatic playing turned off" : $ttrust_options['ttrust_slideshow_speed'] . " seconds"; echo($v); ?> </div>
					<script type="text/javascript">						
					jQuery(document).ready(function() {				
					    	jQuery("#speedSlider").slider('option', 'value', parseInt(<?php echo($ttrust_options['ttrust_slideshow_speed']); ?>));				
					});			    
					</script>
					</div>					
					<p class="instructions"><?php _e('Adjust the speed of the home page slideshow. Move the slider to the far left to disable auto-playing.', 'themetrust'); ?></p>										
				</div>
				
				<!-- Homepage Message-->
				<div class="subOption">
					<h3 class="cssHeading"><?php _e('Message', 'themetrust'); ?></h3>
					<textarea name="ttrust_options[ttrust_home_message]" cols=60 rows=5><?php if(isset($ttrust_options['ttrust_home_message'])) echo $ttrust_options['ttrust_home_message']; ?></textarea>
					<p class="instructions"><?php _e('Enter a message. This will appear on the home page under the slideshow.', 'themetrust'); ?></p>
				</div>
				
				<!-- Home Page Content -->						
				<div class="subOption clearfix">
					<h3 class="pagesHeading"><?php _e('Home Page Content', 'themetrust'); ?></h3>					
					
					<div class="itemRow clearfix divided">						
						<label class="sliderLabel singleLine wide"><?php _e('Show Posts instead of Projects:', 'themetrust'); ?></label>
						<input id="showPostOnHome" name="ttrust_options[ttrust_posts_on_home]" type="checkbox" <?php if($ttrust_options['ttrust_posts_on_home']) echo("checked"); ?>/>
						<p class="instructions clear"><?php _e('Check this box to display posts and a sidebar on the home page instead of the project gallery.', 'themetrust'); ?></p>
					</div>
					
					<div class="itemRow clearfix divided">						
						<label class="sliderLabel singleLine wide"><?php _e('Show Only Featured Projects:', 'themetrust'); ?></label>
						<input id="showPostOnHome" name="ttrust_options[ttrust_featured_on_home]" type="checkbox" <?php if($ttrust_options['ttrust_featured_on_home']) echo("checked"); ?>/>
						<p class="instructions clear"><?php _e('Check this box if you only want to show featured projects instead of all projects.', 'themetrust'); ?></p>
					</div>
				</div>									
				
			</div>
		<input type="submit" class="button right" value="<?php _e('Save Changes', 'themetrust'); ?>" />
		
	</div>
</div>
<?php } ?>