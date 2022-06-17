<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
Plugin Name: Ultimate Responsive Image Slider - 3.5.6
Plugin URI: https://wordpress.org/plugins/ultimate-responsive-image-slider/
Description: Add unlimited image slides using Ultimate Responsive Image Slider in any Page and Post content to give an attractive mode to represent contents.
Version: 3.5.6
Author: FARAZFRANK
Author URI: http://wpfrank.com/
Text Domain: ultimate-responsive-image-slider
Domain Path: /languages
License: GPL2

Ultimate Responsive Image Slider is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or any later version.

Ultimate Responsive Image Slider is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Ultimate Responsive Image Slider. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

//Constant Variable
define("URIS_PLUGIN_URL", plugin_dir_url(__FILE__));

// Apply default settings on activation
register_activation_hook( __FILE__, 'WRIS_DefaultSettingsPro' );
function WRIS_DefaultSettingsPro() {
	$DefaultSettingsProArray = serialize( array(
		//layout 3 settings
		"WRIS_L3_Slide_Title"			=> 1,
		"WRIS_L3_Show_Slide_Title"		=> 0,
		"WRIS_L3_Show_Slide_Desc"		=> 0,
		"WRIS_L3_Auto_Slideshow"		=> 1,
		"WRIS_L3_Transition"			=> 1,
		"WRIS_L3_Transition_Speed"		=> 5000,
		"WRIS_L3_Sliding_Arrow"			=> 1,
		"WRIS_L3_Slider_Navigation"		=> 1,
		"WRIS_L3_Navigation_Button"		=> 1,
		"WRIS_L3_Slider_Width"			=> "1000",
		"WRIS_L3_Slider_Height"			=> "500",
		"WRIS_L3_Font_Style"			=> "Arial",
		"WRIS_L3_Title_Color"			=> "#FFFFFF",
		"WRIS_L3_Slider_Scale_Mode"		=> "cover",
		"WRIS_L3_Slider_Auto_Scale"		=> 1,
		"WRIS_L3_Title_BgColor"			=> "#FFFFFF",
		"WRIS_L3_Desc_Color"			=> "#000000",
		"WRIS_L3_Desc_BgColor"			=> "#FFFFFF",
		"WRIS_L3_Navigation_Color"		=> "#000000",
		"WRIS_L3_Fullscreeen"			=> 1,
		"WRIS_L3_Custom_CSS"			=> "",
		'WRIS_L3_Slide_Order'			=> "ASC",
		'WRIS_L3_Navigation_Position'	=> "bottom",
		'WRIS_L3_Slide_Distance'		=> 5,
		'WRIS_L3_Thumbnail_Style'		=> "border",
		'WRIS_L3_Thumbnail_Width'		=> 120,
		'WRIS_L3_Thumbnail_Height'		=> 120,
		'WRIS_L3_Width'					=> "custom",
		'WRIS_L3_Height'					=> "custom",
		'WRIS_L3_Navigation_Bullets_Color' => "#000000",
		'WRIS_L3_Navigation_Pointer_Color' => "#000000",
	));
	add_option("WRIS_Settings", $DefaultSettingsProArray);
}

// Add settings link on Plugins page
function ris_links($links) {
	$ris_pro_link = ('<a href="http://wpfrank.com/demo/ultimate-responsive-image-slider-pro/" target="_blank">Try Pro</a>');
	array_unshift($links, $ris_pro_link);
	$ris_settings_link = ('<a href="edit.php?post_type=ris_gallery">Settings</a>');
	array_unshift($links, $ris_settings_link);
	return $links;
}
$uris_plugin_name = plugin_basename(__FILE__);
add_filter("plugin_action_links_$uris_plugin_name", 'ris_links' );

// Slider Text Widget Support
add_filter( 'widget_text', 'do_shortcode' );

class URIS {

	private static $instance;
	private $admin_thumbnail_size = 150;
	private $thumbnail_size_w = 150;
	private $thumbnail_size_h = 150;
	var $counter;

