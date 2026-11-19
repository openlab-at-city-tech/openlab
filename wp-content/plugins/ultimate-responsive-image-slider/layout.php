<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'uris_hex2rgba' ) ) {
	function uris_hex2rgba( $color, $opacity = 0.85 ) {
		$color = trim( (string) $color );
		if ( empty( $color ) ) {
			return "rgba(0, 0, 0, {$opacity})";
		}
		if ( $color[0] === '#' ) {
			$color = substr( $color, 1 );
		}
		if ( strlen( $color ) === 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) === 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return "rgba(0, 0, 0, {$opacity})";
		}
		$rgb = array_map( 'hexdec', $hex );
		return 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
	}
} // Exit if accessed directly

//Load All Slides
$URIS_CPT_Name = "ris_gallery";
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! isset( $WRIS_L3_Slide_Order ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$WRIS_L3_Slide_Order = "ASC";
}
$uris_slider_id = ( isset( $Id ) && is_array( $Id ) && ! empty( $Id['id'] ) ) ? absint( $Id['id'] ) : 0;
if ( ! $uris_slider_id ) {
	return;
}

$uris_all_slides = array(  'p' => $uris_slider_id, 'post_type' => $URIS_CPT_Name, 'orderby' => $WRIS_L3_Slide_Order);
$uris_loop = new WP_Query( $uris_all_slides );

while ( $uris_loop->have_posts() ) : $uris_loop->the_post();
//get the post id
$post_id = get_the_ID();

//Get All Slides Details Post Meta
$URIS_All_Slide_Ids = get_post_meta( get_the_ID(), 'ris_all_photos_details', true);
if ( is_string( $URIS_All_Slide_Ids ) ) {
	$URIS_All_Slide_Ids = maybe_unserialize( $URIS_All_Slide_Ids );
}
if ( ! is_array( $URIS_All_Slide_Ids ) ) {
	$URIS_All_Slide_Ids = array();
}
$uris_total_slide_ids = count($URIS_All_Slide_Ids);

if($WRIS_L3_Slide_Order == "DESC" && $uris_total_slide_ids > 0 ) {
	$URIS_All_Slide_Ids = array_reverse($URIS_All_Slide_Ids, true);
}
if($WRIS_L3_Slide_Order == "shuffle" && $uris_total_slide_ids > 0 ) {
	shuffle($URIS_All_Slide_Ids);
}

