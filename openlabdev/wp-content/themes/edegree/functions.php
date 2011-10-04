<?php
$themename = "eDegree&#176;";
$shortname = "tbf2";

$content_width = 585;

$options = array (

	array(	"type" => "open"),

	array(  "name" => "Skin Color",
			"desc" => "Choose the overall color style of your site",
            "id" => $shortname."_skin_color",
			"default" => "silver",
            "type" => "skin_color"),
	
	array(  "name" => "Font Color",
			"desc" => "Click on the color palette above to select your custom font color. Clear the field to reset to default (dark gray).",
            "id" => $shortname."_font_color",
			"default" => "#575757",
            "type" => "font_color"),
	
	array(  "name" => "Header Logo",
			"desc" => "Would you like a logo in your header?",
            "id" => $shortname."_logo_header",
			"default" => "no",
            "type" => "logo"),
			
	array(  "name" => "Logo or Blog Name Location",
			"desc" => "Where do you want your Logo or Blog Name located?",
            "id" => $shortname."_logo_location",
			"default" => "left",
            "type" => "logo-location"),
			
	array(  "name" => "Font Size",
			"desc" => "Selct the font size used for your site",
            "id" => $shortname."_font_size",
			"default" => "12px",
            "type" => "font_size"),
			
	array(  "name" => "Search Bar",
			"desc" => "Would you like a Search Bar in your header? It allows a user to search within your site",
            "id" => $shortname."_search_header",
			"default" => "yes",
            "type" => "search"),
			
	array(  "name" => "User Login / Admin Bar",
			"desc" => "Would you like to display a navigation bar for login/admin pages at the top?",
            "id" => $shortname."_user_login",
			"default" => "no",
            "type" => "login"),
	
	array(  "name" => "Socialize Icons",
			"desc" => "Enter Links to Your Twitter, Facebook, and RSS feeds.<br />If you want to remove the icon, type \"hide\" in the field",
            "type" => "socialize_icons"),

	array(  "id" => $shortname."_icon_twitter",
			"default" => ""),
	array(  "id" => $shortname."_icon_facebook",
			"default" => ""),
	array(  "id" => $shortname."_icon_youtube",
			"default" => ""),
	array(  "id" => $shortname."_icon_rss",
			"default" => ''),
	
	array(  "name" => "Copyright Year",
		    "id" => $shortname."_copy_year",
			"desc" => "Enter the starting year for your copyright. (Used in your footer like \"Copyright &copy; 2009-".date('Y',time())."\")",
			"default" => "",
            "type" => "copyright"),
	
	array(  "id" => $shortname."_exclude_pages",
			"default" => ""),
	
	array(  "id" => $shortname."_nav_hide_home",
			"default" => "no"),

	array(  "name" => "Number of Posts",
			"desc" => "To limit the number of posts to show on your home, go to: Settings > Reading > edit the 'Blog pages show at most' option",
            "id" => $shortname."_number_posts",
			"default" => "",
            "type" => "posts"),

	array(  "id" => $shortname."_custom_slidespot",
			"default" => "no"),
	
	array(  "id" => $shortname."_custom_html_slidespot",
			"default" => ""),
		
	array(  "id" => $shortname."_custom_html_0",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_1",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_2",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_3",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_4",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_5",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_6",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_7",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_8",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_9",
			"default" => ""),
	array(  "id" => $shortname."_custom_html_10",
			"default" => ""),

	array(  "id" => $shortname."_header_image_file",
			"default" => ""),
	array(  "id" => $shortname."_footer_image_file",
			"default" => ""),
	array(  "id" => $shortname."_background_image_file",
			"default" => ""),
	array(  "id" => $shortname."_background_color",
			"default" => ""),
	array(  "id" => $shortname."_background_repeat",
			"default" => ""),
	
	array(	"type" => "close")
	
);

add_action('admin_head', 'wp_admin_js');

function wp_admin_js() {
	if(isset($_GET['page'])) {
		if(stristr($_GET['page'],'tbf-design.php')) { 
			echo '<script type="text/javascript" src="'; echo bloginfo('template_url'); echo '/js/design.js"></script>'."\n"; 
		}
		if(stristr($_GET['page'],'tbf-homepage.php')) { 
			echo '<script type="text/javascript" src="'; echo bloginfo('template_url'); echo '/js/home.js"></script>'."\n"; 
		}
	}
}