	public static function forge() {
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->counter = 0;
		// image crop function
		add_image_size('rpg_gallery_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true);
		add_image_size('rpg_gallery_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true);
		// Translate plugin
		add_action('plugins_loaded', array(&$this, 'URIS_Translate'), 1);
		// CPT Function
		add_action('init', array(&$this, 'ResponsiveImageSlider'),1);
		// generate meta box function
		add_action('add_meta_boxes', array(&$this, 'add_all_ris_meta_boxes'));
		add_action('admin_init', array(&$this, 'add_all_ris_meta_boxes'), 1);
		// meta box setting save function
		add_action('save_post', array(&$this, 'add_image_meta_box_save'), 9, 1);
		add_action('save_post', array(&$this, 'ris_settings_meta_save'), 9, 1);
		// add new slide function
		add_action('wp_ajax_uris_get_thumbnail', array(&$this, 'ajax_get_thumbnail_uris'));

		// only for admin dashboard clone slider ajax JS
		add_action( 'admin_enqueue_scripts', array(&$this, 'uris_scripts'));
		
		//clone slider ajax call back, its required localize ajax object
		add_action('wp_ajax_uris_clone_slider', array(&$this, 'uris_clone_slider'));
	}

	/**
	 * Scripts and styles should not be registered or enqueued until the wp_enqueue_scripts, admin_enqueue_scripts, or login_enqueue_scripts hooks. 
	 */
	public function uris_scripts() {
		wp_enqueue_script( 'ajax-script', URIS_PLUGIN_URL. 'assets/js/uris-ajax-script.js', array('jquery'));
		wp_localize_script( 'ajax-script', 'uris_ajax_object', array('ajax_url' => admin_url( 'admin-ajax.php' )));
	}
	
	//Clone slider call back
	public function uris_clone_slider() {
		if ( current_user_can( 'manage_options' ) ) {
			if ( isset( $_POST['uris_clone_nonce'] ) && wp_verify_nonce( $_POST['uris_clone_nonce'], 'uris_clone_nonce' ) ) {
				$ursi_clone_post_id = sanitize_text_field($_POST['ursi_clone_post_id']);
				// get all required data for cloning
				$post_title = get_the_title($ursi_clone_post_id)." - Clone";
				$post_type = sanitize_text_field("ris_gallery");
				$post_status = sanitize_text_field("publish");
				// get all slide ids for cloning
				$URIS_All_Slide_Ids = get_post_meta( $ursi_clone_post_id, 'ris_all_photos_details', true);
				
				// get slider post meta settings for cloning
				$WRIS_Gallery_Settings_Key = sanitize_text_field("WRIS_Gallery_Settings_".$ursi_clone_post_id);
				$WRIS_Gallery_Settings = get_post_meta( $ursi_clone_post_id, $WRIS_Gallery_Settings_Key, true);
				
				//cloning post
				$uris_cloning_post_array =  array(
					'post_title' => $post_title,
					'post_type' => $post_type,
					'post_status' => $post_status,
					'meta_input' => array(
						// post meta key => value
						'ris_all_photos_details' => $URIS_All_Slide_Ids,
					),
				);
				
				$cloned_post_id = wp_insert_post($uris_cloning_post_array);
				// slider post meta settings cloning
				add_post_meta( $cloned_post_id, "WRIS_Gallery_Settings_".$cloned_post_id, $WRIS_Gallery_Settings);
				die;
			} else {
				die;
			}
		}
	}
	
	/**
	 * Translate Plugin
	 */
	public function URIS_Translate() {
		load_plugin_textdomain('ultimate-responsive-image-slider', FALSE, dirname( plugin_basename(__FILE__)).'/languages/' );
	}

	// Register Custom Post Type
	public function ResponsiveImageSlider() {
		if ( current_user_can( 'manage_options' ) ) {
			$uris_labels = array(
				'name' => 'Ultimate Responsive Image Slider',
				'singular_name' => 'Ultimate Responsive Image Slider',
				'add_new' => __( 'Add New Slider', 'ultimate-responsive-image-slider' ),
				'add_new_item' => __( 'Add New Slider', 'ultimate-responsive-image-slider' ),
				'edit_item' => __( 'Edit Slider', 'ultimate-responsive-image-slider' ),
				'new_item' => __( 'New Slider', 'ultimate-responsive-image-slider' ),
				'view_item' => __( 'View Slider', 'ultimate-responsive-image-slider' ),
				'search_items' => __( 'Search Slider', 'ultimate-responsive-image-slider' ),
				'not_found' => __( 'No Slider found', 'ultimate-responsive-image-slider' ),
				'not_found_in_trash' => __( 'No Slider Found in Trash', 'ultimate-responsive-image-slider' ),
				'parent_item_colon' => __( 'Parent Slider:', 'ultimate-responsive-image-slider' ),
				'all_items' => __( 'All Sliders', 'ultimate-responsive-image-slider' ),
				'menu_name' => 'UR Image Slider',
			);
			$args = array(
				'labels' => $uris_labels,
				'hierarchical' => false,
				'supports' => array( 'title'),
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 10,
				'menu_icon' => 'dashicons-format-gallery',
				'show_in_nav_menus' => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => false,
			);
			register_post_type( 'ris_gallery', $args );
			add_filter( 'manage_edit-ris_gallery_columns', array(&$this, 'ris_gallery_columns' )) ;
			add_action( 'manage_ris_gallery_posts_custom_column', array(&$this, 'ris_gallery_manage_columns' ), 10, 2 );
		}
	}

	function ris_gallery_columns( $columns ){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'UR Image Slider Title' ),
			'shortcode' => __( 'Slider Shortcode' ),
			'date' => __( 'Date' )
		);
		return $columns;
	}

	function ris_gallery_manage_columns( $column, $post_id ){
		global $post;
		switch( $column ) {
			case 'shortcode' :
				$allowed_shortcode = array('input' => array( 'type' => array(), 'value' => array(), 'readonly' => array() ));
				echo wp_kses('<input type="text" value="[URIS id='.$post_id.']" readonly="readonly" />', $allowed_shortcode);
			break;
			default :
			break;
		}
	}

