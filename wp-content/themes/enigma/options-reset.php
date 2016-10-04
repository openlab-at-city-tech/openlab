<?php /**Options Reset Here**/
$wl_theme_options = weblizar_get_options();
/*
* General Settings
*/

function wl_reset_general_setting()
{
	$wl_theme_options['upload_image_logo']="";
	$wl_theme_options['height']=55;
	$wl_theme_options['width']=150;
	$wl_theme_options['_frontpage'] ="1";
	$wl_theme_options['upload_image_favicon']="";
	$wl_theme_options['text_title']="";
	$wl_theme_options['custom_css']="";		
	update_option('enigma_options',$wl_theme_options);
}

/*
* Slide image Settings
*/

function wl_reset_slide_image_setting()
{
	$ImageUrl = get_template_directory_uri()."/images/1.png";
	$ImageUrl2 = get_template_directory_uri()."/images/2.png";
	$ImageUrl3 = get_template_directory_uri()."/images/3.png";
	$wl_theme_options['slide_image_1'] = $ImageUrl;
	$wl_theme_options['slide_title_1'] = "Slide Title";
	$wl_theme_options['slide_desc_1'] = "Lorem Ipsum is simply dummy text of the printing";
	$wl_theme_options['slide_btn_text_1'] = "Read More";
	$wl_theme_options['slide_btn_link_1'] = "#";
	$wl_theme_options['slide_image_2'] = $ImageUrl2;
	$wl_theme_options['slide_title_2'] = "variations of passages";
	$wl_theme_options['slide_desc_2'] = "Contrary to popular belief, Lorem Ipsum is not simply random text";
	$wl_theme_options['slide_btn_text_2'] = "Read More";
	$wl_theme_options['slide_btn_link_2'] = "#";
	$wl_theme_options['slide_image_3'] = $ImageUrl3;
	$wl_theme_options['slide_title_3'] = "Contrary to popular ";
	$wl_theme_options['slide_desc_3'] = "Aldus PageMaker including versions of Lorem Ipsum, rutrum turpi";
	$wl_theme_options['slide_btn_text_3'] = "Read More";
	$wl_theme_options['slide_btn_link_3'] = "#";
	
	
	update_option('enigma_options', $wl_theme_options);
}

/*
* Site into Settings
*/

function wl_reset_portfolio_setting()
{	
	$ImageUrl4 = WL_TEMPLATE_DIR_URI ."/images/portfolio1.png";
	$ImageUrl5 = WL_TEMPLATE_DIR_URI ."/images/portfolio2.png";
	$ImageUrl6 = WL_TEMPLATE_DIR_URI ."/images/portfolio3.png";
	$ImageUrl7 = WL_TEMPLATE_DIR_URI ."/images/portfolio4.png";
	$ImageUrl8 = WL_TEMPLATE_DIR_URI ."/images/portfolio5.png";
	$ImageUrl9 = WL_TEMPLATE_DIR_URI ."/images/portfolio6.png";
	$wl_theme_options['portfolio_home'] = "";
	$wl_theme_options['port_heading']="Recent Works";
	$wl_theme_options['port_1_img']=$ImageUrl4;
	$wl_theme_options['port_2_img']=$ImageUrl5;
	$wl_theme_options['port_3_img']=$ImageUrl6;
	$wl_theme_options['port_4_img']=$ImageUrl7;
	$wl_theme_options['port_5_img']=$ImageUrl8;
	$wl_theme_options['port_6_img']=$ImageUrl9;
	$wl_theme_options['port_1_title']= "Bonorum ";
	$wl_theme_options['port_2_title']= "Content ";
	$wl_theme_options['port_3_title']= "dictionary ";
	$wl_theme_options['port_4_title']= "randomised ";	
	$wl_theme_options['port_1_link']="#";
	$wl_theme_options['port_2_link']="#";
	$wl_theme_options['port_3_link']="#";
	$wl_theme_options['port_4_link']="#";
	update_option('enigma_options', $wl_theme_options);
}

/*
* Service Settings
*/

function wl_reset_service_setting()
{
	$wl_theme_options['service_1_title']="Idea";
	$wl_theme_options['service_1_icons']="fa fa-google";
	$wl_theme_options['service_1_text']="There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in";
	$wl_theme_options['service_1_link']="";
	
	$wl_theme_options['service_2_title']="Records";
	$wl_theme_options['service_2_icons']="fa fa-database";
	$wl_theme_options['service_2_text']="There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in";
	$wl_theme_options['service_2_link']="#";
	
	$wl_theme_options['service_3_title']="WordPress";
	$wl_theme_options['service_3_icons']="fa fa-wordpress";
	$wl_theme_options['service_3_text']="There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in";
	$wl_theme_options['service_3_link']="#";
	
	update_option('enigma_options',$wl_theme_options);
}

/*
* Social Settings
*/

function wl_reset_social_setting()
{
	$wl_theme_options['footer_section_social_media_enbled']="1";
	$wl_theme_options['header_social_media_in_enabled']="1";	
	$wl_theme_options['twitter_link']="#";
	$wl_theme_options['fb_link']="#";
	$wl_theme_options['linkedin_link']="#";
	$wl_theme_options['youtube_link']="#";
	$wl_theme_options['dribble_link'] = "#";
	$wl_theme_options['email_id'] ="example@mymail.com";
	$wl_theme_options['phone_no'] ="0159753586";
	update_option('enigma_options', $wl_theme_options);
}

/*
* footer customizations Settings
*/

function wl_reset_footer_customizations_setting()
{
	$wl_theme_options['footer_customizations']="@ 2015 Weblizar Theme";
	$wl_theme_options['developed_by_text']="Theme Developed By";
	$wl_theme_options['developed_by_weblizar_text']="Weblizar";
	$wl_theme_options['developed_by_link']="http://weblizar.com/";
	update_option('enigma_options',$wl_theme_options);
}

function wl_reset_footer_footercall_setting () {
	$wl_theme_options['fc_home'] = '1';
	$wl_theme_options['fc_title']="Lorem Ipsum is simply dummy text of the printing and typesetting industry. ";
	$wl_theme_options['fc_btn_txt']="Weblizar";
	$wl_theme_options['fc_btn_link']="http://weblizar.com/";
	update_option('enigma_options',$wl_theme_options);
}

function wl_reset_footer_homeblog_setting() {
	
	$wl_theme_options['show_blog'] = '1';
	$wl_theme_options['blog_title']="Latest Blog";
	update_option('enigma_options',$wl_theme_options);
}
?>