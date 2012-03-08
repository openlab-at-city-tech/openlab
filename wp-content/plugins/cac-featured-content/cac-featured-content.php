<?php
/*
Plugin Name: CAC Featured Content
Plugin URI:
Description: This plugin allows site authors to choose what content is to be featured on the home page of the cac site.
Version: 0.8.4
Author: Michael McManus, Cast Iron Coding
Author URI: castironcoding.com
License: GPL2
*/

/*
 * A little utility class that gets used through out
 */
require_once('cac-featured-content-helper.php');

//There was a plan at one point to include some more advanced interaction for the widget in the admin area. Holding off on that for now, but want to keep the includes around so I don't forget.
// wp_register_script('cac-featured-content', WP_PLUGIN_URL . '/cac-featured-content/script.js');
// require_once('cac-featured-content-options.php');
// require_once('cac-featured-content-ajax.php');

/*
	This loads the javascript that controls the widget in the admin area.
*/
add_action('admin_head', 'cac_featured_content_widget_js');
add_action('widgets_init', create_function('', 'return register_widget("Cac_Featured_Content_Widget");'));

function cac_featured_content_widget_js() {
	?>
		<script type="text/javascript" charset="utf-8">
			var cacFeature = {
				/*
					Initializes the widget interface.
				*/
				init: function() {
					// Gather ALL the widgets.
					widgets = jQuery('input[value^=cac_featured_content]').parent('form');
					// Do something with them.
					widgets.each(function() {
						// Get the id.
						widgetId = jQuery(this).children('input[value^=cac_featured_content]').val();
						// This is nice isn't it? cac_featured_content-__i__ is the id of the dummy widget that gets dragged over by the user.
						if(widgetId != 'cac_featured_content-__i__') {
							widget = jQuery(this);
							cacFeature.hideAll(widget);
							cacFeature.showSelected(widgetId);
							cacFeature.imageRemoveSetup(widgetId);
							// Now let's set-up a hanlder to react the type select box changing
							select.change( function() {
								cacFeature.typeChange(this);
							});
						}
					});
				},
				/*
					This function runs after the content-type select box has changed. Basically, it hides all of the
					edit fields and then calls showSelected() to reveal only the fields for the chosen type.
				*/
				typeChange: function(el) {
					// el is the select box element that just changed
					el = jQuery(el);
					form = el.parents('form');
					cacFeature.hideAll(form);
					widgetId = form.children('input[value^=cac_featured_content]').val();
					cacFeature.showSelected(widgetId);
				},
				/**
				* Given a widget id, find the selected feature type and show the edit fields for that type.
				*
				*/
				showSelected: function(widgetId) {
					select = jQuery('#widget-'+widgetId+'-type');
					selected = select.children(':selected').val();
					select.parents('form').find('.cac-featured-'+selected).show();
				},
				/*
					Hides all of the featured content type edit fields.
				*/
				hideAll: function(widget) {
					widget.find('.cac-featured-blog, .cac-featured-post, .cac-featured-member, .cac-featured-resource, .cac-featured-group').hide();
				},
				/**
				*	This is just a little somehting that allows the user to remove an image once it gets added. That's useful, right?
				*/
				imageRemoveSetup: function(widgetId) {
					var addImageAnchor = jQuery('#add_image-widget-'+widgetId+'-image');
					anchor = jQuery('#display-widget-'+widgetId+'-image a.remove');
					var image = jQuery('#display-widget-'+widgetId+'-image img');
					var imageVal = jQuery('#widget-'+widgetId+'-image');
					anchor.click(function() {
						image.hide();
						jQuery(this).hide();
						imageVal.val('');
						icon = addImageAnchor.children('img').clone();
						addImageAnchor.text(' Add image').prepend(icon);
						return false;
					});

					if(imageVal.val()) {
						anchor.show();
					} else {
						anchor.hide();
					}
				}
			}
			/*
				Boom! This is where the magic happens.
			*/
			jQuery(document).ready(function() {
				cacFeature.init();
			});
		</script>
	<?php
}

class Cac_Featured_Content_Widget extends WP_Widget {
	// Our allowed featured content types.
	public $types = array('blog', 'group', 'post', 'member', 'resource');
	// Our allowed image crop rules.
	public $image_crop_rules = array(
		'c' => 'Position in the center (default)',
		't' => 'Align top',
		'tr' => 'Align top right',
		'tl' => 'Align top left',
		'b' => 'Align bottom',
		'br' => 'Align bottom right',
		'bl' => 'Align bottom left',
		'l' => 'Align left',
		'r' => 'Align right',
	);

	var $pluginDomain = 'cac_featured_content';