function tbf2_head() {
	global $shortname, $post; 
	
	echo"<style type=\"text/css\">\n";
	if (get_option($shortname.'_logo_header') == "yes") {
		list($w, $h) = getimagesize(get_option($shortname.'_logo'));
		$height = $h+40;
		
		if($h < 57) //minimum top
			$h = 57;
		if($height < 119) //minimum height
			$height = 119;
			
		echo "#globalnav {top: ".$h ."px !important }\n";
		echo "#header {height: ".$height ."px !important }\n";
	}
	
	if (get_option('tbf2_user_login') == "yes") {
		echo '#bg {margin-top: 20px} body {background-position: center 20px}';
	}
	
	if (get_option($shortname.'_logo_location') == "middle") {
		echo "	#header {text-align: center }\n";
		echo "	#description { clear:both;text-align: center; }\n";
		
	} elseif(get_option($shortname.'_logo_location') == "right") {
		echo "	#header {text-align: right }\n";
		echo "	#description { clear:right;float: right; }\n";
		
	} else {
		echo "	#header {text-align:left }\n";
		echo "	#description { clear:left;float: left; }\n";
	}
	echo"</style>\n";
}
add_action('wp_head', 'tbf2_head');

function mytheme_media_buttons_init() {
?>
<script type="text/javascript">
/* <![CDATA[ */
function send_to_editor(h) {
	var ed;

	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
		ed.focus();
		if (tinymce.isIE)
			ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);

		if ( h.indexOf('[caption') === 0 ) {
			if ( ed.plugins.wpeditimage )
				h = ed.plugins.wpeditimage._do_shcode(h);
		} else if ( h.indexOf('[gallery') === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		}

		ed.execCommand('mceInsertContent', false, h);

	} else if ( typeof edInsertContent == 'function' ) {
		edInsertContent(edCanvas, h);
	} else {
		jQuery( edCanvas ).val( jQuery( edCanvas ).val() + h );
	}

	tb_remove();
}
jQuery(function($) {
	$('div[id=media-buttons] a').click(function() {
		if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
			var editor = $(this).parent().next('.theEditor:first');
			if (!editor.hasClass('theEditor')) return ;
			if (editor.attr('id') == ed.id) return;
			
			if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
				//var active = tinyMCE.get(editor.attr('id'))
				tinyMCE.execInstanceCommand(editor.attr('id'), 'mceFocus')
			}


		}
	
	
	});
});
/* ]]> */
</script>
<?php
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script('thickbox');
	wp_print_scripts();
	wp_print_styles();

}

function mytheme_add_admin() {
    global $themename, $shortname, $options;
	$admin_pages = array('tbf-design.php', 'tbf-features.php', 'tbf-homepage.php', 'tbf-optin.php', 'tbf-landing.php');

    if(isset($_GET['page'])) {
		if (in_array(trim($_GET['page']), $admin_pages)) {
			if(isset($_REQUEST['action'])) {
				if ('save' == $_REQUEST['action']) {
						foreach($options as $value) {
						  if(isset($value['id'])) {
							if(isset($_REQUEST[ $value['id'] ])) {
								update_option( $value['id'], stripslashes_deep($_REQUEST[ $value['id'] ]) );
							}
						  }
						}
						
						foreach(array('logo', 'optin_image_file', 'header_image_file', 'footer_image_file',  'background_image_file') as $file) {
							if(!empty($_FILES[$file]["tmp_name"])){
								$directory = dirname(__FILE__) . "/uploads/";				
								move_uploaded_file($_FILES[$file]["tmp_name"], $directory . $_FILES[$file]["name"]);
								update_option('tbf2_'.$file, get_option('siteurl'). "/wp-content/themes/". get_option('template')."/uploads/". $_FILES[$file]["name"]);
							}
						}
						
						if(stristr($_GET['page'],'&saved=true')) {
							$location = 'admin.php?page='. trim($_GET['page']);
						} else {
							$location = 'admin.php?page='. trim($_GET['page']) . "&saved=true";		
						}

						header("Location: $location");
						die;
				}
			}
			add_action('admin_head', 'mytheme_media_buttons_init');
		}
	}
	// Set all default options
	foreach($options as $default) {
		if(@get_option($default['id']) == '') {
			@update_option($default['id'], $default['default']);
		}
	}
	/* Debug only
	// Delete all default options
	foreach($options as $default) {
		delete_option($default['id'],$default['default']);
	}
	*/	
	
	add_menu_page('Page title', 'My Theme', 10, 'tbf-design.php', 'mytheme_admin');
	add_submenu_page('tbf-design.php', 'Site Design', '1. Design (Start here)', 10, 'tbf-design.php', 'mytheme_admin');
	add_submenu_page('tbf-design.php', 'Site Settings', '2. Settings', 10, 'tbf-features.php', 'mytheme_admin');
	add_submenu_page('tbf-design.php', 'Site Homepage', '3. Homepage', 10, 'tbf-homepage.php', 'mytheme_admin');
}

