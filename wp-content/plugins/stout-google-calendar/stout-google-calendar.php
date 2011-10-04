<?php
/*
	Plugin Name: Stout Google Calendar
	Plugin URI: http://blog.stoutdesign.com/stout-google-calendar-custom-colors
	Description: Allows you to customize the colors of embedded Google calendars and update its options through the WordPress admin. Customized Google Calendars may be embedded to your WordPress site by adding a widget, shortcode to a post/page or template tag to your theme.
	Version: 1.2.3
	Author: Matt McKenny
	Author URI: http://www.stoutdesign.com
	License: GPL2
*/

/*  
	Copyright 2010  Matt McKenny  (email: sgc@stoutdesign.com)
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $msg;
global $wpdb;
global $sgc_db_version;
$sgc_db_version = '2.0';
include_once(ABSPATH . 'wp-includes/pluggable.php'); // Include this to deal with: Fatal error: Call to undefined function wp_get_current_user()
load_plugin_textdomain( 'stout-gc',null, dirname(__FILE__).'/languages/');

// Create table for Google calendar data and colors
$installed_ver = get_option( "stoutgc_db_version" );

$sgc_table = $wpdb->prefix . "stoutgc";

// Check to see if table exists
if($wpdb->get_var("SHOW TABLES LIKE '$sgc_table'") != $sgc_table) {
	$sgc_db_version = '1';
	//Create table v 1
		$sql = "CREATE TABLE " . $sgc_table . " (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  name tinytext NOT NULL,
		  googlecalcode text NOT NULL,
			color0 varchar(32) NOT NULL,
			color1 varchar(32) NOT NULL,
			color2 varchar(32) NOT NULL,
			color3 varchar(32) NOT NULL,
			color4 varchar(32) NOT NULL,
			color5 varchar(32) NOT NULL,
			color6 varchar(32) NOT NULL,
			bkgrdTransparent boolean NOT NULL,
			bkgrdImage mediumint(9) NOT NULL,
		  UNIQUE KEY id (id)
		);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	add_option("stoutgc_db_version", $sgc_db_version);
}

// Update the table to version 2
if( $installed_ver != $sgc_db_version ) {
	//Create table v 2.0
		$sql = "CREATE TABLE " . $sgc_table . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
		  name tinytext NOT NULL,
		  googlecalcode text NOT NULL,
			color0 varchar(32) NOT NULL,
			color1 varchar(32) NOT NULL,
			color2 varchar(32) NOT NULL,
			color3 varchar(32) NOT NULL,
			color4 varchar(32) NOT NULL,
			color5 varchar(32) NOT NULL,
			color6 varchar(32) NOT NULL,
			bubble_width varchar(32) NOT NULL,
			bkgrdTransparent boolean NOT NULL,
			bkgrdImage mediumint(9) NOT NULL,
		  UNIQUE KEY id (id)
		);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	update_option("stoutgc_db_version", $sgc_db_version);
}

// add scripts and css to admin menu     
function my_plugin_admin_init() {
	/* Register our scripts. */
  wp_register_script('colorpickerapp', WP_PLUGIN_URL . '/stout-google-calendar/colorpicker.js');
	wp_register_script('jquery-plugin-validation', 'http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js');  
	wp_register_script('stout_gc', WP_PLUGIN_URL . '/stout-google-calendar/stout_gc.js');
	wp_register_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_register_style('stout_gc', WP_PLUGIN_URL . '/stout-google-calendar/stout_gc.css');
}
add_action('admin_init', 'my_plugin_admin_init');

// Include widget
require_once('stout-gc-widget.php');

/* Build Admin */
add_action('admin_menu','sgc_menu');

function sgc_menu(){
	$page = add_options_page('Stout Google Calendar', 'Stout Google Calendar', 'manage_options', 'stout-gc', 'sgc_plugin_options' );
   add_action( 'admin_print_styles-' . $page, 'sgc_admin_styles' );
   add_action( 'admin_print_scripts-' . $page, 'sgc_admin_scripts' );
}

function sgc_admin_scripts() {
/*
	* It will be called only on your plugin admin page
*/
  wp_enqueue_script('colorpickerapp');
	wp_enqueue_script('jquery-ui-dialog'); 
	wp_enqueue_script('jquery-plugin-validation');  
	wp_enqueue_script('stout_gc');
}