$uris_i = 1;
$uris_j = 1;
?>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) { 
	jQuery( '#slider-pro-3-<?php echo esc_js( $post_id ); ?>' ).sliderPro({
		//width
		<?php if(isset($WRIS_L3_Width) && $WRIS_L3_Width == "100%") { ?>
		width: "100%",
		<?php } else if(isset($WRIS_L3_Width) && $WRIS_L3_Width == "custom") { ?>
		width: <?php if(!empty($WRIS_L3_Slider_Width)) echo esc_js($WRIS_L3_Slider_Width); else echo esc_js("1000"); ?>,
		<?php } else if(isset($WRIS_L3_Width) && $WRIS_L3_Width == "fullWidth") { ?>
		forceSize: 'fullWidth',
		<?php } ?>
		
		//height
		<?php if(isset($WRIS_L3_Height) && $WRIS_L3_Height == "custom") { ?>
		height: <?php if(!empty($WRIS_L3_Slider_Height)) echo esc_js($WRIS_L3_Slider_Height); else echo esc_js("500"); ?>,
		<?php } else { ?>
		autoHeight: true,
		<?php } ?>
		
		//auto play
		<?php if(isset($WRIS_L3_Auto_Slideshow) && $WRIS_L3_Auto_Slideshow == 1) { ?>
		autoplay:  true,
		autoplayOnHover: 'none',
		<?php } ?>
		<?php if(isset($WRIS_L3_Auto_Slideshow) && $WRIS_L3_Auto_Slideshow == 2) { ?>
		autoplay: true,
		autoplayOnHover: 'pause',
		<?php } ?>
		<?php if(isset($WRIS_L3_Auto_Slideshow) && $WRIS_L3_Auto_Slideshow == 3) { ?>
		autoplay:  false,
		<?php } ?>
		autoplayDelay: <?php if(!empty($WRIS_L3_Transition_Speed)) echo esc_js($WRIS_L3_Transition_Speed); else echo esc_js("5000"); ?>,
		
		
		arrows: <?php if(isset($WRIS_L3_Sliding_Arrow) && $WRIS_L3_Sliding_Arrow == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		buttons: <?php if(isset($WRIS_L3_Navigation_Button) && $WRIS_L3_Navigation_Button == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		smallSize: 500,
		mediumSize: 1000,
		largeSize: 3000,
		fade: <?php if(isset($WRIS_L3_Transition) && $WRIS_L3_Transition == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		
		//thumbnail
		thumbnailArrows: true,
		thumbnailWidth: <?php if(!empty($WRIS_L3_Thumbnail_Width)) echo esc_js($WRIS_L3_Thumbnail_Width); else echo esc_js("120"); ?>,
		thumbnailHeight: <?php if(!empty($WRIS_L3_Thumbnail_Height)) echo esc_js($WRIS_L3_Thumbnail_Height); else echo esc_js("100"); ?>,
		<?php if(isset($WRIS_L3_Navigation_Position) && $WRIS_L3_Navigation_Position == "top") { ?>
		thumbnailsPosition: 'top',
		<?php } ?>
		<?php if(isset($WRIS_L3_Navigation_Position) && $WRIS_L3_Navigation_Position == "bottom") { ?>
		thumbnailsPosition: 'bottom',
		<?php } ?>
		<?php if(isset($WRIS_L3_Thumbnail_Style) && $WRIS_L3_Thumbnail_Style == "pointer") { ?>
		thumbnailPointer: true, 
		<?php } ?>
		centerImage: true,
		imageScaleMode: '<?php echo esc_js( !empty($WRIS_L3_Slider_Scale_Mode) ? $WRIS_L3_Slider_Scale_Mode : "cover" ); ?>',
		allowScaleUp: <?php if(isset($WRIS_L3_Slider_Auto_Scale) && $WRIS_L3_Slider_Auto_Scale == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		<?php if($WRIS_L3_Slide_Order == "shuffle") { ?>
		shuffle: true,
		<?php } ?>
		startSlide: 0,
		loop: true,
		slideDistance: <?php if(!empty($WRIS_L3_Slide_Distance)) echo esc_js($WRIS_L3_Slide_Distance); else echo esc_js("5"); ?>,
		autoplayDirection: 'normal',
		touchSwipe: true,
		fullScreen: <?php if(isset($WRIS_L3_Fullscreeen) && $WRIS_L3_Fullscreeen == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
	});
});
</script>

<?php
$uris_post_title = "";
if(isset($WRIS_L3_Slide_Title) && $WRIS_L3_Slide_Title == 1) { ?>
<div id="uris-slider-title-<?php echo esc_attr($post_id); ?>" class="uris-slider-title">
	<h3 class="uris-slider-post-title"><?php echo esc_html( $uris_post_title = get_the_title( $post_id ) ); ?></h3>
</div>
<?php } if($uris_total_slide_ids>0){ ?>
		<div id="slider-pro-3-<?php echo esc_attr($post_id); ?>" class="slider-pro sp-no-js">
			<!---- slides div start ---->
			<div class="sp-slides">
				<?php
					$uris_slide_alt = "";
					if(is_array($URIS_All_Slide_Ids)){
						foreach($URIS_All_Slide_Ids as $URIS_Slide_Id) {
							if ( empty( $URIS_Slide_Id['rpgp_image_id'] ) ) {
								continue;
							}
							$uris_slide_id = $URIS_Slide_Id['rpgp_image_id'];
							$uris_attachment = get_post( $uris_slide_id ); // get all slide details
							if ( ! $uris_attachment ) {
								continue;
							}
							$uris_slide_alt = get_post_meta( $uris_attachment->ID, '_wp_attachment_image_alt', true );
							$uris_slide_caption = $uris_attachment->post_excerpt;
							$uris_slide_description = $uris_attachment->post_content;
							$uris_slide_src = wp_get_attachment_image_src($uris_slide_id, 'full', true); // return is array full image URL
							if ( ! is_array( $uris_slide_src ) || empty( $uris_slide_src[0] ) ) {
								continue;
							}
							$uris_slide_title = $uris_attachment->post_title; // attachment title
							$uris_i++;

							// alt is blank than set attachment title as alt tag
							if($uris_slide_alt == "" && $uris_slide_title != "") {
								$uris_slide_alt = $uris_slide_title;
							}
							// slide title is blank than set post title as alt tag
							if($uris_slide_alt == "" && $uris_slide_title == "") {
								$uris_slide_alt = $uris_post_title;
							}
						?>
						<div class="sp-slide">
							<img class="sp-image" loading="lazy" alt="<?php echo esc_attr($uris_slide_alt); ?>" src="<?php echo esc_url(URIS_PLUGIN_URL."assets/css/images/blank.gif"); ?>" data-src="<?php echo esc_url($uris_slide_src[0]); ?>" />

							<?php
							$uris_has_title = ( $uris_slide_title != "" && ! empty( $WRIS_L3_Show_Slide_Title ) );
							$uris_has_desc  = ( $uris_slide_description != "" && ! empty( $WRIS_L3_Show_Slide_Desc ) );
							if ( $uris_has_title || $uris_has_desc ) {
							?>
							<div class="sp-layer uris-slide-content-layer"
								data-position="bottomCenter"
								data-vertical="20px"
								data-width="90%"
								data-show-transition="up"
								data-show-delay="400">
								<div class="uris-slide-content-wrap">
									<?php if ( $uris_has_title ) { ?>
									<div class="title-in title-in-bg hide-small-screen">
										<?php echo esc_html( $uris_slide_title ); ?>
									</div>
									<?php } ?>

									<?php if ( $uris_has_desc ) { ?>
									<div class="desc-in desc-in-bg hide-medium-screen">
										<?php
										if ( strlen( $uris_slide_description ) > 300 ) {
											echo esc_html( substr( wp_kses_post( $uris_slide_description ), 0, 300 ) ) . "...";
										} else {
											echo esc_html( wp_kses_post( $uris_slide_description ) );
										}
										?>
									</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php } //end for each 
					} //end of is_array 
				?>
			</div>
			<!---- slides div end ---->
			<?php if(isset($WRIS_L3_Slider_Navigation) && $WRIS_L3_Slider_Navigation == 1) { ?>
			<!-- slides thumbnails div start -->
			<div class="sp-thumbnails">
				<?php
				$uris_slide_alt = "";
				if(is_array($URIS_All_Slide_Ids)){
					foreach($URIS_All_Slide_Ids as $URIS_Slide_Id) {
						if ( empty( $URIS_Slide_Id['rpgp_image_id'] ) ) {
							continue;
						}
						$uris_slide_id = $URIS_Slide_Id['rpgp_image_id'];
						$uris_attachment = get_post( $uris_slide_id ); // get all slide details
						if ( ! $uris_attachment ) {
							continue;
						}
						$uris_slide_alt = get_post_meta( $uris_attachment->ID, '_wp_attachment_image_alt', true );
						$uris_slide_caption = $uris_attachment->post_excerpt;
						$uris_slide_description = $uris_attachment->post_content;
						$uris_slide_src = isset( $uris_attachment->guid ) ? $uris_attachment->guid : ''; //  full image URL
						$uris_slide_title = $uris_attachment->post_title; // attachment title
						$uris_slide_medium = wp_get_attachment_image_src($uris_slide_id, 'medium', true); // return is array medium image URL
						$uris_thumb_url = ( is_array( $uris_slide_medium ) && ! empty( $uris_slide_medium[0] ) ) ? $uris_slide_medium[0] : ( ! empty( $uris_slide_src ) ? $uris_slide_src : URIS_PLUGIN_URL . "assets/img/loading.gif" );
						// alt is blank than set attachment title as alt tag
						if($uris_slide_alt == "" && $uris_slide_title != "") {
							$uris_slide_alt = $uris_slide_title;
						}
						// slide title is blank than set post title as alt tag
						if($uris_slide_alt == "" && $uris_slide_title == "") {
							$uris_slide_alt = $uris_post_title;
						}
						$uris_j++; ?>
						<img class="sp-thumbnail" loading="lazy" src="<?php echo esc_url(URIS_PLUGIN_URL."assets/img/loading.gif"); ?>" data-src="<?php echo esc_url($uris_thumb_url); ?>" alt="<?php echo esc_attr( $uris_slide_alt ); ?>"/>
					<?php } // end of for each
				}// end of is_array ?>
			</div>
			<?php } ?>
			<!-- slides thumbnails div end -->
		</div>
		<style>
/* Post Title Spacing & Center Alignment */
#uris-slider-title-<?php echo esc_attr($post_id); ?>,
.uris-slider-title {
	margin-top: 15px;
	margin-bottom: 25px !important;
	clear: both;
	text-align: center !important;
}
#uris-slider-title-<?php echo esc_attr($post_id); ?> .uris-slider-post-title,
.uris-slider-title .uris-slider-post-title {
	margin: 0 auto 15px auto !important;
	padding: 0 !important;
	line-height: 1.4 !important;
	text-align: center !important;
	display: block !important;
}
/* Layout 3 - Enforce Sharp Square Slide Corners */
#slider-pro-3-<?php echo esc_attr($post_id); ?>,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-slides-container,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-mask,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-slides,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-slide,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-image-container,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-image {
	border-radius: 0 !important;
}
/* border */
<?php if(isset($WRIS_L3_Thumbnail_Style) && $WRIS_L3_Thumbnail_Style == "border" && !empty($WRIS_L3_Navigation_Pointer_Color)) { ?>
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-selected-thumbnail {
	border: 4px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?>;
}
<?php } ?>

/* font + color handled in scoped rules below */

/* bullets color and gap */
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-buttons {
	padding-top: 15px;
	text-align: center;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-button  {
	border: 2px solid <?php echo esc_attr(!empty($WRIS_L3_Navigation_Bullets_Color) ? $WRIS_L3_Navigation_Bullets_Color : '#000000'); ?> !important;
	margin: 4px 8px !important;
	display: inline-block !important;
	vertical-align: middle !important;
	box-sizing: border-box !important;
	transition: all 0.2s ease !important;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-selected-button  {
	background-color: <?php echo esc_attr(!empty($WRIS_L3_Navigation_Bullets_Color) ? $WRIS_L3_Navigation_Bullets_Color : '#000000'); ?> !important;
}

/* pointer color - bottom */
<?php if( isset($WRIS_L3_Navigation_Position) && $WRIS_L3_Navigation_Position == "bottom" && !empty($WRIS_L3_Navigation_Pointer_Color)) { ?>
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-selected-thumbnail::before {
	border-bottom: 5px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-selected-thumbnail::after {
	border-bottom: 13px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
<?php } ?>

/* pointer color - top */
<?php if( isset($WRIS_L3_Navigation_Position) && $WRIS_L3_Navigation_Position == "top" && !empty($WRIS_L3_Navigation_Pointer_Color)) { ?>

#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::before {
    border-bottom: 5px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?>;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::after {
    border-top: 13px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
<?php } ?>

/* full screen icon */
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-full-screen-button::before {
    color: <?php echo esc_attr(!empty($WRIS_L3_Navigation_Color) ? $WRIS_L3_Navigation_Color : '#FFFFFF'); ?> !important;
}

/* hover navigation icon color */
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-next-arrow::after,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-next-arrow::before {
	background-color: <?php echo esc_attr(!empty($WRIS_L3_Navigation_Color) ? $WRIS_L3_Navigation_Color : '#FFFFFF'); ?> !important;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-previous-arrow::after,
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-previous-arrow::before {
	background-color: <?php echo esc_attr(!empty($WRIS_L3_Navigation_Color) ? $WRIS_L3_Navigation_Color : '#FFFFFF'); ?> !important;
}

/* Unified Caption Layer - Overlap Prevention */
#slider-pro-3-<?php echo esc_attr($post_id); ?> .uris-slide-content-layer {
	width: 90% !important;
	max-width: 90% !important;
	pointer-events: none;
	text-align: center !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .uris-slide-content-wrap {
	display: inline-flex !important;
	flex-direction: column !important;
	align-items: center !important;
	justify-content: center !important;
	gap: 8px !important;
	width: 100% !important;
	margin: 0 auto !important;
	pointer-events: auto;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .title-in {
	font-family: <?php echo esc_attr(!empty($WRIS_L3_Font_Style) ? $WRIS_L3_Font_Style : 'Arial'); ?> !important;
	color: <?php echo esc_attr(!empty($WRIS_L3_Title_Color) ? $WRIS_L3_Title_Color : '#FFFFFF'); ?> !important;
	font-weight: 700 !important;
	text-align: center !important;
	opacity: 1 !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .title-in-bg {
	background: <?php echo esc_attr( uris_hex2rgba( (!empty($WRIS_L3_Title_BgColor) ? $WRIS_L3_Title_BgColor : '#000000'), 0.88 ) ); ?> !important;
	background-color: <?php echo esc_attr( uris_hex2rgba( (!empty($WRIS_L3_Title_BgColor) ? $WRIS_L3_Title_BgColor : '#000000'), 0.88 ) ); ?> !important;
	white-space: normal !important;
	transform: initial !important;
	-webkit-transform: initial !important;
	font-size: 16px !important;
	line-height: 1.4 !important;
	padding: 8px 20px !important;
	border-radius: 4px !important;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4) !important;
	max-width: 85% !important;
	display: inline-block !important;
	box-sizing: border-box !important;
	margin: 0 auto !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .desc-in {
	font-family: <?php echo esc_attr(!empty($WRIS_L3_Font_Style) ? $WRIS_L3_Font_Style : 'Arial'); ?> !important;
	color: <?php echo esc_attr(!empty($WRIS_L3_Desc_Color) ? $WRIS_L3_Desc_Color : '#FFFFFF'); ?> !important;
	text-align: center !important;
	opacity: 1 !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .desc-in-bg {
	background: <?php echo esc_attr( uris_hex2rgba( (!empty($WRIS_L3_Desc_BgColor) ? $WRIS_L3_Desc_BgColor : '#000000'), 0.88 ) ); ?> !important;
	background-color: <?php echo esc_attr( uris_hex2rgba( (!empty($WRIS_L3_Desc_BgColor) ? $WRIS_L3_Desc_BgColor : '#000000'), 0.88 ) ); ?> !important;
	white-space: normal !important;
	transform: initial !important;
	-webkit-transform: initial !important;
	font-size: 14px !important;
	line-height: 1.5 !important;
	padding: 10px 24px !important;
	border-radius: 4px !important;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4) !important;
	max-width: 95% !important;
	display: block !important;
	box-sizing: border-box !important;
	margin: 0 auto !important;
}

@media (max-width: 640px) {
	#slider-pro-3-<?php echo esc_attr($post_id); ?> .hide-small-screen {
		display: none;
	}
}

@media (max-width: 860px) {
	#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-layer {
		font-size: 18px;
	}
	
	#slider-pro-3-<?php echo esc_attr($post_id); ?> .hide-medium-screen {
		display: none;
	}
}
.slides-not-found {
	background-color: #a92929;
	border-radius: 5px;
	color: #fff;
	font-family: initial;
	text-align: center;
	padding:12px;
}
/* Custom CSS */
<?php if(!empty($WRIS_L3_Custom_CSS)) echo esc_html($WRIS_L3_Custom_CSS); ?>
</style>
<?php } else { ?> <div class="slides-not-found"><i class="fa fa-times-circle"></i> No Slide Found In Slider.</div> <?php } endwhile; ?>
