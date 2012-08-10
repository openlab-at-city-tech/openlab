<?php
/* this code is used to define our included subthemes. Kind of big. */

/*
    ================== Weaver Admin Tab - Weaver Themes ================
*/
if (!function_exists('weaver_themes_admin')) :
function weaver_themes_admin() {
    /* The opening default admin panel - used to pick a predefined theme. Put it first because
      it is less intimidating than the Main Options tab.
    */
	global $weaver_theme_list;
?>

<h3>Predefined Weaver Themes
<?php weaver_help_link('help.html#PredefinedThemes','Help for Weaver Predefined Themes');?>
<small style="font-weight:normal;font-size:10px;">&nbsp;&larr; You can click the ?'s found throughout Weaver admin pages for context specific help.</small></h3>
<b>Welcome to Weaver</b>

<p>For a long time, Weaver 2.2.x has given you extreme control of your WordPress blog appearance.
This page lets you get a quick start by picking one of the many
predefined sub-themes. Once you've picked a starter theme, use the <em>Main Options</em> and <em>Advanced Options</em>
panels to tweak the theme to be whatever you like. After you have a theme you're happy with,
you can save it from the Save/Restore tab. The <em>Snippets</em> tab has
some hints for additional fine tuning, and the <em>Help</em> tab has much more <b>useful</b> information.</p>
<p>Support for Weaver 2.2.x will continue, but please note that a new version, Weaver II, is now available with
many new features, including automatic mobile device support. You can download <strong><a href="http://wordpress.org/extend/themes/weaver-ii" target="_blank">Weaver II at WordPress.org</a></strong>, or visit <strong><a href="http://weavertheme.com" target="_blank">WeaverTheme.com</a></strong> for more details.
</p>

<h4>Get started by trying one of the predefined sub-themes!</h4>
<?php
    weaver_st_pick_theme('');

    echo("<hr />\n");
    $themeimgs = get_template_directory_uri() . '/subthemes/';

	/* first, show the default theme */
    if (!weaver_getopt('ttw_hide_theme_thumbs')) {
	echo ("<h3>Sub-theme thumbnails</h3>\n");

	echo ('<table width="900px" border="0" cellspacing="10" cellpadding="5">');

	$col=0;		/* have default, so start at 1 */

	foreach ($weaver_theme_list as $theme) {
	    $name = $theme['name'];
	    if ($name == 'My Saved Theme') {
		$img = '';
		$desc = '';
	    } else if ($name == '') {
		$img = $themeimgs . 'custom.jpg';
		$desc = "Description not available";
	    } else {
		$img = $themeimgs . $theme['img'];
		$desc = $theme['desc'];
	    }

	    if ($img == '') continue;	// don't show my saved theme...

	    if ($col == 0) {echo '<tr valign="top"><td width="25%">';}

		echo("<strong>$name</strong><br />");	/* info about the theme */
		echo "<img src='$img' width=200 height=150 /><br />";
		echo "<small>$desc</small>";
		echo "</td>\n";
		++$col;			/* track # of cols output */
		if ($col > 3) {
			echo '</tr>';	/* end of row? */
			$col = 0;
		} else echo '<td width=width="25%">';
	}
	if ($col != 0) echo '</tr>';
	echo "</table>\n";		/* all done with table */
    } // end don't hide thumbs
} /* end weaver_themes_admin */
endif;

if (!function_exists('weaver_st_show_subtheme_form')) :
function weaver_st_show_subtheme_form() {
    weaver_st_pick_theme('2');
    ?>
    <form method='post'>
	<table cellspacing='10' cellpadding='5'>
<?php if (weaver_allow_multisite()) : ?>
		<tr><td width=70px'>&nbsp;</td>
		<td><span class='submit'><input name='savemytheme' type='submit' value='Save in My Saved Theme'/></span></td>
		<td><small>Save <u>all</u> currently saved options (both Main and Advanced) as <strong>My Saved Theme</strong>.
 You will be able to restore these later by selecting <strong>My Saved Theme</strong>.</td>
		</tr>
                <tr><td colspan=3>Theme name:&nbsp;
                <input name="newthemename" id="newthemename" type="text" value="<?php if ( weaver_getopt('ttw_themename') != "") { echo weaver_esc_textarea(weaver_getopt('ttw_themename')); } else { echo ''; } ?>" />
                <span class='submit'><input name='changethemename' type='submit' value='Change Theme Name'/></span>&nbsp;<small>This name is used
                only here, but is preserved when you "Save" a theme using this admin tab.</small>
		</td></tr>
<?php endif; ?>
	<?php global $weaver_dev; if ($weaver_dev) { ?>
	<tr>
	    <td colspan=3>
		Theme image file: &nbsp; <input name="newthemeimage" id="newthemeimage" type="text"
			value="<?php if ( weaver_getopt('ttw_theme_image') != "") { echo weaver_esc_textarea(weaver_getopt('ttw_theme_image')); } else { echo ''; } ?>" />
	    </td>
	</tr>
	<tr>
	    <td colspan=3>
		Theme description: &nbsp;  <input size='100' name="newthemedesc" id="newthemedesc" type="text"
			value="<?php if ( weaver_getopt('ttw_theme_description') != "") { echo weaver_esc_textarea(weaver_getopt('ttw_theme_description')); } else { echo ''; } ?>" />
	<?php
	} ?>
        </table>
	<?php weaver_nonce_field('savemytheme');
	weaver_nonce_field('changethemename'); ?>
    </form>
     <?php
}
endif;

