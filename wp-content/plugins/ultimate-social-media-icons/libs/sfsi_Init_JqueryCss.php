<?php
/*  instalation of javascript and css  */
function theme_back_enqueue_script()
{
	if (isset($_GET['page'])) {
		if ($_GET['page'] == 'sfsi-options') {
			wp_enqueue_style("SFSIbootstrap", SFSI_PLUGURL . 'css/bootstrap.min.css');
			wp_enqueue_style("SFSImainAdminCss", SFSI_PLUGURL . 'css/sfsi-admin-style.css');
			/* include CSS for backend  */
			wp_enqueue_style('thickbox');
			wp_enqueue_style("SFSImainCss", SFSI_PLUGURL . 'css/sfsi-style.css');
			wp_enqueue_style("SFSIJqueryCSS", SFSI_PLUGURL . 'css/jquery-ui-1.10.4/jquery-ui-min.css');
			wp_enqueue_style("wp-color-picker");
		}
	}
	wp_enqueue_style("SFSImainAdminCommonCss", SFSI_PLUGURL . 'css/sfsi-admin-common-style.css');

	//including floating option css
	$option9 =  unserialize(get_option('sfsi_section9_options', false));

	if ($option9['sfsi_disable_floaticons'] == 'yes') {
		wp_enqueue_style("disable_sfsi", SFSI_PLUGURL . 'css/disable_sfsi.css');
	}

	if (isset($_GET['page'])) {
		if ($_GET['page'] == 'sfsi-options') {
			wp_enqueue_script('jquery');
			wp_enqueue_script("jquery-migrate");

			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');

			wp_register_script('SFSIJqueryFRM', SFSI_PLUGURL . 'js/jquery.form-min.js', '', '', true);
			wp_enqueue_script("SFSIJqueryFRM");
			wp_enqueue_script("jquery-ui-accordion");
			wp_enqueue_script("wp-color-picker");
			wp_enqueue_script("jquery-effects-core");
			wp_enqueue_script("jquery-ui-sortable");

			wp_register_script('SFSICustomFormJs', SFSI_PLUGURL . 'js/custom-form-min.js', '', '', true);
			wp_enqueue_script("SFSICustomFormJs");

			wp_register_script('SFSICustomJs', SFSI_PLUGURL . 'js/custom-admin.js', '', '', true);
			wp_enqueue_script("SFSICustomJs");
			/* end cusotm js */

			/* initilaize the ajax url in javascript */
			wp_localize_script('SFSICustomJs', 'sfsi_icon_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
			wp_localize_script('SFSICustomJs', 'sfsi_icon_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'plugin_url' => SFSI_PLUGURL));
		}
	}
}
add_action('admin_enqueue_scripts', 'theme_back_enqueue_script');

function theme_front_enqueue_script()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script("jquery-migrate");

	wp_enqueue_script('jquery-ui-core');

	wp_register_script('SFSIjqueryModernizr', SFSI_PLUGURL . 'js/shuffle/modernizr.custom.min.js', array('jquery'), '', true);
	wp_enqueue_script("SFSIjqueryModernizr");

	wp_register_script('SFSIjqueryShuffle', SFSI_PLUGURL . 'js/shuffle/jquery.shuffle.min.js', array('jquery'), '', true);
	wp_enqueue_script("SFSIjqueryShuffle");

	wp_register_script('SFSIjqueryrandom-shuffle', SFSI_PLUGURL . 'js/shuffle/random-shuffle-min.js', array('jquery'), '', true);
	wp_enqueue_script("SFSIjqueryrandom-shuffle");
	$option1 =  unserialize(get_option('sfsi_section1_options', false));

	if (isset($option1["sfsi_wechat_display"]) && $option1["sfsi_wechat_display"] == "yes") {
		wp_register_script('SFSIPLUSqrcode.js', SFSI_PLUGURL . 'js/qrcode.min.js', '', '', true);
		wp_enqueue_script("SFSIPLUSqrcode.js");
	}
	wp_register_script('SFSICustomJs', SFSI_PLUGURL . 'js/custom.js', array('jquery'), '', true);
	wp_enqueue_script("SFSICustomJs");
	/* end cusotm js */

	/* initilaize the ajax url in javascript */
	wp_localize_script('SFSICustomJs', 'sfsi_icon_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	wp_localize_script('SFSICustomJs', 'sfsi_icon_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'plugin_url' => SFSI_PLUGURL));

	/* include CSS for front-end and backend  */
	wp_enqueue_style("SFSImainCss", SFSI_PLUGURL . 'css/sfsi-style.css', true);

	//including floating option css
	$option9 =  unserialize(get_option('sfsi_section9_options', false));
	if ($option9['sfsi_disable_floaticons'] == 'yes') {
		wp_enqueue_style("disable_sfsi", SFSI_PLUGURL . 'css/disable_sfsi.css');
	}
}
add_action('wp_enqueue_scripts', 'theme_front_enqueue_script');

function sfsi_footerFeedbackScript()
{
	?>
	<style>
		@charset "utf-8";
		@font-face {
			font-family: helveticabold;
			src: url(<?php echo SFSI_PLUGURL; ?>css/fonts/helvetica_bold_0-webfont.eot);
			src: url(<?php echo SFSI_PLUGURL; ?>css/fonts/helvetica_bold_0-webfont.eot?#iefix) format('embedded-opentype'), url(<?php echo SFSI_PLUGURL; ?>css/fonts/helvetica_bold_0-webfont.woff) format('woff'), url(<?php echo SFSI_PLUGURL; ?>css/fonts/helvetica_bold_0-webfont.ttf) format('truetype'), url(<?php echo SFSI_PLUGURL; ?>css/fonts/helvetica_bold_0-webfont.svg#helveticabold) format('svg');
			font-weight: 400;
			font-style: normal;
		}
		ul#adminmenu li.toplevel_page_sfsi-options div.wp-menu-image {
			display: none;
		}
		#adminmenu li.toplevel_page_sfsi-options a.toplevel_page_sfsi-options {
			padding: 0 0 0 38px;
			font-family: helveticabold;
		}
		ul#adminmenu li.toplevel_page_sfsi-options a.toplevel_page_sfsi-options {
			color: #e12522;
			transition: 0s;
			background: url(<?php echo SFSI_PLUGURL; ?>css/images/left_log_icn.png) 6px 15px no-repeat #23282d;
			background: url(<?php echo SFSI_PLUGURL; ?>css/images/left_log_icn.png) 6px -43px no-repeat #23282d;
			color: #fafafa;
			font-family: Arial, Helvetica, sans-serif;
		}
		ul#adminmenu li.toplevel_page_sfsi-options a.toplevel_page_sfsi-options:hover {
			background: url(<?php echo SFSI_PLUGURL; ?>css/images/left_log_icn.png) 6px -43px no-repeat black;
			color: #fafafa;
		}
		ul#adminmenu li.toplevel_page_sfsi-options a.current,
		ul#adminmenu li.toplevel_page_sfsi-options a.current:hover {
			background: url(<?php echo SFSI_PLUGURL; ?>css/images/left_log_icn.png) 6px 15px no-repeat #000000;
			color: #e12522;
		}
	</style>
<?php
}
add_action('admin_footer', 'sfsi_footerFeedbackScript');
?>