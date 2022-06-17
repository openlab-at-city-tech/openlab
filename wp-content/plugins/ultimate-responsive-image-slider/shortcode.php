<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_shortcode( 'URIS', 'Wpfrank_URIS_Shortcode' );
function Wpfrank_URIS_Shortcode( $Id ) {

	ob_start();
	// slider JS CSS scripts
	wp_enqueue_script('wpfrank-uris-jquery-sliderPro-js', URIS_PLUGIN_URL.'assets/js/jquery.sliderPro.js', array('jquery'), '1.5.0', true);
	wp_enqueue_style('wpfrank-uris-slider-css', URIS_PLUGIN_URL.'assets/css/slider-pro.css');

	//Load Saved Ultimate Responsive Image Slider Settings
	if(!isset($Id['id'])) {
		$Id['id'] = "";
	} else {
		$WRIS_Id = $Id['id'];
		$WRIS_Gallery_Settings_Key = "WRIS_Gallery_Settings_".$WRIS_Id;
		
		$WRIS_Gallery_Settings = get_post_meta( $WRIS_Id, $WRIS_Gallery_Settings_Key, true);
		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Title'])) 
			$WRIS_L3_Slide_Title				= $WRIS_Gallery_Settings['WRIS_L3_Slide_Title'];
		else
			$WRIS_L3_Slide_Title				= 1;
		
		if(isset($WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Title'])) 
			$WRIS_L3_Show_Slide_Title			= $WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Title'];
		else
			$WRIS_L3_Show_Slide_Title			= 0;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Desc'])) 
			$WRIS_L3_Show_Slide_Desc			= $WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Desc'];
		else
			$WRIS_L3_Show_Slide_Desc			= 0;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Auto_Slideshow'])) 
			$WRIS_L3_Auto_Slideshow				= $WRIS_Gallery_Settings['WRIS_L3_Auto_Slideshow'];
		else
			$WRIS_L3_Auto_Slideshow				= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Transition']))
			$WRIS_L3_Transition					= $WRIS_Gallery_Settings['WRIS_L3_Transition'];
		else
			$WRIS_L3_Transition					= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Transition_Speed']))
			$WRIS_L3_Transition_Speed			= $WRIS_Gallery_Settings['WRIS_L3_Transition_Speed'];
		else
			$WRIS_L3_Transition_Speed			= 5000;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Order']))
			$WRIS_L3_Slide_Order				= $WRIS_Gallery_Settings['WRIS_L3_Slide_Order'];
		else
			$WRIS_L3_Slide_Order				= "ASC";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Distance']))
			$WRIS_L3_Slide_Distance				= $WRIS_Gallery_Settings['WRIS_L3_Slide_Distance'];
		else
			$WRIS_L3_Slide_Distance				= 5;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Sliding_Arrow']))
			$WRIS_L3_Sliding_Arrow				= $WRIS_Gallery_Settings['WRIS_L3_Sliding_Arrow'];
		else
			$WRIS_L3_Sliding_Arrow				= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Navigation']))
			$WRIS_L3_Slider_Navigation			= $WRIS_Gallery_Settings['WRIS_L3_Slider_Navigation'];
		else
			$WRIS_L3_Slider_Navigation			= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Position']))
			$WRIS_L3_Navigation_Position		= $WRIS_Gallery_Settings['WRIS_L3_Navigation_Position'];
		else
			$WRIS_L3_Navigation_Position		= "bottom";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Style']))
			$WRIS_L3_Thumbnail_Style			= $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Style'];
		else
			$WRIS_L3_Thumbnail_Style			= "border";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Width']))
			$WRIS_L3_Thumbnail_Width			= $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Width'];
		else
			$WRIS_L3_Thumbnail_Width			= 120;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Height']))
			$WRIS_L3_Thumbnail_Height			= $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Height'];
		else
			$WRIS_L3_Thumbnail_Height			= 120;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Button']))
			$WRIS_L3_Navigation_Button			= $WRIS_Gallery_Settings['WRIS_L3_Navigation_Button'];
		else
			$WRIS_L3_Navigation_Button			= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Width']))
			$WRIS_L3_Width						= $WRIS_Gallery_Settings['WRIS_L3_Width'];
		else
			$WRIS_L3_Width						= "custom";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Width']))
			$WRIS_L3_Slider_Width				= $WRIS_Gallery_Settings['WRIS_L3_Slider_Width'];
		else
			$WRIS_L3_Slider_Width				= 1000;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Height']))
			$WRIS_L3_Height						= $WRIS_Gallery_Settings['WRIS_L3_Height'];
		else
			$WRIS_L3_Height						= "custom";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Height']))
			$WRIS_L3_Slider_Height				= $WRIS_Gallery_Settings['WRIS_L3_Slider_Height'];
		else
			$WRIS_L3_Slider_Height				= 500;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Scale_Mode'])) 
			$WRIS_L3_Slider_Scale_Mode			= $WRIS_Gallery_Settings['WRIS_L3_Slider_Scale_Mode'];
		else
			$WRIS_L3_Slider_Scale_Mode			= "cover";
			
		if(isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Auto_Scale'])) 
			$WRIS_L3_Slider_Auto_Scale			= $WRIS_Gallery_Settings['WRIS_L3_Slider_Auto_Scale'];
		else
			$WRIS_L3_Slider_Auto_Scale			= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Font_Style']))
			$WRIS_L3_Font_Style					= $WRIS_Gallery_Settings['WRIS_L3_Font_Style'];
		else
			$WRIS_L3_Font_Style					= "Arial";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Title_Color']))
			$WRIS_L3_Title_Color				= $WRIS_Gallery_Settings['WRIS_L3_Title_Color'];
		else
			$WRIS_L3_Title_Color				= "#FFFFFF";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Title_BgColor']))
			$WRIS_L3_Title_BgColor				= $WRIS_Gallery_Settings['WRIS_L3_Title_BgColor'];
		else
			$WRIS_L3_Title_BgColor				= "#000000";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Desc_Color']))
			$WRIS_L3_Desc_Color					= $WRIS_Gallery_Settings['WRIS_L3_Desc_Color'];
		else
			$WRIS_L3_Desc_Color					= "#FFFFFF";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Desc_BgColor']))
			$WRIS_L3_Desc_BgColor				= $WRIS_Gallery_Settings['WRIS_L3_Desc_BgColor'];
		else
			$WRIS_L3_Desc_BgColor				= "#00000";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Color']))
			$WRIS_L3_Navigation_Color			= $WRIS_Gallery_Settings['WRIS_L3_Navigation_Color'];
		else
			$WRIS_L3_Navigation_Color			= "#FFFFFF";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Fullscreeen']))
			$WRIS_L3_Fullscreeen				= $WRIS_Gallery_Settings['WRIS_L3_Fullscreeen'];
		else
			$WRIS_L3_Fullscreeen				= 1;

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Custom_CSS']))
			$WRIS_L3_Custom_CSS					= $WRIS_Gallery_Settings['WRIS_L3_Custom_CSS'];
		else
			$WRIS_L3_Custom_CSS					= "";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Bullets_Color']))
			$WRIS_L3_Navigation_Bullets_Color	= $WRIS_Gallery_Settings['WRIS_L3_Navigation_Bullets_Color'];
		else
			$WRIS_L3_Navigation_Bullets_Color	= "#000000";

		if(isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Pointer_Color']))
			$WRIS_L3_Navigation_Pointer_Color	= $WRIS_Gallery_Settings['WRIS_L3_Navigation_Pointer_Color'];
		else
			$WRIS_L3_Navigation_Pointer_Color	= "#000000";
	}
	//Load Slider Layout Output
	require("layout.php");
	wp_reset_query();
	return ob_get_clean();
}
?>