<?php 
global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');

$options = $aecomments->get_all_admin_options(); //global settings

//Update settings
$updated = false;
if (isset($_POST['update'])) { 
	 check_admin_referer('wp-ajax-edit-comments_admin-options');
	$error = false;
	
	//Update global settings
	$options['show_timer'] = $_POST['show_timer'];
	$options['show_pages'] = $_POST['show_pages'];
	$options['use_rtl'] = "false";
	$options['clear_after'] = $_POST['clear_after'];
	$options['javascript_scrolling'] = $_POST['javascript_scrolling'];
	$options['comment_display_top'] = stripslashes_deep(trim($_POST['comment_display_top']));
	$options['icon_display'] = $_POST['icon_display'];
	$options['icon_set'] = $_POST['icon_set'];
	$options['enable_colorbox'] = $_POST['enable_colorbox'];
	$options['colorbox_width'] = absint( $_POST['colorbox_width'] );
	$options['colorbox_height'] = absint( $_POST['colorbox_height'] );
	//$options['beta_version_notifications'] = $_POST['beta_version_notifications'];
	//Conditions the dropdown values for saving as options
	function aec_dropdown_condition($rowinfo,$postvalue) {
		$postvalue = explode(",", $postvalue);
		$rowinfo['column'] = addslashes(htmlspecialchars($postvalue[0]));
		$rowinfo['position'] =addslashes(htmlspecialchars( $postvalue[1]));
		$rowinfo['enabled'] = addslashes(htmlspecialchars($postvalue[2]));
		return $rowinfo;
	}
	//Conditions the classic values for saving as options
	function aec_classic_condition($rowinfo,$postvalue) {
		$postvalue = explode(",", $postvalue);
		$rowinfo['column'] = addslashes(htmlspecialchars($postvalue[0]));
		$rowinfo['enabled'] = addslashes(htmlspecialchars($postvalue[1]));
		return $rowinfo;
	}
	//Dropdown Menu
	$dropdown = $options['drop_down'];
	foreach ($dropdown as $columns => $info) {
		switch ($info['id']) {
			case "dropdownapprove":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownapprove']);
				break;
			case "dropdownmoderate":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownmoderate']);
				break;
			case "dropdownspam":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownspam']);
				break;
			case "dropdowndelete":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdowndelete']);
				break;
			case "dropdowndelink":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdowndelink']);
				break;
			case "dropdownmove":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownmove']);
				break;
			case "dropdownemail":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownemail']);
				break;
			case "dropdownblacklist":
				$dropdown[$columns] = aec_dropdown_condition($info,$_POST['dropdownblacklist']);
				break;
		}
	}
	$options['drop_down'] = $dropdown;
	
	//Classic menu
	$classic = $options['classic'];
	foreach ($classic as $info => $value) {
		switch ($value['id']) {
			case "edit":
				$classic[$info] = aec_classic_condition($value,$_POST['edit']);
				break;
			case "approve":
				$classic[$info] = aec_classic_condition($value,$_POST['approve']);
				break;
			case "moderate":
				$classic[$info] = aec_classic_condition($value,$_POST['moderate']);
				break;
			case "spam":
				$classic[$info] = aec_classic_condition($value,$_POST['spam']);
				break;
			case "delete":
				$classic[$info] = aec_classic_condition($value,$_POST['delete']);
				break;
			case "delink":
				$classic[$info] = aec_classic_condition($value,$_POST['delink']);
				break;
			case "move":
				$classic[$info] = aec_classic_condition($value,$_POST['move']);
				break;
			case "email":
				$classic[$info] = aec_classic_condition($value,$_POST['email']);
				break;
			case "blacklist":
				$classic[$info] = aec_classic_condition($value,$_POST['blacklist']);
				break;
		}
	}
	$options['classic'] = $classic;
	$updated = true;
}
if ($updated && !$error) {
	$aecomments->save_admin_options( $options );
?>
<div class="updated"><p><strong><?php _e('Settings successfully updated.', 'ajaxEdit') ?></strong></p></div>
<?php
}
?>
<div class="wrap">
<form id="aecadminpanel" method="post" action="<?php echo esc_attr( $_SERVER["REQUEST_URI"] ); ?>">
<?php wp_nonce_field('wp-ajax-edit-comments_admin-options') ?>
<h2>Ajax Edit Comments - <?php _e('Appearance', 'ajaxEdit');?></h2>
<p><?php _e("Your commentators have edited their comments ", 'ajaxEdit') ?><?php echo number_format(intval($options['number_edits'])); ?> <?php _e("times", 'ajaxEdit') ?>.</p>