function mytheme_admin() {
    global $themename, $shortname, $options;
?>
<style type="text/css">
#myForm .inner-sidebar {display: block}
#myForm #post-body {margin-right: 300px}
#headerLogo {overflow: hidden}
#socializediv .form-table th {width:124px; padding: 0; line-height:22px }
#socializediv .form-table {clear:inherit }
#socializediv .form-table td {padding:0 10px } 
#background_color, #font_color {padding-left: 35px; background: url(<?php bloginfo('template_url'); ?>/images/admin/back-palette.png) no-repeat}
</style>

<div class="wrap">
<h2><?php echo $themename; ?> Settings
	<?php		
	if(stristr($_GET['page'],'tbf-design.php')) {
		echo '- Design';
	} elseif(stristr($_GET['page'],'tbf-features.php')) {
		echo '- Settings & Features';
	} elseif(stristr($_GET['page'],'tbf-landing.php')) {
		echo '- Landing Page';
	} elseif(stristr($_GET['page'],'tbf-optin.php')) {
		echo '- Email Optin / Newsletter Setting';
	} else {
		echo '- Homepage Customization'; }  
	?>
</h2>
<?php
if (isset($_REQUEST['saved'])) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
?>
<form method="post" id="myForm" enctype="multipart/form-data">
<div id="poststuff" class="metabox-holder">

<div id="side-info-column" class="inner-sidebar">
	<div id='side-sortables' class='meta-box-sortables'>
		<div id="linksubmitdiv" class="postbox " >
			<h3 class='hndle'><span>Current Saved Settings </span></h3>
			<div class="inside">
				<div class="submitbox" id="submitlink">
					<div id="minor-publishing">
						<div id="misc-publishing-actions">
						<div class="misc-pub-section misc-pub-section-last">
							<ul style="padding:10px 0 0 5px;">
							<?php if(stristr($_GET['page'],'tbf-design.php')) { ?>
									<li>Skin Color: <strong><?php echo ucwords(get_option($shortname.'_skin_color')); ?></strong></li>
									<li>Font Color (Hex): <strong><?php echo ucwords(get_option($shortname.'_font_color')); ?></strong></li>
                                    <li>Header Logo: <strong><?php echo ucwords(get_option($shortname.'_logo_header')); ?></strong></li>
									<li><?php if (get_option($shortname.'_logo_header') == "yes") { echo "Logo"; } else { echo "Blog Name"; } ?> Location: <strong><?php echo ucwords(get_option($shortname.'_logo_location')); ?></strong></li>
							<?php } ?>
							<?php if(stristr($_GET['page'],'tbf-features.php')) { ?>
									<li>Font Size: <strong><?php if(get_option($shortname.'_font_size') == "11px") { echo "11px"; } else { echo ucwords(get_option($shortname.'_font_size')); } ?></strong></li>
									<li>Search Bar: <strong><?php if(get_option($shortname.'_logo_location') == "middle") { echo "No"; } else { echo ucwords(get_option($shortname.'_search_header')); } ?></strong></li>
									<li>Admin Bar: <strong><?php if(get_option($shortname.'_user_login') == "yes") { echo "Yes"; } else { echo ucwords(get_option($shortname.'_user_login')); } ?></strong></li>
							<?php } ?>
							<?php if(stristr($_GET['page'],'tbf-homepage.php')) { ?>
									<li>Number of Posts: <strong><?php echo get_option('posts_per_page'); ?></strong></li>
                                    <li>Slideshow: <strong><?php echo (get_option($shortname.'_custom_slidespot') == 'yes') ? 'No' : 'Yes'; ?></strong></li>
							<?php } ?>
							<?php if(stristr($_GET['page'],'tbf-optin.php')) { ?>
									<li>Optin/Newsletter Form: <strong><?php echo ucwords(get_option($shortname.'_optin_form')); ?></strong></li>
							<?php } ?>
							<?php if(stristr($_GET['page'],'tbf-landing.php')) { ?>
									<li>Landing Page Header: <strong><?php if(get_option($shortname.'_landing_header') == "displayed") { echo "Displayed"; } else { echo ucwords(get_option($shortname.'_landing_header')); } ?></strong></li>
									<li>Landing Page Footer: <strong><?php if(get_option($shortname.'_landing_footer') == "displayed") { echo "Displayed"; } else { echo ucwords(get_option($shortname.'_landing_footer')); } ?></strong></li>
							<?php } ?>
							</ul>
						</div>
						</div>
					</div>

					<div id="major-publishing-actions">
						<div id="delete-action"></div>
						<div id="publishing-action">
							<input name="save" type="submit" class="button-primary" value="Save changes" />    
							<input type="hidden" name="action" value="save" />
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="post-body" class="has-sidebar">
<div id="post-body-content" class="has-sidebar-content">

<?php
	if(stristr($_GET['page'],'tbf-design.php')) {
		foreach($options as $value) {
			if(isset($value['type'])) {
				switch ($value['type']) {
	
					case "skin_color":
					?>
						<div id="skin_colordiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<table>
								<tr>
									<td align="center" style="padding-right: 10px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>2" type="radio" value="silver"<?php if(get_option($value['id']) == "silver") { echo " checked"; } ?> /> White/Silver
									</td>
									<td align="center" style="padding-right: 10px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>3" type="radio" value="red"<?php if(get_option($value['id']) == "red") { echo " checked"; } ?> /> Red
									</td>
									<td align="center" style="padding-right: 10px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>1" type="radio" value="blue"<?php if(get_option($value['id']) == "blue") { echo " checked"; } ?> /> Blue
									</td>
								</tr>
								<tr>
									<td style="padding-right: 10px;">
										<label for="<?php echo $value['id']; ?>2"><img src="<?php bloginfo('template_url'); ?>/images/admin/screen-silver.png" alt="Silver" /></label>
									</td>
									<td style="padding-right: 10px;">
										<label for="<?php echo $value['id']; ?>3"><img src="<?php bloginfo('template_url'); ?>/images/admin/screen-red.png" alt="Red" /></label>
									</td>
									<td style="padding-right: 10px;">
										<label for="<?php echo $value['id']; ?>1"><img src="<?php bloginfo('template_url'); ?>/images/admin/screen-blue.png" alt="Blue" /></label>
									</td>
								</tr>
							</table>
							<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
					<?php
					break;
					
					case "font_color":
					?>
					<div id="logodiv" class="stuffbox">
                    	<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
                        <div class="inside">
                        	<span class="colorpicker">
                                <input type="text" size="8" name="<?php echo $shortname;?>_font_color" id="font_color" value="<?php echo htmlentities($fcolor = get_option($shortname.'_font_color'));?>"> <span id="color-sample" style="background-color:<?php echo $fcolor;?>;padding:0 10px;">&nbsp;</span><br>
                                <div id="colorPickerDivFont" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"> </div>
                            </span>
                        	<p><small><?php echo $value['desc']; ?></small></p>
                        </div>
                    </div>					
					<?php
					break;
					
					case "logo":
					?>
					<div id="logodiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="yes"<?php if(get_option($value['id']) == "yes") { echo " checked"; } ?> />&nbsp;Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="no"<?php if(get_option($value['id']) == "no") { echo " checked"; } ?> />&nbsp;No&nbsp;(Just show the name of my blog)</label>
							
							<p><small><?php echo $value['desc']; ?></small></p>
							<div id="headerLogo">
								Choose a file to upload: <input type="file" name="logo" id="logo" /> (Recommended height: 56px)
								<?php if(get_option($shortname.'_logo')) { echo '<div><img src="'; echo get_option($shortname.'_logo'); echo '"  style="margin-top:10px;border:1px solid #aaa;padding:10px;" /></div>'; } ?> 
							</div>
						</div>
					</div>
					<?php
					break;
					
					case "logo-location":
					?>
					<div id="locationdiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<table>
								<tr>
									<td style="padding-right: 15px;">
										<img src="<?php bloginfo('template_url'); ?>/images/admin/logoleft.png" alt="Left" />
									</td>
									<td style="padding-right: 15px;">
										<img src="<?php bloginfo('template_url'); ?>/images/admin/logoright.png" alt="Right" />
									</td>
									<td style="padding-right: 15px;">
										<img src="<?php bloginfo('template_url'); ?>/images/admin/logomiddle.png" alt="Centered" />
									</td>
								</tr>
								<tr>
									<td align="center" style="padding-right: 15px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="left"<?php if(get_option($value['id']) == "left") { echo " checked"; } ?> />
									</td>
									<td align="center" style="padding-right: 15px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="right"<?php if(get_option($value['id']) == "right") { echo " checked"; } ?> />
									</td>
									<td align="center" style="padding-right: 15px;">
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="middle"<?php if(get_option($value['id']) == "middle") { echo " checked"; } ?> />
									</td>
								</tr>
							</table>
							<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					 </div>
					<?php break;
						
				}
			}
		}	
		?>

				<div id="footer_image_div" class="stuffbox">
					<h3><label for="link_url">Background</label></h3>
					<div class="inside">
					<?php $imageURL = get_option($shortname.'_background_image_file');?>
						Image URL: <input name="<?php echo $shortname?>_background_image_file" id="<?php echo $shortname?>_background_image_file" type="text" size="50" value="<?php echo htmlentities($imageURL);?>">
						<div id="headerLogo">
							Or, Choose a file to upload: <input type="file" name="background_image_file" id="background_image_file" />
						</div>
						
						Background Repeat: 
						<?php $bgr = get_option($shortname.'_background_repeat');?>
						
						<input type="radio" size="8" name="<?php echo $shortname;?>_background_repeat" <?php echo ($bgr == 'repeat-x'  ? 'checked="checked"' : '');?> value="repeat-x"  id="repeat-x" ><label for="repeat-x">Horizontally</label> &nbsp;
						<input type="radio" size="8" name="<?php echo $shortname;?>_background_repeat" <?php echo ($bgr == 'repeat-y'  ? 'checked="checked"' : '');?> value="repeat-y"  id="repeat-y" ><label for="repeat-y">Vertically</label> &nbsp;
						<input type="radio" size="8" name="<?php echo $shortname;?>_background_repeat" <?php echo ($bgr == 'repeat'    ? 'checked="checked"' : '');?> value="repeat"    id="repeat" ><label for="repeat">Tile</label> &nbsp;
						<input type="radio" size="8" name="<?php echo $shortname;?>_background_repeat" <?php echo ($bgr == 'no-repeat' ? 'checked="checked"' : '');?> value="no-repeat" id="no-repeat" ><label for="no-repeat">No Repeat</label>
						<br>
						<span class="colorpicker">
                        	Background Color: <input type="text" size="8" name="<?php echo $shortname;?>_background_color" id="background_color" value="<?php echo htmlentities($bgc = get_option($shortname.'_background_color'));?>"> <span class="color-sample" style="background-color:<?php echo $bgc;?>;padding:0 10px;">&nbsp;</span><br>
                            <div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"> </div>
                        </span>
						
						<?php if ($imageURL):?>
							<img src="<?php echo $imageURL;?>" style="max-height:50px;max-width:600px;min-width:20px" />
						<?php endif;?>
						<br /><span class="description">Clear the values to use the default</span>
					</div>
				</div>
				
				<div id="header_image_div" class="stuffbox">
					<h3><label for="link_url">Header Image</label></h3>
					<div class="inside">
					<?php $imageURL = get_option($shortname.'_header_image_file');?>
						Image URL: <input name="<?php echo $shortname?>_header_image_file" id="<?php echo $shortname?>_header_image_file" type="text" size="50" value="<?php echo htmlentities($imageURL);?>"> <small>900px & higher width recommended</small>
						<div id="headerLogo">
							Or, Choose a file to upload: <input type="file" name="header_image_file" id="header_image_file" />
						</div>
						<?php if ($imageURL):?>
							<img src="<?php echo $imageURL;?>" style="max-width:600px" />
						<?php endif;?>
						<br /><span class="description">Clear URL to use the default</span>
					</div>
				</div>

				<div id="footer_image_div" class="stuffbox">
					<h3><label for="link_url">Footer Image</label></h3>
					<div class="inside">
					<?php $imageURL = get_option($shortname.'_footer_image_file');?>
						Image URL: <input name="<?php echo $shortname?>_footer_image_file" id="<?php echo $shortname?>_footer_image_file" type="text" size="50" value="<?php echo htmlentities($imageURL);?>"> <small>Recommended size: 900px x 200px</small>
						<div id="headerLogo">
							Or, Choose a file to upload: <input type="file" name="footer_image_file" id="footer_image_file" />
						</div>
						<?php if ($imageURL):?>
							<img src="<?php echo $imageURL;?>" style="max-width:600px" />
						<?php endif;?>
						<br /><span class="description">Clear URL to use the default</span>
					</div>
				</div>

				
	<script type="text/javascript">
	/* <![CDATA[ */
	var farbtastic;

	jQuery(function($) {
		$('#background_color').click(function() {
			$('#colorPickerDiv').show();
		});
		farbtastic = jQuery.farbtastic('#colorPickerDiv', function(color) { pickColor(color); });
		pickColor('<?php echo $bgc ?>');
		
		$('#font_color').click(function() {
			$('#colorPickerDivFont').show();
		});
		farbtastic = jQuery.farbtastic('#colorPickerDivFont', function(color) { pickFontColor(color); });
		pickFontColor('<?php echo $fcolor ?>');
		
	});
	function pickColor(color) {
		jQuery('#background-color-sample').css('background-color', color);
		jQuery('#background_color').val(color);
		farbtastic.setColor(color);
	}
	function pickFontColor(color) {
		jQuery('#color-sample').css('background-color', color);
		jQuery('#font_color').val(color);
		farbtastic.setColor(color);
	}
	jQuery(document).mousedown(function() {
		jQuery('#colorPickerDiv').fadeOut(2);
		jQuery('#colorPickerDivFont').fadeOut(2);
	})
	/* ]]> */
    </script>
		
					</div></div>

	<?php	
	} elseif(stristr($_GET['page'],'tbf-features.php')) { //Features Page

		foreach($options as $value) {
			if(isset($value['type'])) {
				switch ($value['type']) {
					case "font_size":
					?>
					<div id="font_sizediv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<span id="searchHeader"><label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="11px"<?php if(get_option($value['id']) == "11px") { echo " checked"; } ?> />&nbsp;<span style="font-size:11px">11px (Small)</span></label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="12px"<?php if(get_option($value['id']) == "12px") { echo " checked"; } ?> />&nbsp;<span style="font-size:12px">12px (Default)</span></label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="13px"<?php if(get_option($value['id']) == "13px") { echo " checked"; } ?> />&nbsp;<span style="font-size:13px">13px (Large)</span></label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="14px"<?php if(get_option($value['id']) == "14px") { echo " checked"; } ?> />&nbsp;<span style="font-size:14px">14px (X-Large)</span></label></span><p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
					<?php
					break;
					
					case "search":
					?>
					<div id="searchdiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<span id="searchHeader"><label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="yes"<?php if(get_option($value['id']) == "yes") { echo " checked"; } ?> />&nbsp;Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="no"<?php if(get_option($value['id']) == "no") { echo " checked"; } ?> />&nbsp;No</span></label><p><small><?php echo $value['desc']; ?></small></p>
						</div>
			  </div>
					<?php
					break;
					
					case "login":
					?>
					<div id="logindiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside"><label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="yes"<?php if(get_option($value['id']) == "yes") { echo " checked"; } ?> />&nbsp;Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" value="no"<?php if(get_option($value['id']) == "no") { echo " checked"; } ?> />&nbsp;No</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
					<?php break;
	
					case "socialize_icons":
					?>
					<div id="socializediv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row"><label for="<?php echo $shortname?>_icon_twitter">Your twitter URL:</label></th>
										<td><input id="<?php echo $shortname?>_icon_twitter" class="regular-text" type="text" value="<?php echo get_option($shortname.'_icon_twitter'); ?>" name="<?php echo $shortname?>_icon_twitter"/><br /><span class="description">Eg. http://twitter.com/username</span></td>
									</tr>
									<tr valign="top">
										<th scope="row"><label for="<?php echo $shortname?>_icon_facebook">Your facebook URL:</label></th>
										<td><input id="<?php echo $shortname?>_icon_facebook" class="regular-text" type="text" value="<?php echo get_option($shortname.'_icon_facebook'); ?>" name="<?php echo $shortname?>_icon_facebook"/><br /><span class="description">Eg. http://www.facebook.com/xxxxxx</span></td>
									</tr>
                                    <tr valign="top">
										<th scope="row"><label for="<?php echo $shortname?>_icon_youtube">YouTube URL:</label></th>
										<td><input id="<?php echo $shortname?>_icon_youtube" class="regular-text" type="text" value="<?php echo get_option($shortname.'_icon_youtube'); ?>" name="<?php echo $shortname?>_icon_youtube"/><br /><span class="description">Eg. http://www.youtube.com/user/xxxxxx </span></td>
									</tr>
									<tr valign="top">
										<th scope="row"><label for="<?php echo $shortname?>_icon_rss">RSS feed URL:</label></th>
										<td><input id="<?php echo $shortname?>_icon_rss" class="regular-text" type="text" value="<?php echo get_option($shortname.'_icon_rss'); ?>" name="<?php echo $shortname?>_icon_rss"/><br /><span class="description">Eg. http://www.yourblog.com/feed/ </span></td>
									</tr>
								</tbody>
							</table>
						<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
					<?php break;
					
					case "copyright":
					?>
					<div id="logindiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside"><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php echo get_option($shortname.'_copy_year'); ?>" />
						<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
					<?php break;
					
				} //End switch
			}
		}
		?>
				
				<div id="navgation_div" class="stuffbox">
					<h3><label for="link_url">Top Navigation</label></h3>
					<div class="inside">
                    <strong>Click on "Select Page..." to exclude pages from your top navigation.</strong><br />
					<?php $excludepages = get_option($shortname.'_exclude_pages');?>
					Page IDs:
					<input name="<?php echo $shortname?>_exclude_pages" id="<?php echo $shortname?>_exclude_pages" size="60" value="<?php echo htmlentities($excludepages);?>">
						
					<?php wp_dropdown_pages(array('show_option_none' => 'Select Page... '));?><br>
					<small>Separate page IDs with a comma (eg. 2,43,16). To reset, simply delete all the IDs</small>
                    
                    <p><br /></p>

                    Hide "Home" from the top navigation?<br />
                    <label><input name="<?php echo $shortname?>_nav_hide_home" id="<?php echo $shortname?>_nav_hide_home" type="radio" value="yes"<?php if(get_option($shortname.'_nav_hide_home') == "yes") { echo " checked"; } ?> />&nbsp;Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input name="<?php echo $shortname?>_nav_hide_home" id="<?php echo $shortname?>_nav_hide_home" type="radio" value="no"<?php if(get_option($shortname.'_nav_hide_home') == "no") { echo " checked"; } ?> />&nbsp;No</label>
					</div>
				</div>
				<script style="text/javascript">
				jQuery(function($) {
					$('#page_id').change(function() {
						var value = $('#<?php echo $shortname?>_exclude_pages').val();
						$('#<?php echo $shortname?>_exclude_pages').val( value + (value != '' ? ',' : '') +$(this).find('option:selected').val());
					});
				});
				</script>
		
			</div></div>

	<?php	
	} else { //tbf-homepage.php
		foreach($options as $value) {
			if(isset($value['type'])) {
				switch ($value['type']) {
					case "posts":
					?>
					<div id="postsdiv" class="stuffbox">
						<h3><label for="link_url"><?php echo $value['name']; ?></label></h3>
						<div class="inside">
						<p><small><?php echo $value['desc']; ?></small></p>
						</div>
					</div>
                    <div id="slideshowdiv" class="stuffbox">
						<h3><label for="link_url">Slideshow Area</label></h3>
						<div class="inside">
                        <p><img src="<?php bloginfo('template_url'); ?>/images/admin/screen-slideshow.png" class="alignright" />If you want to use A slideshow of your posts on your homepage like the picture on the right, follow these steps:</p>
                        <p>1. Install and activate the "Featured Content Gallery" plugin from <a href="http://wordpress.org/extend/plugins/featured-content-gallery/" target="_blank">this page</a><br />
                        2. Once installed, configure it on the <a href="<?php bloginfo('url');?>/wp-admin/options-general.php?page=featured-content-gallery/options.php">"Featured Content Gallery" settings page</a>.<br />
                        3. Add the "articleimg" and "featuredtext" fields in the "Custom field" on your posts (Do this on at least two posts; otherwise, the slideshow will not run).<br />Recommended dimention of your images (586px in width x 261px in height )
                        </p>                        
						<p><small>Watch Video Tutorial &#187; (Coming Soon)</small></p>
                        
                        <h4>If you decide not to use the slideshow, check this box(<input type="checkbox" name="<?php echo $shortname?>_custom_slidespot_box" id="<?php echo $shortname?>_custom_slidespot_box" <?php if(get_option($shortname.'_custom_slidespot') == 'yes') echo 'checked=checked';?> />), and use the editor below.</h4>
                        
                        <input type="hidden" value="<?php echo get_option($shortname.'_custom_slidespot')?>" name="<?php echo $shortname?>_custom_slidespot" id="<?php echo $shortname?>_custom_slidespot" />
                        	<div id="editorcontainer">                          
                            <textarea name="<?php echo $shortname?>_custom_html_slidespot" id="<?php echo $shortname?>_custom_html_slidespot"
                                style="width:100%;height:75px;" class="theEditor" rows="5" cols="80" >
                                <?php echo htmlentities(get_option($shortname.'_custom_html_slidespot'));?>
                            </textarea><br />
                            </div>
						</div>
					</div>
					<?php
					break;
				}
			}
		}
		?>
					</div></div>

		<div id="customhtmldiv" class="stuffbox">
		<h3>Homepage Custom Content (In between posts)</h3>
		<?php 
		$max = ($max = get_option('posts_per_page')) ? $max : 6;
		$baseurl = includes_url('js/tinymce');

		for ($i=0; $i<$max; $i++):
		?>
		<div class="inside">
		Row <?php echo $i+1;?>:<br>
				<?php if ( current_user_can( 'upload_files' ) ) : ?>
				<div id="media-buttons" class="hide-if-no-js">
					<?php do_action( 'media_buttons' ); ?>
				</div>
				<?php endif; ?>

		<textarea name="<?php echo $shortname?>_custom_html_<?php echo $i;?>" id="<?php echo $shortname?>_custom_html_<?php echo $i;?>"
			style="width:100%;height:75px;" class="theEditor"
			rows="5" cols="80"
		><?php echo htmlentities(get_option($shortname.'_custom_html_'.$i));?>
		</textarea>
		</div>
		<?php endfor;?>
		</div>
		<?php 
		wp_enqueue_script('editor');
		wp_print_scripts();
		wp_tiny_mce(  );

	}
}
add_action('admin_menu', 'mytheme_add_admin');

