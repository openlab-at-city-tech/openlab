<?php

add_action('init','woo_options');  
function woo_options(){
	
// VARIABLES
$themename = "The Morning After";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/themorningafter/';
$shortname = "woo";

// Populate WooThemes option in array for use in theme
global $woo_options;
$woo_options = get_option('woo_options');

$GLOBALS['template_path'] = get_template_directory_uri();

//Access the WordPress Categories via an Array
$woo_categories = array();  
$woo_categories_obj = get_categories('hide_empty=0');
foreach ($woo_categories_obj as $woo_cat) {
    $woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;}
$categories_tmp = array_unshift($woo_categories, "Select a category:");    
       
//Access the WordPress Pages via an Array
$woo_pages = array();
$woo_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($woo_pages_obj as $woo_page) {
    $woo_pages[$woo_page->ID] = $woo_page->post_name; }
$woo_pages_tmp = array_unshift($woo_pages, "Select a page:");       

// Image Alignment radio box
$options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 

// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 

//Testing 
$options_select = array("one","two","three","four","five"); 
$options_radio = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five"); 

//URL Shorteners
if (_iscurlinstalled()) {
	$options_select = array("Off","TinyURL","Bit.ly");
	$short_url_msg = 'Select the URL shortening service you would like to use.'; 
} else {
	$options_select = array("Off");
	$short_url_msg = '<strong>cURL was not detected on your server, and is required in order to use the URL shortening services.</strong>'; 
}

//Stylesheets Reader
$alt_stylesheet_path = TEMPLATEPATH . '/styles/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}

//More Options


$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");

// THIS IS THE DIFFERENT FIELDS
$options = array();   
  
$options[] = array( "name" => "General Settings",
					"icon" => "general", 
                    "type" => "heading");
                        
$options[] = array( "name" => "Theme Stylesheet",
					"desc" => "Select your themes alternative color scheme.",
					"id" => $shortname."_alt_stylesheet",
					"std" => "default.css",
					"type" => "select",
					"options" => $alt_stylesheets);

$options[] = array( "name" => "Custom Logo",
					"desc" => "Upload a logo for your theme, or specify an image URL directly.",
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload");    
                                                                                     
$options[] = array( "name" => "Text Title",
					"desc" => "Enable if you want Blog Title and Tagline to be text-based. Setup title/tagline in WP -> Settings -> General.",
					"id" => $shortname."_texttitle",
					"std" => "true",
					"type" => "checkbox");

$options[] = array( "name" => "Custom Favicon",
					"desc" => "Upload a 16px x 16px <a href='http://www.faviconr.com/'>ico image</a> that will represent your website's favicon.",
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload"); 
                                               
$options[] = array( "name" => "Tracking Code",
					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");        

$options[] = array( "name" => "RSS URL",
					"desc" => "Enter your preferred RSS URL. (Feedburner or other)",
					"id" => $shortname."_feed_url",
					"std" => "",
					"type" => "text");
                    
$options[] = array( "name" => "E-Mail URL",
					"desc" => "Enter your preferred E-mail subscription URL. (Feedburner or other)",
					"id" => $shortname."_subscribe_email",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Contact Form E-Mail",
					"desc" => "Enter your E-mail address to use on the Contact Form Page Template. Add the contact form by adding a new page and selecting 'Contact Form' as page template.",
					"id" => $shortname."_contactform_email",
					"std" => "",
					"type" => "text");



$options[] = array( "name" => "Custom CSS",
                    "desc" => "Quickly add some CSS to your theme by adding it to this block.",
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea");

$options[] = array( "name" => "Post/Page Comments",
					"desc" => "Select if you want to enable/disable comments on posts and/or pages. ",
					"id" => $shortname."_comments",
					"type" => "select2",
					"options" => array("post" => "Posts Only", "page" => "Pages Only", "both" => "Pages / Posts", "none" => "None") );       
					
$options[] = array( "name" => "Show Full Content Home",
					"desc" => "Check this if you want to show the full post content on homepage.",
					"id" => $shortname."_post_content_home",
					"std" => "false",
					"type" => "checkbox");

$options[] = array( "name" => "Show Full Content Archive",
					"desc" => "Check this if you want to show the full post content on archive pages.",
					"id" => $shortname."_post_content_archives",
					"std" => "false",
					"type" => "checkbox");                                                
    
$options[] = array( "name" => "Homepage",
					"icon" => "homepage", 
					"type" => "heading"); 
					
$options[] = array( "name" => "Featured Heading",
					"desc" => "Type a custom heading for your featured sections.",
					"id" => $shortname."_featured_heading",
					"std" => "Featured Posts",
					"type" => "text");       
                                            
$options[] = array( "name" => "Featured Category",
					"desc" => "Select the category that you would like to have displayed in the featured section on your homepage.",
					"id" => $shortname."_featured_category",
					"std" => "Select a category:",
					"type" => "select",
					"options" => $woo_categories);
					
$options[] = array( "name" => "Featured Limit",
					"desc" => "Maximum amount of Featured Posts on the Homepage.",
					"id" => $shortname."_featured_limit",
					"std" => "1",
					"type" => "text");     
					                                               
$options[] = array( "name" => "Updates Heading",
					"desc" => "Type a custom heading for your updates sections on your homepage.",
					"id" => $shortname."_updates_heading",
					"std" => "Updates",
					"type" => "text");   
					
$options[] = array( "name" => "Updates Limit",
					"desc" => "Maximum amount of Updates on the Homepage.",
					"id" => $shortname."_updates_limit",
					"std" => "5",
					"type" => "text");    
					
$options[] = array( "name" => "Welcome Heading",
					"desc" => "Title of intro text on homepage.",
					"id" => $shortname."_home_heading",
					"std" => "Welcome to " . get_bloginfo( 'name' ),
					"type" => "text");   
					
$options[] = array( "name" => "Welcome Content",
					"desc" => "Content of intro on homepage.",
					"id" => $shortname."_home_text",
					"std" => "Add you text here. Leave this and the heading blank to remove this completely.",
					"type" => "textarea");   
					
$options[] = array( "name" => "Header Links",
					"icon" => "header", 
					"type" => "heading"); 
					
$options[] = array( "name" => "Home",
					"desc" => "Links for designated nav in header.",
					"id" => $shortname."_nav_home",
					"std" => home_url( '/' ),
					"type" => "text");  
					
$options[] = array( "name" => "About",
					"desc" => "Links for designated navigation in header.",
					"id" => $shortname."_nav_about",
					"std" => "#",
					"type" => "text");  
					
$options[] = array( "name" => "Archives",
					"desc" => "Links for designated navigation in header.",
					"id" => $shortname."_nav_archives",
					"std" => "#",
					"type" => "text");  
					
$options[] = array( "name" => "Subscribe",
					"desc" => "Links for designated navigation in header.",
					"id" => $shortname."_nav_subscribe",
					"std" => "#",
					"type" => "text");  
					
$options[] = array( "name" => "Contact",
					"desc" => "Links for designated navigation in header.",
					"id" => $shortname."_nav_contact",
					"std" => "#",
					"type" => "text"); 
					
$options[] = array( "name" => "Template Headings",
					"icon" => "header", 
					"type" => "heading");  	
					
$options[] = array( "name" => "General Prefix",
					"desc" => "Gets added before all page titles.",
					"id" => $shortname."_pageheading_prefix",
					"std" => "// ",
					"type" => "text"); 		
					
$options[] = array( "name" => "Homepage",
					"desc" => "",
					"id" => $shortname."_pageheading_home",
					"std" => "home",
					"type" => "textarea"); 
					
$options[] = array( "name" => "Index",
					"desc" => "",
					"id" => $shortname."_pageheading_index",
					"std" => "index",
					"type" => "textarea"); 
					
$options[] = array( "name" => "Single Post",
					"desc" => "",
					"id" => $shortname."_pageheading_single",
					"std" => "you're reading...",
					"type" => "textarea"); 				
								
$options[] = array( "name" => "Archives",
					"desc" => "",
					"id" => $shortname."_pageheading_archives",
					"std" => "archives",
					"type" => "textarea"); 	
					
$options[] = array( "name" => "Search Results",
					"desc" => "",
					"id" => $shortname."_pageheading_search",
					"std" => "here you go",
					"type" => "textarea"); 		 	
					
$options[] = array( "name" => "Author Archive",
					"desc" => "",
					"id" => $shortname."_pageheading_author",
					"std" => "author archive",
					"type" => "textarea"); 

$options[] = array( "name" => "404",
					"desc" => "",
					"id" => $shortname."_pageheading_404",
					"std" => "uh oh!",
					"type" => "textarea");  							
										

$options[] = array( "name" => "Dynamic Images",
					"icon" => "image", 
				    "type" => "heading");  
				    
				   
$options[] = array( "name" => "Enable WordPress Post Thumbnail Support",
					"desc" => "Use WordPress post thumbnail support to assign a post thumbnail.",
					"id" => $shortname."_post_image_support",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox"); 

$options[] = array( "name" => "Dynamically Resize Post Thumbnail",
					"desc" => "The post thumbnail will be dynamically resized using native WP resize functionality. <em>(Requires PHP 5.2+)</em>",
					"id" => $shortname."_pis_resize",
					"std" => "true",
					"class" => "hidden",
					"type" => "checkbox"); 									   
					
$options[] = array( "name" => "Hard Crop Post Thumbnail",
					"desc" => "The image will be cropped to match the target aspect ratio.",
					"id" => $shortname."_pis_hard_crop",
					"std" => "true",
					"class" => "hidden last",
					"type" => "checkbox"); 									   

$options[] = array( "name" => "Enable Dynamic Image Resizer",
					"desc" => "This will enable the thumb.php script. It dynamicaly resizes images on your site.",
					"id" => $shortname."_resize",
					"std" => "true",
					"type" => "checkbox");    
                    
$options[] = array( "name" => "Automatic Image Thumbs",
					"desc" => "If no image is specified in the 'image' custom field then the first uploaded post image is used.",
					"id" => $shortname."_auto_img",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Thumbnail Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"type" => array( 
									array(  'id' => $shortname. '_thumb_w',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_thumb_h',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Height')
								  ));
                                                                                                
$options[] = array( "name" => "Thumbnail Image alignment",
					"desc" => "Select how to align your thumbnails with posts.",
					"id" => $shortname."_thumb_align",
					"std" => "alignleft",
					"type" => "radio",
					"options" => $options_thumb_align); 

$options[] = array( "name" => "Show thumbnail in Single Posts",
					"desc" => "Show the attached image in the single post page.",
					"id" => $shortname."_thumb_single",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Single Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the image size. Max width is 576.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"class" => "hidden last",
					"type" => array( 
									array(  'id' => $shortname. '_single_w',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_single_h',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Height')
								  ));

$options[] = array( "name" => "Add thumbnail to RSS feed",
					"desc" => "Add the the image uploaded via your Custom Settings to your RSS feed",
					"id" => $shortname."_rss_thumb",
					"std" => "false",
					"type" => "checkbox");  
					
//Footer
$options[] = array( "name" => "Footer Customization",
					"icon" => "footer", 
                    "type" => "heading");
					
					
$options[] = array( "name" => "Custom Affiliate Link",
					"desc" => "Add an affiliate link to the WooThemes logo in the footer of the theme.",
					"id" => $shortname."_footer_aff_link",
					"std" => "",
					"type" => "text");	
									
$options[] = array( "name" => "Enable Custom Footer (Left)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_left",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Left)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_left_text",
					"class" => "hidden last",
					"std" => "<p></p>",
					"type" => "textarea");
						
$options[] = array( "name" => "Enable Custom Footer (Right)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_right",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Right)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_right_text",
					"class" => "hidden last",
					"std" => "<p></p>",
					"type" => "textarea");
							                                              

// Add extra options through function
if ( function_exists("woo_options_add") )
	$options = woo_options_add($options);

if ( get_option('woo_template') != $options) update_option('woo_template',$options);      
if ( get_option('woo_themename') != $themename) update_option('woo_themename',$themename);   
if ( get_option('woo_shortname') != $shortname) update_option('woo_shortname',$shortname);
if ( get_option('woo_manual') != $manualurl) update_option('woo_manual',$manualurl);

                                     
// Woo Metabox Options
$woo_metaboxes = array();
			
$woo_metaboxes[] = array (	"name" => "image",
							"label" => "Image",
							"type" => "upload",
							"desc" => "Upload file here...");

$woo_metaboxes[] = array (  "name"  => "embed",
				            "std"  => "",
				            "label" => "Embed Code",
				            "type" => "textarea",
				            "desc" => "Enter the video embed code for your video (YouTube, Vimeo or similar)");
							    
// Add extra metaboxes through function
if ( function_exists("woo_metaboxes_add") )
	$woo_metaboxes = woo_metaboxes_add($woo_metaboxes);
    
if ( get_option('woo_custom_template') != $woo_metaboxes) update_option('woo_custom_template',$woo_metaboxes);      

}

?>