<div class="wrap">

<h3><?php _e('Display', 'ajaxEdit') ?></h3>
<table class="form-table">
	<tbody>
  <tr valign="top">
  	<th scope="row"><?php _e('Countdown Timer', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Show a Countdown Timer?', 'ajaxEdit') ?></strong></p><p><?php _e('Selecting "No" will turn off the countdown timer for non-admin commentators.', 'ajaxEdit') ?></p>
    <p><label for="show_timer_yes"><input type="radio" id="show_timer_yes" name="show_timer" value="true" <?php if ($options['show_timer'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="show_timer_no"><input type="radio" id="show_timer_no" name="show_timer" value="false" <?php if ($options['show_timer'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  	<th scope="row"><?php _e('Pages', 'ajaxEdit') ?></th>
    <td>
    <p><strong><?php _e('Display on pages?', 'ajaxEdit') ?></strong></p><p><?php _e('Selecting "No" will turn off comment editing on pages.', 'ajaxEdit') ?></p>
    <p><label for="show_pages_yes"><input type="radio" id="show_pages_yes" name="show_pages" value="true" <?php if ($options['show_pages'] == "true") { echo('checked="checked"'); }?> /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="show_pages_no"><input type="radio" id="show_pages_no" name="show_pages" value="false" <?php if ($options['show_pages'] == "false") { echo('checked="checked"'); }?>/> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
  </tr>
  <tr valign="top">
  <th scope="row"><?php _e('Clearfix', 'ajaxEdit'); ?></th>
  <td>
  <p><strong><?php _e('Turn Off clearfix:after?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('The clearfix is enabled by default for maximum compatibility with themes.', 'ajaxEdit') ?></p>
<p><label for="clear_after_yes"><input type="radio" id="clear_after_yes" name="clear_after" value="false" <?php if ($options['clear_after'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="clear_after_no"><input type="radio" id="clear_after_no" name="clear_after" value="true" <?php if ($options['clear_after'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('JavaScript Scrolling', 'ajaxEdit'); ?></th>
<td>
<p><strong><?php _e('Turn Off Admin JavaScript Scrolling?', 'ajaxEdit') ?></strong></p>
    <p><?php _e('The plugin tries to correct incorrect offsets on a post if you are admin.', 'ajaxEdit') ?></p>
<p><label for="javascript_scrolling_yes"><input type="radio" id="javascript_scrolling_yes" name="javascript_scrolling" value="false" <?php if ($options['javascript_scrolling'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="javascript_scrolling_no"><input type="radio" id="javascript_scrolling_no" name="javascript_scrolling" value="true" <?php if ($options['javascript_scrolling'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Edit Interface Location', 'ajaxEdit'); ?></th>
<td>
    <p><strong><?php _e('Comment Edit Interface On Bottom?', 'ajaxEdit') ?></strong></p>
<p><label for="comment_display_top_no"><input type="radio" id="comment_display_top_no" name="comment_display_top" value="false" <?php if ($options['comment_display_top'] == "false") { echo('checked="checked"'); }?> /> <?php _e('Yes', 'ajaxEdit') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="comment_display_top_yes"><input type="radio" id="comment_display_top_yes" name="comment_display_top" value="true" <?php if ($options['comment_display_top'] == "true") { echo('checked="checked"'); }?>/> <?php _e('No', 'ajaxEdit') ?></label></p>
</td>
</tr>
</tbody>
</table>

<h3><?php _e('Icons', 'ajaxEdit') ?></h3>
<table class="form-table">
	<tbody>
    <tr valign="top">
    <th scope="row"><?php _e('Icon Display', 'ajaxEdit') ?></th>
    <td>
    <p><?php _e('Select an option below to determine how the icons are displayed on your website.', 'ajaxEdit') ?></p>
<select name="icon_display">
	<option value="noicons" <?php if ($options['icon_display'] == "noicons") { echo('selected="selected"'); }?>><?php _e('Text Only','ajaxEdit');?></option>
  <option value="classic" <?php if ($options['icon_display'] == "classic") { echo('selected="selected"'); }?>><?php _e('Classic','ajaxEdit');?></option>
  <option value="dropdown" <?php if ($options['icon_display'] == "dropdown") { echo('selected="selected"'); }?>><?php _e('Dropdown','ajaxEdit');?></option>
  <option value="iconsonly" <?php if ($options['icon_display'] == "iconsonly") { echo('selected="selected"'); }?>><?php _e('Icons Only','ajaxEdit');?></option>
</select>
</td>
<tr valign="top">
<th scope="row"><?php _e('Icon Set', 'ajaxEdit') ?></th>
<td>
   <p><?php _e('Select an option below to display the icon set on your website.', 'ajaxEdit') ?></p>
    <?php 
		// Files in wp-content/plugins directory
		$path = $aecomments->get_plugin_dir( "/images/themes" );
		if ( is_dir( $path ) ) {
			$themedir = @ opendir($path);
			echo "<select name='icon_set'>";
			while (($file = readdir( $themedir ) ) !== false ) {
				
				if (is_dir($path.'/'.$file ) && substr_count($file, '.') == 0) {
					$selected = '';
					if ($file == $options['icon_set']) {
						$selected = "selected";
					}
					echo "<option value='$file' $selected>$file</option>";
				}
			}
			echo "</select>";
		} //end is_dir $path
		?>
        <div id="iconpreview"><img src='<?php echo $aecomments->get_plugin_url( "/images/themes/" . $options['icon_set'] . "/sprite.png" );?>' alt="Icon Preview" /><input type="hidden" name="iconpreviewurl" value="<?php echo $aecomments->get_plugin_url('/images/themes/');?>" /></div>
    </td></tr>
    <tr valign="top">
    <th scope="row"><?php _e('Icon Drop Down Menu', 'ajaxEdit') ?></th>
    <td>
    	<p><?php _e('Drag and drop between lists to adjust the icon order.  Click on the image to disable or enable the option.', 'ajaxEdit') ?></p>
        <table>
            <tr valign="top">
            <?php 
			//DROP DOWN STUFF
			//Sort the columns for the dropdown
			function aec_position_order($a, $b) {
				return strcmp($a['position'], $b['position']);
			}
			function aec_column_order($a, $b) {
				return strcmp($a['column'], $b['column']);
			}
			$lis = array();
			$dropdown = $options['drop_down'];
			//Create the array columns
			$columns = array('column0'=>array(),'column1'=>array(), 'column2'=> array());
			foreach ($dropdown as $items => $item) {
				switch ($item['column']) {
					case "0":
						$columns['column0'][sizeof($columns['column0'])] = $item;
						break;
					case "1":
						$columns['column1'][sizeof($columns['column1'])] = $item;
						break;
					case "2":
						$columns['column2'][sizeof($columns['column2'])] = $item;
						break;
				}	
			}
			$lis = array();
			foreach ($columns as $column) {
				usort($column, 'aec_position_order');
				//Build the LIs
				$li = '';
				foreach ($column as $info) {
					$li .= "<li class='sortable' id='" . $info['id'] . "'><span class='dropdown ";
					if ($info['enabled'] == '1') { $li .= "enabled"; } else { $li .= "disabled";}
					$li .= "' id='" . $info['id'] . "'></span>";
					$li .= __($info['text'], 'ajaxEdit');
					$li .= "<input type='hidden' name='" . $info['id'] . "' value='" . $info['column'] . "," . $info['position'] . "," . $info['enabled'] . "' />";
					$li .= "</li>";
				}
				$lis[sizeof($lis)] = $li;
			}
			//Output
			for ($i = 0; $i < sizeof($lis); $i++) {
				echo "<td id='sort$i'>";
				echo "<ul id='sort$i" . "ul' class='connectedSortable'>";
				echo $lis[$i];
				echo "</ul>";
				echo "</td>";
			}
			?>
            </tr>
        </table>
    </td>
    </tr>
    <tr valign="top">
    <th scope="row"><?php _e('Classic and Icons Only Vew', 'ajaxEdit') ?></th>
    	<td>
    	<p><?php _e('Drag and drop to adjust the icon order.  Click on the image to disable or enable the option.', 'ajaxEdit') ?></p>
        <table>
            <tr valign="top">
            <?php 
			$classic = $options['classic'];
			//Create the array columns
			$columns = array();
			foreach ($classic as $items => $item) {
				$columns[sizeof($columns)] = $item;
			}
			
			$items = '';
			usort($columns, 'aec_column_order');
			foreach ($columns as $column) {
				//Build the LIs
				$items .= "<li class='sortableclassic' id='" . $column['id'] . "'><span class='classic ";
				if ($column['enabled'] == '1') { $items .= "enabled"; } else { $items .= "disabled";}
				$items .= "' id='" . $column['id'] . "'></span>";
				$items .= __($column['text'], 'ajaxEdit');
				$items .= "<input type='hidden' name='" . $column['id'] . "' value='" . $column['column'] . "," . $column['enabled'] . "' />";
				$items .= "</li>";
			}
			echo "<td id='sortclassic'>";
			echo "<ul id='sortclassicul' >";
			echo $items;
			echo "</ul>";
			echo "</td>";
			?>
            </tr>
        </table>
 		</td>
    </tr>
    </tbody>
</table>

<h3><?php _e('Colorbox', 'ajaxEdit') ?></h3>
<p><?php printf( __( '%s is a lightbox script that is used for the various pop-ups within this plugin.', 'ajaxEdit' ), sprintf( "<a href='%s'>Colorbox</a>", esc_url( 'http://colorpowered.com/colorbox/' ) ) ); ?></p>
<table class="form-table">
	<tbody>
     <tr valign='top'>
   <th scope='row'><?php _e('Enable Colorbox on the front-end?', 'ajaxEdit'); ?></th>
   	<td>
    <p><?php printf( __('Disable this option if you would like to use another Colorbox WordPress plugin such as %s or %s.', 'ajaxEdit' ), sprintf( "<a href='%s'>jQuery Colorbox</a>", esc_url( 'http://wordpress.org/extend/plugins/jquery-colorbox/' ) ), sprintf( "<a href='%s'>Lightbox Plus</a>", esc_url( 'http://wordpress.org/extend/plugins/lightbox-plus/' ) ) );?></p>
    <p><label for="enable_colorbox_yes"><input type="radio" id="enable_colorbox_yes" name="enable_colorbox" value="true" <?php checked( $options[ 'enable_colorbox' ], 'true' ); ?>  /> <?php _e('Yes','ajaxEdit'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enable_colorbox_no"><input type="radio" id="enable_colorbox_no" name="enable_colorbox" value="false" <?php checked( $options[ 'enable_colorbox' ], 'false' ); ?>  /> <?php _e('No','ajaxEdit'); ?></label></p>
    </td>
    </tr>
       <tr valign='top'>
   <th scope='row'><label for="colorbox_width"><?php _e('Set the Colorbox Width', 'ajaxEdit'); ?></label></th>
   	<td>
    <p><input type="text" size="30" value="<?php echo esc_attr( absint( $options['colorbox_width'] ) ); ?>" name="colorbox_width" id="colorbox_width" /></p>
    </td>
    </tr>
         <tr valign='top'>
   <th scope='row'><label for="colorbox_height"><?php _e('Set the Colorbox Height', 'ajaxEdit'); ?></label></th>
   	<td>
    <p><input type="text" size="30" value="<?php echo esc_attr( absint( $options['colorbox_height'] ) ); ?>" name="colorbox_height" id="colorbox_height" /></p>
    </td>
    </tr>
    
  </tbody>
</table>	

<p class="submit">
  <input class='button-primary' type="submit" name="update" value="<?php _e('Update Settings', 'ajaxEdit') ?>" />
</p><!--/submit-->
</form>
</div><!-- .wrap -->