	public function add_all_ris_meta_boxes() {
		add_meta_box( __('Add Slides', 'ultimate-responsive-image-slider'), __('Add Slides', 'ultimate-responsive-image-slider'), array(&$this, 'ris_generate_add_image_meta_box_function'), 'ris_gallery', 'normal', 'low' );
		add_meta_box( __('Configure Settings', 'ultimate-responsive-image-slider'), __('Configure Settings', 'ultimate-responsive-image-slider'), array(&$this, 'ris_settings_meta_box_function'), 'ris_gallery', 'normal', 'low');
		add_meta_box( 'Upgrade To Pro Plugin', 'Upgrade To Pro Plugin', array(&$this, 'ris_upgrade_to_pro_meta_box_function'), 'ris_gallery', 'normal', 'low');
		add_meta_box ( __('Slider Shortcode', 'ultimate-responsive-image-slider'), __('Slider Shortcode', 'ultimate-responsive-image-slider'), array(&$this, 'ris_shotcode_meta_box_function'), 'ris_gallery', 'side', 'low');
		add_meta_box ( __('Try My New Slider Plugin', 'ultimate-responsive-image-slider'), __('Try My New Slider Plugin', 'ultimate-responsive-image-slider'), array(&$this, 'ris_new_plugin_meta_box_function'), 'ris_gallery', 'side', 'low');
		add_meta_box('Show US Some Love & Rate Us', 'Show US Some Love & Rate Us', array(&$this, 'uris_Rate_us_meta_box_function'), 'ris_gallery', 'side', 'low');
	}

	//Rate Us Meta Box
	public function uris_Rate_us_meta_box_function() { ?>
		<style>
		.urisp-rate-us span.dashicons {
			width: 30px;
			height: 30px;
		}
		.urisp-rate-us span.dashicons-star-filled:before {
			content: "\f155";
			font-size: 30px;
		}
		.wpf_uris_fivestar{
			width: 80%;
		}
		a.wpf_fs_btn {
			text-decoration: none;
			background-color: #d72323;
			padding-left: 20px;
			padding-right: 20px;
			border-radius: 5px;
			color: #fff;
			padding-top: 8px;
			padding-bottom: 8px;
		}
		a:focus, a:hover {
			color: #fff !important;
			text-decoration: none !important;
		}
		</style>
		<div align="center">
			<p>Please Review & Rate Us On WordPress</p>
			<a class="upgrade-to-pro-demo urisp-rate-us" style=" text-decoration: none; height: 40px; width: 40px;" href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider/reviews/#new-post" target="_blank">
				<img class="wpf_uris_fivestar" src="<?php echo $path = esc_url(URIS_PLUGIN_URL."assets/img/5star.jpg"); ?>">
			</a>
		</div>
		<div class="upgrade-to-pro" style="text-align:center;margin-bottom:10px;margin-top:10px;">
			<a href="https://wordpress.org/support/plugin/ultimate-responsive-image-slider/reviews/#new-post" target="_blank" class="wpf_fs_btn">RATE US</a>
		</div>
		<?php
	}

