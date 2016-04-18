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
<table id="SZGoogleWidgetPlusCommunity" class="sz-google-table-widget">

<!-- WIDGETS (Field with inclusion of the title widget) -->
<tr class="only-widgets">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_title ?>"><?php echo ucfirst(__('title','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_title ?>" name="<?php echo $NAME_title ?>" type="text" value="<?php echo $VALUE_title ?>" placeholder="<?php echo __('insert title for widget','sz-google') ?>"/></td>
</tr>

<!-- WIDGETS (Field selection for configuration ID or specific) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_method ?>"><?php echo ucfirst(__('community','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals">
		<select class="sz-google-switch-hidden widefat" data-switch="sz-google-switch-specific" data-close="1" onchange="szgoogle_switch_hidden_onchange(this);" id="<?php echo $ID_method ?>" name="<?php echo $NAME_method ?>">
			<option value="1" <?php selected("1",$VALUE_method) ?>><?php echo ucfirst(__('configuration ID','sz-google')) ?></option>
			<option value="2" <?php selected("2",$VALUE_method) ?>><?php echo ucfirst(__('specific ID','sz-google')) ?></option>
		</select>
	</td>
</tr>

<!-- WIDGETS (Field for insertion of a specific ID) -->
<tr class="sz-google-switch-specific sz-google-hidden">
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_specific ?>"><?php echo ucfirst(__('community ID','sz-google')) ?>:</label></td>
	<td colspan="2" class="sz-cell-vals"><input class="widefat" id="<?php echo $ID_specific ?>" name="<?php echo $NAME_specific ?>" type="text" value="<?php echo $VALUE_specific ?>" placeholder="<?php echo __('insert specific ID','sz-google') ?>"/></td>
</td></tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the size) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label for="<?php echo $ID_width ?>"><?php echo ucfirst(__('width','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width ?>" class="sz-google-checks-width widefat" name="<?php echo $NAME_width ?>" type="text" size="5" placeholder="auto" value="<?php echo $VALUE_width ?>"/></td>
	<td colspan="1" class="sz-cell-vals"><input id="<?php echo $ID_width_auto ?>" class="sz-google-checks-hidden checkbox" data-switch="sz-google-checks-width" onchange="szgoogle_checks_hidden_onchange(this);" name="<?php echo $NAME_width_auto ?>" type="checkbox" value="1" <?php echo checked($VALUE_width_auto,true,false) ?>>&nbsp;<?php echo ucfirst(__('auto','sz-google')) ?></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the parameter layout) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('layout','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_layout ?>" value="portrait"  <?php if ($VALUE_layout == 'portrait') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('V','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_layout ?>" value="landscape" <?php if ($VALUE_layout != 'portrait') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('H','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter theme) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('theme','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_theme ?>" value="light" <?php if ($VALUE_theme == 'light') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('light','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_theme ?>" value="dark"  <?php if ($VALUE_theme != 'light') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('dark','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter photo) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('photo','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_photo ?>" value="true"  <?php if ($VALUE_photo == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_photo ?>" value="false" <?php if ($VALUE_photo != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<!-- WIDGETS (Field to specify the parameter owner) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('owner','sz-google')) ?>:</label></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_owner ?>" value="true"  <?php if ($VALUE_owner == 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('yes','sz-google')) ?></td>
	<td colspan="1" class="sz-cell-vals"><input type="radio" name="<?php echo $NAME_owner ?>" value="false" <?php if ($VALUE_owner != 'true') echo ' checked'?>>&nbsp;<?php echo ucfirst(__('no','sz-google')) ?></td>
</tr>

<tr><td colspan="3"><hr></td></tr>

<!-- WIDGETS (Field to specify the parameter align) -->
<tr>
	<td colspan="1" class="sz-cell-keys"><label><?php echo ucfirst(__('align','sz-google')) ?>:</label></td>
	<td colspan="2"><select class="widefat" id="<?php echo $ID_align ?>" name="<?php echo $NAME_align ?>">
		<option value="none"   <?php echo selected("none"  ,$VALUE_align) ?>><?php echo __('none'  ,'sz-google') ?></option>
		<option value="left"   <?php echo selected("left"  ,$VALUE_align) ?>><?php echo __('left'  ,'sz-google') ?></option>
		<option value="center" <?php echo selected("center",$VALUE_align) ?>><?php echo __('center','sz-google') ?></option>
		<option value="right"  <?php echo selected("right" ,$VALUE_align) ?>><?php echo __('right' ,'sz-google') ?></option>
	</select></td>
</tr>

<!-- WIDGETS (Closing the main table form widget) -->
</table>

<!-- WIDGETS (Javascript code for UI functions) -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		if (typeof(szgoogle_checks_hidden_onload) == 'function') { szgoogle_checks_hidden_onload('SZGoogleWidgetPlusCommunity'); }
		if (typeof(szgoogle_switch_hidden_onload) == 'function') { szgoogle_switch_hidden_onload('SZGoogleWidgetPlusCommunity'); }
	});
</script>