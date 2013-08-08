<?php

register_activation_hook( __FILE__, array('GFDirectory_Admin', 'activation')  );
add_action('init', array('GFDirectory_Admin', 'initialize'));

class GFDirectory_Admin {

	function initialize() {
		new GFDirectory_Admin();
	}

	function __construct() {

		if(!is_admin()) { return; }

		$settings = GFDirectory::get_settings();

		add_action('admin_notices', array(&$this, 'gf_warning'));
		add_filter('gform_pre_render',array(&$this, 'show_field_ids'));

		//creates a new Settings page on Gravity Forms' settings screen
        if(GFDirectory::has_access("gravityforms_directory")){
            RGForms::add_settings_page("Directory & Addons", array(&$this, "settings_page"), "");
        }
        add_filter("gform_addon_navigation", array(&$this, 'create_menu')); //creates the subnav left menu

        //Adding "embed form" button
		add_action('media_buttons', array(&$this, 'add_form_button'), 30);

		if(in_array(RG_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
			add_action('admin_footer',	array(&$this, 'add_mce_popup'));
			wp_enqueue_script("jquery-ui-datepicker");
		}


		if(!empty($settings['modify_admin'])) {
			add_action('admin_head', array(&$this, 'admin_head'), 1);
		}
	}

	// If the classes don't exist, the plugin won't do anything useful.
	function gf_warning() {
		global $pagenow;
		$message = '';

		if($pagenow != 'plugins.php') { return; }

		if(!GFDirectory::is_gravityforms_installed()) {
			if(file_exists(WP_PLUGIN_DIR.'/gravityforms/gravityforms.php')) {
				$message .= __(sprintf('%sGravity Forms is installed but not active. %sActivate Gravity Forms%s to use the Gravity Forms Directory & Addons plugin.%s', '<p>', '<a href="'.wp_nonce_url(admin_url('plugins.php?action=activate&plugin=gravityforms/gravityforms.php'), 'activate-plugin_gravityforms/gravityforms.php').'" style="font-weight:strong;">', '</a>', '</p>'), 'gravity-forms-addons');
			} else {
				$message = sprintf(__('%sGravity Forms cannot be found%s

				The %sGravity Forms plugin%s must be installed and activated for the Gravity Forms Addons plugin to work.

				If you haven\'t installed the plugin, you can %3$spurchase the plugin here%4$s. If you have, and you believe this notice is in error, %5$sstart a topic on the plugin support forum%4$s.

				%6$s%7$sBuy Gravity Forms%4$s%8$s
				', 'gravity-forms-addons'), '<strong>', '</strong>', "<a href='http://katz.si/gravityforms'>", '</a>', '<a href="http://wordpress.org/tags/gravity-forms-addons?forum_id=10#postform">', '<p class="submit">', "<a href='http://katz.si/gravityforms' style='color:white!important' class='button button-primary'>", '</p>');
			}
		}
		if(!empty($message)) {
			echo '<div id="message" class="error">'.wpautop($message).'</div>';
		} else if($message = get_transient('kws_gf_activation_notice')) {
			echo '<div id="message" class="updated">'.wpautop($message).'</div>';
			delete_transient('kws_gf_activation_notice');
		}
	}

    public function activation() {
		self::add_activation_notice();
    }

	public function add_activation_notice() {
#		if(!get_option("gf_addons_settings")) {
			$message = __(sprintf('Congratulations - the Gravity Forms Directory & Addons plugin has been installed. %sGo to the settings page%s to read usage instructions and configure the plugin default settings. %sGo to settings page%s', '<a href="'.admin_url('admin.php?page=gf_settings&addon=Directory+%26+Addons&viewinstructions=true').'">', '</a>', '<p class="submit"><a href="'.admin_url('admin.php?page=gf_settings&addon=Directory+%26+Addons&viewinstructions=true').'" class="button button-secondary">', '</a></p>'), 'gravity-forms-addons');
			set_transient('kws_gf_activation_notice', $message, 60*60);
#		}
	}

	public function admin_head($settings = array()) {
		if(empty($settings)) {
			$settings = GFDirectory::get_settings();
		}

		if(!empty($settings['modify_admin']['expand'])) {
			if(@$_REQUEST['page'] == 'gf_edit_forms' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
				$style = '<style type="text/css">
					.gforms_edit_form_expanded ul.menu li.add_field_button_container ul,
					.gforms_edit_form_expanded ul.menu li.add_field_button_container ul ol {
						display:block!important;
					}
					#floatMenu {padding-top:1.4em!important;}
				</style>';
				$style= apply_filters('kws_gf_display_all_fields', $style);
				echo $style;
			}
		}
		if(!empty($settings['modify_admin']['ids'])) {
			echo '<script src="'.GFCommon::get_base_url().'/js/jquery.simplemodal-1.3.min.js"></script>'; // Added for the new IDs popup
		}

		if(isset($_REQUEST['page']) && ($_REQUEST['page'] == 'gf_edit_forms' || $_REQUEST['page'] == 'gf_entries')) {
			echo self::add_edit_js(isset($_REQUEST['id']), $settings);
		}
	}

	private function add_edit_js($edit_forms = false, $settings = array()) {
	?>
		<script type="text/javascript">
			// Edit link for Gravity Forms entries
			jQuery(document).ready(function($) {
	<?php	if(!empty($settings['modify_admin']['expand']) && $edit_forms) { ?>
				var onScrollScript = window.onscroll;
				$('div.gforms_edit_form #add_fields #floatMenu').prepend('<div class="gforms_expend_all_menus_form"><label for="expandAllMenus"><input type="checkbox" id="expandAllMenus" value="1" /> Expand All Menus</label></div>');

				$('input#expandAllMenus').live('click', function(e) {
					if($(this).is(':checked')) {
						window.onscroll = '';
						$('div.gforms_edit_form').addClass('gforms_edit_form_expanded');
						//$('ul.menu li .button-title-link').unbind().die(); // .unbind() is for the initial .click()... .die() is for the live() below
					} else {
						window.onscroll = onScrollScript;
						$('div.gforms_edit_form').removeClass('gforms_edit_form_expanded');
					}
				});

				<?php
			}
			if(!empty($settings['modify_admin']['toggle']) && $edit_forms) { ?>

				$('ul.menu').addClass('noaccordion');
			<?php
			}

			if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'gf_entries' && !empty($settings['modify_admin']['edit'])) {
				?>
				// Changed from :contains('Delete') to :last-child to work with 1.6
				$(".row-actions span:last-child").each(function() {
					var editLink = $(this).parents('tr').find('.column-title a').attr('href');
					editLink = editLink + '&screen_mode=edit';
					//alert();
					$(this).after('<span class="edit">| <a title="<?php _e("Edit this entry", "gravity-forms-addons"); ?>" href="'+editLink+'"><?php _e("Edit", "gravity-forms-addons"); ?></a></span>');
				});
				<?php
			}

			else if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'gf_edit_forms' && !empty($settings['modify_admin']['ids'])) {
				?>
				// Changed from :contains('Delete') to :last-child for future-proofing
				$(".row-actions span.edit:last-child").each(function() {
					var formID = $(this).parents('tr').find('.column-id').text();;
					$(this).after('<span class="edit">| <a title="<?php _e("View form field IDs", "gravity-forms-addons"); ?>" href="<?php  echo WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/field-ids.php"; ?>?id='+formID+'&amp;show_field_ids=true" class="form_ids"><?php _e("IDs", "gravity-forms-addons"); ?></a></span>');
				});
					var h = $('#gravityformspreviewidsiframe').css('height');

					$("a.form_ids").live('click', function(e) {
						e.preventDefault();
						var src = $(this).attr('href');
						$.modal('<iframe src="' + src + '" width="" height="" style="border:0;">', {
//							closeHTML:"<a href='#'>Close</a>",
							minHeight:400,
							minWidth: 600,
							containerCss:{
								borderColor: 'transparent',
								borderWidth: 0,
								padding:10,
								escClose: true,
								minWidth:500,
								maxWidth:800,
								minHeight:500,
							},
							overlayClose:true,
							onShow: function(dlg) {
								var iframeHeight = $('iframe', $(dlg.container)).height();
								var containerHeight = $(dlg.container).height();
								var iframeWidth = $('iframe', $(dlg.container)).width();
								var containerWidth = $(dlg.container).width();

								if(containerHeight < iframeHeight) { $(dlg.container).height(iframeHeight); }
								else { $('iframe', $(dlg.container)).height(containerHeight); }

								if(containerWidth < iframeWidth) { $(dlg.container).width(iframeWidth); }
								else { $('iframe', $(dlg.container)).width(containerWidth); }
							}
						});
			         });

				<?php } ?>
			});
		</script>
		<?php
	}

	function show_field_ids($form = array()) {
		if(isset($_REQUEST['show_field_ids'])) {
		$form = RGFormsModel::get_form_meta($_GET["id"]);
		$form = RGFormsModel::add_default_properties($form);

		echo <<<EOD
		<style type="text/css">
			#input_ids th, #input_ids td { border-bottom:1px solid #999; padding:.25em 15px; }
			#input_ids th { border-bottom-color: #333; font-size:.9em; background-color: #464646; color:white; padding:.5em 15px; font-weight:bold;  }
			#input_ids { background:#ccc; margin:0 auto; font-size:1.2em; line-height:1.4; width:100%; border-collapse:collapse;  }
			#input_ids strong { font-weight:bold; }
			#preview_hdr { display:none;}
			#input_ids caption { color:white!important;}
		</style>
EOD;

		if(!empty($form)) { echo '<table id="input_ids"><caption id="input_id_caption">Fields for <strong>Form ID '.$form['id'].'</strong></caption><thead><tr><th>Field Name</th><th>Field ID</th></thead><tbody>'; }
		foreach($form['fields'] as $field) {
			// If there are multiple inputs for a field; ie: address has street, city, zip, country, etc.
			if(is_array($field['inputs'])) {
				foreach($field['inputs'] as $input) {
					echo "<tr><td width='50%'><strong>{$input['label']}</strong></td><td>{$input['id']}</td></tr>";
				}
			}
			// Otherwise, it's just the one input.
			else {
				echo "<tr><td width='50%'><strong>{$field['label']}</strong></td><td>{$field['id']}</td></tr>";
			}
		}
		if(!empty($form)) { echo '</tbody></table><div style="clear:both;"></div></body></html>'; exit(); }
		} else {
			return $form;
		}
	}

	static function add_mce_popup(){

		//Action target that displays the popup to insert a form to a post/page
		?>
		<script type="text/javascript">
			function addslashes (str) {
				   // Escapes single quote, double quotes and backslash characters in a string with backslashes
				   // discuss at: http://phpjs.org/functions/addslashes
				   return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
			}

			jQuery('document').ready(function($) {


			    jQuery('#select_gf_directory_form .datepicker').each(
			    	function (){
				    	if($.fn.datepicker) {
				            var element = jQuery(this);
				            var format = "yy-mm-dd";

				            var image = "";
				            var showOn = "focus";
				            if(element.hasClass("datepicker_with_icon")){
				                showOn = "both";
				                image = jQuery('#gforms_calendar_icon_' + this.id).val();
				            }

				            element.datepicker({ yearRange: '-100:+10', showOn: showOn, buttonImage: image, buttonImageOnly: true, dateFormat: format });
				        }
				   }
			    );


				$('#select_gf_directory_form').bind('submit', function(e) {
					e.preventDefault();
					var shortcode = InsertGFDirectory();
					//send_to_editor(shortcode);
					return false;
				});



				$('#insert_gf_directory').live('click', function(e) {
					e.preventDefault();

					$('#select_gf_directory_form').trigger('submit');
					return;
				});

				$('a.select_gf_directory').live('click', function(e) {
					// This auto-sizes the box
					if(typeof tb_position == 'function') {
						tb_position();
					}
					return;
				});

				jQuery('a.kws_gf_advanced_settings').click(function(e) {  e.preventDefault(); jQuery('#kws_gf_advanced_settings').toggle(); return false; });

				function InsertGFDirectory(){
					var directory_id = jQuery("#add_directory_id").val();
					if(directory_id == ""){
						alert("<?php _e("Please select a form", "gravity-forms-addons") ?>");
						jQuery('#add_directory_id').focus();
						return false;
					}

			<?php
					$js = self::make_popup_options(true);

					$ids = $idOutputList = $setvalues = $vars = '';

					foreach($js as $j) {
						$vars .= $j['js'] ."
						";
						$ids .= $j['idcode'] . " ";
						$setvalues .= $j['setvalue']."
						";
						$idOutputList .= $j['id'].'Output' .' + ';
					}
					echo $vars;
					echo $setvalues;
			?>

				var win = window.dialogArguments || opener || parent || top;
				var shortcode = "[directory form=\"" + directory_id +"\"" + <?php echo addslashes($idOutputList); ?>"]";
				win.send_to_editor(shortcode);
				return false;
			}
		});

		</script>
	<div id="select_gf_directory" style="overflow-x:hidden; overflow-y:auto;display:none;">
		<form action="#" method="get" id="select_gf_directory_form">
			<div class="wrap">
				<div>
					<div style="padding:15px 15px 0 15px;">
						<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Insert A Directory", "gravity-forms-addons"); ?></h3>
						<span>
							<?php _e("Select a form below to add it to your post or page.", "gravity-forms-addons"); ?>
						</span>
					</div>
					<div style="padding:15px 15px 0 15px;">
						<select id="add_directory_id">
							<option value="">  <?php _e("Select a Form", "gravity-forms-addons"); ?>  </option>
							<?php
								$forms = RGFormsModel::get_forms(1, "title");
								foreach($forms as $form){
									?>
									<option value="<?php echo absint($form->id) ?>"><?php echo esc_html($form->title) ?></option>
									<?php
								}
							?>
						</select> <br/>
						<div style="padding:8px 0 0 0; font-size:11px; font-style:italic; color:#5A5A5A"><?php _e("This form will be the basis of your directory.", "gravity-forms-addons"); ?></div>
					</div>
						<?php

						self::make_popup_options();

						?>
					<div class="submit">
						<input type="submit" class="button-primary" style="margin-right:15px;" value="Insert Directory" id="insert_gf_directory" />
						<a class="button button-secondary" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel", "gravity-forms-addons"); ?></a>
					</div>
				</div>
			</div>
		</form>
	</div>
		<?php
	}

	function make_popup_options($js = false) {
		$i = 0;

		$defaults = GFDirectory::directory_defaults();

		$standard = array(
				array('text', 'page_size'  ,  20, __( "Number of entries to show at once. Use <code>0</code> to show all entries.", 'gravity-forms-addons')),
				array('select', 'directoryview' , array(
						array('value' => 'table', 'label' => __( "Table", 'gravity-forms-addons')),
						array('value' => 'ul', 'label'=> __( "Unordered List", 'gravity-forms-addons')),
						array('value' => 'dl', 'label' => __( "Definition List", 'gravity-forms-addons')),
					), __( "Format for directory listings (directory view)", 'gravity-forms-addons')
				),
				array('select','entryview' , array(
						array('value' =>'table', 'label' => __( "Table", 'gravity-forms-addons')),
						array('value' =>'ul', 'label'=>__( "Unordered List", 'gravity-forms-addons')),
						array('value' => 'dl', 'label' => __( "Definition List", 'gravity-forms-addons')),
					), __( "Format for single entries (single entry view)", 'gravity-forms-addons')
				),
				array('checkbox',  'search'  ,  true, __( "Show the search field", 'gravity-forms-addons')),
				array('checkbox', 'smartapproval' , true, __("Automatically convert directory into Approved-only when an Approved field is detected.", 'gravity-forms-addons')),
				array('checkbox', 'approved' , false, __("(If Smart Approval above is not enabled) Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved'). <span class='description'><strong>Note:</strong> This will hide entries that have not been explicitly approved.</span>", 'gravity-forms-addons')),
			  );
		if(!$js) {
			echo '<ul>';
			foreach($standard as $o) {
				self::make_field($o[0], $o[1], maybe_serialize($o[2]), $o[3], $defaults);
			}
			echo '</ul>';
		} else {
			foreach($standard as $o) {
				$out[$i] = self::make_popup_js($o[0], $o[1], $defaults);
				$i++;
			}
		}

			$content = array(
			        array('checkbox', 'entry' ,  true, __("If there's a displayed Entry ID column, add link to each full entry", 'gravity-forms-addons')),
					array('checkbox',  'wpautop'  ,  true, __( "Convert bulk paragraph text to paragraphs (using the WordPress function <code><a href='http://codex.wordpress.org/Function_Reference/wpautop'>wpautop()</a></code>)", 'gravity-forms-addons')),
					array('checkbox',  'getimagesize'  ,  false, __( "Calculate image sizes (Warning: this may slow down the directory loading speed!)", 'gravity-forms-addons')),
					array('radio'	, 'postimage' , array(
							array('label' =>'<img src="'.GFCommon::get_base_url().'/images/doctypes/icon_image.gif" /> Show image icon', 'value'=>'icon', 'default'=>'1'),
							array('label' => __('Show full image', 'gravity-forms-addons'), 'value'=>'image')
						), __("How do you want images to appear in the directory?", 'gravity-forms-addons')
					),
					array('checkbox', 'fulltext' , true, __("Show full content of a textarea or post content field, rather than an excerpt", 'gravity-forms-addons')),

					array('date', 'start_date' ,  false, __('Start date (in <code>YYYY-MM-DD</code> format)', 'gravity-forms-addons')),
					array('date', 'end_date' ,  false, __('End date (in <code>YYYY-MM-DD</code> format)', 'gravity-forms-addons')),
			);

			$administration = array(
				array('checkbox', 'showadminonly' ,  false, __("Show Admin-Only columns <span class='description'>(in Gravity Forms, Admin-Only fields are defined by clicking the Advanced tab on a field in the Edit Form view, then editing Visibility > Admin Only)</span>", 'gravity-forms-addons')),
				array('checkbox', 'useredit' , false, __("Allow logged-in users to edit entries they created. Will add an 'Edit Your Entry' field to the Single Entry View.", 'gravity-forms-addons')),
				array('checkbox', 'limituser' , false, __("Display entries only the the creator of the entry (users will not see other people's entries).", 'gravity-forms-addons')),
				array('checkbox', 'adminedit' , false, __(sprintf('Allow %sadministrators%s to edit all entries. Will add an \'Edit Your Entry\' field to the Single Entry View.','<strong>', '</strong>'), 'gravity-forms-addons')),
			);

			$lightbox = array(
				#array('checkbox',  'lightbox'  ,  true, __( sprintf("Show images in a %slightbox%s", '<a href="http://en.wikipedia.org/wiki/Lightbox_(JavaScript)" target="_blank">', '</a>'), 'gravity-forms-addons')),
				array('radio'	, 'lightboxstyle' ,
					array(
						array('label' =>'Style 1 <a href="'.GFDirectory::get_base_url().'/colorbox/example1/index.html" target="_blank">See example</a>', 'value'=>'1'),
						array('label' =>'Style 2 <a href="'.GFDirectory::get_base_url().'/colorbox/example2/index.html" target="_blank">See example</a>', 'value'=>'2'),
						array('label' =>'Style 3 <a href="'.GFDirectory::get_base_url().'/colorbox/example3/index.html" target="_blank">See example</a>', 'value'=>'3','default'=>'1'),
						array('label' =>'Style 4 <a href="'.GFDirectory::get_base_url().'/colorbox/example4/index.html" target="_blank">See example</a>', 'value'=>'4'),
						array('label' =>'Style 5 <a href="'.GFDirectory::get_base_url().'/colorbox/example5/index.html" target="_blank">See example</a>', 'value'=>'5')
					), "What style should the lightbox use?"
				),
				array('checkboxes'	, 'lightboxsettings' ,
					array(
						array('label' => __('Images', 'gravity-forms-addons'), 'value'=>'images', 'default' => '1'),
						array('label' => __( "Entry Links (Open entry details in lightbox)"), 'value'=>'entry'),
						array('label' => __('Website Links (non-entry)', 'gravity-forms-addons'), 'value'=>'urls')
					), __("Set what type of links should be loaded in the lightbox", 'gravity-forms-addons')
				),
				#array('checkbox',  'entrylightbox' ,  false, __( "Open entry details in lightbox (defaults to lightbox settings)", 'gravity-forms-addons'))
			);

			$formatting = array(
				array('checkbox', 'jstable' ,  false, __('Use the TableSorter jQuery plugin to sort the table?', 'gravity-forms-addons')),
				array('checkbox', 'titleshow'  ,  true, __("<strong>Show a form title?</strong> By default, the title will be the form title.", 'gravity-forms-addons')),
				array('checkbox', 'showcount'  ,  true, __("Do you want to show 'Displaying 1-19 of 19'?", 'gravity-forms-addons')),
				array('checkbox', 'thead'  ,  true, __("Show the top heading row (<code>&lt;thead&gt;</code>)", 'gravity-forms-addons')),
				array('checkbox', 'tfoot'  ,  true, __("Show the bottom heading row (<code>&lt;tfoot&gt;</code>)", 'gravity-forms-addons')),
				array('checkbox', 'pagelinksshowall'  ,  true, __("Show each page number (eg: <a>1</a> <a>2</a> <a>3</a> <a>4</a> <a>5</a> <a>6</a> <a>7</a> <a>8</a>) instead of summary (eg: <a>1</a> <a>2</a> <a>3</a> ... <a>8</a> <a>&raquo;</a>)", 'gravity-forms-addons')),
		#		array('checkbox', 'showrowids'  ,  true, __("Show the row ids, which are the entry IDs, in the HTML; eg: <code>&lt;tr id=&quot;lead_row_565&quot;&gt;</code>", 'gravity-forms-addons')),
		#		array('checkbox', 'icon'  ,  false, __("Show the GF icon as it does in admin? <img src=\"". GFCommon::get_base_url()."/images/gravity-title-icon-32.png\" />", 'gravity-forms-addons')),
		#		array('checkbox', 'searchtabindex'  ,  false, __("Adds tabindex='' to the search field", 'gravity-forms-addons')),
				array('checkbox', 'jssearch' ,  true, __("Use JavaScript for sorting (otherwise, <em>links</em> will be used for sorting by column)", 'gravity-forms-addons')),
				array('checkbox', 'dateformat' ,  false, __("Override the options from Gravity Forms, and use standard PHP date formats", 'gravity-forms-addons')),
			);

			$links = array(
				array('checkbox', 'linkemail'  ,  true, __("Convert email fields to email links", 'gravity-forms-addons')),
				array('checkbox', 'linkwebsite'  ,  true, __("Convert URLs to links", 'gravity-forms-addons')),
				array('checkbox', 'truncatelink' ,  false, __("Show more simple links for URLs (strip <code>http://</code>, <code>www.</code>, etc.)", 'gravity-forms-addons')),	#'truncatelink' => false,
				array('checkbox', 'linknewwindow'  ,  false, __("<strong>Open links in new window?</strong> (uses <code>target='_blank'</code>)", 'gravity-forms-addons')),
				array('checkbox', 'nofollowlinks'  ,  false, __("<strong>Add <code>nofollow</code> to all links</strong>, including emails", 'gravity-forms-addons')),
			);

			$address = array(
				array('checkbox', 'appendaddress'  ,  false, __("Add the formatted address as a column at the end of the table", 'gravity-forms-addons')),
				array('checkbox',  'hideaddresspieces'  ,  false, __( "Hide the pieces that make up an address (Street, City, State, ZIP, Country, etc.)", 'gravity-forms-addons'))
			);

			$entry = array(
				array('text',  'entrytitle' ,  __('Entry Detail', 'gravity-forms-addons'), __( "Title of entry lightbox window", 'gravity-forms-addons')),
				array('text',  'entrydetailtitle', __('Entry Detail Table Caption', 'gravity-forms-addons'), __( "The text displayed at the top of the entry details. Use <code>%%formtitle%%</code> and <code>%%leadid%%</code> as variables that will be replaced.", 'gravity-forms-addons')),
				array('text',  'entrylink' ,  __('View entry details', 'gravity-forms-addons'), __( "Link text to show full entry", 'gravity-forms-addons')),
				array('text',  'entryth' ,  __('More Info', 'gravity-forms-addons'), __( "Entry ID column title", 'gravity-forms-addons')),
				array('text',  'entryback' ,  __('&larr; Back to directory', 'gravity-forms-addons'), __( "The text of the link to return to the directory view from the single entry view.", 'gravity-forms-addons')),
				array('checkbox',  'entryonly' ,  true, __( "When viewing full entry, show entry only? Otherwise, show entry with directory below", 'gravity-forms-addons')),
				array('checkbox',  'entryanchor' ,  true, __( "When returning to directory view from single entry view, link to specific anchor row?", 'gravity-forms-addons')),
			);

		$fieldsets = array(
			__('Content Settings', 'gravity-forms-addons') => $content,
			__('Administration of Entries', 'gravity-forms-addons') =>$administration,
			__('Lightbox Options', 'gravity-forms-addons')=>$lightbox,
			__('Formatting Options', 'gravity-forms-addons')=>$formatting,
			__('Link Settings', 'gravity-forms-addons')=>$links,
			__('Address Options', 'gravity-forms-addons')=>$address
		);

		if(!$js) {
			echo '<a href="#kws_gf_advanced_settings" class="kws_gf_advanced_settings">'.__('Show advanced settings', 'gravity-forms-addons').'</a>';
			echo '<div style="display:none;" id="kws_gf_advanced_settings">';
			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>Single-Entry View</h2>";
			echo '<span class="howto">These settings control whether users can view each entry as a separate page or lightbox. Single entries will show all data associated with that entry.</span>';
			echo '<ul style="padding:0 15px 0 15px; width:100%;">';
			foreach($entry as $o) {
				if(isset($o[3])) { $o3 = esc_html($o[3]); } else { $o3 = '';}
				self::make_field($o[0], $o[1], maybe_serialize($o[2]), $o3, $defaults);
			}
			echo '</ul>';

			echo '<div class="hr-divider label-divider"></div>';

			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>".__('Directory View', 'gravity-forms-addons')."</h2>";
			echo '<span class="howto">'.__('These settings affect how multiple entries are shown at once.', 'gravity-forms-addons').'</span>';

			foreach($fieldsets as $title => $fieldset) {
				echo "<fieldset><legend><h3 style='padding-top:1em; padding-bottom:.5em; margin:0;'>{$title}</h3></legend>";
				echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
				foreach($fieldset as $o) {
					self::make_field($o[0], $o[1], maybe_serialize($o[2]), $o[3], $defaults);
				}
				echo '</ul></fieldset>';
				echo '<div class="hr-divider label-divider"></div>';
			}
			echo "<h2 style='margin:0; padding:0; font-weight:bold; font-size:1.5em; margin-top:1em;'>".__('Additional Settings', 'gravity-forms-addons')."</h2>";
			echo '<span class="howto">'.__('These settings affect both the directory view and single entry view.', 'gravity-forms-addons').'</span>';
			echo '<ul style="padding: 0 15px 0 15px; width:100%;">';
		} else {
			foreach($entry as $o) {
				$out[$i] = self::make_popup_js($o[0], $o[1], $defaults);
				$i++;
			}
			foreach($fieldsets as $title => $fieldset) {
				foreach($fieldset as $o) {
					$out[$i] = self::make_popup_js($o[0], $o[1], $defaults);
					$i++;
				}
			}
		}
	$advanced = array(
			array('text', 'tableclass' ,  'gf_directory widefat fixed', __( "Class for the <table>, <ul>, or <dl>", 'gravity-forms-addons')),
			array('text', 'tablestyle' ,  '', __( "inline CSS for the <table>, <ul>, or <dl>", 'gravity-forms-addons')),
			array('text', 'rowclass' ,  '', __( "Class for the <table>, <ul>, or <dl>", 'gravity-forms-addons')),
			array('text', 'rowstyle' ,  '', __( "Inline CSS for all <tbody><tr>'s, <ul><li>'s, or <dl><dt>'s", 'gravity-forms-addons')),
			array('text', 'valign' ,  'baseline', __("Vertical align for table cells", 'gravity-forms-addons')),
			array('text', 'sort' ,  'date_created', __( "Use the input ID ( example: 1.3 or 7 or ip)", 'gravity-forms-addons')),
			array('text', 'dir' ,  'DESC', __("Sort in ascending order (<code>ASC</code> or descending (<code>DESC</code>)", 'gravity-forms-addons')),
			array('text', 'startpage'  ,  1, __( "If you want to show page 8 instead of 1", 'gravity-forms-addons')),
			array('text', 'pagelinkstype'  ,  'plain', __( "Type of pagination links. <code>plain</code> is just a string with the links separated by a newline character. The other possible values are either <code>array</code> or <code>list</code>.", 'gravity-forms-addons')),
			array('text', 'titleprefix'  ,  'Entries for ', __( "Default GF behavior is 'Entries : '", 'gravity-forms-addons')),
			array('text', 'tablewidth'  ,  '100%', __( "Set the 'width' attribute for the <table>, <ul>, or <dl>", 'gravity-forms-addons')),
			array('text', 'datecreatedformat'  ,  get_option('date_format').' \a\t '.get_option('time_format'), __( "Use <a href='http://php.net/manual/en/function.date.php' target='_blank'>standard PHP date formats</a>", 'gravity-forms-addons')),
			array('checkbox', 'credit'  ,  true, __( "Give credit to the plugin creator (who has spent over 200 hours on this free plugin!) with a link at the bottom of the directory", 'gravity-forms-addons'))
			);
		if(!$js) {
			foreach($advanced as $o) {
				self::make_field($o[0], $o[1], maybe_serialize($o[2]), esc_html($o[3]), $defaults);
			}
			echo '</ul></fieldset></div>';
		} else {
			foreach($advanced as $o) {
				$out[$i] = self::make_popup_js($o[0], $o[1], $defaults);
				$i++;
			}
			return $out;
		}
	}

	function make_field($type, $id, $default, $label, $defaults = array()) {
		$rawid = $id;
		$idLabel = '';
		if(GFDirectory::is_gravity_page('gf_settings')){
			$id = 'gf_addons_directory_defaults['.$id.']';
			$idLabel = " <span style='color:#868686'>(".__(sprintf('%s', "<pre style='display:inline'>{$rawid}</pre>"), 'gravity-forms-addons').")</span>";
		}
		$checked = '';
		$label = str_replace('&lt;code&gt;', '<code>', str_replace('&lt;/code&gt;', '</code>', $label));
		$output = '<li class="setting-container" style="width:90%; clear:left; border-bottom: 1px solid #cfcfcf; padding:.25em .25em .4em; margin-bottom:.25em;">';
		$default = maybe_unserialize($default);

		$class = '';
		if($type == 'date') {
			$type = 'text';
			$class = ' class="gf_addons_datepicker datepicker"';
		}

		if($type == "checkbox") {
				if(!empty($defaults["{$rawid}"]) || ($defaults["{$rawid}"] === '1' || $defaults["{$rawid}"] === 1)) {
					$checked = ' checked="checked"';
				}
				$output .= '<label for="gf_settings_'.$rawid.'"><input type="hidden" value="" name="'.$id.'" /><input type="checkbox" id="gf_settings_'.$rawid.'"'.$checked.' name="'.$id.'" /> '.$label.$idLabel.'</label>'."\n";
		}
		elseif($type == "text") {
				$default = $defaults["{$rawid}"];
				$output .= '<label for="gf_settings_'.$rawid.'"><input type="text" id="gf_settings_'.$rawid.'" value="'.htmlspecialchars(stripslashes($default)).'" style="width:40%;" name="'.$id.'"'.$class.' /> <span class="howto">'.$label.$idLabel.'</span></label>'."\n";
		} elseif($type == 'radio' || $type == 'checkboxes') {
			if(is_array($default)) {
				$output .= $label.$idLabel.'<ul class="ul-disc">';
				foreach($default as $opt) {
					if($type == 'radio') {
						$id_opt = $id.'_'.sanitize_title($opt['value']);
						if(!empty($defaults["{$rawid}"]) && $defaults["{$rawid}"] == $opt['value']) { $checked = ' checked="checked"'; } else { $checked = ''; }
						$inputtype = 'radio';
						$name = $id;
						$value = $opt['value'];
						$output .= '
						<li><label for="gf_settings_'.$id_opt.'">';
					} else {
						$id_opt = $rawid.'_'.sanitize_title($opt['value']);
						if(!empty($defaults["{$rawid}"][sanitize_title($opt['value'])])) { $checked = ' checked="checked"'; } else { $checked = ''; }
						$inputtype = 'checkbox';
						$name = $id.'['.sanitize_title($opt['value']).']';
						$value = 1;
						$output .= '
							<li><label for="gf_settings_'.$id_opt.'">
								<input type="hidden" value="0" name="'.$name.'" />';
					}
					$output .= '
							<input type="'.$inputtype.'"'.$checked.' value="'.$value.'" id="gf_settings_'.$id_opt.'" name="'.$name.'" /> '.$opt['label']." <span style='color:#868686'>(".__(sprintf('%s', "<pre style='display:inline'>".sanitize_title($opt['value'])."</pre>"), 'gravity-forms-addons').")</span>".'
						</label>
					</li>'."\n";
				}
				$output .= "</ul>";
			}
		} elseif($type == 'select') {
			if(is_array($default)) {
				$output .= '
				<label for="gf_settings_'.$rawid.'">'.$label.'
				<select name="'.$id.'" id="gf_settings_'.$rawid.'">';
				foreach($default as $opt) {

					if(!empty($defaults["{$rawid}"]) && $defaults["{$rawid}"] == $opt['value']) { $checked = ' selected="selected"'; } else { $checked = ''; }
					$id_opt = $id.'_'.sanitize_title($opt['value']);
					$output .= '<option'.$checked.' value="'.$opt['value'].'"> '.$opt['label'].'</option>'."\n";
				}
				$output .= '</select>'.$idLabel.'
				</label>
				';
			} else {
				$output = '';
			}
		}
		if(!empty($output)) {
			$output .= '</li>'."\n";
			echo $output;
		}
	}

	function make_popup_js($type, $id, $defaults) {

		foreach($defaults as $key => $default) {
			if($default === true || $default === 'on') {
				$defaults[$key] = 'true';
			} elseif($default === false || ($type == 'checkbox' && empty($default))) {
				$defaults[$key] = 'false';
			}
		}
		$defaultsArray = array();
		if($type == "checkbox") {
			$js = 'var '.$id.' = jQuery("#gf_settings_'.$id.'").is(":checked") ? "true" : "false";';
		} elseif($type == "checkboxes" && is_array($defaults["{$id}"])) {
			$js = ''; $i = 0;
			$js .= "\n\t\t\tvar ".$id.' = new Array();';
			foreach($defaults["{$id}"] as $key => $value) {
				$defaultsArray[] = $key;
				$js .=  "\n\t\t\t".$id.'['.$i.'] = jQuery("input#gf_settings_'.$id.'_'.$key.'").is(":checked") ? "'.$key.'" : null;';
				$i++;
			}
		} elseif($type == "text" || $type == "date") {
			$js = 'var '.$id.' = jQuery("#gf_settings_'.$id.'").val();';
		} elseif($type == 'radio') {
			$js = '
			if(jQuery("input[name=\''.$id.'\']:checked").length > 0) {
				var '.$id.' = jQuery("input[name=\''.$id.'\']:checked").val();
			} else {
				var '.$id.' = jQuery("input[name=\''.$id.'\']").eq(0).val();
			}';
		} elseif($type == 'select') {
			$js = '
			if(jQuery("select[name=\''.$id.'\']:selected").length > 0) {
				var '.$id.' = jQuery("select[name=\''.$id.'\']:selected").val();
			} else {
				var '.$id.' = jQuery("select[name=\''.$id.'\']").eq(0).val();
			}';
		}
		$set = '';
		if(!is_array($defaults["{$id}"])) {
			$idCode = $id.'=\""+'.$id.'+"\"';
			$set = 'var '.$id.'Output = (jQuery.trim('.$id.') == "'.trim(addslashes(stripslashes($defaults["{$id}"]))).'") ? "" : " '.$idCode.'";';
		} else {

			$idCode2 = $id.'.join()';
			$idCode = '"'.$idCode2.'"';
			$set = '
			'.$id.' =  jQuery.grep('.$id.',function(n){ return(n); });
			var '.$id.'Output = (jQuery.trim('.$idCode2.') === "'.implode(',',$defaultsArray).'") ? "" : " '.$id.'=\""+ '.$idCode2.'+"\"";';
		}
		 // Debug

		$return = array('js'=>$js, 'id' => $id, 'idcode'=>$idCode, 'setvalue' => $set);

		return $return;
	}

	public function add_form_button() {

		$output = '
			<style>
			.gfdirectory_media_icon {
                background:url('.plugins_url( '/editor-icon.gif', __FILE__).') no-repeat top left;
	            display: inline-block;
	            height: 16px;
	            margin: 0 2px 0 0;
	            vertical-align: text-top;
	            width: 16px;
            }
            </style>
            <a href="#TB_inline?width=640&inlineId=select_gf_directory" class="thickbox button select_gf_directory gform_media_link" id="add_gform" title="' . __("Add a Gravity Forms Directory", 'gravity-forms-addons') . '"><span class="gfdirectory_media_icon "></span> ' . __("Add Directory", "gravityforms") . '</a>';

		echo $output;
	}

	//Creates directory left nav menu under Forms
    public static function create_menu($menus){
        // Adding submenu if user has access
        $permission = GFDirectory::has_access("gravityforms_directory");
        if(!empty($permission))
            $menus[] = array("name" => "gf_settings&addon=Directory+%26+Addons", "label" => __("Directory &amp; Addons", "gravity-forms-addons"), "callback" =>  array("GFDirectory_Admin", "settings_page"), "permission" => $permission);

        return $menus;
    }

    public static function settings_page(){
		$message = $validimage = false; global $plugin_page;

        if(isset($_POST["gf_addons_submit"])){
            check_admin_referer("update", "gf_directory_update");

            $settings = array(
            	"directory" => isset($_POST["gf_addons_directory"]),
            	"referrer" => isset($_POST["gf_addons_referrer"]),
            	"directory_defaults" => GFDirectory::directory_defaults($_POST['gf_addons_directory_defaults'], true),
            	"modify_admin" => isset($_POST["gf_addons_modify_admin"]) ? $_POST["gf_addons_modify_admin"] : array(),
            	"version" => GFDirectory::get_version(),
            	"saved" => true
            );
            $message = __('Settings saved.', 'gravity-forms-addons');
            update_option("gf_addons_settings", $settings);
        } else {
           $settings = GFDirectory::get_settings();
	    }

        ?>
        <style type="text/css">
            .ul-square li { list-style: square!important; }
            .ol-decimal li { list-style: decimal!important; }
            .form-table label { font-size: 1em!important; margin: .4em 0; display: block;}
            li.setting-container { border: none!important; }
            #kws_gf_donate {
				float: right;
				width: 300px;
				padding: 0 10px;
				color: #333;
				margin-bottom: 10px;
			}
			#kws_gf_donate .button-primary {
				display:block; float:left; margin:5px 0; text-align:center;
			}
			#kws_gf_donate img {
				float: left;
				margin-right: 10px;
				margin-bottom: .5em;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
			}

        </style>
        <script type="text/javascript">
        	jQuery('document').ready(function($) {
				$('#kws_gf_advanced_settings').show();
				$('a:contains(Directory)', $('ul.subsubsub')).css('font-weight', 'bold');
				$('.wp-submenu li.current, .wp-submenu li.current a').removeClass('current');
				$('a:contains(Directory)', $('.wp-submenu')).addClass('current').parent('li').addClass('current');

				$('a.kws_gf_advanced_settings').hide(); //click(function(e) {  e.preventDefault(); jQuery('#kws_gf_advanced_settings').slideToggle(); return false; });

				$('#kws_gf_advanced_settings').change(function() {
					if($("#gf_settings_thead:checked").length || $("#gf_settings_tfoot:checked").length) {
						$('#gf_settings_jssearch').parents('li').show();
					} else {
						$('#gf_settings_jssearch').parents('li').hide();
					}
				}).trigger('change');

				$('label[for=gf_addons_directory]').live('load click', function() {
					if($('#gf_addons_directory').is(":checked")) {
						$("tr#directory_settings_row").show();
					} else {
						$("tr#directory_settings_row").hide();
					}
				}).trigger('load');

				$('#kws_gf_instructions_button').click(function(e) {
					e.preventDefault();
					visible = $('#kws_gf_instructions').is(':visible');
					if(!visible) { $('#kws_gf_donate').slideUp(150); }
					$('#kws_gf_instructions').slideToggle(function() {
						var $this = $(this);
						var $that = $('#kws_gf_instructions_button');
						$that.text(function() {
							if(visible) {
								$('#kws_gf_donate').slideDown(100);
								return '<?php _e('Hide Instructions', 'gravity-forms-addons'); ?>';
							} else {
								return '<?php _e('View Directory Instructions', 'gravity-forms-addons'); ?>';
							}
						});
					});
					return false;
				});

				$('#message.fade').delay(1000).fadeOut('slow');

			});
		</script>
		<div class="wrap">
		<?php
			if($plugin_page !== 'gf_settings') {

				echo '<h2>'.__('Gravity Forms Directory Add-on',"gravity-forms-addons").'</h2>';
			}
			if($message) {
				echo "<div class='fade below-h2 updated' id='message'>".wpautop($message)."</div>";
			}

		// if you must, you can filter this out...
		if(apply_filters('kws_gf_show_donate_box', true)) {
		?>
		<div id="kws_gf_donate" class="alert_gray"<?php echo isset($_GET['viewinstructions']) ? ' style="display:none;"' : ''; ?>>
			<p>
			<?php if(!is_ssl()) {?><img src="http://www.gravatar.com/avatar/f0f175f8545912adbdab86f0b586f4c3?s=64" alt="Zack Katz, plugin author" height="64" width="64" /> <?php } _e('Hi there! If you find this plugin useful, consider showing your appreciation by making a small donation to its author!', 'gravity-forms-addons'); ?>
			<a href="http://katz.si/35" target="_blank" class="button button-primary"><?php _e('Donate using PayPal', 'gravity-forms-addons'); ?></a>
			</p>
		</div>
		<?php } ?>
		<p class="submit"><span style="padding-right:.5em;" class="description">Need help getting started?</span> <a href="#" class="button button-secondary" id="kws_gf_instructions_button"><?php
			if(!empty($settings['saved']) && !isset($_REQUEST['viewinstructions'])) {
				_e('View Directory Instructions', 'gravity-forms-addons');
			} else {
				_e('Hide Directory Instructions', 'gravity-forms-addons');
			}
		?></a></p>

		<div id="kws_gf_instructions"<?php if(!empty($settings['saved']) && !isset($_REQUEST['viewinstructions'])) {?>  class="hide-if-js clear" <?php } ?>>
			<div class="delete-alert alert_gray">
				<div class="alignright" style="margin:1em 1.2em;">
					<iframe width="400" height="255" src="http<?php echo is_ssl() ? 's' : '';?>://www.youtube.com/embed/PMI7Jb-RP2I?hd=1" frameborder="0" allowfullscreen></iframe>
				</div>
				<h3><?php _e('To integrate a form with Directory:', 'gravity-forms-addons'); ?></h3>
				<ol class="ol-decimal">
					<li><?php _e('Go to the post or page where you would like to add the directory.', 'gravity-forms-addons'); ?></li>
					<li><?php _e('Click the "Add Directory" button above the content area.', 'gravity-forms-addons'); ?></li>
					<li><?php _e('Choose a form from the drop-down menu and configure settings as you would like them.', 'gravity-forms-addons'); ?></li>
					<li><?php _e('Click "Insert Directory". A "shortcode" should appear in the content editor that looks similar to <code style="font-size:1em;">[directory form="#"]</code>', 'gravity-forms-addons'); ?></li>
					<li><?php _e('Save the post or page', 'gravity-forms-addons'); ?></li>
				</ol>

				<h4><?php _e('Configuring Fields &amp; Columns', "gravity-forms-addons"); ?></h4>

				<?php echo wpautop(__('When editing a form, click on a field to expand the field. Next, click the "Directory" tab. There, you will find options to:',"gravity-forms-addons")); ?>

		        <ul class="ul-square">
				        <li><?php _e("Choose whether you would like the field to be a link to the Single Entry View;", "gravity-forms-addons"); ?></li>
				        <li><?php _e("Hide the field in Directory View; and", "gravity-forms-addons"); ?></li>
				        <li><?php _e("Hide the field in Single Entry View", "gravity-forms-addons"); ?></li>
				</ul>

				<h4><?php _e('Configuring Column Visibility &amp; Order', "gravity-forms-addons"); ?></h4>

				<?php echo wpautop(__('When editing a form in Gravity Forms, click the link near the top-center of the page named "Directory Columns"',"gravity-forms-addons")); ?>

		        <ol class="ol-decimal">
				        <li><?php _e('When editing a form in Gravity Forms, click the link near the top-center of the page named "Directory Columns"', "gravity-forms-addons"); ?></li>
				        <li><?php _e('Drag and drop columns from the right ("Hidden Columns") side to the left ("Visible Columns") side.', "gravity-forms-addons"); ?></li>
				        <li><?php _e('Click the "Save" button', "gravity-forms-addons"); ?></li>
				</ol>

			</div>

			<div class="hr-divider"></div>
	    </div>
        <form method="post" action="" class="clear">
            <?php wp_nonce_field("update", "gf_directory_update") ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gf_addons_directory"><?php _e("Gravity Forms Directory", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_directory" class="howto"><input type="checkbox" id="gf_addons_directory" name="gf_addons_directory" <?php checked($settings["directory"]); ?> /> <?php _e('Enable Gravity Forms Directory capabilities', 'gravity-forms-addons'); ?></label>
                    </td>
                </tr>
                <tr id="directory_settings_row">
                	<th scope="row"></th>
                	<td>
                		<h2 style="margin-bottom:0; padding-bottom:0;"><?php _e("Directory Default Settings", "gravity-forms-addons"); ?></h2>
                		<h3><?php _e("These defaults can be over-written when inserting a directory.", "gravity-forms-addons"); ?></h3>

                		<?php
                		self::make_popup_options(false);
                		?>
                		<div class="hr-divider"></div>
                	</td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_addons_referrer"><?php _e("Add Referrer Data to Emails", "gravity-forms-addons"); ?></label> </th>
                    <td>
                        <label for="gf_addons_referrer"><input type="checkbox" id="gf_addons_referrer" name="gf_addons_referrer" <?php checked($settings["referrer"]); ?> /> <?php _e("Adds referrer data to entries, including the path the user took to get to the form before submitting.", 'gravity-forms-addons'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gf_addons_modify_admin"><?php _e("Modify Gravity Forms Admin", "gravity-forms-addons"); ?></label> </th>
                    <td>
                       <ul>
	                        <li><label for="gf_addons_modify_admin_expand"><input type="checkbox" id="gf_addons_modify_admin_expand" name="gf_addons_modify_admin[expand]" <?php checked(isset($settings["modify_admin"]['expand'])); ?> /> <?php _e("Show option to expand Form Editor Field boxes", "gravity-forms-addons"); ?></label></li>

	                        <li><label for="gf_addons_modify_admin_toggle"><input type="checkbox" id="gf_addons_modify_admin_toggle" name="gf_addons_modify_admin[toggle]" <?php checked(isset($settings["modify_admin"]['toggle'])); ?> /> <?php _e('When clicking Form Editor Field boxes, toggle open and closed instead of "accordion mode" (closing all except the clicked box).', "gravity-forms-addons"); ?></label></li>

	                        <li><label for="gf_addons_modify_admin_edit"><input type="checkbox" id="gf_addons_modify_admin_edit" name="gf_addons_modify_admin[edit]" <?php checked(isset($settings["modify_admin"]['edit'])); ?> /> <?php _e(sprintf("Makes possible direct editing of entries from %sEntries list view%s", '<a href="'.admin_url('admin.php?page=gf_entries').'">', '</a>'), "gravity-forms-addons"); ?></label></li>

	                        <li><label for="gf_addons_modify_admin_ids"><input type="checkbox" id="gf_addons_modify_admin_ids" name="gf_addons_modify_admin[ids]" <?php checked(isset($settings["modify_admin"]['ids'])); ?> /> <?php _e(sprintf("Adds a link in the Forms list view to view form IDs", '<a href="'.admin_url('admin.php?page=gf_edit_forms').'">', '</a>'), "gravity-forms-addons"); ?></label></li>
                      </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" ><input type="submit" name="gf_addons_submit" class="button-primary" value="<?php _e("Save Settings", "gravity-forms-addons") ?>" /></td>
                </tr>
            </table>
        </form>
        </div>
        <?php
    }
}