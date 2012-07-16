<?php
/*
  ////This file contains all the functions needed to interact with the different
  options and settings.

  Options are saved in the WP DB in one option called 'weaver_main_settings'.

    This file includes the interface to the WP Settings API.

   Because the SAPI is quite limiting on the format of the output fields
   supported by add_settings_field, we will not use that part.

   Settings that need validation and nonce handling, we use our function weaver_sapi_advanced_name() that
   generates the <input name="weaver_main_settings[ttw_option_name]" ...> format required for
   processing by the sapi handlers. They create an array called $_POST['weaver_main_settings']. Each
   setting in that array corresponds to a Weaver option value, and will be passed to the
   validation function.

   We will wrap the two main forms (Main Options and most Advanced Option with our functions
   weaver_sapi_form_top() and weaver_sapi_form_bottom() that generates required calls to sapi.

   All other forms will use submit buttons that include their own nonce definition.
*/


/*
    ================= Main SAPI helper functions =================
*/
function weaver_sapi_options_init() {
    /* this will initialize the SAPI stuff, must be called from the admin_init cb function .
	In reality, we really only need to register one setting - 'weaver_main_settings_group',
	and the settings will be saved in the WP DB as 'weaver_main_settings'. The SAPI uses
	the name param of any <input> fields to figure out where to store the input value.

	The validation will have to scan the ENTIRE list of options and lookup the kind of
	validation each parameter needs.
    */

    register_setting('weaver_main_settings_group',	/* the group name of our settings */
	'weaver_main_settings',				/* the get_option name */
	'weaver_validate_main_cb');			/* a validation call back */

    register_setting('weaver_advanced_settings_group',	/* the group name of our settings */
	'weaver_advanced_settings',			/* the get_option name */
	'weaver_validate_advanced_cb');			/* a validation call back */

}


function weaver_sapi_form_top($group, $form_name='') {
    /* beginning of a form */
    $name = '';
    if ($form_name != '') $name = 'name="' . $form_name . '"';

    /* we are going to give ourselves full manual control over the settings list - by not calling action="options.php",
      we bypass its calls to validate. We'll do that manually.
    */
    echo("	<form action=\"options.php\" $name method=\"post\">\n");	/* <form action="options.php" method="post"> */
    settings_fields($group);		// use our one set of settings
}

function weaver_sapi_form_bottom($form_name='end of form') {
    echo ("	</form> <!-- $form_name -->\n");
}

function weaver_sapi_submit($submit_action = 'Submit', $submit_label = 'Submit', $class='button-primary') {
	// generate a submit button for the form
?>
	<input name="<?php echo($submit_action); ?>" type="submit" class="<?php echo($class); ?>" value="<?php echo($submit_label); ?>" />
<?php
}

function weaver_sapi_advanced_name($id, $echo=true) {
    /* generate the SAPI name for 'weaver_advanced_settings' */
    if ($echo) echo 'weaver_advanced_settings[' . $id . ']';
    return 'weaver_advanced_settings[' . $id . ']';
}

function weaver_sapi_main_name($id, $echo=true) {
    /* generate the SAPI name for 'weaver_main_settings' */
    if ($echo) echo 'weaver_main_settings[' . $id . ']';
    return 'weaver_main_settings[' . $id . ']';
}