	//Upgrade To Meta Box
	public function ris_upgrade_to_pro_meta_box_function() { ?>
		<div class="welcome-panel-column" id="wpfrank-action-metabox">
			<h4>Unlock More Features in Ultimate Responsive Image Slider Pro</h4>
			<p>5 Design Layouts, Transition Effect, Color Customizations, 500+ Google Fonts For Slide Title & Description, Slides Ordering, Link On Slides, 2 Light Box Style, Various Slider Control Settings</p>
			<a class="button button-primary button-hero load-customize hide-if-no-customize wpfrank-action-button" target="_blank" href="http://wpfrank.com/demo/ultimate-responsive-image-slider-pro/">Check Pro Plugin Demo</a>
			<a class="button button-primary button-hero load-customize hide-if-no-customize wpfrank-action-button" target="_blank" href="http://wpfrank.com/account/signup/ultimate-responsive-image-slider-pro">Buy Pro Plugin $25</a>
			<h4>Also Try My New Slider Plugin</h4>
			<a class="button button-primary button-hero load-customize hide-if-no-customize wpfrank-action-button" target="_blank" href="https://wordpress.org/plugins/slider-factory/">Slider Factory</a>
		</div>
		<?php
	}

	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into photo gallery
	 */
	public function ris_generate_add_image_meta_box_function($post) { ?>
		<p><a href="edit.php?post_type=ris_gallery&page=uris-recover-slider" class="button button-primary button-hero">Click To Recover Old Sliders</a> ( ignore if this slider already recovered )</p>
		<div id="uris-container" class="uris-container">
			<input type="hidden" id="uris-save-action" name="uris-save-action" value="uris-save-settings">
			<ul id="uris-slides-container" class="clearfix SortSlides">
				<?php
				/* load all slide into dashboard */
				$URIS_All_Slide_Ids = get_post_meta( $post->ID, 'ris_all_photos_details', true);
				$TotalSlideIdsArray = get_post_meta( $post->ID, 'ris_all_photos_details', true );
				if(is_array($TotalSlideIdsArray)) {
					$TotalSlideIds = count($TotalSlideIdsArray);
				} else {
					$TotalSlideIds = 0;
				}
				
				$i = 0;
				if($TotalSlideIds) {
					if(is_array($URIS_All_Slide_Ids)){
						foreach($URIS_All_Slide_Ids as $URIS_Slide_Id) {
							$slide_id = $URIS_Slide_Id['rpgp_image_id'];
							$attachment = get_post( $slide_id ); // get all slide details
							$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
							$slide_caption = $attachment->post_excerpt;
							$slide_description = $attachment->post_content;
							$slide_src = $attachment->guid; //  full image URL
							$slide_title = $attachment->post_title; // attachment title
							$slide_medium = wp_get_attachment_image_src($slide_id, 'medium', true); // return is array	medium image URL
							$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
							?>
							<li id="<?php echo esc_attr($slide_id); ?>" class="uris-slide" data-position="<?php echo esc_attr($slide_id); ?>">
								<a id="uris-slide-delete-icon" class="uris-slide-delete-icon"><img src="<?php echo  esc_url(URIS_PLUGIN_URL.'assets/img/close-icon.png'); ?>" /></a>
								<div class="uris-slide-meta">
									<p>
										<img src="<?php echo esc_url($slide_medium[0]); ?>" class="uris-slide-image">
									</p>
									<p>
										<label><?php _e('Slide Title', 'ultimate-responsive-image-slider'); ?></label>
										<input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr($UniqueString); ?>" />
										<input type="hidden" id="rpgp_image_id[]" name="rpgp_image_id[]" value="<?php echo esc_attr($slide_id); ?>">
										<input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" class="uris-slide-input-text" value="<?php echo esc_attr( $slide_title ); ?>" placeholder="<?php _e('Enter Slide Title', 'ultimate-responsive-image-slider'); ?>" >
									</p>
									<p>
										<label><?php _e('Slide Descriptions', 'ultimate-responsive-image-slider'); ?></label>
										<textarea rows="4" cols="50" id="rpgp_image_desc[]" name="rpgp_image_desc[]" class=" urisp_richeditbox_<?php echo esc_attr($i); ?> uris-slide-input-text" placeholder="<?php _e('Enter Slide Description', 'ultimate-responsive-image-slider'); ?>"><?php echo htmlentities( $slide_description ); ?></textarea>
										<button type="button" class="btn btn-md btn-info btn-block" data-toggle="modal" data-target="#myModal" onclick="urisp_richeditor(<?php echo esc_attr($i); ?>)"><?php _e('Use Rich Text Editor', 'ultimate-responsive-image-slider'); ?> <i class="fa fa-edit"></i></button>
									</p>
									<p>
										<label><?php _e('Slide Alt Text', 'ultimate-responsive-image-slider'); ?></label>
										<input type="text" id="rpgp_image_alt[]" name="rpgp_image_alt[]" class="uris-slide-input-text" value="<?php echo esc_attr($slide_alt); ?>" placeholder="<?php _e('Max Length 125 Characters', 'ultimate-responsive-image-slider'); ?>">
									</p>
								</div>
							</li>
							<?php
							$i++;
						} // end of for each
					}
				} else {
					$TotalSlideIds = 0;
				}
				?>
			</ul>

			<!--uris editor modal-->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
					<!-- modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title"><i class="fa fa-edit" style="font-size:23px"></i> Rich Editor</h4>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
						  <p>
							<?php
								$urisp_box = '';
								$urisp_editor_id = 'fetch_wpeditor_data';
								$settings  = array( 'media_buttons' => false ,'quicktags' => array( 'buttons' => 'strong,em,del,link,close' ) ); // for remove media button from editor
								wp_editor( $urisp_box, $urisp_editor_id, $settings); // without media button
							?>
							<input type="hidden" value="" id="fetch_wpelement" name="fetch_wpelement" />
						  </p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" onclick="urisp_richeditor_putdata()" data-dismiss="modal">Continue</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>
						</div>
					</div>
				</div>
			</div>
			<!--rich editor modal-->
		</div>

		<!--Add New Image Button-->
		<div class="uris-control-buttons">
		<div id="uris-add-new-slide" class="uris-add-new-slide" data-uploader_title="Upload Slide" data-uploader_button_text="Select" >
			<div class="dashicons dashicons-plus"></div>
			<p><?php _e('Add New Slide', 'ultimate-responsive-image-slider'); ?></p>
		</div>
		<div id="sort-all-slides" class="uris-clone-slider" onclick="return URISSortSlides('ASC');">
			<div class="dashicons dashicons-sort"></div>
			<p><?php _e("Sort Ascending", 'ultimate-responsive-image-slider'); ?></p>
		</div>
		<div id="sort-all-slides" class="uris-clone-slider" onclick="return URISSortSlides('DESC');">
			<div class="dashicons dashicons-sort"></div>
			<p><?php _e("Sort Descending", 'ultimate-responsive-image-slider'); ?></p>
		</div>
		<div id="uris-clone-slider" class="uris-clone-slider" onclick="return uris_clone_run(<?php echo esc_attr($post->ID); ?>);">
			<div class="dashicons dashicons-admin-page"></div>
			<?php wp_nonce_field('uris_clone_nonce','uris_clone_nonce' ); ?>
			<p><?php _e("Clone Slider (beta)", 'ultimate-responsive-image-slider'); ?></p>
		</div>
		
		<div id="uris-delete-all-slide" class="uris-delete-all-slide">
			<div class="dashicons dashicons-trash"></div>
			<p><?php _e('Delete All Slides', 'ultimate-responsive-image-slider'); ?></p>
		</div>
		
		<div id="uris-clone-success" class="uris-clone-success">
			<h1><?php _e('Slider clone created successfully.', 'ultimate-responsive-image-slider'); ?> <?php _e('Go to', 'ultimate-responsive-image-slider'); ?> <a href="edit.php?post_type=ris_gallery"><?php _e('All Slider', 'ultimate-responsive-image-slider'); ?></a> <?php _e('page to edit cloned slider.', 'ultimate-responsive-image-slider'); ?></h1>
		</div>
		
		<div style="clear:left;"></div>
		<style>
		.review-notice {
			background-color: #27A4DD !important;
			color: #FFFFFF !important;
		}
		</style>
		<script>
		function urisp_richeditor(id){
			var richeditdata = jQuery(".urisp_richeditbox_"+id).val();
			jQuery("#fetch_wpeditor_data").val(richeditdata);
			jQuery("#fetch_wpeditor_data-html").click();
			jQuery("#fetch_wpelement").val(id);
		}
		function urisp_richeditor_putdata(){
			jQuery("#fetch_wpeditor_data").click();
			jQuery("#fetch_wpeditor_data-html").click();
			var fetch_wpelement_id = jQuery("#fetch_wpelement").val();
			var fetched_data = jQuery("#fetch_wpeditor_data").val();
			jQuery(".urisp_richeditbox_"+fetch_wpelement_id).val(fetched_data);
		}
		</script>
		<script>
		function URISSortSlides(order){
			if(order == "ASC") {
				jQuery(".SortSlides li").sort(sort_li).appendTo('.SortSlides');
				function sort_li(a, b) {
					return (jQuery(b).data('position')) > (jQuery(a).data('position')) ? 1 : -1;
				}
			}
			if(order == "DESC") {
				jQuery(".SortSlides li").sort(sort_li).appendTo('.SortSlides');
				function sort_li(a, b) {
					return (jQuery(b).data('position')) < (jQuery(a).data('position')) ? 1 : -1;
				}
			}
		}
		</script>
		<?php
	}

