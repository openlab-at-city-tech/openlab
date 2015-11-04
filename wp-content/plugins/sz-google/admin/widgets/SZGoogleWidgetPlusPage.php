<?php

/**
 * HTML code of this widget in the administration section
 * This code is on a separate file to exclude it from the frontend
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

?>
<!-- WIDGETS (Table for the FORM widget) -->
<table id="SZGoogleWidgetPlusPage" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</td></tr>

<!-- WIDGETS (Field selection for configuration ID or specific) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_method ?>"><?php echo ucfirst(__('page','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="sz-google-switch-hidden widefat" data-switch="sz-google-switch-specific" data-close="1" onchange="szgoogle_switch_hidden_onchange(this);" id="<?php echo $ID_method ?>" name="<?php echo $NAME_method ?>">
			<option value="1" <?php selected("1",$VALUE_method) ?>><?php echo ucfirst(__('configuration ID','sz-google')) ?></option>
			<option value="2" <?php selected("2",$VALUE_method) ?>><?php echo ucfirst(__('specific ID','sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for insertion of a specific ID) -->
<tr class="sz-google-switch-specific sz-google-hidden">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_specific ?>"><?php echo ucfirst(__('page ID','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_specific ?>" name="<?php echo $NAME_specific ?>" type="text" value="<?php echo $VALUE_specific ?>" placeholder="<?php echo __('insert specific ID','sz-google') ?>"/></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field for input type badge) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_type ?>"><?php echo ucfirst(__('type','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select onchange="szgoogle_switch_select_onchange(this);" class="sz-google-row-select widefat" id="<?php echo $ID_type ?>" name="<?php echo $NAME_type ?>">
			<option data-open="1" value="standard" <?php selected("standard",$VALUE_type) ?>><?php echo ucfirst(__('standard','sz-google')) ?></option>
			<option data-open="2" value="popup" <?php selected("popup",$VALUE_type) ?>><?php echo ucfirst(__('popup','sz-google')) ?></option>
		</select>
	</td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the size) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_width ?>"><?php echo ucfirst(__('width','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width ?>" class="sz-google-checks-width widefat" name="<?php echo $NAME_width ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_width ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-width" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_width_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_width_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr class="sz-google-row-tab sz-google-row-tab-1"><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the parameter layout) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('layout','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_layout ?>" value="portrait"  <?php if ($VALUE_layout == 'portrait') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('V','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_layout ?>" value="landscape" <?php if ($VALUE_layout != 'portrait') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('H','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter theme) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('theme','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_theme ?>" value="light" <?php if ($VALUE_theme == 'light') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('light','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_theme ?>" value="dark"  <?php if ($VALUE_theme != 'light') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('dark','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter cover) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('cover','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_cover ?>" value="true"  <?php if ($VALUE_cover == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_cover ?>" value="false" <?php if ($VALUE_cover != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter tagline) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('tagline','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_tagline ?>" value="true"  <?php if ($VALUE_tagline == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_tagline ?>" value="false" <?php if ($VALUE_tagline != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter publisher) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('publisher','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_publisher ?>" value="true"  <?php if ($VALUE_publisher == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_publisher ?>" value="false" <?php if ($VALUE_publisher != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<tr class="sz-google-row-tab sz-google-row-tab-1"><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the parameter align) -->
<tr class="sz-google-row-tab sz-google-row-tab-1">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_align ?>"><?php echo ucfirst(__('align','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="widefat" id="<?php echo $ID_align ?>" name="<?php echo $NAME_align ?>">
			<option value="none"   <?php echo selected("none"  ,$VALUE_align) ?>><?php echo __('none'  ,'sz-google') ?></option>
			<option value="left"   <?php echo selected("left"  ,$VALUE_align) ?>><?php echo __('left'  ,'sz-google') ?></option>
			<option value="center" <?php echo selected("center",$VALUE_align) ?>><?php echo __('center','sz-google') ?></option>
			<option value="right"  <?php echo selected("right" ,$VALUE_align) ?>><?php echo __('right' ,'sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for text input) -->
<tr class="sz-google-row-tab sz-google-row-tab-2">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_text ?>"><?php echo ucfirst(__('text','sz-google')) ?>:</label></td>
	<td colspan="2"><textarea class="widefat" rows="3" cols="20" id="<?php echo $ID_text ?>" name="<?php echo $NAME_text ?>" placeholder="<?php echo __('insert text for badge','sz-google') ?>"><?php echo $VALUE_text ?></textarea></td>
</tr>

<!-- WIDGETS (Field to insert image to use as a badge) -->
<tr class="sz-google-row-tab sz-google-row-tab-2">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_image ?>"><?php echo ucfirst(__('image','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input class="sz-upload-image-url-2 widefat" id="<?php echo $ID_image ?>" name="<?php echo $NAME_image ?>" type="text" value="<?php echo $VALUE_image ?>" placeholder="<?php echo __('choose image for badge','sz-google') ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input class="sz-upload-image-button button" type="button" value="<?php echo ucfirst(__('file','sz-google')) ?>" data-field-url="sz-upload-image-url-2" data-title="<?php echo ucfirst(__('select or upload a file','sz-google')) ?>" data-button-text="<?php echo ucfirst(__('confirm selection','sz-google')) ?>"/></td>
</tr>

<tr class="sz-google-row-tab sz-google-row-tab-2"><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetPlusPage'); }
		if (typeof(szgoogle_switch_hidden_onload) == 'function') { szgoogle_switch_hidden_onload('SZGoogleWidgetPlusPage'); }
		if (typeof(szgoogle_switch_select_onload) == 'function') { szgoogle_switch_select_onload('SZGoogleWidgetPlusPage'); }
		if (typeof(szgoogle_upload_select_media)  == 'function') { szgoogle_upload_select_media(); }
	});
</script>