/*
    ========================= Weaver Admin Tab - Save/Restore Themes ==============
*/
if (!function_exists('weaver_saverestore_admin')) :
function weaver_saverestore_admin() {
    /* admin tab for saving and restoring theme */

    if (!weaver_f_file_access_available()) {
	echo '<h2>Save/Restore Themes and Backups - DISABLED';
	weaver_help_link('help.html#SaveRestore','Help on Save/Restore Themes');
	echo('</h2>');

	echo '<p>Weaver will continue to function with reduced functionality. You will be able to change sub-themes,
	set your own options, and your theme will be displayed with in-line CSS. You won\'t be able to save backups
	of your work, or have page/post styling.</p>' . "\n";

	echo '<div style="display:none">';
	$upload_link = '';

    } else {
	 $upload_link = weaver_write_current_theme('current_ttw_subtheme');	// make a temp copy
    }
    $ttw_theme_dir = weaver_f_uploads_base_dir() .'weaver-subthemes/';
    ?>

    <h2>Save/Restore Themes and Backups<?php
    weaver_help_link('help.html#SaveRestore','Help on Save/Restore Themes');?>
    </h2>
    <h4>You can save either all your settings in a backup file, or just theme related settings in a theme file:</h4>
    <ol style="font-size: 85%">
     <li>Save <em>all</em> your current settings in a backup file on your site's file system (in <?php echo($ttw_theme_dir);?>). Automatically names the backup file to include current date and time.
     Survives Weaver Theme updates. -or-</li>
    <li>"Save in My Saved Theme" - Saves <em>all</em> settings in a special "My Saved Theme" backup file. Survives Weaver Theme updates. -or-</li>

    <li>Download current theme related settings to a file on your own computer. -or-</li>
   <li>Save theme related settings to a file on your Site's file system (in <?php echo($ttw_theme_dir);?>.</li></ol>
<?php if (weaver_allow_multisite()) : ?>
    <h4>You can restore a saved theme or backup file by:</h4>
    <ol style="font-size: 85%">
   <li>Picking "My Saved Theme" backup from the standard themes list. -or-</li>
   <li>Restoring a theme/backup that you saved in a file on your site (to current settings). -or-</li>
   <li>Uploading a theme/backup from a file saved on your own computer (to current settings).</li>
    </ol>
<?php endif; ?>
<?php if (!weaver_allow_multisite()) : ?>
    <h4>You will be unable to restore your saved file directly</h4>
    <p>Since this is a WordPress Multi-site installation, you are restricted from uploading
    a Weaver theme/backup from a saved file. However, the save file capability gives you the ability
    to save your work so you can transfer it to a WordPress site where you have full admin
    capabilities (non-Multi-site installation, for example), or to share with others. Please
    note that you <em>can</em> save your settings in "My Saved Theme" which will allow you
    to explore other predefined themes without losing your work.
    </p>
<?php endif; ?>

    <hr />
    <h3><span style="color:blue;">Use "My Saved Theme"</span></h3>
    <?php weaver_st_show_subtheme_form();    /* add the picker for subthemes */ ?>

        <hr />
    <h3><span style="color:blue;">Save Current Settings in Backup File</span></h3>
     <small><strong>Save</strong> <u>all</u> current options (both Main and Advanced) in a <strong>file</strong> on your
     WordPress Site's <em><?php echo($ttw_theme_dir);?></em> directory named 'weaver_backup_yyyy-mm-dd-hhmm.wvb'
     where the last part is a GMT based date and time stamp.
<?php if (weaver_allow_multisite()) : ?>
    You will be able to restore this theme later using the <strong>Restore Saved Theme/Backup</strong> section.
<?php endif; ?>
    Please be sure you've saved any changes you might have made.</small><br />
     <form enctype="multipart/form-data" name='backup-settings' method='post'>
	<span class='submit'><input name='backup_settings' type='submit' value='Backup All Current Settings'/></span>
    <?php weaver_nonce_field('backup_settings'); ?>
    </form><br />

    <hr />
    <h3><span style="color:blue;">Save Current Theme to File or Download to your computer</span></h3>
     <small><strong>Save</strong> current <em>theme related</em> settings (non-site specific settings), either by downloading
    to <strong>your computer</strong> or saving a <strong>file</strong> on your WordPress Site's <em><?php echo($ttw_theme_dir);?></em> directory.
<?php if (weaver_allow_multisite()) : ?>
    You will be able to restore this theme later using the <strong>Restore Saved Theme/Backup</strong> section.
<?php endif; ?>
    Please be sure you've saved any changes you might have made.</small><br /><br />

  <strong>Save as file on this website's server</strong>
 <p>Please provide a name for your file, then click the "Save File" button. <b>Warning:</b> Duplicate names will
    automatically overwrite existing file without notification.</p>
 <form enctype="multipart/form-data" name='savetheme' method='post'><table cellspacing='10' cellpadding='5'>
    <table>
    <td>Name for saved theme: <input type="text" name="savethemename" size="30" />&nbsp;<small>(Please use a meaningful
    name - do not provide file extension. Name might be altered to standard form.)</small></td></tr>
	<tr>
	<td><span class='submit'><input name='filesavetheme' type='submit' value='Save Theme in File'/></span>&nbsp;&nbsp;
	<small><strong>Save Theme in File</strong> - Theme will be saved in <em><?php echo($ttw_theme_dir);?></em> directory on your site server.</small></td>
        </tr>
    </table>
    <?php weaver_nonce_field('filesavetheme'); ?>
 </form><br />

    <strong>Download to your computer</strong>

 <p>Please <em>right</em>-click <a href="<?php echo("$upload_link"); ?>"><strong>[* here *]</strong></a> to download the saved theme to your computer. </p>

<?php if (weaver_allow_multisite()) : ?>
<hr />

    <h3><span style="color:blue;">Restore Saved Theme/Backup from file</span></h3>
    <small>You can restore a previously saved theme (.wvr) or backup (.wvb) file directly from your WordPress
    Site's <em><?php echo($ttw_theme_dir);?></em> directory, or from a file saved on your computer.
    Note: after you restore a saved theme, it will be loaded into the current settings. A <em>theme</em> restore will
    replace only settings that are not site-specific. A <em>backup</em> file will replace all current settings.
    If you've uploaded the theme from your computer, you might then want to also save a local copy on your
    website server.</small><br /><br />

    <form enctype="multipart/form-data" name='localrestoretheme' method='post'><table cellspacing='10' cellpadding='5'>
    <table>
    <tr><td><strong>Restore from file saved on this website's server</strong></td></tr>
    <tr>
        <td>Select theme/backup file: <?php weaver_subtheme_list('ttw_restorename'); ?>&nbsp;Note: <strong>.wvr</strong> are Theme definitions. <strong>.wvb</strong> are full backups. (Restores to current settings.)</td></tr>
	<tr>
	<td><span class='submit'><input name='restoretheme' type='submit' value='Restore Theme/Backup'/></span>&nbsp;&nbsp;
	<small><strong>Restore</strong> a theme/backup  you've previously saved on your site's <em><?php echo($ttw_theme_dir);?></em> directory. Will replace current settings.</small></td>
    </tr>
        <tr><td>&nbsp;</td></tr>
    </table>
    <?php weaver_nonce_field('restoretheme') ; ?>
    </form>
    <form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
	<table>
            <tr><td><strong>Upload file saved on your computer</strong></td></tr>
		<tr valign="top">
			<td>Select theme/backup file to upload: <input name="uploaded" type="file" />
			<input type="hidden" name="uploadit" value="yes" />&nbsp;(Restores to current settings.)
                        </td>
		</tr>
                <tr><td><span class='submit'><input name="uploadtheme" type="submit" value="Upload theme/backup" /></span>&nbsp;<small><strong>Upload and Restore</strong> a theme/backup from file on your computer. Will become current settings.</small></td></tr>
                <tr><td>&nbsp;</td></tr>
	</table>
	<?php weaver_nonce_field('uploadtheme'); ?>
    </form>

    <hr />

    <form enctype="multipart/form-data" name='maintaintheme' method='post'>
    <h3><span style="color:green;">Sub-theme and Backup File Maintenance</span></h3>
        <?php weaver_subtheme_list('selectName'); ?>

        <span class='submit'><input name='deletetheme' type='submit' value='Delete Sub-Theme/Backup File'/></span>
          <strong>Warning!</strong>This action can't be undone, so be sure you mean to delete a file!
	  <?php weaver_nonce_field('deletetheme'); ?>
    </form>
<?php endif;

    if (!weaver_f_file_access_available()) {
	echo '</div> <!-- end of hiding save/restore because not file access available -->';
    }
?>
    <hr />
	<form name="ttw_resetweaver_form" method="post" onSubmit="return confirm('Are you sure you want to reset all Weaver settings?');">
	    <h3><span style="color:green;">Clear all Weaver Settings</span></h3>
	    <strong>Click the Clear button to reset all Weaver settings to the default values.</strong><br > <em>Warning: You will lose all current settings.</em> You should use "Backup All Current Settings" to save a copy
	    of your current settings before clearing! <span class="submit"><input type="submit" name="reset_weaver" value="Clear All Weaver Settings"/></span>
	    <?php weaver_nonce_field('reset_weaver'); ?>
	</form> <!-- ttw_resetweaver_form -->
    <hr />
<?php
}  /* end weaver_saverestore_admin */
endif;