	/**
	 * This function display Add New Image interface
	 * Also loads all saved gallery photos into photo gallery
	 */
	public function ris_settings_meta_box_function($post) {
		wp_enqueue_script('wpfrank-uris-bootstrap-js', URIS_PLUGIN_URL.'assets/js/bootstrap.js');
		wp_enqueue_style('wpfrank-uris-bootstrap-css', URIS_PLUGIN_URL.'assets/css/bootstrap-latest/bootstrap.css');
		wp_enqueue_style('wpfrank-uris-editor-modal', URIS_PLUGIN_URL.'assets/css/editor-modal.css');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('wpfrank-uris-media-uploader-js', URIS_PLUGIN_URL . 'assets/js/wpfrank-uris-multiple-media-uploader.js', array('jquery'));
		wp_enqueue_media();

		//custom add image box CSS
		wp_enqueue_style('wpfrank-uris-settings-css', URIS_PLUGIN_URL.'assets/css/wpfrank-uris-settings.css', array(), '1.0');

		//font awesome CSS
		wp_enqueue_style('wpfrank-uris-font-awesome-all-css', URIS_PLUGIN_URL.'assets/css/font-awesome-latest/css/fontawesome-all.css');

		//tool-tip JS & CSS
		wp_enqueue_script('wpfrank-uris-tool-tip-js',URIS_PLUGIN_URL.'assets/tooltip/jquery.darktooltip.min.js', array('jquery'));
		wp_enqueue_style('wpfrank-uris-tool-tip-css', URIS_PLUGIN_URL.'assets/tooltip/darktooltip.min.css');

		//color-picker CSS n JS
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wpfrank-uris-color-picker-custom-js', plugins_url('assets/js/wpfrank-uris-color-picker-custom.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

		//code-mirror CSS & JS for custom CSS section
		wp_enqueue_style('wpfrank-uris-code-mirror-css', URIS_PLUGIN_URL.'assets/css/codemirror/codemirror.css');
		wp_enqueue_style('wpfrank-uris-blackboard-css', URIS_PLUGIN_URL.'assets/css/codemirror/blackboard.css');
		wp_enqueue_style('wpfrank-uris-show-hint-css', URIS_PLUGIN_URL.'assets/css/codemirror/show-hint.css');

		wp_enqueue_script('wpfrank-uris-code-mirror-js',URIS_PLUGIN_URL.'assets/css/codemirror/codemirror.js',array('jquery'));
		wp_enqueue_script('wpfrank-uris-css-js',URIS_PLUGIN_URL.'assets/css/codemirror/ris-css.js',array('jquery'));
		wp_enqueue_script('wpfrank-uris-css-hint-js',URIS_PLUGIN_URL.'assets/css/codemirror/css-hint.js',array('jquery'));
		require_once('settings.php');
	}
	
