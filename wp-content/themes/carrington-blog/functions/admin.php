<?php

// This file is part of the Carrington Blog Theme for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008-2009 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

function cfct_blog_settings_form() {
	global $cfct_color_options;
	$ajax_load_options = '';
	$color_options = '';
	$lightbox_options = '';
	$values = array(
		'yes' => __('Yes', 'carrington-blog'),
		'no' => __('No', 'carrington-blog'),
	);
	$settings = array(
		'cfct_ajax_load',
		'cfct_custom_colors',
		'cfct_custom_header_image',
		'cfct_lightbox',
		'cfct_css_background_images',
	);
	foreach ($values as $k => $v) {
		foreach ($settings as $setting) {
			$options = $setting.'_options';
			if ($k == cfct_get_option($setting)) {
				$selected = 'selected="selected"';
			}
			else {
				$selected = '';
			}
			$$options .= "\n\t<option value='$k' $selected>$v</option>";
		}
	}
	$cfct_posts_per_archive_page = get_option('cfct_posts_per_archive_page');
	if (intval($cfct_posts_per_archive_page) == 0) {
		$cfct_posts_per_archive_page = 25;
	}
	cfct_get_option('cfct_custom_colors') == 'no' ? $colors_class = 'hidden' : $colors_class = '';
	cfct_get_option('cfct_custom_header_image') == 'no' ? $header_image_class = 'hidden' : $header_image_class = '';
	$html = '
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">'.sprintf(__('Design', 'carrington-blog'), $key).'</td>
					<td>
						<fieldset>
							<p>
								<label for="cfct_css_background_images">'.__('Show Pretty Background Images:', 'carrington-blog').'</label>
								<select name="cfct_css_background_images" id="cfct_css_background_images">'.$cfct_css_background_images_options.'</select>
							</p>
							<p>
								<label for="cfct_custom_colors">'.__('Customize Colors:', 'carrington-blog').'</label>
								<select name="cfct_custom_colors" id="cfct_custom_colors">'.$cfct_custom_colors_options.'</select>
							</p>
							<fieldset class="'.$colors_class.'" id="cfct_color_options_panel">
								<legend>'.__('Custom Colors', 'carrington-blog').'</legend>
	';
	foreach ($cfct_color_options as $option => $default) {
		$value = get_option($option);
		$value == '' ? $value = $default : $value = attribute_escape($value);
		$label = ucwords(str_replace(
			array('cfct_', '_'),
			array('', ' '),
			$option
		));
		$html .= '
								<p>
									<label for="'.$option.'">'.__($label.':', 'carrington-blog').'</label>
									#<input type="text" name="'.$option.'" id="'.$option.'" value="'.$value.'" size="6" maxlength="6" class="cfct_colorpicker" />
								</p>
		';
	}
	$html .= '
								<p class="submit">
									<input type="hidden" name="cfct_header_image_type" id="cfct_header_image_type" value="dark" />
									<input type="hidden" name="cfct_footer_image_type" id="cfct_footer_image_type" value="dark" />
									<input id="reset_colors" type="reset" name="reset_button" value="'.__('Reset to Default Colors', 'carrington-blog').'" />
									<a href="#" id="preview_colors" class="thickbox button" title="'.__('Custom Color Preview - Remember to Save!', 'carrington-blog').'">'.__('Preview', 'carrington-blog').'</a>
								</p>
							</fieldset>
							<p>
								<label for="cfct_custom_header_image">'.__('Customize Header Image:', 'carrington-blog').'</label>
								<select name="cfct_custom_header_image" id="cfct_custom_header_image">'.$cfct_custom_header_image_options.'</select>
							</p>
							<fieldset class="'.$header_image_class.'" id="cfct_header_image_panel">
							'.cfct_header_image_form().'
							</fieldset>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">'.sprintf(__('Behavior', 'carrington-blog'), $key).'</td>
					<td>
						<fieldset>
							<p>
								<label for="cfct_ajax_load">'.__('Load archives and comments with AJAX:', 'carrington-blog').'</label>
								<select name="cfct_ajax_load" id="cfct_ajax_load">'.$cfct_ajax_load_options.'</select>
							</p>
							<p>
								<label for="cfct_lightbox">'.__('Use a lightbox effect for image galleries:', 'carrington').'</label>
								<select name="cfct_lightbox" id="cfct_lightbox">'.$cfct_lightbox_options.'</select>
							</p>
							<p>
								<label for="cfct_posts_per_archive_page">'.__('Posts shown on archives pages:', 'carrington-blog').'</label>
								<input type="text" name="cfct_posts_per_archive_page" id="cfct_posts_per_archive_page" value="'.$cfct_posts_per_archive_page.'" size="3" />
							</p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>
	';
	echo $html;
}
add_action('cfct_settings_form_top', 'cfct_blog_settings_form');