/*
    ============== Validation =====================
*/
function weaver_validate_main_cb($in) {
   /* validation for main options  */

    $err_msg = '';			// no error message yet

    // if (isset($_POST['saveoptions'])) $err_msg .= '*** saveoptions =' . $_POST['saveoptions'];
    foreach ($in as $key => $value) {
	switch ($key) {

	    /* -------- integer -------- */
	    case 'ttw_after_header':
	    case 'ttw_border_adjust_sidebar':
	    case 'ttw_excerpt_length':
	    case 'ttw_footer_widget_count':
	    case 'ttw_header_image_height':
	    case 'ttw_header_image_width':
	    case 'ttw_site_margins':
	    case 'ttw_title_on_header_xy_X':
	    case 'ttw_title_on_header_xy_Y':
	    case 'ttw_title_on_header_xy_desc_X':
	    case 'ttw_title_on_header_xy_desc_Y':

		if (!empty($value) && (!is_numeric($value) || !is_int((int)$value))) {
		    $opt_id = str_replace('_X', '', $key);	// kill _css
		    $opt_id = str_replace('_Y', '', $opt_id);
		    $name = weaver_get_mainopt_name($opt_id);
		    $err_msg .= __('Option must be an integer value: ',WEAVER_TRANSADMIN) . $name . ' = "' . $value . '".'
		    . __(' Reset to blank value.',WEAVER_TRANSADMIN) . '<br />';
		    $in[$key] = '';
		}
		break;

	    /* -------- color -------- */
	    case 'ttw_body_bgcolor':
	    case 'ttw_caption_color':
	    case 'ttw_container_bgcolor':
	    case 'ttw_content_bgcolor':
	    case 'ttw_content_color':
	    case 'ttw_desc_color':
	    case 'ttw_footer_bgcolor':
	    case 'ttw_footer_border_color':
	    case 'ttw_footer_widget_bgcolor':
	    case 'ttw_header_bgcolor':
	    case 'ttw_hr_color':
	    case 'ttw_ilink_color':
	    case 'ttw_ilink_hover_color':
	    case 'ttw_ilink_visited_color':
	    case 'ttw_info_color':
	    case 'ttw_infotop_bgcolor':
	    case 'ttw_infobottom_bgcolor':
	    case 'ttw_input_bgcolor':
	    case 'ttw_link_color':
	    case 'ttw_link_hover_color':
	    case 'ttw_link_visited_color':
	    case 'ttw_main_bgcolor':
	    case 'ttw_media_lib_border':
	    case 'ttw_media_lib_captioned_border':
	    case 'ttw_menubar_color':
	    case 'ttw_menubar_curpage_color':
	    case 'ttw_menubar_hover_color':
	    case 'ttw_menubar_hoverbg_color':
	    case 'ttw_menubar_text_color':
	    case 'ttw_page_bgcolor':
	    case 'ttw_page_title_color':
	    case 'ttw_plink_color':
	    case 'ttw_plink_hover_color':
 	    case 'ttw_plink_visited_color':
	    case 'ttw_post_bgcolor':
	    case 'ttw_post_title_color':
	    case 'ttw_post_title_color_css':
	    case 'ttw_side1_bgcolor':
	    case 'ttw_side2_bgcolor':
	    case 'ttw_side3_bgcolor':
	    case 'ttw_sidebar_width':
	    case 'ttw_stickypost_bgcolor':
	    case 'ttw_text_color':
	    case 'ttw_title_color':
	    case 'ttw_title_font':
	    case 'ttw_topbottom_bgcolor':
	    case 'ttw_widget_color':
	    case 'ttw_widget_item_bgcolor':
	    case 'ttw_widget_title_color':
	    case 'ttw_wlink_color':
	    case 'ttw_wlink_hover_color':
	    case 'ttw_wlink_visited_color':

		if (!empty($value)) {
		    $val = weaver_filter_code($value);
		    if (strpos($val, '#') !== false)
			$val = strtoupper($val);		// force hex values to upper case, just to be tidy
		    $in[$key] = $val;
		}

		break;

	    /* ---------- css -------- */
	    case 'ttw_body_bgcolor_css':
	    case 'ttw_caption_color_css':
	    case 'ttw_container_bgcolor_css':
	    case 'ttw_content_bgcolor_css':
	    case 'ttw_content_color_css':
	    case 'ttw_desc_color_css':
	    case 'ttw_footer_border_color_css':
	    case 'ttw_footer_widget_bgcolor_css':
	    case 'ttw_header_bgcolor_css':
	    case 'ttw_hr_color_css':
	    case 'ttw_ilink_color_css':
	    case 'ttw_ilink_hover_color_css':
	    case 'ttw_ilink_visited_color_css':
	    case 'ttw_info_color_css':
	    case 'ttw_infotop_bgcolor_css':
	    case 'ttw_infobottom_bgcolor_css':
	    case 'ttw_input_bgcolor_css':
	    case 'ttw_link_color_css':
	    case 'ttw_link_hover_color_css':
	    case 'ttw_link_visited_color_css':
	    case 'ttw_main_bgcolor_css':
	    case 'ttw_media_lib_captioned_border_css':
	    case 'ttw_media_lib_border_css':
	    case 'ttw_menubar_color_css':
	    case 'ttw_menubar_curpage_color_css':
	    case 'ttw_menubar_hover_color_css':
	    case 'ttw_menubar_hoverbg_color_css':
	    case 'ttw_menubar_text_color_css':
	    case 'ttw_page_bgcolor_css':
	    case 'ttw_page_title_color_css':
	    case 'ttw_plink_color_css':
	    case 'ttw_plink_hover_color_css':
	    case 'ttw_plink_visited_color_css':
	    case 'ttw_post_bgcolor_css':
	    case 'ttw_side1_bgcolor_css':
	    case 'ttw_side2_bgcolor_css':
	    case 'ttw_side3_bgcolor_css':
	    case 'ttw_stickypost_bgcolor_css':
	    case 'ttw_text_color_css':
	    case 'ttw_title_color_css':
	    case 'ttw_topbottom_bgcolor_css':
	    case 'ttw_widget_color_css':
	    case 'ttw_widget_item_bgcolor_css':
	    case 'ttw_widget_title_color_css':
	    case 'ttw_wlink_color_css':
	    case 'ttw_wlink_hover_color_css':
	    case 'ttw_wlink_visited_color_css':

		if (!empty($value)) {
		    $val = weaver_filter_code($value);
		    $in[$key] = $val;

		    if (strpos($val, '{') === false || strpos($val, '}') === false) {
			$opt_id = str_replace('_css', '', $key);	// kill _css
			$name = weaver_get_mainopt_name($opt_id);
			$err_msg .= __('Warning: CSS options must be enclosed in {}\'s: ',WEAVER_TRANSADMIN) . $name . ' = "' . $value . '".'
			    . __(' Please correct your entry.',WEAVER_TRANSADMIN) . '<br />';
		    }
		}

		break;

	    /* ---------- text ----------- */
	    case 'ttw_excerpt_more_msg':

		if (!empty($value))
		    $in[$key] = weaver_filter_textarea($value);
		break;

	    default:		/* to here, then checkboxes or selection lists */
		break;
	}

    }
    if (!empty($err_msg)) {
	add_settings_error('weaver_main_settings', 'settings_updated', $err_msg,'error');
    } else {
	add_settings_error('weaver_main_settings', 'settings_updated', __('Weaver Main Settings Saved.',WEAVER_TRANSADMIN),'updated');
    }
    return $in;
}

