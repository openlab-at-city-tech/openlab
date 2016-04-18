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
<table id="SZGoogleWidgetYoutubeLink" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field for entering URL type) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_method ?>"><?php echo ucfirst(__('URL','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="sz-google-switch-hidden widefat" id="<?php echo $ID_method ?>" name="<?php echo $NAME_method ?>" onchange="szgoogle_switch_hidden_onchange(this);" data-close="1" data-switch="sz-google-switch-url">
			<option value="1" <?php echo selected("1",$VALUE_method) ?>><?php echo __('configuration','sz-google') ?></option>
			<option value="2" <?php echo selected("2",$VALUE_method) ?>><?php echo __('specific channel','sz-google') ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for entering specific URL) -->
<tr class="sz-google-switch-url sz-google-hidden">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_channel ?>"><?php echo ucfirst(__('channel','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="sz-upload-image-url widefat" id="<?php echo $ID_channel ?>" name="<?php echo $NAME_channel ?>" type="text" value="<?php echo $VALUE_channel ?>" placeholder="<?php echo __('insert channel','sz-google') ?>"/></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field with entering values ​​SHOW) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('subscription','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<input type="radio" name="<?php echo $NAME_subscription ?>" value="y" <?php if ($VALUE_subscription == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?>&nbsp;&nbsp;
		<input type="radio" name="<?php echo $NAME_subscription ?>" value="n" <?php if ($VALUE_subscription != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?>
	</td>
</tr>

<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('new tab','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<input type="radio" name="<?php echo $NAME_newtab ?>" value="y" <?php if ($VALUE_newtab == 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?>&nbsp;&nbsp;
		<input type="radio" name="<?php echo $NAME_newtab ?>" value="n" <?php if ($VALUE_newtab != 'y') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no' ,'sz-google')) ?>
	</td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field for entering text to use as a badge) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_text ?>"><?php echo ucfirst(__('text','sz-google')) ?>:</label></td>
	<td colspan="2"><textarea class="widefat" rows="3" cols="20" id="<?php echo $ID_text ?>" name="<?php echo $NAME_text ?>" placeholder="<?php echo __('insert text of link','sz-google') ?>"><?php echo $VALUE_text ?></textarea></td>
</tr>

<!-- WIDGETS (Field for entering image to use as a badge) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_image ?>"><?php echo ucfirst(__('image','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input class="sz-upload-image-url-2 widefat" id="<?php echo $ID_image ?>" name="<?php echo $NAME_image ?>" type="text" value="<?php echo $VALUE_image ?>" placeholder="<?php echo __('choose image for badge','sz-google') ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input class="sz-upload-image-button button" type="button" value="<?php echo ucfirst(__('file','sz-google')) ?>" data-field-url="sz-upload-image-url-2" data-title="<?php echo ucfirst(__('select or upload a file','sz-google')) ?>" data-button-text="<?php echo ucfirst(__('confirm selection','sz-google')) ?>"/></td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetYoutubeLink'); }
		if (typeof(szgoogle_switch_hidden_onload) == 'function') { szgoogle_switch_hidden_onload('SZGoogleWidgetYoutubeLink'); }
		if (typeof(szgoogle_upload_select_media)  == 'function') { szgoogle_upload_select_media(); }
	});
</script>