	function Cac_Featured_Content_Widget() {
		$widget_ops = array('classname' => 'cac_featured_content', 'description' => 'A widget that allows you to feature content from across the Commons, including Blogs, Groups, Wiki Articles, and People.' );
		$this->WP_Widget('cac_featured_content', 'Featured', $widget_ops);

		$control_ops = array( 'id_base' => 'widget_cac_featured_content' );

		// The following is also from Shane & Peter.
		global $pagenow;
		if (is_admin() || is_network_admin()) {
    		add_action( 'admin_init', array( $this, 'fix_async_upload_image' ) );
			if ( 'widgets.php' == $pagenow ) {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( $control_ops['id_base'], WP_PLUGIN_URL.'/cac-featured-content/script.js',array('thickbox'), false, true );
				add_action( 'admin_head-widgets.php', array( $this, 'admin_head' ) );
			} elseif ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
				add_filter( 'image_send_to_editor', array( $this,'cac_featured_image_send_to_editor'), 5, 8);
				add_filter( 'gettext', array( $this, 'replace_text_in_thitckbox' ), 1, 3 );
				add_filter( 'media_upload_tabs', array( $this, 'media_upload_tabs' ) );

			}
		}
	}

	/*******************************************************************
	*
	*	BEGIN IMAGE UPLOAD CODE FROM Shane & Peter, Inc. (Peter Chester)
	*
	********************************************************************/

	function fix_async_upload_image() {
		if(isset($_REQUEST['attachment_id'])) {
			$GLOBALS['post'] = get_post($_REQUEST['attachment_id']);
		}
	}

	/**
	 * Retrieve resized image URL
	 *
	 * @param int $id Post ID or Attachment ID
	 * @param int $width desired width of image (optional)
	 * @param int $height desired height of image (optional)
	 * @return string URL
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function get_image_url( $id, $width=false, $height=false ) {

		/**/
		// Get attachment and resize but return attachment path (needs to return url)
		$attachment = wp_get_attachment_metadata( $id );
		$attachment_url = wp_get_attachment_url( $id );
		if (isset($attachment_url)) {
			if ($width && $height) {
				$uploads = wp_upload_dir();
				$imgpath = $uploads['basedir'].'/'.$attachment['file'];
				error_log($imgpath);
				$image = image_resize( $imgpath, $width, $height );
				if ( $image && !is_wp_error( $image ) ) {
					error_log( is_wp_error($image) );
					$image = path_join( dirname($attachment_url), basename($image) );
				} else {
					$image = $attachment_url;
				}
			} else {
				$image = $attachment_url;
			}
			if (isset($image)) {
				return $image;
			}
		}
	}

	/**
	 * Test context to see if the uploader is being used for the image widget or for other regular uploads
	 *
	 * @return void
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function is_sp_widget_context() {
		if ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$this->id_base) !== false ) {
			return true;
		} elseif ( isset($_REQUEST['_wp_http_referer']) && strpos($_REQUEST['_wp_http_referer'],$this->id_base) !== false ) {
			return true;
		} elseif ( isset($_REQUEST['widget_id']) && strpos($_REQUEST['widget_id'],$this->id_base) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Somewhat hacky way of replacing "Insert into Post" with "Insert into Widget"
	 *
	 * @param string $translated_text text that has already been translated (normally passed straight through)
	 * @param string $source_text text as it is in the code
	 * @param string $domain domain of the text
	 * @return void
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function replace_text_in_thitckbox($translated_text, $source_text, $domain) {
		if ( $this->is_sp_widget_context() ) {
			if ('Insert into Post' == $source_text) {
				return __('Insert Into Widget', $this->pluginDomain );
			}
		}
		return $translated_text;
	}

	/**
	 * Filter image_end_to_editor results
	 *
	 * @param string $html
	 * @param int $id
	 * @param string $alt
	 * @param string $title
	 * @param string $align
	 * @param string $url
	 * @param array $size
	 * @return string javascript array of attachment url and id or just the url
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function cac_featured_image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size='thumbnail', $alt = '' ) {
		if ( $this->is_sp_widget_context() ) {
			// Normally, media uploader return an HTML string (in this case, typically a complete image tag surrounded by a caption).
			// Don't change that; instead, send custom javascript variables back to opener.
			// Check that this is for the widget. Shouldn't hurt anything if it runs, but let's do it needlessly.
			$size = 'thumbnail';
			preg_match_all('/<img[^>]+>/i',$html, $result);

			// We really just assume one image
			foreach($result as $img_tag) {

				preg_match_all('/(alt|title|src)=("[^"]*")/i',$img_tag[0], $result);

			}
			$img_src = $result[0][0];
			$img_path = $this->get_fully_qualified_image_path($img_src);
			// Unlike Shane & Peter, we use timthumb to crop the image sent back by the uploader so that it fits within the dimensions of the widget editor.
			$html = '<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/cac-featured-content/timthumb.php?src='.$img_path.' &h=50&w=50&q=100&a=l" class="avatar" width="50" hight="50"/>';

			if ($alt=='') $alt = $title;
			?>
			<script type="text/javascript">
				// send image variables back to opener
				var win = window.dialogArguments || opener || parent || top;
				win.IW_html = '<?php echo addslashes($html) ?>';
				win.IW_img_id = '<?php echo $id ?>';
				win.IW_alt = '<?php echo addslashes($alt) ?>';
				win.IW_caption = '<?php echo addslashes($caption) ?>';
				// win.IW_title = '<?php echo addslashes($title) ?>';
				win.IW_align = '<?php echo $align ?>';
				win.IW_url = '<?php echo $url ?>';
				win.IW_size = '<?php echo $size ?>';
			</script>
			<?php
		}
		return $html;
	}

	/**
	 * Remove from url tab until that functionality is added to widgets.
	 *
	 * @param array $tabs
	 * @return void
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function media_upload_tabs($tabs) {
		if ( $this->is_sp_widget_context() ) {
			unset($tabs['type_url']);
		}
		return $tabs;
	}

	/**
	 * Admin header css
	 *
	 * @return void
	 * @author Shane & Peter, Inc. (Peter Chester)
	 */
	function admin_head() {
		?>
		<style type="text/css">
			.aligncenter {
				display: block;
				margin-left: auto;
				margin-right: auto;
			}
		</style>
		<?php
	}

	/*******************************************************************
	*
	*	END IMAGE UPLOAD CODE FROM Shane & Peter, Inc. (Peter Chester)
	*
	********************************************************************/

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		/*****************************************************
		 *****Widget Instance Data and other parameters ******
		******************************************************/
		$this->title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		$this->image_width = empty($instance['image_width']) ? '&nbsp;' : apply_filters('widget_image_width', $instance['image_width']);
		$this->image_height = empty($instance['image_height']) ? '&nbsp;' : apply_filters('widget_image_height', $instance['image_height']);
		$this->imageurl = $this->get_fully_qualified_image_path($instance['imageurl']);
		
		// Crop length fallback
		if ( !empty( $instance['crop_length'] ) && (int) $instance['crop_length'] ) {
			$this->crop_length = (int) $instance['crop_length'];
		} else {
			$this->crop_length = apply_filters( 'cacfc_default_crop_length', 100 );
		}
		
		// Backward compatibility filter. Don't use it.
		$this->crop_length = apply_filters( 'widget_crop_length', $this->crop_length );
		
		// No longer used. Use bp_create_excerpt() instead
		$this->image_crop_rule = empty($instance['image_crop_rule']) ? '&nbsp;' : apply_filters('widget_image_crop_rule', $instance['image_crop_rule']);
		
		// If no Read More text is provided, don't show a link at all
		$this->read_more_text = empty($instance['read_more_text']) ? '' : apply_filters('widget_read_more_text', $instance['read_more_text']);
		
		$type = empty($instance['type']) ? '&nbsp;' : apply_filters('widget_id', $instance['type']);
		$this->blog_id = empty($instance['blog_id']) ? '&nbsp;' : apply_filters('widget_id', $instance['blog_id']);
		$this->blog_domain = empty($instance['blog_domain']) ? '&nbsp;' : apply_filters('widget_id', $instance['blog_domain']);
		$this->post_domain = empty($instance['post_domain']) ? '&nbsp;' : apply_filters('widget_id', $instance['post_domain']);
		$this->post_slug = empty($instance['post_slug']) ? '&nbsp;' : apply_filters('widget_id', $instance['post_slug']);
		$this->group_slug = empty($instance['group_slug']) ? '&nbsp;' : apply_filters('widget_id', $instance['group_slug']);
		$this->group_id = empty($instance['group_id']) ? '&nbsp;' : apply_filters('widget_id', $instance['group_id']);
		$this->member_identifier = empty($instance['member_identifier']) ? '&nbsp;' : apply_filters('widget_id', $instance['member_identifier']);
		$this->resource_link = empty($instance['resource_link']) ? '&nbsp;' : apply_filters('widget_id', $instance['resource_link']);
		$this->resource_text = empty($instance['resource_text']) ? '&nbsp;' : apply_filters('widget_id', $instance['resource_text']);
		$this->resource_title = empty($instance['resource_title']) ? '&nbsp;' : apply_filters('widget_id', $instance['resource_title']);
		$this->resource_image_source = empty($instance['resource_image_source']) ? '&nbsp;' : apply_filters('widget_id', $instance['resource_image_source']);
		$this->crop_config = $this->crop_length."|&nbsp;...|1";

		/**********************
		****** OUTPUT *********
		***********************/
		echo $before_widget;
		// Load the utility class
		$this->cacfc = new CACFeaturedContentHelper();
		$renderMethod = 'renderType_'.strtoupper($type);
		$this->$renderMethod();
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['image'] = $new_instance['image'];
		$instance['imageurl'] = $this->get_image_url($new_instance['image'],100,100);  // image resizing not working right now
		$instance['image_title'] = strip_tags($new_instance['image_title']);
		$instance['image_crop_rule'] = strip_tags($new_instance['image_crop_rule']);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['crop_length'] = strip_tags($new_instance['crop_length']);
		$instance['image_width'] = strip_tags($new_instance['image_width']);
		$instance['image_height'] = strip_tags($new_instance['image_height']);
		$instance['read_more_text'] = strip_tags($new_instance['read_more_text']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['blog_id'] = strip_tags($new_instance['blog_id']);
		$instance['blog_domain'] = strip_tags($new_instance['blog_domain']);
		$instance['post_domain'] = strip_tags($new_instance['post_domain']);
		$instance['post_slug'] = strip_tags($new_instance['post_slug']);
		$instance['group_slug'] = strip_tags($new_instance['group_slug']);
		$instance['group_id'] = strip_tags($new_instance['group_id']);
		$instance['member_identifier'] = strip_tags($new_instance['member_identifier']);
		$instance['resource_link'] = strip_tags($new_instance['resource_link']);
		$instance['resource_text'] = strip_tags($new_instance['resource_text'], '<img>');
		$instance['resource_title'] = strip_tags($new_instance['resource_title']);
		$instance['resource_image_source'] = strip_tags($new_instance['resource_image_source']);

		return $instance;
	}

	function form($instance) {
		/**********************
		 *****Form Config******
		***********************/

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'blog_id' => '',
				'image' => '',
				'image_title' => '',
				'imageurl' => '',
				'type' => '',
				'title' => '',
				'crop_length' =>'',
				'image_crop_rule',
				'image_width' =>'',
				'image_height' =>'',
				'read_more_text' => '',
				'blog_domain' => '',
				'post_domain' => '',
				'post_slug' => '',
				'group_slug' => '',
				'group_id' => '',
				'member_identifier' => '',
				'resource_link' => '',
				'resource_text' => '',
				'resource_title' => '',
				'resource_image_source' => ''
			)
		);

		$blog_id 		= strip_tags($instance['blog_id']);
		$type 			= strip_tags($instance['type']);
		$title 			= strip_tags($instance['title']);
		$crop_length 		= strip_tags($instance['crop_length']);
		$image_width 		= strip_tags($instance['image_width']);
		$image_height 		= strip_tags($instance['image_height']);
		$image_crop_rule 	= isset( $instance['image_crop_rule'] ) ? strip_tags($instance['image_crop_rule']) : '';
		$read_more_text 	= strip_tags($instance['read_more_text']);
		$blog_domain 		= $instance['blog_domain'];
		$post_domain 		= $instance['post_domain'];
		$post_slug		= $instance['post_slug'];
		$group_slug		= $instance['group_slug'];
		$member_identifier 	= $instance['member_identifier'];
		$resource_link 		= $instance['resource_link'];
		$resource_text 		= $instance['resource_text'];
		$resource_title 	= $instance['resource_title'];
		$resource_image_source 	= $instance['resource_image_source'];

		/********************
		 *****Form Data******
		*********************/


		/**********************
		 *****Form Markup******
		 **********************/
		?>

		<!-- We need to re-initalize the widgets every time they get loaded since saving a widget reloads it. Sigh... -->
		<script type="text/javascript">
			cacFeature.init();
		</script>

			<label for="<?php echo $this->get_field_id('title'); ?>"><b>Title: </b>
				If you leave this field blank, a generic title will be automatically rendered.
				</label>
			<p></p>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /><br/><br/>

		<?php echo $this->getCropLengthField($crop_length, true); ?>

		<?php echo $this->getImageWidthAndHeightFields($image_width, $image_height, true); ?>

		<?php echo $this->getReadMoreTextField($read_more_text, true); ?>

		<?php
		// Type Field
		echo $this->getTypeSelectList($type);

		/***** POST FIELDS *****/
		?>
		<hr />
		<div class="cac-featured-post">
			<h3> Featured Post Info</h3>
			<?php
			// Blog Domain Text Input
			echo $this->getPostDomainField($post_domain, true);
			// Post Title Text Input
			echo $this->getPostTitleField($post_slug, true);
			?>
			<hr />
		</div>

		<div class="cac-featured-group">
			<h3> Featured Group Info</h3>
			<?php
			echo $this->getGroupNameField($group_slug, true);
			?>
			<hr />
		</div>

		<div class="cac-featured-member">
			<h3> Featured Member Info</h3>
			<?php
			echo $this->getMemberIdentifierField($member_identifier, true);
			?>
			<hr />
		</div>

		<div class="cac-featured-blog">
			<h3>Featured Blog Info</h3>
			<?php
			echo $this->getBlogDomainField($blog_domain, true);
			?>
			<hr />
		</div>

		<div class="cac-featured-resource">
			<h3>Featured Resource</h3>
			<?php
			echo $this->getResourceLinkField($resource_link, true);
			?>
			<?php
			echo $this->getResourceTitleField($resource_title, true);
			?>
			<?php
			echo $this->getResourceTextField($resource_text, true);
			?>
			<p>You can choose to use an image with an external source, or select an image from the media library.</p>
			<hr />
			<p><label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image from the media library:', $this->pluginDomain); ?></label>
			<?php
				$media_upload_iframe_src = "media-upload.php?type=image&widget_id=".$this->id; //NOTE #1: the widget id is added here to allow uploader to only return array if this is used with image widget so that all other uploads are not harmed.
				$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src");
				$image_title = __(($instance['image'] ? 'Change Image' : 'Add Image'), $this->pluginDomain);
			?><br />
			<a href="<?php echo $image_upload_iframe_src; ?>&TB_iframe=true" id="add_image-<?php echo $this->get_field_id('image'); ?>" class="thickbox-cac-featured-content-widget" title='<?php echo $image_title; ?>' onClick="set_active_widget('<?php echo $this->id; ?>');return false;" style="text-decoration:none"><img src='images/media-button-image.gif' alt='<?php echo $image_title; ?>' align="absmiddle" /> <?php echo $image_title; ?></a>
			<div id="display-<?php echo $this->get_field_id('image'); ?>"><?php
			if ($instance['imageurl']) {
				echo '<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/cac-featured-content/timthumb.php?src='.$this->get_fully_qualified_image_path($instance['imageurl']).'&w=50&q=100" class="avatar" width="50" hight="50"/>';
				// echo "<img src=\"{$instance['imageurl']}\" alt=\"{$instance['image_title']}\" style=\"";
				// 					if ($instance['width'] && is_numeric($instance['width'])) {
				// 						echo "max-width: {$instance['width']}px;";
				// 					}
				// 					if ($instance['height'] && is_numeric($instance['height'])) {
				// 						echo "max-height: {$instance['height']}px;";
				// 					}
				// 					echo "\"";
				// 					if (!empty($instance['align']) && $instance['align'] != 'none') {
				// 						echo " class=\"align{$instance['align']}\"";
				// 					}
				// 					echo " />";
			}
			?>
			<br />
			<a class="remove">Remove image</a>
			</div>
			<input id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="hidden" value="<?php echo $instance['image']; ?>" />
			</p>
			<?php
			echo $this->getResourceImageSourceField($resource_image_source, true);
			?>
			<?php
			echo $this->getImageCropRuleField($image_crop_rule, true);
			?>
		</div>
