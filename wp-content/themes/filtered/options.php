<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * This can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */

 function optionsframework_option_name() {        
       $optionsframework_settings = get_option('optionsframework');
       $optionsframework_settings['id'] = 'ttrust_options';
       update_option('optionsframework', $optionsframework_settings);
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {	

	// Background
	$background_options = array("bkgNone" => "None", "bkgGrid" => "Grid", "bkgScratches" => "Scratches", "bkgNoise" => "Noise");	
	
	// Footer
	$footer_options = array("dark" => "Dark", "light" => "Light");	

	// Slideshow Transition Effect
	$slideshow_effect = array("slide" => "Slide", "fade" => "Fade");
	
	// If using image radio buttons, define a directory path
	$imagepath =  get_bloginfo('stylesheet_directory') . '/images/';
		
	$options = array();
/* General */		
	$options[] = array( "name" => __('General','themetrust'),
						"type" => "heading");	
	
	$options[] = array( "name" => __('Logo','themetrust'),
						"desc" => __('Upload a custom logo.','themetrust'),
						"id" => "ttrust_logo",
						"type" => "upload");
						
	$options[] = array( "name" => __('Favicon','themetrust'),
						"desc" => __('Upload a custom favicon.','themetrust'),
						"id" => "ttrust_favicon",
						"type" => "upload");						
	
	$options[] = array( "name" => __('Custom CSS','themetrust'),
						"desc" => __('Enter custom CSS here.','themetrust'),
						"id" => "ttrust_custom_css",
						"std" => "",
						"type" => "textarea");					
	
	$options[] = array( "name" => __('Left Footer Text','themetrust'),
						"desc" => __('This will appear on the left side of the footer.','themetrust'),
						"id" => "ttrust_footer_left",
						"std" => "",
						"type" => "textarea");

	$options[] = array( "name" => __('Right Footer Text','themetrust'),
						"desc" => __('This will appear on the right side of the footer.','themetrust'),
						"id" => "ttrust_footer_right",
						"std" => "",
						"type" => "textarea");
/* Appearance */						
	$options[] = array( "name" => __('Appearance','themetrust'),
						"type" => "heading");

	$options[] = array( "name" => __('Background','themetrust'),
						"desc" => __('Select a pattern for your background.','themetrust'),
						"id" => "ttrust_background",
						"std" => "bkgNone",
						"type" => "select",
						"options" => $background_options);

	$options[] = array( "name" => __('Footer Color','themetrust'),
						"desc" => __('Select a pattern for your background.','themetrust'),
						"id" => "ttrust_footer_color",
						"std" => "dark",
						"type" => "select",
						"options" => $footer_options);
							
	$options[] = array( "name" => __('Button Color','themetrust'),
						"desc" => __('Select a color for your buttons.','themetrust'),
						"id" => "ttrust_color_btn",
						"std" => "",
						"type" => "color");

	$options[] = array( "name" => __('Button Hover Color','themetrust'),
						"desc" => __('Select a hover color for your buttons on hover.','themetrust'),
						"id" => "ttrust_color_btn_hover",
						"std" => "",
						"type" => "color");

	$options[] = array( "name" => __('Link Color','themetrust'),
						"desc" => __('Select a color for your links.','themetrust'),
						"id" => "ttrust_color_link",
						"std" => "",
						"type" => "color");

	$options[] = array( "name" => __('Link Hover Color','themetrust'),
						"desc" => __('Select a hover color for your links.','themetrust'),
						"id" => "ttrust_color_link_hover",
						"std" => "",
						"type" => "color");

/* Typography */						
	$options[] = array( "name" => __('Typography','themetrust'),
						"type" => "heading");						
						
	$options[] = array( "name" => __('Font for Headings','themetrust'),
						"desc" => __('Enter the name of the <a href="http://www.google.com/webfonts" target="_blank">Google Web Font</a> you want to use for headings.','themetrust'),
						"id" => "ttrust_heading_font",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" => __('Font for Body Text','themetrust'),
						"desc" => __('Enter the name of the <a href="http://www.google.com/webfonts" target="_blank">Google Web Font</a> you want to use for the body text.','themetrust'),
						"id" => "ttrust_body_font",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" => __('Font for the Home Message','themetrust'),
						"desc" => __('Enter the name of the <a href="http://www.google.com/webfonts" target="_blank">Google Web Font</a> you want to use for the call to action box text.','themetrust'),
						"id" => "ttrust_home_message_font",
						"std" => "",
						"type" => "text");					
											
/* Integration */						
	$options[] = array( "name" => __('Integration','themetrust'),
						"type" => "heading");						
						
	$options[] = array( "name" => __('Analytics','themetrust'),
						"desc" => __('Enter your custom analytics code. (e.g. Google Analytics).','themetrust'),
						"id" => "ttrust_analytics",
						"std" => "",
						"type" => "textarea",
						"validate" => "none");	
/* Home Page */						
	$options[] = array( "name" => __('Home Page','themetrust'),
						"type" => "heading");

	$options[] = array( "name" => __('Enable Slideshow','themetrust'),
						"desc" => __('Check this box to enable the home page slideshow.','themetrust'),
						"id" => "ttrust_slideshow_enabled",
						"std" => "1",
						"type" => "checkbox");	
	
	$options[] = array( "name" => __('Deactivate Links','themetrust'),
						"desc" => __('Check this box to prevent slides from linking to corresponding pages.','themetrust'),
						"id" => "ttrust_slide_deactivate_links",
						"std" => "0",
						"type" => "checkbox");	
						
	$options[] = array( "name" => __('Slideshow Effect','themetrust'),
						"desc" => __('Select the type of transition effect for the slideshow.','themetrust'),
						"id" => "ttrust_slideshow_effect",
						"std" => "fade",
						"type" => "select",
						"options" => $slideshow_effect);						
						
	$options[] = array( "name" => __('Slideshow Speed','themetrust'),
						"desc" => __('Enter the delay in seconds between slides.','themetrust'),
						"id" => "ttrust_slideshow_speed",
						"std" => "6",
						"type" => "text");
						
	$options[] = array( "name" => __('Home Message','themetrust'),
						"desc" => __('Enter a short message to be displayed on the home page.','themetrust'),
						"id" => "ttrust_home_message",
						"std" => "",
						"type" => "textarea");
						
	$options[] = array( "name" => __('Show Posts Instead of Projects?','themetrust'),
						"desc" => __('Check this box to display posts and a sidebar on the home page instead of the project gallery.','themetrust'),
						"id" => "ttrust_posts_on_home",
						"std" => "0",
						"type" => "checkbox",);					

	$options[] = array( "name" => __('Show Only Featured Projects','themetrust'),
						"desc" => __('Check this box if you only want to show featured projects instead of all projects.','themetrust'),
						"id" => "ttrust_featured_on_home",
						"std" => "0",
						"type" => "checkbox",);					
						
	return $options;
}