function cfct_blog_admin_js() {
	global $cfct_color_options;
?>
<script type="text/javascript">
jQuery(function($) {
	$('input.cfct_colorpicker').each(function() {
		cfct_color_preview($(this), $(this).val());
		var id = $(this).attr('id');
		$('#' + id).ColorPicker({
			onSubmit: function(hsb, hex, rgb) {
				$('#' + id).val(hex.toLowerCase()).each(function() {
					cfct_color_preview($(this), hex, rgb);
				});
			},
			onChange: function(hsb, hex, rgb) {
				$('#' + id).val(hex.toLowerCase()).each(function() {
					cfct_color_preview($(this), hex, rgb);
				});
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function() {
			$(this).val($(this).val().toLowerCase()).ColorPickerSetColor(this.value).each(function() {
				cfct_color_preview($(this), this.value);
			});
		});
	});
	$('#cfct_custom_colors').change(function() {
		if ($(this).val() == 'yes') {
			$('#cfct_color_options_panel').slideDown();
		}
		else {
			$('#cfct_color_options_panel').slideUp();
		}
	});
	$('#cfct_custom_header_image').change(function() {
		if ($(this).val() == 'yes') {
			$('#cfct_header_image_panel').slideDown();
		}
		else {
			$('#cfct_header_image_panel').slideUp();
		}
	});
	$('#reset_colors').click(function() {
		cfct_reset_colors();
		return false;
	});
});
cfct_reset_colors = function() {
<?php
	foreach ($cfct_color_options as $key => $default) {
		echo '	jQuery("#'.$key.'").val("'.$default.'").each(function() { cfct_color_preview(jQuery(this), "'.$default.'"); });'."\n";
	}
?>
}
cfct_set_image_types = function() {
	areas = ['header', 'footer'];
	for (var i = 0; i < areas.length; i++) {
		var area = areas[i];
		var rgb = getRGB(jQuery('#cfct_' + area + '_background_color').val());
		var brightness = (rgb.r + rgb.g + rgb.b) / 3;
		brightness > 127 ? img = 'dark' : img = 'light';
		jQuery('#cfct_' + area + '_image_type').val(img);
	}
}
cfct_color_preview = function(elem, hex) {
	var rgb = getRGB(hex);
	var brightness = (rgb.r + rgb.g + rgb.b) / 3;
	brightness > 127 ? color = '#000' : color = '#fff';
	jQuery(elem).css({
		backgroundColor: '#' + hex,
		color: color
	});
	cfct_set_image_types();
	cfct_set_preview_url();
}
cfct_set_preview_url = function() {
	var preview_url = '<?php echo trailingslashit(bloginfo('home')); ?>?cfct_action=custom_color_preview';
<?php
foreach ($cfct_color_options as $k => $v) {
echo 'preview_url += "&'.$k.'=" + encodeURIComponent(jQuery("#'.$k.'").val());';
}
?>
	var H = jQuery(window).height();
	var W = jQuery(window).width();
	jQuery('#preview_colors').attr('href', preview_url + '&TB_iframe=true&width=' + ( W - 110 ) + '&height=' + ( H - 100 ));

}
</script>
<?php
}
if ($_GET['page'] == 'carrington-settings') {
	add_action('admin_head', 'cfct_blog_admin_js');
}

function cfct_blog_admin_css() {
// override default WP admin setting
?>
<style type="text/css">
.colorpicker input[type="text"] {
	-moz-box-sizing:content-box;
}
#cfct_color_options_panel {
	background: #fff;
	border: 1px solid #ccc;
	padding: 0 20px;
}
#cfct_color_options_panel legend {
	font-weight: bold;
	padding: 0 5px;
}
#cfct_header_image_panel {
	padding: 0;
}
#reset_colors {
	float: right;
}
#TB_title {
	background-color: #222;
	color: #cfcfcf;
}
</style>
<?php
	echo '
<link rel="stylesheet" type="text/css" media="screen" href="'.get_bloginfo('template_directory').'/carrington-core/lightbox/css/thickbox.css" />
	';
}
// our copy of thickbox used for color previews
if (is_admin() && $_GET['page'] == 'carrington-settings') {
	add_action('admin_head', 'cfct_blog_admin_css');
	wp_enqueue_script('cfct_thickbox', get_bloginfo('template_directory').'/carrington-core/lightbox/thickbox.js', array('jquery'), '1.0');
}

?>