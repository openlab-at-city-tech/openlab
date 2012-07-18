<?php
/*
    Weaver Admin - Uses yetii JavaScript to build tabs.
    Tabs include:
	Weaver Themes		(in wvr-subthemes.php)
	Main Options		(in this file)
	Advanced Options	(in wvr_advancedopts.php)
	Save/Restore Themes	(in wvr-subthemes.php)
	Snippets		(in wvr-help.php)
	CSS Help		ditto
	Help			ditto
/*
    ========================= Weaver Admin Tab - Main Options ==============
*/
function weaver_do_admin() {
/* theme admin page */

/* This generates the startup script calls, etc, for the admin page */
    global $weaver_opts_cache, $weaver_main_options, $weaver_main_opts_list;

    if (!current_user_can('edit_theme_options')) wp_die("No permission to access that page.");

    weaver_admin_page_process_options();		/* handle incoming options settings for all pages*/
    echo('<div class="wrap">');
    screen_icon("themes");	/* add a nice icon to the admin page */
?>
<div style="float:left;"><h2><?php echo(WEAVER_THEMEVERSION); ?> Options</h2><a name="top_main" id="top_main"></a>
</div>


<?php
      if (!weaver_getopt('ttw_subtheme')) weaver_init_opts('admin',true);

      if (! weaver_f_check_WP_Filesystem() && !weaver_getopt('ftp_hide_check_message')) {			// let's get a working FS first
	    weaver_ftp_form();
      } else {
      if (false && !function_exists( 'weaver_plus_plugin' ) ) { ?>
<div style="float:right;padding-right:30px;"><small><strong>Like Weaver? Please</strong></small>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6Y68LG9G9M82W">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
<?php } ?>
<div style="clear:both;">
<?php
	    weaver_check_for_2010_weaver(); 	/* notify if 2010 Weaver settings found */
	    weaver_check_version();		// check version RSS
?>

<div id="tabwrap">
  <div id="tab-container-1" class='yetii'>
    <ul id="tab-container-1-nav" class='yetii'>
	<li><a href="#tab0"><?php echo(__('Weaver Themes',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#tab1"><?php echo(__('Main Options',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#tab2"><?php echo(__('Advanced Options',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#tab3"><?php echo(__('Save/Restore',WEAVER_TRANSADMIN)); ?></a></li>
	<?php do_action('wvrx_add_plus_tab_title','<li><a href="#tab9">','</a></li>'); ?>
	<li><a href="#tab4"><?php echo(__('Snippets',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#tab7"><?php echo(__('CSS Help',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#tab5"><?php echo(__('Help',WEAVER_TRANSADMIN)); ?></a></li>
	<?php do_action('wvrx_add_extended_tab_title','<li><a href="#tab6">','</a></li>'); ?>
    </ul>

    <div id="tab0" class="tab" >
         <?php weaver_themes_admin(); ?>
    </div>
    <div id="tab1" class="tab" >
         <?php weaver_options_admin(); ?>
    </div>
    <div id="tab2" class="tab">
       <?php weaver_advanced_admin(); ?>
    </div>
    <div id="tab3" class="tab">
       <?php weaver_saverestore_admin(); ?>
    </div>
    <?php do_action('wvrx_add_plus_tab','<div id="tab9" class="tab" >', '</div>'); /* plus option admin tab */ ?>

    <div id="tab4" class="tab">
       <?php weaver_snippets_admin(); ?>
    </div>
    <div id="tab7" class="tab">
       <?php weaver_csshelp_admin(); ?>
    </div>
    <div id="tab5" class="tab">
       <?php weaver_help_admin(); ?>
    </div>
    <?php do_action('wvrx_add_extended_tab','<div id="tab6" class="tab" >', '</div>'); /* extended option admin tab */ ?>

  </div>

<?php if (weaver_getopt('ttw_show_preview')) { ?>

<h3>Preview of site. Displays current look <em>after</em> you save options or select sub-theme.</h3>
<iframe id="preview" name="preview" src="<?php echo get_option('siteurl');  ?>?temppreview=true" style="width:100%;height:400px;border:1px solid #ccc"></iframe>
<?php } else { echo("<h4>If you'd like a preview box of your site here, check the 'Show Site Preview' box near the bottom of the Advanced Options tab.</h4>\n"); } ?>
</div>
    <script type="text/javascript">
	var tabber1 = new Yetii({
	id: 'tab-container-1',
	persist: true
	});
</script>
    <script type="text/javascript">
	var tabberMain = new Yetii({
	id: 'tab-container-main',
	tabclass: 'tab_mainopt',
	persist: true
	});
</script>
</div>
<?php
      } // end of else weaver_check_write_files
}	/* end weaver_do_admin */

function weaver_check_version() {
  if (weaver_getopt('ttw_hide_updatemsg')) return;
    $version = WEAVER_VERSION;
    $latest = weaver_latest_version();     // check if newer version is available
    if (stripos($latest,'announcement') !== false) {
      weaver_save_msg( $latest . ' - Please check <a href="http://wpweaver.info" target="_blank">WPWeaver.info</a>.');
    } else if ($latest != 'unavailable' && version_compare($version,$latest,'<') ) {
       weaver_save_msg('Current Weaver version is ' . WEAVER_VERSION . '. A newer version (' . $latest .
            ') is available now or very soon from WordPress.org. <br />The latest version is always available at <a href="http://wpweaver.info" target="_blank">WPWeaver.info</a>.');
    }
}

function weaver_latest_version() {
    $rss = fetch_feed('http://wpweaver.wordpress.com/feed/');
     if (is_wp_error($rss) ) {
	return 'unavailable';
    }
    $out = '';
    $items = 1;
    $num_items = $rss->get_item_quantity($items);
    if ( $num_items < 1 ) {
	$out .= 'unavailable';
	$rss->__destruct();
	unset($rss);
	return $out;
    }
    $rss_items = $rss->get_items(0, $items);
    foreach ($rss_items as $item ) {
 	$title = esc_attr(strip_tags($item->get_title()));
	if ( empty($title) )
	    $title = 'unavailable';
    }
    if (stripos($title,'announcement') === false) {
        $blank = strpos($title,' ');    // find blank
        if ($blank < 1)     // problem
            $title = 'unavailable';
        else {
            $title = substr($title,0,$blank);
        }
    }
    $out .= $title;
    $rss->__destruct();
    unset($rss);
    return $out;
}

function weaver_uploadit() {
    // upload theme from users computer
    // they've supplied and uploaded a file

	$ok = true;     // no errors so far

        if (isset($_FILES['uploaded']['name']))
            $filename = $_FILES['uploaded']['name'];
        else
            $filename = "";

        if (isset($_FILES['uploaded']['tmp_name'])) {
            $openname = $_FILES['uploaded']['tmp_name'];
        } else {
            $openname = "";
        }

	//Check the file extension
	$check_file = strtolower($filename);
	$ext_check = end(explode('.', $check_file));

	if (!weaver_f_file_access_available()) {
	    $errors[] = "Sorry - Weaver unable to access files.<br />";
	    $ok = false;
	}

	if ($filename == "") {
	    $errors[] = "You didn't select a file to upload.<br />";
	    $ok = false;
	}

	if ($ok && $ext_check != 'wvr' && $ext_check != 'wvb'){
	    $errors[] = "Theme files must have <em>.wvr</em> or <em>.wvb</em> extension.<br />";
	    $ok = false;
	}

        if ($ok) {
            if (!weaver_f_exists($openname)) {
                $errors[] = '<strong><em style="color:red;">'.
                 __('Sorry, there was a problem uploading your file. You may need to check your folder permissions or other server settings.',WEAVER_TRANSADMIN).'</em></strong>'.
                    "<br />(Trying to use file '$openname')";
                $ok = false;
            }
        }
	if (!$ok) {
	    echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">ERROR</em></strong></p><p>';
	    foreach($errors as $error){
		echo $error.'<br />';
	    }
	    echo '</p></div>';
	} else {    // OK - read file and save to My Saved Theme
            // $handle has file handle to temp file.
            $contents = weaver_f_get_contents($openname);

            if (!weaver_set_current_to_serialized_values($contents,'weaver_uploadit:'.$openname)) {
                echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">'.
                __('Sorry, there was a problem uploading your file. The file you picked was not a valid Weaver theme file.',WEAVER_TRANSADMIN).'</em></strong></p></div>';
	    } else {
                $t = weaver_getopt('ttw_subtheme'); if ($t == '') $t = 'Wheat';    /* did we save a theme? */
                weaver_save_msg(__("Weaver theme options reset to uploaded theme, saved as: ",WEAVER_TRANSADMIN).$t);
            }
        }
}

function weaver_options_admin() {
/* theme admin page - Main Options tab */
	global $weaver_main_options;
?>

<div id="tabwrap_main" style="padding-left:4px;">
<?php if (weaver_getopt_checked('ttw_notab_mainoptions')) { ?>
<div id="tab-container-main-notab" class='yetiisub'>
    <ul id="tab-container-main-nav-notab" class='yetiisub'>
<?php } else { ?>
    <div id="tab-container-main" class='yetiisub'>
    <ul id="tab-container-main-nav" class='yetiisub'>
<?php } ?>
	<li><a href="#maintab0"><?php echo(__('General Appearance',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#maintab1"><?php echo(__('Header Options',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#maintab2"><?php echo(__('Content Areas',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#maintab3"><?php echo(__('Post Page Specifics',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#maintab4"><?php echo(__('Footer Options',WEAVER_TRANSADMIN)); ?></a></li>
	<li><a href="#maintab5"><?php echo(__('Widget Areas',WEAVER_TRANSADMIN)); ?></a></li>
    </ul>

<h3>Main Options<?php weaver_help_link('help.html#MainOptions','Help for Main Options'); ?></h3>

<?php
	weaver_put_main_options_form($weaver_main_options, 'saveoptions', "Save All Current Main Options");
        echo "</br><a name='color_note'></a><small>&diams; Note: Options marked with a &diams; are considered per-site, and are not retained when you save a theme
	definition. (All settings are retained when you save to <em>My Saved Theme</em> or a Backup.) ";
	weaver_help_link('help.html#SaveRestore','Help on Save/Restore Themes');
	echo "<br />
	&nbsp;&nbsp;Color value boxes also allow text such as <em>blue, inherit , transparent, rgba(),</em> etc.
	The values are not checked for valid color attributes.</small>&nbsp;&nbsp;&nbsp;<a href=\"#top_main\">top</a>";
?>
    </div> <!-- #tab-container-main -->
</div>	<!-- #tabwrap_main -->


<?php
}

function weaver_put_main_options_form($weaver_olist, $submit_action, $submit_label) {
    /* output a list of options - this really does the layout for the options defined in an array */

    weaver_sapi_form_top('weaver_main_settings_group','weaver_main_options_form');

    weaver_sapi_submit($submit_action, $submit_label);
    echo("&nbsp;&nbsp;<small>All \"" . $submit_label . "\" buttons save Main Options from <em>all</em> sub-tabs.</small><br>\n");
?>
<div><table>	<!-- open, empty tab div -->
<?php

    foreach ($weaver_olist as $value) {
	if ($value['type'] == "text" || $value['type'] == 'widetext') { 			/* ============= text ============= */
	  if ($value['type'] == 'text') $twide = '60';
	  else $twide = '140';
?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		<input name="<?php weaver_sapi_main_name($value['id']); ?>" id="<?php echo $value['id']; ?>" type="text" style="width:<?php echo $twide;?>px;height:22px;" class="regular-text" value="<?php echo weaver_esc_textarea(weaver_getopt( $value['id'] )); ?>" />
		</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		} ?>
		</tr>
	<?php } elseif ($value['type'] == "text_xy") { 			/* ============= text_xy ============= */ ?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		X:<input name="<?php weaver_sapi_main_name($value['id']. '_X'); ?>" id="<?php echo $value['id']; ?>_X" type="text" style="width:40px;height:20px;" class="regular-text" value="<?php echo weaver_esc_textarea(weaver_getopt( $value['id'].'_X' )); ?>" />
		&nbsp;Y:<input name="<?php weaver_sapi_main_name($value['id'] . '_Y'); ?>" id="<?php echo $value['id']; ?>_Y" type="text" style="width:40px;height:20px;" class="regular-text" value="<?php echo weaver_esc_textarea(weaver_getopt( $value['id'].'_Y' )); ?>" />
		</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		}
		?>
		</tr>
	<?php } elseif ($value['type'] == "ctext") { 			/* ============= ctext ============= */
                $pclass = 'color {hash:true, adjust:false}';    // starting with V 1.3, allow text in color pickers
		$img_css = '<img src="'. get_template_directory_uri() . '/images/weaver/css.png" />' ;
		$img_hide = get_template_directory_uri() . '/images/weaver/hide.png' ;
		$img_show = get_template_directory_uri() . '/images/weaver/show.png' ;
		$help_file = get_template_directory_uri() . '/css-help.html';
		$css_id = $value['id'] . '_css';
		$css_id_text = weaver_getopt($css_id);
		if ($css_id_text && !weaver_getopt( 'ttw_hide_auto_css_rules' )) {
		    $img_toggle = $img_hide;
		} else {
		    $img_toggle = $img_show;
		}
        ?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		<input class="<?php echo $pclass; ?>" name="<?php weaver_sapi_main_name($value['id']); ?>" id="<?php echo $value['id']; ?>" type="text" style="width:110px" value="<?php if ( weaver_getopt( $value['id'] ) != "") { echo weaver_esc_textarea(weaver_getopt( $value['id'] )); } else { echo WEAVER_DEFAULT_COLOR; } ?>" />
		<?php echo $img_css; ?><a href="javascript:void(null);"
			onclick="wvr_ToggleRowCSS(document.getElementById('<?php echo $css_id . '_js'; ?>'), this, '<?php echo $img_show; ?>', '<?php echo $img_hide; ?>')"><?php echo '<img src="' . $img_toggle . '" />'; ?></a>
		</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		} ?>
		</tr>
		<?php $css_rows = weaver_getopt('ttw_css_rows'); if ($css_rows < 1 || $css_rows > 25) $css_rows = 1;?>
		<?php if ($css_id_text && !weaver_getopt( 'ttw_hide_auto_css_rules' )) { ?>
		<tr id="<?php echo $css_id . '_js'; ?>">
		<th scope="row" align="right"><span style="color:green;"><small>Custom CSS styling:</small></span></th>
		<td align="right"><small>&nbsp;</small></td>
		<td>
		    <small>You can enter CSS rules, enclosed in {}'s, and separated by <strong>;</strong>.
		    See <a href="<?php echo $help_file; ?>" target="_blank">CSS Help</a> for more details.</small><br />
		    <textarea name="<?php weaver_sapi_main_name($css_id); ?>" rows=<?php echo $css_rows;?> style="width: 85%"><?php echo(weaver_esc_textarea($css_id_text)); ?></textarea>
		</td></tr>
		<?php } else { ?>
		<tr id="<?php echo $css_id . '_js'; ?>" style="display:none;">
		<th scope="row" align="right"><span style="color:green;"><small>Custom CSS styling:</small></span></th>
		<td align="right"><small>&nbsp;</small></td>
		<td>
		    <small>You can enter CSS rules, enclosed in {}'s, and separated by <strong>;</strong>.
		    See <a href="<?php echo $help_file; ?>" target="_blank">CSS Help</a> for more details.</small><br />
		    <textarea name="<?php weaver_sapi_main_name($css_id); ?>" rows=<?php echo $css_rows;?> style="width: 85%"><?php echo(weaver_esc_textarea($css_id_text)); ?></textarea>
		</td></tr>
		<?php } ?>
	<?php } elseif ($value['type'] == "checkbox") { 		/* ============= checkbox ============= */ ?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		<input type="checkbox" name="<?php weaver_sapi_main_name($value['id']); ?>" id="<?php echo $value['id']; ?>"
		  <?php checked(weaver_getopt_checked( $value['id'] )); ?> >
		</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		}
		?>
		</tr>
	<?php } elseif ($value['type'] == "select") { 			/* ============= select ============= */ ?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		<select name="<?php weaver_sapi_main_name($value['id']); ?>" id="<?php echo $value['id']; ?>">
                <?php foreach ($value['value'] as $option) { ?>
                <option<?php if ( weaver_getopt( $value['id'] ) == $option) { echo ' selected="selected"'; }?>><?php echo $option; ?></option>
                <?php } ?>
		</select>
		</td>

		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		} ?>
		</tr>
        <?php } elseif ($value['type'] == "imgselect") { 			/* ============= imgselect ============= */
                /* special handling of bullet images - will add the bullet image to each item */ ?>
		<tr>
		<th scope="row" align="right"><?php echo $value['name']; ?>:&nbsp;</th>
		<td>
		<select name="<?php echo weaver_sapi_main_name($value['id']); ?>" id="<?php echo $value['id']; ?>">
                <?php

                foreach ($value['value'] as $opt) {
                    $img = get_template_directory_uri() . '/images/bullets/' . $opt . '.gif';
                    if ($opt == '')     /* special case - the empty default option */
                        $style = '';
                    else
                        $style = ' style="background-image:url('. $img . ');background-repeat:no-repeat;padding-left:16px;height:16px;line-height:16px;"';

                    if (weaver_getopt( $value['id'] ) == $opt)
                        $sel = ' selected="selected" ';
                    else
                        $sel = '';
                    printf('<option%s%s>%s</option>',$sel,$style,$opt); echo("\n");
                }
                ?>
                </select>
		</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		} ?>
		</tr>
	<?php } elseif ($value['type'] == "note") { 			/* ============= note ============= */ ?>
		<tr>
		<th scope="row" align="right">&nbsp;</th>
		<td style="float:right;font-weight:bold;"><?php echo $value['name']; ?>&nbsp;</td>
		<?php if ($value['info'] != '') {
		    echo('<td style="padding-left: 10px"><small>'); echo $value['info']; echo("</small></td>");
		}
		?>
		</tr>

	<?php } elseif ($value['type'] == "header") { 			/* ============= header ============= */ ?>
</table></div>	<!-- close tab -->
<div id="<?php echo $value['id'];?>" class="tab_mainopt" >
<table class="optiontable" style="margin-top:6px;">
		<tr>
		<th scope="row" align="left" style="width:25%;"><?php	/* NO SAPI SETTING */
		echo '<span style="color:blue; font-weight:bold; font-size: larger;"><em>'.$value['name'].'</em></span>';
		if (!empty($value['value'])) {
		    weaver_help_link($value['value'], 'Help for ' . $value['name']);
	    	}
		?>
		</th>
		<td style="width:170px;">&nbsp;</td>
		<?php
		if ($value['info'] != '') {	// has own nonce
		    echo('<td style="padding-left: 10px"><u><em><strong>'); echo $value['info'];
		    echo("</strong></em></u></td>\n");
		}
		?>
		</tr>
	<?php }
	} ?>
</table></div> <!-- close previous tab div -->
 	<br />
	<?php weaver_sapi_submit($submit_action, $submit_label); ?>
	<br /><br />
<?php
    weaver_sapi_form_bottom('weaver_main_options_form');
}

