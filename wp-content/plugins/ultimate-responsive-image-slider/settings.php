<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Load Saved Settings
 */
$PostId = $post->ID;
$WRIS_Gallery_Settings_Key = "WRIS_Gallery_Settings_".$PostId;
$WRIS_Gallery_Settings = get_post_meta( $PostId, $WRIS_Gallery_Settings_Key, true);

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
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	var editor = CodeMirror.fromTextArea(document.getElementById("wl-l3-custom-css"), {
		lineWrapping: true,
		lineNumbers: true,
		styleActiveLine: true,
		matchBrackets: true,
		hint:true,
		theme : 'blackboard',
		extraKeys: {"Ctrl-Space": "autocomplete"},
	});
	jQuery(window).scroll(function(){
		if (jQuery(this).scrollTop() < 200) {
			jQuery('#smoothup') .fadeOut();
		} else {
			jQuery('#smoothup') .fadeIn();
		}
	});
	jQuery('#smoothup').on('click', function(){
		jQuery('html, body').animate({scrollTop:0}, 'fast');
		return false;
	});
});
</script>
<style>
.custnote{
	background-color: rgba(23, 31, 22, 0.64);
	color: #fff;
	width: 348px;
	border-radius: 5px;
	padding-right: 5px;
	padding-left: 5px;
	padding-top: 2px;
	padding-bottom: 2px;
}
.thumb-pro th, .thumb-pro label, .thumb-pro h3, .thumb-pro {
	color:#31a3dd !important;
	font-weight:bold;
}
.caro-pro th, .caro-pro label, .caro-pro h3, .caro-pro {
	color:#DA5766 !important;
	font-weight:bold;
}
#smoothup {
	height: 50px;
	width: 50px;
	position:fixed;
	bottom:50px;
	right:250px;
	text-indent:-9999px;
	display:none;
	background: url("<?php echo esc_url(URIS_PLUGIN_URL.'assets/img/up.png'); ?>");
	-webkit-transition-duration: 0.4s;
	-moz-transition-duration: 0.4s; transition-duration: 0.4s;
}
#smoothup:hover {
	-webkit-transform: rotate(360deg) }
	background: url('') no-repeat;
}
</style>
<?php require_once("tooltip.php"); ?>
<input type="hidden" id="wl_action" name="wl_action" value="wl-save-settings">
<table class="form-table">
	<tbody>
		<tr id="L3">
			<th scope="row"><label><?php _e('Display Slider Post Title', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slide_Title)) $WRIS_L3_Slide_Title = 1; ?>
				<input type="radio" name="wl-l3-slide-title" id="wl-l3-slide-title" value="1" <?php if($WRIS_L3_Slide_Title == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-slide-title" id="wl-l3-slide-title" value="0" <?php if($WRIS_L3_Slide_Title == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show/hide slide title above slider', 'ultimate-responsive-image-slider'); ?>.
					<a href="#" id="p1" data-tooltip="#s1"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Display Slide Title', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Show_Slide_Title)) $WRIS_L3_Show_Slide_Title = 0; ?>
				<input type="radio" name="wl-l3-show-slide-title" id="wl-l3-show-slide-title" value="1" <?php if($WRIS_L3_Show_Slide_Title == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-show-slide-title" id="wl-l3-show-slide-title" value="0" <?php if($WRIS_L3_Show_Slide_Title == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show/hide slide title over slides.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p2" data-tooltip="#s2"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Title Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Title_Color)) $WRIS_L3_Title_Color = "#FFFFFF"; ?>
				<input id="wl-l3-title-color" name="wl-l3-title-color" type="text" value="<?php echo esc_attr($WRIS_L3_Title_Color); ?>" class="my-color-field" data-default-color="#000000" />
				<p class="description">
					<?php _e('Select a color to set slide title color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p3" data-tooltip="#s3"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Title Background Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Title_BgColor)) $WRIS_L3_Title_BgColor = "#000000"; ?>
				<input id="wl-l3-title-bgcolor" name="wl-l3-title-bgcolor" type="text" value="<?php echo esc_attr($WRIS_L3_Title_BgColor); ?>" class="my-color-field" data-default-color="#ffffff" />
				<p class="description">
					<?php _e('Select a color to set slide title background color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p4" data-tooltip="#s4"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Display Slide Description', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Show_Slide_Desc)) $WRIS_L3_Show_Slide_Desc = 0; ?>
				<input type="radio" name="wl-l3-show-slide-desc" id="wl-l3-show-slide-desc" value="1" <?php if($WRIS_L3_Show_Slide_Desc == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-show-slide-desc" id="wl-l3-show-slide-desc" value="0" <?php if($WRIS_L3_Show_Slide_Desc == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show/hide slide description over slides.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p5" data-tooltip="#s5"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Description Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Desc_Color)) $WRIS_L3_Desc_Color = "#FFFFFF"; ?>
				<input id="wl-l3-desc-color" name="wl-l3-desc-color" type="text" value="<?php echo esc_attr($WRIS_L3_Desc_Color); ?>" class="my-color-field" data-default-color="#ffffff" />
				<p class="description">
					<?php _e('Select a color to set slide description color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p6" data-tooltip="#s6"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Description Background Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Desc_BgColor)) $WRIS_L3_Desc_BgColor = "#000000"; ?>
				<input id="wl-l3-desc-bgcolor" name="wl-l3-desc-bgcolor" type="text" value="<?php echo esc_attr($WRIS_L3_Desc_BgColor); ?>" class="my-color-field" data-default-color="#000000" />
				<p class="description">
					<?php _e('Select a color to set slide description background color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p7" data-tooltip="#s7"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Auto Play Slide Show', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Auto_Slideshow)) $WRIS_L3_Auto_Slideshow = 1; ?>
				<input type="radio" name="wl-l3-auto-slide" id="wl-l3-auto-slide" value="1" <?php if($WRIS_L3_Auto_Slideshow == 1 ) { echo esc_attr("checked"); } ?>> <?php _e('Yes', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-auto-slide" id="wl-l3-auto-slide" value="2" <?php if($WRIS_L3_Auto_Slideshow == 2 ) { echo esc_attr("checked"); } ?>> <?php _e('Yes with Stop on Mouse Hover', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-auto-slide" id="wl-l3-auto-slide" value="3" <?php if($WRIS_L3_Auto_Slideshow == 3 ) { echo esc_attr("checked"); } ?>> <?php _e('No', 'ultimate-responsive-image-slider'); ?>
				<p class="description">
					<?php _e('Select Yes/No option to auto slide enable or disable into slider.', 'ultimate-responsive-image-slider'); ?>
					<!--<a href="#" id="p8" data-tooltip="#s8"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Transition', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Transition)) $WRIS_L3_Transition = 1; ?>
				<input type="radio" name="wl-l3-transition" id="wl-l3-transition" value="1" <?php if($WRIS_L3_Transition == 1 ) { echo esc_attr("checked"); } ?>> Fade &nbsp;&nbsp;
				<input type="radio" name="wl-l3-transition" id="wl-l3-transition" value="0" <?php if($WRIS_L3_Transition == 0 ) { echo esc_attr("checked"); } ?>> Slide
				<p class="description">
					<?php _e('Select a transition effect you want to apply on slides.', 'ultimate-responsive-image-slider'); ?>
					<!--<a href="#" id="p9" data-tooltip="#s9"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>		
		<tr id="L3">
			<th scope="row"><label><?php _e('Auto play Slider Speed', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Transition_Speed)) $WRIS_L3_Transition_Speed = 5000; ?>
				<input class="uris-slider" name="wl-l3-transition-speed" id="wl-l3-transition-speed" type="range" min="1000" max="60000" step="1000" value="<?php echo esc_attr($WRIS_L3_Transition_Speed); ?>" data-rangeSlider>
				<span id="uris-range-val"></span>
				<p class="description">
					<?php _e('Set your desired slider speed of slides. Default speed is 5 Second.', 'ultimate-responsive-image-slider'); ?>
					<!--<a href="#" id="p10" data-tooltip="#s10"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>
		
		<script>
		var slider = document.getElementById("wl-l3-transition-speed");
		var output = document.getElementById("uris-range-val");
		
		var x = slider.value;
		var y = x/1000;
		output.innerHTML = y;
		
		slider.oninput = function() {
			var x = slider.value;
			var y = x/1000;
			output.innerHTML = y;
		}
		</script>
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Order', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slide_Order)) $WRIS_L3_Slide_Order = "ASC"; ?>
				<input type="radio" name="wl-l3-slide-order" id="wl-l3-slide-order" value="ASC" <?php if($WRIS_L3_Slide_Order == "ASC" ) { echo esc_attr("checked"); } ?>> <?php _e('Ascending', 'ultimate-responsive-image-slider'); ?>  &nbsp;&nbsp;
				<input type="radio" name="wl-l3-slide-order" id="wl-l3-slide-order" value="DESC" <?php if($WRIS_L3_Slide_Order == "DESC" ) { echo esc_attr("checked"); } ?>> <?php _e('Descending', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-slide-order" id="wl-l3-slide-order" value="shuffle" <?php if($WRIS_L3_Slide_Order == "shuffle" ) { echo esc_attr("checked"); } ?>> <?php _e('Random', 'ultimate-responsive-image-slider'); ?>
				<p class="description">
					<?php _e('Select a slide order you want to apply on slides', 'ultimate-responsive-image-slider'); ?>.
					<!--<a href="#" id="p11" data-tooltip="#s11"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Distance Between Slide', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slide_Distance)) $WRIS_L3_Slide_Distance = 5; ?>
				<select name="wl-l3-slide-distance" id="wl-l3-slide-distance">
					<option value="0" <?php if($WRIS_L3_Slide_Distance == 0) echo esc_attr("selected=selected");?>>0</option>
					<option value="5" <?php if($WRIS_L3_Slide_Distance == 5) echo esc_attr("selected=selected");?>>5</option>
					<option value="10" <?php if($WRIS_L3_Slide_Distance == 10) echo esc_attr("selected=selected");?>>10</option>
					<option value="15" <?php if($WRIS_L3_Slide_Distance == 15) echo esc_attr("selected=selected");?>>15</option>
					<option value="20" <?php if($WRIS_L3_Slide_Distance == 20) echo esc_attr("selected=selected");?>>20</option>
					<option value="25" <?php if($WRIS_L3_Slide_Distance == 25) echo esc_attr("selected=selected");?>>25</option>
				</select>
				<p class="description">
					<?php _e('Set a gap between all slides. Range 0 to 25. Works when Slide Transition setting selected to Slide.', 'ultimate-responsive-image-slider'); ?>&nbsp;
					<a href="#" id="p12" data-tooltip="#s12"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Show Thumbnail', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slider_Navigation)) $WRIS_L3_Slider_Navigation = 1; ?>
				<input type="radio" name="wl-l3-navigation" id="wl-l3-navigation" value="1" <?php if($WRIS_L3_Slider_Navigation == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-navigation" id="wl-l3-navigation" value="0" <?php if($WRIS_L3_Slider_Navigation == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show or hide thumbnail based navigation under slides.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p13" data-tooltip="#s13"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Thumbnail Position', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Navigation_Position)) $WRIS_L3_Navigation_Position = "bottom"; ?>
				<input type="radio" name="wl-l3-navigation-position" id="wl-l3-navigation-position" value="top" <?php if($WRIS_L3_Navigation_Position == "top" ) { echo esc_attr("checked"); } ?>> <?php _e('Top', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-navigation-position" id="wl-l3-navigation-position" value="bottom" <?php if($WRIS_L3_Navigation_Position == "bottom" ) { echo esc_attr("checked"); } ?>> <?php _e('Bottom', 'ultimate-responsive-image-slider'); ?>
				<p class="description">
					<?php _e('Select a thumbnail position to show above or below the slider.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p14" data-tooltip="#s14"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Selected Thumbnail Style', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Thumbnail_Style)) $WRIS_L3_Thumbnail_Style = "border"; ?>
				<input type="radio" name="wl-l3-thumbnail-style" id="wl-l3-thumbnail-style" value="border" <?php if($WRIS_L3_Thumbnail_Style == "border" ) { echo esc_attr("checked"); } ?>> <?php _e('Border', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-thumbnail-style" id="wl-l3-thumbnail-style" value="pointer" <?php if($WRIS_L3_Thumbnail_Style == "pointer" ) { echo esc_attr("checked"); } ?>> <?php _e('Pointer', 'ultimate-responsive-image-slider'); ?>
				<p class="description">
					<?php _e('Select a style to apply on select thumbnails.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p15" data-tooltip="#s15"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Selected Thumbnail Style Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Navigation_Pointer_Color)) $WRIS_L3_Navigation_Pointer_Color = "#000000"; ?>
				<input id="wl-l3-navigation-pointer-color" name="wl-l3-navigation-pointer-color" type="text" value="<?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?>" class="my-color-field" data-default-color="#000000" />
				<p class="description">
					<?php _e('Select a color to set on selected thumbnail style color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p16" data-tooltip="#s16"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Thumbnail Resize', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Thumbnail_Width)) $WRIS_L3_Thumbnail_Width = "120"; ?>
				<?php if(!isset($WRIS_L3_Thumbnail_Height)) $WRIS_L3_Thumbnail_Height = "120"; ?>
				<?php _e('Width', 'ultimate-responsive-image-slider'); ?> <input type="text" name="wl-l3-navigation-width" id="wl-l3-navigation-width" value="<?php echo esc_attr($WRIS_L3_Thumbnail_Width); ?>"> &nbsp;&nbsp;
				<?php _e('Height', 'ultimate-responsive-image-slider'); ?> <input type="text" name="wl-l3-navigation-height" id="wl-l3-navigation-height" value="<?php echo esc_attr($WRIS_L3_Thumbnail_Height); ?>">
				<p class="description">
					<?php _e('Set custom height and width for thumbnails.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p17" data-tooltip="#s17"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slider Width', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slider_Width)) $WRIS_L3_Slider_Width = 1000; ?>
				<?php if(!isset($WRIS_L3_Width)) $WRIS_L3_Width = "custom"; ?>
				<input type="radio" name="wl-l3-width" id="wl-l3-width" value="100%" <?php if($WRIS_L3_Width == "100%" ) { echo esc_attr("checked"); } ?>> <?php _e('100% Width', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-width" id="wl-l3-width" value="fullWidth" <?php if($WRIS_L3_Width == "fullWidth" ) { echo esc_attr("checked"); } ?>> <?php _e('Full Width', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-width" id="wl-l3-width" value="custom" <?php if($WRIS_L3_Width == "custom" ) { echo esc_attr("checked"); } ?>> <?php _e('Custom', 'ultimate-responsive-image-slider'); ?>
				<input type="text" name="wl-l3-slider-width" id="wl-l3-slider-width" value="<?php echo esc_attr($WRIS_L3_Slider_Width); ?>">
				<p class="description">
					<?php _e('Enter your desired width for slider. Default width is 1000px.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p18" data-tooltip="#s18"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slider Height', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slider_Height)) $WRIS_L3_Slider_Height = 500; ?>
				<?php if(!isset($WRIS_L3_Height)) $WRIS_L3_Height = "custom"; ?>
				<input type="radio" name="wl-l3-height" id="wl-l3-height" value="auto" <?php if($WRIS_L3_Height == "auto" ) { echo esc_attr("checked"); } ?>> <?php _e('Auto Height', 'ultimate-responsive-image-slider'); ?> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-height" id="wl-l3-height" value="custom" <?php if($WRIS_L3_Height == "custom" ) { echo esc_attr("checked"); } ?>> <?php _e('Custom', 'ultimate-responsive-image-slider'); ?>
				<input type="text" name="wl-l3-slider-height" id="wl-l3-slider-height" value="<?php echo esc_attr($WRIS_L3_Slider_Height); ?>">
				<p class="description">
					<?php _e('Enter your desired height for slider. Default height is 500px.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p19" data-tooltip="#s19"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Scale Mode', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slider_Scale_Mode)) $WRIS_L3_Slider_Scale_Mode = "cover"; ?>
				<select name="wl-l3-slider_scale_mode" id="wl-l3-slider_scale_mode" class="standard-dropdown">
					<optgroup label="Select Slider Scale Mode">
						<option value="cover" <?php if($WRIS_L3_Slider_Scale_Mode == "cover" ) { echo esc_attr("selected=selected"); } ?>><?php _e('Cover', 'ultimate-responsive-image-slider'); ?></option>
						<option value="contain" <?php if($WRIS_L3_Slider_Scale_Mode == "contain" ) { echo esc_attr("selected=selected"); } ?>><?php _e('Contain', 'ultimate-responsive-image-slider'); ?></option>
						<option value="exact" <?php if($WRIS_L3_Slider_Scale_Mode == "exact" ) { echo esc_attr("selected=selected"); } ?>><?php _e('Exact', 'ultimate-responsive-image-slider'); ?></option>
						<option value="none" <?php if($WRIS_L3_Slider_Scale_Mode == "none" ) { echo esc_attr("selected=selected"); } ?>><?php _e('None', 'ultimate-responsive-image-slider'); ?></option>
					</optgroup>
				</select>
				<p class="description">
					<?php _e('COVER will scale and crop the image so that it fills the entire slide.', 'ultimate-responsive-image-slider'); ?><br />
					<?php _e('CONTAIN will keep the entire image visible inside the slide.', 'ultimate-responsive-image-slider'); ?><br />
					<?php _e('EXACT will match the size of the image to the size of the slide.', 'ultimate-responsive-image-slider'); ?><br />
					<?php _e('NONE will leave the image to its original size.', 'ultimate-responsive-image-slider'); ?>
					<!--<a href="#" id="p20" data-tooltip="#s20"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Slide Auto Scale Up', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Slider_Auto_Scale)) $WRIS_L3_Slider_Auto_Scale = 1; ?>
				<input type="radio" name="wl-l3-slider-auto-scale" id="wl-l3-slider-auto-scale" value="1" <?php if($WRIS_L3_Slider_Auto_Scale == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> 
				<input type="radio" name="wl-l3-slider-auto-scale" id="wl-l3-slider-auto-scale" value="0" <?php if($WRIS_L3_Slider_Auto_Scale == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('If the slide can be scaled up more than its original size.', 'ultimate-responsive-image-slider'); ?>
					<!--<a href="#" id="p21" data-tooltip="#s21"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>-->
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Show Navigation Arrow', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Sliding_Arrow)) $WRIS_L3_Sliding_Arrow = 1; ?>
				<input type="radio" name="wl-l3-sliding-arrow" id="wl-l3-sliding-arrow" value="1" <?php if($WRIS_L3_Sliding_Arrow == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-sliding-arrow" id="wl-l3-sliding-arrow" value="0" <?php if($WRIS_L3_Sliding_Arrow == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show or hide arrows on mouse hover on slide.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p22" data-tooltip="#s22"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Navigation Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Navigation_Color)) $WRIS_L3_Navigation_Color = "#FFFFFF"; ?>
				<input id="wl-l3-navigation-color" name="wl-l3-navigation-color" type="text" value="<?php echo esc_attr($WRIS_L3_Navigation_Color); ?>" class="my-color-field" data-default-color="#ffffff" />
				<p class="description">
					<?php _e('Select a color to set navigation arrow and full screen icon color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p23" data-tooltip="#s23"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Show Navigation Bullets', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Navigation_Button)) $WRIS_L3_Navigation_Button = 1; ?>
				<input type="radio" name="wl-l3-navigation-button" id="wl-l3-navigation-button" value="1" <?php if($WRIS_L3_Navigation_Button == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-navigation-button" id="wl-l3-navigation-button" value="0" <?php if($WRIS_L3_Navigation_Button == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option to show or hide slider navigation buttons under image slider.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p24" data-tooltip="#s24"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Navigation Bullets Color', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Navigation_Bullets_Color)) $WRIS_L3_Navigation_Bullets_Color = "#000000"; ?>
				<input id="wl-l3-navigation-bullets-color" name="wl-l3-navigation-bullets-color" type="text" value="<?php echo esc_attr($WRIS_L3_Navigation_Bullets_Color); ?>" class="my-color-field" data-default-color="#000000" />
				<p class="description">
					<?php _e('Select a color to set navigation bullets color.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p25" data-tooltip="#s25"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Full Screen Slide Show', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Fullscreeen)) $WRIS_L3_Fullscreeen = 1; ?>
				<input type="radio" name="wl-l3-fullscreen" id="wl-l3-fullscreen" value="1" <?php if($WRIS_L3_Fullscreeen == 1 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-check fa-2x"></i> &nbsp;&nbsp;
				<input type="radio" name="wl-l3-fullscreen" id="wl-l3-fullscreen" value="0" <?php if($WRIS_L3_Fullscreeen == 0 ) { echo esc_attr("checked"); } ?>> <i class="fa fa-times fa-2x"></i>
				<p class="description">
					<?php _e('Select Yes/No option for full screen slide show.', 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p26" data-tooltip="#s26"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e("Fonts", 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Font_Style)) $WRIS_L3_Font_Style = "Arial";?>
				<select name="wl-l3-font-style" id="wl-l3-font-style" class="standard-dropdown" >
					<optgroup label="Default Fonts">
						<option value="Arial" <?php selected($WRIS_L3_Font_Style, 'Arial' ); ?>>Arial</option>
						<option value="Arial Black" <?php selected($WRIS_L3_Font_Style, 'Arial Black' ); ?>>Arial Black</option>
						<option value="Courier New" <?php selected($WRIS_L3_Font_Style, 'Courier New' ); ?>>Courier New</option>
						<option value="cursive" <?php selected($WRIS_L3_Font_Style, 'cursive' ); ?>>Cursive</option>
						<option value="fantasy" <?php selected($WRIS_L3_Font_Style, 'fantasy' ); ?>>Fantasy</option>
						<option value="Georgia" <?php selected($WRIS_L3_Font_Style, 'Georgia' ); ?>>Georgia</option>
						<option value="Grande"<?php selected($WRIS_L3_Font_Style, 'Grande' ); ?>>Grande</option>
						<option value="Helvetica Neue" <?php selected($WRIS_L3_Font_Style, 'Helvetica Neue' ); ?>>Helvetica Neue</option>
						<option value="Impact" <?php selected($WRIS_L3_Font_Style, 'Impact' ); ?>>Impact</option>
						<option value="Lucida" <?php selected($WRIS_L3_Font_Style, 'Lucida' ); ?>>Lucida</option>
						<option value="Lucida Console"<?php selected($WRIS_L3_Font_Style, 'Lucida Console' ); ?>>Lucida Console</option>
						<option value="monospace" <?php selected($WRIS_L3_Font_Style, 'monospace' ); ?>>Monospace</option>
						<option value="Open Sans" <?php selected($WRIS_L3_Font_Style, 'Open Sans' ); ?>>Open Sans</option>
						<option value="Palatino" <?php selected($WRIS_L3_Font_Style, 'Palatino' ); ?>>Palatino</option>
						<option value="sans" <?php selected($WRIS_L3_Font_Style, 'sans' ); ?>>Sans</option>
						<option value="sans-serif" <?php selected($WRIS_L3_Font_Style, 'sans-serif' ); ?>>Sans-Serif</option>
						<option value="Tahoma" <?php selected($WRIS_L3_Font_Style, 'Tahoma' ); ?>>Tahoma</option>
						<option value="Times New Roman"<?php selected($WRIS_L3_Font_Style, 'Times New Roman' ); ?>>Times New Roman</option>
						<option value="Trebuchet MS" <?php selected($WRIS_L3_Font_Style, 'Trebuchet MS' ); ?>>Trebuchet MS</option>
						<option value="Verdana" <?php selected($WRIS_L3_Font_Style, 'Verdana' ); ?>>Verdana</option>
					</optgroup>
				</select>
				<p class="description">
					<?php _e("Choose a font to apply on slide title and description.", 'ultimate-responsive-image-slider'); ?>
					<a href="#" id="p27" data-tooltip="#s27"><?php _e('Preview', 'ultimate-responsive-image-slider'); ?></a>
				</p>
				
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label><?php _e('Custom CSS', 'ultimate-responsive-image-slider'); ?></label></th>
			<td>
				<?php if(!isset($WRIS_L3_Custom_CSS)) $WRIS_L3_Custom_CSS = ""; ?>
				<textarea name="wl-l3-custom-css" id="wl-l3-custom-css" rows="5" cols="75"><?php echo esc_attr($WRIS_L3_Custom_CSS); ?></textarea>
				<p class="description">
					<?php _e('Enter any custom CSS you want to apply on this slider into text area filed.', 'ultimate-responsive-image-slider'); ?><br>
				</p>
				<p class="custnote">Note: Please Do Not Use <b>Style</b> Tag With Custom CSS</p>
			</td>
		</tr>
		
		<tr id="L3">
			<th scope="row"><label>Review Request</label></th>
			<td>
				<p>
					I hope you like my plugin & It will be useful to make your website beautiful.<br />
					I need your support and favor to always continue this plugin development.<br />
					Please post a good review for my work and support to more encourage me.<br />
				</p>
				<p>
					<a id="review-request" href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider/reviews/#new-post" target="_blank" class="button button-primary button-large">Write A Review</a>
				</p>
			</td>
		</tr>
	</tbody>
</table>
<a href="#top" id="smoothup" title="Back to top"></a>