	public function ris_shotcode_meta_box_function() { ?>
		<p><?php _e("Use below shortcode in any Page/Post to publish your slider", 'ultimate-responsive-image-slider');?></p>
		<input readonly="readonly" type="text" value="<?php echo esc_attr("[URIS id=".get_the_ID()."]"); ?>" style="width:100%;">
		
		<p><?php _e("To embed slider in any custom theme template", 'ultimate-responsive-image-slider');?></p>
		<?php $uris_shortcode = esc_attr("[URIS id=".get_the_ID()."]"); ?>
		<input readonly="readonly" type="text" value="&#x3c;&#x3f;php do_shortcode&#x28; '<?php echo esc_attr("[URIS id=".get_the_ID()."]"); ?>' &#x29;; &#x3f;&#x3e;" style="width:100%;">
		<?php
	}
	
	public function ris_new_plugin_meta_box_function() { ?>
		<p><a href="https://wordpress.org/plugins/slider-factory/" target="_blank"><img src="<?php echo esc_url(URIS_PLUGIN_URL . "assets/img/products/slider-factory-free-wordpress-plugin.jpg"); ?>" width="100%" /></a></p>
		<p style="text-align: center;"><a href="plugin-install.php?s=slider%20factory&tab=search&type=term" class="button button-primary button-hero">Install Plugin</a></p>
		<?php
	}
	
	public function admin_thumb_uris($id) {
		$attachment = get_post( $id );
		$slide_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
		$slide_caption = $attachment->post_excerpt;
		$slide_description = $attachment->post_content;
		$slide_href = get_permalink( $attachment->ID );
		$slide_src = $attachment->guid;
		$slide_title = $attachment->post_title;
		$slide_medium = wp_get_attachment_image_src($id, 'medium', true);
		$slide_full  = wp_get_attachment_image_src($id, 'full', true);
		$UniqueString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
		?>
		<li id="<?php echo esc_attr($id); ?>" class="uris-slide" data-position="<?php echo esc_attr($id); ?>">
			<a id="uris-slide-delete-icon" class="uris-slide-delete-icon"><img src="<?php echo  esc_url(URIS_PLUGIN_URL.'assets/img/close-icon.png'); ?>" /></a>
			<div>
				<p>
					<img src="<?php echo esc_url($slide_medium[0]); ?>" class="uris-slide-image">
				<p>
					<label><?php _e('Slide Title', 'ultimate-responsive-image-slider'); ?></label>
					<input type="hidden" id="unique_string[]" name="unique_string[]" value="<?php echo esc_attr($UniqueString); ?>" />
					<input type="hidden" id="rpgp_image_id[]" name="rpgp_image_id[]" value="<?php echo esc_attr($id); ?>">
					<input type="text" id="rpgp_image_label[]" name="rpgp_image_label[]" value="<?php echo esc_attr($slide_title); ?>" placeholder="<?php _e('Enter Slide Title Here', 'ultimate-responsive-image-slider'); ?>" class="uris-slide-input-text">
				</p>
				<p>
					<label><?php _e('Slide Description', 'ultimate-responsive-image-slider'); ?></label>
					<textarea rows="4" cols="50" id="rpgp_image_desc[]" name="rpgp_image_desc[]" placeholder="<?php _e('Enter Slide Description Here', 'ultimate-responsive-image-slider'); ?>" class="urisp_richeditbox_<?php echo esc_attr($id); ?> uris-slide-input-text"><?php echo $slide_description; ?></textarea>
					<button type="button" class="btn btn-md btn-info btn-block" data-toggle="modal" data-target="#myModal" onclick="urisp_richeditor(<?php echo esc_attr($id); ?>)"><?php _e('Use Rich Text Editor', 'ultimate-responsive-image-slider'); ?> <i class="fa fa-edit"></i></button>
				</p>
				<p>
					<label><?php _e('Slide Alt Text', 'ultimate-responsive-image-slider'); ?></label>
					<input type="text" id="rpgp_image_alt[]" name="rpgp_image_alt[]" class="uris-slide-input-text" value="<?php echo esc_attr($slide_alt); ?>" placeholder="<?php _e('Max Length 125 Characters', 'ultimate-responsive-image-slider'); ?>">
				</p>
			</div>
		</li>
		<?php
	}

	public function ajax_get_thumbnail_uris() {
		echo esc_html($this->admin_thumb_uris($_POST['imageid']));
		die;
	}