if (! function_exists('weaver_subtheme_list')) :
function weaver_subtheme_list($lbl) {
    // output the form to select a file list from weaver-subthemes directory
?>
    <select name="<?php echo($lbl);?>" id="<?php echo($lbl);?>">
	    <option value="None">-- Select File --</option>
	    <?php
		// echo the theme file list
		$wpdir = wp_upload_dir();		// get the upload directory
                $ttw_theme_dir = $wpdir['basedir'].'/weaver-subthemes/';
		if($media_dir = opendir($ttw_theme_dir)){
		    while ($m_file = readdir($media_dir)) {
			if($m_file != "." && $m_file != ".." && $m_file[0]!='.' && $m_file != 'current_ttw_subtheme.wvr'){
			    echo '<option value="'.$m_file.'">'.$m_file.'</option>';
			}
		    }
		}
	    ?>
	</select>
    <?php
}
endif;
if (!function_exists('weaver_st_pick_theme')) :
function weaver_st_pick_theme($altID) {
    // display a picker for the list of themes.
    global $weaver_theme_list;		// the master list
    $themeimgs = get_template_directory_uri() . '/subthemes/';

   /* define control items for theme picker */

    $curTheme = weaver_getopt('ttw_subtheme');

    $showImg = 'custom.jpg';

    $selectID = 'ttw_subtheme'.$altID;	// allows more than one form on the same admin page
    $subName = 'setsubtheme'.$altID;

?>

    <form name="weaver_select_theme_form" method="post">  <table class="optiontable">
     <tr>
	<th style="width:110px;">Select a theme: &nbsp;</th>
	<td>
	<select <?php echo("name='$selectID' id='$selectID'"); ?> >
        <?php
	foreach ($weaver_theme_list as $theme) {
	    $name = $theme['name']; ?>
	    <option<?php if ( $curTheme == $name) {
		$showImg = $theme['img'];
		echo ' selected="selected"';
		}?>><?php echo $name; ?></option>
	    <?php } ?>
 	</select>
	</td>
	<td ><small>Select a predefined sub-theme from the list.</small>
	<?php echo("&nbsp; &nbsp;<small>Current theme: <strong>$curTheme</strong></small>"); ?>
	</td>
	<td valign="middle" align="left" style="width:120px;">
	<?php
	echo ("&nbsp;&nbsp;<img src='". $themeimgs . $showImg . "' width=67 height=50 />");
	?>
	</td>
	</tr>

	<tr><td>&nbsp;</td><td><span class='submit' style="padding-right:15px;"><input <?php echo("name='$subName'"); ?> type='submit' value='Set to Selected Sub-Theme'/></span></td>
<?php if (!weaver_allow_multisite()) { ?>
                <td ><small><strong>Please note:</strong> Changing sub-theme will replace all theme related your current "Main Options" settings.
		<em>Per site options</em> (those marked with a &diams; on
		the Main Options Page are not changed.)
		You can save them first with the "Save in My Saved Theme" button below.</small></td>
                </tr>
		<tr><td>&nbsp;</td>
		<td><span class='submit'><input name='savemytheme' type='submit' value='Save in My Saved Theme'/></span></td>
		<td colspan='2'><small>Save <u>all</u> currently saved options as <strong>My Saved Theme</strong>.
 You will be able to restore these later by selecting <strong>My Saved Theme</strong>. Please note: be sure to click <em>Save Current Settings</em>
 on the Main Options panel first to save any changes you might have made.</td>
		</tr>
<?php } else { ?>
                <td><small><strong>Please note:</strong> Changing sub-theme will replace all theme related settings. <em>Per site options</em> (those marked with a &diams; on
		the Main Options Page, as well as most "Advanced Options" are not changed. (Important: Using "My Saved Theme" saves and restores ALL settings. You can also backup all
		current settings from the Save/Restore tab).</small></td>
                </tr>
<?php }     /* end TTW_MULTISITE */ ?>
        </table>
	<?php weaver_nonce_field($subName); weaver_nonce_field('savemytheme'); ?>
    </form>
<?php
}
endif;
?>
