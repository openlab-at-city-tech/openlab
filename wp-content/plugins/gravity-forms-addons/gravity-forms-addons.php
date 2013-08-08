<?php
/*
Plugin Name: Gravity Forms Directory & Addons
Plugin URI: http://www.seodenver.com/gravity-forms-addons/
Description: Turn <a href="http://katz.si/gravityforms" rel="nofollow">Gravity Forms</a> into a great WordPress directory...and more!
Author: Katz Web Services, Inc.
Version: 3.4.1
Author URI: http://www.katzwebservices.com

Copyright 2012 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

register_activation_hook( __FILE__, array('GFDirectory', 'activation')  );
add_action('plugins_loaded',  array('GFDirectory', 'plugins_loaded'));
add_action('plugins_loaded',  'kws_gf_load_functions');

class GFDirectory {

	private static $path = "gravity-forms-addons/gravity-forms-addons.php";
	private static $url = "http://www.gravityforms.com";
	private static $slug = "gravity-forms-addons";
	private static $version = "3.4.1";
	private static $min_gravityforms_version = "1.3.9";

	public static function directory_defaults($args = array()) {
    	$defaults = array(
			'form' => 1, // Gravity Forms form ID
			'approved' => false, // Show only entries that have been Approved (have a field in the form that is an Admin-only checkbox with a value of 'Approved'
			'smartapproval' => true, // Auto-convert form into Approved-only when an Approved field is detected.
			'directoryview' => 'table', // Table, list or DL
			'entryview' => 'table', // Table, list or DL
			'hovertitle' => true, // Show column name as user hovers over cell
			'tableclass' => 'gf_directory widefat fixed', // Class for the <table>
			'tablestyle' => '', // inline CSS for the <table>
			'rowclass' => '', // Class for the <table>
			'rowstyle' => '', // inline CSS for all <tbody><tr>'s
			'valign' => '',
			'sort' => 'date_created', // Use the input ID ( example: 1.3 or 7 or ip )
			'dir' => 'DESC',

			'useredit' => false,
			'limituser' => false,
			'adminedit' => false,

			'status' => 'active', // Added in 2.0
			'start_date' => '', // Added in 2.0
			'end_date' => '', // Added in 2.0

			'wpautop' => true, // Convert bulk paragraph text to...paragraphs
			'page_size' => 20, // Number of entries to show at once
			'startpage' => 1, // If you want to show page 8 instead of 1

			'lightboxstyle' => 3,
			'lightboxsettings' => array('images' => true, 'entry' => null, 'websites' => null),

			'showcount' => true, // Do you want to show "Displaying 1-19 of 19"?
			'pagelinksshowall' => true, // Whether to show each page number, or just 7
			'next_text' => '&raquo;',
			'prev_text' => '&laquo;',
			'pagelinkstype' => 'plain', // 'plain' is just a string with the links separated by a newline character. The other possible values are either 'array' or 'list'.
			'fulltext' => true, // If there's a textarea or post content field, show the full content or a summary?
			'linkemail' => true, // Convert email fields to email mailto: links
			'linkwebsite' => true, // Convert URLs to links
			'linknewwindow' => false, // Open links in new window? (uses target="_blank")
			'nofollowlinks' => false, // Add nofollow to all links, including emails
			'titleshow' => true, // Show a form title? By default, the title will be the form title.
			'titleprefix' => 'Entries for ', // Default GF behavior is 'Entries : '
			'tablewidth' => '100%', // 'width' attribute for the table
			'searchtabindex' => false, // adds tabindex="" to the search field
			'search' => true, // show the search field
			'tfoot' => true, // show the <tfoot>
			'thead' => true, // show the <thead>
			'showadminonly' => false, // Admin only columns aren't shown by default, but can be (added 2.0.1)
			'datecreatedformat' => get_option('date_format').' \a\t '.get_option('time_format'), // Use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'credit' => true, // Credit link
			'dateformat' => false, // Override the options from Gravity Forms, and use standard PHP date formats (http://php.net/manual/en/function.date.php)
			'postimage' => 'icon', // Whether to show icon, thumbnail, or large image
			'getimagesize' => false,
			'entry' => true, // If there's an Entry ID column, link to the full entry
			'entrylink' => 'View entry details',
			'entryth' => 'More Info',
			'entryback' => '&larr; Back to directory',
			'entryonly' => true,
			'entrytitle' => 'Entry Detail',
			'entrydetailtitle' => '%%formtitle%% : Entry # %%leadid%%',
			'entryanchor' => true,
			'truncatelink' => false,
			'appendaddress' => false,
			'hideaddresspieces' => false,
			'jssearch' => true,
			'jstable' => false,
			'lightbox' => null, // depreciated - Combining with lightboxsettings
			'entrylightbox' => null, // depreciated - Combining with lightboxsettings
		);

		$settings = get_option("gf_addons_settings");
		if(isset($settings['directory_defaults'])) {
			$defaults = wp_parse_args($settings['directory_defaults'], $defaults);
		}

		$options = wp_parse_args($args, $defaults);

		// Backward Compatibility
		if(!empty($args['lightbox'])) { $options['lightboxsettings']['images'] = 1; }
		if(!empty($args['entrylightbox'])) { $options['lightboxsettings']['entry'] = 1; }
		unset($options['lightbox'], $options['entrylightbox']); // Depreciated for lightboxsettings

		return apply_filters('kws_gf_directory_defaults', $options);
    }

	public static function plugins_loaded() {

		if(!self::is_gravityforms_installed()) { return false; }

		include_once(WP_PLUGIN_DIR . '/' . basename(dirname( __FILE__ )) .'/edit-form.php');
		include_once(WP_PLUGIN_DIR . '/' . basename(dirname( __FILE__ )) .'/admin.php');
        include_once(WP_PLUGIN_DIR . '/' . basename(dirname( __FILE__ )) .'/change-lead-creator.php');

		if(in_array(RG_CURRENT_PAGE, array("gf_entries", "admin.php", "admin-ajax.php"))) {
	    	self::globals_get_approved_column();
	    }
	    if(self::is_gravity_page()) {
		    self::load_functionality();
		}

	    add_action('init',  array('GFDirectory', 'init'));
	    self::process_bulk_update();
	    add_shortcode('directory', array('GFDirectory', 'make_directory'));

	}

    //Plugin starting point. Will load appropriate files
    public static function init(){
		global $current_user;

		self::add_rewrite();

		if(!self::is_gravityforms_supported()){
           return;
        }

        if(!is_admin()){

		   add_action('template_redirect', array('GFDirectory', 'enqueue_files'));
        	if(apply_filters('kws_gf_directory_canonical_add', true)) {
				add_filter('post_link', array('GFDirectory','directory_canonical'), 1, 3);
				add_filter('page_link', array('GFDirectory','directory_canonical'), 1, 3);
			}
			if(apply_filters('kws_gf_directory_shortlink', true)) {
				add_filter('get_shortlink', array('GFDirectory', 'shortlink'));
			}
			add_filter('kws_gf_directory_lead_filter', array('GFDirectory','show_only_user_entries'), 10, 2);
			add_filter('kws_gf_directory_anchor_text', array('GFDirectory', 'directory_anchor_text'));
        }

        //integrating with Members plugin
        if(function_exists('members_get_capabilities')) {
            add_filter('members_get_capabilities', array("GFDirectory", "members_get_capabilities"));
        }

        add_filter('kws_gf_directory_td_address', array('GFDirectory','format_address'), 1, 2); // Add this filter so it can be removed or overridden by users

        if(self::is_directory_page()){

            //enqueueing sack for AJAX requests
            wp_enqueue_script(array("sack", 'datepicker'));
			wp_enqueue_style('gravityforms-admin', GFCommon::get_base_url().'/css/admin.css');

         }
         else if(self::is_gravity_page('gf_entries')) {
            wp_enqueue_script( 'thickbox', array('jquery'));
         	add_filter("gform_get_field_value", array('GFDirectory','add_lead_approved_hidden_input'), 1, 3);
         }
         else if(in_array(RG_CURRENT_PAGE, array("admin-ajax.php"))){
            add_action('wp_ajax_rg_update_feed_active', array('GFDirectory', 'update_feed_active'));
            add_action('wp_ajax_gf_select_directory_form', array('GFDirectory', 'select_directory_form'));
            add_action('wp_ajax_rg_update_approved', array('GFDirectory','directory_update_approved_hook'));
            add_action('wp_ajax_change_directory_columns', array('GFDirectory', 'change_directory_columns'));
        } else if(in_array(RG_CURRENT_PAGE, array("plugins.php"))){

	        add_filter('plugin_action_links', array('GFDirectory', 'settings_link'), 10, 2 );

	    }

    }

    //Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities
    public static function members_get_capabilities( $caps ) {
        return array_merge($caps, array("gravityforms_directory", "gravityforms_directory_uninstall"));
    }

    public function activation() {
		self::add_permissions();
		self::flush_rules();
    }

    public static function is_gravityforms_installed(){
        return class_exists("RGForms");
    }

    public static function add_permissions(){
        global $wp_roles;
        $wp_roles->add_cap("administrator", "gravityforms_directory");
        $wp_roles->add_cap("administrator", "gravityforms_directory_uninstall");
    }

    public function flush_rules() {
		global $wp_rewrite;
		self::add_rewrite();
		$wp_rewrite->flush_rules();
		return;
	}



    private function load_functionality() {

    	register_deactivation_hook( __FILE__, array('GFDirectory', 'uninstall') );

		$settings = GFDirectory::get_settings();
		extract($settings);

		if($referrer) {
			// Load Joost's referrer tracker
			@include_once('gravity-forms-referrer.php');
		}

	}

	public function shortlink($link = '') {
		global $post;
		if(empty($post)) { return; }
		if(empty($link) && isset($post->guid)) {
			$link = $post->guid;
			return $link;
		}

		$url = add_query_arg(array());
		if(preg_match('/'.sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry')).'\/([0-9]+)(?:\/|-)([0-9]+)\/?/ism',$url, $matches)) {
			$link = add_query_arg(array('form'=>(int)$matches[1], 'leadid'=>(int)$matches[2]), $link);
		} elseif(isset($_REQUEST['leadid']) && isset($_REQUEST['form'])) {
			$link = add_query_arg(array('leadid'=>(int)$_REQUEST['leadid'], 'form'=>(int)$_REQUEST['form']), $link);
		}
		return $link;
	}

	public function directory_canonical($permalink, $sentPost = '', $leavename = '') {

		// This was messing up the wp menu links
		if(did_action('wp_head')) { return $permalink; }

		global $post; $post->permalink = $permalink; $url = add_query_arg(array());
		$sentPostID = is_object($sentPost) ? $sentPost->ID : $sentPost;
		// $post->ID === $sentPostID is so that add_query_arg match doesn't apply to prev/next posts; just current
		preg_match('/('.sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry')).'\/([0-9]+)(?:\/|-)([0-9]+)\/?)/ism',$url, $matches);
		if(isset($post->ID) && $post->ID === $sentPostID && !empty($matches)) {
			return trailingslashit($permalink).$matches[0];
		} elseif(isset($post->ID) && $post->ID === $sentPostID && (isset($_REQUEST['leadid']) && isset($_REQUEST['form'])) || !empty($matches)) {
			if($matches)  { $leadid = $matches[2]; $form = $matches[1]; }
			else { $leadid = $_REQUEST['leadid']; $form = $_REQUEST['form']; }

			return add_query_arg(array('leadid' =>$leadid, 'form'=>$form), trailingslashit($permalink));
		}
		return $permalink;
	}

    public function enqueue_files() {
    	global $post, $kws_gf_styles, $kws_gf_scripts,$kws_gf_directory_options;

    	$kws_gf_styles = isset($kws_gf_styles) ? $kws_gf_styles : array();
    	$kws_gf_scripts = isset($kws_gf_scripts) ? $kws_gf_scripts : array();

    	if(	!empty($post) &&
    		!empty($post->post_content) &&
    		preg_match('/(.?)\[(directory)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/', $post->post_content, $matches)
    	) {

			$options = self::directory_defaults(shortcode_parse_atts($matches[3]));
    		if(!is_array($options['lightboxsettings'])) { $options['lightboxsettings'] = explode(',', $options['lightboxsettings']); }

    		$kws_gf_directory_options = $options;
    		do_action('kws_gf_directory_enqueue', $options, $post);

			extract($options);

    		if($jstable) {
    			$theme = apply_filters('kws_gf_tablesorter_theme', 'blue', $form);
    			wp_enqueue_style('tablesorter-'.$theme, plugins_url( "/tablesorter/themes/{$theme}/style.css", __FILE__));
    			wp_enqueue_script('tablesorter-min', plugins_url( "/tablesorter/jquery.tablesorter.min.js", __FILE__), array('jquery'));
    			$kws_gf_styles[] = 'tablesorter-'.$theme;
    			$kws_gf_scripts[] = 'tablesorter-min';
    		}

    		if(!empty($lightboxsettings)) {
    			wp_enqueue_script('colorbox', plugins_url( "/colorbox/js/jquery.colorbox-min.js", __FILE__), array('jquery'));
    			wp_enqueue_style('colorbox', plugins_url( "/colorbox/example{$lightboxstyle}/colorbox.css", __FILE__), array());
    			$kws_gf_scripts[] = $kws_gf_styles[] = 'colorbox';
    			add_action(apply_filters('kws_gf_directory_colorbox_action', 'wp_footer'), array('GFDirectory', 'load_colorbox'), 1000);
			}
    	}
    }

    function format_colorbox_settings($colorboxSettings = array()) {
    	$settings = array();
    	if(!empty($colorboxSettings) && is_array($colorboxSettings)) {
			foreach($colorboxSettings as $key => $value) {
				if($value === null) { continue; }
				if($value === true) {
					$value = 'true';
				} elseif(empty($value) && $value !== 0) {
					$value = 'false';
				} else {
					$value = '"'.$value.'"';
				}
				$settings["{$key}"] = $key.':'.$value.'';
			}
		}
		return $settings;
    }

    public function load_colorbox() {
    	global $kws_gf_directory_options;
    	extract($kws_gf_directory_options);

		$lightboxsettings = apply_filters('kws_gf_directory_lightbox_settings', $lightboxsettings);
		$colorboxSettings = apply_filters('kws_gf_directory_colorbox_settings', array(
			'width' => apply_filters('kws_gf_directory_lightbox_width', '70%'),
			'height' => apply_filters('kws_gf_directory_lightbox_height', '70%'),
			'iframe' => true,
			'maxWidth' => '95%',
			'maxHeight' => '95%',
			'current' => '{current} of {total}',
			'rel' => apply_filters('kws_gf_directory_lightbox_settings_rel', null)
		));

		?>
    <script type="text/javascript">
    	jQuery(document).ready(function($) {
 <?php
    		$output = '';
    		foreach($lightboxsettings as $key => $value) {
    			$settings = $colorboxSettings;
    			if(is_numeric($key)) { $key = $value; }
    			switch($key) {
    				case "images":
	    				$settings['width'] = $settings['height'] = $settings['iframe'] = null;
	    				break;
	    			case "urls":
	    				$settings['height'] = '80%';
	    				break;
	    		}
    			$output .= "\t\t".'$(".colorbox[rel~=\'directory_'.$key.'\']").colorbox(';
    			if(!empty($settings)) {
	    			$output .= "{\n\t\t\t".implode(",\n\t\t\t",self::format_colorbox_settings(apply_filters("kws_gf_directory_lightbox_{$key}_settings", $settings)))."\n\t\t}";
    			}
    			$output .= ");\n\n";
    		}
    		echo $output;
    		do_action('kws_gf_directory_jquery', $kws_gf_directory_options);
    		?>
    	});
    </script>
    	<?php
    }


    public function add_rewrite() {
    	global $wp_rewrite,$wp;

		if(!$wp_rewrite->using_permalinks()) { return; }
		$endpoint = sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry'));

		# @TODO: Make sure this works in MU
		$wp_rewrite->add_permastruct("{$endpoint}", $endpoint.'/%'.$endpoint.'%/?', true);
		$wp_rewrite->add_endpoint("{$endpoint}",EP_ALL);
	}

    //Returns true if the current page is one of Gravity Forms pages. Returns false if not
    public static function is_gravity_page($page = array()){
        $current_page = trim(strtolower(RGForms::get("page")));
        if(empty($page)) {
	        $gf_pages = array("gf_edit_forms","gf_new_form","gf_entries","gf_settings","gf_export","gf_help");
	    } else {
	    	$gf_pages = is_array($page) ? $page : array($page);
	    }

        return in_array($current_page, $gf_pages);
    }

    function directory_update_approved($lead_id = 0, $approved = 0, $form_id = 0, $approvedcolumn = 0) {
        global $wpdb, $_gform_directory_approvedcolumn, $current_user;
        $current_user = wp_get_current_user();
        $user_data = get_userdata($current_user->ID);

        if(!empty($approvedcolumn)) { $_gform_directory_approvedcolumn = $approvedcolumn; }

        if(empty($_gform_directory_approvedcolumn)) { return false; }

        $lead_detail_table = RGFormsModel::get_lead_details_table_name();

        // This will be faster in the 1.6+ future.
        if(function_exists('gform_update_meta')) { gform_update_meta($lead_id, 'is_approved', $approved); }

        if(empty($approved)) {
            //Deleting details for this field
            $sql = $wpdb->prepare("DELETE FROM $lead_detail_table WHERE lead_id=%d AND field_number BETWEEN %f AND %f ", $lead_id, $_gform_directory_approvedcolumn - 0.001, $_gform_directory_approvedcolumn + 0.001);
            $wpdb->query($sql);

            RGFormsModel::add_note($lead_id, $current_user->ID, $user_data->display_name, stripslashes(__('Disapproved the lead', 'gravity-forms-addons')));

        } else {

            // Get the fields for the lead
            $current_fields = $wpdb->get_results($wpdb->prepare("SELECT id, field_number FROM $lead_detail_table WHERE lead_id=%d", $lead_id));

            $lead_detail_id = RGFormsModel::get_lead_detail_id($current_fields, $_gform_directory_approvedcolumn);

            // If there's already a field for the approved column, then we update it.
            if($lead_detail_id > 0){
                $update = $wpdb->update($lead_detail_table, array("value" => $approved), array("lead_id" => $lead_id, 'form_id' => $form_id, 'field_number' => $_gform_directory_approvedcolumn), array("%s"), array("%d", "%d", "%f"));
            }
            // Otherwise, we create it.
            else {
                $update = $wpdb->insert($lead_detail_table, array("lead_id" => $lead_id, "form_id" => $form_id, "field_number" => $_gform_directory_approvedcolumn, "value" => $approved), array("%d", "%d", "%f", "%s"));
            }

            RGFormsModel::add_note($lead_id, $current_user->ID, $user_data->display_name, stripslashes(__('Approved the lead', 'gravity-forms-addons')));
        }
    }

	public function edit_lead_detail($Form, $lead, $options) {
		global $current_user, $_gform_directory_approvedcolumn;
		require_once(GFCommon::get_base_path() . "/form_display.php");
		if(empty($_gform_directory_approvedcolumn)) { $_gform_directory_approvedcolumn = self::get_approved_column($Form); }

		// We fetch this again, since it may have had some admin-only columns taken out.
		#$lead = RGFormsModel::get_lead($lead["id"]);

		// If you want to allow users to edit their own approval (?) add a filter and return true.
		if(apply_filters('kws_gf_directory_allow_user_edit_approved', false) === false) {
			$Form['fields'] = self::remove_approved_column('form', $Form['fields'], $_gform_directory_approvedcolumn);
		}

		// If this is not the form that should be edited
		list($urlformid, $urlleadid) = self::get_form_and_lead_ids();
		if(intval($Form['id']) !== intval($urlformid) || intval($lead['id']) !== intval($urlleadid)) { return; }

		// If either of these two things are false (creator of lead, or admin)
		if(!(

			// Users can edit their own listings, they are logged in, the current user is the creator of the lead
			(!empty($options['useredit']) && is_user_logged_in() && intval($current_user->id) === intval($lead['created_by'])) === true || // OR

			// Administrators can edit every listing, and this person has administrator access
			(!empty($options['adminedit']) && self::has_access("gravityforms_directory")) === true)
		) {
			// Kick them out.
			_e(sprintf('%sYou do not have permission to edit this form.%s', '<div class="error">', '</div>'), 'gravity-forms-addons');
			return;
		}

		$validation_message = '';

		 // If the form is submitted
		if(RGForms::post("action") === "update") {
            check_admin_referer('gforms_save_entry', 'gforms_save_entry');

            $lead = apply_filters('kws_gf_directory_lead_being_updated', $lead, $Form);

            // We don't DO passwords.
            foreach($Form['fields'] as $key => $field) {
                if($field['type'] === 'password') { unset($Form['fields'][$key]); }
            }

			$is_valid = GFFormDisplay::validate($Form, $lead);

           $validation_message = '';
	    	foreach($Form['fields'] as $field) {
	    		if(!GFCommon::is_product_field($field["type"])){
	        		$validation_message .= (rgget("failed_validation", $field) && !empty($field["validation_message"])) ? sprintf("<li class='gfield_description validation_message'><strong>%s</strong>: %s</li>", $field["label"], $field["validation_message"]) : "";;
	        	}
	    	}
	    	if(!empty($validation_message)) {
	        	$validation_message = '<ul>'.$validation_message.'</ul>';
	        	_e(apply_filters('kws_gf_directory_lead_error_message', sprintf("%sThere were errors with the edit you made.%s%s", "<div class='error' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'><p>", "</p>", $validation_message.'</div>'), $lead, $Form), 'gravity-forms-addons');
	    	}

	    	// So the form submission always throws an error even though there's no problem.
	    	// Product fields can't be edited, so that doesn't really matter.
           if(!empty($is_valid) || (empty($is_valid) && empty($validation_message))) {
	            do_action('kws_gf_directory_pre_update_lead', $lead, $Form);
	            RGFormsModel::save_lead($Form, $lead);
	            $lead = RGFormsModel::get_lead($lead["id"]);

	            do_action('kws_gf_directory_post_update_lead', $lead, $Form);
	            _e(apply_filters('kws_gf_directory_lead_updated_message', sprintf("%sThe entry was successfully updated.%s", "<p class='updated' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'>", "</p>"), $lead, $Form), 'gravity-forms-addons');
	            return $lead;
            }
		}

		if((isset($_GET['edit']) && wp_verify_nonce($_GET['edit'], 'edit')) || !empty($validation_message)) {
		?>
			<form method="post" id="entry_form" enctype="multipart/form-data" action="<?php echo remove_query_arg(array('gf_search','sort','dir', 'pagenum', 'edit'), add_query_arg(array()));?>">
		<?php
	            wp_nonce_field('gforms_save_entry', 'gforms_save_entry');
	    ?>
	            <input type="hidden" name="action" id="action" value="update"/>
	            <input type="hidden" name="screen_mode" id="screen_mode" value="edit" />
	            <?php
	            	$form_without_products = $Form;
                    $post_message_shown = false;
	            	foreach($Form['fields'] as $key => $field) {
	            		if(
                           GFCommon::is_product_field($field["type"]) ||
                           is_numeric($lead["post_id"]) && GFCommon::is_post_field($field)
                        ){
                            if(is_numeric($lead["post_id"]) && GFCommon::is_post_field($field) && !$message_shown ) {
                                echo apply_filters('kws_gf_directory_edit_post_details_text', sprintf('You can edit post details from the %1$spost page%2$s.', '<a href="'.admin_url('post.php?action=edit&post='.$lead["post_id"]).'">', '</a>'), $field, $lead, $lead['post_id']);
                                $message_shown = true;
                            }

		            		unset($form_without_products['fields'][$key]);
		            		$product_fields[] = $field['id'];
		            		if(!empty($field['inputs'])) {
		            		foreach($field['inputs'] as $input) {
			            		$product_fields[] = $input['id'];
		            		}
		            		}
	            		}
	            	}

                    $lead_without_products = &$lead;
                    foreach($product_fields as $product_field) {
                        $value = RGFormsModel::get_lead_field_value($lead, $field);
                        unset($lead_without_products[$product_field]);
                    }

	            	require_once(GFCommon::get_base_path() . "/entry_detail.php");
	            	GFEntryDetail::lead_detail_edit(apply_filters( 'kws_gf_directory_form_being_edited', $form_without_products, $lead), apply_filters( 'kws_gf_directory_lead_being_edited', $lead_without_products, $form_without_products));
					_e('<input class="button-primary" type="submit" tabindex="4" value="'.apply_filters('kws_gf_directory_update_lead_button_text', __('Update Entry', 'gravity-forms-addons')).'" name="save" />');
				?>
			</form>
			<?php
			do_action('kws_gf_directory_post_after_edit_lead_form', $lead, $Form);
			return false;
		} elseif((isset($_GET['edit']) && !wp_verify_nonce($_GET['edit'], 'edit'))) {
			_e(apply_filters('kws_gf_directory_edit_access_error_message', sprintf("%sThe link to edit this entry is not valid; it may have expired.%s", "<p class='error' id='message' style='padding:.5em .75em; background-color:#ffffcc; border:1px solid #ccc;'>", "</p>"), $lead, $Form), 'gravity-forms-addons');
		}

		return $lead;
	}


	public function lead_detail($Form, $lead, $allow_display_empty_fields=false, $inline = true, $options = array()) {
			global $current_user, $_gform_directory_approvedcolumn;
			get_currentuserinfo();

			$display_empty_fields = ''; $allow_display_empty_fields = true;
			if($allow_display_empty_fields){
				$display_empty_fields = @rgget("gf_display_empty_fields", $_COOKIE);
			}
			if(empty($options)) {
				$options = self::directory_defaults();
			}

			// There is no edit link
			if(isset($_GET['edit']) || RGForms::post("action") === "update") {
				// Process editing leads
				$lead = self::edit_lead_detail($Form, $lead, $options);
				if(RGForms::post("action") !== "update") { return; }
			}

			extract($options);

			?>
			<table cellspacing="0" class="widefat fixed entry-detail-view">
			<?php
				$title = str_replace('%%formtitle%%', $Form["title"], str_replace('%%leadid%%', $lead['id'], $entrydetailtitle));
				if(!empty($title) && $inline) { ?>
				<thead>
					<tr>
						<th id="details" colspan="2" scope="col">
						<?php
							$title = apply_filters('kws_gf_directory_detail_title', apply_filters('kws_gf_directory_detail_title_'.(int)$lead['id'], array($title, $lead), true), true);
							if(is_array($title)) {
								echo $title[0];
							} else {
								echo $title;
							}
						?>
						</th>
					</tr>
				</thead>
				<?php
				}
				?>
				<tbody>
					<?php
					$count = 0;
					$field_count = sizeof($Form["fields"]);

					foreach($Form["fields"] as $field){

						// Don't show fields defined as hide in single.
						if(!empty($field['hideInSingle'])) {
							if(self::has_access("gravityforms_directory")) {
								echo "\n\t\t\t\t\t\t\t\t\t".'<!-- '.__(sprintf('(Admin-only notice) Field #%d not shown: "Hide This Field in Single Entry View" was selected.', $field['id']), 'gravity-forms-addons').' -->'."\n\n";
							}
							continue;
						}

						$count++;
						$is_last = $count >= $field_count ? true : false;

						switch(RGFormsModel::get_input_type($field)){
							case "section" :
	                            if(!GFCommon::is_section_empty($field, $Form, $lead) || $display_empty_fields){
	                                $count++;
	                                $is_last = $count >= $field_count ? true : false;
	                                ?>
	                                <tr>
	                                    <td colspan="2" class="entry-view-section-break<?php echo $is_last ? " lastrow" : ""?>"><?php echo esc_html(GFCommon::get_label($field))?></td>
	                                </tr>
	                                <?php
	                            }
	                        break;

	                        case "captcha":
	                        case "html":
	                        case "password":
	                        case "page":
	                            //ignore captcha, html, password, page field
	                        break;

							case "fileupload" :
							case "post_image" :
								$value = RGFormsModel::get_lead_field_value($lead, $field);
								$valueArray = explode("|:|", $value);

								@list($url, $title, $caption, $description) = $valueArray;
								$size = '';
								if(!empty($url)){
									//displaying thumbnail (if file is an image) or an icon based on the extension
									 $icon = self::get_icon_url($url);
									 if(!preg_match('/icon\_image\.gif/ism', $icon)) {
									 	$lightboxclass = '';
									 	$src = $icon;
									 	if(!empty($getimagesize)) {
											$size = @getimagesize($src);
											$img = "<img src='$src' {$size[3]}/>";
										} else {
											$size = false;
											$img = "<img src='$src' />";
										}
									 } else { // No thickbox for non-images please
									 	switch(strtolower(trim($postimage))) {
									 		case 'image':
									 			$src = $url;
									 			break;
									 		case 'icon':
									 		default:
									 			$src = $icon;
									 			break;
									 	}
									 	if(!empty($getimagesize)) {
											$size = @getimagesize($src);
										} else {
											$size = false;
										}
									 }
									 $img = array(
									 	'src' => $src,
									 	'size' => $size,
									 	'title' => $title,
									 	'caption' => $caption,
									 	'description' => $description,
									 	'url' => esc_attr($url),
									 	'code' => isset($size[3]) ? "<img src='$src' {$size[3]} />" : "<img src='$src' />"
									 );
									 $img = apply_filters('kws_gf_directory_lead_image', apply_filters('kws_gf_directory_lead_image_'.$postimage, apply_filters('kws_gf_directory_lead_image_'.$lead['id'], $img)));
									 $value = $display_value = "<a href='{$url}'{$target}{$lightboxclass}>{$img['code']}</a>";
								}
							break;

							default :
								//ignore product fields as they will be grouped together at the end of the grid
                            if(GFCommon::is_product_field($field["type"])){
                                $has_product_fields = true;
                                continue;
                            }

                            $value = RGFormsModel::get_lead_field_value($lead, $field);
                            $display_value = GFCommon::get_lead_field_display($field, $value, $lead["currency"]);

                            $display_value = apply_filters("gform_entry_field_value", $display_value, $field, $lead, $Form);
                            if($display_empty_fields || !empty($display_value) || $display_value === "0"){
                                $count++;
                                $is_last = $count >= $field_count && !$has_product_fields ? true : false;
                                $last_row = $is_last ? " lastrow" : "";

                                $display_value =  empty($display_value) && $display_value !== "0" ? "&nbsp;" : $display_value;

                                $content = '
                                <tr>
                                    <th colspan="2" class="entry-view-field-name">' . esc_html(GFCommon::get_label($field)) . '</th>
                                </tr>
                                <tr>
                                    <td colspan="2" class="entry-view-field-value' . $last_row . '">' . $display_value . '</td>
                                </tr>';

                                $content = apply_filters("gform_field_content", $content, $field, $value, $lead["id"], $Form["id"]);

                                echo $content;

                            }
							break;
						}
					} // End switch

					$products = array();
                if($has_product_fields){
                    $products = GFCommon::get_product_fields($Form, $lead);
                    if(!empty($products["products"])){
                        ?>
                        <tr>
                            <td colspan="2" class="entry-view-field-name"><?php echo apply_filters("gform_order_label_{$Form["id"]}", apply_filters("gform_order_label", __("Order", "gravityforms"), $Form["id"]), $Form["id"]) ?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="entry-view-field-value lastrow">
                                <table class="entry-products" cellspacing="0" width="97%">
                                    <colgroup>
                                          <col class="entry-products-col1">
                                          <col class="entry-products-col2">
                                          <col class="entry-products-col3">
                                          <col class="entry-products-col4">
                                    </colgroup>
                                    <thead>
                                        <th scope="col"><?php echo apply_filters("gform_product_{$form_id}", apply_filters("gform_product", __("Product", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col" class="textcenter"><?php echo apply_filters("gform_product_qty_{$form_id}", apply_filters("gform_product_qty", __("Qty", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col"><?php echo apply_filters("gform_product_unitprice_{$form_id}", apply_filters("gform_product_unitprice", __("Unit Price", "gravityforms"), $form_id), $form_id) ?></th>
                                        <th scope="col"><?php echo apply_filters("gform_product_price_{$form_id}", apply_filters("gform_product_price", __("Price", "gravityforms"), $form_id), $form_id) ?></th>
                                    </thead>
                                    <tbody>
                                    <?php

                                        $total = 0;
                                        foreach($products["products"] as $product){
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="product_name"><?php echo esc_html($product["name"])?></div>
                                                    <ul class="product_options">
                                                        <?php
                                                        $price = GFCommon::to_number($product["price"]);
                                                        if(is_array(rgar($product,"options"))){
                                                            $count = sizeof($product["options"]);
                                                            $index = 1;
                                                            foreach($product["options"] as $option){
                                                                $price += GFCommon::to_number($option["price"]);
                                                                $class = $index == $count ? " class='lastitem'" : "";
                                                                $index++;
                                                                ?>
                                                                <li<?php echo $class?>><?php echo $option["option_label"]?></li>
                                                                <?php
                                                            }
                                                        }
                                                        $subtotal = floatval($product["quantity"]) * $price;
                                                        $total += $subtotal;
                                                        ?>
                                                    </ul>
                                                </td>
                                                <td class="textcenter"><?php echo $product["quantity"] ?></td>
                                                <td><?php echo GFCommon::to_money($price, $lead["currency"]) ?></td>
                                                <td><?php echo GFCommon::to_money($subtotal, $lead["currency"]) ?></td>
                                            </tr>
                                            <?php
                                        }
                                        $total += floatval($products["shipping"]["price"]);
                                    ?>
                                    </tbody>
                                    <tfoot>
                                        <?php
                                        if(!empty($products["shipping"]["name"])){
                                        ?>
                                            <tr>
                                                <td colspan="2" rowspan="2" class="emptycell">&nbsp;</td>
                                                <td class="textright shipping"><?php echo $products["shipping"]["name"] ?></td>
                                                <td class="shipping_amount"><?php echo GFCommon::to_money($products["shipping"]["price"], $lead["currency"])?>&nbsp;</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <?php
                                            if(empty($products["shipping"]["name"])){
                                            ?>
                                                <td colspan="2" class="emptycell">&nbsp;</td>
                                            <?php
                                            }
                                            ?>
                                            <td class="textright grandtotal"><?php _e("Total", "gravityforms") ?></td>
                                            <td class="grandtotal_amount"><?php echo GFCommon::to_money($total, $lead["currency"])?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>

                        <?php
                    }
                }

					// Edit link
					if(
						!empty($options['useredit']) && is_user_logged_in() && $current_user->id === $lead['created_by'] || // Is user who created the entry
						!empty($options['adminedit']) && self::has_access("gravityforms_directory") // Or is an administrator
					) {

					if(!empty($options['adminedit']) && self::has_access("gravityforms_directory")) {
						$editbuttontext = apply_filters('kws_gf_directory_edit_entry_text_admin', __("Edit Entry", 'gravity-forms-addons'));
					} else {
						$editbuttontext = apply_filters('kws_gf_directory_edit_entry_text_user', __("Edit Your Entry", 'gravity-forms-addons'));
					}

					?>
						<tr>
							<th scope="row" class="entry-view-field-name"><?php _e(apply_filters('kws_gf_directory_edit_entry_th', "Edit"), "gravity-forms-addons"); ?></th>
							<td class="entry-view-field-value useredit"><a href="<?php echo add_query_arg(array('edit' => wp_create_nonce('edit'))); ?>"><?php _e($editbuttontext); ?></a></td>
						</tr>
					<?php
					}

					?>
				</tbody>
			</table>
			<?php
	}

	public function get_admin_only($form, $adminOnly = array()) {
		if(!is_array($form)) { return false; }

		foreach($form['fields'] as $key=>$col) {
			// Only the Go to Entry button adds disableMargins.

			if($col['type'] === 'hidden' && !empty($col['useAsEntryLink']) && !empty($col['disableMargins'])) {
				continue;
			}
			if(!empty($col['adminOnly'])) {
				$adminOnly[] = $col['id'];
			}
			if(isset($col['inputs']) && is_array($col['inputs'])) {
				foreach($col['inputs'] as $key2=>$input) {
					if(!empty($col['adminOnly'])) {
						$adminOnly[] = $input['id'];
					}
				}
			}
		}
		return $adminOnly;
	}

	/*
	* 	Get the form and lead IDs from the URL or from $_REQUEST
	*	@return array|null $formid, $leadid if found. Null if not.
	*/
	private function get_form_and_lead_ids() {
		global $wp, $wp_rewrite;

		$formid = $leadid = null;

		$url = isset($wp->request) ? $wp->request : add_query_arg(array());

		if(
			// If permalinks is turned on
			$wp_rewrite->using_permalinks() &&
			// And
			preg_match('/\/?'.sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry')).'\/([0-9]+)(?:\/|-)([0-9]+)/ism', $url, $matches)) {
			$formid = $matches[1];
			$leadid = $matches[2];
		} else {
			$formid = isset($_REQUEST['form']) ? (int)$_REQUEST['form'] : $formid;
			$leadid = isset($_REQUEST['leadid']) ? (int)$_REQUEST['leadid'] : $leadid;
		}

		return array($formid, $leadid);

	}


	/**
	 * get_back_link function.
	 *
	 * @access public
	 * @param string $entryback (default: '') The text of the back-link anchor
	 * @return string The HTML link for the backlink
	 */
	public function get_back_link($options = array()) {
		global $pagenow,$wp_rewrite;

		if(empty($options)) {
			$options = self::directory_defaults();
		}

		if(isset($_GET['edit'])) {
			return '<p class="entryback"><a href="'.add_query_arg(array(), remove_query_arg(array('edit'))).'">'.esc_html(__(apply_filters('kws_gf_directory_edit_entry_cancel', "&larr; Cancel Editing"), "gravity-forms-addons")).'</a></p>';
		}

		list($formid, $leadid) = self::get_form_and_lead_ids();
		extract($options);

        if($pagenow !== 'entry-details.php') {
            $href = remove_query_arg(array('row', 'leadid', 'form', 'edit'));
    		if($wp_rewrite->using_permalinks()) {
    			$href = preg_replace('/('.sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry')).'\/(?:[0-9]+)(?:\/|-)(?:[0-9]+)\/?)/ism', '', $href);
    		}
    		$url = parse_url(add_query_arg(array(), $href));
    		if(!empty($url['query']) && !empty($permalink)) { $href .= '?'.$url['query']; }
    		if(!empty($options['entryanchor'])) { $href .= '#lead_row_'.$leadid; }
        } else {
            $href = '#" onclick="parent.jQuery.fn.colorbox.close();';
        }

		// If there's a back link, format it
		if(!empty($entryback) && !empty($entryonly)) {
			$link = apply_filters('kws_gf_directory_backlink', '<p class="entryback"><a href="'.$href.'">'.esc_html($entryback).'</a></p>', $href, $entryback);
		} else {
			$link = '';
		}

		return $link;
	}

	public function process_lead_detail($inline = true, $entryback = '', $showadminonly = false, $adminonlycolumns = array(), $approvedcolumn = null, $options = array(), $entryonly = true) {
		global $wp,$post,$wp_rewrite,$wpdb;
		$formid = $leadid = false;

		list($formid, $leadid) = self::get_form_and_lead_ids();

		if(!is_null($leadid) && !is_null($formid)) {

			$form = apply_filters('kws_gf_directory_lead_detail_form', RGFormsModel::get_form_meta((int)$formid));
			$lead = apply_filters('kws_gf_directory_lead_detail', RGFormsModel::get_lead((int)$leadid));

			if(empty($approvedcolumn)) { $approvedcolumn = self::get_approved_column($form); }
			if(empty($adminonlycolumns) && !$showadminonly) { $adminonlycolumns = self::get_admin_only($form); }

			if(!$showadminonly)  {
				$lead = self::remove_admin_only(array($lead), $adminonlycolumns, $approvedcolumn, true, true, $form);
				$lead = $lead[0];
				$form['fields'] = self::remove_admin_only($form['fields'], $adminonlycolumns, $approvedcolumn, false, true, $form); // This is screwing things up!
			}

			ob_start(); // Using ob_start() allows us to filter output
				@self::lead_detail($form, $lead, false, $inline, $options);
				$content = ob_get_contents(); // Get the output
			ob_end_clean(); // Clear the buffer

			// Get the back link if this is a single entry.
			$link = !empty($entryonly) ? self::get_back_link() : '';

			$content = $link . $content;
			$content = apply_filters('kws_gf_directory_detail', apply_filters('kws_gf_directory_detail_'.(int)$leadid, $content, (int)$leadid), (int)$leadid);


			if(isset($options['entryview'])) {
				$content = self::html_display_type_filter($content, $options['entryview'], true);
			}

			return $content;
		} else {
			return false;
		}
	}

    public function change_directory_columns() {
        check_ajax_referer('gforms_directory_columns','gforms_directory_columns');
        $columns = GFCommon::json_decode(stripslashes($_POST["directory_columns"]), true);
        self::update_grid_column_meta((int)$_POST['form_id'], $columns);
    }

    public static function update_grid_column_meta($form_id, $columns){
        global $wpdb;

        $meta = maybe_serialize(stripslashes_deep($columns) );

        update_option('gf_directory_form_'.$form_id.'_grid', $meta);
    }

    public static function get_grid_column_meta($form_id){
        global $wpdb;

        $grid = get_option('gf_directory_form_'.$form_id.'_grid');
        if(!$grid) {
            $grid = GFFormsModel::get_grid_column_meta($form_id);
            self::update_grid_column_meta($form_id, $grid);
        }
        return maybe_unserialize($grid);
    }

    public static function get_grid_columns($form_id, $input_label_only=false){
        $form = GFFormsModel::get_form_meta($form_id);
        $field_ids = self::get_grid_column_meta($form_id);

        if(!is_array($field_ids)){
            $field_ids = array();
            for($i=0, $count=sizeof($form["fields"]); $i<$count && $i<5; $i++){
                $field = $form["fields"][$i];

                if(RGForms::get("displayOnly",$field))
                    continue;


                if(isset($field["inputs"]) && is_array($field["inputs"])){
                    $field_ids[] = $field["id"];
                    if($field["type"] == "name"){
                        $field_ids[] = $field["id"] . '.3'; //adding first name
                        $field_ids[] = $field["id"] . '.6'; //adding last name
                    }
                    else if(isset($field["inputs"][0])){
                        $field_ids[] = $field["inputs"][0]["id"]; //getting first input
                    }
                }
                else{
                    $field_ids[] = $field["id"];
                }
            }
            //adding default entry meta columns
            $entry_metas = GFFormsModel::get_entry_meta($form_id);
            foreach ($entry_metas as $key => $entry_meta){
                if (rgar($entry_meta,"is_default_column"))
                    $field_ids[] = $key;
        }
        }

        $columns = array();
        $entry_meta = GFFormsModel::get_entry_meta($form_id);
        foreach($field_ids as $field_id){

            switch($field_id){
                case "id" :
                    $columns[$field_id] = array("label" => "Entry Id", "type" => "id");
                break;
                case "ip" :
                    $columns[$field_id] = array("label" => "User IP", "type" => "ip");
                break;
                case "date_created" :
                    $columns[$field_id] = array("label" => "Entry Date", "type" => "date_created");
                break;
                case "source_url" :
                    $columns[$field_id] = array("label" => "Source Url", "type" => "source_url");
                break;
                case "payment_status" :
                    $columns[$field_id] = array("label" => "Payment Status", "type" => "payment_status");
                break;
                case "transaction_id" :
                    $columns[$field_id] = array("label" => "Transaction Id", "type" => "transaction_id");
                break;
                case "payment_date" :
                    $columns[$field_id] = array("label" => "Payment Date", "type" => "payment_date");
                break;
                case "payment_amount" :
                    $columns[$field_id] = array("label" => "Payment Amount", "type" => "payment_amount");
                break;
                case "created_by" :
                    $columns[$field_id] = array("label" => "User", "type" => "created_by");
                break;
                case ((is_string($field_id) || is_int($field_id)) && array_key_exists($field_id, $entry_meta)) :
                    $columns[$field_id] = array("label" => $entry_meta[$field_id]["label"], "type" => $field_id);
                break;
                default :
                    $field = GFFormsModel::get_field($form, $field_id);
                    if($field) {
                        $columns[strval($field_id)] = array("label" => self::get_label($field, $field_id, $input_label_only), "type" => rgget("type", $field), "inputType" => rgget("inputType", $field));
                    }
            }
        }
        return $columns;
    }

    /**
     * Get the label for the input field. This is necessary to prevent Admin Labels from being used instead of normal labels.
     */
    public static function get_label($field, $input_id = 0, $input_only = false){
        $field_label = rgar($field,"label");
        $input = GFFormsModel::get_input($field, $input_id);
        if(rgget("type", $field) == "checkbox" && $input != null)
            return $input["label"];
        else if($input != null)
            return $input_only ? $input["label"] : $field_label . ' (' . $input["label"] . ')';
        else
            return $field_label;
    }

	function make_directory($atts) {
		global $wpdb,$wp_rewrite,$post, $wpdb,$directory_shown,$kws_gf_scripts,$kws_gf_styles;

		if(!class_exists('GFEntryDetail')) { @require_once(GFCommon::get_base_path() . "/entry_detail.php"); }
		if(!class_exists('GFCommon')) { @require_once(WP_PLUGIN_DIR . "/gravityforms/common.php"); }
		if(!class_exists('RGFormsModel')) { @require_once(WP_PLUGIN_DIR . "/gravityforms/forms_model.php"); }

		//quit if version of wp is not supported
		if(!class_exists('GFCommon') || !GFCommon::ensure_wp_version())
			return;

		// Already showed edit directory form and there are more than one forms on the page.
		if(did_action('kws_gf_directory_post_after_edit_lead_form')) { return; }

		ob_start(); // Using ob_start() allows us to use echo instead of $output .=

		foreach($atts as $key => $att) {
			if(strtolower($att) == 'false') { $atts[$key] = false; }
			if(strtolower($att) == 'true') { $atts[$key] = true; }
		}

		$atts['approved'] = isset($atts['approved']) ? $atts['approved'] : -1;

		if(!empty($atts['lightboxsettings']) && is_string($atts['lightboxsettings'])) {
			$atts['lightboxsettings'] = explode(',', $atts['lightboxsettings']);
		}

		$options = self::directory_defaults($atts);

		// Make sure everything is on the same page.
		if(is_array($options['lightboxsettings'])) {
			foreach($options['lightboxsettings'] as $key => $value) {
				if(is_numeric($key)) {
					$options['lightboxsettings']["{$value}"] = $value;
					unset($options['lightboxsettings']["{$key}"]);
				}
			}
		}

		extract( $options );

			$form_id = $form;

			$form = RGFormsModel::get_form_meta($form_id);

			if(empty($form)) { return;}

			$sort_field = empty($_GET["sort"]) ? $sort : $_GET["sort"];
			$sort_direction = empty($_GET["dir"]) ? $dir : $_GET["dir"];
			$search_query = !empty($_GET["gf_search"]) ? $_GET["gf_search"] : null;

			$start_date = !empty($_GET["start_date"]) ? $_GET["start_date"] : $start_date;
			$end_date = !empty($_GET["end_date"]) ? $_GET["end_date"] : $end_date;

			$page_index = empty($_GET["pagenum"]) ? $startpage -1 : intval($_GET["pagenum"]) - 1;
			$star = (isset($_GET["star"]) && is_numeric($_GET["star"])) ? intval($_GET["star"]) : null;
			$read = (isset($_GET["read"]) && is_numeric($_GET["read"])) ? intval($_GET["read"]) : null;
			$first_item_index = $page_index * $page_size;
			$link_params = array();
			if(!empty($page_index)) { $link_params['pagenum'] = $page_index; }
			$formaction = remove_query_arg(array('gf_search','sort','dir', 'pagenum', 'edit'), add_query_arg($link_params));
			$tableclass .= !empty($jstable) ? ' tablesorter' : '';
			$title = $form["title"];
			$sort_field_meta = RGFormsModel::get_field($form, $sort_field);
			$is_numeric = $sort_field_meta["type"] == "number";
			$columns = self::get_grid_columns($form_id, true);

			$approvedcolumn = null;

			if((!$approved && $approved !== -1) || (!empty($smartapproval) && $approved === -1)) {
                $approvedcolumn = self::get_approved_column($form);
            }

			if(!empty($smartapproval) && $approved === -1 && !empty($approvedcolumn)) {
				$approved = true; // If there is an approved column, turn on approval
			} else {
				$approved = false; // Otherwise, show entries as normal.
			}

			$entrylinkcolumns = self::get_entrylink_column($form, $entry);
			$adminonlycolumns = self::get_admin_only($form);

			//
			// Show only a single entry
			//
			$detail = self::process_lead_detail(true, $entryback, $showadminonly, $adminonlycolumns, $approvedcolumn, $options, $entryonly);

			if(!empty($entry) && !empty($detail)) {

				// Once again, checking to make sure this hasn't been shown already with multiple shortcodes on one page.
				if(!did_action('kws_gf_after_directory')) {
					echo $detail;
				}

				if(!empty($entryonly)) {
					do_action('kws_gf_after_directory', do_action('kws_gf_after_directory_form_'.$form_id, $form, compact("approved","sort_field","sort_direction","search_query","first_item_index","page_size","star","read","is_numeric","start_date","end_date")));

					$content = ob_get_clean(); // Get the output and clear the buffer

					// If the form is form #2, two filters are applied: `kws_gf_directory_output_2` and `kws_gf_directory_output`
					$content = apply_filters('kws_gf_directory_output', apply_filters('kws_gf_directory_output_'.$form_id, self::html_display_type_filter($content, $directoryview)));
					return $content;
				}
			}

			//
			// Or start to generate the directory
			//
#			$leads = RGFormsModel::get_leads($form_id);

			$leads = GFDirectory::get_leads($form_id, $sort_field, $sort_direction, $search_query, $first_item_index, $page_size, $star, $read, $is_numeric, $start_date, $end_date, 'active', $approvedcolumn, $limituser);

			# @TODO - implement filtering!
			#$filters = GFDirectory::get_filters($leads);

			if(!$showadminonly)	 {
				$columns = self::remove_admin_only($columns, $adminonlycolumns, $approvedcolumn, false, false, $form);
				$leads = self::remove_admin_only($leads, $adminonlycolumns, $approvedcolumn, true, false, $form);
			}

			// Allow lightbox to determine whether showadminonly is valid without passing a query string in URL
			if($entry === true && !empty($lightboxsettings['entry'])) {
				if(get_site_transient('gf_form_'.$form_id.'_post_'.$post->ID.'_showadminonly') != $showadminonly) {
					set_site_transient('gf_form_'.$form_id.'_post_'.$post->ID.'_showadminonly', $showadminonly, 60*60);
				}
			} else {
				delete_site_transient('gf_form_'.$form_id.'_post_'.$post->ID.'_showadminonly');
			}


			// Get a list of query args for the pagination links
			if(!empty($search_query)) { $args["gf_search"] = urlencode($search_query); }
			if(!empty($sort_field)) { $args["sort"] = $sort_field; }
			if(!empty($sort_direction)) { $args["dir"] = $sort_direction; }
			if(!empty($star)) { $args["star"] = $star; }

			if($page_size > 0) {

				$lead_count = self::get_lead_count($form_id, $search_query, $star, $read, $approvedcolumn, $approved, $leads, $start_date, $end_date, $limituser);

				$page_links = array(
					'base' =>  @add_query_arg('pagenum','%#%'),// get_permalink().'%_%',
					'format' => '&pagenum=%#%',
					'add_args' => $args,
					'prev_text' => $prev_text,
					'next_text' => $next_text,
					'total' => ceil($lead_count / $page_size),
					'current' => $page_index + 1,
					'show_all' => $pagelinksshowall,
				);

				$page_links = apply_filters('kws_gf_results_pagination', $page_links);

				$page_links = paginate_links($page_links);
			} else {
				// Showing all results
				$page_links = false;
				$lead_count = sizeof($leads);
			}


			if(!isset($directory_shown)) {
				$directory_shown = true;


                ?>

				<script type="text/javascript">
					<?php if(!empty($lightboxsettings['images']) || !empty($lightboxsettings['entry'])) { ?>

					var tb_pathToImage = "<?php echo site_url('/wp-includes/js/thickbox/loadingAnimation.gif'); ?>";
					var tb_closeImage = "<?php echo site_url('/wp-includes/js/thickbox/tb-close.png'); ?>";
					var tb_height = 600;
					<?php } ?>
					function not_empty(variable) {
						if(variable == '' || variable == null || variable == 'undefined' || typeof(variable) == 'undefined') {
							return false;
						} else {
							return true;
						}
					}

				<?php if(!empty($jstable)) { ?>
					jQuery(document).ready(function($) {
						$('.tablesorter').each(function() {
							$(this).tablesorter(<?php echo apply_filters('kws_gf_directory_tablesorter_options', '') ?>);
						});
					});
				<?php } else if(isset($jssearch) && $jssearch) { ?>
					function Search(search, sort_field_id, sort_direction){
						if(not_empty(search)) { var search = "&gf_search=" + encodeURIComponent(search); } else {  var search = ''; }
						if(not_empty(sort_field_id)) { var sort = "&sort=" + sort_field_id; } else {  var sort = ''; }
						if(not_empty(sort_direction)) { var dir = "&dir=" + sort_direction; } else {  var dir = ''; }
						var page = '<?php if($wp_rewrite->using_permalinks()) { echo '?'; } else { echo '&'; } ?>page='+<?php echo isset($_GET['pagenum']) ? intval($_GET['pagenum']) : '"1"'; ?>;
						var location = "<?php echo get_permalink($post->ID); ?>"+page+search+sort+dir;
						document.location = location;
					}
				<?php } ?>
				</script>
				<!-- <link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin.css" type="text/css" /> -->
			<?php } ?>

			<div class="wrap">
				<?php if($titleshow) { ?><h2><?php echo $titleprefix.$title; ?> </h2><?php } ?>
				<?php if($search && ($lead_count > 0 || !empty($_GET['gf_search']))) { ?>
				<form id="lead_form" method="get" action="<?php echo $formaction; ?>">
					<p class="search-box">
						<label class="hidden" for="lead_search"><?php _e("Search Entries:", "gravity-forms-addons"); ?></label>
						<input type="text" name="gf_search" id="lead_search" value="<?php echo $search_query ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex).'"';}?> />
						<?php
							// If not using permalinks, let's make the form work!
							echo !empty($_GET['p']) ? '<input name="p" type="hidden" value="'.esc_html($_GET['p']).'" />' : '';
							echo !empty($_GET['page_id']) ? '<input name="page_id" type="hidden" value="'.esc_html($_GET['page_id']).'" />' : '';
						?>
						<input type="submit" class="button" id="lead_search_button" value="<?php _e("Search", "gravity-forms-addons") ?>"<?php if($searchtabindex) { echo ' tabindex="'.intval($searchtabindex++).'"';}?> />
					</p>
				</form>
				<?php }


				//Displaying paging links if appropriate

					if($lead_count > 0 && $showcount || $page_links){
						if($lead_count == 0) { $first_item_index--; }
						?>
					<div class="tablenav">
						<div class="tablenav-pages">
							<?php if($showcount) {
							if(($first_item_index + $page_size) > $lead_count || $page_size <= 0) {
								$second_part = $lead_count;
							} else {
								$second_part = $first_item_index + $page_size;
							}
							?>
							<span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravity-forms-addons"), $first_item_index + 1, $second_part, $lead_count)  ?></span>
							<?php } if($page_links){ echo $page_links; } ?>
						</div>
						<div class="clear"></div>
					</div>
						<?php
				   }

				do_action('kws_gf_before_directory_after_nav', do_action('kws_gf_before_directory_after_nav_form_'.$form_id, $form, $leads, compact("approved","sort_field","sort_direction","search_query","first_item_index","page_size","star","read","is_numeric","start_date","end_date")));
				?>

				<table class="<?php echo $tableclass; ?>" cellspacing="0"<?php if(!empty($tablewidth)) { echo ' width="'.$tablewidth.'"'; } echo $tablestyle ? ' style="'.$tablestyle.'"' : ''; ?>>
					<?php if($thead) {?>
					<thead>
						<tr>
							<?php

							$addressesExist = false;
							foreach($columns as $field_id => $field_info){
								$dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
								if($field_id == $sort_field) { //reverting direction if clicking on the currently sorted field
									$dir = $sort_direction == "ASC" ? "DESC" : "ASC";
								}
								if(is_array($adminonlycolumns) && !in_array($field_id, $adminonlycolumns) || (is_array($adminonlycolumns) && in_array($field_id, $adminonlycolumns) && $showadminonly) || !$showadminonly) {
								if($field_info['type'] == 'address' && $appendaddress && $hideaddresspieces) { $addressesExist = true; continue; }
								?>
								<?php
                                $_showlink = false;
                                if(isset($jssearch) && $jssearch && !isset($jstable)) { ?>
								<th scope="col" id="gf-col-<?php echo $form_id.'-'.$field_id ?>" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php
								} elseif(isset($jstable) && $jstable || $field_info['type'] === 'id') {?>
									<th scope="col" id="gf-col-<?php echo $form_id.'-'.$field_id ?>" class="manage-column">
								<?php } else {
                                    $_showlink = true;
                                    ?>
								    <th scope="col" id="gf-col-<?php echo $form_id.'-'.$field_id ?>" class="manage-column">
								    <a href="<?php
									$searchpage = isset($_GET['pagenum']) ? intval($_GET['pagenum']) : 1;
									echo add_query_arg(array('gf_search' => $search_query, 'sort' => $field_id, 'dir' => $dir, 'pagenum' => $searchpage), get_permalink($post->ID));
								?>"><?php
								}
								if($field_info['type'] == 'id' && $entry) { $label = $entryth; }
								else { $label = $field_info["label"]; }

								$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $label)));
								echo esc_html($label);

                                if($_showlink) { ?></a><?php } ?>
							   </th>
								<?php
								}
							}

							if($appendaddress && $addressesExist) {
								?>
								<th scope="col" id="gf-col-<?php echo $form_id.'-'.$field_id ?>" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php
								$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'));
								echo esc_html($label)

								 ?></th>
								<?php
							}
							?>
						</tr>
					</thead>
					<?php } ?>
					<tbody class="list:user user-list">
						<?php
							require_once(WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/template-row.php");
						?>
					</tbody>
					<?php if($tfoot) {
						if(isset($jssearch) && $jssearch && !isset($jstable)) {
							$th = '<th scope="col" id="gf-col-'.$form_id.'-'.$field_id.'" class="manage-column" onclick="Search(\''.$search_query.'\', \''.$field_id.'\', \''.$dir.'\');" style="cursor:pointer;">';
						} else {
							$th = '<th scope="col" id="gf-col-'.$form_id.'-'.$field_id.'" class="manage-column">';
						}
					?>
					<tfoot>
						<tr>
							<?php
							$addressesExist = false;
							foreach($columns as $field_id => $field_info){
								$dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
								if($field_id == $sort_field) { //reverting direction if clicking on the currently sorted field
									$dir = $sort_direction == "ASC" ? "DESC" : "ASC";
								}
								if(is_array($adminonlycolumns) && !in_array($field_id, $adminonlycolumns) || (is_array($adminonlycolumns) && in_array($field_id, $adminonlycolumns) && $showadminonly) || !$showadminonly) {
								if($field_info['type'] == 'address' && $appendaddress && $hideaddresspieces) { $addressesExist = true; continue; }

								echo $th;

								if($field_info['type'] == 'id' && $entry) { $label = $entryth; }
								else { $label = $field_info["label"]; }

								$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_'.$field_id, apply_filters('kws_gf_directory_th_'.sanitize_title($label), $label)));
								echo esc_html($label)

								 ?></th>
								<?php
								}
							}
							if($appendaddress && $addressesExist) {
								?>
								<th scope="col" id="gf-col-<?php echo $form_id.'-'.$field_id ?>" class="manage-column" onclick="Search('<?php echo $search_query ?>', '<?php echo $field_id ?>', '<?php echo $dir ?>');" style="cursor:pointer;"><?php
								$label = apply_filters('kws_gf_directory_th', apply_filters('kws_gf_directory_th_address', 'Address'));
								echo esc_html($label)

								 ?></th>
								<?php
							}
							?>
						</tr>
						<?php if(!empty($credit)) { self::get_credit_link(sizeof($columns), $options); } ?>
					</tfoot>
					<?php } ?>
				</table>
					<?php

						do_action('kws_gf_after_directory_before_nav', do_action('kws_gf_after_directory_before_nav_form_'.$form_id, $form, $leads, compact("approved","sort_field","sort_direction","search_query","first_item_index","page_size","star","read","is_numeric","start_date","end_date")));


					//Displaying paging links if appropriate

					if($lead_count > 0 && $showcount || $page_links){
						if($lead_count == 0) { $first_item_index--; }
						?>
					<div class="tablenav">
						<div class="tablenav-pages">
							<?php if($showcount) {
							if(($first_item_index + $page_size) > $lead_count || $page_size <= 0) {
								$second_part = $lead_count;
							} else {
								$second_part = $first_item_index + $page_size;
							}
							?>
							<span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravity-forms-addons"), $first_item_index + 1, $second_part, $lead_count)  ?></span>
							<?php } if($page_links){ echo $page_links; } ?>
						</div>
						<div class="clear"></div>
					</div>
						<?php
				   }

				  ?>
			</div>
			<?php
			if(empty($credit)) {
				echo "\n".'<!-- Directory generated by Gravity Forms Directory & Addons : http://wordpress.org/extend/plugins/gravity-forms-addons/ -->'."\n";
			}

			do_action('kws_gf_after_directory', do_action('kws_gf_after_directory_form_'.$form_id, $form, $leads, compact("approved","sort_field","sort_direction","search_query","first_item_index","page_size","star","read","is_numeric","start_date","end_date")));

			$content = ob_get_contents(); // Get the output
			ob_end_clean(); // Clear the cache

			// If the form is form #2, two filters are applied: `kws_gf_directory_output_2` and `kws_gf_directory_output`
			$content = apply_filters('kws_gf_directory_output', apply_filters('kws_gf_directory_output_'.$form_id, self::html_display_type_filter($content, $directoryview)));

			return $content; // Return it!
	}

	/**
	 * Generate and show the drop-down filters.
	 * @param  array $leads Entries as retrieved by GFDirectory::get_leads()
	 * @return string $output
	 */
	private function get_filters($leads) {

        $form_id = $leads[0]['form_id'];

        if(empty($leads) || !is_array($leads)) { return ''; }
        $filters = array();
		foreach($leads as $lead) {
			foreach($lead as $key => $value) {
				if(!empty($value) && (!isset($filters[$key]) || (isset($filters[$key]) && (!is_array($filters[$key]) || !in_array($value, $filters[$key]))))) {
					$filters[$key][] = $value;
				}
			}
		}

		$output = '<form method="get">';
		foreach($filters as $key => $filter) {
			$output .= '<select name="filter['.$key.']">';
				$output .= '<option value="">Select a '.$key.'</option>';
				foreach($filter as $value) {
					$output .= '<option value="'.$key.'|'.$value.'">'.$value.'</option>';
				}
			$output .= '</select>';
		}
		$output .= '
            <input type="hidden" name="gf_search" />
            <input type="submit" value="Search" />
        </form>';
        #echo $output;
		return $output;
#		echo $output;
#		echo '<pre>'; print_r($filters, false).'</pre>';

#		echo '<h3>Form ID #'.$form_id.'</h3>';
#		echo '<pre>'; print_r($filters, false).'</pre>';
#		echo '<pre>'; print_r($leads, false).'</pre>';
	}

    public function get_credit_link($columns = 1, $options = array()) {
    	global $post;// prevents calling before <HTML>
    	if(empty($post) || is_admin()) { return; }

    	$settings = self::get_settings();

    	// Only show credit link if the user has saved settings;
    	// this prevents existing directories adding a link without user action.
    	if(isset($settings['version'])) {
    		echo "<tr><th colspan='{$columns}'>".self::attr($options)."</th></tr>";
    	}
    }

    public function get_version() {
	    return self::$version;
    }

    public function return_7776000() {
	    return 7776000; // extend the cache to 90 days
    }

    public function attr($options, $default = '<span class="kws_gf_credit" style="font-weight:normal; text-align:center; display:block; margin:0 auto;">Powered by <a href="http://seodenver.com/gravity-forms-addons/">Gravity Forms Directory</a></span>') {
		include_once(ABSPATH . WPINC . '/feed.php');
		add_filter( 'wp_feed_cache_transient_lifetime' , array('GFDirectory', 'return_7776000'));
		$rss = fetch_feed(add_query_arg(array('site' => htmlentities(substr(get_bloginfo('url'), is_ssl() ? 8 : 7)), 'from' => 'kws_gf_addons', 'version' => self::$version, 'credit' => !empty($options['credit'])), 'http://www.katzwebservices.com/development/attribution.php'));
		remove_filter( 'wp_feed_cache_transient_lifetime' , array('GFDirectory', 'return_7776000'));
		if($rss && !is_wp_error($rss)) {
			// We want to strip all tags except for 'style', 'id', and 'class' so that the return value is always safe for the site.
			$strip = array('bgsound','expr','onclick','onerror','onfinish','onmouseover','onmouseout','onfocus','onblur','lowsrc','dynsrc');
			$rss->strip_attributes($strip); $rss_items = $rss->get_items(0, 1);
			foreach ( $rss_items as $item ) {
				return str_replace(array("\n", "\r"), ' ', $item->get_description());
			}
		}

		return $default;
	}


	public function add_lead_approved_hidden_input($value, $lead, $field = '') {
		global $_gform_directory_processed_meta, $_gform_directory_approvedcolumn;

		if(!in_array($lead['id'], $_gform_directory_processed_meta)) {
			$_gform_directory_processed_meta[] = $lead['id'];
			if(empty($_gform_directory_approvedcolumn)) {
				$forms = RGFormsModel::get_forms(null, "title");
	        	$_gform_directory_approvedcolumn = self::globals_get_approved_column($forms[0]->id);
			}
			if(self::check_approval($lead, $_gform_directory_approvedcolumn)) {
				echo '<td style="display:none;"><input type="hidden" class="lead_approved" id="lead_approved_'.$lead['id'].'" value="true" /></td>';
			}
		}

		return $value;
	}


    public function globals_get_approved_column($formID = 0) {
	    global $_gform_directory_processed_meta, $_gform_directory_approvedcolumn, $_gform_directory_activeform;

	        $_gform_directory_processed_meta = array();

	        if(empty($formID)) {
		        $formID = RGForms::get("id");

		        if(empty($formID)) {
			        $forms = RGFormsModel::get_forms(null, "title");
		            $formID = $forms[0]->id;
		        }
		    }

	        if(!empty($formID)) {
	        	$_gform_directory_activeform = RGFormsModel::get_form_meta($formID);
	        } else if(isset($_GET['id'])) {
	        	$_gform_directory_activeform = RGFormsModel::get_form_meta($_GET['id']);
	        }

	        $_gform_directory_approvedcolumn = self::get_approved_column($_gform_directory_activeform);

	        return $_gform_directory_approvedcolumn;
	}

	public function get_approved_column($form) {
		if(!is_array($form)) { return false; }

		foreach(@$form['fields'] as $key=>$col) {
			if(isset($col['inputs']) && is_array($col['inputs'])) {
				foreach($col['inputs'] as $key2=>$input) {
					if(strtolower($input['label']) == 'approved' && $col['type'] == 'checkbox' && !empty($col['adminOnly'])) {
						return $input['id'];
					}
				}
			}
		}

		foreach(@$form['fields'] as $key=>$col) {
			if(isset($col['label']) && strtolower($col['label']) == 'approved' && $col['type'] == 'checkbox') {
				if(isset($col['inputs'][0]['id']))
				return $key;
			}
		}

		return null;
	}


    public function process_bulk_update() {
		global $process_bulk_update_message;

        if(RGForms::post("action") === 'bulk'){
            check_admin_referer('gforms_entry_list', 'gforms_entry_list');

            $bulk_action = !empty($_POST["bulk_action"]) ? $_POST["bulk_action"] : $_POST["bulk_action2"];
            $leads = $_POST["lead"];

            $entry_count = count($leads) > 1 ? sprintf(__("%d entries", "gravityforms"), count($leads)) : __("1 entry", "gravityforms");

			$bulk_action = explode('-', $bulk_action);
			if(!isset($bulk_action[1]) || empty($leads)) { return false; }

            switch($bulk_action[0]){
                case "approve":
                    self::directory_update_bulk($leads, 1, $bulk_action[1]);
                    $process_bulk_update_message = sprintf(__("%s approved.", "gravity-forms-addons"), $entry_count);
                break;

                case "unapprove":
            		self::directory_update_bulk($leads, 0, $bulk_action[1]);
                    $process_bulk_update_message = sprintf(__("%s disapproved.", "gravity-forms-addons"), $entry_count);
                break;
			}
		}
	}

    private function directory_update_bulk($leads, $approved, $form_id) {
    	global $_gform_directory_approvedcolumn;

    	if(empty($leads) || !is_array($leads)) { return false; }

    	$_gform_directory_approvedcolumn = empty($_gform_directory_approvedcolumn) ? self::globals_get_approved_column($_POST['form_id']) : $_gform_directory_approvedcolumn;

		$approved = empty($approved) ? 0 : 'Approved';
    	foreach($leads as $lead_id) {
			self::directory_update_approved($lead_id, $approved, $form_id);
		}
    }

    public function directory_update_approved_hook(){
    	global $_gform_directory_approvedcolumn;
		check_ajax_referer('rg_update_approved','rg_update_approved');
		if(!empty($_POST["lead_id"])) {
			$_gform_directory_approvedcolumn = empty($_gform_directory_approvedcolumn) ? self::globals_get_approved_column($_POST['form_id']) : $_gform_directory_approvedcolumn;
		    self::directory_update_approved((int)$_POST["lead_id"], $_POST["approved"], (int)$_POST['form_id'], $_gform_directory_approvedcolumn);
		}
	}

    public function settings_link( $links, $file ) {
        static $this_plugin;
        if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
        if ( $file == $this_plugin ) {
            $settings_link = '<a href="' . admin_url( 'admin.php?page=gf_settings&addon=Directory+%26+Addons' ) . '">' . __('Settings', 'gravity-forms-addons') . '</a>';
            array_unshift( $links, $settings_link ); // before other links
        }
        return $links;
    }

    //Returns true if the current page is an Feed pages. Returns false if not
    private static function is_directory_page(){
    	if(empty($_GET["pagenum"])) { return false; }
        $current_page = trim(strtolower($_GET["pagenum"]));
        $directory_pages = array("gf_directory");

        return in_array($current_page, $directory_pages);
    }

    public function get_settings() {
		return get_option("gf_addons_settings", array(
        		"directory" => true,
        		"directory_defaults" => array(),
        		"referrer" => false,
        		"modify_admin" => array(
            		'expand' => true,
           			'toggle' => true,
            		'edit' => true,
            		'ids' => true
            	),
            	"saved" => false,
            	"version" => self::$version
        	)
        );
	}

    public static function disable_directory(){
        delete_option("gf_directory_oid");
    }

    public static function uninstall(){

        if(!GFDirectory::has_access("gravityforms_directory_uninstall"))
            (__("You don't have adequate permission to uninstall Directory Add-On.", "gravity-forms-addons"));

        //removing options
        delete_option("gf_addons_settings");

        //Deactivating plugin
        $plugin = "gravity-forms-addons/gravity-forms-addons.php";
        deactivate_plugins($plugin);
        update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
    }

    private static function is_gravityforms_supported(){
        if(class_exists("GFCommon")){
            $is_correct_version = version_compare(GFCommon::$version, self::$min_gravityforms_version, ">=");
            return $is_correct_version;
        }
        else{
            return false;
        }
    }

	protected static function get_has_access($required_permission) {
		$has_members_plugin = function_exists('members_get_capabilities');
        $has_access = $has_members_plugin ? current_user_can($required_permission) : current_user_can("level_7");
        if($has_access)
            return $has_members_plugin ? $required_permission : "level_7";
        else
            return false;
	}

    public static function has_access($required_permission){
        return self::get_has_access($required_permission);
    }

    //Returns the url of the plugin's root folder
    public function get_base_url(){
        return plugins_url(null, __FILE__);
    }

	public static function get_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null, $status='active', $approvedcolumn = null, $limituser = false) {
        global $wpdb;

        if($sort_field_number == 0)
            $sort_field_number = "date_created";

        // Retreive the leads based on whether it's sorted or not.
        if(is_numeric($sort_field_number))
            $sql = self::sort_by_custom_field_query($form_id, $sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $status, $approvedcolumn, $limituser);
        else
            $sql = self::sort_by_default_field_query($form_id, $sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date, $status, $approvedcolumn, $limituser);

		//initializing rownum
        $wpdb->query("select @rownum:=0");

        //getting results

        $results = $wpdb->get_results($sql);

        $return = '';
		if(function_exists('gform_get_meta')) {
			$return = RGFormsModel::build_lead_array($results); // This is a private function until 1.6
		}

		// Used by at least the show_only_user_entries() method
		$return = apply_filters('kws_gf_directory_lead_filter', $return, compact("approved","sort_field","sort_direction","search_query","first_item_index","page_size","star","read","is_numeric","start_date","end_date","status", "approvedcolumn", "limituser"));

        return $return;
    }

    function is_current_user( $lead = array()) {
		global $current_user;
		get_currentuserinfo();
		return ( (int)$current_user->ID === (int)$lead["created_by"]) ;
	}

	function show_only_user_entries($leads = array(), $settings = array()) {
		if(empty($settings['limituser'])) { return $leads; }
		return array_filter($leads, array('GFDirectory', 'is_current_user'));
	}

	/**
	 * A copy of the Gravity Forms method, but adding $approvedcolumns and $limituser args
	 */
    private static function sort_by_custom_field_query($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false, $status='active', $approvedcolumn = null, $limituser = false){
        global $wpdb, $current_user;
        if(!is_numeric($form_id) || !is_numeric($sort_field_number)|| !is_numeric($offset)|| !is_numeric($page_size))
            return "";

        $lead_detail_table_name = RGFormsModel::get_lead_details_table_name();
        $lead_table_name = RGFormsModel::get_lead_table_name();

        $orderby = $is_numeric_sort ? "ORDER BY query, (value+0) $sort_direction" : "ORDER BY query, value $sort_direction";

        //$search = empty($search) ? "" : "WHERE d.value LIKE '%$search%' ";
        $search_term = "%$search%";
        $search_filter = empty($search) ? "" : $wpdb->prepare("WHERE d.value LIKE %s", $search_term);

        //starred clause
        $where = empty($search) ? "WHERE" : "AND";
        $search_filter .= $star !== null && $status == 'active' ? $wpdb->prepare("$where is_starred=%d AND status='active' ", $star) : "";

        //read clause
        $where = empty($search) ? "WHERE" : "AND";
        $search_filter .= $read !== null && $status == 'active' ? $wpdb->prepare("$where is_read=%d AND status='active' ", $read) : "";

		//status clause
        if(function_exists('gform_get_meta')) {
        	$where = empty($search) ? "WHERE" : "AND";
	        $search_filter .= $wpdb->prepare("$where status=%s ", $status);
	    }

		if($limituser) {
			get_currentuserinfo();
			if((int)$current_user->ID !== 0 || ($current_user->ID === 0 && apply_filters('kws_gf_show_entries_if_not_logged_in', apply_filters('kws_gf_treat_not_logged_in_as_user', true)))) {
				$where = empty($search_filter) ? "WHERE" : "AND";
	        	if((int)$current_user->ID === 0) {
	        		$search_filter .= $wpdb->prepare("$where (created_by IS NULL OR created_by=%d)", $current_user->ID);
	        	} else {
	        		$search_filter .= $wpdb->prepare("$where l.created_by=%d ", $current_user->ID);
	        	}
			} else {
				return false;
			}
		}

        $field_number_min = $sort_field_number - 0.001;
        $field_number_max = $sort_field_number + 0.001;

        $in_filter = "";
		if(!empty($approvedcolumn)) {
			$in_filter = $wpdb->prepare("WHERE l.id IN (SELECT lead_id from $lead_detail_table_name WHERE field_number BETWEEN %f AND %f)", $approvedcolumn - 0.001, $approvedcolumn + 0.001);
			// This will work once all the fields are converted to the meta_key after 1.6
			#$search_filter .= $wpdb->prepare(" AND m.meta_key = 'is_approved' AND m.meta_value = %s", 1);
		}

		$limit_filter = '';
		if($page_size > 0) { $limit_filter = "LIMIT $offset,$page_size"; }

        $sql = "
            SELECT filtered.sort, l.*, d.field_number, d.value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
            INNER JOIN (
                SELECT distinct sorted.sort, l.id
                FROM $lead_table_name l
                INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
                INNER JOIN (
                    SELECT @rownum:=@rownum+1 as sort, id FROM (
                        SELECT 0 as query, lead_id as id, value
                        FROM $lead_detail_table_name
                        WHERE form_id=$form_id
                        AND field_number between $field_number_min AND $field_number_max

                        UNION ALL

                        SELECT 1 as query, l.id, d.value
                        FROM $lead_table_name l
                        LEFT OUTER JOIN $lead_detail_table_name d ON d.lead_id = l.id AND field_number between $field_number_min AND $field_number_max
                        WHERE l.form_id=$form_id
                        AND d.lead_id IS NULL

                    ) sorted1
                   $orderby
                ) sorted ON d.lead_id = sorted.id
                $search_filter
                $limit_filter
            ) filtered ON filtered.id = l.id
            $in_filter
            ORDER BY filtered.sort";

        return $sql;
    }

    /**
	 * A copy of the Gravity Forms method, but adding $approvedcolumns and $limituser args
	 */
    private static function sort_by_default_field_query($form_id, $sort_field, $sort_direction='DESC', $search='', $offset=0, $page_size=30, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null, $status='active', $approvedcolumn = null, $limituser = false){
        global $wpdb, $current_user;

		if(!is_numeric($form_id) || !is_numeric($offset)|| !is_numeric($page_size)){
            return "";
        }

        $lead_detail_table_name = RGFormsModel::get_lead_details_table_name();
        $lead_table_name = RGFormsModel::get_lead_table_name();

        $search_term = "%$search%";
        $search_filter = empty($search) ? "" : $wpdb->prepare(" AND value LIKE %s", $search_term);

        $star_filter = $star !== null && $status == 'active' ? $wpdb->prepare(" AND is_starred=%d AND status='active' ", $star) : "";
        $read_filter = $read !== null && $status == 'active' ? $wpdb->prepare(" AND is_read=%d AND status='active' ", $read) :  "";
        if(function_exists('gform_get_meta')) {
	        $status_filter = $wpdb->prepare(" AND status=%s ", $status);
	    } else {
	    	$status_filter = '';
	    }

        $start_date_filter = empty($start_date) ? "" : " AND datediff(date_created, '$start_date') >=0";
        $end_date_filter = empty($end_date) ? "" : " AND datediff(date_created, '$end_date') <=0";

		$in_filter = "";
		if(!empty($approvedcolumn)) {
			$in_filter = $wpdb->prepare("l.id IN (SELECT lead_id from $lead_detail_table_name WHERE field_number BETWEEN %f AND %f) AND", $approvedcolumn - 0.001, $approvedcolumn + 0.001);
			// This will work once all the fields are converted to the meta_key after 1.6
			#$search_filter .= $wpdb->prepare(" AND m.meta_key = 'is_approved' AND m.meta_value = %s", 1);
		}

		$user_filter = '';
		if($limituser) {
			get_currentuserinfo();
			if((int)$current_user->ID !== 0 || ($current_user->ID === 0 && apply_filters('kws_gf_show_entries_if_not_logged_in', apply_filters('kws_gf_treat_not_logged_in_as_user', true)))) {
	        	if((int)$current_user->ID === 0) {
	        		$user_filter = $wpdb->prepare(" AND (created_by IS NULL OR created_by=%d)", $current_user->ID);
	        	} else {
	        		$user_filter = $wpdb->prepare(" AND created_by=%d ", $current_user->ID);
	        	}
			} else {
				return false;
			}
		}

		$limit_filter = '';
		if($page_size > 0) { $limit_filter = "LIMIT $offset,$page_size"; }

        $sql = "
            SELECT filtered.sort, l.*, d.field_number, d.value
            FROM $lead_table_name l
            INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
            INNER JOIN
            (
                SELECT @rownum:=@rownum + 1 as sort, id
                FROM
                (
                    SELECT distinct l.id
                    FROM $lead_table_name l
                    INNER JOIN $lead_detail_table_name d ON d.lead_id = l.id
                    WHERE $in_filter
                    l.form_id=$form_id
                    $search_filter
                    $star_filter
                    $read_filter
                    $user_filter
                    $status_filter
                    $start_date_filter
                    $end_date_filter
                    ORDER BY $sort_field $sort_direction
                    $limit_filter
                ) page
            ) filtered ON filtered.id = l.id
            ORDER BY filtered.sort";

        return $sql;
    }

    function directory_anchor_text($value = null) {

		if(apply_filters('kws_gf_directory_anchor_text_striphttp', true)) {
			$value = str_replace('http://', '', $value);
			$value = str_replace('https://', '', $value);
		}

		if(apply_filters('kws_gf_directory_anchor_text_stripwww', true)) {
			$value = str_replace('www.', '', $value);
		}
		if(apply_filters('kws_gf_directory_anchor_text_rootonly', true)) {
			$value = preg_replace('/(.*?)\/(.+)/ism', '$1', $value);
		}
		if(apply_filters('kws_gf_directory_anchor_text_nosubdomain', true)) {
			$value = preg_replace('/((.*?)\.)+(.*?)\.(.*?)/ism', '$3.$4', $value);
		}
		if(apply_filters('kws_gf_directory_anchor_text_noquerystring', true)) {
			$ary = explode("?", $value);
			$value = $ary[0];
		}
		return $value;
	}

    public function r($content, $die = false) {
        echo '<pre>'.print_r($content, true).'</pre>';
        if($die) { die(); }
    }

	private function get_entrylink_column($form, $entry = false) {
		if(!is_array($form)) { return false; }

		$columns = empty($entry) ? array() : array('id' => 'id');
        foreach(@$form['fields'] as $key=>$col) {
			if(!empty($col['useAsEntryLink'])) {
				$columns[$col['id']] = $col['useAsEntryLink'];
			}
		}

        return empty($columns) ? false : $columns;
	}

	private function prep_address_field($field) {
		return !empty($field) ? trim($field) : '';
	}

	function format_address($address = array(), $linknewwindow = false) {
        $address_field_id = @self::prep_address_field($address['id']);
        $street_value = @self::prep_address_field($address[$address_field_id . ".1"]);
		$street2_value = @self::prep_address_field($address[$address_field_id . ".2"]);
		$city_value = @self::prep_address_field($address[$address_field_id . ".3"]);
		$state_value = @self::prep_address_field($address[$address_field_id . ".4"]);
		$zip_value = @self::prep_address_field($address[$address_field_id . ".5"]);
		$country_value = @self::prep_address_field($address[$address_field_id . ".6"]);

		$address = $street_value;
		$address .= !empty($address) && !empty($street2_value) ? "<br />$street2_value" : $street2_value;
		$address .= !empty($address) && (!empty($city_value) || !empty($state_value)) ? "<br />$city_value" : $city_value;
		$address .= !empty($address) && !empty($city_value) && !empty($state_value) ? ", $state_value" : $state_value;
		$address .= !empty($address) && !empty($zip_value) ? " $zip_value" : $zip_value;
		$address .= !empty($address) && !empty($country_value) ? "<br />$country_value" : $country_value;

		//adding map link
		if(!empty($address) && apply_filters('kws_gf_directory_td_address_map', 1)) {
			$address_qs = str_replace("<br />", " ", $address); //replacing <br/> with spaces
			$address_qs = urlencode($address_qs);
			$target = ''; if($linknewwindow) { $target = ' target="_blank"'; }
			$address .= "<br/>".apply_filters('kws_gf_directory_map_link', "<a href='http://maps.google.com/maps?q=$address_qs'".$target." class='map-it-link'>".__('Map It')."</a>");
		}
		return $address;
	}

	public function html_display_type_filter($content = null, $type = 'table', $single = false) {
		switch($type) {
			case 'table':
				return $content;
				break;
			case 'ul':
				$content = self::convert_to_ul($content, $single);
				break;
			case 'dl':
				$content = self::convert_to_dl($content, $single);
				break;
		}
		return $content;
	}

	public function convert_to_ul($content = null, $singleUL = false) {

		$strongHeader = apply_filters('kws_gf_convert_to_ul_strong_header', 1);

		// Directory View
		if(!$singleUL) {
			$content = preg_replace("/<table([^>]*)>/ism","<ul$1>", $content);
			$content = preg_replace("/<\/table([^>]*)>/ism","</ul>", $content);
			if($strongHeader) {
				$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><ul>", $content);
				$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
			} else {
				$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content);
				$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
			}
			$content = preg_replace("/<\/tr[^>]*>/","\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content);
		}
		// Single listing view
		else {
			$content = preg_replace("/<table([^>]*)>/ism","<ul$1>", $content);
			$content = preg_replace("/<\/table([^>]*)>/ism","</ul>", $content);
			if($strongHeader) {
				$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1><strong>", $content);
				$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2</strong>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
			} else {
				$content = preg_replace("/<tr([^>]*)>\s+/","\n\t\t\t\t\t\t\t\t\t\t\t\t<li$1>", $content);
				$content = preg_replace("/<th([^>]*)\>(.*?)\<\/th\>/","$2\n\t\t\t\t\t\t\t\t\t\t\t\t\t<ul>", $content);
			}
			$content = preg_replace("/<\/tr[^>]*>/","\t\t\t\t\t</ul>\n\t\t\t\t\t\t\t\t\t\t\t\t</li>", $content);
		}
	#	$content = preg_replace("/\<\/p\>\s+\<\/li/ism","\<\/p\>\<\/li", $content);
		$content = preg_replace("/(?:\s+)?(valign\=\"(?:.*?)\"|width\=\"(?:.*?)\"|cellspacing\=\"(?:.*?)\")(?:\s+)?/ism", ' ', $content);
		$content = preg_replace("/<\/?tbody[^>]*>/","", $content);
		$content = preg_replace("/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is","", $content);
		$content = preg_replace("/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/","", $content);
		$content = preg_replace("/\<td([^>]*)\>/","\t\t\t\t\t<li$1>", $content);
		$content = preg_replace("/<\/td[^>]*>/","</li>", $content);
		$content = preg_replace('/\s?colspan\="([^>]*?)"\s?/ism', ' ', $content);
		return $content;
	}

	public function convert_to_dl($content, $singleDL = false) {
		$back = '';
		// Get the back link, if it exists
		preg_match("/\<p\sclass=\"entryback\"\>(.*?)\<\/p\>/", $content, $matches);
		if(isset($matches[0])) { $back = $matches[0]; }
		$content = preg_replace("/\<p\sclass=\"entryback\"\>(.*?)\<\/p\>/", "", $content);
		$content = preg_replace("/<\/?table[^>]*>|<\/?tbody[^>]*>/","", $content);
		$content = preg_replace("/<thead[^>]*>.*<\/thead>|<tfoot[^>]*>.*<\/tfoot>/is","", $content);
		if(!$singleDL) {
			$content = preg_replace("/<tr([^>]*)>/","<dl$1>", $content);
			$content = preg_replace("/<\/tr[^>]*>/","</dl>", $content);
		} else {
			$content = preg_replace("/<tr([^>]*)>/","", $content);
			$content = preg_replace("/<\/tr[^>]*>/","", $content);
		}
		$content = preg_replace("/\<td([^>]*)\>(\&nbsp;|)\<\/td\>/","", $content);
		$content = preg_replace("/\<th([^>]*)\>(.*?)<\/th\>/ism","<dt$1>$2</dt>", $content);
		$content = preg_replace('/<td(.*?)(title="(.*?)")?>(.*?)<\/td[^>]*>/ism',"<dt$1>$3</dt><dd>$4</dd>", $content);
		$output = $back;
		$output .= "\n\t\t\t\t\t\t\t\t".'<dl>';
		$output .= $content;
		$output .= "\t\t\t\t\t\t".'</dl>';
		return $output;
	}

	public function make_entry_link($options = array(), $link = false, $lead_id = '', $form_id = '', $field_id = '', $field_label = '', $linkClass = '') {
		global $wp_rewrite,$post,$wp;
		extract($options);
		$entrylink = (empty($link) || $link === '&nbsp;') ? $field_label : $link; //$entrylink;

		$entrytitle = apply_filters('kws_gf_directory_detail_title', apply_filters('kws_gf_directory_detail_title_'.$lead_id, $entrytitle));

		if(!empty($lightboxsettings['entry'])) {
			$href = plugins_url( "/entry-details.php?leadid=$lead_id&amp;form={$form_id}&amp;post={$post->ID}", __FILE__);

			if(wp_script_is('colorbox', 'registered')) {
				$linkClass = ' class="colorbox lightbox" rel="directory_all directory_entry"';
			} else if(wp_script_is('thickbox', 'registered')) {
				$linkClass = ' class="thickbox lightbox" rel="directory_all directory_entry"';
			}
		} else {
			$multisite = (function_exists('is_multisite') && is_multisite() && $wpdb->blogid == 1);
			if($wp_rewrite->using_permalinks()) {
				// example.com/example-directory/entry/4/14/
				if(isset($post->ID)) {
					$url = get_permalink($post->ID);
				} else {
					$url = parse_url(add_query_arg(array()));
					$url = $url['path'];
				}
				$href = trailingslashit($url).sanitize_title(apply_filters('kws_gf_directory_endpoint', 'entry')).'/'.$form_id.apply_filters('kws_gf_directory_endpoint_separator', '/').$lead_id.'/';
				#if(!empty($url['query'])) { $href .= '?'.$url['query']; }
				$href = add_query_arg(array('gf_search' => !empty($_REQUEST['gf_search']) ? $_REQUEST['gf_search'] : null, 'sort' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null, 'dir' => isset($_REQUEST['dir']) ? $_REQUEST['dir'] : null, 'pagenum' => isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : null, 'start_date' => isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : null, 'end_date' => isset($_REQUEST['start_date']) ? $_REQUEST['end_date'] : null), $href);
			} else {
				// example.com/?page_id=24&leadid=14&form=4
				$href = add_query_arg(array('leadid'=>$lead_id, 'form' => $form_id));
			}
		}

		$value = '<a href="'.$href.'"'.$linkClass.' title="'.$entrytitle.'">'.$entrylink.'</a>';
		return $value;
	}

	function get_icon_url($path){
		$info = pathinfo($path);

		switch(strtolower($info["extension"])){

			case "css" :
				$file_name = "icon_css.gif";
			break;

			case "doc" :
				$file_name = "icon_doc.gif";
			break;

			case "fla" :
				$file_name = "icon_fla.gif";
			break;

			case "html" :
			case "htm" :
			case "shtml" :
				$file_name = "icon_html.gif";
			break;

			case "js" :
				$file_name = "icon_js.gif";
			break;

			case "log" :
				$file_name = "icon_log.gif";
			break;

			case "mov" :
				$file_name = "icon_mov.gif";
			break;

			case "pdf" :
				$file_name = "icon_pdf.gif";
			break;

			case "php" :
				$file_name = "icon_php.gif";
			break;

			case "ppt" :
				$file_name = "icon_ppt.gif";
			break;

			case "psd" :
				$file_name = "icon_psd.gif";
			break;

			case "sql" :
				$file_name = "icon_sql.gif";
			break;

			case "swf" :
				$file_name = "icon_swf.gif";
			break;

			case "txt" :
				$file_name = "icon_txt.gif";
			break;

			case "xls" :
				$file_name = "icon_xls.gif";
			break;

			case "xml" :
				$file_name = "icon_xml.gif";
			break;

			case "zip" :
				$file_name = "icon_zip.gif";
			break;

			case "gif" :
			case "jpg" :
			case "jpeg":
			case "png" :
			case "bmp" :
			case "tif" :
			case "eps" :
				$file_name = "icon_image.gif";
			break;

			case "mp3" :
			case "wav" :
			case "wma" :
				$file_name = "icon_audio.gif";
			break;

			case "mp4" :
			case "avi" :
			case "wmv" :
			case "flv" :
				$file_name = "icon_video.gif";
			break;

			default:
				$file_name = "icon_generic.gif";
			break;
		}

		return GFCommon::get_base_url() . "/images/doctypes/$file_name";
	}

  	function get_lead_count($form_id, $search, $star=null, $read=null, $column, $approved = false, $leads = array(), $start_date = null, $end_date = null, $limituser = false){
		global $wpdb, $current_user;

		if(!is_numeric($form_id))
			return "";

		$detail_table_name = RGFormsModel::get_lead_details_table_name();
		$lead_table_name = RGFormsModel::get_lead_table_name();

		$star_filter = $star !== null ? $wpdb->prepare("AND is_starred=%d ", $star) : "";
		$read_filter = $read !== null ? $wpdb->prepare("AND is_read=%d ", $read) : "";
		if(function_exists('gform_get_meta')) {
	        $status_filter = $wpdb->prepare(" AND status=%s ", 'active');
	    } else {
	    	$status_filter = '';
	    }
		$start_date_filter = empty($start_date) ? "" : " AND datediff(date_created, '$start_date') >=0";
		$end_date_filter = empty($end_date) ? "" : " AND datediff(date_created, '$end_date') <=0";

		$search_term = "%$search%";
		$search_filter = empty($search) ? "" : $wpdb->prepare("AND ld.value LIKE %s", $search_term);

		$user_filter = '';
		if($limituser) {
			get_currentuserinfo();
			if((int)$current_user->ID !== 0 || ($current_user->ID === 0 && apply_filters('kws_gf_show_entries_if_not_logged_in', apply_filters('kws_gf_treat_not_logged_in_as_user', true)))) {
				if(!empty($current_user->ID)) {
	        		$user_filter = $wpdb->prepare(" AND l.created_by=%d ", $current_user->ID);
	        	} else {
	        		$user_filter = $wpdb->prepare(" AND (created_by IS NULL OR created_by=%d)", $current_user->ID);
	        	}
			} else {
				return false;
			}

		}

		$in_filter = "";
		if($approved) {
			$in_filter = $wpdb->prepare("l.id IN (SELECT lead_id from $detail_table_name WHERE field_number BETWEEN %f AND %f) AND", $column - 0.001, $column + 0.001);
			// This will work once all the fields are converted to the meta_key after 1.6
			#$search_filter .= $wpdb->prepare(" AND m.meta_key = 'is_approved' AND m.meta_value = %s", 1);
		}

		$sql = "SELECT count(distinct l.id) FROM $lead_table_name as l,
				$detail_table_name as ld";
#		$sql .= function_exists('gform_get_meta') ? " INNER JOIN wp_rg_lead_meta m ON l.id = m.lead_id " : ""; // After 1.6
		$sql .= "
				WHERE $in_filter
				l.form_id=$form_id
				AND ld.form_id=$form_id
				AND l.id = ld.lead_id
				$star_filter
				$read_filter
				$status_filter
				$user_filter
				$start_date_filter
				$end_date_filter
				$search_filter";

		return $wpdb->get_var($sql);
	}

	function check_meta_approval($lead_id) {
        return gform_get_meta($lead_id, 'is_approved');
	}

	function check_approval($lead, $column) {
		return self::check_meta_approval($lead['id']);
	}

	function hide_in_directory($form, $field_id) {
		return self::check_hide_in('hideInDirectory', $form, $field_id);
	}

	function hide_in_single($form, $field_id) {
		return self::check_hide_in('hideInSingle', $form, $field_id);
	}

	function check_hide_in($type, $form, $field_id) {
		foreach($form['fields'] as $field) {
#			echo $field['label'] . ' / ' . floor($field['id']).' / '.floor($field_id).' / <strong>'.$field["{$type}"].'</strong><br />';
			if(floor($field_id) === floor($field['id']) && !empty($field["{$type}"])) {
				return true;
			}
		}

		return false;
	}

	function remove_approved_column($type = 'form', $fields, $approvedcolumn) {

		foreach($fields as $key => $column) {
			if((int)floor($column['id']) === (int)floor($approvedcolumn)) {
				unset($fields["{$key}"]);
			}
		}

		return $fields;
	}

	function remove_admin_only($leads, $adminOnly, $approved, $isleads, $single = false, $form) {

		if(empty($adminOnly) || !is_array($adminOnly)) { $adminOnly = array(); }

		if(!is_array($leads)) { return $leads; }

		$i = 0;
		if($isleads) {
            if(empty($leads) || !is_array($leads)) { return array(); }
			foreach($leads as $key => $lead) {
				if(@in_array($key, $adminOnly) && $key != $approved && $key != floor($approved)) {
					if($single) {
						foreach($adminOnly as $ao) {
							unset($lead[$ao]);
						}
					} else {
						unset($leads[$i]);
					}
				}
			}
			return $leads;
		} else {
			$columns = $leads;
			foreach($columns as $key => $column) {
				// Not sure why this was coded like this. Doesn't seem to make much sense now.
				// if(@in_array($key, $adminOnly) && $key != $approved && $key != floor($approved) && !$single || ($single && (!isset($column['id']) || isset($column['id']) && in_array($column['id'], $adminOnly)))) {
				if(
					@in_array($key, $adminOnly) && $key != $approved ||
					($single && self::hide_in_single($form, $key)) ||
					(!$single && self::hide_in_directory($form, $key))
				) {
					if($single) {
						unset($columns[floor($key)]);
					} else {
						unset($columns[$key]);
					}
				}
			}

			return $columns;
		}
	}
}