	public function add_image_meta_box_save($PostID) {
		if ( current_user_can( 'manage_options' ) ) {
			if(isset($PostID) && isset($_POST['uris-save-action'])) {
				$TotalSlideIds = count($_POST['rpgp_image_id']);
				$SlideIds = array();
				if($TotalSlideIds) {
					for($i=0; $i < $TotalSlideIds; $i++) {
						$slide_id = sanitize_text_field($_POST['rpgp_image_id'][$i]);
						$slide_title = sanitize_text_field($_POST['rpgp_image_label'][$i]);
						$slide_desc = sanitize_textarea_field($_POST['rpgp_image_desc'][$i]);
						$slide_alt = sanitize_text_field($_POST['rpgp_image_alt'][$i]);
						$SlideIds[] = array(
							'rpgp_image_id' => $slide_id,
						);
						// update attachment image title and description
						$attachment_details = array(
							'ID' => $slide_id,
							'post_title' => $slide_title,
							'post_content' => $slide_desc
						);
						wp_update_post( $attachment_details );
						
						// update attachment alt text
						update_post_meta( $slide_id, '_wp_attachment_image_alt', $slide_alt );
					}
					update_post_meta($PostID, 'ris_all_photos_details', $SlideIds);
				} else {
					update_post_meta($PostID, 'ris_all_photos_details', $SlideIds);
				}
			}
		}
	}

	//save settings meta box values
	public function ris_settings_meta_save($PostID) {
		if ( current_user_can( 'manage_options' ) ) {
			if(isset($PostID) && isset($_POST['wl_action']) == "wl-save-settings") {
				$WRIS_L3_Slide_Title				=	sanitize_text_field ( $_POST['wl-l3-slide-title'] );
				$WRIS_L3_Show_Slide_Title			=	sanitize_text_field ( $_POST['wl-l3-show-slide-title'] );
				$WRIS_L3_Show_Slide_Desc			=	sanitize_textarea_field ( $_POST['wl-l3-show-slide-desc'] );
				$WRIS_L3_Auto_Slideshow				=	sanitize_text_field ( $_POST['wl-l3-auto-slide'] );
				$WRIS_L3_Transition					=	sanitize_text_field ( $_POST['wl-l3-transition'] );
				$WRIS_L3_Transition_Speed			=	sanitize_text_field ( $_POST['wl-l3-transition-speed'] );
				$WRIS_L3_Sliding_Arrow				=	sanitize_text_field ( $_POST['wl-l3-sliding-arrow'] );
				$WRIS_L3_Slider_Navigation			=	sanitize_text_field ( $_POST['wl-l3-navigation'] );
				$WRIS_L3_Navigation_Button			=	sanitize_text_field ( $_POST['wl-l3-navigation-button'] );
				$WRIS_L3_Slider_Width				=	sanitize_text_field ( $_POST['wl-l3-slider-width'] );
				$WRIS_L3_Slider_Height				=	sanitize_text_field ( $_POST['wl-l3-slider-height'] );
				$WRIS_L3_Font_Style					=	sanitize_text_field ( $_POST['wl-l3-font-style'] );
				$WRIS_L3_Title_Color				=	sanitize_text_field ( $_POST['wl-l3-title-color'] );
				$WRIS_L3_Slider_Scale_Mode			=	sanitize_text_field ( $_POST['wl-l3-slider_scale_mode'] );
				$WRIS_L3_Slider_Auto_Scale			=	sanitize_text_field ( $_POST['wl-l3-slider-auto-scale'] );
				$WRIS_L3_Title_BgColor				=	sanitize_text_field ( $_POST['wl-l3-title-bgcolor'] );
				$WRIS_L3_Desc_Color					=	sanitize_text_field ( $_POST['wl-l3-desc-color'] );
				$WRIS_L3_Desc_BgColor				=	sanitize_text_field ( $_POST['wl-l3-desc-bgcolor'] );
				$WRIS_L3_Navigation_Color			=	sanitize_text_field ( $_POST['wl-l3-navigation-color'] );
				$WRIS_L3_Fullscreeen				=	sanitize_text_field ( $_POST['wl-l3-fullscreen'] );
				$WRIS_L3_Custom_CSS					=	sanitize_text_field ( $_POST['wl-l3-custom-css'] );
				$WRIS_L3_Slide_Order				= 	sanitize_text_field ( $_POST['wl-l3-slide-order'] );
				$WRIS_L3_Slide_Distance				= 	sanitize_text_field ( $_POST['wl-l3-slide-distance'] );
				$WRIS_L3_Thumbnail_Style			= 	sanitize_text_field ( $_POST['wl-l3-thumbnail-style'] );
				$WRIS_L3_Navigation_Position		= 	sanitize_text_field ( $_POST['wl-l3-navigation-position'] );
				$WRIS_L3_Thumbnail_Width			= 	sanitize_text_field ( $_POST['wl-l3-navigation-width'] );
				$WRIS_L3_Thumbnail_Height			= 	sanitize_text_field ( $_POST['wl-l3-navigation-height'] );
				$WRIS_L3_Width						= 	sanitize_text_field ( $_POST['wl-l3-width'] );
				$WRIS_L3_Height						= 	sanitize_text_field ( $_POST['wl-l3-height'] );
				$WRIS_L3_Navigation_Bullets_Color	= 	sanitize_text_field ( $_POST['wl-l3-navigation-bullets-color'] );
				$WRIS_L3_Navigation_Pointer_Color	=	sanitize_text_field ( $_POST['wl-l3-navigation-pointer-color'] );

				$WRIS_Settings_Array = array(
					'WRIS_L3_Slide_Title'  			=>	$WRIS_L3_Slide_Title,
					'WRIS_L3_Show_Slide_Title'		=>	$WRIS_L3_Show_Slide_Title,
					'WRIS_L3_Show_Slide_Desc'		=>	$WRIS_L3_Show_Slide_Desc,
					'WRIS_L3_Auto_Slideshow'  		=>	$WRIS_L3_Auto_Slideshow,
					'WRIS_L3_Transition'  			=>	$WRIS_L3_Transition,
					'WRIS_L3_Transition_Speed'  	=>	$WRIS_L3_Transition_Speed,
					'WRIS_L3_Sliding_Arrow'  		=>	$WRIS_L3_Sliding_Arrow,
					'WRIS_L3_Slider_Navigation'  	=>	$WRIS_L3_Slider_Navigation,
					'WRIS_L3_Navigation_Button'  	=>	$WRIS_L3_Navigation_Button,
					'WRIS_L3_Slider_Width'  		=>	$WRIS_L3_Slider_Width,
					'WRIS_L3_Slider_Height'  		=>	$WRIS_L3_Slider_Height,
					'WRIS_L3_Font_Style'  			=>	$WRIS_L3_Font_Style,
					'WRIS_L3_Title_Color'   		=>	$WRIS_L3_Title_Color,
					'WRIS_L3_Slider_Scale_Mode'		=>	$WRIS_L3_Slider_Scale_Mode,
					'WRIS_L3_Slider_Auto_Scale'		=>	$WRIS_L3_Slider_Auto_Scale,
					'WRIS_L3_Title_BgColor'   		=>	$WRIS_L3_Title_BgColor,
					'WRIS_L3_Desc_Color'   			=>	$WRIS_L3_Desc_Color,
					'WRIS_L3_Desc_BgColor'  		=>	$WRIS_L3_Desc_BgColor,
					'WRIS_L3_Navigation_Color' 		=>	$WRIS_L3_Navigation_Color,
					'WRIS_L3_Fullscreeen' 			=>	$WRIS_L3_Fullscreeen,
					'WRIS_L3_Custom_CSS'  			=>	$WRIS_L3_Custom_CSS,
					'WRIS_L3_Slide_Order'   		=>	$WRIS_L3_Slide_Order,
					'WRIS_L3_Slide_Distance'   		=>	$WRIS_L3_Slide_Distance,
					'WRIS_L3_Thumbnail_Style'   	=>	$WRIS_L3_Thumbnail_Style,
					'WRIS_L3_Navigation_Position'   =>	$WRIS_L3_Navigation_Position,
					'WRIS_L3_Thumbnail_Width'   	=>	$WRIS_L3_Thumbnail_Width,
					'WRIS_L3_Thumbnail_Height'   	=>	$WRIS_L3_Thumbnail_Height,
					'WRIS_L3_Width'   				=>	$WRIS_L3_Width,
					'WRIS_L3_Height'   				=>	$WRIS_L3_Height,
					'WRIS_L3_Navigation_Bullets_Color'		=>	$WRIS_L3_Navigation_Bullets_Color,
					'WRIS_L3_Navigation_Pointer_Color'		=>	$WRIS_L3_Navigation_Pointer_Color,
				);
				$WRIS_Gallery_Settings = "WRIS_Gallery_Settings_".$PostID;
				update_post_meta($PostID, $WRIS_Gallery_Settings, $WRIS_Settings_Array);
			}
		}
	}
}

