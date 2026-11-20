<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_shortcode( 'URIS', 'Wpfrank_URIS_Shortcode' );
function Wpfrank_URIS_Shortcode( $Id ) {

	// Normalize attributes
	if ( ! is_array( $Id ) ) {
		$Id = array();
	}

	if ( empty( $Id['id'] ) ) {
		return '';
	}

	$WRIS_Id = absint( $Id['id'] );
	if ( ! $WRIS_Id ) {
		return '';
	}
	$Id['id'] = $WRIS_Id;

	ob_start();
	// slider frontend JS CSS
	wp_enqueue_script('wpfrank-uris-js');
	wp_enqueue_style('wpfrank-uris-css');

	//Load Saved Ultimate Responsive Image Slider Settings
	$WRIS_Gallery_Settings_Key = "WRIS_Gallery_Settings_".$WRIS_Id;
	$WRIS_Gallery_Settings = get_post_meta( $WRIS_Id, $WRIS_Gallery_Settings_Key, true);
	if ( is_string( $WRIS_Gallery_Settings ) ) {
		$WRIS_Gallery_Settings = maybe_unserialize( $WRIS_Gallery_Settings );
	}
	if ( ! is_array( $WRIS_Gallery_Settings ) ) {
		$WRIS_Gallery_Settings = array();
	}

	$WRIS_L3_Slide_Title				= isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Title']) ? $WRIS_Gallery_Settings['WRIS_L3_Slide_Title'] : 0;
	$WRIS_L3_Show_Slide_Title			= isset($WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Title']) ? $WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Title'] : 0;
	$WRIS_L3_Show_Slide_Desc			= isset($WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Desc']) ? $WRIS_Gallery_Settings['WRIS_L3_Show_Slide_Desc'] : 0;
	$WRIS_L3_Auto_Slideshow				= isset($WRIS_Gallery_Settings['WRIS_L3_Auto_Slideshow']) ? $WRIS_Gallery_Settings['WRIS_L3_Auto_Slideshow'] : 1;
	$WRIS_L3_Transition					= isset($WRIS_Gallery_Settings['WRIS_L3_Transition']) ? $WRIS_Gallery_Settings['WRIS_L3_Transition'] : 1;
	$WRIS_L3_Transition_Speed			= isset($WRIS_Gallery_Settings['WRIS_L3_Transition_Speed']) ? $WRIS_Gallery_Settings['WRIS_L3_Transition_Speed'] : 5000;
	$WRIS_L3_Slide_Order				= isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Order']) ? $WRIS_Gallery_Settings['WRIS_L3_Slide_Order'] : "ASC";
	$WRIS_L3_Slide_Distance				= isset($WRIS_Gallery_Settings['WRIS_L3_Slide_Distance']) ? $WRIS_Gallery_Settings['WRIS_L3_Slide_Distance'] : 5;
	$WRIS_L3_Sliding_Arrow				= isset($WRIS_Gallery_Settings['WRIS_L3_Sliding_Arrow']) ? $WRIS_Gallery_Settings['WRIS_L3_Sliding_Arrow'] : 1;
	$WRIS_L3_Slider_Navigation			= isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Navigation']) ? $WRIS_Gallery_Settings['WRIS_L3_Slider_Navigation'] : 1;
	$WRIS_L3_Navigation_Position		= isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Position']) ? $WRIS_Gallery_Settings['WRIS_L3_Navigation_Position'] : "bottom";
	$WRIS_L3_Thumbnail_Style			= isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Style']) ? $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Style'] : "border";
	$WRIS_L3_Thumbnail_Width			= isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Width']) ? $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Width'] : 120;
	$WRIS_L3_Thumbnail_Height			= isset($WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Height']) ? $WRIS_Gallery_Settings['WRIS_L3_Thumbnail_Height'] : 120;
	$WRIS_L3_Navigation_Button			= isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Button']) ? $WRIS_Gallery_Settings['WRIS_L3_Navigation_Button'] : 1;
	$WRIS_L3_Width						= isset($WRIS_Gallery_Settings['WRIS_L3_Width']) ? $WRIS_Gallery_Settings['WRIS_L3_Width'] : "custom";
	$WRIS_L3_Slider_Width				= isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Width']) ? $WRIS_Gallery_Settings['WRIS_L3_Slider_Width'] : 1000;
	$WRIS_L3_Height						= isset($WRIS_Gallery_Settings['WRIS_L3_Height']) ? $WRIS_Gallery_Settings['WRIS_L3_Height'] : "custom";
	$WRIS_L3_Slider_Height				= isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Height']) ? $WRIS_Gallery_Settings['WRIS_L3_Slider_Height'] : 500;
	$WRIS_L3_Slider_Scale_Mode			= isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Scale_Mode']) ? $WRIS_Gallery_Settings['WRIS_L3_Slider_Scale_Mode'] : "cover";
	$WRIS_L3_Slider_Auto_Scale			= isset($WRIS_Gallery_Settings['WRIS_L3_Slider_Auto_Scale']) ? $WRIS_Gallery_Settings['WRIS_L3_Slider_Auto_Scale'] : 1;
	$WRIS_L3_Font_Style					= isset($WRIS_Gallery_Settings['WRIS_L3_Font_Style']) ? $WRIS_Gallery_Settings['WRIS_L3_Font_Style'] : "Arial";
	$WRIS_L3_Title_Color				= isset($WRIS_Gallery_Settings['WRIS_L3_Title_Color']) ? $WRIS_Gallery_Settings['WRIS_L3_Title_Color'] : "#FFFFFF";
	$WRIS_L3_Title_BgColor				= isset($WRIS_Gallery_Settings['WRIS_L3_Title_BgColor']) ? $WRIS_Gallery_Settings['WRIS_L3_Title_BgColor'] : "#000000";
	$WRIS_L3_Desc_Color					= isset($WRIS_Gallery_Settings['WRIS_L3_Desc_Color']) ? $WRIS_Gallery_Settings['WRIS_L3_Desc_Color'] : "#FFFFFF";
	$WRIS_L3_Desc_BgColor				= isset($WRIS_Gallery_Settings['WRIS_L3_Desc_BgColor']) ? $WRIS_Gallery_Settings['WRIS_L3_Desc_BgColor'] : "#000000";
	$WRIS_L3_Navigation_Color			= isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Color']) ? $WRIS_Gallery_Settings['WRIS_L3_Navigation_Color'] : "#FFFFFF";
	$WRIS_L3_Fullscreeen				= isset($WRIS_Gallery_Settings['WRIS_L3_Fullscreeen']) ? $WRIS_Gallery_Settings['WRIS_L3_Fullscreeen'] : 1;
	$WRIS_L3_Custom_CSS					= isset($WRIS_Gallery_Settings['WRIS_L3_Custom_CSS']) ? $WRIS_Gallery_Settings['WRIS_L3_Custom_CSS'] : "";
	$WRIS_L3_Navigation_Bullets_Color	= isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Bullets_Color']) ? $WRIS_Gallery_Settings['WRIS_L3_Navigation_Bullets_Color'] : "#000000";
	$WRIS_L3_Navigation_Pointer_Color	= isset($WRIS_Gallery_Settings['WRIS_L3_Navigation_Pointer_Color']) ? $WRIS_Gallery_Settings['WRIS_L3_Navigation_Pointer_Color'] : "#000000";

	//Load Slider Layout Output
	require("layout.php");
	wp_reset_postdata();
	return ob_get_clean();
}