function weaver_validate_advanced_cb($in) {	/* a no-op for now */
    $err_msg = '';
    foreach ($in as $key => $value) {

	switch ($key) {

	    /* code */

	    case 'ttw_footer_opts':          	// insert into footer
	    case 'ttw_head_opts':           	// Predefined Theme CSS Rules
	    case 'ttw_preheader_insert':     	// Pre-Header Code
	    case 'ttw_header_insert':        	// Site Header Insert Code
	    case 'ttw_custom_header_insert': 	// Custom Header Page Template Code
	    case 'ttw_postheader_insert':    	// Post-Header Code
	    case 'ttw_prefooter_insert':     	// Pre-Footer Code
	    case 'ttw_postfooter_insert':    	// Post-Footer Code
	    case 'ttw_presidebar_insert':    	// Pre-Sidebar Code
	    case 'ttw_end_opts':             	// The Last Thing
	    case 'ttw_metainfo':               	// meta info for header
	    case 'ttw_theme_head_opts':		// Predefined Theme CSS Rules
	    case 'ttw_menu_addhtml-left':	// add html to left menu
	    case 'ttw_menu_addhtml':
	    case 'ttw_copyright':		// Alternate copyright
	    case 'ttw_css_rows':
	    case 'ftp_hostname':
	    case 'ftp_username':
		if (!empty($value)) {
		    $in[$key] = weaver_filter_code($value);
		}
		break;

	    case 'ftp_password':		// special handling for password
		if (!empty($value)) {
		    $c_t = weaver_encrypt(trim($value));
		    $in[$key] = $c_t;
		}
		break;

	    case 'ttw_perpagewidgets':       	// Add widget areas for per page - names must be lower case
		if (!empty($value)) {
		    $in[$key] = strtolower(str_replace(' ', '' , weaver_filter_code($value)));
		}
		break;

	    /* must not have <style .... </style> */
	    case 'ttw_add_css':              	// Add CSS Rules to Weaver's style rules

		if (!empty($value)) {
		    $val = weaver_filter_code($value);
		    $in[$key] = $val;
		    if (stripos($val,'<style') !== false || stripos($val, '</style') !== false) {
			$err_msg .= __('"Add CSS Rules" option must not contain &lt;style&gt; tags!',WEAVER_TRANSADMIN)
			    . __(' Please correct your entry.',WEAVER_TRANSADMIN) . '<br />';
		    }

		}
		break;

	    default:				/* to here, then checkboxes or selection lists */
		break;
	}
    }
    if (!empty($err_msg)) {
	add_settings_error('weaver_advanced_settings', 'settings_updated', $err_msg,'error');
    } else {
	add_settings_error('weaver_advanced_settings', 'settings_updated', __('Weaver Advanced Settings Saved.',WEAVER_TRANSADMIN),'updated');
    }
    return $in;
}

/*
    ============ validation filters ===============
*/
function weaver_filter_code( $text ) {
    static $weaver_allowedadmintags = array(
		'address' => array(),
		'a' => array(
			'class' => array (),
			'href' => array (),
			'id' => array (),
			'title' => array (),
			'rel' => array (),
			'rev' => array (),
			'name' => array (),
			'target' => array()),
		'abbr' => array(
			'class' => array (),
			'title' => array ()),
		'acronym' => array(
			'title' => array ()),
		'article' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'aside' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'b' => array(),
		'big' => array(),
		'blockquote' => array(
			'id' => array (),
			'cite' => array (),
			'class' => array(),
			'lang' => array(),
			'xml:lang' => array()),
		'br' => array (
			'class' => array ()),
		'button' => array(
			'disabled' => array (),
			'name' => array (),
			'type' => array (),
			'value' => array ()),
		'caption' => array(
			'align' => array (),
			'class' => array ()),
		'cite' => array (
			'class' => array(),
			'dir' => array(),
			'lang' => array(),
			'title' => array ()),
		'code' => array (
			'style' => array()),
		'col' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'span' => array (),
			'dir' => array(),
			'style' => array (),
			'valign' => array (),
			'width' => array ()),
		'del' => array(
			'datetime' => array ()),
		'dd' => array(),
		'details' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'open' => array (),
			'style' => array (),
			'xml:lang' => array(),
		),
		'div' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array()),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'fieldset' => array(),
		'figure' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'figcaption' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'font' => array(
			'color' => array (),
			'face' => array (),
			'size' => array ()),
		'footer' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'form' => array(
			'action' => array (),
			'accept' => array (),
			'accept-charset' => array (),
			'enctype' => array (),
			'method' => array (),
			'name' => array (),
			'target' => array ()),
		'h1' => array(
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h2' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h3' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h4' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h5' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'h6' => array (
			'align' => array (),
			'class' => array (),
			'id'    => array (),
			'style' => array ()),
		'header' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'hgroup' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'hr' => array (
			'align' => array (),
			'class' => array (),
			'noshade' => array (),
			'size' => array (),
			'width' => array ()),
		'i' => array(),
		'img' => array(
			'alt' => array (),
			'align' => array (),
			'border' => array (),
			'class' => array (),
			'height' => array (),
			'hspace' => array (),
			'longdesc' => array (),
			'vspace' => array (),
			'src' => array (),
			'style' => array (),
			'width' => array ()),
		'ins' => array(
			'datetime' => array (),
			'cite' => array ()),
		'kbd' => array(),
		'label' => array(
			'for' => array ()),
		'legend' => array(
			'align' => array ()),
		'li' => array (
			'align' => array (),
			'class' => array ()),
		'menu' => array (
			'class' => array (),
			'style' => array (),
			'type' => array ()),
		'nav' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'p' => array(
			'class' => array (),
			'align' => array (),
			'dir' => array(),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array()),
		'pre' => array(
			'style' => array(),
			'width' => array ()),
		'q' => array(
			'cite' => array ()),
		's' => array(),
		'script' => array(),
		'span' => array (
			'class' => array (),
			'dir' => array (),
			'align' => array (),
			'lang' => array (),
			'style' => array (),
			'title' => array (),
			'xml:lang' => array()),
		'section' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'style' => array(),
		'sub' => array(),
		'summary' => array(
			'align' => array (),
			'class' => array (),
			'dir' => array (),
			'lang' => array(),
			'style' => array (),
			'xml:lang' => array(),
		),
		'sup' => array(),
		'table' => array(
			'align' => array (),
			'bgcolor' => array (),
			'border' => array (),
			'cellpadding' => array (),
			'cellspacing' => array (),
			'class' => array (),
			'dir' => array(),
			'id' => array(),
			'rules' => array (),
			'style' => array (),
			'summary' => array (),
			'width' => array ()),
		'tbody' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'td' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'dir' => array(),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'style' => array (),
			'valign' => array (),
			'width' => array ()),
		'textarea' => array(
			'cols' => array (),
			'rows' => array (),
			'disabled' => array (),
			'name' => array (),
			'readonly' => array ()),
		'tfoot' => array(
			'align' => array (),
			'char' => array (),
			'class' => array (),
			'charoff' => array (),
			'valign' => array ()),
		'th' => array(
			'abbr' => array (),
			'align' => array (),
			'axis' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'colspan' => array (),
			'headers' => array (),
			'height' => array (),
			'nowrap' => array (),
			'rowspan' => array (),
			'scope' => array (),
			'valign' => array (),
			'width' => array ()),
		'thead' => array(
			'align' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'valign' => array ()),
		'title' => array(),
		'tr' => array(
			'align' => array (),
			'bgcolor' => array (),
			'char' => array (),
			'charoff' => array (),
			'class' => array (),
			'style' => array (),
			'valign' => array ()),
		'tt' => array(),
		'u' => array(),
		'ul' => array (
			'class' => array (),
			'style' => array (),
			'type' => array ()),
		'ol' => array (
			'class' => array (),
			'start' => array (),
			'style' => array (),
			'type' => array ()),
		'var' => array ());
    // virtually all option input from Weaver can be code, and thus must not be
    // content filtered. The utf8 check is about the extent of it, although even
    // that is more restrictive than the standard text widget uses.
    // Note: this check also works OK for simple checkboxes/radio buttons/selections,
    // so it is ok to blindly pass those options in here, too.
    $noslash = trim(stripslashes($text));
    if ( current_user_can('unfiltered_html') ) {
        return wp_check_invalid_utf8( $noslash );
    } else if (current_user_can('add_users')) {
	return wp_kses( $text , $weaver_allowedadmintags);
    } else {
	return stripslashes( wp_filter_post_kses( addslashes($text) ) ); // wp_filter_post_kses() expects slashed
    }
}