include(TEMPLATEPATH.'/widgets/widget_youtube.php');
?>
<?php
if (function_exists("register_sidebar")) {
	register_sidebar(array('name' => 'Top Custom Content (Home)', 'before_widget' => '<div class="widget">', 'after_widget' => '</div>',	'before_title' => '<h2>', 'after_title' => '</h2>'));
	register_sidebar(array('name' => 'Top Custom Content (Internal)', 'before_widget' => '<div class="widget">', 'after_widget' => '</div>',	'before_title' => '<h2>', 'after_title' => '</h2>'));
	register_sidebar(array('name' => 'Sidebar', 'before_widget' => '<li class="widget" id="%1$s">', 'after_widget' => '</li>',	'before_title' => '<h2>', 'after_title' => '</h2>'));
	register_sidebar(array('name'=>'250x? Side Banner Space', 'before_widget' => '<li class="widget" id="%1$s">', 'after_widget' => '</li>',	'before_title' => '', 'after_title' => ''));
	register_sidebar(array('name' => 'Footer Left', 'before_widget' => '<li class="widget" id="%1$s">', 'after_widget' => '</li>',	'before_title' => '<h2>', 'after_title' => '</h2>'));
	register_sidebar(array('name' => 'Footer Middle', 'before_widget' => '<li class="widget" id="%1$s">', 'after_widget' => '</li>', 'before_title' => '<h2>', 'after_title' => '</h2>'));
	register_sidebar(array('name' => 'Footer Right', 'before_widget' => '<li class="widget" id="%1$s">', 'after_widget' => '</li>',	'before_title' => '<h2>', 'after_title' => '</h2>'));
}