function kws_gf_load_functions() {

	// If Gravity Forms is installed and exists
	if(defined('RG_CURRENT_PAGE')) {

		function gf_field_value($leadid, $fieldid, $form = array()) {
			echo get_gf_field_value($leadid, $fieldid, $form);
		}


		// To retrieve textarea inputs from a lead
		// Example: get_gf_field_value_long(22, '14');
		function get_gf_field_value_long($leadid, $fieldid, $form = array(), $apply_filter=true) {
			return RGFormsModel::get_field_value_long($leadid, $fieldid, $form, $apply_filter);
		}

		// To retrieve textarea inputs from a lead
		// Example: get_gf_field_value_long(22, '14');
		function get_gf_field_value($leadid, $fieldid, $form = array()) {
			$lead = RGFormsModel::get_lead($leadid);
			$fieldid = floatval($fieldid);
			if(is_numeric($fieldid)) {
				$result = $lead["$fieldid"];
			}

			$max_length = GFORMS_MAX_FIELD_LENGTH;

			if(strlen($result) >= ($max_length - 50)) {
				$result = get_gf_field_value_long($lead["id"], $fieldid, $form);
	        }
	        $result = trim($result);

	        if(!empty($result)) { return $result; }
			return false;
		}

		function gf_field_value_long($leadid, $fieldid, $form = array()) {
			echo get_gf_field_value_long($leadid, $fieldid, $form);
		}


		// Gives you the label for a form input (such as First Name). Enter in the form and the field ID to access the label.
		// Example: echo get_gf_field_label(1,1.3);
		// Gives you the label for a form input (such as First Name). Enter in the form and the field ID to access the label.
		// Example: echo get_gf_field_label(1,1.3);
		function get_gf_field_label($form_id, $field_id) {
			$form = RGFormsModel::get_form_meta($form_id);
			foreach($form["fields"] as $field){
				if($field['id'] == $field_id) {
					# $output = RGForms::escape_text($field['label']); // No longer used
					$output = esc_html($field['label']); // Using esc_html(), a WP function
				}elseif(is_array($field['inputs'])) {
					foreach($field["inputs"] as $input){
						if($input['id'] == $field_id) {
							if(class_exists('GFCommon')) {
								$output = esc_html(GFCommon::get_label($field,$field_id));
							} else {
								#$output = RGForms::escape_text(RGForms::get_label($field,$field_id));  // No longer used
								$output = esc_html(RGForms::get_label($field,$field_id));  // No longer used
							}
						}
					}
				}
			}
			return $output;
		}
		function gf_field_label($form_id, $field_id) {
			echo get_gf_field_label($form_id, $field_id);
		}

		// Returns a form using php instead of shortcode
		function get_gf_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null){
			if(class_exists('GFFormDisplay')) {
				return GFFormDisplay::get_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null);
			} else {
				return RGFormsModel::get_form($id, $display_title, $display_description);
			}
		}
		function gf_form($id, $display_title=true, $display_description=true, $force_display=false, $field_values=null){
			echo get_gf_form($id, $display_title, $display_description, $force_display, $field_values);
		}

		// Returns array of leads for a specific form
		function get_gf_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=3000, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null, $status = 'active', $approvedcolumn = false, $limituser = false) {
			return GFDirectory::get_leads($form_id,$sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date, $status, $approvedcolumn, $limituser);
		}

		function gf_leads($form_id, $sort_field_number=0, $sort_direction='DESC', $search='', $offset=0, $page_size=3000, $star=null, $read=null, $is_numeric_sort = false, $start_date=null, $end_date=null) {
			echo get_gf_leads($form_id,$sort_field_number, $sort_direction, $search, $offset, $page_size, $star, $read, $is_numeric_sort, $start_date, $end_date);
		}

		function kws_gf_directory($atts) {
			GFDirectory::make_directory($atts);
		}


		if(!function_exists('kws_print_r')) {
			function kws_print_r($content, $die = false) {
				echo '<pre>'.print_r($content, true).'</pre>';
				if($die) { die(); }
				return $content;
			}
		}

	}
}

/* Ending ?> left out intentionally */