function weaver_filter_textarea( $text ) {
    // virtually all option text input from Weaver can be code, and thus must not be
    // content filtered. Treat like code for now....
    return weaver_filter_code($text);
}

function weaver_esc_textarea($text) {
    /* likely replace with esc_textarea for 3.1 */
    return esc_html(stripslashes($text));
}

/*
    ============== Option Functions and Helpers =================
*/
function weaver_getopt_plus($val) {
    // get a setting from weaver plus
    if (function_exists('weaver_plus_getopt')) {
	return weaver_plus_getopt($val);
    }
    else return false;
}

function weaver_setopt($opt, $val) {
    global $weaver_opts_cache;
    $weaver_opts_cache[$opt] = $val;
}

function weaver_getopt($opt) {
    global $weaver_opts_cache;
    if (!isset($weaver_opts_cache[$opt]))	// handles changes to data structure
      {
	$weaver_opts_cache[$opt] = false;
	return false;
      }
    return stripslashes($weaver_opts_cache[$opt]);	// left over from 2010 Weaver
}
function weaver_getopt_checked($opt) {
    global $weaver_opts_cache;
    if (!isset($weaver_opts_cache[$opt]))	// handles changes to data structure
      {
	$weaver_opts_cache[$opt] = false;
	return false;
      }
    if (!$weaver_opts_cache[$opt]) return false;
    return true;
}

function weaver_deleteopt($opt) {
    global $weaver_opts_cache;
    $weaver_opts_cache[$opt] = false;
}

/* some helper functions to access values */
function weaver_getopt_color($color) {
    global $weaver_main_options;
    $stdval = WEAVER_DEFAULT_COLOR;
    $setcolor = weaver_getopt($color);
    if ($setcolor != $stdval)
        return $setcolor;
    return $stdval;
}

/* some helper functions to access values */
function weaver_get_mainopt_name($id) {
    global $weaver_main_options;
    $name = __('You entered ',WEAVER_TRANSADMIN);
    foreach ($weaver_main_options as $option => $val) {
	if ($val['id'] == $id) {
	    $name = '"' . $val['name'] . '"';
	    break;
	}
    }
    return $name;
}

function weaver_get_font_value($byid) {
	/* get font value if not default */
	global $weaver_main_options;

	foreach ($weaver_main_options as $value) {
		if ($value['id'] == $byid) {
			$v = weaver_getopt($value['id']);
			if ($v == '') $v = $value['std'];
			if ($v == $value['std']) return '';
			return $v;
		}
	}
	return '';
}


/*
    =============== Option Initialize and Save =================
*/

function weaver_load_cache() {
    // This is called as a result of the after_setup_theme hook.
    // This means it is called both for Weaver Admin and during normal site display.

    global $weaver_opts_cache, $weaver_main_opts_list, $weaver_advanced_opts_list;

    $weaver_main_settings = get_option('weaver_main_settings');	/* most of the time, this will be there */
    $weaver_advanced_settings = get_option('weaver_advanced_settings');

    if (!$weaver_main_settings || !$weaver_advanced_settings) {		/* first time (or settings cleared!), so get some settings */
	$weaver_wpdb_has_settings = false;		// weaver settings do not exist in WP DB
	$weaver_main_settings = array();
	$weaver_advanced_settings = array();
	foreach ($weaver_main_opts_list as $key => $val) {
	    $weaver_main_settings[$key] = false;
	}
	foreach ($weaver_advanced_opts_list as $key => $val) {
	    $weaver_advanced_settings[$key] = false;
	}
     } else {
	$weaver_wpdb_has_settings = true;		// settings exist in WP DB
     }

    /* to here, then $weaver_main_settings will have a valid set of settings */
    $weaver_opts_cache = array();			/* it will be an array */
    foreach ($weaver_main_settings as $key => $val) {	/* cache to local */
	$weaver_opts_cache[$key] = $val;
    }
    foreach ($weaver_advanced_settings as $key => $val) {	/* cache to local */
	$weaver_opts_cache[$key] = $val;
    }
    return $weaver_wpdb_has_settings;
}

function weaver_wpdb_has_settings() {
    $main_settings = get_option('weaver_main_settings');	/* most of the time, this will be there */
    if (!$main_settings) return false;
    return true;
}

