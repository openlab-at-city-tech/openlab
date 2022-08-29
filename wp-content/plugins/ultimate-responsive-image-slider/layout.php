<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Load All Slides
$URIS_CPT_Name = "ris_gallery";
$AllSlides = array(  'p' => $Id['id'], 'post_type' => $URIS_CPT_Name, 'orderby' => $WRIS_L3_Slide_Order);
$loop = new WP_Query( $AllSlides );

while ( $loop->have_posts() ) : $loop->the_post();
//get the post id
$post_id = get_the_ID();

//Get All Slides Details Post Meta
$URIS_All_Slide_Ids = get_post_meta( get_the_ID(), 'ris_all_photos_details', true);
$TotalSlideIds = count(get_post_meta( get_the_ID(), 'ris_all_photos_details', true ));

if($WRIS_L3_Slide_Order == "DESC" ) {
	$URIS_All_Slide_Ids = array_reverse($URIS_All_Slide_Ids, true);
}
if($WRIS_L3_Slide_Order == "shuffle" ) {
	$shuffle = shuffle($URIS_All_Slide_Ids);
}

$i = 1;
$j = 1;
?>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) { 
	jQuery( '#slider-pro-3-<?php echo $post_id; ?>' ).sliderPro({
		//width
		<?php if($WRIS_L3_Width == "100%") { ?>
		width: "100%",
		<?php } else if($WRIS_L3_Width == "custom") { ?>
		width: <?php if($WRIS_L3_Slider_Width != "") echo esc_js($WRIS_L3_Slider_Width); else echo esc_js("1000"); ?>,
		<?php } else if($WRIS_L3_Width == "fullWidth") { ?>
		forceSize: 'fullWidth',
		<?php } ?>
		
		//height
		<?php if($WRIS_L3_Height == "custom") { ?>
		height: <?php if($WRIS_L3_Slider_Height != "") echo esc_js($WRIS_L3_Slider_Height); else echo esc_js("500"); ?>,
		<?php } else { ?>
		autoHeight: true,
		<?php } ?>
		
		//auto play
		<?php if($WRIS_L3_Auto_Slideshow == 1) { ?>
		autoplay:  true,
		autoplayOnHover: 'none',
		<?php } ?>
		<?php if($WRIS_L3_Auto_Slideshow == 2) { ?>
		autoplay: true,
		autoplayOnHover: 'pause',
		<?php } ?>
		<?php if($WRIS_L3_Auto_Slideshow == 3) { ?>
		autoplay:  false,
		<?php } ?>
		autoplayDelay: <?php if($WRIS_L3_Transition_Speed != "") echo esc_js($WRIS_L3_Transition_Speed); else echo esc_js("5000"); ?>,
		
		
		arrows: <?php if($WRIS_L3_Sliding_Arrow == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		buttons: <?php if($WRIS_L3_Navigation_Button == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		smallSize: 500,
		mediumSize: 1000,
		largeSize: 3000,
		fade: <?php if($WRIS_L3_Transition == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		
		//thumbnail
		thumbnailArrows: true,
		thumbnailWidth: <?php if($WRIS_L3_Thumbnail_Width != "") echo esc_js($WRIS_L3_Thumbnail_Width); else echo esc_js("120"); ?>,
		thumbnailHeight: <?php if($WRIS_L3_Thumbnail_Height != "") echo esc_js($WRIS_L3_Thumbnail_Height); else echo esc_js("100"); ?>,
		<?php if($WRIS_L3_Navigation_Position == "top") { ?>
		thumbnailsPosition: 'top',
		<?php } ?>
		<?php if($WRIS_L3_Navigation_Position == "bottom") { ?>
		thumbnailsPosition: 'bottom',
		<?php } ?>
		<?php if($WRIS_L3_Thumbnail_Style == "pointer") { ?>
		thumbnailPointer: true, 
		<?php } ?>
		centerImage: true,
		imageScaleMode: '<?php echo $WRIS_L3_Slider_Scale_Mode;?>',
		allowScaleUp: <?php if($WRIS_L3_Slider_Auto_Scale == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
		<?php if($WRIS_L3_Slide_Order == "shuffle") { ?>
		shuffle: true,
		<?php } ?>
		startSlide: 0,
		loop: true,
		slideDistance: <?php if($WRIS_L3_Slide_Distance) echo esc_js($WRIS_L3_Slide_Distance); else echo esc_js("5"); ?>,
		autoplayDirection: 'normal',
		touchSwipe: true,
		fullScreen: <?php if($WRIS_L3_Fullscreeen == 1) echo esc_js("true"); else echo esc_js("false"); ?>,
	});
});
</script>
<style>
/* Layout 3 */
/* border */
<?php if($WRIS_L3_Thumbnail_Style == "border") { ?>
#slider-pro-3-<?php echo esc_attr($post_id); ?> .sp-selected-thumbnail {
	border: 4px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?>;
}
<?php } ?>

/* font + color */
.title-in  {
	font-family: <?php echo esc_attr($WRIS_L3_Font_Style); ?> !important;
	color: <?php echo esc_attr($WRIS_L3_Title_Color); ?> !important;
	background-color: <?php echo esc_attr($WRIS_L3_Title_BgColor); ?> !important;
	opacity: 0.7 !important;
}
.desc-in  {
	font-family: <?php echo esc_attr($WRIS_L3_Font_Style); ?> !important;
	color: <?php echo esc_attr($WRIS_L3_Desc_Color); ?> !important;
	background-color: <?php echo esc_attr($WRIS_L3_Desc_BgColor); ?> !important;
	opacity: 0.7 !important;
}

/* bullets color */
.sp-button  {
	border: 2px solid <?php echo esc_attr($WRIS_L3_Navigation_Bullets_Color); ?> !important;
}
.sp-selected-button  {
	background-color: <?php echo esc_attr($WRIS_L3_Navigation_Bullets_Color); ?> !important;
}

/* pointer color - bottom */
<?php if( $WRIS_L3_Navigation_Position == "bottom") { ?>
.sp-selected-thumbnail::before {
	border-bottom: 5px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
.sp-selected-thumbnail::after {
	border-bottom: 13px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
<?php } ?>

/* pointer color - top */
<?php if( $WRIS_L3_Navigation_Position == "top") { ?>

.sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::before {
    border-bottom: 5px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?>;
}
.sp-top-thumbnails.sp-has-pointer .sp-selected-thumbnail::after {
    border-top: 13px solid <?php echo esc_attr($WRIS_L3_Navigation_Pointer_Color); ?> !important;
}
<?php } ?>

/* full screen icon */
.sp-full-screen-button::before {
    color: <?php echo esc_attr($WRIS_L3_Navigation_Color); ?> !important;
}

/* hover navigation icon color */
.sp-next-arrow::after, .sp-next-arrow::before {
	background-color: <?php echo esc_attr($WRIS_L3_Navigation_Color); ?> !important;
}
.sp-previous-arrow::after, .sp-previous-arrow::before {
	background-color: <?php echo esc_attr($WRIS_L3_Navigation_Color); ?> !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .title-in {
	color: <?php echo esc_attr($WRIS_L3_Title_Color); ?> !important;
	font-weight: bolder;
	text-align: center;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .title-in-bg {
	background: rgba(255, 255, 255, 0.7); !important;
	white-space: unset !important;
	max-width: 90%;
	min-width: 40%;
	transform: initial !important;
	-webkit-transform: initial !important;
	font-size: 14px !important;
}

#slider-pro-3-<?php echo esc_attr($post_id); ?> .desc-in {
	color: <?php echo esc_attr($WRIS_L3_Desc_Color); ?> !important;
	text-align: center;
}
#slider-pro-3-<?php echo esc_attr($post_id); ?> .desc-in-bg {
	background: rgba(<?php echo esc_attr($WRIS_L3_Desc_BgColor); ?>, <?php echo esc_attr("0.7"); ?>) !important;
	white-space: unset !important;
	width: 80% !important;
	min-width: 30%;
	transform: initial !important;
	-webkit-transform: initial !important;
	font-size: 13px !important;
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
<?php echo esc_html($WRIS_L3_Custom_CSS); ?>
</style>
<?php  
$post_title = "";
if($WRIS_L3_Slide_Title) { ?>
<h3 class="uris-slider-post-title"><?php echo $post_title = get_the_title( $post_id ); ?></h3>
<?php } if($TotalSlideIds>0){ ?>
		<div id="slider-pro-3-<?php echo esc_attr($post_id); ?>" class="slider-pro">
			<!---- slides div start ---->
			<div class="sp-slides">
				<?php
					$slide_alt = "";
					if(is_array($URIS_All_Slide_Ids)){
						foreach($URIS_All_Slide_Ids as $URIS_Slide_Id) {
							$slide_id = $URIS_Slide_Id['rpgp_image_id'];
							$attachment = get_post( $slide_id ); // get all slide details
							$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
							$slide_caption = $attachment->post_excerpt;
							$slide_description = $attachment->post_content;
							$slide_src = wp_get_attachment_image_src($slide_id, 'full', true); // return is array full image URL
							$slide_title = $attachment->post_title; // attachment title
							$i++;
							
							// alt is blank than set attachment title as alt tag
							if($slide_alt == "" && $slide_title != "") {
								$slide_alt = $slide_title;
							}
							// slide title is blank than set post title as alt tag
							if($slide_alt == "" && $slide_title == "") {
								$slide_alt = $post_title;
							}
						?>
						<div class="sp-slide">
							<img class="sp-image" alt="<?php echo esc_attr($slide_alt); ?>" src="<?php echo esc_url(URIS_PLUGIN_URL."assets/css/images/blank.gif"); ?>" data-src="<?php echo esc_url($slide_src[0]); ?>" />

							<?php if($slide_title != "" && $WRIS_L3_Show_Slide_Title) { ?>
							<p class="sp-layer sp-white sp-padding title-in title-in-bg hide-small-screen" 
								data-position="centerCenter"
								data-vertical="-14%"
								data-show-transition="left" data-show-delay="500">
								<?php echo esc_html( $slide_title ); ?>
							</p>
							<?php } ?>

							<?php if($slide_description != "" && $WRIS_L3_Show_Slide_Desc) { ?>
							<p class="sp-layer sp-black sp-padding desc-in desc-in-bg hide-medium-screen" 
								data-position="centerCenter"
								data-vertical="14%"
								data-show-transition="right" data-show-delay="500">
								<?php if(strlen($slide_description) > 300 ) echo substr(wp_kses_post($slide_description),0,300)."..."; else echo wp_kses_post($slide_description); ?>
							</p>
							<?php } ?>
						</div>
						<?php } //end for each 
					} //end of is_array 
				?>
			</div>
			
			<!---- slides div end ---->
			<?php if($WRIS_L3_Slider_Navigation == 1) { ?>
			<!-- slides thumbnails div start -->
			<div class="sp-thumbnails">
				<?php
				$slide_alt = "";
				if(is_array($URIS_All_Slide_Ids)){
					foreach($URIS_All_Slide_Ids as $URIS_Slide_Id) {
						$slide_id = $URIS_Slide_Id['rpgp_image_id'];
						$attachment = get_post( $slide_id ); // get all slide details
						$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
						$slide_caption = $attachment->post_excerpt;
						$slide_description = $attachment->post_content;
						$slide_src = $attachment->guid; //  full image URL
						$slide_title = $attachment->post_title; // attachment title
						$slide_medium = wp_get_attachment_image_src($slide_id, 'medium', true); // return is array medium image URL
						// alt is blank than set attachment title as alt tag
						if($slide_alt == "" && $slide_title != "") {
							$slide_alt = $slide_title;
						}
						// slide title is blank than set post title as alt tag
						if($slide_alt == "" && $slide_title == "") {
							$slide_alt = $post_title;
						}
						$j++; ?>
						<img class="sp-thumbnail" src="<?php echo esc_url(URIS_PLUGIN_URL."assets/img/loading.gif"); ?>" data-src="<?php echo esc_url($slide_medium[0]); ?>" alt="<?php echo $slide_alt; ?>"/>
					<?php } // end of for each
				}// end of is_array ?>
			</div>
			<?php } ?>
			<!-- slides thumbnails div end -->
		</div>
<?php } else { ?> <div class="slides-not-found"><i class="fa fa-times-circle"></i> No Slide Found In Slider.</div> <?php } endwhile; ?>