<?php
	}

	function getCropLengthField($value, $pWrap = false) {
		$labelText = "<b>Crop length: </b>Please enter the length (in characters) that you want to crop text by: <br/>";
		$label = '<label for="'.$this->get_field_id('crop_length').'">'.$labelText.'</label>';
		$input = $this->buildInputField('crop_length', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getImageCropRuleField($value, $pWrap = false) {
		$labelText = "<b>Image Cropping instructions: </b>Whether you've chosen an image from the media library or inserted an external url, the image is going to be cropped to fit the dimensions specified above. Here you can enter a rule to tell the image cropper how to crop the image: <br/>";
		$label = '<label for="'.$this->get_field_id('image_crop_rule').'">'.$labelText.'</label>';

		$input = $this->getImageCropRuleSelectField($value);

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getImageWidthAndHeightFields($width, $height, $pWrap = false) {
		$halfWidth = true;
		$widthLabelText = "W: ";
		$widthLabel = '<label " for="'.$this->get_field_id('image_width').'">'.$widthLabelText.'</label>';
		$widthInput = $this->buildInputField('image_width', $width, $halfWidth);

		$heightLabelText = "&nbsp;&nbsp;x H: ";
		$heightLabel = '<label  for="'.$this->get_field_id('image_height').'">'.$heightLabelText.'</label>';
		$heightInput = $this->buildInputField('image_height', $height, $halfWidth);

		$out = '<label><b>Image Dimensions: </b></label><br/><br/>'.$widthLabel.' '.$widthInput;
		$out .= $heightLabel.' '.$heightInput;
		$out .= '<br/><br/>';

		return $out;
	}

	function getImageHeightField($value, $pWrap = false) {
		$labelText = "Height: <br/>";
		$label = '<label for="'.$this->get_field_id('image_height').'">'.$labelText.'</label>';
		$size = 10;
		$input = $this->buildInputField('image_height', $value, $size);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getResourceTitleField($value, $pWrap = false) {
		$labelText = "Please enter title of your resource: <br/>";
		$label = '<label for="'.$this->get_field_id('resource_title').'">'.$labelText.'</label>';
		$input = $this->buildInputField('resource_title', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getReadMoreTextField($value, $pWrap = false) {
		$labelText = "<b>Read more text:</b> What should the 'read more' text say:";
		$label = '<label for="'.$this->get_field_id('read_more_text').'">'.$labelText.'</label><br/>';
		$input = $this->buildInputField('read_more_text', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getResourceLinkField($value, $pWrap = false) {
		$labelText = "Please enter the url you'd like the title to link to: <br/>";
		$label = '<label for="'.$this->get_field_id('resource_link').'">'.$labelText.'</label>';
		$input = $this->buildInputField('resource_link', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getResourceTextField($value, $pWrap = false) {
		$labelText = "Please enter the text content to display beneath the link (will be cropped according to the crop-length specified above): <br/>";
		$label = '<label for="'.$this->get_field_id('resource_text').'">'.$labelText.'</label>';
		$input = $this->buildTextAreaField('resource_text', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getResourceImageSourceField($value, $pWrap = false) {
		$labelText = "Please enter the url of an external image you'd like to use.: <br/>";
		$label = '<label for="'.$this->get_field_id('resource_image_source').'">'.$labelText.'</label>';
		$input = $this->buildInputField('resource_image_source', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getMemberIdentifierField($value, $pWrap = false) {
		$labelText = "Please enter the member identifier you'd like to feature: <br/>";
		$label = '<label for="'.$this->get_field_id('member_identifier').'">'.$labelText.'</label>';
		$input = $this->buildInputField('member_identifier', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getGroupNameField($value, $pWrap = false) {
		$labelText = "Please enter the name of the group you'd like to feature: <br/>";
		$label = '<label for="'.$this->get_field_id('group_slug').'">'.$labelText.'</label>';
		$input = $this->buildInputField('group_slug', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getPostTitleField($value, $pWrap = false) {
		$labelText = "Please enter the title-slug of the post you'd like to feature: <br/>";
		$label = '<label for="'.$this->get_field_id('post_slug').'">'.$labelText.'</label>';
		$input = $this->buildInputField('post_slug', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getBlogDomainField($value, $pWrap = false) {
		$labelText = "Please enter the domain of the blog you'd like to feature: <br/>";
		$label = '<label for="'.$this->get_field_id('blog_domain').'">'.$labelText.'</label>';
		$input = $this->buildInputField('blog_domain', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function getPostDomainField($value, $pWrap = false) {
		$labelText = "Please enter the domain of the blog you'd like to feature: <br/>";
		$label = '<label for="'.$this->get_field_id('post_domain').'">'.$labelText.'</label>';
		$input = $this->buildInputField('post_domain', $value);

		if($pWrap) {
			$input = '<p>'.$input.'</p>';
		}

		$out = $label.'<br/>'.$input;
		return $out;
	}

	function titleDefaults($title, $type) {
		if(!$title) {
			$title = 'Featured '.$type;
		}
		return $title;
	}

	function getTypeSelectList($instance) {
		$labelText = "<b>Content type: </b>Please select the type of content you'd like to feature:";
		$label = '<label for="'.$this->get_field_id('type').'">'.$labelText.'</label><br/>';


		$select = '<select id="'.$this->get_field_id('type').'" name="'.$this->get_field_name('type').'">';
		foreach($this->types as $type) {
			$type == $instance ? $selected = 'selected="selected"' : $selected ='';
			$select .= '<option value="'.$type.'"'.$selected.'>'.ucfirst($type).'</option>';
		}
		$select .= '</select>';

		$out = '<p>'.$label.'<br/>'.$select.'</p>';
		return $out;
	}

	function getImageCropRuleSelectField($instance) {

		$select = '<select id="'.$this->get_field_id('image_crop_rule').'" name="'.$this->get_field_name('image_crop_rule').'">';
		foreach($this->image_crop_rules as $rule => $label) {
			$rule == $instance ? $selected = 'selected="selected"' : $selected ='';
			$select .= '<option value="'.$rule.'"'.$selected.'>'.$label.'</option>';
		}
		$select .= '</select>';

		return $select;
	}

	function buildInputField($field, $intVal, $halfWidth = false) {
		if ($halfWidth) {
			$halfWidth = 'style="width: 30%;"';
		}
		return '<input class="widefat" id="'.$this->get_field_id($field).'" name='.$this->get_field_name($field).' type="text" value="'.esc_attr($intVal).'" '.$halfWidth.'/>';
	}

	function buildTextAreaField($field, $intVal) {
		return '<textarea rows="16" cols="20" class="widefat" id="'.$this->get_field_id($field).'" name='.$this->get_field_name($field).' type="text">'.$intVal.'</textarea>';
	}


	function get_fully_qualified_image_path($theImageSrc) {
		global $blog_id;
		if (isset($blog_id) && $blog_id > 0) {
			$imageParts = explode('/files/', $theImageSrc);
			if (isset($imageParts[1])) {
				$theImageSrc = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
			}
		}
		return $theImageSrc;
	}

	public function renderType_POST() {
		$cacfc = new CACFeaturedContentHelper();
		$blog = $cacfc->getBlogByDomain($this->post_domain, true);

		$blog_id = $blog->blog_id;
		$public = $blog->public;
		$post_count = $blog->post_count;
		$blog_name = $blog->blogname;
		$site_url = $blog->siteurl;
		$post_slug = $this->post_slug;
		$blog_admin_email = get_blog_option($blog_id, 'admin_email');
		$blog_admin_id = get_user_id_from_string($blog_admin_email);
		$blog_description = get_blog_option($blog->blog_id,'blogdescription');
		$post = $cacfc->getPostBySlug($post_slug, $blog_id);
		
		$post_excerpt = bp_create_excerpt($post->post_content);
		$author_id = $post->post_author;
		$author_email = get_the_author_meta('user_email',$author_id);

		if ( !$height = (int)$this->image_height )
			$height = '100';

		if ( !$width = (int)$this->image_width )
			$width = '100';

		// Avatar will default to blog avatar if there is no post image
		$avatar = bp_core_fetch_avatar( array( 'item_id' => $author_id, 'type' => 'full', 'height' => $height , 'width' => $width, 'no_grav' => false ) );
		
		$avatar = apply_filters( 'cac_featured_content_blog_avatar', $avatar, $blog_id );

		/*************************
		******Switch Context******
		**************************/
		switch_to_blog($blog_id);

		$the_posts = new WP_Query( array( 'p' => $post->ID, 'posts_per_page' => 1 ) );

		//Set the loop for this one post
		if ( $the_posts->have_posts() ) { while( $the_posts->have_posts() ) : $the_posts->the_post();

		// Ok, we're just going to go in search of an image in the post_content
		$post_with_one_image = $this->getPostContentImage(get_the_content());
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_with_one_image, $matches);

		$first_img = isset( $matches[1][0] ) ? $this->get_fully_qualified_image_path($matches[1][0]) : '';
		$the_post_image_link = isset( $matches[0][0] ) ? $matches[0][0] : '';

		if($this->title == '&nbsp;') {
		    $header = 'Featured Post';
		} else {
		    $header = $this->title;
		}
		
		?>
		<h3><?php echo $header ?></h3>
			<div>
				<?php if($the_post_image_link): ?>
					<?php echo $the_post_image_link ?>
					<!-- <img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/cac-featured-content/timthumb.php?src=<?php echo $the_post_image_link ?>&h=<?php echo $this->image_height ?>&w=<?php echo $this->image_width ?>&q=100&a=c" class="avatar" /> -->
				<?php else: ?>
				<?php echo $avatar; ?>
				<?php endif; ?>
				<div class="cac-content">
				<h4><a href="<?php echo the_permalink() ?>"><?php echo get_the_title() ?></a></h4>
				<!-- <p>by&nbsp;<a style="display: block;" href="<?php echo bp_core_get_user_domain($author_id) ?>"><?php the_author() ?></a></p> -->
				<!-- from the blog <a href="<?php echo $site_url ?>"><em style="line-height: 14px; display: block; margin-top: 10px;"><?php bloginfo('name') ?></em></a> -->
				<!-- <div class="clear"></div> -->
				<p><?php echo bp_create_excerpt( get_the_content(), $this->crop_length ); ?></p>
				
				<?php if ( $this->read_more_text ) : ?>
				<p class="more">
					<?php $moreLink = '<a href="'.get_permalink().'">'.$this->read_more_text.'</a>'; ?>
					<?php echo $moreLink; ?>
				</p>
				<?php endif ?>
				
				</div>
			</div>
		<?php
		endwhile; }
		restore_current_blog();
	}

	/**
	*
	* This is pretty much taken directly from buddy press. Given some post content this method will return an html <img> element.
	* The element's src attribute will be set to the path of the first image found in the post. The img's width and height attributes
	* will be set so that the image fits within the width, specified in the widget form -- the resulting dimensions are ratio aware, so
	* height will vary. If you'd rather have the image be scaled relative to height, then see the comment below that tells you how to
	* get that.
	*
	* @param $content str - The content to work with
	* @return $content str - The content with images stripped and replaced with a single thumb.
	**/
	function getPostContentImage($content, $failreturn = 'content') {
		preg_match_all( '/<img[^>]*>/Ui', $content, $matches );
		$content = preg_replace('/<img[^>]*>/Ui', '', $content );

		if ( !empty( $matches[0][0] ) ) {
			/* Get the SRC value */
			preg_match( '/<img.*?(src\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $src );

			/* Get the width and height */
			preg_match( '/<img.*?(height\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $height );
			preg_match( '/<img.*?(width\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $width );

			if ( !empty( $src ) ) {
				$src = substr( substr( str_replace( 'src=', '', $src[1] ), 0, -1 ), 1 );
				$height = substr( substr( str_replace( 'height=', '', $height[1] ), 0, -1 ), 1 );
				$width = substr( substr( str_replace( 'width=', '', $width[1] ), 0, -1 ), 1 );

				if ( empty( $width ) || empty( $height ) ) {
					$width = 100;
					$height = 100;
				}
				// This was modified from the original so that the width gets set to whatever the user has selected in the plugin.
				if ( (int) $this->image_width ) {
					$ratio = (int)$height / (int)$width;
					$new_width = $this->image_width;
					$new_height = $new_width * $ratio;
				} else {
					$new_width = $width;
					$new_height= $height;
				}

				// If you wanted the image to be scaled to fit a height, you could use the following:
				// $ratio = (int)$width / (int)$height;
				// $new_height = $this->image_height;
				// $new_width = $new_height * $ratio;

				$content = '<img class="avatar" src="' . esc_attr( $src) . '" width="' . $new_width . '" height="' . $new_height . '" alt="' . __( 'Thumbnail', 'buddypress' ) . '" class="align-left thumbnail" />';
			}
		} else {
			if ( !$failreturn ) {
				$content = false;
			}
		}
		return $content;
	}

	public function renderType_BLOG() {
		$cacfc = new CACFeaturedContentHelper();
		$blog = $cacfc->getBlogByDomain($this->blog_domain, true);

		$description = get_blog_option($blog->blog_id,'blogdescription');
		$blog_id = $blog->blog_id;
		$public = $blog->public;
		$post_count = $blog->post_count;
		$blog_name = $blog->blogname;
		$site_url = $blog->siteurl;
		$blog_admin_email = get_blog_option($blog_id, 'admin_email');
		$blog_admin_id = get_user_id_from_string($blog_admin_email);

		if ( (int) $this->image_width ) {
			$width = $this->image_width;
		} else {
			$width = '100';
		}

		if ( (int) $this->image_height ) {
			$height = $this->image_height;
		} else {
			$height = '100';
		}

		$imageurl = false;
		if ( $this->imageurl ) {
			$imageurl = $this->imageurl;
		} elseif( $this->resource_image_source ) {
			$imageurl = $this->resource_image_source;
		}

		switch_to_blog($blog_id);

		// Try to get a post image before falling back on the user avatar
		$blog_posts = new WP_Query( array( 'post_type' => 'post' ) );
		if ( $blog_posts->have_posts() ) {
			while ( $blog_posts->have_posts() ) {
				$blog_posts->the_post();
				$image = $this->getPostContentImage( get_the_content(), false );

				if ( !empty( $image ) ) {
					break;
				}
			}
		}

		if ( empty( $image ) ) {
			$avatar = bp_core_fetch_avatar( array( 'item_id' => $blog_admin_id, 'type' => 'full', 'height' => $height, 'width' => $width, 'no_grav' => false ) );
		} else {
			$avatar = $image;
		}

		$avatar = apply_filters( 'cac_featured_content_blog_avatar', $avatar, $blog_id );

		if($this->title == '&nbsp;') {
		    $header = 'Featured Blog';
		} else {
		    $header = $this->title;
		}
		/************
		 ** OUTPUT **
		 ************
		 */

		?>
		<h3><?php echo $header ?></h3>
		<div>
		    <?php echo $avatar ?>
		    <div class="cac-content">
		    <h4><a href="<?php echo $site_url ?>"><?php echo $blog_name?></a></h4>
			<p>
				<?php echo bp_create_excerpt( $description, $this->crop_length ); ?>
			</p>
		    </div>
		</div>

		<?php if ( $this->read_more_text ) : ?>
		<p class="more">
			<?php $moreLink = '<a href="'.get_home_url().'">'.$this->read_more_text.'</a>'; ?>
			<?php echo $moreLink; ?>
		</p>
		<?php endif ?>
		
		<?php
		// Don't forget to restore current blog
		restore_current_blog();
	}

	public function renderType_GROUP() {
		$before = '<div>';
		$after = '</div>';
		if($this->title == '&nbsp;') {
			$header = 'Featured Group';
		} else {
			$header = $this->title;
		}
		
		if ( (int) $this->image_width ) {
			$width = $this->image_width;
		} else {
			$width = '100';
		}

		if ( (int) $this->image_height ) {
			$height = $this->image_height;
		} else {
			$height = '100';
		}
		
		?>
		<?php if(bp_has_groups('type=single-group&slug='.$this->group_slug)): ?>
			<?php while(bp_groups()) : bp_the_group(); ?>
		    <h3><?php echo $header ?></h3>
			<div style="display: inline-block; ">
				<?php bp_group_avatar(array('width' => $width, 'height'=> $height)) ?>

			        <div class="cac-content">
			   	<h4><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></h4>
					<?php
						$moreLink = '<a href="'.bp_get_group_permalink().'">'.$this->read_more_text.'</a>';
						echo bp_create_excerpt( bp_get_group_description(), $this->crop_length );
					?>
				<p class="more">
					<span class="extra"><?php bp_group_status() ?> | <?php bp_group_member_count() ?></span>
					
					<?php if ( $this->read_more_text ) : ?>
						<?php echo $moreLink; ?>
					<?php endif ?>
				</p>
				</div>
			</div>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php
	}

	public function renderType_MEMBER() {
			$before = '<div>';
			$after = '</div>';

			$user_id   = bp_core_get_userid( $this->member_identifier );
			$link 	   = bp_core_get_userlink( $user_id, false, true );
			$display_name = bp_core_get_user_displayname( $user_id );

			$avatar = bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'full', 'height' => $this->image_height, 'width' => $this->image_width, 'no_grav' => false ) );
			$blogs = bp_blogs_get_blogs_for_user($user_id);

			if($this->title == '&nbsp;') {
				$header = 'Featured Member';
			} else {
				$header = $this->title;
			}

		?>
		<?php if  ( bp_has_members('include='.$user_id.'&max=1') ) : ?>
			<?php while  ( bp_members() ) : bp_the_member(); ?>
			    <h3><?php echo $header ?></h3>
			    <div>
					<?php echo $avatar ?>
					
					<div class="cac-content">
					<div>
					    <h4><a href="<?php echo $link ?>"><?php echo $display_name ?></a></h4>
					    <div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
					</div>
					
					<?php do_action( 'cacfc_featured_member_additional_content' ) ?>
					
					<?php if ( $this->read_more_text ) : ?>
					<p class="more">
						<?php $moreLink = '<a href="'.$link.'">'.$this->read_more_text.'</a>'; ?>
						<?php echo $moreLink; ?>
					</p>
					<?php endif ?>
					
					</div>
			    </div>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php
	}

	public function renderType_RESOURCE() {
	    if($this->title == '&nbsp;') {
		    $header = 'Featured Resource';
	    } else {
		    $header = $this->title;
	    }
	?>
	    <h3><?php echo $header ?></h3>
	    <div>

		<?php if($this->imageurl || $this->resource_image_source) ?>
			<?php if($this->imageurl): ?>
				<img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/cac-featured-content/timthumb.php?src=<?php echo $this->imageurl ?>&h=<?php echo $this->image_height ?>&w=<?php echo $this->image_width ?>&q=100&a=<?php echo $this->image_crop_rule ?>" class="avatar" />
			<?php else: ?>
				<img src="<?php echo get_bloginfo('wpurl');?>/wp-content/plugins/cac-featured-content/timthumb.php?src=<?php echo $this->resource_image_source ?>&h=<?php echo $this->image_height ?>&w=<?php echo $this->image_width ?>&q=100&a=<?php echo $this->image_crop_rule ?>" class="avatar" />
			<?php endif; ?>

          	<div class="cac-content">
          	
		<h4 ><a href="<?php echo $this->resource_link ?>"><?php echo $this->resource_title ?></a></h4>
		<div class="clear"></div>
		    <p>
			<?php echo bp_create_excerpt( $this->resource_text, $this->crop_length ); ?>
		    </p>
		    
		    <?php if ( $this->read_more_text ) : ?>
			<p class="more">
				<?php $moreLink = '<a href="'. $this->resource_link.'">'.$this->read_more_text.'</a>'; ?>
				<?php echo $moreLink; ?>
			</p>
		    <?php endif ?>
		</div>
	    </div>
	<?php
	}
}
?>