function weaver_init_opts($who='', $force=false) {
    // This is called only when Weaver Admin is loaded - either at initialization, or when resetting Weaver settings

    global $weaver_opts_cache, $weaver_main_opts_list, $weaver_advanced_opts_list;

    /* initialize other stuff that needs a value */
    /* now if there were no settings, need to fill in the default theme and <HEAD> Section and SEO default */
    if ($force || !weaver_wpdb_has_settings()) {
	// if (!weaver_f_file_access_available()) return;
	weaver_setopt('ttw_version_id',WEAVER_VERSION_ID);

	$myName = esc_attr( get_bloginfo( 'name', 'display' ) );
	$myDescrip = esc_attr( get_bloginfo( 'description', 'display' ) );
	if (strcasecmp($myDescrip,'Just another WordPress site') == 0) $myDescrip = '';

	$headText = "<!-- Add your own CSS snippets between the style tags. -->
<style type=\"text/css\">
</style>";
	$SEOText = "<meta name=\"description\" content=\" $myName - $myDescrip \" />
<meta name=\"keywords\" content=\"$myName blog, $myName\" />";

	weaver_setopt('ttw_head_opts', $headText);	// fill in something first time
	weaver_setopt('ttw_metainfo', $SEOText);	// fill in something first time

        require_once('wvr-subthemes.php'); // we only need to include this once on first install.
	weaver_activate_subtheme(WEAVER_START_THEME);
	weaver_save_opts('weaver_init_opts');				// changed some things, so re-save
    }
}

function weaver_save_opts($who='',$no_bump = false) {
    /* this is a hard write - updates both options and saves settings to DB */

    // in theory, this is unnecessary.
    global $weaver_opts_cache;
    global $weaver_main_opts_list, $weaver_advanced_opts_list;

    if (!$no_bump) {		// this is where we bump the CSS version number - everytime after settings change
	$vers = weaver_getopt('ttw_style_version');
	$vers++;
	weaver_setopt('ttw_style_version',$vers);
    }

    $weaver_main_settings = get_option('weaver_main_settings');	/* most of the time, this will be there */
    $weaver_advanced_settings = get_option('weaver_advanced_settings');
    if (!$weaver_main_settings || !$weaver_advanced_settings) {	// must be working with valid data
	$weaver_main_settings= array();
	$weaver_advanced_settings = array();
    }

    /* unmerge $weaver_opts_cache into separate lists */

    foreach ($weaver_opts_cache as $key => $val) {
	if (isset($weaver_main_opts_list[$key]))
	    $weaver_main_settings[$key] = $val;
	else if (isset($weaver_advanced_opts_list[$key]) )
	    $weaver_advanced_settings[$key] = $val;
    }

    update_option('weaver_main_settings',$weaver_main_settings);	/* save a version in the DB */
    update_option('weaver_advanced_settings',$weaver_advanced_settings);

    if (weaver_f_file_access_available()) {
	require_once('wvr-generatecss.php');
	weaver_save_current_css();
    }
}


/*
    ==================== SAVE / RESTORE THEMES AND BACKUPS ==========================
*/
function weaver_savemytheme() {
    /* saves all current settings into My Saved Theme file, changes current theme name. */
    /* Weaver will save themes and backups in files.
    = .wvr files are theme files, and are pretty much compatible back to 2010 Weaver 1.1.
	Older versions of .wvr files will include "per-site" settings that are now being
	ignored, but are harmless. .wvr files saved by new versions of Weaver will not
	include per-site settings.
    = .wvb files are backup versions which save everything that is possible to set. They
	are used to save "My Saved Theme" as well as backup files
 */
    weaver_setopt('ttw_subtheme', 'My Saved Theme');		/* make it my saved theme */
    return weaver_write_backup('weaver_my_saved_theme', false /* not theme */);
}

function weaver_savebackup() {
    /* generate file name with current date and time to save backup file */
    $name = 'weaver_backup_' . date('Y-m-d-Hi');

    if (weaver_write_backup($name, false))
	return $name;
    else
	return false;
}

function weaver_write_current_theme($savefile) {
     return weaver_write_backup($savefile, true);		// write a theme save file
}

function weaver_write_backup($savefile, $is_theme = true) {
    /*	write the current settings to a file, return true or false
	$savefile is a base-name - no directory, no extension
    */

    global $weaver_opts_cache, $weaver_main_opts_list, $weaver_advanced_opts_list;

    $nosave = array( 'ftp_hostname' => true,
    'ftp_username' => true,
    'ftp_password' => true);		// don't write these

    if (!weaver_f_file_access_available()) {
	weaver_f_file_access_fail('Limited file access. Probably running with reduced functionality.');
	return '';
    }
    weaver_save_opts('weaver_write_backup',true); // let's save it in case the user forgot (saves everything)

    $wpdir = wp_upload_dir();		// get the upload directory

    $save_dir = weaver_f_uploads_base_dir() . 'weaver-subthemes';
    $save_url = weaver_f_uploads_base_url() . 'weaver-subthemes';

    if ($is_theme) $ext = '.wvr';
    else $ext = '.wvb';

    $usename = strtolower(sanitize_file_name($savefile));
    $usename = str_replace($ext,'',$usename);
    if (strlen($usename) < 1) return '';
    $usename = $usename . $ext;

    $ttw_theme_dir_exists = weaver_f_mkdir($save_dir);
    $ttw_theme_dir_writable = $ttw_theme_dir_exists;

   if (!weaver_f_is_writable($save_dir)) {
        $ttw_theme_dir_writable = false;
    }

    $filename = trailingslashit($save_dir) . $usename;

    if (!$ttw_theme_dir_writable || !$ttw_theme_dir_exists || !($handle = weaver_f_open($filename, 'w')) ) {
	weaver_f_file_access_fail('Unable to create file. Probably a file system permission problem. File: ' . $filename);
	return '';
    }

    /* file open, ready to write - so let's write something - either a backup or a theme */
    if (!$is_theme) {

        weaver_f_write($handle,"WVB-V02.00");			/* Save theme settings: 10 byte header */
	$theme_opts = array();
	foreach ($weaver_opts_cache as $key => $value) {
	   if ($value != '' && !isset($nosave[$key])) {
		$theme_opts[$key] = $value;
	   }
	}
	$tosave = serialize($theme_opts);	/* serialize full set of options right now */

    } else {						/* save only theme settings */

	weaver_f_write($handle,"TTW-V01.10");			/* Save theme settings: 10 byte header */
	global $weaver_dev;
	if ($weaver_dev) {
	    $theme_name = weaver_getopt('ttw_themename');
	    weaver_setopt('ttw_subtheme',$theme_name);
	}
	$theme_opts = array();
	   /* now can set the new values from $restore */

	foreach ($weaver_opts_cache as $key => $value) {
	    if ($value != '' && !isset($nosave[$key])) {
		if (isset($weaver_main_opts_list[$key]) &&  !$weaver_main_opts_list[$key])
		    $theme_opts[$key] = $value;
		else if (isset($weaver_advanced_opts_list[$key]) &&  !$weaver_advanced_opts_list[$key])
		    $theme_opts[$key] = $value;
	    }
	}

	$tosave = serialize($theme_opts);
    }

    weaver_f_write($handle, $tosave);	// write all Weaver settings to user save file
    weaver_f_close($handle);

    return trailingslashit($save_url) . $usename;
}