global $URIS;
$URIS = URIS::forge();

// All Slider Post Features Box
add_action( "admin_notices", "uris_admin_notice_resport" );
function uris_admin_notice_resport() {
	global $pagenow;
	$uris_screen = get_current_screen();
	if ( $pagenow == 'edit.php' && $uris_screen->post_type == "ris_gallery" && ! isset( $_GET['page'] ) ) {
		require_once ( 'admin-banner.php' );
	}
}

// upgrade to pro
add_action('admin_menu' , 'uris_menu_pages');
function uris_menu_pages() {
	if ( current_user_can( 'manage_options' ) ) {
		add_submenu_page('edit.php?post_type=ris_gallery', 'Recover Old Sliders', 'Recover Old Sliders', 'administrator', 'uris-recover-slider', 'uris_recover_slider_page');
		add_submenu_page('edit.php?post_type=ris_gallery', 'Help & Support', 'Help & Support', 'administrator', 'uris-help-page', 'uris_help_and_support_page');
		function uris_recover_slider_page() {
			require_once('recover-slider.php');
		}
		function uris_help_and_support_page() {
			wp_enqueue_style('bootstrap-admin.css', URIS_PLUGIN_URL.'assets/css/bootstrap-latest/bootstrap-admin.css');
			require_once('help-and-support.php');
		}
	}
}

// URIS Shortcode
require_once("shortcode.php");
require_once('products.php');
?>