function weaver_ftp_form() {
    // display warning message, and ftp info
    $readme = get_template_directory_uri().'/help.html';
?>
      <br /><br /><br /><div style="background-color:#FFEEEE; border: 5px ridge red; margin: 10px 60px 0px 20px; padding:15px;">
<strong style="color:#f00; line-height:150%;">*** IMPORTANT NOTICE! ***</strong> <small style="padding-left:20px;">(But don't panic!)</small>
<?php weaver_help_link('help.html#File_access_plugin','Weaver File Access Plugin'); ?>
	<p>Your web host configuration needs "FTP" file access for full Weaver functionality. You need to use one of the following options. There are more details in the help file. <?php weaver_help_link('help.html#File_access_plugin','Weaver File Access Plugin'); ?></p>
	<ul style="list-style-type:disc !important;list-style-position:inside !important;">
	  <li><strong>For a shared web host</strong>: Provide FTP credentials to enable file access. You can do this by filling the form below, or by adding the proper information to your wp-config.php file as described in the help file. <small>Note: most shared hosts do not need FTP file access, and won't generate this message.</small></li>
      <li><strong>For a private server or VPS</strong>: Provide FTP credentials, or install the <a href="http://wordpress.org/extend/plugins/weaver-file-access-plugin/" target="_blank">Weaver File Access Plugin</a>. The Weaver File Access Plugin will provide the required file access with the most efficiency.</li>
      <li><strong>For compatibility with existing Weaver installations:</strong> install the <a href="http://wordpress.org/extend/plugins/weaver-file-access-plugin/" target="_blank">Weaver File Access Plugin</a>. This will provide the required file access with the most compatibility with previous versions, but with a very small security risk. See the help file.
      If you have an existing Weaver installation, you may find switching file access methods (Weaver File Access Plugin to FTP, or the opposite) may result in file or directory access permission issues. See the help file.</li>
      <li><strong>For reduced functionality mode:</strong> Check the <em>Hide FTP Access Start up Dialog</em> below.
      Weaver <strong>will continue to operate</strong> in reduced functionality mode (no editor styling, no save/restore, Inline CSS)
	until you provide FTP credentials, or download and activate the <em>Weaver File Access Plugin</em>, either from <a href="http://wordpress.org/extend/plugins/weaver-file-access-plugin/" target="_blank">WordPress.org</a>, or <a href="http://wpweaver.info/themes/weaver/weaver-file-access-plugin/" target="_blank">WPWeaver.info</a>.</li>
</ul>
	<ul style="list-style-type:disc !important;list-style-position:inside !important;">
	    <li>If your host requires secure FTP or SSH access, you will need to provide the proper credentials using the wp-config.php file.</li>
      <li>Installing the <a href="http://wordpress.org/extend/plugins/weaver-file-access-plugin/" target="_blank">Weaver File Access Plugin</a> will allow full Weaver functionality on any host, but sometimes with a small security risk</li>
</ul>
	<p><strong>Please read more details in the
<?php
      echo '<a href="' . $readme . '#File_access_plugin" target="_blank">';
?>
	Weaver File Access Plugin</a> topic from the <em>Weaver Help tab</em></strong>.
	</p>
	</div><br />


    <form name="weaver_set_ftp_form" method="post">
      <p><span style="color:#00f; font-weight:bold; font-size: larger;"><strong>Provide FTP File Access Credentials</strong></span></p>
	<?php
        if (!(defined('FTP_HOST') && defined('FTP_USER') && defined('FTP_PASS'))) {
	    echo "<p>\n";
	    _e('Please enter your FTP credentials to proceed.',WEAVER_TRANSADMIN); echo ' ';
	    _e('If you do not remember your credentials, you should contact your web host.',WEAVER_TRANSADMIN); echo "</p>\n";
?>
	<label><?php _e('Hostname',WEAVER_TRANSADMIN) ?>: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input name="ftp_hostnamex" id="ftp_hostnamex" type="text" style="width:300px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_getopt('ftp_hostname'))); ?>" />
    <small>Specify the name of your host. Usually something like 'example.com'.</small>
    <br /><label><?php echo __('FTP Username',WEAVER_TRANSADMIN);?>: </label><input name="ftp_usernamex" id="ftp_usernamex" type="text" style="width:300px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_getopt('ftp_username'))); ?>" />
    <small>Specify your FTP Username.</small>
    <br /><label><?php _e('FTP Password',WEAVER_TRANSADMIN) ?>: </label><input name="ftp_passwordx" id="ftp_passwordx" type="password" style="width:300px;height:20px;" class="regular-text" value="<?php echo(weaver_esc_textarea(weaver_decrypt(weaver_getopt('ftp_password')))); ?>" />
    <small>Specify your FTP Password. This will be saved in an encrypted form.</small>
<br />
<?php
	} else {
?>
<p><em>Credentials provided in the wp-config.php file.</em> There must be some kind of problem because you shouldn't be seeing
this page unless you need to provide FTP access credentials. Perhaps the password is not included in wp-config.php.</p>
<?php
	}
?>
<p>If you continue to get this message even after entering your credentials, please double check the values. If that
	that continues to fail, you can install the
	<a href="http://wordpress.org/extend/plugins/weaver-file-access-plugin/" target="_blank">Weaver File Access Plugin</a> instead.</p><br />

<p><span style="color:#00f; font-weight:bold; font-size: larger;"><strong>Install Weaver File Access Plugin</strong></span><br />
      The easiest way to install the <em>Weaver File Access Plugin</em> is to open the Plugins&rarr;Add New panel, and enter
      <em>Weaver File Access Plugin</em> into the search box. Then install and activate the plugin.
</p>

<p><span style="color:#00f; font-weight:bold; font-size: larger;"><strong>Continue with reduced file access</strong></span><br />
      If want to continue to use Weaver without full file access, check
the box below. You can still use all of Weaver's options. You just won't be able to use the Save/Restore features, the
Page/Post editor will use plain styling, and CSS rules will be included Inline on your site's pages. <em>You can still
install the Weaver File Access Plugin, or provide FTP credentials later.</em></p>

<label><strong>Hide FTP Access Start up Dialog:</strong> </label><input type="checkbox" name="ftp_hide_check_messagex" id="ftp_hide_check_messagex" <?php checked(weaver_getopt_checked( 'ftp_hide_check_message' )); ?> />
	<small>If you check this, then this FTP File Access message box when you enter Weaver Admin will not be displayed. Weaver will function in
	reduced functionality mode: no editor styling, no save/restore, Inline CSS.</small><br /><br />

<input class="button-primary" type="submit" name="ftp_save_form" value="Save FTP File Access Options"/>
	<?php weaver_nonce_field('ftp_save_form'); ?>
    </form>



    </div> <!-- #wrap -->
<?php
}


// now that we are in the admin code, we can load the rest of the stuff needed
require_once('wvr-subthemes.php');
require_once('wvr-advancedopts.php');
require_once('wvr-plus.php');
require_once('wvr-help.php');
?>