function weaver_upload_backup($file_basename) {

    return weaver_upload_theme( weaver_f_uploads_base_dir() . 'weaver-subthemes/' . $file_basename . '.wvb' );
}

function weaver_upload_theme($filename) {

    if (!weaver_f_exists($filename)) return weaver_f_fail("Can't open $filename");     	/* can't open */
    if (!weaver_f_file_access_available()) {	// try an alternative approach - use the allowed file()
	$contents = implode('',file($filename));
    } else {
	$contents = weaver_f_get_contents($filename);
    }
    if (!$contents) return weaver_f_fail("Can't open $filename");

    return weaver_set_current_to_serialized_values($contents);
 }

function weaver_set_current_to_serialized_values($contents)  {
    global $weaver_main_opts_list, $weaver_advanced_opts_list;

    if (substr($contents,0,10) == "TTW-V01.10")
	$type = 'theme';
    else if (substr($contents,0,10) == "WVB-V02.00")
	$type = 'backup';
    else
	return weaver_f_fail(__("Wrong theme file format version",WEAVER_TRANSADMIN)); 	/* simple check for one of ours */
    $restore = array();
    $restore = unserialize(substr($contents,10));

    if (!$restore) return weaver_f_fail("Unserialize failed");

    if ($type == 'theme') {
	weaver_clear_theme_settings();			/* now restore defaults - saved will override changes */
        /* now can set the new values from $restore */
	foreach ($restore as $rkey => $rval) {
	    if ($rval != '') {
		if (isset($weaver_main_opts_list[$rkey]) &&  !$weaver_main_opts_list[$rkey])
		    weaver_setopt( $rkey, $rval );
		else if (isset($weaver_advanced_opts_list[$rkey]) &&  !$weaver_advanced_opts_list[$rkey])
		    weaver_setopt( $rkey, $rval );
	    }
	}
    } else if ($type == 'backup') {
	weaver_clear_cache_settings();
	global $weaver_opts_cache;
	foreach ($restore as $key => $value) {
	    $weaver_opts_cache[$key] = $value;
	}
    }

    weaver_save_opts('loading theme',true);	// OK, now we've saved the options, update them in the DB
    return true;
}


function weaver_clear_theme_settings() {
    /* clear the theme related options */

    global $weaver_main_opts_list, $weaver_advanced_opts_list;
    foreach ($weaver_main_opts_list as $key => $val) {
	if (!$val) weaver_setopt( $key, false );		// just clear the non-per-site options
    }
    foreach ($weaver_advanced_opts_list as $key => $val) {
	if (!$val) weaver_setopt( $key, false );		// just clear the non-per-site options
    }
}

function weaver_clear_cache_settings() {
    /* clear all settings */
    global $weaver_opts_cache;
    foreach ($weaver_opts_cache as $key => $value) {
	$weaver_opts_cache[$key] = false;		// clear everything
    }
}

if (!function_exists('weaver_activate_subtheme')) :
function weaver_activate_subtheme($use_subtheme) {
    /* load settings for specified theme - including special "My Saved Theme" */
    global $weaver_opts_cache, $weaver_theme_list;

    if ($use_subtheme == 'My Saved Theme') { /* special handling for My Saved Theme */

	weaver_upload_backup('weaver_my_saved_theme');

    } else {			/* picked a pre-defined sub-theme */

	// apply_filters('wvrx_themes_gettheme',$def,$use_subtheme))) // might be plugin theme

	// find the file name based on the name
	$theme_file = 'wvr-wheat'; 	// a default version
	foreach ($weaver_theme_list as $theme) {
	    if ($theme['name'] == $use_subtheme) {
		$theme_file = $theme['file'];
		break;
	    }
	}

	/* build the filename - theme files stored in /wp-content/themes/weaver/subthemes/ */

	$filename = get_template_directory() . '/subthemes/' . $theme_file . '.wvr';
	weaver_upload_theme($filename);
    }
    // weaver_setopt('ttw_subtheme', $use_subtheme);	// don't need - weaver_upload_backup and _theme do this
}
endif;