function csv_tags() {
    $posttags = get_the_tags();
    foreach((array)$posttags as $tag) {
        $csv_tags .= $tag->name . ',';
    }
    echo '<meta name="keywords" content="'.$csv_tags.'" />';
}

function theme_excerpt($num) {
	global $more;
	$more = 1;
	$link = get_permalink();
	$limit = $num+1;
	$excerpt = explode(' ', strip_tags(get_the_content()), $limit);
	array_pop($excerpt);
	$excerpt = implode(" ",$excerpt).'...<a href="'.$link.'" class="readmore"> &raquo;</a>';
	echo '<p>'.$excerpt.'</p>';
	$more = 0;
}

function metaDesc() {
	$content = strip_tags(get_the_content());
	if (strlen($content) < 155) {
		echo $content;
	} else {
		$desc = substr($content,0,155);
		echo $desc."...";
	}
}

function getImage($num) {
	global $more;
	$more = 1;
	$link = get_permalink();
	$content = get_the_content();
	$count = substr_count($content, '<img src=');
	$start = 0;
	for($i=1;$i<=$count;$i++) {
		$imgBeg = strpos($content, '<img', $start);
		$post = substr($content, $imgBeg);
		$imgEnd = strpos($post, '>');
		$postOutput = substr($post, 0, $imgEnd+1);
		$result = preg_match('/width="([0-9]*)" height="([0-9]*)"/', $postOutput, $matches);
		if ($result) {
			$pagestring = $matches[0];
			$image[$i] = str_replace($pagestring, "", $postOutput);
		} else {
			$image[$i] = $postOutput;
		}
		$start=$imgEnd+1;
	}
	if(isset($image)) {
		if(stristr($image[$num],'<img src=')) { echo '<a href="'.$link.'">'.$image[$num]."</a>"; }
	}
	$more = 0;
}

