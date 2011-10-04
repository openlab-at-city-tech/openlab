<?php

// Theme Settings
$themename = "ePortfolio Theme";
$shortname = "ahstheme";

// categories
$categories_list = get_categories('hide_empty=0&orderby=name');
$cats = array();
foreach($categories_list as $acategory) {
	$cats[$acategory->cat_ID] = $acategory->cat_name;
}

$options = array (

	array("name" => "Global Settings",
		"id" => $shortname."_global_settings",
		"type" => "header"),

	array("name" => "Welcome Page",
		"id" => $shortname."_welcomepage",
		'help'=>'An excerpt of this page is shown in the homepage. The Featured Image from this page is used as well.',
		"type" => "pages"),

	array("name" => "What is ePortfolio? Page",
		"id" => $shortname."_whatispage",
		'help'=>'An excerpt of this page is shown in the homepage in the bottom left corner.',
		"type" => "pages"),

	array("name" => "Blog Category",
		"id" => $shortname."_blogcat",
		'options' => $cats,
		"type" => "select"),

	array("name" => "Projects Category",
		"id" => $shortname."_projectscat",
		'options' => $cats,
		"type" => "select"),

	array("name" => "FAQ Category",
		"id" => $shortname."_faqid",
		'options' => $cats,
		"type" => "select"),

	array("name" => "Recommendations Category",
		"id" => $shortname."_recid",
		'options' => $cats,
		"type" => "select"),

);

$options[]=array("name" => "Styling &amp; Colors",
		"id" => $shortname."_styling",
		"type" => "header");

$options[]=array("name" => "CSS",
		"id" => $shortname."_customcss",
		"type" => "textarea",
		"std" => "",
		"rows"=>6,
		"help" => "Any css you include here will be added after the theme's default styles, so you can re-style anything you'd like.");

function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {

        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=themeoptions.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=themeoptions.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "Theme Settings", 'manage_options', basename(__FILE__), 'mytheme_admin');

}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap" id="backtotop">
<h2>Theme Notes &amp; Settings</h2>

<h3>Notes</h3>

<p>This theme comes with a special shortcode. If you put <code>[contact_form]</code> into a page, it will turn into a contact form.</p>

<h3>Settings</h3>

<p style="width:70%;"><strong>Jump down to a section.</strong></p>

<ol>
	<li><a href="#<?php echo $shortname; ?>_global_settings">Global Settings</a></li>
	<li><a href="#<?php echo $shortname; ?>_styling">Styling &amp; Colors</a></li>
</ol>

<p style="width:70%;"><strong>Wherever you see 'Save Changes' it will save changes for ALL theme settings.</strong></p>


<form method="post">

<table class="optiontable">

<?php foreach ($options as $value) {

if ($value['type'] == "text") { ?>

<tr valign="top">
    <th scope="row" style="text-align:left;"><?php

if (isset($value['icon'])) {
	echo '<img src="';
	bloginfo('stylesheet_directory');
	echo '/images/social/'.strtolower($value['name']).'.png" width="20" height="20" style="padding-right: 5px;" />';
}
echo $value['name'];

?>:</th>
    <td>
        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])); } else { echo $value['std']; } ?>" size="<?php if (!empty($value['size'])) echo $value['size']; else echo '40' ?>" />
	<div style="font-size:8pt;padding-bottom:10px;"><?php echo $value['help']; ?></div>
    </td>
</tr>

<?php } elseif ($value['type'] == "header") { ?>
<tr colspan=2><td>
<p class="submit">
	<input name="save" type="submit" value="<?php _e('Save Changes'); ?>" />
	<input type="hidden" name="action" value="save" />
</p>
</td></tr>
<tr colspan=2><td><a href="#backtotop"><?php _e("Go to Top"); ?></a></td></tr>
<tr>
	<td colspan=2><h3 id="<?php echo $value['id']; ?>" style="text-align:left;padding-bottom:5px;border-bottom:1px solid #ccc;font-family:georgia,times,serif;margin-bottom:10px;font-size:16pt;color:#666;font-weight:normal;"><?php echo $value['name']; ?></h3>		<div style="font-size:8pt;padding-bottom:10px;"><?php echo $value['help']; ?></div>
</td>
</tr>

<?php } elseif ($value['type'] == "subheader") { ?>
<tr>
	<td colspan=2><h4 id="<?php echo $value['id']; ?>" style="text-align:left;padding-bottom:5px;font-family:georgia,times,serif;margin: 5px 0 10px 0;font-size:14pt;color:#666;font-weight:normal;"><?php echo $value['name']; ?></h4></td>
</tr>

<?php } elseif ($value['type'] == "textarea") { ?>

<tr valign="top">
    <th scope="row" style="text-align:left;"><?php echo $value['name']; ?>:</th>
    <td>
		<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" rows="<?php if (!empty($value['rows'])) echo $value['rows']; else echo '5' ?>" cols="<?php if (!empty($value['cols'])) echo $value['cols']; else echo '90' ?>"><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] ) ); } else { echo stripslashes($value['std'] ); } ?></textarea>
		<div style="font-size:8pt;padding-bottom:10px;"><?php echo $value['help']; ?></div>
    </td>
</tr>

<?php } elseif ($value['type'] == "select") { ?>

    <tr valign="top">
        <th scope="row" style="text-align:left;"><?php echo $value['name']; ?>:</th>
        <td>
            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                <?php foreach ($value['options'] as $optid=>$option) { ?>
                <option value="<?php echo $optid ?>" <?php if ( get_settings( $value['id'] ) == $optid) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
                <?php } ?>
            </select>
		<div style="font-size:8pt;padding-bottom:10px;"><?php echo $value['help']; ?></div>
        </td>
    </tr>

<?php } elseif ($value['type'] == "pages") { ?>

    <tr valign="top">
        <th scope="row" style="text-align:left;"><?php echo $value['name']; ?>:</th>
        <td>
            <?php wp_dropdown_pages('name='.$value['id'].'&selected='.get_settings($value['id'])) ?>
		<div style="font-size:8pt;padding-bottom:10px;"><?php echo $value['help']; ?></div>
        </td>
    </tr>

<?php
}
}
?>

</table>

<p class="submit">
	<input name="save" type="submit" value="<?php _e('Save Changes'); ?>" />
	<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
	<p class="submit" style="float:right;">
		<input name="reset" type="submit" value="<?php _e('Delete all Data and Reset to Default Settings'); ?>" />
		<input type="hidden" name="action" value="reset" />
	</p>
</form>

<?php
}

function mytheme_wp_head() { ?>
<link href="<?php bloginfo('template_directory'); ?>/style.php" rel="stylesheet" type="text/css" />
<?php }

add_action('wp_head', 'mytheme_wp_head');
add_action('admin_menu', 'mytheme_add_admin');



?>