function sgc_admin_styles() {
/*
	* It will be called only on your plugin admin page, enqueue our stylesheet here
*/
	wp_enqueue_style( 'jquery-style' );
	wp_enqueue_style( 'stout_gc' );
}



function sgc_plugin_options(){
	global $wpdb;
	$sgc_table = $wpdb->prefix . "stoutgc";
	
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
	{
	  wp_die( __('You do not have sufficient permissions to access this page.', 'stout-gc') );
	}
	
	// variables for the field and option names 
	$hidden_field_name = 'sgc_submit_hidden';	
	
	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if(isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			$msg ='<div class="updated"><p><strong>';
			
			// we're updating a record
			if(isset($_POST['update_record']) && $_POST['update_record'] == 'Y'){
				 	global $wpdb;
					$wpdb->update( $sgc_table, array( 'name' => $_POST['name'], 'googlecalcode' => $_POST['googlecalcode'], 'bkgrdImage' => $_POST['bkgrdImage'], 'bkgrdTransparent' => $_POST['bkgrdTransparent'], 'color0' => $_POST['color0'], 'color1' => $_POST['color1'], 'color2' => $_POST['color2'], 'color3' => $_POST['color3'], 'color4' => $_POST['color4'], 'color5' => $_POST['color5'], 'color6' => $_POST['color6'], 'bubble_width' => $_POST['bubble_width']  ), array('id' => $_POST['id']), array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), array( '%d') );	
					// Put a settings updated message on the screen
					$msg .=  sprintf(__( 'Settings saved for calendar: %s.', 'stout-gc'), stripslashes($_POST['name']));
			
			//we're creating a new record													
			} elseif(isset($_POST[ 'new_record' ]) && $_POST[ 'new_record' ] == 'Y' ) {
					global $wpdb;
					$wpdb->insert( $sgc_table, array( 'name' => $_POST['name'], 'googlecalcode' => $_POST['googlecalcode'], 'bkgrdImage' => $_POST['bkgrdImage'], 'bkgrdTransparent' => $_POST['bkgrdTransparent'], 'color0' => $_POST['color0'], 'color1' => $_POST['color1'], 'color2' => $_POST['color2'], 'color3' => $_POST['color3'], 'color4' => $_POST['color4'], 'color5' => $_POST['color5'], 'color6' => $_POST['color6'], 'bubble_width' => $_POST['bubble_width']  ), array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
					// Put a settings saved message on the screen
					$msg .=  sprintf(__( 'Calendar successfully created: %s.', 'stout-gc' ), stripslashes($_POST['name']));
		
			//we're deleting a calendar
			} elseif(isset($_POST[ 'delete_record' ]) && $_POST[ 'delete_record' ] == 'Y' ) {
						global $wpdb;
						$wpdb->query( "DELETE FROM $sgc_table WHERE `id` = $_POST[id] LIMIT 1" );
						// Put a settings saved message on the screen
						$msg .=  sprintf(__( 'Calendar deleted: %s.', 'stout-gc' ), stripslashes($_POST['name']));
			}
			$msg .= '</strong></p></div>';
	}

	// Now display the settings editing screen
	echo '<div class="wrap">';
	
	// header
	echo "<h2>" . __( 'Stout Google Calendar', 'stout-gc' ) . "</h2>";

	echo ($msg != '') ? $msg : '';
	
	// header for new calendar
	echo "<h2 class='sgc-subhead'>" . __( 'Add a New Calendar', 'stout-gc' ) . "</h2>";
?>
	<div id="calendar-0" class="sgc-form-wrapper" style="display:block">
		<form name="form1" method="post" action="" id="sgc-form0">
		<div class="sgc-name-code">
			<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
			<input type="hidden" name="new_record" value="Y" />
			<p><?php _e("Calendar Name:", 'stout-gc' ); ?><br /><input type="text" name="name" value="" class="required" size="50" /></p>
			<p><?php _e("Google Calendar iframe embed code:", 'stout-gc' ); ?><br /><textarea name="googlecalcode" cols="44" rows="15" class="required sgccode" id="sgccode0"></textarea></p>
			<div id="sgc_preview_wrapper0">
			<div id="new-preview-msg"></div><a href="#" class="sgc_preview" id="new-preview"><?php _e('Preview Calendar', 'stout-gc');?></a>
			<?php $new_src = WP_PLUGIN_URL.'/stout-google-calendar/gcalendar-wrapper.php?src=en.usa%23holiday%40group.v.calendar.google.com&sgc0=FFFFFF&sgc1=c3d9ff&sgc2=000000&sgc3=e8eef7&sgc4=000000&sgc5=ffffcc&sgc6=000000&bubbleWidth=&bubbleUnit=pixel&sgcImage=&sgcImage=0&sgcBkgrdTrans=0'; ?>
				<div class="sgc_iframe_wrapper" style="display:none;width:800;height:600;">
					<iframe id="sgc_iframe_0" src="<?php echo $new_src; ?>" allowtransparency="true" style=" border:'0' " width="800" height="600" frameborder="0" scrolling="no"></iframe>
				</div>
			</div>
		</div>	
		<div class="sgc-pickers">
			<table class="sgc-color-picker" >
				<tr><th colspan="2" style="text-align:left"><?php _e('Calendar Colors:', 'stout-gc');?></th></tr>
			  <tr>
					<td>
						<?php _e("Main Background:", 'stout-gc' ); ?><br/>
						<input type="hidden" name="bkgrdTransparent"  value="" /> 
						<input type="checkbox" name="bkgrdTransparent" id="bkgrdTransparent0" class="bkgrdTransparent" value="1" /> <label for="bkgrdTransparent0"><?php _e('Transparent?', 'stout-gc');?></label>
					</td> 
					<td><input type="text" class="colorpicker0"  id="color00" name="color0" value="FFFFFF" size="6" style="background-color:#FFFFFF" /></td>
				</tr>
				<tr>
					<td><?php _e("Main Background Text:", 'stout-gc' ); ?></td> 
					<td><input type="text" class="colorpicker6"  name="color6" value="000000" size="6" style="background-color:#000000" /></td>
				</tr>
				<tr>
					<td><?php _e("Active Tab Bkgrd:", 'stout-gc' ); ?></td> 
					<td><input type="text" class="colorpicker1"  name="color1" value="c3d9ff" size="6" style="background-color:#c3d9ff" /></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Active Tab Text:", 'stout-gc' ); ?></td>                                                      
					<td><input type="text" class="colorpicker2"  name="color2" value="000000" size="6" style="background-color:#000000" /></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Inactive Tab Bkgrd:", 'stout-gc' ); ?></td>                                                      
					<td><input type="text" class="colorpicker3"  name="color3" value="e8eef7" size="6" style="background-color:#e8eef7" /></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Inactive Tab Text:", 'stout-gc' ); ?></td>                                                  
					<td><input type="text" class="colorpicker4"  name="color4" value="000000" size="6" style="background-color:#000000" /></td>
				</tr> 
				<tr>                                                                                          
					<td><?php _e("Current Day Bkgrd:", 'stout-gc'); ?></td>
					<td><input type="text" class="colorpicker5"  name="color5" value="ffffcc" size="6" style="background-color:#ffffcc" /></td>
				</tr>
				<tr><th colspan="2">Calendar Size:</th></tr>
				<tr>                                                                                          
					<td><?php _e("Width:", 'stout-gc' ); ?></td>
					<td><input type="text" class="sgcWidthOrHeight"  id="width0" name="width" value="" size="6" /></td>
				</tr>
				<tr>                                                                                          
					<td><?php _e("Height:", 'stout-gc' ); ?></td>
					<td><input type="text" class="sgcWidthOrHeight"  id="height0" name="height" value="" size="6" /></td>
				</tr>
				<tr>                                                                                          
					<td><?php _e("Bubble Width:", 'stout-gc' ); ?> <br/><span style="font-size:11px;color:gray;white-space:normal;width:140px;display:block"><em>Event detail popup width in month view (px or %)</em></span></td>
					<td><input type="text" class="sgcBubble"  id="bubble0" name="bubble_width" value="" size="6" /></td>
				</tr>
			</table>
	
			<table class="sgc-button-picker" id="button-image-bkgrd_0">
				<tr><th colspan="2" style="text-align:left"><?php _e('Button Style:', 'stout-gc');?></th></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-new0" value="0" title="<?php _e('Google Standard', 'stout-gc');?>" checked="checked" /></td><td> <label for="bkgrdImage-new0"><img alt="Google Default" height="17" width="32" style="margin-bottom:-3px; background-image: url(https://calendar.google.com/googlecalendar/images/combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /> <?php _e('Normal', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-new1" value="1" title="<?php _e('Solid Gray', 'stout-gc');?>"/></td><td> <label for="bkgrdImage-new1"><img alt="Solid Gray" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_gray_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /><?php _e('Gray', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-new2" value="2" title="<?php _e('Black, 50% opacity', 'stout-gc');?>"/></td><td> <label for="bkgrdImage-new2"><img alt="50% Opacity - Black" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_50black_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /><?php _e('50% Black', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-new3" value="3" title="<?php _e('White, 50% opacity', 'stout-gc');?>"/></td><td> <label for="bkgrdImage-new3"><img alt="50% Opacity - White" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_50white_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /><?php _e('50% White', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><th colspan="2" style="text-align:left"><?php _e('Calendar View:', 'stout-gc');?></th></tr>
				<tr class="no-background">
					<td colspan="2">
						<select name="mode" class="calMode">
							<option class="calMode" id="mode-month0" value="MONTH" ><?php _e('Month', 'stout-gc');?></option>
							<option class="calMode" id="mode-week0" value="WEEK" ><?php _e('Week', 'stout-gc');?></option>
							<option class="calMode" id="mode-agenda0" value="AGENDA" ><?php _e('Agenda', 'stout-gc');?></option>
						</select>
					</td>
				</tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showNav" id="showNav0" /></td><td><label for="showNav0"><?php _e('Show Nav?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showDate" id="showDate0" /></td><td><label for="showDate0"><?php _e('Show Date?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showPrint" id="showPrint0" /></td><td><label for="showPrint0"><?php _e('Show Print?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showTabs" id="showTabs0" /></td><td><label for="showTabs0"><?php _e('Show Tabs?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showCalendars" id="showCalendars0" /></td><td><label for="showCalendars0"><?php _e('Show Calendars?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showTz" id="showTz0" /></td><td><label for="showTz0"><?php _e('Show Timezone?', 'stout-gc');?></label></td></tr>
				<tr class="no-background">
					<td colspan="2"><?php _e('Language', 'stout-gc');?><br />
					<select id="hl0" class="calLanguage">
						<?php include "hl.php"; ?>
					</select>
					</td>
				</tr>
			</table>
		</div>
		<p class="submit-new"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Add Calendar', 'stout-gc') ?>" /></p>
		<br class="clear" />
		</form>
	</div>
<?php
			
	//Subhead for saved Calendars
	echo "<h2 class='sgc-subhead saved-calendars'>" . __( 'Saved Calendars', 'stout-gc' ) . "</h2>";

	//Check for existing records
	$calendars = $wpdb->get_results("SELECT * FROM $sgc_table ORDER BY id ASC");

	foreach ($calendars as $calendar) {
?>
	<h3 class="sgc-name"><?php echo stripslashes($calendar->name); ?> <br /><span style="font-size:smaller;font-weight:normal"><?php _e('Shortcode:', 'stout-gc');?> <code>[stout_gc id=<?php echo $calendar->id; ?>]</code><br /><?php _e('Template Tag:', 'stout-gc');?> <code>&lt;?php echo stout_gc(<?php echo $calendar->id; ?>); ?&gt;</code></span></h3> <?php echo stout_gc($calendar->id,FALSE,TRUE); ?>
	<div id="calendar-<?php echo $calendar->id; ?>" class="sgc-form-wrapper">
		<form name="form1" method="post" action="" id="sgc-form<?php echo $calendar->id; ?>">
		<div class="sgc-name-code">
			<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
			<input type="hidden" name="id" value="<?php echo $calendar->id; ?>" />
			<input type="hidden" name="update_record" value="Y" />
			<p><?php _e("Calendar Name:", 'stout-gc' ); ?><br /><input type="text" name="name" value="<?php echo stripslashes($calendar->name); ?>" class="required" size="50" /></p>
			<p><?php _e("Google Calendar iframe embed code:", 'stout-gc' ); ?><br /><textarea name="googlecalcode" cols="44" rows="15" class="required sgccode" id="sgccode<?php echo $calendar->id; ?>"><?php echo stripslashes($calendar->googlecalcode); ?></textarea></p>
		</div>	
		<div class="sgc-pickers">
			<table class="sgc-color-picker" >
				<tr><th colspan="2" style="text-align:left"><?php _e('Calendar Colors:', 'stout-gc');?></th></tr>
			  <tr>
					<td>
						<?php _e("Main Background:", 'stout-gc' ); ?><br/>
						<input type="checkbox" name="bkgrdTransparent" id="bkgrdTransparent<?php echo $calendar->id; ?>" class="bkgrdTransparent" value="1" <?php echo ($calendar->bkgrdTransparent == 1) ? 'checked="checked"' : '' ?> /> <label for="bkgrdTransparent<?php echo $calendar->id; ?>"><?php _e('Transparent?', 'stout-gc');?></label>
					</td> 
					<td><input type="text" class="colorpicker0" name="color0" id="color0<?php echo $calendar->id; ?>" value="<?php echo $calendar->color0; ?>" size="6" style="background-color:#<?php echo $calendar->color0; ?>"/></td>
				</tr>
					<tr>
						<td><?php _e("Main Background Text:", 'stout-gc' ); ?></td> 
						<td><input type="text" class="colorpicker6" name="color6" id="color6<?php echo $calendar->id; ?>" value="<?php echo $calendar->color6; ?>" size="6" style="background-color:#<?php echo $calendar->color6; ?>"/></td>
					</tr>
				<tr>
					<td><?php _e("Active Tab Bkgrd:", 'stout-gc' ); ?></td> 
					<td><input type="text" class="colorpicker1" name="color1" id="color1<?php echo $calendar->id; ?>" value="<?php echo $calendar->color1; ?>" size="6" style="background-color:#<?php echo $calendar->color1; ?>"/></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Active Tab Text:", 'stout-gc' ); ?></td>                                                      
					<td><input type="text" class="colorpicker2" name="color2" id="color2<?php echo $calendar->id; ?>" value="<?php echo $calendar->color2; ?>" size="6" style="background-color:#<?php echo $calendar->color2; ?>"/></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Inactive Tab Bkgrd:", 'stout-gc' ); ?></td>                                                      
					<td><input type="text" class="colorpicker3" name="color3" id="color3<?php echo $calendar->id; ?>" value="<?php echo $calendar->color3; ?>" size="6" style="background-color:#<?php echo $calendar->color3; ?>"/></td>
				</tr>                                                                                           
				<tr>
					<td><?php _e("Inactive Tab Text:", 'stout-gc' ); ?></td>                                                  
					<td><input type="text" class="colorpicker4" name="color4" id="color4<?php echo $calendar->id; ?>" value="<?php echo $calendar->color4; ?>" size="6" style="background-color:#<?php echo $calendar->color4; ?>"/></td>
				</tr> 
				<tr>                                                                                          
					<td><?php _e("Current Day Bkgrd:", 'stout-gc' ); ?></td>
					<td><input type="text" class="colorpicker5" name="color5" id="color5<?php echo $calendar->id; ?>" value="<?php echo $calendar->color5; ?>" size="6" style="background-color:#<?php echo $calendar->color5; ?>"/></td>
				</tr>
				<tr><th colspan="2"><?php _e('Calendar Size:', 'stout-gc');?></th></tr>
				<tr>                                                                                          
					<td><?php _e("Width:", 'stout-gc' ); ?></td>
					<td><input type="text" class="sgcWidthOrHeight" id="width<?php echo $calendar->id; ?>" name="width" value="" size="6"/></td>
				</tr>
				<tr>                                                                                          
					<td><?php _e("Height:", 'stout-gc' ); ?></td>
					<td><input type="text" class="sgcWidthOrHeight" id="height<?php echo $calendar->id; ?>" name="height" value="" size="6"/></td>
				</tr>
				<tr>                                                                                          
					<td><?php _e("Bubble Width:", 'stout-gc' ); ?> <br/><span style="font-size:11px;color:gray;white-space:normal;width:140px;display:block"><em><?php _e('Event detail popup width in month view (px or %); Blank for default; Should be smaller than calendar width', 'stout-gc');?></em></span></td>
					<td><input type="text" class="sgcBubble sgcBubbleSaved"  id="bubble<?php echo $calendar->id; ?>" name="bubble_width" value="<?php echo $calendar->bubble_width; ?>" size="6" /></td>
				</tr>
			</table>

			<table class="sgc-button-picker" id="button-image-bkgrd_<?php echo $calendar->id; ?>" style="background:#<?php echo $calendar->color0; ?>">
				<tr><th colspan="2" style="text-align:left"><?php _e('Button Style:', 'stout-gc');?></th></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-<?php echo $calendar->id; ?>0" value="0" title="<?php _e('Google Standard', 'stout-gc');?>" <?php echo ($calendar->bkgrdImage == 0) ? 'checked="checked"' : '' ?> /></td><td> <label for="bkgrdImage-<?php echo $calendar->id; ?>0"><img alt="Google Default" height="17" width="32" style="margin-bottom:-3px; background-image: url(https://calendar.google.com/googlecalendar/images/combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /> <?php _e('Normal', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-<?php echo $calendar->id; ?>1" value="1" title="<?php _e('Solid Gray', 'stout-gc');?>" <?php echo ($calendar->bkgrdImage == 1) ? 'checked="checked"' : '' ?> /></td><td> <label for="bkgrdImage-<?php echo $calendar->id; ?>1"><img alt="Solid Gray" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_gray_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /> <?php _e('Gray', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-<?php echo $calendar->id; ?>2" value="2" title="<?php _e('Black, 50% opacity', 'stout-gc');?>" <?php echo ($calendar->bkgrdImage == 2) ? 'checked="checked"' : '' ?> /></td><td> <label for="bkgrdImage-<?php echo $calendar->id; ?>2"><img alt="50% Opacity - Black" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_50black_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /> <?php _e('50% Black', 'stout-gc');?></label></td></tr>
				<tr><td><input type="radio" class="bkgrdImage" name="bkgrdImage" id="bkgrdImage-<?php echo $calendar->id; ?>3" value="3" title="<?php _e('White, 50% opacity', 'stout-gc');?>" <?php echo ($calendar->bkgrdImage == 3) ? 'checked="checked"' : '' ?> /></td><td> <label for="bkgrdImage-<?php echo $calendar->id; ?>3"><img alt="50% Opacity - White" height="17" width="32" style="margin-bottom:-3px; background-image: url(<?php echo WP_PLUGIN_URL ?>/stout-google-calendar/images/sgc_50white_combined_v18.png); background-position: -241px 0" src="http://calendar.google.com/googlecalendar/images/blank.gif" /> <?php _e('50% White', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><th colspan="2" style="text-align:left"><?php _e('Calendar View:', 'stout-gc');?></th></tr>
				<tr class="no-background">
					<td colspan="2">
						<select name="mode" class="calMode">
							<option class="calMode" id="mode-month<?php echo $calendar->id; ?>" value="MONTH" ><?php _e('Month', 'stout-gc');?></option>
							<option class="calMode" id="mode-week<?php echo $calendar->id; ?>" value="WEEK" ><?php _e('Week', 'stout-gc');?></option>
							<option class="calMode" id="mode-agenda<?php echo $calendar->id; ?>" value="AGENDA" ><?php _e('Agenda', 'stout-gc');?></option>
						</select>
					</td>
				</tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showNav" id="showNav<?php echo $calendar->id; ?>" /></td><td><label for="showNav<?php echo $calendar->id; ?>"><?php _e('Show Nav?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showDate" id="showDate<?php echo $calendar->id; ?>" /></td><td><label for="showDate<?php echo $calendar->id; ?>"><?php _e('Show Date?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showPrint" id="showPrint<?php echo $calendar->id; ?>" /></td><td><label for="showPrint<?php echo $calendar->id; ?>"><?php _e('Show Print?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showTabs" id="showTabs<?php echo $calendar->id; ?>" /></td><td><label for="showTabs<?php echo $calendar->id; ?>"><?php _e('Show Tabs?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showCalendars" id="showCalendars<?php echo $calendar->id; ?>" /></td><td><label for="showCalendars<?php echo $calendar->id; ?>"><?php _e('Show Calendars?', 'stout-gc');?></label></td></tr>
				<tr class="no-background"><td><input type="checkbox" class="sgc-toggle-options" name="showTz" id="showTz<?php echo $calendar->id; ?>" /></td><td><label for="showTz<?php echo $calendar->id; ?>"><?php _e('Show Timezone?', 'stout-gc');?></label></td></tr>
				<tr class="no-background">
					<td colspan="2"><?php _e('Language', 'stout-gc');?><br />
					<select id="hl<?php echo $calendar->id; ?>" class="calLanguage">
						<?php include "hl.php"; ?>
					</select>
					</td>
				</tr>
			</table>
			</div>
			<p class="submit-update" ><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Update Calendar', 'stout-gc') ?>" /></p>
			</form>
			
			<form action="" method="post" class="sgcdelete" name="<?php echo $calendar->id; ?>">
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
				<input type="hidden" name="id" value="<?php echo $calendar->id; ?>" />
				<input type="hidden" name="delete_record" value="Y" />
				<input type="hidden" name="name" value="<?php echo $calendar->name; ?>" />
				<p class="submit-delete" ><input type="submit" name="Submit" class="button-primary" style="background-image:none;background:red;border-color:red" value="<?php esc_attr_e('Delete Calendar', 'stout-gc') ?>" /></p>
			</form>
			<div class="delete-confirm" id="delete-confirm<?php echo $calendar->id; ?>" title="<?php printf(__('Delete Calendar: %s?', 'stout-gc'), $calendar->name);?>">
				<p><?php printf(__('Are you sure you want to delete the calendar: <strong>%s</strong>?', 'stout-gc'), $calendar->name);?></p>
			</div>										
		<br style="clear:both" />
	</div>					
<?php
	//end loop for calendars
 	}

//Check for plugin requirements
	if( !class_exists( 'WP_Http' ) ) {
		$wp_http = '<span style="color:red">Sorry, this plugin will not work with WP_Http</span>';
	}else {
		$wp_http =  '<span style="color:green">WP_Http present. Looks good.</span>';
	}
?>
	
	<div style="border-top:1px solid gray;margin-top:20px;padding:20px 0">
		<a href="http://stoutdesign.com"><img src="https://lh3.googleusercontent.com/_TKDu_kHO3SM/TWVjQJ61cDI/AAAAAAAAAB0/g2iLxc9bodc/Stout-distressed-logo.png" alt="Stout Design" style="float:left;margin-right:20px"/></a>
		
		<h3 style="line-height:1.4em">If you find this plugin useful, please <a href="http://wordpress.org/extend/plugins/stout-google-calendar/">rate the Stout Google Calendar</a> plugin.
			<br/>Questions? Please visit the <a href="http://wordpress.org/tags/stout-google-calendar">Support Forum</a>
			<br/>Oh, and <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8Y6HL2PMLPQXA">donations are always welcome</a> ;)
		</h3>
		
		<h4>This plugin requires:
		<br/>WP_Http : <?php echo $wp_http; ?>	
		</h4>
		
	</div>
	
<?php
 	
	// Close wrap div for all content
	echo '</div>';
	    
}

// Shortcode for embedding calendar
function stout_gc_func($atts) {
	extract(shortcode_atts(array(
		'id' => '1',
		'show_name' => 'FALSE'
	), $atts));
	return stout_gc($id,$show_name);
}

add_shortcode('stout_gc', 'stout_gc_func');


//Display calendar	
function stout_gc($cal, $showName = 'FALSE'){
	global $wpdb;
	$sgc_table = $wpdb->prefix . "stoutgc";

	$errors = '';
	
	//Check to see if valid calendar specified
	if(!in_array($cal,range(0,10000))){
		$errors[] = ('Invalid calendar specified.');
	}else{
		$calendar = $wpdb->get_row("SELECT * FROM $sgc_table WHERE id = $cal");
		$calcode = stripslashes($calendar->googlecalcode);
		$calname = stripslashes($calendar->name); 
	}	

	//Get query string from google embed code
	$calquery = preg_match('/\?(\S+)/',$calcode,$matches);
	if($matches[0] != ''){
		$calquery = substr($matches[0],0,-1);
	}else {
		$errors[] = __('Google calendar embed code appears to be incorrect.', 'stout-gc');
	}

	// Get the width of iframe from google embed code
	$iframe_width = preg_match('/width="(\d+\W?)"/',$calcode,$matches);
	if($matches[1] != ''){
		$iframe_width = $matches[1];
	}else{
		$errors[] = __('Cannot determine width of the calendar.', 'stout-gc');
	}

	// Get the height of iframe from google embed code
	$iframe_height = preg_match('/height="(\d+\W?)"/',$calcode,$matches);
	if($matches[1] != ''){
		$iframe_height = $matches[1];
	}else{
		$errors[] = __('Cannot determine height of the calendar.', 'stout-gc');
	}

	// Get the width of iframe from google embed code
	$iframe_border = preg_match('/border:(\w+ \w+ #\w+)/',$calcode,$matches);
	if (count($matches) > 1) {
		if($matches[1] != ''){
			$iframe_border = $matches[1];
		}else{
			//no border
			$iframe_border  = '0';
		}
	}

	// Check for Bubble Width and determin % or px
	$bubble = preg_match('/(%)/',$calendar->bubble_width,$unitMatches);
	if($unitMatches[1]){
		$bubbleUnit = 'percentage';
	}else{
		$bubbleUnit = 'pixel';
	}
	
	$bubble = preg_match('/(\d+)/',$calendar->bubble_width,$widthMatches);
	if($widthMatches[1]){
		$bubbleWidth = $widthMatches[1];
	}else{
		$bubbleWidth = '';
	}
		
	if($errors != ''){
		$errors = '<div style="padding:10px;border:1px solid red;color:red">'.$errors[0];
		if( is_admin() ) { $errors .= '<br /><a href="#" class="sgc-form-toggle">Show Calendar Editor</a>'; }
		$errors .= '</div>';
		return $errors;
	}else{
		//build src
		$src = WP_PLUGIN_URL.'/stout-google-calendar/gcalendar-wrapper.php'.$calquery.'&sgc0='.$calendar->color0.'&sgc1='.$calendar->color1.'&sgc2='.$calendar->color2.'&sgc3='.$calendar->color3.'&sgc4='.$calendar->color4.'&sgc5='.$calendar->color5.'&sgc6='.$calendar->color6.'&bubbleWidth='.$bubbleWidth.'&bubbleUnit='.$bubbleUnit.'&sgcImage='.$calendar->bkgrdImage.'&sgcBkgrdTrans='.$calendar->bkgrdTransparent;
		
		if( is_admin() ) {
			//in preview mode (admin)
			$preview = '
			<div id="sgc_preview_wrapper'.$cal.'">
				<a href="#" class="sgc-form-toggle">Show Calendar Editor</a> | <a href="#" class="sgc_preview">Preview Calendar</a>
				<div class="sgc_iframe_wrapper" style="display:none;width:'.$iframe_width.';height:'.$iframe_height.';">
					<iframe id="sgc_iframe_'.$cal.'" src="'.$src.'" allowtransparency="true" style=" border:'.$iframe_border.' " width="'.$iframe_width.'" height="'.$iframe_height.'" frameborder="0" scrolling="no"></iframe>
				</div>
			</div>';
			return $preview;			
		//return iframe for shortcode
		}else{
			$calendar_output = '';
			if(strtoupper($showName) == 'TRUE') { $calendar_output .= '<span class="sgc-name sgc-'.$cal.'">'.$calname.'</span><br />';}
			$calendar_output .= '<iframe src="'.$src.'" allowtransparency="true" style=" border:'.$iframe_border.' " width="'.$iframe_width.'" height="'.$iframe_height.'" frameborder="0" scrolling="no"></iframe>';
			return $calendar_output;
		}
	}
}

?>