function tbf2_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
        <div id="comment-<?php comment_ID(); ?>">
            <div class="comment-avatar">
            <div class="pic"><?php echo get_avatar($comment,$size='36',$default='' ); ?></div>
            <span class="name"><?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?> says:</span>
        </div>

		<?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
        <?php endif; ?>
        
        <div class="comment-meta commentmetadata"><a class="comment-time-meta" href="<?php echo htmlspecialchars(get_comment_link( $comment->comment_ID )) ?>">
			<?php printf(__('%1$s at %2$s'), get_comment_date(),get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?>
            <div class="comment-text">
				<?php if($args['max_depth']!=$depth) { ?>
                    <?php if(get_option('thread_comments')) :?>	
                    	<span class="reply"><?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
                    <?php endif; ?>
                <?php } ?>
                <?php comment_text() ?>
            </div>
        </div>
        <div class="recover"></div>
     </div>
<?php
}

add_action('admin_print_styles', create_function('', "wp_enqueue_script('farbtastic');wp_enqueue_style('farbtastic');"));
add_action('admin_print_styles', create_function('', "wp_enqueue_script('thickbox');wp_enqueue_style('thickbox');")); 

//Resize the dimention of youtube videos so that they fit within the left column
function resize_youtube( $content ) {
	return str_replace('width="640" height="505"></embed>', 'width="500" height="395"></embed>', $content);
}
add_filter('the_content', 'resize_youtube', 999);

//Get the current skin color directory
function get_skinDir() {
	$skin_dir = '';
	
	$skin_folders = array('red'=>'skin-red', 'blue'=>'skin-blue');
	
	foreach($skin_folders as $key=>$value) {
		if(get_option('tbf2_skin_color') == $key) {
			$skin_dir = $value;
		}
	}
	return $skin_dir;
}


/*
* Returns a string of custom css defined by admin
*/
function get_BodyCSS() {
	$body_css  = '';
	$body_css .= (get_option('tbf2_background_image_file')) ? 'background-image:url('.get_option('tbf2_background_image_file'). ');' : '';
	$body_css .= (get_option('tbf2_background_color')) ? 'background-color:'.get_option('tbf2_background_color'). ';' : '';
	$body_css .= (get_option('tbf2_background_repeat')) ? 'background-repeat:'.get_option('tbf2_background_repeat'). ';' : '';
	$body_css .= (get_option('tbf2_font_size')) ? 'font-size:'.get_option('tbf2_font_size'). ';' : '';
	$body_css .= (get_option('tbf2_font_color')) ? 'color:'.get_option('tbf2_font_color'). ';' : '';
	
	return $body_css;
}
?>