/*
    ================= import old 2010 Weaver settings ================
*/
function weaver_check_for_2010_weaver() {
    /* see if old 2010 Weaver settings found, and tell them how to upgrade */
    if (!weaver_getopt('wvr_hide_if_are_oldWeaver_opts') && get_option('ttw_options'))
      { ?>
      <div style="background-color:#FFEEEE; border: 5px ridge red; margin: 0px 60px 0px 20px; padding:15px;">
        <form name="wvr_hide_import_old_form" method="post"
	      onSubmit="return confirm('You can show this notice again with an Administrative Option on the bottom of the Advanced Options tab.');">
	<h4 style="margin-top:0px;">IMPORTANT! Previously existing settings from an older version of 2010 Weaver or Weaver found.</h4>
	    <p>Settings from an older version of Weaver have been found. If you are using this version of Weaver for the first time,
	    you can import all your previous settings, and your site will then look the same as it did before. (You don't have
	    to decide right now -  the previous settings won't go away while you check out this version of Weaver.)
	    <br /> <br />
	     Note: if you have used <em>Appearance->Menus</em> to define Custom Menus, you will have to manually re-activate your menus.
	    Weaver Version 2 has other small incompatibilities with earlier Weaver versions. Click for details:
	    <?php weaver_help_link('help.html#v2incompatibilities',__('Weaver Version 2.0 Differences',WEAVER_TRANSADMIN)); ?>
	    </p>
	    <span class="submit"><input type="submit" name="hide_import_old_weaver" value="Hide This Notice From Now On" /></span>
	    &nbsp;<small>This will hide this notice box. There's an option at the bottom of the Advanced Options tab to show it again.</small><br />
	    <?php weaver_nonce_field('hide_import_old_weaver'); ?>
	</form> <!-- ttw_importold_form -->
	<?php if (true) { ?>
        <form name="wvr_import_old_form" method="post"
	      onSubmit="return confirm('If you made any changes, you might want to use the Backup option on the Save/Restore tab first. Are you sure you want to overwrite your current settings with the previously existing Weaver settings?');">
	    <span class="submit"><input type="submit" name="import_old_weaver" value="Load Previous Weaver Settings"/></span>
	    &nbsp;<small>Load the previous Weaver settings into the current settings. Those previous settings will not be deleted.</small>
	    <?php weaver_nonce_field('import_old_weaver'); ?>
	</form> <!-- ttw_importold_form -->
	<?php } ?>
	</div>
      <?php
      }
}

function weaver_import_2010_weaver() {
    /* convert existing 2010 Weaver options from DB to current settings */
    global $weaver_opts_cache;

    weaver_clear_cache_settings();	// start with an empty set of options

    /* first, lets import any old my saved theme options */
    $myopts = get_option('ttw_myoptions');
    if ($myopts) {
	$old_opts = unserialize($myopts);
	foreach ($old_opts as $key => $value) {
	   $weaver_opts_cache[$key] = $value;		// set everything
	}
        if (!weaver_write_backup('weaver_my_saved_theme',false /* not theme */))
	    wp_die('Failure to import existing Weaver files.');
	weaver_clear_cache_settings();	// back to an empty set of options
    }

   $opts = get_option('ttw_options');
    if ($opts) {
        $old_opts = unserialize($opts);
	foreach ($old_opts as $key => $value) {
	   $weaver_opts_cache[$key] = $value;		// set everything
	}
    };

    $aopts = get_option('ttw_adminoptions');
    if ($aopts) {
	$adminOpts = unserialize($aopts);
	foreach ($adminOpts as $key => $value) {
	   $weaver_opts_cache[$key] = $value;		// set everything
	}
    }
    weaver_setopt('ttw_version_id',WEAVER_VERSION_ID);
    weaver_setopt('wvr_hide_if_are_oldWeaver_opts',true);
    // weaver_save_opts('weaver_import_2010_weaver');					// save it all
}

function weaver_save_msg($msg) {
    echo '<div id="message" class="updated fade"><p><strong>' . $msg .
	    '</strong></p></div>';
}
/*
    ================= process settings when enter admin pages ================
*/
function weaver_admin_page_process_options() {
    /* Process all options - called upon entry to options forms */
    // echo("WEAVER-SETTINGS:"); var_dump($weaver_main_settings);
    // echo "POST-weaver-settings:" ; var_dump($_POST['weaver_main_settings']);
    // echo "POST-FULL:" ; var_dump($_POST);

    settings_errors();			// display results from SAPI save settings

    /* ================ Weaver Upgrade 2020 Weaver buttons ================== */
    if (weaver_submitted('hide_import_old_weaver')) {
	weaver_setopt('wvr_hide_if_are_oldWeaver_opts',true);
	weaver_save_msg(__('Hide 2010 Weaver Notice',WEAVER_TRANSADMIN));
    }

    if (weaver_submitted('import_old_weaver')) {
	weaver_import_2010_weaver();
	weaver_save_msg(__("Previously existing settngs imported.",WEAVER_TRANSADMIN));
    }

    /* ================ Weaver Themes Tab ================== */

    if (weaver_submitted('setsubtheme') || weaver_submitted('setsubtheme2')) {
	/* seems like Mozilla doesn't like 2 sets of select inputs on same page, so we make up 2 ids/names to use */

	if (isset($_POST['setsubtheme'])) $pID = 'ttw_subtheme';
	else $pID = 'ttw_subtheme2';

        $cur_subtheme = weaver_filter_textarea($_POST[ $pID]);	/* must have been set to get here */
        if ($cur_subtheme == '') $cur_subtheme = 'Wheat';	/* but just in case */

        /* now, I set all values for theme */
        weaver_activate_subtheme($cur_subtheme);

        $t = weaver_getopt('ttw_subtheme'); if ($t == '') $t = 'Wheat';    /* did we save a theme? */
        weaver_save_msg(__("Weaver current sub-theme set to: ",WEAVER_TRANSADMIN).$t);
    }

    /* ================ Weaver FTP File Access ================== */


    if (weaver_submitted('ftp_save_form')) {

	if (isset($_POST['ftp_hostnamex'])) weaver_setopt('ftp_hostname', trim(weaver_filter_textarea( $_POST['ftp_hostnamex'])));
	if (isset($_POST['ftp_usernamex'])) weaver_setopt('ftp_username', trim(weaver_filter_textarea( $_POST['ftp_usernamex'])));
	if (isset($_POST['ftp_passwordx'])) weaver_setopt('ftp_password', weaver_encrypt(trim( $_POST['ftp_passwordx'])));
	if (isset($_POST['ftp_hide_check_messagex'])) weaver_setopt('ftp_hide_check_message', true);
	else weaver_setopt('ftp_hide_check_message', false);

	weaver_save_msg(__('FTP File Access Form Settings Saved.',WEAVER_TRANSADMIN));

    }

    /* ================ Weaver Main Options Tab ================== */

    /* Weaver Main and Advanced Options processed in validation callbacks */


    /* ================ Weaver Advanced Options Tab ================== */

    /* SAPI settings are handled in the Main Options Tab section above */

    if (weaver_submitted('reset_weaver')) {
	// delete everything!
	weaver_save_msg(__('All Weaver settings have been reset to the defaults.',WEAVER_TRANS));
	delete_option('weaver_main_settings');
	delete_option('weaver_advanced_settings');
	weaver_load_cache();		// be sure cache has something valid in it
	weaver_init_opts('reset_weaver');
    }


    /* ================ Weaver Save/Restore Themes Tab ================== */

    /* this tab has the most individual forms and submit commands */

    if (weaver_submitted('changethemename')) {

	if (isset($_POST['newthemename'])) {
            $new_name = sanitize_user($_POST['newthemename']);
            weaver_setopt('ttw_themename',$new_name);
            echo '<div id="message" class="updated fade"><p><strong>Theme name changed to '.$new_name.'</strong></p></div>';
	}
	global $weaver_dev;
	if($weaver_dev) {		// used to save a theme for distribution/inclusion in dynamic theme db
	    if (isset($_POST['newthemeimage'])) {
		weaver_setopt('ttw_theme_image',$_POST['newthemeimage']);
	    }
	    if (isset($_POST['newthemedesc'])) {
		weaver_setopt('ttw_theme_description',$_POST['newthemedesc']);
	    }
	}
    }

    if (weaver_submitted('savemytheme')) {
	if (weaver_savemytheme())
	    weaver_save_msg(__('All current main and advanced options backed up in <em>My Saved Theme</em>.',WEAVER_TRANSADMIN));
	else
	    weaver_save_msg(__('ERROR: Saving <em>My Saved Theme</em> failed.',WEAVER_TRANSADMIN));
    }

     if (weaver_submitted('backup_settings')) {
	$name = weaver_savebackup();
	if ($name !== false)
	    weaver_save_msg(__('All current main and advanced options backed up in:',WEAVER_TRANSADMIN).' "'. $name . '.wvb"');
	else
	    weaver_save_msg(__('ERROR: Saving backup failed.',WEAVER_TRANSADMIN));
    }

    if (weaver_submitted('filesavetheme')) {
        $base = strtolower(sanitize_file_name($_POST['savethemename']));
        $temp_url =  weaver_write_current_theme($base);
        if ($temp_url == '')
            weaver_save_msg(__('Invalid name supplied to save theme to file.',WEAVER_TRANSADMIN));
        else
            weaver_save_msg(__("All current main and advanced options saved in ",WEAVER_TRANSADMIN) . $temp_url);
    }

    if (weaver_submitted('uploadtheme') &&  isset($_POST['uploadit']) && $_POST['uploadit'] == 'yes') {
        weaver_uploadit();
    }

    if (weaver_submitted('restoretheme')) {
	$base = $_POST['ttw_restorename'];
	$valid = validate_file($base);		// make sure an ok file name
        $fn = weaver_f_uploads_base_dir() .'weaver-subthemes/'.$base;

	if ($valid < 1 && weaver_upload_theme($fn)) {
	    $t = weaver_getopt('ttw_subtheme'); if ($t == '') $t = 'Wheat';    /* did we save a theme? */
	    weaver_save_msg(__("Weaver theme restored from file, saved as: ",WEAVER_TRANSADMIN).$t);
	} else {
	    weaver_save_msg('<em style="color:red;">'. __('INVALID FILE NAME PROVIDED - Try Again',WEAVER_TRANSADMIN). "($fn)" . '</em>');
	}
    }

    if (weaver_submitted('deletetheme')) {
        $myFile = $_POST['selectName'];
	$valid = validate_file($myFile);
        if ($valid < 1 && $myFile != "None") {
            weaver_f_delete(weaver_f_uploads_base_dir() .'weaver-subthemes/'.$myFile);
	    echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade"><p>File: <strong>'.$myFile.'</strong> has been deleted.</p></div>';
        } else {
	    echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade"><p>File: <strong>'.$myFile.'</strong> invalid file name, not deleted.</p></div>';
	}
    }

    /* ====================================================== */

    if (weaver_submitted('wvrx_save_extension'))			/* for theme extensions */
	do_action('wvrx_save_extension');

    if (weaver_submitted('wvrx_plus_save_plus'))
        do_action('wvrx_plus_save_plus');		// All plus submit buttons...

    weaver_save_opts('Weaver Admin');			/* FINALLY - SAVE ALL OPTIONS AND UPDATE CURRENT CSS FILE */

}
/* ^^^^^ end weaver_admin_page_process_options ^^^^^^ */
/*
    ================= nonce helpers =====================
*/
function weaver_submitted($submit_name) {
    // do a nonce check for each form submit button
    // pairs 1:1 with weaver_nonce_field
    $nonce_act = $submit_name.'_act';
    $nonce_name = $submit_name.'_nonce';

    if (isset($_POST[$submit_name])) {
	if (isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name],$nonce_act)) {
	    return true;
	} else {
	    die("WARNING: invalid form submit detected ($submit_name). Possibly caused by session time-out, or, rarely, a failed security check. Please contact weavertheme.com if you continue to receive this message.");
	}
    } else {
	return false;
    }
}

function weaver_nonce_field($submit_name) {
    // pairs 1:1 with ttw_sumbitted
    // will be one for each form submit button

    wp_nonce_field($submit_name.'_act',$submit_name.'_nonce');
}

require_once ("wvr-fileio.php");
?>
